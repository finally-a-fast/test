<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Elements;

use Faf\TemplateEngine\Helpers\ParserElement;

/**
 * Class GetFormat
 *
 * @package fafcms\parser\elements
 */
class GetFormat extends ParserElement
{
    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return 'get-format';
    }

    /**
     * {@inheritdoc}
     */
    public function description(): string
    {
        return 'The format.';
    }

    /**
     * {@inheritdoc}
     */
    public function allowedParents(): ?array
    {
        return [Get::class];
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        return $this->content;
    }
}
