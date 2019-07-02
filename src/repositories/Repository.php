<?php

namespace anatoliy700\redirect\repositories;

use anatoliy700\redirect\models\IRedirectItem;
use anatoliy700\redirect\models\RedirectItem;
use ReflectionClass;

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

    /**
     * @param array $arg
     * @return array
     * @throws \ReflectionException
     */
    public function getParametersForConstructor(array $arg): array
    {
        $params = [];

        $reflection = new ReflectionClass($this->redirectItemClass);
        $constructorParams = $reflection->getConstructor()->getParameters();

        foreach ($constructorParams as $param) {
            $paramName = $this->getHeader($param->getName());
            if (array_key_exists($paramName, $arg)) {
                $params[$param->getPosition()] = $arg[$paramName];
            }
        }

        return $params;
    }
}
