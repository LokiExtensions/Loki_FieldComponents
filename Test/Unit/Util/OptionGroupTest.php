<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Util;

use Loki\Field\Util\Option;
use Loki\Field\Util\OptionGroup;
use Loki\Field\Util\OptionGroupInterface;
use PHPUnit\Framework\TestCase;

class OptionGroupTest extends TestCase
{
    public function testDefaults(): void
    {
        $group = new OptionGroup('Europe');

        $this->assertInstanceOf(OptionGroupInterface::class, $group);
        $this->assertSame('Europe', $group->getLabel());
        $this->assertSame([], $group->getOptions());
        $this->assertFalse($group->isDisabled());
    }

    public function testOptionsAndDisabledFlag(): void
    {
        $options = [new Option('nl', 'Netherlands'), new Option('be', 'Belgium')];
        $group = new OptionGroup('Europe', $options, true);

        $this->assertSame($options, $group->getOptions());
        $this->assertTrue($group->isDisabled());
    }
}
