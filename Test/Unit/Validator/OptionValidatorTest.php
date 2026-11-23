<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Field\Component\Base\Field\FieldViewModelInterface;
use Loki\Field\Test\Unit\Mock\BlockMock;
use Loki\Field\Util\Option;
use Loki\Field\Util\OptionGroup;
use Loki\Field\Util\OptionModelInterface;
use Loki\Field\Util\OptionValue;
use Loki\Field\Validator\OptionValidator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OptionValidatorTest extends TestCase
{
    use BlockMock;

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
            'zero without placeholder flag' => ['0', false],
            'boolean true without matching option' => [true, false],
            'boolean false without placeholder flag' => [false, false],
            'unknown option' => ['purple', false],
            'disabled option' => ['black', false],
            'option in disabled group' => ['blue', false],
            'non-scalar value' => [new \stdClass(), false],
        ];
    }

    public function testZeroPlaceholderIsAcceptedWithFlag(): void
    {
        $validator = new OptionValidator(new OptionValue());
        $component = $this->getComponent([new Option('red', 'Red')], ['allow_zero_placeholder' => true]);

        $this->assertTrue($validator->validate('0', $component));
        $this->assertTrue($validator->validate(false, $component));
    }

    public function testZeroPlaceholderFlagMustBeBoolean(): void
    {
        $component = $this->getComponent([new Option('red', 'Red')], ['allow_zero_placeholder' => '1']);

        $this->assertNotTrue((new OptionValidator(new OptionValue()))->validate('0', $component));
    }

    public function testBooleanCannotSelectDisabledOption(): void
    {
        $component = $this->getComponent([new Option('1', 'Yes', true), new Option('2', 'No')]);

        $this->assertNotTrue((new OptionValidator(new OptionValue()))->validate(true, $component));
    }

    public function testBooleanSelectsMatchingOption(): void
    {
        $component = $this->getComponent([new Option('1', 'Yes'), new Option('2', 'No')]);

        $this->assertTrue((new OptionValidator(new OptionValue()))->validate(true, $component));
    }

    #[DataProvider('getBooleanFieldTypes')]
    public function testBooleanIsAcceptedForBooleanFieldTypes(string $fieldType): void
    {
        $component = $this->getComponent([new Option('1', 'Yes', true)], ['field_type' => $fieldType]);

        $this->assertTrue((new OptionValidator(new OptionValue()))->validate(true, $component));
        $this->assertTrue((new OptionValidator(new OptionValue()))->validate(false, $component));
    }

    public static function getBooleanFieldTypes(): array
    {
        return [['checkbox'], ['switch']];
    }

    public function testButtonGroupOnlyAcceptsEnabledOptions(): void
    {
        $validator = new OptionValidator(new OptionValue());
        $component = $this->getComponent(
            [new Option('red', 'Red'), new Option('black', 'Black', true)],
            ['field_type' => 'button_group']
        );

        $this->assertTrue($validator->validate('red', $component));
        $this->assertNotTrue($validator->validate('black', $component));
        $this->assertNotTrue($validator->validate('"><script>alert(1)</script>', $component));
        $this->assertNotTrue($validator->validate(true, $component));
        $this->assertNotTrue($validator->validate(['red'], $component));
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

    private function getComponent(array $options, array $blockData = []): ComponentInterface
    {
        $optionModel = $this->createMock(OptionModelInterface::class);
        $optionModel->method('getOptions')->willReturn($options);

        $viewModel = $this->createMock(FieldViewModelInterface::class);
        $viewModel->method('getOptionModel')->willReturn($optionModel);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);
        $component->method('getBlock')->willReturn($this->getBlockMock('loki.test', $blockData));

        return $component;
    }
}
