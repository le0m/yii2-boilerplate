<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\AuthItem;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AuthAssignmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var $roles string[] */


$this->title = Yii::t('app.view', 'Role Assignments');
$this->params['breadcrumbs'][] = Yii::t('app.view', "RBAC");
?>

<div class="auth-assignment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app.view', 'Invalidate cache'), ['invalidate-cache'], [
            'class' => 'btn btn-warning',
            'data' => [
                'confirm' => Yii::t('app.view', 'Are you sure you want to invalidate cache?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'user_id',
                'label' => Yii::t('app.view', "User"),
                'value' => function ($model) {
                    /** @var \common\models\AuthAssignment $model */
                    return $model->user->username;
                }
            ],
            [
                'attribute' => 'item_name',
                'label' => Yii::t('app.view', "Role"),
                'filter' => ArrayHelper::map($roles, 'name', 'name')
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
</div>
