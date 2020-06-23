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
 * Trait HashIdTrait
 * @package fafcms\helpers\traits
 */
trait HashIdTrait
{
    /**
     * @return string
     */
    public function getHashId(): string
    {
        return Yii::$app->fafcms->idToHash($this->id, static::class);
    }
}
