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

/**
 * Class Content
 *
 * @package fafcms\helpers\abstractions
 */
abstract class Content extends BaseObject
{
    /**
     * @var string
     */
    public static $fullyQualifiedTypeName;

    /**
     * @var bool
     */
    public static $isDefault = false;

    /**
     * @var string[]
     */
    public $label = ['fafcms-core', 'Default View'];

    /**
     * @var bool
     */
    public $isPublic = true;

    /**
     * @var bool
     */
    public $isStatic = true;

    /**
     * @var array
     */
    public $items = [];
}
