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

use Yii;

/**
 * Class UserSetting
 * @package fafcms\helpers\classes
 */
class UserSetting extends PluginSetting
{
    /**
     * @var int User id
     */
    public $userId;

    /**
     * @var bool
     */
    public $projectBased = false;

    /**
     * @var bool
     */
    public $languageBased = false;

    /**
     * @param string|null $variation
     * @return string
     */
    protected function getCleanVariation(?string $variation): string
    {
        if ($variation === null) {
            $variation = '';
        }

        if ($this->userId === null && isset(Yii::$app->components['user']) && !Yii::$app->user->isGuest) {
            $this->userId = Yii::$app->user->id;
        }

        return $variation.'\U_'.$this->userId;
    }
}
