<?php

namespace app\controllers;

use app\entities\Page;
use Exception;
use Yii;
use yii\web\Controller;
use app\models\Code;

class SiteController extends Controller
{

    public function actionIndex(): string
    {
        $model = new Code();

        if (
            Yii::$app->request->isPost
            && $model->load(Yii::$app->request->post())
            && $model->validate()
        )
        {
            try {
                $page = new Page($model->name);
                $page->getRaw();
                if (
                    $page->hasProductsData()
                ) {
                    $page->extract();
                    $page->save();
                }

                Yii::$app->session->setFlash('Success!');
            } catch (Exception $e) {
                Yii::$app->session->setFlash('Failure: ' . $e->getMessage());
            }

        }

        return $this->render('index', ['model' => $model]);
    }

}
