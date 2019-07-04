<?php

namespace anatoliy700\redirect\repositories\csv;

use League\Csv\Reader;

class ReaderBuilder extends BaseBuilder
{
    /**
     * Путь до файла со ссылками
     *
     * @var string
     */
    public $filePath;


    /**
     * Разделитель полей
     *
     * @var string
     */
    public $delimiter = ',';

    /**
     * Если в файле присутсвуют заголовки слолбцов,
     * то здесь указывается порядковый номер строки с заголовками(отсчет начинается с 0).
     * Имена столбцов обязательно должны быть "'oldPath', 'newPath', 'statusCode'".
     * Столбец 'statusCode' не обязателен, если он не указан, то код будет передан "301"
     *
     * @var int
     */
    public $headerOffset = null;


    /**
     * @return $this
     * @throws \ReflectionException
     * @throws \yii\base\InvalidConfigException
     */
    protected function setParams()
    {
        foreach ($this->getProperties() as $property) {
            if ($property->getValue($this) !== null) {
                $this->setOneParam($property->getName(), $property->getValue($this));
            }
        }

        return $this;
    }

    /**
     * @return \League\Csv\Statement|object
     */
    protected function getObject()
    {
        if (is_null($this->object)) {
            $this->object = $this->getTargetClassName()::createFromPath($this->filePath, 'r');
        }

        return $this->object;
    }

    /**
     * @inheritDoc
     */
    protected function getTargetClassName(): string
    {
        return Reader::class;
    }
}
