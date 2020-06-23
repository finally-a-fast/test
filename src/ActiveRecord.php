<?php

namespace fafcms\helpers;

use fafcms\fafcms\queries\DefaultQuery;
use fafcms\helpers\behaviors\AttributesBehavior;
use fafcms\helpers\traits\HashIdTrait;
use Yii;
use Closure;
use yii\base\InvalidCallException;
use yii\base\UnknownMethodException;
use yii\base\UnknownPropertyException;
use yii\helpers\ArrayHelper;

/**
 * Class ActiveRecord
 * @package fafcms\helpers
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    use HashIdTrait;

    public const EVENT_BEFORE_ACTIVATE   = 'beforeActivate';
    public const EVENT_BEFORE_DEACTIVATE = 'beforeDeactivate';

    public const STATUS_ACTIVE   = 'active';
    public const STATUS_INACTIVE = 'inactive';
    public const STATUS_DELETED  = 'deleted';

    public const RESULT_ACTION_MERGE = 'merge';
    public const RESULT_ACTION_BOOL  = 'bool';

    // region todo move to ModelTrait
    /**
     * @var string $editableScenario
     */
    public $editableScenario;

    /**
     * {@inheritDoc}
     */
    public function beforeValidate()
    {
        if ($this->editableScenario !== null && isset($this->scenarios()[$this->editableScenario])) {
            $this->setScenario($this->editableScenario);
        }

        return parent::beforeValidate();
    }
    // endregion todo move to ModelTrait

    /**
     * @param array $row
     *
     * @return ActiveRecord|object|static
     * @throws \yii\base\InvalidConfigException
     */
    public static function instantiate($row)
    {
        return \Yii::createObject(static::class);
    }

    /**
     * Declares the name of the database table associated with this AR class.
     * By default this method returns the class name as the table name by calling [[Inflector::camel2id()]].
     * @return string the table name
     */
    public static function prefixableTableName()
    {
        return parent::tableName();
    }

    public static function find()
    {
        if (isset(Yii::$app->fafcms->modelQueryMap[static::class])) {
            return new Yii::$app->fafcms->modelQueryMap[static::class](static::class);
        }
        
        return new DefaultQuery(static::class);
    }

    /**
     * Warning - You should should not overwrite this. Please only overwrite [[ActiveRecord::prefixableTableName()]]!
     * {@inheritdoc}
     */
    public static function tableName()
    {
        $classParts = explode('\\', static::class);
        $lastContentmeta = null;
        $tablePrefix = '';

        do {
            $classTarget = implode('\\', $classParts);

            if (isset(Yii::$app->fafcms->modelTablePrefixes[$classTarget])) {
                $tablePrefix = Yii::$app->fafcms->modelTablePrefixes[$classTarget];
                break;
            }

            if (isset(Yii::$app->fafcms->modelTablePrefixes[$classTarget.'\\'])) {
                $tablePrefix = Yii::$app->fafcms->modelTablePrefixes[$classTarget.'\\'];
                break;
            }

            array_pop($classParts);
        } while (count($classParts) > 0);

        return str_replace('%', '%'.$tablePrefix, static::prefixableTableName());
    }

    /**
     * @param string $methodName
     * @param array  $data
     * @param bool|array|null   $result
     * @param string $resultAction
     */
    public function executeExtendedMethods(string $methodName, array $data, &$result = null, $resultAction = self::RESULT_ACTION_BOOL): void
    {
        $model = $this;
        $methodNames = preg_grep('/^'.$methodName.'/', get_class_methods($model));

        if (count($methodNames) > 0) {
            array_walk($methodNames, static function ($method) use ($model, $data, &$result, $resultAction, $methodName) {
                $methodResult = $model->$method(...$data);

                if ($resultAction === self::RESULT_ACTION_BOOL) {
                    if (!$methodResult) {
                        $result = false;
                    }
                } elseif ($resultAction === self::RESULT_ACTION_MERGE) {
                    $result = array_merge($result, $methodResult);
                }
            });
        }
    }

    public function load($data, $formName = null)
    {
        $result = parent::load($data, $formName);
        $this->executeExtendedMethods('fafcmsLoad', [$data, $formName], $result);

        return $result;
    }

    public function validate($attributeNames = null, $clearErrors = true)
    {
        $result = parent::validate($attributeNames, $clearErrors);
        $this->executeExtendedMethods('fafcmsValidate', [$attributeNames, $clearErrors], $result);

        return $result;
    }

    public function loadDefaultValues($skipIfSet = true)
    {
        $result = parent::loadDefaultValues($skipIfSet);
        $this->executeExtendedMethods('fafcmsLoadDefaultValues', [$skipIfSet], $result);

        return $result;
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $result = parent::save($runValidation, $attributeNames);
        $this->executeExtendedMethods('fafcmsSave', [$runValidation, $attributeNames], $result);

        return $result;
    }

    public function getErrorSummary($showAllErrors)
    {
        $result = parent::getErrorSummary($showAllErrors);
        $this->executeExtendedMethods('fafcmsGetErrorSummary', [$showAllErrors], $result, self::RESULT_ACTION_MERGE);

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave($insert): bool
    {
        if ($this->hasAttribute('status') && $this->isAttributeChanged('status')) {
            if ($this->status === static::STATUS_ACTIVE) {
                $this->beforeActivate();
            } elseif ($this->status === static::STATUS_INACTIVE) {
                $this->beforeDeactivate();
            }
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return void
     */
    public function beforeActivate(): void
    {
        $this->trigger(static::EVENT_BEFORE_ACTIVATE);
    }

    /**
     * @return void
     */
    public function beforeDeactivate(): void
    {
        $this->trigger(static::EVENT_BEFORE_DEACTIVATE);
    }

    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'default' => [
                'class' => AttributesBehavior::class,
                'owner' => $this,
            ],
        ]);
    }

    public function rules()
    {
        $result = parent::rules();
        $this->executeExtendedMethods('fafcmsRules', [], $result, self::RESULT_ACTION_MERGE);

        return $result;
    }

    public function attributeLabels()
    {
        $result = parent::attributeLabels();
        $this->executeExtendedMethods('fafcmsAttributeLabels', [], $result, self::RESULT_ACTION_MERGE);

        return $result;
    }

    /**
     * @return array
     */
    public function attributes()
    {
        $className = get_class($this);
        return array_merge(parent::attributes(), array_keys(Yii::$app->fafcms->modelExtensionAttributes[$className]??[]));
    }

    /**
     * @param string $className
     * @param string $name
     * @param array $params
     * @return mixed
     */
    private function getModelExtensionMethod(string $className, string $name, array $params = [])
    {
        $lowerName = mb_strtolower($name);

        if (isset(Yii::$app->fafcms->modelExtensionMethods[$className][$lowerName]) && Yii::$app->fafcms->modelExtensionMethods[$className][$lowerName] instanceof Closure) {
            return call_user_func(Yii::$app->fafcms->modelExtensionMethods[$className][$lowerName], $this, $params);
        }/* elseif (mb_stripos($name, 'get') === 0) {
            try {
                return $this->getModelExtensionAttribute($className, lcfirst(mb_substr($name, 3)));
            } catch (UnknownPropertyException $e) {}
        } elseif (mb_stripos($name, 'set') === 0) {
            try {
                return $this->setModelExtensionAttribute($className, lcfirst(mb_substr($name, 3), $params));
            } catch (UnknownPropertyException $e) {}
        }*/

        throw new UnknownMethodException($className.' does\'t have an extension method with name "'.$name.'".');
    }

    /**
     * @param string $name
     * @param array $params
     * @return mixed
     */
    function __call($name, $params)
    {
        $className = get_class($this);

        try {
            return $this->getModelExtensionMethod($className, $name, $params);
        } catch (UnknownMethodException $e) {
            return parent::__call($name, $params);
        }
    }

    /**
     * @param string $className
     * @param string $name
     * @return mixed
     * @throws UnknownPropertyException
     */
    private function getModelExtensionAttribute(string $className, string $name)
    {
        if (isset(Yii::$app->fafcms->modelExtensionAttributes[$className][$name])) {
            if (Yii::$app->fafcms->modelExtensionAttributes[$className][$name] instanceof Closure) {
                return call_user_func(Yii::$app->fafcms->modelExtensionAttributes[$className][$name], $this);
            }

            return Yii::$app->fafcms->modelExtensionAttributes[$className][$name];
        }

        throw new UnknownPropertyException($className.' does\'t have an extension attribute with name "'.$name.'".');
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        $className = get_class($this);

        try {
            return $this->getModelExtensionAttribute($className, $name);
        } catch (UnknownPropertyException $e) {
            return parent::__get($name);
        }
    }

    /**
     * @param string $className
     * @param string $name
     * @param $value
     * @return mixed
     * @throws UnknownPropertyException
     */
    private function setModelExtensionAttribute(string $className, string $name, $value)
    {
        if (isset(Yii::$app->fafcms->modelExtensionAttributes[$className][$name])) {
            if (Yii::$app->fafcms->modelExtensionAttributes[$className][$name] instanceof Closure) {
                return call_user_func(Yii::$app->fafcms->modelExtensionAttributes[$className][$name], $this, $value);
            }
            else {
                return Yii::$app->fafcms->modelExtensionAttributes[$className][$name] = $value;
            }
        }

        throw new UnknownPropertyException($className.' does\'t have an extension attribute with name "'.$name.'".');
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return mixed|void
     */
    public function __set($name, $value)
    {
        $className = get_class($this);

        try {
            return $this->setModelExtensionAttribute($className, $name, $value);
        } catch (UnknownPropertyException $e) {
            try {
                parent::__set($name, $value);
            } catch (InvalidCallException $e) {
                $this->$name = $value;
            }
        }
    }
}
