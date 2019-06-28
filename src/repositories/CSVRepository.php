<?php


namespace anatoliy700\redirect\repositories;


use anatoliy700\redirect\models\RedirectItem;

class CSVRepository implements IRepository
{
    /**
     * @var string
     */
    public $filePath;


    public function getRedirectItemByOldPath(string $oldPath): ?RedirectItem
    {
//        return null;

        return \Yii::createObject([
            'class' => RedirectItem::class,
            'oldPath' => 'qwerty',
            'newPath' => 'content',
            'statusCode' => 302,
        ]);
    }
}
