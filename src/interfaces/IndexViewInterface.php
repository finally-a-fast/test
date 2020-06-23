<?php
/**
 * @author Christoph Möke <christophmoeke@gmail.com>
 * @copyright Copyright (c) 2019 Finally a fast
 * @license https://www.finally-a-fast.com/packages/fafcms-helpers/license MIT
 * @link https://www.finally-a-fast.com/packages/fafcms-helpers
 * @see https://www.finally-a-fast.com/packages/fafcms-helpers/docs Documentation of fafcms-helpers
 * @since File available since Release 1.0.0
 */

namespace fafcms\helpers\interfaces;

use yii\db\ActiveRecord;

/**
 * Interface IndexViewInterface
 * @package fafcms\helpers\interfaces
 */
interface IndexViewInterface
{
    /**
     * @return array
     */
    public static function indexView(): array;
}
