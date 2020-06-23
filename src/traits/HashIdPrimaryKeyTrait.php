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

/**
 * Trait HashIdPrimaryKeyTrait
 * @package fafcms\helpers\traits
 */
trait HashIdPrimaryKeyTrait
{
    public static function primaryKey()
    {
        $primaryKey = static::getTableSchema()->primaryKey;

        if ($primaryKey[0] === 'id') {
            $primaryKey[0] = 'hashId';
        }

        return $primaryKey;
    }
}
