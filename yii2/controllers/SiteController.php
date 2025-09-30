<?php

namespace app\controllers;

use Exception;
use Yii;
use yii\web\Controller;
use app\models\Curler;

class SiteController extends Controller
{

    public function actionIndex(): string
    {
        $model = new Curler();

        if (
            Yii::$app->request->isPost
            && $model->load(Yii::$app->request->post())
            && $model->validate()
        )
        {
            try {
                $model->process();
                Yii::$app->session->setFlash('ok');
            } catch (Exception $e) {
                Yii::$app->session->setFlash('failure: ' . $e->getMessage());
            }

        }

        return $this->render('index', ['model' => $model]);
    }

}
