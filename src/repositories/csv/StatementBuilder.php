<?php


namespace anatoliy700\redirect\repositories\csv;

use League\Csv\Statement;
use ReflectionClass;
use ReflectionProperty;

class StatementBuilder extends BaseBuilder
{

    /**
     * Callables to filter the iterator.
     *
     * @var callable[]
     */
    public $where = [];

    /**
     * Callables to sort the iterator.
     *
     * @var callable[]
     */
    public $orderBy = [];

    /**
     * iterator Offset.
     *
     * @var int
     */
    public $offset = 0;

    /**
     * iterator maximum length.
     *
     * @var int
     */
    public $limit = -1;


    /**
     * @return $this
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    protected function setParams()
    {
        foreach ($this->getProperties() as $property) {
            if (is_array($property->getValue($this))) {
                foreach ($property->getValue($this) as $item) {
                    $this->setOneParam($property->getName(), $item);
                }
            } else {
                $this->setOneParam($property->getName(), $property->getValue($this));
            }
        }

        return $this;
    }

    /**
     * @param $name
     * @param $val
     * @throws \yii\base\InvalidConfigException
     */
    protected function setOneParam($name, $val)
    {
        $this->object = $this->getObject()->$name($val);
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
     * @inheritDoc
     */
    protected function getTargetClassName(): string
    {
        return Statement::class;
    }
}
