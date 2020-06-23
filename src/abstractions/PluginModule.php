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

use fafcms\helpers\classes\PluginSetting;
use fafcms\helpers\classes\UserSetting;
use Yii;
use yii\base\ErrorException;
use yii\base\Module;
use yii\helpers\ArrayHelper;

/**
 * Class PluginModule
 * @package fafcms\helpers\abstractions
 *
 * @property PluginSetting[] $pluginSettings
 * @property string $pluginSettingValue
 * @property UserSetting[] $userSettings
 * @property string $settingValueBySettingType
 * @property array $userSettingRules
 * @property string $userSettingValue
 * @property array $pluginSettingRules
 */
abstract class PluginModule extends Module
{
    public const TYPE_FRONTEND = 'frontend';
    public const TYPE_BACKEND = 'backend';

    /**
     * @var string
     */
    public $type;

    /**
     * @var array
     */
    public $accessRules = [];

    /**
     * @var string
     */
    public $packageName;

    /**
     * @return static
     * @throws ErrorException
     */
    public static function getLoadedModule(): self
    {
        $loadedModule = null;
        $modules = Yii::$app->fafcms->getLoadedPluginsByIndex('module');
        $pluginClass = static::class;

        if (isset($modules[$pluginClass])) {
            $moduleBootstrap = $modules[$pluginClass]['bootstrap'];

            /**
             * @var $loadedModule null|self
             */
            $loadedModule = Yii::$app->getModule($moduleBootstrap::$id);
        }

        if ($loadedModule === null) {
            throw new ErrorException('Requested Module is not loaded');
        }

        return $loadedModule;
    }

    /**
     * @return array
     */
    public function getPluginSettingRules(): array
    {
        return [];
    }

    /**
     * @return PluginSetting[]
     */
    public function getPluginSettings(): array
    {
        return ArrayHelper::index($this->pluginSettingDefinitions(), 'name');
    }

    /**
     * @return PluginSetting[]
     */
    protected function pluginSettingDefinitions(): array
    {
        return [];
    }

    /**
     * @param string $name
     * @param int|null $projectId
     * @param int|null $languageId
     * @param string|null $variation
     * @return mixed|null
     */
    public function getPluginSettingValue(string $name, int $projectId = null, int $languageId = null, string $variation = null)
    {
        return $this->getSettingValueBySettingType('getPluginSettings', $name, $projectId, $languageId, $variation);
    }

    /**
     * @param string $type
     * @param string $name
     * @param int|null $projectId
     * @param int|null $languageId
     * @param string|null $variation
     * @return mixed|null
     */
    public function getSettingValueBySettingType(string $type, string $name, int $projectId = null, int $languageId = null, string $variation = null)
    {
        if (isset($this->$type()[$name])) {
            return $this->$type()[$name]->getValue($projectId, $languageId, $variation);
        }

        return null;
    }

    /**
     * @param string $name
     * @param $value
     * @param int|null $projectId
     * @param int|null $languageId
     * @param string|null $variation
     * @return bool
     */
    public function setPluginSettingValue(string $name, $value, int $projectId = null, int $languageId = null, string $variation = null): bool
    {
        return $this->setSettingValueBySettingType('getPluginSettings', $name, $value, $projectId, $languageId, $variation);
    }

    /**
     * @param string $type
     * @param string $name
     * @param $value
     * @param int|null $projectId
     * @param int|null $languageId
     * @param string|null $variation
     * @return bool
     */
    public function setSettingValueBySettingType(string $type, string $name, $value, int $projectId = null, int $languageId = null, string $variation = null): bool
    {
        if (isset($this->$type()[$name])) {
            return $this->$type()[$name]->setValue($value, $projectId, $languageId, $variation);
        }

        return false;
    }

    /**
     * @return array
     */
    public function getUserSettingRules(): array
    {
        return [];
    }

    /**
     * @return UserSetting[]
     */
    public function getUserSettings(): array
    {
        return ArrayHelper::index($this->userSettingDefinitions(), 'name');
    }

    /**
     * @return UserSetting[]
     */
    protected function userSettingDefinitions(): array
    {
        return [];
    }

    /**
     * @param string $name
     * @param int|null $projectId
     * @param int|null $languageId
     * @param string|null $variation
     * @return mixed|null
     */
    public function getUserSettingValue(string $name, int $projectId = null, int $languageId = null, string $variation = null)
    {
        return $this->getSettingValueBySettingType('getUserSettings', $name, $projectId, $languageId, $variation);
    }

    /**
     * @param string $name
     * @param $value
     * @param int|null $projectId
     * @param int|null $languageId
     * @param string|null $variation
     * @return bool
     */
    public function setUserSettingValue(string $name, $value, int $projectId = null, int $languageId = null, string $variation = null): bool
    {
        return $this->setSettingValueBySettingType('getUserSettings', $name, $value, $projectId, $languageId, $variation);
    }
}
