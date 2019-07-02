<?php

namespace anatoliy700\redirect\repositories\csv;

use League\Csv\Statement;
use ReflectionClass;
use ReflectionProperty;
use Yii;
use yii\helpers\ArrayHelper;

abstract class BaseBuilder
{
    /**
     * @var
     */
    protected $object;


    /**
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public function build()
    {
        $this->setParams();

        return $this->getObject();
    }

    abstract protected function setParams();

    /**
     * @return Statement|object
     * @throws \yii\base\InvalidConfigException
     */
    protected function getObject()
    {
        if (is_null($this->object)) {
            $this->object = Yii::createObject($this->getTargetClassName());
        }

        return $this->object;
    }

    /**
     * @param $config
     * @return object
     * @throws \yii\base\InvalidConfigException
     */
    public static function create($config)
    {
        $config = ArrayHelper::merge(['class' => static::class], $config);

        return Yii::createObject($config);
    }

    /**
     * @return ReflectionProperty[]
     * @throws \ReflectionException
     */
    protected function getProperties()
    {
        $reflection = new ReflectionClass(static::class);

        return $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
    }

    /**
     * @param $name
     * @param $val
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    protected function setOneParam($name, $val)
    {
        $setMethodName = 'set' . ucfirst($name);
        $reflection = new \ReflectionClass($this->getTargetClassName());
        if ($reflection->hasMethod($name)) {
            $this->object = $this->getObject()->$name($val);
        } elseif ($reflection->hasMethod($setMethodName)) {
            $this->object = $this->getObject()->$setMethodName($val);
        }
    }


    /**
     * @return string
     */
    abstract protected function getTargetClassName(): string;
}
