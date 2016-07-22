<?php

namespace app\controllers;

use yii\web\Controller;
use \Yii;

class HelloController extends Controller
{
    public function actionIndex()
    {
    return  Yii::$app->basePath;
        return 'Hello World!';
    }
}
