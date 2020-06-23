<?php
/**
 * @author Christoph Möke <christophmoeke@gmail.com>
 * @copyright Copyright (c) 2020 Finally a fast
 * @license https://www.finally-a-fast.com/packages/fafcms-helpers/license MIT
 * @link https://www.finally-a-fast.com/packages/fafcms-helpers
 * @see https://www.finally-a-fast.com/packages/fafcms-helpers/docs Documentation of fafcms-helpers
 * @since File available since Release 1.0.0
 */

namespace fafcms\helpers\assets;

use yii\web\AssetBundle;

/**
 * Class MaskedInputAsset
 *
 * @package fafcms\helpers\assets
 */
class MaskedInputAsset extends AssetBundle
{
    public $sourcePath = '@vendor/robinherbots/jquery.inputmask/dist';

    public $js = [
        'jquery.inputmask.min.js',
    ];
    
    public $depends = [
        'yii\web\YiiAsset',
    ];
}
