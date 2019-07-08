<?php

namespace anatoliy700\redirect\repositories;

use anatoliy700\redirect\models\IRedirectItem;
use Yii;
use yii\base\InvalidConfigException;

abstract class Repository implements IRepository
{

    /**
     * @param array $config
     * @return IRedirectItem
     * @throws InvalidConfigException
     */
    protected function createRedirectItem(array $config): IRedirectItem
    {
        return Yii::createObject(IRedirectItem::class, $config);
    }
}
