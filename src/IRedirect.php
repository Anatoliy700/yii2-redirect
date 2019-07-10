<?php

namespace anatoliy700\redirect;

use anatoliy700\redirect\exceptions\RedirectItemNotFoundException;
use anatoliy700\redirect\models\IRedirectItem;
use yii\web\Request;

interface IRedirect
{
    /**
     * @param Request $request
     * @return IRedirectItem|null
     */
    public function getRedirectItem(Request $request): ?IRedirectItem;
}
