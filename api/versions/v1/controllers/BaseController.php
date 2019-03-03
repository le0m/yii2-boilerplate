<?php

namespace api\versions\v1\controllers;

use common\components\behaviors\LanguageBehavior;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\Cors;
use yii\rest\ActiveController;
use yii\web\Response;


class BaseController extends ActiveController
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);

        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON
            ],
            'languages' => [
                'en',
            ]
        ];

        // set accepted origins
        $corsOrigins = [];

        // enable localhost for dev environment
        if (YII_ENV === 'dev') {
            $corsOrigins[] = 'http://localhost:8080';
        }

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => $corsOrigins,
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Max-Age' => 86400
            ]
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options']
        ];

        $behaviors['language'] = [
            'class' => LanguageBehavior::class,
        ];

        return $behaviors;
    }
}
