<?php

namespace fafcms\helpers\validators;

use fafcms\fafcms\assets\PasswordValidationAsset;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\validators\Validator;

class StrengthValidator extends Validator
{
    public $hasUser = true;
    public $hasEmail = true;
    public $allowSpaces = false;
    public $min = 4;
    public $max;
    public $length;
    public $lower = 2;
    public $upper = 2;
    public $digit = 2;
    public $special = 2;
    public $userAttribute = 'username';

    protected static $_rules = [
        'min' => ['int' => true],
        'max' => ['int' => true],
        'length' => ['int' => true],
        'allowSpaces' => ['bool' => true],
        'hasUser' => ['bool' => true],
        'hasEmail' => ['match' => '/^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/i', 'bool' => true],
        'lower' => ['match' => '![a-z]!', 'int' => true],
        'upper' => ['match' => '![A-Z]!', 'int' => true],
        'digit' => ['match' => '![\d]!', 'int' => true],
        'special' => ['match' => '![\W]!', 'int' => true]
    ];

    public function init()
    {
        parent::init();

        foreach (self::$_rules as $rule => $setup) {
            if (isset($this->$rule) && !empty($setup['int']) && $setup['int'] && (!is_int($this->$rule) || $this->$rule < 0)) {
                throw new InvalidConfigException("The property '{$rule}' must be a positive integer.");
            }
            if (isset($this->$rule) && !empty($setup['bool']) && $setup['bool'] && !is_bool($this->$rule)) {
                throw new InvalidConfigException("The property '{$rule}' must be either true or false.");
            }
        }

        if (isset($this->max)) {
            $chars = $this->lower + $this->upper + $this->digit + $this->special;

            if ($chars > $this->max) {
                throw new InvalidConfigException(
                    "Total number of required characters {$chars} is greater than maximum allowed {$this->max}. " .
                    "Validation is not possible!"
                );
            }
        }
    }

    public function validateAttribute($model, $attribute)
    {
        $value = Html::getAttributeValue($model, $attribute);

        if (!is_string($value)) {
            $this->addError($model, $attribute, Yii::t('passwordvalidation', 'string'));
            return;
        }

        $label = $model->getAttributeLabel($attribute);
        $username = !$this->hasUser ? '' : Html::getAttributeValue($model, $this->userAttribute);
        $temp = [];

        foreach (self::$_rules as $rule => $setup) {
            $errorMessage = Yii::t('passwordvalidation', $rule);

            if ($rule === 'hasUser' && $this->hasUser && !empty($value) && !empty($username) && strpos($value, $username) !== false ||
                $rule === 'hasEmail' && $this->hasEmail && preg_match($setup['match'], $value, $matches) ||
                $rule === 'allowSpaces' && strpos($value, ' ') !== false) {
                $this->addError($model, $attribute, $errorMessage, ['attribute' => $label]);
            }
            elseif ($rule !== 'hasEmail' && $rule !== 'hasUser' && !empty($setup['match'])) {
                $count = preg_match_all($setup['match'], $value, $temp);
                if ($count < $this->$rule) {
                    $this->addError($model, $attribute, $errorMessage, ['attribute' => $label, 'n' => $this->$rule, 'found' => $count]);
                }
            }
            else {
                $length = mb_strlen($value, Yii::$app->charset);
                $test = false;
                if ($rule === 'length') {
                    $test = ($length !== $this->$rule);
                } elseif ($rule === 'min') {
                    $test = ($length < $this->$rule);
                } elseif ($rule === 'max') {
                    $test = ($length > $this->$rule);
                }
                if ($this->$rule !== null && $test) {
                    $this->addError($model, $attribute, $errorMessage, [
                        'attribute' => $label . ' (' . $rule . ' , ' . $this->$rule . ')',
                        'n' => $this->$rule,
                        'found' => $length
                    ]);
                }
            }
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        PasswordValidationAsset::register($view);

        $label = $model->getAttributeLabel($attribute);
        $options = ['strError' => Html::encode(Yii::t('passwordvalidation', 'string', ['attribute' => $label]))];
        $options['userField'] = '#' . Html::getInputId($model, $this->userAttribute);

        foreach (self::$_rules as $rule => $setup) {
            if ($this->$rule !== null) {
                $options[$rule] = $this->$rule;
                $options[$rule.'Error'] = Html::encode(Yii::t('passwordvalidation', $rule, ['n' => $this->$rule, 'attribute' => $label]));
            }
        }

        return "passwordvalidation.validate(value, messages, " . Json::encode($options) . ");";
    }
}
