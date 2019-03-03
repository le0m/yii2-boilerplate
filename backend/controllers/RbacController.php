<?php

namespace backend\controllers;

use common\models\AuthAssignment;
use common\models\AuthAssignmentSearch;
use common\models\AuthItem;
use Yii;
use yii\web\NotFoundHttpException;
use common\controllers\BaseController;

/**
 * RbacController implements the CRUD actions for AuthAssignment model.
 */
class RbacController extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access']['rules'] = [
            [
                'allow' => true,
                'roles' => [AuthItem::ROLE_ADMIN],
            ],
        ];

        return $behaviors;
    }

    /**
     * Lists all AuthAssignment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AuthAssignmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        /** @var string[] $roles */
        $roles = AuthItem::find()->select('name')->asArray()->groupBy('name')->all();

        return $this->render('index', [
            'roles' => $roles,
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing AuthAssignment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $item_name
     * @param integer $user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($item_name, $user_id)
    {
        $model = $this->findModel($item_name, $user_id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        /** @var string[] $roles */
        $roles = AuthItem::find()->select('name')->asArray()->groupBy('name')->all();

        return $this->render('update', [
            'roles' => $roles,
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AuthAssignment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $item_name
     * @param integer $user_id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable in case delete failed.
     */
    public function actionDelete($item_name, $user_id)
    {
        $this->findModel($item_name, $user_id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Invalidates RBAC cache manually.
     *
     * @return \yii\web\Response
     */
    public function actionInvalidateCache()
    {
        AuthAssignment::invalidateCache();
        Yii::$app->session->setFlash("success", Yii::t('app.flash', "RBAC cache invalidated."));

        return $this->redirect(Yii::$app->request->getReferrer());
    }

    /**
     * Finds the AuthAssignment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $item_name
     * @param integer $user_id
     * @return AuthAssignment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($item_name, $user_id)
    {
        if (($model = AuthAssignment::findOne(['item_name' => $item_name, 'user_id' => $user_id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app.view', 'The requested page does not exist.'));
    }
}
