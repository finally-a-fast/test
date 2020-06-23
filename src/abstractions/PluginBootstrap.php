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

use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\ErrorException;

/**
 * Class PluginBootstrap
 * @package fafcms\helpers\abstractions
 */
abstract class PluginBootstrap implements BootstrapInterface
{
    /**
     * @var string
     */
    public static $id;

    /**
     * @var string
     */
    public static $tablePrefix;

    /**
     * @var null|array
     */
    public static $tablePrefixTargets;

    /**
     * @return string
     */
    public static function getTablePrefix(): string
    {
        if (static::$tablePrefix === null) {
            return static::$id.'_';
        }

        return static::$tablePrefix;
    }

    /**
     * @param Application $app
     * @param PluginModule $module
     * @return bool
     */
    protected function bootstrapTranslations(Application $app, PluginModule $module): bool
    {
        return true;
    }

    /**
     * @param Application $app
     * @param PluginModule $module
     * @return bool
     */
    protected function bootstrapApp(Application $app, PluginModule $module): bool
    {
        return true;
    }

    /**
     * @param Application $app
     * @param PluginModule $module
     * @return bool
     */
    protected function bootstrapWebApp(Application $app, PluginModule $module): bool
    {
        return true;
    }

    /**
     * @param Application $app
     * @param PluginModule $module
     * @return bool
     */
    protected function bootstrapConsoleApp(Application $app, PluginModule $module): bool
    {
        return true;
    }

    /**
     * @param Application $app
     * @param PluginModule $module
     * @return bool
     */
    protected function bootstrapFrontendApp(Application $app, PluginModule $module): bool
    {
        return true;
    }

    /**
     * @param Application $app
     * @param PluginModule $module
     * @return bool
     */
    protected function bootstrapBackendApp(Application $app, PluginModule $module): bool
    {
        return true;
    }

    /**
     * @param Application $app
     * @throws ErrorException
     */
    public function bootstrap($app): void
    {
        if ($app->hasModule(static::$id) && ($module = $app->getModule(static::$id)) instanceof PluginModule) {
            /**
             * @var $module PluginModule
             */
            if ($module->type === null && isset($app->fafcms)) {
                $module->type = $app->fafcms->getAppType();
            }

            if (!$this->bootstrapTranslations($app, $module)) {
                throw new ErrorException('Cannot bootstrap translations');
            }

            if (!$this->bootstrapApp($app, $module)) {
                throw new ErrorException('Cannot bootstrap app');
            }

            if ($app instanceof \yii\web\Application) {
                if (!$this->bootstrapWebApp($app, $module)) {
                    throw new ErrorException('Cannot bootstrap web app');
                }

                if ($module->type === PluginModule::TYPE_BACKEND) {
                    if (!$this->bootstrapBackendApp($app, $module)) {
                        throw new ErrorException('Cannot bootstrap backend app');
                    }
                } elseif ($module->type === PluginModule::TYPE_FRONTEND) {
                    if (!$this->bootstrapFrontendApp($app, $module)) {
                        throw new ErrorException('Cannot bootstrap frontend app');
                    }
                }
            } elseif (!$this->bootstrapConsoleApp($app, $module)) {
                throw new ErrorException('Cannot bootstrap console app');
            }
        }
    }
}
