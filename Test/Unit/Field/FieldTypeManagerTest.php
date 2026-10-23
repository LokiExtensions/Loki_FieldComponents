<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Field;

use Loki\Field\Component\Base\Field\FieldViewModelInterface;
use Loki\Field\Field\FieldTypeManager;
use Loki\Field\Test\Unit\Mock\BlockMock;
use Loki\Field\Util\OptionModelInterface;
use PHPUnit\Framework\TestCase;

class FieldTypeManagerTest extends TestCase
{
    use BlockMock;

    private const FIELD_TYPES = [
        'input' => 'input.phtml',
        'text' => 'input.phtml',
        'select' => 'select.phtml',
        'radio' => 'radio.phtml',
    ];

    public function testDefaultFieldType(): void
    {
        $viewModel = $this->getViewModel([]);
        $this->assertSame('input', $this->getFieldTypeManager()->getFieldTypeFromViewModel($viewModel));
    }

    public function testUnknownFieldTypeFallsBackToInput(): void
    {
        $viewModel = $this->getViewModel(['field_type' => 'unknown']);
        $this->assertSame('input', $this->getFieldTypeManager()->getFieldTypeFromViewModel($viewModel));
    }

    public function testTextSelectWithoutOptionsIsText(): void
    {
        $viewModel = $this->getViewModel(['field_type' => 'text_select'], []);
        $this->assertSame('text', $this->getFieldTypeManager()->getFieldTypeFromViewModel($viewModel));
    }

    public function testTextSelectUsesOptionModelOfViewModel(): void
    {
        // The option model is not set on the block itself, but resolved by the ViewModel (for instance via an ancestor block)
        $viewModel = $this->getViewModel(['field_type' => 'text_select'], ['a' => 'A']);
        $this->assertSame('select', $this->getFieldTypeManager()->getFieldTypeFromViewModel($viewModel));
    }

    public function testUnknownFieldTemplateFallsBackToInput(): void
    {
        $this->assertSame('input.phtml', $this->getFieldTypeManager()->getFieldTemplateByType('unknown'));
    }

    public function testKnownFieldType(): void
    {
        $viewModel = $this->getViewModel(['field_type' => 'radio']);
        $this->assertSame('radio', $this->getFieldTypeManager()->getFieldTypeFromViewModel($viewModel));
    }

    public function testFieldTypeWithoutBlockIsInput(): void
    {
        $viewModel = $this->createMock(FieldViewModelInterface::class);
        $viewModel->method('getBlock')->willReturn(null);

        $fieldTypeManager = $this->getFieldTypeManager();
        $this->assertSame('input', $fieldTypeManager->getFieldTypeFromViewModel($viewModel));
        $this->assertSame('input.phtml', $fieldTypeManager->getFieldTemplateFromViewModel($viewModel));
    }

    public function testFieldTemplateFromFieldType(): void
    {
        $viewModel = $this->getViewModel(['field_type' => 'select']);
        $this->assertSame('select.phtml', $this->getFieldTypeManager()->getFieldTemplateFromViewModel($viewModel));
    }

    public function testFieldTemplateFromBlockWinsOverFieldType(): void
    {
        $viewModel = $this->getViewModel(['field_type' => 'select', 'field_template' => 'Foo_Bar::custom.phtml']);
        $this->assertSame(
            'Foo_Bar::custom.phtml',
            $this->getFieldTypeManager()->getFieldTemplateFromViewModel($viewModel)
        );
    }

    public function testTextSelectWithoutOptionModelIsText(): void
    {
        $viewModel = $this->getViewModel(['field_type' => 'text_select']);
        $this->assertSame('text', $this->getFieldTypeManager()->getFieldTypeFromViewModel($viewModel));
    }

    public function testFieldTemplateWithEmptyMappingFallsBackToDefaultInputTemplate(): void
    {
        $this->assertSame(
            'Loki_FieldComponents::form/field/input.phtml',
            (new FieldTypeManager())->getFieldTemplateByType('select')
        );
    }

    public function testFieldTemplateMapping(): void
    {
        $this->assertSame(self::FIELD_TYPES, $this->getFieldTypeManager()->getFieldTemplateMapping());
    }

    public function testShowLabelByDefault(): void
    {
        $this->assertTrue($this->getFieldTypeManager()->showLabel($this->getViewModel([])));
    }

    public function testHideLabelForCheckbox(): void
    {
        $fieldTypeManager = new FieldTypeManager(self::FIELD_TYPES + ['checkbox' => 'checkbox.phtml']);
        $this->assertFalse($fieldTypeManager->showLabel($this->getViewModel(['field_type' => 'checkbox'])));
    }

    public function testShowLabelFromBlockWins(): void
    {
        $fieldTypeManager = new FieldTypeManager(self::FIELD_TYPES + ['checkbox' => 'checkbox.phtml']);
        $this->assertTrue($fieldTypeManager->showLabel($this->getViewModel([
            'field_type' => 'checkbox',
            'show_label' => true,
        ])));
        $this->assertFalse($fieldTypeManager->showLabel($this->getViewModel(['show_label' => false])));
    }

    private function getViewModel(array $blockData, ?array $options = null): FieldViewModelInterface
    {
        $optionModel = null;
        if (is_array($options)) {
            $optionModel = $this->createMock(OptionModelInterface::class);
            $optionModel->method('getOptions')->willReturn($options);
        }

        $viewModel = $this->createMock(FieldViewModelInterface::class);
        $viewModel->method('getBlock')->willReturn($this->getBlockMock('loki.test.field', $blockData));
        $viewModel->method('getOptionModel')->willReturn($optionModel);

        return $viewModel;
    }

    private function getFieldTypeManager(): FieldTypeManager
    {
        return new FieldTypeManager(self::FIELD_TYPES);
    }
}
