<?php


namespace anatoliy700\redirect\repositories;

use anatoliy700\redirect\models\IRedirectItem;
use yii\web\NotFoundHttpException;

interface IRepository
{
    /**
     * @param string $oldPath
     * @return IRedirectItem
     * @throws NotFoundHttpException
     */
    public function getRedirectItemByOldPath(string $oldPath): IRedirectItem;
}
