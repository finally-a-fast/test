<?php
/**
 * @author Christoph Möke <christophmoeke@gmail.com>
 * @copyright Copyright (c) 2019 Finally a fast
 * @license https://www.finally-a-fast.com/packages/fafcms-helpers/license MIT
 * @link https://www.finally-a-fast.com/packages/fafcms-helpers
 * @see https://www.finally-a-fast.com/packages/fafcms-helpers/docs Documentation of fafcms-helpers
 * @since File available since Release 1.0.0
 */

namespace fafcms\helpers\classes;

use fafcms\helpers\abstractions\ContentItem;
use fafcms\helpers\abstractions\Setting;
use Closure;

/**
 * Class ContentItemSetting
 * @package fafcms\helpers\classes
 */
class ContentItemSetting extends Setting
{
    /**
     * @var ContentItem
     */
    public $contentItem;

    /**
     * {@inheritdoc}
     */
    public function __construct(ContentItem $contentItem, $config = [])
    {
        $this->contentItem = $contentItem;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value, ...$params): bool
    {
        $this->contentItem->settings[$this->name] = $value;
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getValue(...$params)
    {
        if (!isset($this->contentItem->settings[$this->name])) {
            if ($this->defaultValue instanceof Closure) {
                $this->contentItem->settings[$this->name] = call_user_func($this->defaultValue, $this);
            } else {
                $this->contentItem->settings[$this->name] = $this->defaultValue;
            }
        }

        return $this->contentItem->settings[$this->name];
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->name;
    }
}
