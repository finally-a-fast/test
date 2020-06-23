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

use fafcms\fafcms\inputs\Chips;
use fafcms\fafcms\models\Tag;
use fafcms\fafcms\models\Tagrealation;
use Yii;
use yii\base\Exception;
use yii\db\ActiveQuery;
use yii\db\ActiveQueryInterface;
use yii\helpers\ArrayHelper;

/**
 * Trait TagTrait
 * @package fafcms\helpers\traits
 */
trait TagTrait
{
    /**
     * @return array
     */
    public function getFieldConfigTagTrait(): array
    {
        $options = array_map(static function () {
            return null;
        }, array_flip(Tag::getOptions()));

        return [
            'tags' => [
                'type' => Chips::class,
                'items' => $options
            ],
        ];
    }

    /**
     * @return array
     */
    public function getDefaultEditViewItemsTagTrait(): array
    {
        return [
            'tag-tab' => [
                'class' => \fafcms\fafcms\items\Tab::class,
                'settings' => [
                    'label' => Tag::instance()->getEditDataPlural(),
                ],
                'contents' => [
                    'row-1' => [
                        'class' => \fafcms\fafcms\items\Row::class,
                        'contents' => [
                            'page-properties-column' => [
                                'class' => \fafcms\fafcms\items\Column::class,
                                'settings' => [
                                    's' => 12,
                                ],
                                'contents' => [
                                    'page-properties-card' => [
                                        'class' => \fafcms\fafcms\items\Card::class,
                                        'settings' => [
                                            'title' => Tag::instance()->getEditDataPlural(),
                                            'icon' => Tag::instance()->getEditDataIcon(),
                                        ],
                                        'contents' => [
                                            [
                                                'class' => \fafcms\fafcms\items\FormField::class,
                                                'settings' => [
                                                    'field' => 'tags',
                                                ],
                                            ],
                                        ]
                                    ],
                                ]
                            ],
                        ],
                    ],
                ]
            ]
        ];
    }

    /**
     * @var string
     */
    private $_tags;

    /**
     * @return array
     */
    public function getTags(): array
    {
        if ($this->_tags === null) {
            $this->_tags = [];

            if ($this->tagModels !== null) {
                $this->_tags = ArrayHelper::getColumn($this->tagModels, 'name');
            }
        }

        return $this->_tags;
    }

    /**
     * @return array
     */
    public function setTags($value)
    {
        if (is_string($value)) {
            if ($value === '') {
                $value = null;
            } else {
                $value = explode(',', $value);
            }
        }

        $this->_tags = $value;
    }

    /**
     * @return ActiveQueryInterface|null
     */
    public function getTagModels(): ?ActiveQueryInterface
    {
        if (method_exists($this, 'hasMany')) {
            return $this->hasMany(Tag::class, ['id' => 'tag_id'])->viaTable(Tagrealation::tableName(), ['model_id' => 'id'], static function (ActiveQuery $query) {
                $query->andWhere(['model_class' => self::class]);
            });
        }

        return null;
    }

    /**
     * @param $runValidation
     * @param $attributeNames
     * @return bool
     */
    public function fafcmsSaveTagTrait($runValidation, $attributeNames): bool
    {
        $savedTags = ArrayHelper::getColumn($this->tagModels, 'name');

        $deletedTagIds = Tag::find()->where(['name' => array_diff($savedTags, $this->tags)])->select('id')->column();

        $newTags = array_diff($this->tags, $savedTags);
        $tagIds = ArrayHelper::map(Tag::find()->where(['name' => $newTags])->select('id, LOWER(name) AS name')->asArray()->all(), 'name', 'id');

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $deletedCount = Tagrealation::deleteAll([
                'model_class' => self::class,
                'model_id' => $this->id,
                'tag_id' => $deletedTagIds,
            ]);

            if ($deletedCount < count($deletedTagIds)) {
                throw new Exception('Not all tags could be deleted.');
            }

            foreach ($newTags as $newTag) {
                $tagIndex = mb_strtolower($newTag);

                if (!isset($tagIds[$tagIndex])) {
                    $tag = new Tag([
                        'name' => $newTag,
                    ]);
                    $tag->loadDefaultValues();
                    $tag->contentmeta->title = $newTag;

                    if (!$tag->save()) {
                        throw new Exception('Could not save new tag "'.$newTag.'". '.print_r($tag->getErrorSummary(true), true));
                    }

                    $tagIds[$tagIndex] = $tag->id;
                }

                $tagrealation = new Tagrealation([
                    'model_class' => self::class,
                    'model_id' => $this->id,
                    'tag_id' => $tagIds[$tagIndex],
                ]);
                $tagrealation->loadDefaultValues();

                if (!$tagrealation->save()) {
                    throw new Exception('Could not save new tag relation for "'.$newTag.'". '.print_r($tagrealation->getErrorSummary(true), true));
                }
            }

            $transaction->commit();
            return true;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->addError('tags', $e->getMessage());
            return false;
        }
    }

    public function fafcmsRulesTagTrait(): array
    {
        return [['tags', 'safe']];
    }

    public function fafcmsAttributeLabelsTagTrait(): array
    {
        return [
            'tags' => Yii::t('fafcms-core', 'Tags'),
        ];
    }
}
