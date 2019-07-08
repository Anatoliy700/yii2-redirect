<?php

namespace anatoliy700\redirect\repositories\csv;

use anatoliy700\redirect\models\IRedirectItem;
use anatoliy700\redirect\repositories\Repository;
use krok\filesystem\FileNotFoundException;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use ReflectionException;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

class CSVRepository extends Repository
{
    /**
     * Путь до CSV файла со ссылками
     *
     * @var string
     */
    public $filePath;

    /**
     * @var string
     */
    public $headerOffset;

    /**
     * @param string $oldPath
     * @return IRedirectItem
     * @throws ErrorException
     * @throws FileNotFoundException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws ReflectionException
     */
    public function getRedirectItemByOldPath(string $oldPath): IRedirectItem
    {
        $path = $this->getFilePath();
        $redirectItemConfig = $this->getRedirectItemConfig($path, $oldPath);

        if ($redirectItemConfig) {
            return $this->createRedirectItem(
                $this->getParametersForConstructor($redirectItemConfig)
            );
        }

        throw new NotFoundHttpException('The item redirect for this path was not found.');
    }

    /**
     * @return bool|string
     * @throws FileNotFoundException
     */
    protected function getFilePath(): string
    {
        $path = Yii::getAlias($this->filePath);
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
     * @throws ReflectionException
     * @throws InvalidConfigException
     */
    protected function getRedirectItemConfig($filePath, string $oldPath): array
    {
        $filterByOldPath = function ($record) use ($oldPath) {
            return ltrim($record[$this->getHeader('oldPath')], '/') === $oldPath;
        };

        try {
            $reader = Reader::createFromPath($filePath, 'r');
            if (!is_null($this->headerOffset)) {
                $reader->setHeaderOffset($this->headerOffset);
            }

            $statement = (Yii::createObject(Statement::class))
                ->where($filterByOldPath);

            if ($headers = $reader->getHeader()) {
                $this->validateHeaders($headers);
                $records = $statement->process($reader);
            } else {
                $this->validateHeaders($this->headers);
                $records = $statement->process($reader, $this->getHeaders());
            }

            return $records->fetchOne();
        } catch (Exception $e) {
            //TODO: Обработать исключение
            throw new ErrorException($e->getMessage());
        }
    }
}
