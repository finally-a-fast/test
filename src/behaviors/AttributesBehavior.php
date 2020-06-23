<?php


namespace fafcms\helpers\behaviors;

use fafcms\helpers\ActiveRecord;

use Yii;
use yii\base\Event;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;
use yii\behaviors\AttributesBehavior as BaseAttributesBehavior;

/**
 * Class AttributesBehavior
 *
 * @see BaseAttributesBehavior
 * @package fafcms\helpers
 */
class AttributesBehavior extends BaseAttributesBehavior
{

    /**
     * @var $createdAtAttribute string
     */
    public $createdAtAttribute = 'created_at';

    /**
     * @var $updatedAtAttribute string.
     */
    public $updatedAtAttribute = 'updated_at';

    /**
     * @var $deletedAtAttribute string
     */
    public $deletedAtAttribute = 'deleted_at';

    /**
     * @var $activatedAtAttribute string
     */
    public $activatedAtAttribute = 'activated_at';

    /**
     * @var $deactivatedAtAttribute string
     */
    public $deactivatedAtAttribute = 'deactivated_at';

    /**
     * @var $createdByAttribute string
     */
    public $createdByAttribute = 'created_by';

    /**
     * @var $updatedByAttribute string
     */
    public $updatedByAttribute = 'updated_by';

    /**
     * @var $deletedByAttribute string
     */
    public $deletedByAttribute = 'deleted_by';

    /**
     * @var $activatedByAttribute string
     */
    public $activatedByAttribute = 'activated_by';

    /**
     * @var $deactivatedByAttribute string
     */
    public $deactivatedByAttribute = 'deactivated_by';

    /**
     * @var $timeValue mixed
     */
    public $timeValue = 'timeValue';

    /**
     * @var $blameValue mixed
     */
    public $blameValue = 'blameValue';

    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        parent::init();

        if ($this->owner instanceof ActiveRecord) {
            $attributes = $this->getActiveRecordAttributes();
        }

        $this->attributes = ArrayHelper::merge($attributes ?? [], $this->attributes);
    }

    /**
     * {@inheritDoc}
     */
    protected function getValue($attribute, $event)
    {
        if (!isset($this->attributes[$attribute][$event->name])) {
            return null;
        }

        $value = $this->attributes[$attribute][$event->name];

        if (!empty($value) && is_string($value) && $this->hasMethod('get' . ucfirst($value))) {
            return $this->{'get' . strtoupper($value)}($attribute, $event);
        }

        return parent::getValue($attribute, $event);
    }

    /**
     * @param mixed $attribute
     * @param Event $event
     *
     * @return array|mixed|Expression|null
     */
    public function getTimeValue($attribute, Event $event)
    {
        return new Expression('NOW()');
    }

    /**
     * @param mixed $attribute
     * @param Event $event
     *
     * @return array|mixed|null
     * @throws InvalidConfigException
     */
    public function getBlameValue($attribute, Event $event)
    {
        if (Yii::$app->has('user')) {
            return Yii::$app->get('user')->id ?? null;
        }

        return null;
    }

    /**
     * @return array
     */
    protected function getActiveRecordAttributes(): array
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;

        $attributes = [];
        if ($this->check('timestamp')) {
            if ($owner->hasAttribute($this->createdAtAttribute)) {
                $attributes[$this->createdAtAttribute] = [
                    $owner::EVENT_BEFORE_INSERT => $this->timeValue,
                    $owner::EVENT_BEFORE_UPDATE => $this->timeValue,
                ];
            }

            if ($owner->hasAttribute($this->updatedAtAttribute)) {
                $attributes[$this->updatedAtAttribute] = [
                    $owner::EVENT_BEFORE_UPDATE => $this->timeValue,
                ];
            }

            if ($owner->hasAttribute($this->deletedAtAttribute)) {
                $attributes[$this->deletedAtAttribute] = [
                    $owner::EVENT_BEFORE_DELETE => $this->timeValue,
                ];
            }

            if ($owner->hasAttribute($this->activatedAtAttribute)) {
                $attributes[$this->activatedAtAttribute] = [
                    $owner::EVENT_BEFORE_ACTIVATE => $this->timeValue,
                ];
            }

            if ($owner->hasAttribute($this->deactivatedAtAttribute)) {
                $attributes[$this->deactivatedAtAttribute] = [
                    $owner::EVENT_BEFORE_DEACTIVATE => $this->timeValue,
                ];
            }
        }

        if ($this->check('blameable')) {
            if ($owner->hasAttribute($this->createdByAttribute)) {
                $attributes[$this->createdByAttribute] = [
                    $owner::EVENT_BEFORE_INSERT => $this->blameValue,
                ];
            }

            if ($owner->hasAttribute($this->updatedByAttribute)) {
                $attributes[$this->updatedByAttribute] = [
                    $owner::EVENT_BEFORE_UPDATE => $this->blameValue,
                ];
            }

            if ($owner->hasAttribute($this->deletedByAttribute)) {
                $attributes[$this->deletedByAttribute] = [
                    $owner::EVENT_BEFORE_DELETE => $this->blameValue,
                ];
            }

            if ($owner->hasAttribute($this->activatedByAttribute)) {
                $attributes[$this->activatedByAttribute] = [
                    $owner::EVENT_BEFORE_ACTIVATE => $this->blameValue,
                ];
            }

            if ($owner->hasAttribute($this->deactivatedByAttribute)) {
                $attributes[$this->deactivatedByAttribute] = [
                    $owner::EVENT_BEFORE_DEACTIVATE => $this->blameValue,
                ];
            }
        }

        return $attributes;
    }

    /*
     * @todo get from Setting
     *
     * @param string $string
     *
     * @return bool
     */
    protected function check(string $string): bool
    {
        return true;
    }
}
