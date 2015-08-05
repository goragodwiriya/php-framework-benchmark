<?php namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\World;

class OrmController extends Controller
{

    public function actionIndex()
    {
        World::updateAll(array('name' => ''));
        for ($i = 0; $i < 100; $i++) {
            $rnd = mt_rand(1, 10000);
            $result = World::findOne($rnd);
            $result->name = 'Hello World!';
            $result->save();
        }
        $result = World::findOne($result->id);
        return $result->name;
    }
}