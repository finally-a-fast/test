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

use fafcms\helpers\classes\ContentItemSetting;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

/**
 * Class ContentItem
 * @package fafcms\helpers\abstractions
 */
abstract class ContentItem extends BaseObject
{
    const TYPE_ITEM = 1;
    const TYPE_CONTAINER = 2;
    const TYPE_GRID = 3;
    const TYPE_COLUMN = 4;

    /**
     * @var int
     */
    public $type = self::TYPE_ITEM;

    /**
     * @var array Contents of item
     */
    public $contents;

    /**
     * @var bool
     */
    public $preRenderContent = true;

    /**
     * @var string
     */
    public $renderedContent = '';

    /**
     * @var array
     */
    public $settings = [];

    /**
     * @return bool
     */
    public function isItem(): bool
    {
        return $this->type === self::TYPE_ITEM;
    }

    /**
     * @return bool
     */
    public function isContainer(): bool
    {
        return $this->type === self::TYPE_CONTAINER;
    }

    /**
     * @return bool
     */
    public function isGrid(): bool
    {
        return $this->type === self::TYPE_GRID;
    }

    /**
     * @return bool
     */
    public function isColumn(): bool
    {
        return $this->type === self::TYPE_COLUMN;
    }

    /**
     * @return array
     */
    public function editorOptions(): array
    {
        return ['tag' => 'div'];
    }

    /**
     * @return ContentItemSetting[]
     */
    public function itemSettings(): ?array
    {
        return null;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function getSetting(string $name)
    {
        $settings = ArrayHelper::index($this->itemSettings()??[], 'name');

        if (isset($settings[$name])) {
            return $settings[$name]->getValue();
        }

        return null;
    }

    /**
     * @return string
     */
    abstract public function label(): string;

    /**
     * @return string
     */
    abstract public function description(): string;

    /**
     * @return string|array
     */
    abstract public function run();
}
