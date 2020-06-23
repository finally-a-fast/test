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
use yii\base\InvalidConfigException;
use fafcms\helpers\abstractions\PluginModule;
use fafcms\helpers\abstractions\Setting;
use fafcms\fafcms\components\FafcmsComponent;
use fafcms\settingmanager\Bootstrap as SettingmanagerBootstrap;
use fafcms\settingmanager\Module as SettingmanagerModule;

/**
 * Class PluginSetting
 * @package fafcms\helpers\classes
 */
class PluginSetting extends Setting
{
    /**
     * @var PluginModule
     */
    private $module;

    /**
     * @var bool
     */
    public $projectBased = true;

    /**
     * @var bool
     */
    public $languageBased = true;

    /**
     * PluginSetting constructor.
     * @param PluginModule $module
     * {@inheritdoc}
     */
    public function __construct(PluginModule $module, $config = [])
    {
        $this->module = $module;
        parent::__construct($config);
    }

    /**
     * @param int|null $projectId
     * @param int|null $languageId
     * @param string|null $variation
     * @return string
     */
    protected function getFullName(int $projectId = null, int $languageId = null, string $variation = null): string
    {
        return $this->name;
    }

    /**
     * @param int|null $projectId
     * @return int
     */
    protected function getCleanProjectId(?int $projectId): int
    {
        if ($this->projectBased && $projectId === null) {
            $projectId = Yii::$app->fafcms->getCurrentProjectId();
        }

        if (!$this->projectBased || $projectId === null) {
            $projectId = 0;
        }

        return $projectId;
    }

    /**
     * @param int|null $languageId
     * @return int
     */
    protected function getCleanLanguageId(?int $languageId): int
    {
        if ($this->languageBased && $languageId === null) {
            $languageId = Yii::$app->fafcms->getCurrentLanguageId();
        }

        if (!$this->languageBased || $languageId === null) {
            $languageId = 0;
        }

        return $languageId;
    }

    /**
     * @param string|null $variation
     * @return string
     */
    protected function getCleanVariation(?string $variation): string
    {
        if ($variation === null) {
            $variation = '';
        }

        return $variation;
    }

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function setValue($value, ...$params): bool
    {
        $projectId = $this->getCleanProjectId($params[0]??null);
        $languageId = $this->getCleanLanguageId($params[1]??null);
        $variation = $this->getCleanVariation($params[2]??null);
        $name = $this->getFullName($projectId, $languageId, $variation);

        if ($this->valueType === FafcmsComponent::VALUE_TYPE_CUSTOM) {
            throw new InvalidConfigException(get_class($this) . ' must implement saveValue method.');
        }

        if (($settingModule = Yii::$app->getModule(SettingmanagerBootstrap::$id)) instanceof SettingmanagerModule) {
            /**
             * @var $settingModule SettingmanagerModule
             */
            $result = $settingModule->createOrUpdateSetting($this->getId(), $name, $projectId, $languageId, $variation, $this->label, $this->description, $this->valueType, $value);
            $this->errors = $settingModule->getErrors($this->getId(), $name);

            return $result;
        }

        throw new InvalidConfigException(get_class($this) . ' requires "fafcms\settingmanager\Module". Please install "finally-a-fast/fafcms-module-settingmanager" and enable the module or overwrite the setValue method.');
    }

    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function getValue(...$params)
    {
        $projectId = $this->getCleanProjectId($params[0]??null);
        $languageId = $this->getCleanLanguageId($params[1]??null);
        $variation = $this->getCleanVariation($params[2]??null);
        $name = $this->getFullName($projectId, $languageId, $variation);

        if ($this->valueType === FafcmsComponent::VALUE_TYPE_CUSTOM) {
            throw new InvalidConfigException(get_class($this) . ' must implement getValue method.');
        }

        if (($settingModule = Yii::$app->getModule(SettingmanagerBootstrap::$id)) instanceof SettingmanagerModule) {
            /**
             * @var $settingModule SettingmanagerModule
             */
            $settingValue = $settingModule->getSettingValue($this->getId(), $name, $projectId, $languageId, $variation);

            if ($settingValue === null && $this->defaultValue !== null) {
                if ($this->defaultValue instanceof \Closure) {
                    $settingValue = call_user_func($this->defaultValue, $this->getId(), $name, $projectId, $languageId, $variation, true);
                } else {
                    $settingValue = $this->defaultValue;
                }

                $result = $settingModule->createOrUpdateSetting($this->getId(), $name, $projectId, $languageId, $variation, $this->label, $this->description, $this->valueType, $settingValue);
            }

            return $settingValue;
        }

        throw new InvalidConfigException(get_class($this) . ' requires "fafcms\settingmanager\Module". Please install "finally-a-fast/fafcms-module-settingmanager" and enable the module or overwrite the getValue method.');
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->module->id;
    }
}
