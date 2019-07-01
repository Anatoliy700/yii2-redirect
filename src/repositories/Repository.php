<?php


namespace anatoliy700\redirect\repositories;

use anatoliy700\redirect\models\IRedirectItem;
use anatoliy700\redirect\models\RedirectItem;

abstract class Repository implements IRepository
{
    /**
     * @var IRedirectItem
     */
    public $redirectItemClass = RedirectItem::class;

    /**
     * @param array $config
     * @return IRedirectItem
     * @throws \yii\base\InvalidConfigException
     */
    public function getRedirectItem(array $config): IRedirectItem
    {
        return \Yii::createObject($this->redirectItemClass, $config);
    }
}
