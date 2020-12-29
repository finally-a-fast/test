<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use IntlCalendar;
use IntlDateFormatter;
use Locale;
use Yiisoft\Validator\Rule\Required;

/**
 * Class TimeTag
 *
 * @package Faf\TemplateEngine\Elements
 */
class TimeTag extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'time-tag';
    }

    /**
     * {@inheritdoc}
     */
    public function aliases(): array
    {
        return ['html-time'];
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Creates an html time tag.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'datetime',
                'label' => 'Datetime',
                //'element' => StrReplaceSubject::class,
                'content' => true
            ]),
            new ElementSetting([
                'name' => 'machine-format',
                'label' => 'Machine format',
                'defaultValue' => 'yyyy-MM-dd HH:mm:ssZ',
                //TODO
                //'element' => TimeTagMachineFormat::class,
                'rules' => [
                    new Required(),
                ]
            ]),
            new ElementSetting([
                'name' => 'human-format',
                'label' => 'Human format',
                'defaultValue' => IntlDateFormatter::MEDIUM,
               //TODO
               //'element' => TimeTagHumanFormat::class,
                'rules' => [
                    new Required(),
                ]
            ]),
            new ElementSetting([
                'name' => 'input-format',
                'label' => 'Input format',
                'safeData' => false,
                //TODO
                //'element' => TimeTagInputFormat::class,
            ]),
            new ElementSetting([
                'name' => 'machine-time-zone',
                'label' => 'Machine time zone',
                //TODO
                //'element' => TimeTagMachineFormat::class,
            ]),
            new ElementSetting([
                'name' => 'human-time-zone',
                'label' => 'Human time zone',
               //TODO
               //'element' => TimeTagHumanFormat::class,
            ]),
            new ElementSetting([
                'name' => 'input-time-zone',
                'label' => 'Input time zone',
                //TODO
                //'element' => TimeTagInputFormat::class,
            ]),
            new ElementSetting([
                'name' => 'attributes',
                'label' => 'Attributes',
                'element' => TagAttribute::class,
                'rawData' => true,
                'attributeNameAsKey' => true,
                'multiple' => true,
                'multipleAttributeExpression' => '/^(.*)?$/i',
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     * @throws \JsonException
     */
    public function run()
    {
        //TODO default settings from parser
        //'machine_time_default_format', 'yyyy-MM-dd HH:mm:ss';
        //'human_time_default_format', 'medium';
        $machineTimeZone = null;

        if (!empty($this->data['machine-time-zone'])) {
            $machineTimeZone = new \DateTimeZone($this->data['machine-time-zone']);
        }

        $humanTimeZone = null;

        if (!empty($this->data['human-time-zone'])) {
            $humanTimeZone = new \DateTimeZone($this->data['human-time-zone']);
        }

        $options = $this->data['attributes'];

        unset(
            $options['datetime'],
            $options['machine-format'],
            $options['human-format'],
            $options['input-format'],
            $options['machine-time-zone'],
            $options['human-time-zone'],
            $options['input-time-zone']
        );

        $options['datetime'] = $this->parser->formatDateTime($this->data['datetime'], $this->data['machine-format'], $machineTimeZone);

        return $this->parser->htmlTag('time', $this->parser->formatDateTime($this->data['datetime'], $this->data['human-format'], $humanTimeZone) ?: '', $options);
    }
}
