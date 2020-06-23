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
 * Trait OptionTrait
 * @package fafcms\helpers\traits
 */
trait OptionTrait
{
    /**
     * @param bool $empty
     * @param array|null $select
     * @param array|null $sort
     * @param array|null $where
     * @param array|null $joinWith
     * @param string|null $emptyLabel
     * @return array
     */
    public static function getOptions(bool $empty = true, array $select = null, array $sort = null, array $where = null, array $joinWith = null, string $emptyLabel = null): array
    {
        if ($empty) {
            return ['' => $emptyLabel??Yii::t('fafcms-core', '- None - ')];
        }

        return [];
    }
}
