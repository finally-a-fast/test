<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Parser;
use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Boolean;
use Yiisoft\Validator\Rule\Required;

/**
 * Class Parse
 *
 * @package Faf\TemplateEngine\Elements
 */
class Parse extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'parse';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Parses a string.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'string',
                'label' => 'String',
                'element' => ParseString::class,
                'content' => true
            ])
        ];
    }

    /**
     * {@inheritdoc}
     * @throws \Exception
     */
    public function run()
    {
        /**
         * TODO add option to create new parser and pass data language etc. options
         *
         *  $fafte = new Parser([
         *      'logger' => $logger,
         *      'cache' => $cache,
         *      'mode' => Parser::MODE_DEV
         *  ]);
         *
         *  $fafte->setData($data);
         */

        $fafte = $this->parser;

        return $fafte->parseElements($this->data['string'], $this->tagName());
    }
}
