<?php
/**
 * @author Christoph Möke <christophmoeke@gmail.com>
 * @copyright Copyright (c) 2019 Finally a fast
 * @license https://www.finally-a-fast.com/packages/fafcms-helpers/license MIT
 * @link https://www.finally-a-fast.com/packages/fafcms-helpers
 * @see https://www.finally-a-fast.com/packages/fafcms-helpers/docs Documentation of fafcms-helpers
 * @since File available since Release 1.0.0
 */

namespace fafcms\helpers;

use Yii;

/**
 * Class Request
 * @package fafcms\helpers
 */
class Request extends \yii\web\Request
{
    /**
     * @var array
     */
    public $noCsrfValidationRoutes = [];

    public function validateCsrfToken($clientSuppliedToken = null)
    {
        if (in_array(Yii::$app->getRequest()->getPathInfo(), $this->noCsrfValidationRoutes)) {
            return true;
        }

        return parent::validateCsrfToken($clientSuppliedToken); // TODO: Change the autogenerated stub
    }
}
