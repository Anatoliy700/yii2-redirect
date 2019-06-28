<?php


namespace anatoliy700\redirect\models;


use yii\base\BaseObject;

class RedirectItem extends BaseObject
{
    public $oldPath;

    public $newPath;

    public $statusCode;

}
