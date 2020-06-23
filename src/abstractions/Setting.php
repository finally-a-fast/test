<?php
/**
 * @author Christoph Möke <christophmoeke@gmail.com>
 * @copyright Copyright (c) 2019 Finally a fast
 * @license https://www.finally-a-fast.com/packages/fafcms-helpers/license MIT
 * @link https://www.finally-a-fast.com/packages/fafcms-helpers
 * @see https://www.finally-a-fast.com/packages/fafcms-helpers/docs Documentation of fafcms-helpers
 * @since 1.0.0 File available since Release
 */

namespace fafcms\helpers\abstractions;

use fafcms\fafcms\components\FafcmsComponent;
use fafcms\fafcms\inputs\TextInput;
use fafcms\fafcms\models\SettingForm;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yiiui\yii2materialize\ActiveForm;
use yiiui\yii2materialize\Html;

/**
 * Class Setting
 * @package fafcms\helpers\abstractions
 *
 * @property string $id
 * @property mixed $value
 */
abstract class Setting extends BaseObject
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $label;

    /**
     * @var string
     */
    public $description;

    /**
     * @var int
     */
    public $valueType = FafcmsComponent::VALUE_TYPE_VARCHAR;

    /**
     * @var int
     */
    public $inputType = TextInput::class;

    /**
     * @var array
     */
    public $labelOptions = [];

    /**
     * @var array
     */
    public $inputOptions = [];

    /**
     * @var array
     */
    public $containerOptions = [];

    /**
     * @var array
     */
    public $fieldConfig = [];

    /**
     * @var null|array
     */
    public $items;

    /**
     * @var mixed
     */
    public $defaultValue;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @param       $value
     * @param mixed ...$params
     *
     * @return bool
     */
    abstract public function setValue($value, ...$params): bool;

    /**
     * @param mixed ...$params
     *
     * @return mixed
     */
    abstract public function getValue(...$params);

    /**
     * @return string
     */
    abstract public function getId(): string;

    /**
     * @param $form
     * @param $model
     *
     * @return string
     * @throws InvalidConfigException
     */
    public function getContainer($form, $model): string
    {
        $options = $this->containerOptions;
        Html::addCssClass($options, 'plugin-setting-container');
        $options['id'] = $this->getId() . '-setting-container';

        return Html::tag('div', $this->getInput($form, $model), $options);
    }

    /**
     * @param ActiveForm  $form
     * @param SettingForm $model
     *
     * @return string
     */
    public function getInput(ActiveForm $form, SettingForm $model): string
    {
        $fieldConfig = $this->fieldConfig;
        $options = $this->inputOptions;
        Html::addCssClass($options, 'plugin-setting-input');
        $options['id'] = $this->getId() . '-setting-input';

        $fieldConfig['options'] = $options;

        if ($this->items !== null) {
            $fieldConfig['items'] = $this->items;
        }

        $fieldConfig['description'] = $this->description;

        $inputName = Html::getInputName($model, $this->name);
        $inputId = Html::getInputId($model, $this->name);

        return Yii::$app->fafcms->getInput(
            $form,
            $model,
            $this->name,
            $this->inputType,
            $this->labelOptions,
            $fieldConfig,
            $inputName,
            $inputId
        );
    }
}
