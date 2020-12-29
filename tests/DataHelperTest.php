<?php

declare(strict_types=1);

namespace Faf\TemplateEngine\Tests;

use PHPUnit\Framework\TestCase;
use Faf\TemplateEngine\Helpers\DataHelper;

final class DataHelperTest extends TestCase
{
    public function testSubstring(): void
    {
        $dataHelper = new DataHelper();

        $result = $dataHelper->setName('test');

        self::assertSame($dataHelper, $result);
    }
}
