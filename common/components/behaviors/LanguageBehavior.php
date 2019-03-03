<?php

namespace common\components\behaviors;

use Yii;
use yii\base\ActionEvent;
use yii\base\Behavior;
use yii\base\Controller;
use yii\db\ActiveRecord;

/**
 * This behavior handles user language preference.
 *
 * Adjust settings to use a model attribute for logged in users.
 *
 * For guest users, or if model is disabled, the preferred language
 * will be chosen based on supported languages and request headers.
 *
 * @author Leo Mainardi <mainardi.leo@gmail.com>
 */
class LanguageBehavior extends Behavior
{
    /**
     * @var bool
     *
     * enable model usage for user preferred language
     */
    public $enableModel = true;

    /**
     * @var string
     *
     * model class to use to find the user model
     */
    public $modelClass = 'common\models\User';

    /**
     * @var string
     *
     * model attribute name, where the preferred language is stored
     */
    public $modelAttribute = 'language';

    /**
     * @var array
     *
     * supported languages by the application; use same format of `Yii::$app->language`
     */
    public $supportedLanguages = ['en-US'];


    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction'
        ];
    }

    /**
     * @param ActionEvent $event
     * @return bool
     */
    public function beforeAction($event)
    {
        $prefLanguage = $this->getPreferredLanguage();

        Yii::$app->language = $prefLanguage;

        return $event->isValid;
    }

    /**
     * Get user's preferred language.
     *
     * Checks
     *
     * @return string|null
     */
    protected function getPreferredLanguage()
    {
        $language = null;

        if (!Yii::$app->user->isGuest && $this->enableModel) {
            /** @var ActiveRecord $modelClass */
            $modelClass = $this->modelClass;
            /** @var ActiveRecord $user */
            $user = $modelClass::findOne(Yii::$app->user->identity->getId());

            $language = $user->getAttribute($this->modelAttribute);
        }

        if ($language === null) {
            $language = Yii::$app->request->getPreferredLanguage($this->supportedLanguages);
        }

        Yii::debug(sprintf("Got language: %s", $language), __METHOD__);

        return $language;
    }
}
