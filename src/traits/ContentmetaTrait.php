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

use fafcms\fafcms\inputs\Checkbox;
use fafcms\fafcms\inputs\DropDownList;
use fafcms\fafcms\inputs\NumberInput;
use fafcms\fafcms\inputs\Select2;
use fafcms\fafcms\inputs\Textarea;
use fafcms\fafcms\inputs\TextInput;
use fafcms\sitemanager\models\Layout;
use fafcms\sitemanager\models\Topic;
use fafcms\sitemanager\models\Contentmeta;
use Yii;
use Closure;

trait ContentmetaTrait
{
    /**
     * @return array
     */
    public function getFieldConfigContentmetaTrait(): array
    {
        $disabledItems = [];

        if (($this->contentmeta->id ?? null) !== null) {
            $disabledItems[$this->contentmeta->id] = ['disabled' => true];
        }

        return [
            'contentmeta.parent_contentmeta_id' => [
                'type' => Select2::class,
                'items' => Contentmeta::instance()->attributeOptions()['parent_contentmeta_id'],
                'relationClassName' => self::class,
                'relationId' => $this->contentmeta->parent_contentmeta_id ?? null,
                'options' => ['options' => ['options' => $disabledItems]]
            ],
            'contentmeta.id' => [
                'type' => TextInput::class,
            ],
            'contentmeta.slug' => [
                'type' => TextInput::class,
            ],
            'contentmeta.title' => [
                'type' => TextInput::class,
            ],
            'contentmeta.description' => [
                'type' => Textarea::class,
                'description' => Yii::t('fafcms-sitemanager', 'The SEO description should be about 160 characters long.'),
                'counter' => true,
                'maxlength' => true
            ],
            'contentmeta.topicids' => [
                'type' => Select2::class,
                'items' => Contentmeta::instance()->attributeOptions()['topicids'],
                'options' => [
                    'options' => ['multiple' => true]
                ]
            ],
            'contentmeta.layout_id' => [
                'type' => Select2::class,
                'items' => Contentmeta::instance()->attributeOptions()['layout_id'],
                'relationClassName' => Layout::class,
                'relationId' => $this->contentmeta->layout_id ?? null,
            ],
            'contentmeta.sitemap_list' => [
                'type' => Checkbox::class,
            ],
            'contentmeta.sitemap_priority' => [
                'type' => NumberInput::class,
            ],
            'contentmeta.sitemap_changefreq' => [
                'type' => DropDownList::class,
                'items' => Contentmeta::instance()->attributeOptions()['sitemap_changefreq'],
            ],
            'contentmeta.robots_disallow' => [
                'type' => Checkbox::class,
            ],
            'contentmeta.robots_disallow_names' => [
                'type' => TextInput::class,
            ],
        ];
    }

    /**
     * @return array
     */
    public function getDefaultEditViewItemsContentmetaTrait(): array
    {
        return [
            'master-data-tab' => [
                'contents' => [
                    'row-1' => [
                        'contents' => [
                            'page-properties-column' => [
                                'class' => \fafcms\fafcms\items\Column::class,
                                'settings' => [
                                    's' => 12,
                                    'm' => 4,
                                ],
                                'contents' => [
                                    'page-properties-card' => [
                                        'class' => \fafcms\fafcms\items\Card::class,
                                        'settings' => [
                                            'title' => Yii::t('fafcms-sitemanager', 'Page properties'),
                                            'icon' => 'card-bulleted-settings-outline',
                                        ],
                                        'contents' => [
                                            [
                                                'class' => \fafcms\fafcms\items\FormField::class,
                                                'settings' => [
                                                    'field' => 'contentmeta.layout_id',
                                                ],
                                            ],
                                            [
                                                'class' => \fafcms\fafcms\items\FormField::class,
                                                'settings' => [
                                                    'field' => 'contentmeta.parent_contentmeta_id',
                                                ],
                                            ],
                                            [
                                                'class' => \fafcms\fafcms\items\FormField::class,
                                                'settings' => [
                                                    'field' => 'contentmeta.slug',
                                                ],
                                            ],
                                        ]
                                    ],
                                ]
                            ],
                        ],
                    ],
                ]
            ],
            'seo-tab' => [
                'class' => \fafcms\fafcms\items\Tab::class,
                'settings' => [
                    'label' => Yii::t('fafcms-core', 'SEO'),
                ],
                'contents' => [
                    'row-1' => [
                        'class' => \fafcms\fafcms\items\Row::class,
                        'contents' => [
                            'page-properties-column' => [
                                'class' => \fafcms\fafcms\items\Column::class,
                                'settings' => [
                                    's' => 12,
                                    'm' => 6,
                                ],
                                'contents' => [
                                    'page-properties-card' => [
                                        'class' => \fafcms\fafcms\items\Card::class,
                                        'settings' => [
                                            'title' => Yii::t('fafcms-sitemanager', 'Seo'),
                                            'icon' => 'file-document-box-search-outline',
                                        ],
                                        'contents' => [
                                            [
                                                'class' => \fafcms\fafcms\items\FormField::class,
                                                'settings' => [
                                                    'field' => 'contentmeta.title',
                                                ],
                                            ],
                                            [
                                                'class' => \fafcms\fafcms\items\FormField::class,
                                                'settings' => [
                                                    'field' => 'contentmeta.description',
                                                ],
                                            ],
                                        ]
                                    ],
                                ]
                            ],
                            'page-index-column' => [
                                'class' => \fafcms\fafcms\items\Column::class,
                                'settings' => [
                                    's' => 12,
                                    'm' => 6,
                                ],
                                'contents' => [
                                    'page-properties-card' => [
                                        'class' => \fafcms\fafcms\items\Card::class,
                                        'settings' => [
                                            'title' => Yii::t('fafcms-sitemanager', 'Index'),
                                            'icon' => 'cloud-search-outline',
                                        ],
                                        'contents' => [
                                            [
                                                'class' => \fafcms\fafcms\items\FormField::class,
                                                'settings' => [
                                                    'field' => 'contentmeta.sitemap_list',
                                                ],
                                            ],
                                            [
                                                'class' => \fafcms\fafcms\items\FormField::class,
                                                'settings' => [
                                                    'field' => 'contentmeta.sitemap_priority',
                                                ],
                                            ],
                                            [
                                                'class' => \fafcms\fafcms\items\FormField::class,
                                                'settings' => [
                                                    'field' => 'contentmeta.sitemap_changefreq',
                                                ],
                                            ],
                                            [
                                                'class' => \fafcms\fafcms\items\FormField::class,
                                                'settings' => [
                                                    'field' => 'contentmeta.robots_disallow',
                                                ],
                                            ],
                                            [
                                                'class' => \fafcms\fafcms\items\FormField::class,
                                                'settings' => [
                                                    'field' => 'contentmeta.robots_disallow_names',
                                                ],
                                            ],
                                        ]
                                    ],
                                ]
                            ],
                        ],
                    ],
                ]
            ],
            'topic-tab' => [
                'class' => \fafcms\fafcms\items\Tab::class,
                'settings' => [
                    'label' => Topic::instance()->getEditDataPlural(),
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
                                            'title' => Topic::instance()->getEditDataPlural(),
                                            'icon' => Topic::instance()->getEditDataIcon(),
                                        ],
                                        'contents' => [
                                            [
                                                'class' => \fafcms\fafcms\items\FormField::class,
                                                'settings' => [
                                                    'field' => 'contentmeta.topicids',
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
     * @return Closure
     */
    public function getEditViewButtonsContentmetaTrait(): Closure
    {
        return static function($buttons, $model, $form, $editView) {
            if (!$model->isNewRecord) {
                $buttons[] = [
                    'icon' => 'mdi mdi-card-search-outline',
                    'label' => Yii::t('fafcms-core', 'Open {modelClass}', [
                        'modelClass' => $model->getEditData()['singular'],
                    ]),
                    'url' => $model->getAbsoluteUrl(),
                    'options' => [
                        'target' => '_blank',
                    ],
                ];
            }

            return $buttons;
        };
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContentmeta()
    {
        return $this->hasOne(Contentmeta::className(), ['model_id' => 'id'])->andWhere([Contentmeta::tableName().'.model_class' => self::class]);
    }

    /**
     * @param Contentmeta $relationModel
     * @param array       $relationRecordData
     */
    public function beforeSetContentmetaAttributesContentmetaTrait(Contentmeta &$relationModel, array &$relationRecordData): void
    {
        $relationRecordData['model_class'] = self::class;
    }

    public static function getUrl($model, $scheme = false)
    {
        return Contentmeta::getUrl($model['contentmeta'], $scheme);
    }

    public function getRelativeUrl()
    {
        return self::getUrl($this);
    }

    public function getAbsoluteUrl()
    {
        return self::getUrl($this, true);
    }
}
