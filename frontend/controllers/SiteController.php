<?php
namespace frontend\controllers;

use common\controllers\BaseController;
use common\models\LoginForm;
use frontend\models\AccountActivationForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;

/**
 * Site controller
 */
class SiteController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access']['only'] = ['logout', 'signup', 'activate-account', 'request-password-reset', 'reset-password'];
        $behaviors['access']['rules'] = [
            [
                'actions' => ['signup', 'activate-account', 'request-password-reset', 'reset-password'],
                'allow' => true,
                'roles' => ['?'],
            ],
            [
                'actions' => ['logout'],
                'allow' => true,
                'roles' => ['@'],
            ],
        ];

        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if ($model->sendActivationEmail($user)) {
                    Yii::$app->session->setFlash("success", Yii::t('app.flash', "Registration successful, check your email."));

                    return $this->goHome();
                }

                Yii::$app->session->setFlash("error", Yii::t('app.flash', "There was an error sending your activation email, contact us."));
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Activate account using activation token.
     *
     * @param string $token
     *
     * @return \yii\web\Response
     *
     * @throws BadRequestHttpException
     */
    public function actionActivateAccount($token)
    {
        try {
            $model = new AccountActivationForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if (!$model->activateAccount()) {
            Yii::$app->session->setFlash("error", Yii::t('app.flash', "There was an error activating your account, contact us."));

            return $this->goHome();
        }

        Yii::$app->session->setFlash("success", Yii::t('app.flash', "Account activated!"));

        return $this->redirect(['login']);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', Yii::t('app.flash', "Check your email for further instructions."));

                return $this->goHome();
            }

            Yii::$app->session->setFlash('error', Yii::t('app.flash', "Sorry, we are unable to reset password for the provided email address."));
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     *
     * @return mixed
     *
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->resetPassword()) {
                Yii::$app->session->setFlash('success', Yii::t('app.flash', "New password saved."));

                return $this->goHome();
            }

            Yii::$app->session->setFlash("error", Yii::t('app.flash', "There was an error resetting your password, contact us."));
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
