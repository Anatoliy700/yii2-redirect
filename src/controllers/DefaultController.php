<?php


namespace anatoliy700\redirect\controllers;

use anatoliy700\redirect\actions\RedirectAction;
use yii\web\Controller;
use yii\web\ErrorAction;

class DefaultController extends Controller
{
    public function actions()
    {
        return [
            'index' => RedirectAction::class,
            'error' => ErrorAction::class,
        ];
    }
}
