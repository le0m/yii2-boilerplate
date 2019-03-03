<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AuthAssignment */
/** @var $roles string[] */

$this->title = Yii::t('app.view', 'Update Role Assignment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app.view', 'RBAC'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app.view', 'Update');
?>
<div class="auth-assignment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'roles' => $roles,
        'model' => $model,
    ]) ?>

</div>
