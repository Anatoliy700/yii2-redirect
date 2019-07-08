<?php

namespace anatoliy700\redirect\repositories;

use anatoliy700\redirect\models\IRedirectItem;
use anatoliy700\redirect\models\RedirectItem;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

abstract class Repository implements IRepository
{
    /**
     * @var IRedirectItem
     */
    public $redirectItemClass = RedirectItem::class;

    /**
     * Используются данные заголовки столбцов если не указано $headerOffset.
     * Если в вашем файле другой порядок столбцов,
     * то необходимо переопределить данное свойство
     * и указать "['oldPath', 'newPath', 'statusCode']" в том порядке как в вашем файле.
     * Если у вас используются другие заголовки, то вы можете их переопредедить как показано ниже:
     *
     *      'headers' => [
     *          'oldPath' => 'yourHeaderName',
     *          'newPath' => '...',
     *          'statusCode'
     *      ]
     *
     * @var array
     */
    public $headers = ['oldPath', 'newPath', 'statusCode'];


    /**
     * @param array $config
     * @return IRedirectItem
     * @throws InvalidConfigException
     */
    protected function createRedirectItem(array $config): IRedirectItem
    {
        return Yii::createObject($this->redirectItemClass, $config);
    }

    /**
     * @param array $arg
     * @return array
     * @throws ReflectionException
     */
    protected function getParametersForConstructor(array $arg): array
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

    /**
     * @param array $names
     * @return array
     */
    protected function getHeaders(array $names = []): array
    {
        $headers = [];

        if (!$names) {
            return array_values($this->headers);
        }

        foreach ($names as $name) {
            $headers[] = $this->getHeader($name);
        }

        return $headers;
    }

    /**
     * @param string $name
     * @return string
     */
    protected function getHeader(string $name): string
    {
        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }

        return $name;
    }

    /**
     * @param array $headers
     * @throws ReflectionException
     */
    protected function validateHeaders(array $headers): void
    {
        $reflection = new ReflectionClass($this->redirectItemClass);
        $params = $reflection->getConstructor()->getParameters();

        $filter = function ($param) {
            /* @var $param ReflectionParameter */
            if ($param->isOptional()) {
                return false;
            }

            return true;
        };

        $requiredParams = ArrayHelper::getColumn(array_filter($params, $filter), 'name');
        $missingHeaders = array_diff($this->getHeaders($requiredParams), $headers);

        if ($missingHeaders) {
            throw new InvalidArgumentException(
                'No required headers found: '
                . implode(', ', $missingHeaders)
            );
        }
    }
}
