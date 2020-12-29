<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use NumberFormatter;
use Yiisoft\Validator\Rule\Number;
use Yiisoft\Validator\Rule\Required;

/**
 * Class FormatAsShortSize
 *
 * @package Faf\TemplateEngine\Elements
 */
class FormatAsShortSize extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'format-as-short-size';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Formats the value in bytes as a size in human readable form for example `12 kB`.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'value',
                'label' => 'Value',
                'element' => FormatAsShortSizeValue::class,
                'content' => true,
                'rules' => [
                    new Required(),
                ]
            ]),
            new ElementSetting([
                'name' => 'decimals',
                'label' => 'Decimals',
                'element' => FormatAsShortSizeDecimals::class,
                'defaultValue' => 0,
                'rules' => [
                    (new Number())->integer()->min(0),
                ]
            ]),
            new ElementSetting([
                'name' => 'base',
                'label' => 'Base',
                'aliases' => [
                    'size-format-base'
                ],
                //TODO
                //'element' => FormatAsShortSizeDecimals::class,
                'defaultValue' => 1000,
                'rules' => [
                    (new Number())->integer()->min(0),
                ]
            ])
        ];
    }

    public const BYTE_UNITS = [
        'short' => [
            'default' => ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
            '1024' => ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'YiB'],
        ],
        'full' => [
            'default' => ['byte', 'kilobyte', 'megabyte', 'gigabyte', 'terabyte', 'petabyte', 'exabyte', 'zettabyte', 'yottabyte'],
            '1024' => ['byte', 'kibibyte', 'mebibyte', 'gibibyte', 'tebibyte', 'pebibyte', 'exbibyte', 'zebibyte', 'yobibyte'],
        ],
    ];

    public const BYTE_PRECISION = [0, 0, 1, 2, 2, 3, 3, 4, 4];

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $value = $this->data['value'];
        $decimals = $this->data['decimals'];
        $sizeFormatBase = $this->data['base'];

        $units = self::BYTE_UNITS['short'][$sizeFormatBase === 1024 ? '1024' : 'default'];

        /**
         * Author TyrotoxismB https://gist.github.com/liunian/9338301#gistcomment-3293173
         */
        for ($i = 0; ($value / $sizeFormatBase) >= 0.9 && $i < count($units); $i++) {
            $value /= $sizeFormatBase;
        }

        $value = round($value, is_null($decimals) ? self::BYTE_PRECISION[$i] : $decimals);
        $suffix = $units[$i];

        return ($this->parser->formatNumber($value, NumberFormatter::DECIMAL) ?: $value) . ' ' . $suffix;
    }
}
