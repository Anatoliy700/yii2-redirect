<?php


namespace anatoliy700\redirect\repositories;

use anatoliy700\redirect\exceptions\RedirectItemNotFoundException;
use anatoliy700\redirect\models\IRedirectItem;

interface IRepository
{
    /**
     * @param string $oldPath
     * @return IRedirectItem
     * @throws RedirectItemNotFoundException
     */
    public function getRedirectItemByOldPath(string $oldPath): IRedirectItem;
}
