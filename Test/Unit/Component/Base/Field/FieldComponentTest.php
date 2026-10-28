<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Component\Base\Field;

use Loki\Field\Component\Base\Field\FieldComponent;
use Loki\Field\Component\Base\Field\FieldViewModel;
use Loki\Field\Test\Unit\Mock\BlockMock;
use Loki\Field\Util\OptionModelInterface;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class FieldComponentTest extends TestCase
{
    use BlockMock;

    public function testFieldNameIsAlwaysAValidator(): void
    {
        $component = $this->getComponent();
        $this->assertSame(['first_name'], $component->getValidators());
    }

    public function testRequiredValidatorIsAddedForRequiredField(): void
    {
        $component = $this->getComponent(required: true);
        $this->assertSame(['first_name', 'required'], $component->getValidators());
    }

    public function testLengthValidatorIsAddedWithMinlength(): void
    {
        $component = $this->getComponent(blockData: ['minlength' => 2]);
        $this->assertContains('length', $component->getValidators());
    }

    public function testLengthValidatorIsAddedWithMaxlength(): void
    {
        $component = $this->getComponent(blockData: ['maxlength' => 255]);
        $this->assertContains('length', $component->getValidators());
    }

    public function testLengthValidatorIsNotAddedWithZeroLength(): void
    {
        $component = $this->getComponent(blockData: ['minlength' => 0, 'maxlength' => '0']);
        $this->assertNotContains('length', $component->getValidators());
    }

    public function testValidatorsAreMergedWithoutDuplicates(): void
    {
        $component = $this->getComponent(
            required: true,
            componentValidators: ['required', 'email'],
            viewModelValidators: ['email', 'length'],
        );

        $this->assertSame(['required', 'email', 'first_name', 'length'], $component->getValidators());
    }

    public function testValidatorsArgumentIsKept(): void
    {
        $component = $this->getComponent();
        $this->assertSame(['custom', 'first_name'], $component->getValidators(['custom']));
    }

    public function testOptionsValidatorIsAddedWithOptionModel(): void
    {
        $component = $this->getComponent(optionModel: $this->createMock(OptionModelInterface::class));
        $this->assertContains(FieldComponent::OPTIONS_VALIDATOR, $component->getValidators());
    }

    public function testOptionsValidatorCanBeDisabled(): void
    {
        $component = $this->getComponent(
            blockData: ['validate_options' => false],
            optionModel: $this->createMock(OptionModelInterface::class)
        );

        $this->assertNotContains(FieldComponent::OPTIONS_VALIDATOR, $component->getValidators());
    }

    public function testOptionsValidatorIsNotAddedWithoutOptionModel(): void
    {
        $this->assertNotContains(FieldComponent::OPTIONS_VALIDATOR, $this->getComponent()->getValidators());
    }

    public function testSecurityFilterIsAlwaysAdded(): void
    {
        $component = $this->getComponent(componentFilters: ['trim']);
        $this->assertSame(['trim', 'security'], $component->getFilters());
    }

    public function testSecretFieldSkipsAlteringFilters(): void
    {
        $component = $this->getComponent(componentFilters: ['trim', 'lowercase'], secretField: true);
        $this->assertSame(['lowercase'], $component->getFilters());
    }

    private function getComponent(
        bool $required = false,
        array $blockData = [],
        array $componentValidators = [],
        array $viewModelValidators = [],
        ?OptionModelInterface $optionModel = null,
        array $componentFilters = [],
        bool $secretField = false,
    ): FieldComponent {
        $block = $this->getBlockMock('loki.test.first_name', $blockData);

        $viewModel = $this->createMock(FieldViewModel::class);
        $viewModel->method('getFieldName')->willReturn('first_name');
        $viewModel->method('isRequired')->willReturn($required);
        $viewModel->method('getBlock')->willReturn($block);
        $viewModel->method('getValidators')->willReturn($viewModelValidators);
        $viewModel->method('getOptionModel')->willReturn($optionModel);
        $viewModel->method('isSecretField')->willReturn($secretField);

        $component = $this->getMockBuilder(FieldComponent::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getViewModel'])
            ->getMock();
        $component->method('getViewModel')->willReturn($viewModel);

        (new ReflectionProperty($component, 'validators'))->setValue($component, $componentValidators);
        (new ReflectionProperty($component, 'filters'))->setValue($component, $componentFilters);

        return $component;
    }
}
