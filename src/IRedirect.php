<?php

namespace anatoliy700\redirect;

use anatoliy700\redirect\models\IRedirectItem;

interface IRedirect
{
    public function getRedirectItem(string $pathInfo, array $queryParams): ?IRedirectItem;
}
