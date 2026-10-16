<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Util;

use Loki\Field\Util\Option;
use Loki\Field\Util\OptionValue;
use PHPUnit\Framework\TestCase;

class OptionValueTest extends TestCase
{
    /**
     * @dataProvider getIsSelectedValues
     */
    public function testIsSelected(mixed $option, int|string $optionIndex, mixed $currentValue, bool $expected): void
    {
        $this->assertSame($expected, (new OptionValue())->isSelected($option, $optionIndex, $currentValue));
    }

    public static function getIsSelectedValues(): array
    {
        return [
            'string key matches' => ['A', 'a', 'a', true],
            'string key does not match' => ['A', 'a', 'b', false],
            'numeric key matches string value' => ['Yes', 1, '1', true],
            'numeric key matches int value' => ['Yes', 1, 1, true],
            'zero key does not match empty value' => ['No', 0, '', false],
            'zero key does not match null value' => ['No', 0, null, false],
            'zero key matches zero value' => ['No', 0, '0', true],
            'option object matches' => [new Option(5, 'Five'), 0, '5', true],
            'option object flagged as selected' => [new Option('x', 'X', false, true), 0, null, true],
            'selected flag ignored when value is set' => [new Option('x', 'X', false, true), 0, 'y', false],
            'option in multiple values' => ['B', 'b', ['a', 'b'], true],
            'option not in multiple values' => ['C', 'c', ['a', 'b'], false],
        ];
    }

    public function testGetValueAsString(): void
    {
        $optionValue = new OptionValue();
        $this->assertSame('1', $optionValue->getValue('Yes', 1));
        $this->assertSame('x', $optionValue->getValue(new Option('x', 'X'), 0));
        $this->assertSame('X', $optionValue->getLabel(new Option('x', 'X')));
        $this->assertSame('Yes', $optionValue->getLabel('Yes'));
        $this->assertTrue($optionValue->isDisabled(new Option('x', 'X', true)));
        $this->assertFalse($optionValue->isDisabled('Yes'));
    }
}
