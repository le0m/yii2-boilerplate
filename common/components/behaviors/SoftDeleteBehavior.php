<?php

namespace common\components\behaviors;

use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\BaseActiveRecord;
use yii\base\ModelEvent;

/**
 * --------------------------------
 * Based off:
 * @link https://github.com/yii2tech/ar-softdelete
 * @copyright Copyright (c) 2015 Yii2tech
 * @license [New BSD License](http://www.opensource.org/licenses/bsd-license.php)
 *
 * --------------------------------
 *
 * This behavior adds soft deletion and restore to [[\yii\db\ActiveRecord]].
 *
 * It adds events for soft-deletion and restore, before and after.
 *
 * @property BaseActiveRecord $owner owner ActiveRecord instance.
 */
class SoftDeleteBehavior extends Behavior
{
    /**
     * @event ModelEvent an event that is triggered before deleting a record.
     * You may set [[ModelEvent::isValid]] to be false to stop the deletion.
     */
    const EVENT_BEFORE_SOFT_DELETE = 'beforeSoftDelete';

    /**
     * @event Event an event that is triggered after a record is deleted.
     */
    const EVENT_AFTER_SOFT_DELETE = 'afterSoftDelete';

    /**
     * @event ModelEvent an event that is triggered before record is restored from "deleted" state.
     * You may set [[ModelEvent::isValid]] to be false to stop the restoration.
     */
    const EVENT_BEFORE_RESTORE = 'beforeRestore';

    /**
     * @event Event an event that is triggered after a record is restored from "deleted" state.
     */
    const EVENT_AFTER_RESTORE = 'afterRestore';

    /**
     * @var array values of the owner attributes which should be applied on soft delete,
     * in format: [attributeName => attributeValue].
     *
     * Attribute value can be a value or a callable:
     *
     * ```php
     * ['isDeleted' => function ($model) {return time()}]
     * ```
     */
    public $softDeleteAttributeValues;

    /**
     * @var array|null  values of the owner attributes, which should be applied on restoration from "deleted" state,
     * in format: [attributeName => attributeValue].
     */
    public $restoreAttributeValues;

    /**
     * @var bool whether to invoke owner [[BaseActiveRecord::beforeDelete()]] and [[BaseActiveRecord::afterDelete()]]
     * while performing soft delete. This option affects only [[softDelete()]] method.
     */
    public $invokeDeleteEvents = true;


    /**
     * Marks the owner as deleted.
     *
     * @param null|array optional values of the owner attributes to soft-delete,
     * in format: [attributeName => attributeValue].
     *
     * @return int|false the number of rows marked as deleted, or false if the soft deletion is unsuccessful for some reason.
     * Note that it is possible the number of rows deleted is 0, even though the soft deletion execution is successful.
     *
     * @throws InvalidConfigException
     */
    public function softDelete(array $values = null)
    {
        if ($this->invokeDeleteEvents && !$this->owner->beforeDelete()) {
            return false;
        }

        $result = $this->softDeleteInternal($values);

        if ($this->invokeDeleteEvents) {
            $this->owner->afterDelete();
        }

        return $result;
    }

    /**
     * Marks the owner as deleted.
     *
     * @param null|array optional values of the owner attributes to soft-delete,
     * in format: [attributeName => attributeValue]. If null, [[softDeleteAttributeValues]] will be used.
     *
     * @return int|false the number of rows marked as deleted, or false if the soft deletion is unsuccessful for some reason.
     *
     * @throws InvalidConfigException
     */
    protected function softDeleteInternal(array $values = null)
    {
        $softDeleteAttributeValues = $values ?: $this->softDeleteAttributeValues;

        if ($softDeleteAttributeValues === null) {
            throw new InvalidConfigException('No soft-delete values, "' . get_class($this) . '::$softDeleteAttributeValues" should be explicitly set.');
        }

        $result = false;

        if ($this->beforeSoftDelete()) {
            $attributes = $this->owner->getDirtyAttributes();

            foreach ($softDeleteAttributeValues as $attribute => $value) {
                if (!is_scalar($value) && is_callable($value)) {
                    $value = call_user_func($value, $this->owner);
                }
                $attributes[$attribute] = $value;
            }

            $result = $this->owner->updateAttributes($attributes);
            $this->afterSoftDelete();
        }

        return $result;
    }

    /**
     * This method is invoked before soft deleting a record.
     *
     * The default implementation raises the [[EVENT_BEFORE_SOFT_DELETE]] event.
     * Make sure you call the parent implementation so that the event is raised properly.
     *
     * @return bool whether the record should be deleted. Defaults to true.
     */
    public function beforeSoftDelete()
    {
        if (method_exists($this->owner, 'beforeSoftDelete')) {
            if (!$this->owner->beforeSoftDelete()) {
                return false;
            }
        }

        $event = new ModelEvent();
        $this->owner->trigger(self::EVENT_BEFORE_SOFT_DELETE, $event);

        return $event->isValid;
    }

    /**
     * This method is invoked after soft deleting a record.
     * You may override this method to do postprocessing after the record is deleted.
     *
     * The default implementation raises the [[EVENT_AFTER_SOFT_DELETE]] event.
     * Make sure you call the parent implementation so that the event is raised properly.
     */
    public function afterSoftDelete()
    {
        if (method_exists($this->owner, 'afterSoftDelete')) {
            $this->owner->afterSoftDelete();
        }

        $this->owner->trigger(self::EVENT_AFTER_SOFT_DELETE);
    }

    /**
     * Restores record from "deleted" state, after it has been "soft" deleted.
     *
     * @param null|array optional values of the owner attributes to restore,
     * in format: [attributeName => attributeValue].
     *
     * @return int|false the number of restored rows, or false if the restoration is unsuccessful for some reason.
     *
     * @throws InvalidConfigException
     */
    public function restore(array $values = null)
    {
        $result = false;

        if ($this->beforeRestore()) {
            $result = $this->restoreInternal($values);
            $this->afterRestore();
        }

        return $result;
    }

    /**
     * Performs restoration for soft-deleted record.
     *
     * @param null|array optional values of the owner attributes to restore,
     * in format: [attributeName => attributeValue]. If null, [[restoreAttributeValues]] will be used.
     *
     * @return int the number of restored rows.
     *
     * @throws InvalidConfigException
     */
    protected function restoreInternal(array $values = null)
    {
        $restoreAttributeValues = $values ?: $this->restoreAttributeValues;

        if ($restoreAttributeValues === null) {
            throw new InvalidConfigException('No restore values, "' . get_class($this) . '::$restoreAttributeValues" should be explicitly set.');
        }

        $attributes = $this->owner->getDirtyAttributes();

        foreach ($restoreAttributeValues as $attribute => $value) {
            if (!is_scalar($value) && is_callable($value)) {
                $value = call_user_func($value, $this->owner);
            }

            $attributes[$attribute] = $value;
        }

        return $this->owner->updateAttributes($attributes);
    }

    /**
     * This method is invoked before record is restored from "deleted" state.
     *
     * The default implementation raises the [[EVENT_BEFORE_RESTORE]] event.
     * Make sure you call the parent implementation so that the event is raised properly.
     *
     * @return bool whether the record should be restored. Defaults to `true`.
     */
    public function beforeRestore()
    {
        if (method_exists($this->owner, 'beforeRestore')) {
            if (!$this->owner->beforeRestore()) {
                return false;
            }
        }

        $event = new ModelEvent();
        $this->owner->trigger(self::EVENT_BEFORE_RESTORE, $event);

        return $event->isValid;
    }

    /**
     * This method is invoked after record is restored from "deleted" state.
     * You may override this method to do postprocessing after the record is restored.
     *
     * The default implementation raises the [[EVENT_AFTER_RESTORE]] event.
     * Make sure you call the parent implementation so that the event is raised properly.
     */
    public function afterRestore()
    {
        if (method_exists($this->owner, 'afterRestore')) {
            $this->owner->afterRestore();
        }

        $this->owner->trigger(self::EVENT_AFTER_RESTORE);
    }
}
