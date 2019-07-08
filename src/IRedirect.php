<?php

namespace anatoliy700\redirect;

use anatoliy700\redirect\models\IRedirectItem;
use yii\web\NotFoundHttpException;
use yii\web\Request;

interface IRedirect
{
    /**
     * @param Request $request
     * @return IRedirectItem
     * @throws NotFoundHttpException
     */
    public function getRedirectItem(Request $request): IRedirectItem;
}
