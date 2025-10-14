<?php

/** @var yii\web\View $this */
/** @var Code $model */

use app\models\Code;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'My Yii Application';

$js = <<< JS
    $(document).ready(function() {
        setTimeout(function() {
            $('[role=alert]').fadeOut(5000); 
        }, 0);
    });
    JS;
$this->registerJs($js);

?>
<div class="site-index">

    <div class="body-content">
        <?php

            if (Yii::$app->session->hasFlash('ok')) {
                echo
                "<div class=\"alert alert-success alert-dismissible\" role=\"alert\">
                    ok 
                </div>"
                ;
                echo '</div>';
            }
            if (Yii::$app->session->hasFlash('failure')) {
                echo
                "<div class=\"alert alert-danger alert-dismissible\" role=\"alert\">
                    fail 
                </div>"
                ;
                echo '</div>';
            }
        ?>
        <div class="row">
            <?php
            $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'form-horizontal'],
            ]) ?>
            <?= $form->field($model, 'name') ?>

            <div class="form-group">
                <div class="col-lg-offset-1 col-lg-11">
                    <?= Html::submitButton('Process', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
            <?php ActiveForm::end() ?>
        </div>

    </div>
</div>
