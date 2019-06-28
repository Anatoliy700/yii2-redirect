<?php


namespace anatoliy700\redirect\repositories;


use anatoliy700\redirect\models\RedirectItem;
use krok\filesystem\FileNotFoundException;
use League\Csv\Reader;

class CSVRepository implements IRepository
{
    /**
     * @var string
     */
    public $filePath;

    /**
     * @var string
     */
    public $redirectItemClass = RedirectItem::class;


    /**
     * @param string $oldPath
     * @return RedirectItem|null
     * @throws FileNotFoundException
     * @throws \yii\base\InvalidConfigException
     */
    public function getRedirectItemByOldPath(string $oldPath): ?RedirectItem
    {

        $path = \Yii::getAlias($this->filePath);
        if (!file_exists($path)) {
            throw new FileNotFoundException("File {$path} not found");
        }

        $reader = Reader::createFromPath($path, 'r');
        $records = $reader->getRecords();

        foreach ($records as $index => $record) {
            if ($this->verify($oldPath, $record)) {
                return $this->getRedirectItem($record);
            }
        }

        return null;
    }

    /**
     * @param string $oldPath
     * @param array $item
     * @return bool
     */
    public function verify(string $oldPath, array $item): bool
    {
        return $oldPath === $item[0];
    }

    /**
     * @param $config
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public function getRedirectItem(array $config): RedirectItem
    {
        return \Yii::createObject($this->redirectItemClass, $config);
    }
}
