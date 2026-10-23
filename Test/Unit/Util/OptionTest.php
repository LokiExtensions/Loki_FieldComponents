<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Util;

use Loki\Field\Util\Option;
use Loki\Field\Util\OptionInterface;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    public function testDefaults(): void
    {
        $option = new Option('nl', 'Netherlands');

        $this->assertInstanceOf(OptionInterface::class, $option);
        $this->assertSame('nl', $option->getValue());
        $this->assertSame('Netherlands', $option->getLabel());
        $this->assertFalse($option->isDisabled());
        $this->assertFalse($option->isSelected());
    }

    public function testFlags(): void
    {
        $option = new Option(42, 'Answer', true, true);

        $this->assertSame(42, $option->getValue());
        $this->assertTrue($option->isDisabled());
        $this->assertTrue($option->isSelected());
    }

    public function testValueKeepsItsType(): void
    {
        $this->assertSame(['a', 'b'], (new Option(['a', 'b'], 'Array'))->getValue());
        $this->assertNull((new Option(null, 'Null'))->getValue());
    }
}
