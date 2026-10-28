<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Field\Component\Base\Field\FieldViewModelInterface;
use Loki\Field\Util\Option;
use Loki\Field\Util\OptionGroup;
use Loki\Field\Util\OptionModelInterface;
use Loki\Field\Util\OptionValue;
use Loki\Field\Validator\OptionValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OptionValidatorTest extends TestCase
{
    #[DataProvider('getValues')]
    public function testValidate(mixed $value, bool $expected): void
    {
        $result = (new OptionValidator(new OptionValue()))->validate($value, $this->getComponent([
            new Option('red', 'Red'),
            new Option('black', 'Black', true),
            new OptionGroup('Group', [new Option('green', 'Green')]),
            new OptionGroup('Disabled group', [new Option('blue', 'Blue')], true),
        ]));

        $this->assertSame($expected, $result === true);
    }

    public static function getValues(): array
    {
        return [
            'enabled option' => ['red', true],
            'option in group' => ['green', true],
            'empty value' => ['', true],
            'null value' => [null, true],
            'placeholder zero' => ['0', true],
            'unknown option' => ['purple', false],
            'disabled option' => ['black', false],
            'option in disabled group' => ['blue', false],
            'non-scalar value' => [new \stdClass(), false],
        ];
    }

    public function testScalarOptionsUseTheirIndexAsValue(): void
    {
        $validator = new OptionValidator(new OptionValue());
        $component = $this->getComponent(['nl' => 'Netherlands', 'be' => 'Belgium']);

        $this->assertTrue($validator->validate('nl', $component));
        $this->assertNotTrue($validator->validate('Netherlands', $component));
    }

    public function testZeroIsCheckedWhenItIsAnOption(): void
    {
        $component = $this->getComponent([new Option('0', 'None', true)]);
        $this->assertNotTrue((new OptionValidator(new OptionValue()))->validate('0', $component));
    }

    public function testEmptyOptionListAcceptsAnyValue(): void
    {
        $this->assertTrue((new OptionValidator(new OptionValue()))->validate('foo', $this->getComponent([])));
    }

    public function testComponentWithoutOptionModelAcceptsAnyValue(): void
    {
        $viewModel = $this->createMock(FieldViewModelInterface::class);
        $viewModel->method('getOptionModel')->willReturn(null);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);

        $this->assertTrue((new OptionValidator(new OptionValue()))->validate('foo', $component));
    }

    private function getComponent(array $options): ComponentInterface
    {
        $optionModel = $this->createMock(OptionModelInterface::class);
        $optionModel->method('getOptions')->willReturn($options);

        $viewModel = $this->createMock(FieldViewModelInterface::class);
        $viewModel->method('getOptionModel')->willReturn($optionModel);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);

        return $component;
    }
}
