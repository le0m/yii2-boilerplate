<?php

namespace common\components;

use common\models\Setting;
use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\caching\Cache;
use yii\helpers\ArrayHelper;


/**
 * This component retrieves application parameters stored in a DB, using an helper [[ActiveRecord]].
 *
 * Parameters defined in DB have precedence to ones defined in configuration files. If you define a
 * parameter in both places, the one in DB will overwrite the one in configuration files.
 *
 * Parameters retrieved from DB are stored in cache by default. See the properties docs for info on how to
 * control this behavior.
 *
 * @author Leo Mainardi <mainardi.leo@gmail.com>
 */
class Settings extends Component
{
	/**
	 * @var string|false|\yii\caching\Cache
     *
     * Name of the cache component to use for caching settings, or an instance.
     * If set to `false`, no cache wil be used.
	 */
	public $cache = 'cache';

	/**
	 * @var integer
     *
     * Duration of the cache, in seconds.
	 */
	public $cacheDuration = 3600;

	/**
	 * @var string
     *
     * Key used to cache settings.
	 */
	public $cacheKey = 'le0m/settings';


    /**
     * {@inheritdoc}
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        if (is_string($this->cache)) {
            $this->cache = Yii::$app->get($this->cache);
        }

        if ($this->cache !== false && !($this->cache instanceof Cache)) {
            throw new InvalidConfigException("Cache provider must extend from yii\caching\Cache");
        }

        if (Yii::$app instanceof \yii\web\Application) {
            $data = $this->getData();

            foreach ($data as $name => $value) {
                if (!isset(Yii::$app->params[$name])) {
                    Yii::$app->params[$name] = $value;
                }
            }
        }
    }

    /**
     * Refresh the cache.
     *
     * Call this if you're using caching and change the parameters
     * in DB.
     *
     * @return bool success status
     */
    public function refreshCache()
    {
        $data = $this->getDataFromDb();

        return $this->saveToCache($data);
	}

    /**
     * Get settings from DB, wrapped by cache if used.
     *
     * If caching is used, data from DB is saved on first run
     * and then read from cache on following occasions.
     * Use [[refreshCache()]] if you change settings.
     *
     * An empty array is returned if nothing is found.
     *
     * @return array
     */
	protected function getData()
    {
        $data = [];

        if ($this->cache instanceof Cache) {
            $data = $this->cache->get($this->cacheKey) ?: [];
            Yii::debug(sprintf("Retrieved %d parameters from cache.", $data ? count($data) : 0), __METHOD__);
        }

        if (count($data) === 0) {
            $data = $this->getDataFromDb();

            if ($this->cache instanceof Cache) {
                $this->saveToCache($data);
            }
        }

        return $data;
    }

    /**
     * Get settings from DB.
     *
     * @return array
     */
    protected function getDataFromDb()
    {
        /* @var $settings Setting[] */
        $settings = Setting::find()->all();
        $data = ArrayHelper::map($settings, 'name', 'value');
        Yii::debug(sprintf("Retrieved %d parameters from DB.", count($data)), __METHOD__);

        return $data;
    }

    /**
     * Save data into cache.
     *
     * Nothing happens if cache is not used.
     *
     * @param array $data
     *
     * @return bool success status
     */
    protected function saveToCache($data)
    {
        $result = true;

	    if ($this->cache instanceof Cache) {
            $result = $this->cache->set($this->cacheKey, $data, $this->cacheDuration);
            Yii::debug(sprintf("Saved %d parameters to cache.", count($data)), __METHOD__);
        }

        return $result;
    }
}
