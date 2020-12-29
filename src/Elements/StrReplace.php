<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ElementSetting;
use Faf\TemplateEngine\Helpers\ParserElement;
use Yiisoft\Validator\Rule\Required;

/**
 * Class StrReplace
 *
 * @package Faf\TemplateEngine\Elements
 */
class StrReplace extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'str-replace';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'Replace all occurrences of the search string with the replacement string.';
    }

    /**
     * {@inheritdoc}
     */
    public function elementSettings(): array
    {
        return [
            new ElementSetting([
                'name' => 'subject',
                'label' => 'Subject',
                'element' => StrReplaceSubject::class,
                'content' => true
            ]),
            new ElementSetting([
                'name' => 'search',
                'label' => 'Search',
                //TODO
                //'element' => StrReplaceSearch::class,
            ]),
            new ElementSetting([
                'name' => 'replace',
                'label' => 'Replace',
               //TODO
               //'element' => StrReplaceReplace::class,
            ]),
            new ElementSetting([
                'name' => 'count',
                'label' => 'Count',
                'safeData' => false,
                //TODO
                //'element' => StrReplaceCount::class,
            ]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $count = 0;

        if (!empty($this->data['count'])) {
            $count = &$this->parser->getAttributeData($this->data['count']);

            if ($count === null) {
                $count = 0;
                $this->parser->setAttributeData($this->data['count'], $count);
            }
        }

        return str_replace($this->data['search'], $this->data['replace'], $this->data['subject'], $count);
    }
}
