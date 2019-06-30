<?php


namespace anatoliy700\redirect\repositories;


use anatoliy700\redirect\models\IRedirectItem;

interface IRepository
{
    /**
     * @param string $oldPath
     * @return IRedirectItem|null
     */
    public function getRedirectItemByOldPath(string $oldPath): ?IRedirectItem;

    /**
     * @param array $config
     * @return IRedirectItem
     */
    public function getRedirectItem(array $config): IRedirectItem;
}
