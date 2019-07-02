<?php


namespace anatoliy700\redirect\repositories\csv;

use anatoliy700\redirect\models\IRedirectItem;
use anatoliy700\redirect\repositories\Repository;
use krok\filesystem\FileNotFoundException;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use yii\base\ErrorException;
use yii\base\InvalidArgumentException;
use yii\helpers\ArrayHelper;

class CSVRepository extends Repository
{
    /**
     * Путь до файла со ссылками
     *
     * @var string
     */
    public $filePath;

    /**
     * Используются данные заголовки столбцов если не указано $headerOffset.
     * Если в вашем файле другой порядок столбцов,
     * то необходимо переопределить данное свойство
     * и указать "['oldPath', 'newPath', 'statusCode']" в том порядке как в вашем файле.
     * Если у вас используются другие заголовки, то вы можете их переопредедить как показано ниже:
     *
     *      'headers' => [
     *          'oldPath' => 'yourHeaderName',
     *          'newPath' => '...',
     *          'statusCode'
     *      ]
     *
     * @var array
     */
    public $headers = ['oldPath', 'newPath', 'statusCode'];

    /**
     * Конфигурация League\Csv\Reader и League\Csv\Statement
     * Варианты конфигурации можно найти здесь @see https://csv.thephpleague.com/9.0/reader/statement/
     *
     *      'urlRepository' => [
     *          'class' => '\anatoliy700\redirect\repositories\csv\CSVRepository',
     *          'filePath' => '@app/redirectFile/redirect.csv',
     *          'csvReaderConfig' => [
     *              'limit' => -1,
     *              'offset' => 0,
     *              'where' => [
     *                  function(array $record [, int $offset [, Iterator $iterator]]): self
     *              ]
     *          ]
     *      ]
     *
     *
     * @var array
     */
    public $csvReaderConfig = [];

    /**
     * @param string $oldPath
     * @return IRedirectItem|null
     * @throws ErrorException
     * @throws FileNotFoundException
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function getRedirectItemByOldPath(string $oldPath): ?IRedirectItem
    {
        $path = $this->getCsvFile();
        $redirectItemConfig = $this->getRedirectItemConfig($path, $oldPath);

        if ($redirectItemConfig) {
            return $this->getRedirectItem(
                $this->getParametersForConstructor($redirectItemConfig)
            );
        }

        return null;
    }

    /**
     * @return bool|string
     * @throws FileNotFoundException
     */
    protected function getCsvFile()
    {
        $path = \Yii::getAlias($this->filePath);
        if (!file_exists($path)) {
            throw new FileNotFoundException("File {$path} not found");
        }

        return $path;
    }

    /**
     * @param $filePath
     * @param string $oldPath
     * @return array
     * @throws ErrorException
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    protected function getRedirectItemConfig($filePath, string $oldPath): array
    {
        $filterByOldPath = function ($record) use ($oldPath) {
            return ltrim($record[$this->getHeader('oldPath')], '/') === $oldPath;
        };

        try {
            $config = $this->csvReaderConfig;
            $config['where'][] = $filterByOldPath;
            $config['filePath'] = $filePath;
            /* @var Statement $stmt */
            $stmt = (StatementBuilder::create($config))->build();
            /* @var Reader $reader */
            $reader = (ReaderBuilder::create($config))->build();

            $headers = $reader->getHeader();
            if ($headers) {
                $this->validateHeaders($headers);
                $records = $stmt->process($reader);
            } else {
                $this->validateHeaders($this->headers);
                $records = $stmt->process($reader, $this->getHeaders());
            }

            return $records->fetchOne();
        } catch (Exception $e) {
            //TODO: Обработать исключение
            throw new ErrorException($e->getMessage());
        }
    }

    /**
     * @param array $headers
     * @throws \ReflectionException
     */
    public function validateHeaders(array $headers): void
    {
        $reflection = new \ReflectionClass($this->redirectItemClass);
        $params = $reflection->getConstructor()->getParameters();
        $filter = function ($param) {
            /* @var $param \ReflectionParameter */
            if ($param->isOptional()) {
                return false;
            }

            return true;
        };

        $requiredParams = ArrayHelper::getColumn(array_filter($params, $filter), 'name');
        $missingHeaders = array_diff($this->getHeaders($requiredParams), $headers);

        if ($missingHeaders) {
            throw new InvalidArgumentException('No required headers found: ' . implode(', ', $missingHeaders));
        }
    }

    /**
     * @param array $names
     * @return array
     */
    public function getHeaders(array $names = []): array
    {
        $headers = [];

        if (!$names) {
            return array_values($this->headers);
        }

        foreach ($names as $name) {
            $headers[] = $this->getHeader($name);
        }

        return $headers;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getHeader(string $name): string
    {
        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }

        return $name;
    }
}
