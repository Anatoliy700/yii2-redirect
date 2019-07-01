<?php


namespace anatoliy700\redirect\controllers;

use yii\web\Controller;
use yii\web\ErrorAction;

class DefaultController extends Controller
{
    public $defaultAction = 'error';

    public function actions()
    {
        return [
            'error' => [
                'class' => ErrorAction::class,
            ],
        ];
    }
}
