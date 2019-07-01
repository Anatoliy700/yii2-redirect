<?php


namespace anatoliy700\redirect\repositories;

use anatoliy700\redirect\models\IRedirectItem;
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
     * Разделитель полей
     *
     * @var string
     */
    public $delimiter = ',';

    /**
     * Если в файле присутсвуют заголовки слолбцов,
     * то здесь указывается порядковый номер строки с заголовками(отсчет начинается с 0).
     * Имена столбцов обязательно должны быть "'oldPath', 'newPath', 'statusCode'".
     * Столбец 'statusCode' не обязателен, если он не указан, то код будет передан "301"
     *
     * @var int
     */
    public $headerOffset;

    /**
     * Используются данные заголовки столбцов если не указано $headerOffset.
     * Если в вашем файле другой порядок столбцов,
     * то необходимо переопределить данное свойство
     * и указать "['oldPath', 'newPath', 'statusCode']" в том порядке как в вашем файле.
     *
     * @var array
     */
    public $headers = ['oldPath', 'newPath', 'statusCode'];

    /**
     * Задает смещение начала считывания строк из файла.
     *
     * @var int
     */
    public $offset = 0;

    /**
     * Задает количество строк, которые будут считаны и обработаны.
     *
     * @var int
     */
    public $limit = -1;


    /**
     * @param string $oldPath
     * @return IRedirectItem|null
     * @throws ErrorException
     * @throws Exception
     * @throws FileNotFoundException
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    public function getRedirectItemByOldPath(string $oldPath): ?IRedirectItem
    {
        $path = \Yii::getAlias($this->filePath);
        if (!file_exists($path)) {
            throw new FileNotFoundException("File {$path} not found");
        }

        try {
            $reader = Reader::createFromPath($path, 'r');
            $reader->setDelimiter($this->delimiter);

            $filterByOldPath = function ($record) use ($oldPath) {
                //TODO: продумать подстанову 'oldPath' если название поля изменится
                return ltrim($record['oldPath'], '/') === $oldPath;
            };

            $stmt = (new Statement())
                ->offset($this->offset)
                ->limit($this->limit)
                ->where($filterByOldPath);

            if (isset($this->headerOffset)) {
                $reader->setHeaderOffset($this->headerOffset);
                $this->validateHeaders($reader->getHeader());
                $records = $stmt->process($reader);
            } else {
                $this->validateHeaders($this->headers);
                $records = $records = $stmt->process($reader, $this->headers);
            }

            $redirectItemConfig = $records->fetchOne();
        } catch (Exception $e) {
            //TODO: Обработать исключение
            throw new ErrorException($e->getMessage());
        }

        if ($redirectItemConfig) {
            return $this->getRedirectItem(
                $this->getParametersForController(
                    $this->redirectItemClass,
                    $records->fetchOne()
                )
            );
        }

        return null;
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
        $missingHeaders = array_diff($requiredParams, $headers);

        if ($missingHeaders) {
            throw new InvalidArgumentException('No required headers found: ' . implode(', ', $missingHeaders));
        }
    }

    /**
     * @param string $class
     * @param array $arg
     * @return array
     * @throws \ReflectionException
     */
    public function getParametersForController(string $class, array $arg): array
    {
        $params = [];

        $reflection = new \ReflectionClass($class);
        $constructorParams = $reflection->getConstructor()->getParameters();

        foreach ($constructorParams as $param) {
            if (array_key_exists($param->getName(), $arg)) {
                $params[$param->getPosition()] = $arg[$param->getName()];
            }
        }

        return $params;
    }
}
