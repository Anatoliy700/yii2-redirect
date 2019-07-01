<?php

namespace anatoliy700\redirect\models;

use yii\base\BaseObject;

abstract class BaseRedirectItem extends BaseObject implements IRedirectItem
{
    /**
     * BaseRedirectItem constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct($config);
    }
}
