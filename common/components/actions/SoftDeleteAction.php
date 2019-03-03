<?php

namespace common\components\actions;

use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\rest\Action;
use yii\web\ServerErrorHttpException;


/**
 * Action to soft-delete a record. Best used with REST API.
 *
 * The model must implement `common\components\behaviors\SoftDeleteBehavior`.
 */
class SoftDeleteAction extends Action
{
    /**
     * Deletes a model.
     *
     * @param mixed $id id of the model to be deleted.
     *
     * @throws \yii\web\HttpException
     * @throws InvalidConfigException
     */
    public function run($id)
    {
        /** @var ActiveRecord $model */
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        if ($model->getBehavior('softDelete') === null) {
            throw new InvalidConfigException('The model class ' . get_class($model) . ' does not implement SoftDeleteBehavior');
        }

        if ($model->softDelete() === false) {
            throw new ServerErrorHttpException('Failed to delete the object for unknown reason.');
        }

        Yii::$app->getResponse()->setStatusCode(204);
    }
}
