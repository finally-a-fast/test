<?php
/**
 * @author Christoph Möke <christophmoeke@gmail.com>
 * @copyright Copyright (c) 2019 Finally a fast
 * @license https://www.finally-a-fast.com/packages/fafcms-helpers/license MIT
 * @link https://www.finally-a-fast.com/packages/fafcms-helpers
 * @see https://www.finally-a-fast.com/packages/fafcms-helpers/docs Documentation of fafcms-helpers
 * @since File available since Release 1.0.0
 */

namespace fafcms\helpers\traits;

use Yii;

/**
 * Trait BeautifulModelTrait
 * @package fafcms\helpers\traits
 */
trait BeautifulModelTrait
{
    /**
     * @param \yii\db\ActiveRecord | array $model
     * @return string
     */
    abstract public static function editDataUrl($model): string;

    /**
     * @return string
     */
    public function getEditDataUrl(): string
    {
        return self::editDataUrl($this);
    }

    /**
     * @param \yii\db\ActiveRecord | array $model
     * @return string
     */
    abstract public static function editDataIcon($model): string;

    /**
     * @return string
     */
    public function getEditDataIcon(): string
    {
        return self::editDataIcon($this);
    }

    /**
     * @param \yii\db\ActiveRecord | array $model
     * @return string
     */
    abstract public static function editDataPlural($model): string;

    /**
     * @return string
     */
    public function getEditDataPlural(): string
    {
        return self::editDataPlural($this);
    }

    /**
     * @param \yii\db\ActiveRecord | array $model
     * @return string
     */
    abstract public static function editDataSingular($model): string;

    /**
     * @return string
     */
    public function getEditDataSingular(): string
    {
        return self::editDataSingular($this);
    }

    /**
     * @param \yii\db\ActiveRecord | array $model
     * @return array
     */
    public function editData($model): array
    {
        return [
            'url' => $model->getEditDataUrl(),
            'icon' => $model->getEditDataIcon(),
            'plural' => $model->getEditDataPlural(),
            'singular' => $model->getEditDataSingular()
        ];
    }

    /**
     * @return array
     */
    public function getEditData(): array
    {
        return self::editData($this);
    }

    /**
     * @param \yii\db\ActiveRecord | array $model
     * @param bool $html
     * @param array $params
     * @return string
     */
    abstract public static function extendedLabel($model, bool $html = true, array $params = []): string;

    /**
     * @param bool $html
     * @param mixed ...$params
     * @return string
     */
    public function getExtendedLabel(bool $html = true, ...$params): string
    {
        return self::extendedLabel($this, $html, $params);
    }
}
