<?php
/**
 * @author Christoph Möke <christophmoeke@gmail.com>
 * @copyright Copyright (c) 2019 Finally a fast
 * @license https://www.finally-a-fast.com/packages/fafcms-helpers/license MIT
 * @link https://www.finally-a-fast.com/packages/fafcms-helpers
 * @see https://www.finally-a-fast.com/packages/fafcms-helpers/docs Documentation of fafcms-helpers
 * @since File available since Release 1.0.0
 */

namespace fafcms\helpers\abstractions;

use yii\base\BaseObject;
use yii\base\Model;
use yiiui\yii2materialize\ActiveForm;

/**
 * Class FormInput
 * @package fafcms\helpers\abstractions
 */
abstract class FormInput extends BaseObject
{
    /**
     * @var ActiveForm
     */
    public $form;

    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $inputName;

    /**
     * @var string
     */
    public $inputId;

    /**
     * @var array
     */
    public $labelOptions = [];

    /**
     * @var string
     */
    public $description;

    /**
     * @var array
     */
    public $options = [];

    /**
     * @param bool $isWidget
     *
     * @return array
     */
    public function getInputOptions(bool $isWidget = false): array
    {
        $options = $this->options;

        if ($isWidget) {
            if (isset($options['name'])) {
                $options['options']['name'] = $options['name'];
            }

            if (isset($options['id'])) {
                $options['options']['id'] = $options['id'];
            }

            unset($options['id'], $options['name']);
        }

        return $options;
    }

    /**
     * @return string
     */
    abstract public function run(): string;
}
