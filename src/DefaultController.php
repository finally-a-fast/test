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

use fafcms\blogmanager\models\Article;
use fafcms\fafcms\controllers\FrontendController;
use fafcms\fafcms\models\Domain;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Class DefaultController
 * @package fafcms\fafcms\controllers
 */
class DefaultController extends Controller
{
    public static $modelClass;

    public function init()
    {
        if (static::$modelClass === null) {
            throw new InvalidConfigException('You must set "public static $modelClass" in controller "'.static::class.'".');
        }

        parent::init();
    }

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => Yii::$app->fafcms->accessRules['default'],
            ],
        ];
    }

    public function getIndexButtons($filterModel)
    {
        return [[
            'icon' => 'mdi mdi-arrow-left',
            'label' => Yii::t('fafcms-core', 'Back'),
            'url' => Yii::$app->getUser()->getReturnUrl()
        ], [
            'options' => [
                'class' => 'primary'
            ],
            'icon' => 'mdi mdi-plus',
            'label' => Yii::t('fafcms-core', 'Create {modelClass}', [
                'modelClass' => $filterModel->getEditData()['singular'],
            ]),
            'url' => ['update']
        ]];
    }

    /**
     * Lists all available translation files.
     * @return array|string
     * @throws InvalidConfigException
     */
    public function actionIndex()
    {
        return $this->renderActionContent(Yii::$app->fafcms->renderIndexView([
            'modelClass' => static::$modelClass,
        ]));
    }

    /**
     * Creates a model.
     * @return array|string
     * @throws InvalidConfigException
     */
    public function actionCreate()
    {
        return $this->renderActionContent(Yii::$app->fafcms->renderEditView([
            'modelClass' => static::$modelClass
        ]));
    }

    /**
     * Updates a model.
     * @param string|integer $id
     * @return array|string
     * @throws InvalidConfigException
     */
    public function actionUpdate($id)
    {
        return $this->renderActionContent(Yii::$app->fafcms->renderEditView([
            'modelClass' => static::$modelClass,
            'modelId' => $id
        ]));
    }

    /**
     * Deletes an existing model.
     * If deletion is successful, the browser will be redirected to the previous page.
     *
     * @param integer $id
     *
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException if the model cannot be found
     * @throws \Throwable
     */
    public function actionDelete($id)
    {
        $allowed = Yii::$app->user->can('delete', ['modelClass' => static::$modelClass, 'modelId' => $id]);

        if (!$allowed) {
            throw new ForbiddenHttpException(Yii::t('fafcms-core', 'Your not allowed to {action} {model}.', ['action' => Yii::t('fafcms-core', 'delete'), 'model' => static::$modelClass::instance()->getEditDataPlural()]));
        }

        $model = static::$modelClass::findOne($id);

        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        if ($this->deleteModel($model) || $model->delete()) {
            Yii::$app->session->setFlash('success', Yii::t('fafcms-core', '{modelClass} has been deleted!', [
                'modelClass' => $model->getEditData()['singular'],
            ]));
        } else {
            Yii::$app->session->setFlash('error', Yii::t('fafcms-core', 'Error while deleting {modelClass}!', [
                'modelClass' => $model->getEditData()['singular']
            ]).'<br><br>'.implode('<br><br>', $model->getErrorSummary(true)));
        }

        return $this->goBack(Yii::$app->getRequest()->getReferrer());
    }

    /**
     * @param $content
     *
     * @return array|string
     */
    public function renderActionContent($content)
    {
        $renderMode = FrontendController::getRenderMode();
        return FrontendController::renderSiteContent($this, $renderMode, $content);
    }

    /**
     * @param ActiveRecord $model
     *
     * @return bool
     * @throws \Throwable
     */
    protected function deleteModel(ActiveRecord $model): bool
    {
        if ($model->hasAttribute('status')) {
            if ($model->beforeDelete()) {
                $model->status = $model::STATUS_DELETED;
                $model->afterDelete();
                return $model->save(false);
            }
        }

        return false;
    }
}
