<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Component\Base\Field;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentRepositoryInterface;
use Loki\Components\Filter\Filter;
use Loki\Field\Component\Base\Field\FieldViewModel;
use Loki\Field\Test\Unit\Mock\BlockMock;
use Loki\Field\Util\OptionModelInterface;
use Magento\Framework\Escaper;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FieldViewModelTest extends TestCase
{
    use BlockMock;

    public function testAriaLabelIsRenderedAsPlainString(): void
    {
        $viewModel = $this->getViewModel(['field_label' => 'First Name']);
        $attributes = $viewModel->getFieldAttributesAsString();

        $this->assertStringContainsString('aria-label="First Name"', $attributes);
    }

    public function testAttributeValuesAreEscaped(): void
    {
        $viewModel = $this->getViewModel(['placeholder' => 'Say "hi"']);
        $attributes = $viewModel->getFieldAttributesAsString([
            'value' => 'a"b<c>\'d',
            'data-list' => ['it\'s', '"quoted"'],
        ]);

        $this->assertStringContainsString('value="a&quot;b&lt;c&gt;&#039;d"', $attributes);
        $this->assertStringContainsString('placeholder="Say &quot;hi&quot;"', $attributes);
        $this->assertStringContainsString(
            'data-list="[&quot;it&#039;s&quot;,&quot;\&quot;quoted\&quot;&quot;]"',
            $attributes
        );
    }

    public function testBooleanAttributes(): void
    {
        $viewModel = $this->getViewModel(['required' => true]);
        $attributes = $viewModel->getFieldAttributesAsString(['checked' => false, 'multiple' => true]);

        $this->assertMatchesRegularExpression('/(^| )required( |$)/', $attributes);
        $this->assertMatchesRegularExpression('/(^| )multiple( |$)/', $attributes);
        $this->assertStringNotContainsString('checked', $attributes);
        $this->assertDoesNotMatchRegularExpression('/(^| )disabled( |$)/', $attributes);
    }

    #[DataProvider('getFalsyButFilledValues')]
    public function testFalsyValueIsNotReplacedByDefaultValue(mixed $value): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->method('getValue')->willReturn($value);
        $repository->method('getDefaultValue')->willReturn('default');
        $repository->expects($this->never())->method('saveValue');

        $viewModel = $this->getViewModel([], $repository);

        $this->assertSame($value, $viewModel->getValue());
    }

    #[DataProvider('getIsEmptyValues')]
    public function testIsEmpty(mixed $value, bool $expected): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->method('getValue')->willReturn($value);

        $viewModel = $this->getViewModel([], $repository);

        $this->assertSame($expected, $viewModel->isEmpty());
    }

    public static function getIsEmptyValues(): array
    {
        return [
            [0, false],
            ['0', false],
            [0.0, false],
            ['foo', false],
            [['a'], false],
            [true, false],
            [false, true],
            [null, true],
            ['', true],
            ['  ', true],
            [[], true],
        ];
    }

    public static function getFalsyButFilledValues(): array
    {
        return [
            [0],
            ['0'],
            [false],
            [0.0],
        ];
    }

    #[DataProvider('getEmptyValues')]
    public function testEmptyValueIsReplacedByDefaultValue(mixed $value): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->method('getValue')->willReturn($value);
        $repository->method('getDefaultValue')->willReturn('default');
        $repository->expects($this->once())->method('saveValue')->with('default');

        $viewModel = $this->getViewModel([], $repository);

        $this->assertSame('default', $viewModel->getValue());
    }

    public static function getEmptyValues(): array
    {
        return [
            [null],
            [''],
            [[]],
        ];
    }

    public function testFieldNameFromLayoutName(): void
    {
        $this->assertSame('first_name', $this->getViewModel()->getFieldName());
    }

    public function testFieldNameFromBlock(): void
    {
        $this->assertSame('firstname', $this->getViewModel(['field_name' => 'firstname'])->getFieldName());
    }

    public function testFieldLabelFallsBackToFieldName(): void
    {
        $this->assertSame('First Name', $this->getViewModel()->getFieldLabel());
    }

    public function testFieldLabelFromBlock(): void
    {
        $this->assertSame('Given name', $this->getViewModel(['field_label' => 'Given name'])->getFieldLabel());
    }

    public function testShowFieldLabel(): void
    {
        $this->assertTrue($this->getViewModel()->showFieldLabel());
        $this->assertFalse($this->getViewModel(['show_field_label' => false])->showFieldLabel());
        $this->assertTrue($this->getViewModel(['show_field_label' => 'no boolean'])->showFieldLabel());
    }

    public function testFieldId(): void
    {
        $this->assertSame('test-element-field', $this->getViewModel()->getFieldId());
    }

    public function testFieldData(): void
    {
        $cssClasses = ['block' => ['grid' => 'col-span-6']];
        $this->assertSame(
            ['alias' => 'field-container', 'css_classes' => $cssClasses],
            $this->getViewModel(['css_classes' => $cssClasses])->getFieldData()
        );
    }

    public function testJsComponentName(): void
    {
        $this->assertSame('LokiFieldComponent', $this->getViewModel()->getJsComponentName());
    }

    public function testDefaultFieldAttributes(): void
    {
        $attributes = $this->getViewModel()->getFieldAttributes();

        $this->assertSame('test-element-field', $attributes['id']);
        $this->assertSame('First Name', $attributes['aria-label']);
        $this->assertSame('test-element-messages', $attributes['aria-errormessage']);
        $this->assertSame('field', $attributes['data-type']);
        $this->assertSame('field', $attributes['x-ref']);
        $this->assertSame('off', $attributes['autocomplete']);
        $this->assertFalse($attributes['required']);
        $this->assertFalse($attributes['disabled']);
        $this->assertArrayNotHasKey('placeholder', $attributes);
        $this->assertArrayNotHasKey('min', $attributes);
        $this->assertArrayNotHasKey('max', $attributes);
    }

    public function testAutocompleteFromBlock(): void
    {
        $viewModel = $this->getViewModel(['autocomplete' => 'given-name']);

        $this->assertTrue($viewModel->hasAutocomplete());
        $this->assertSame('given-name', $viewModel->getFieldAttributes()['autocomplete']);
    }

    public function testAutocompleteIsOffForDisabledField(): void
    {
        $attributes = $this->getViewModel(['autocomplete' => 'given-name', 'disabled' => true])->getFieldAttributes();

        $this->assertSame('off', $attributes['autocomplete']);
        $this->assertTrue($attributes['disabled']);
    }

    public function testDateAttributes(): void
    {
        $attributes = $this->getViewModel([
            'input_type' => 'date',
            'min_date' => '2020-01-31',
            'max_date' => '2030-12-01',
        ])->getFieldAttributes();

        $this->assertSame('2020-01-31', $attributes['min']);
        $this->assertSame('2030-12-01', $attributes['max']);
    }

    public function testDateAttributesAreOnlyAddedForDateInput(): void
    {
        $attributes = $this->getViewModel(['min_date' => '2020-01-31'])->getFieldAttributes();
        $this->assertArrayNotHasKey('min', $attributes);
    }

    public function testFieldAttributesPriority(): void
    {
        $viewModel = $this->getViewModel([
            'placeholder' => 'Placeholder',
            'field_attributes' => ['maxlength' => 10, 'data-foo' => 'block'],
        ]);

        $attributes = $viewModel->getFieldAttributes(['data-foo' => 'custom', 'id' => 'custom-id']);

        $this->assertSame('Placeholder', $attributes['placeholder']);
        $this->assertSame(10, $attributes['maxlength']);
        $this->assertSame('block', $attributes['data-foo'], 'Block attributes win over custom attributes');
        $this->assertSame('custom-id', $attributes['id'], 'Custom attributes win over default attributes');
    }

    public function testNullAttributeIsRenderedWithoutValue(): void
    {
        $attributes = $this->getViewModel()->getFieldAttributesAsString(['readonly' => null, 'maxlength' => 10]);

        $this->assertMatchesRegularExpression('/(^| )readonly( |$)/', $attributes);
        $this->assertStringContainsString('maxlength="10"', $attributes);
    }

    public function testInputType(): void
    {
        $this->assertSame('text', $this->getViewModel()->getInputType());
        $this->assertSame('email', $this->getViewModel(['input_type' => 'email'])->getInputType());
    }

    public function testSimpleBlockGetters(): void
    {
        $viewModel = $this->getViewModel([
            'input_label' => 'Input label',
            'input_prefix' => '+31',
            'help_text' => 'Help',
            'comment' => 'Comment',
            'placeholder' => 'Placeholder',
        ]);

        $this->assertSame('Input label', $viewModel->getInputLabel());
        $this->assertSame('+31', $viewModel->getInputPrefix());
        $this->assertSame('Help', $viewModel->getHelpText());
        $this->assertSame('Comment', $viewModel->getComment());
        $this->assertSame('Placeholder', $viewModel->getPlaceholder());
        $this->assertSame('loki.test.first_name', $viewModel->getNameInLayout());
    }

    public function testIsRequired(): void
    {
        $this->assertFalse($this->getViewModel()->isRequired());
        $this->assertTrue($this->getViewModel(['required' => true])->isRequired());
        $this->assertFalse($this->getViewModel(['required' => 'yes'])->isRequired(), 'Only booleans are used');
    }

    public function testIsRequiredByDefault(): void
    {
        $viewModel = $this->getViewModel([], null, new class extends FieldViewModel {
            protected bool $isRequiredByDefault = true;
        });

        $this->assertTrue($viewModel->isRequired());
    }

    public function testIsDisabled(): void
    {
        $this->assertFalse($this->getViewModel()->isDisabled());
        $this->assertTrue($this->getViewModel(['disabled' => true])->isDisabled());
    }

    #[DataProvider('getEmptyValues')]
    public function testRequiredEmptyFieldIsInvalid(mixed $value): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->method('getValue')->willReturn($value);

        $viewModel = $this->getViewModel(['required' => true], $repository);

        $this->assertFalse($viewModel->isValid());
    }

    public function testRequiredUncheckedCheckboxIsInvalid(): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->method('getValue')->willReturn(false);

        $viewModel = $this->getViewModel(['required' => true], $repository);

        $this->assertFalse($viewModel->isValid());
    }

    public function testValueWithoutRepositoryIsNull(): void
    {
        $this->assertNull($this->getViewModel()->getValue());
    }

    public function testEmptyValueIsKeptWhenDefaultValueIsEmpty(): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->method('getValue')->willReturn('');
        $repository->method('getDefaultValue')->willReturn(null);
        $repository->expects($this->never())->method('saveValue');

        $this->assertSame('', $this->getViewModel([], $repository)->getValue());
    }

    public function testOptionModelFromBlock(): void
    {
        $optionModel = $this->createMock(OptionModelInterface::class);
        $this->assertSame($optionModel, $this->getViewModel(['option_model' => $optionModel])->getOptionModel());
    }

    public function testOptionModelFromAncestorBlock(): void
    {
        $optionModel = $this->createMock(OptionModelInterface::class);
        $ancestorBlock = $this->getBlockMock('loki.test', ['option_model' => $optionModel]);

        $viewModel = $this->getViewModel(['ancestor_block' => $ancestorBlock]);

        $this->assertSame($optionModel, $viewModel->getOptionModel());
    }

    public function testNoOptionModel(): void
    {
        $this->assertNull($this->getViewModel(['option_model' => 'invalid'])->getOptionModel());

        $ancestorBlock = $this->getBlockMock('loki.test', []);
        $this->assertNull($this->getViewModel(['ancestor_block' => $ancestorBlock])->getOptionModel());
    }

    private function getViewModel(
        array $blockData = [],
        ?ComponentRepositoryInterface $repository = null,
        ?FieldViewModel $viewModel = null
    ): FieldViewModel {
        $block = $this->getBlockMock('loki.test.first_name', $blockData);

        /** @var ComponentInterface&MockObject $component */
        $component = $this->createMock(ComponentInterface::class);
        $component->method('getBlock')->willReturn($block);
        $component->method('isValidated')->willReturn(true);
        $component->method('hasRepository')->willReturn($repository instanceof ComponentRepositoryInterface);
        $component->method('getRepository')->willReturn($repository);

        $filter = $this->createMock(Filter::class);
        $filter->method('filter')->willReturnArgument(1);

        $viewModel ??= new class extends FieldViewModel {
            public function getElementId(): string
            {
                return 'test-element';
            }

            protected function getEscaper(): Escaper
            {
                return new Escaper();
            }
        };

        $viewModel->setComponent($component);
        $viewModel->setBlock($block);
        $viewModel->setFilter($filter);

        return $viewModel;
    }
}
