<?php

namespace fafcms\helpers\validators;

use fafcms\fafcms\assets\PasswordValidationAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\validators\Validator;

class HashIdValidator extends Validator
{
    public function init()
    {
        parent::init();
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;

        if ($value !== null) {
            $value = Yii::$app->fafcms->hashToId($value, get_class($model));

            if ($value === null) {
                $this->addError($model, $attribute, 'Cannot validate id');
                return;
            } else {
                $model->$attribute = $value;
            }
        }
    }
}
