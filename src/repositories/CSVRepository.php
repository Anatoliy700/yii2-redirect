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
     * @var string
     */
    public $filePath;

    /**
     * @var string
     */
    public $delimiter = ',';

    public $headerOffset;

    public $headers = ['oldPath', 'newPath', 'statusCode'];

    public $offset = 0;

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
                return ltrim($record['oldPath'],
                        '/') === $oldPath; //TODO: продумать подстанову 'oldPath' если название поля изменится
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
            return $this->getRedirectItem($this->getParametersForController($this->redirectItemClass,
                $records->fetchOne()));
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
            throw new InvalidArgumentException("No required headers found: ".implode(', ', $missingHeaders));
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
