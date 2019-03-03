<?php

namespace common\controllers;

use common\components\behaviors\LanguageBehavior;
use yii\filters\AccessControl;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\web\Controller;


class BaseController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [],
            ],
            'rate' => [
                'class' => RateLimiter::class
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'language' => [
                'class' => LanguageBehavior::class,
            ],
        ];
    }
}
