<?php

namespace anatoliy700\redirect\repositories\csv;

use anatoliy700\redirect\exceptions\FileNotFoundException;
use anatoliy700\redirect\models\IRedirectItem;
use anatoliy700\redirect\repositories\Repository;
use League\Csv\Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use Yii;
use yii\base\ErrorException;
use yii\base\InvalidConfigException;

class CSVRepository extends Repository
{
    /**
     * Путь до CSV файла со ссылками
     *
     * @var string
     */
    public $filePath;

    /**
     * @param string $oldPath
     * @return IRedirectItem|null
     * @throws ErrorException
     * @throws FileNotFoundException
     * @throws InvalidConfigException
     */
    public function getRedirectItemByOldPath(string $oldPath): ?IRedirectItem
    {
        $path = $this->getFilePath();
        $redirectItemConfig = $this->getRedirectItemConfig($path, $oldPath);

        if (!$redirectItemConfig) {
            return null;
        }

        return $this->createRedirectItem($redirectItemConfig);
    }

    /**
     * @return bool|string
     * @throws FileNotFoundException
     */
    protected function getFilePath(): string
    {
        $path = Yii::getAlias($this->filePath);
        if (!file_exists($path)) {
            throw new FileNotFoundException($path);
        }

        return $path;
    }

    /**
     * @param $filePath
     * @param string $oldPath
     * @return array
     * @throws ErrorException
     * @throws InvalidConfigException
     */
    protected function getRedirectItemConfig($filePath, string $oldPath): array
    {
        $filterByOldPath = function ($record) use ($oldPath) {
            return ltrim($record[0], '/') === $oldPath;
        };

        try {
            $reader = Reader::createFromPath($filePath, 'r');

            $statement = (Yii::createObject(Statement::class))
                ->where($filterByOldPath);

            $records = $statement->process($reader);

            return $records->fetchOne();
        } catch (Exception $e) {
            //TODO: Обработать исключение
            throw new ErrorException($e->getMessage());
        }
    }
}
