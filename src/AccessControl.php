<?php

namespace fafcms\helpers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

/**
 * Class AccessControl
 * @package app\helpers
 */
class AccessControl extends \yii\filters\AccessControl
{
    /**
     * @param false|\yii\web\User $user
     * @throws ForbiddenHttpException
     */
    protected function denyAccess($user)
    {
        if ($user !== false && $user->getIsGuest()) {
            if (Yii::$app->getRequest()->getIsAjax()) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                Yii::$app->user->setReturnUrl(Yii::$app->request->getReferrer());

                throw new ForbiddenHttpException(json_encode([
                    'url' => Yii::$app->user->loginUrl,
                    'modalFixed' => true,
                    'headerRemoveFromContent' => true,
                    'headerSelector' => '.form-header',
                    'footerRemoveFromContent' => true,
                    'footerSelector' => '.form-footer',
                    'modalSelector' => '.form-content'
                ]), 42);
            }

            $user->loginRequired();
        } else {
            throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
        }
    }
}
