<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class FormatAsDatetimeFormat
 * @package fafcms\parser\elements
 */
class FormatAsDatetimeFormat extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'format-as-datetime-format';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'The format used to convert the value into a date time string.';
    }

    /**
     * {@inheritdoc}
     */
    public function allowedParents(): ?array
    {
        return [FormatAsDatetime::class];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->content;
    }
}
