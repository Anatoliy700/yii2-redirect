<?php


namespace anatoliy700\redirect\repositories;


use anatoliy700\redirect\models\RedirectItem;

interface IRepository
{
    public function getRedirectItemByOldPath(string $oldPath): ?RedirectItem;

}
