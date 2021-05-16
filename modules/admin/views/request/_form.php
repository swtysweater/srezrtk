<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\admin\models\Request */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="request-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'categoryID')->dropdownList(\yii\helpers\ArrayHelper::map(\app\modules\admin\models\Category::find()->all(), 'id', 'name')) ?>

    <?= $form->field($model, 'statusID')->dropdownList(\yii\helpers\ArrayHelper::map(\app\modules\admin\models\Status::find()->all(), 'id', 'name')) ?>

    <?= $form->field($model, 'reject_msg')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'imageFileBefore')->fileInput() ?>

    <?= $form->field($model, 'imageFileAfter')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
