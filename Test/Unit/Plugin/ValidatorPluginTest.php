<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Plugin;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Validator\Validator;
use Loki\Field\Component\Base\Field\FieldViewModel;
use Loki\Components\Messages\LocalMessageRegistry;
use Loki\Field\Plugin\ValidatorPlugin;
use Loki\Field\Test\Unit\Mock\BlockMock;
use Loki\Field\Validator\FieldValueGuard;
use PHPUnit\Framework\TestCase;

class ValidatorPluginTest extends TestCase
{
    use BlockMock;

    public function testDisabledFieldIsInvalidWithoutCallingValidator(): void
    {
        $component = $this->getComponent($this->getFieldViewModel(true));

        $result = $this->getPlugin()->aroundValidate(
            $this->createMock(Validator::class),
            function () {
                $this->fail('Validator should not be called for a disabled field');
            },
            $component,
            'foo'
        );

        $this->assertFalse($result);
    }

    public function testEnabledFieldIsPassedToValidator(): void
    {
        $component = $this->getComponent($this->getFieldViewModel(false));

        $arguments = [];
        $proceed = function (...$args) use (&$arguments) {
            $arguments = $args;
            return true;
        };

        $result = $this->getPlugin()->aroundValidate(
            $this->createMock(Validator::class),
            $proceed,
            $component,
            'foo',
            'scope',
            2
        );

        $this->assertTrue($result);
        $this->assertSame([$component, 'foo', 'scope', 2], $arguments);
    }

    public function testResultOfValidatorIsReturned(): void
    {
        $component = $this->getComponent($this->getFieldViewModel(false));

        $result = $this->getPlugin()->aroundValidate(
            $this->createMock(Validator::class),
            fn () => false,
            $component
        );

        $this->assertFalse($result);
    }

    public function testNonFieldComponentIsPassedToValidator(): void
    {
        $component = $this->getComponent($this->createMock(ComponentViewModelInterface::class));

        $called = false;
        $result = $this->getPlugin()->aroundValidate(
            $this->createMock(Validator::class),
            function () use (&$called) {
                $called = true;
                return true;
            },
            $component,
            'foo'
        );

        $this->assertTrue($called);
        $this->assertTrue($result);
    }

    public function testInvalidFieldValueIsRejectedWithMessage(): void
    {
        $viewModel = $this->getFieldViewModel(false, ['field_type' => 'select']);

        $localMessageRegistry = $this->createMock(LocalMessageRegistry::class);
        $localMessageRegistry->expects($this->once())->method('addError');

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);
        $component->method('dispatchLocalMessages')->willReturn(true);
        $component->method('getLocalMessageRegistry')->willReturn($localMessageRegistry);

        $result = $this->getPlugin()->aroundValidate(
            $this->createMock(Validator::class),
            function () {
                $this->fail('Validator should not be called for an invalid field value');
            },
            $component,
            ['red', 'green']
        );

        $this->assertFalse($result);
    }

    public function testFieldValueIsOnlyGuardedAtTheTopLevel(): void
    {
        $component = $this->getComponent($this->getFieldViewModel(false, ['field_type' => 'select']));

        $result = $this->getPlugin()->aroundValidate(
            $this->createMock(Validator::class),
            fn () => true,
            $component,
            ['red'],
            '',
            1
        );

        $this->assertTrue($result);
    }

    private function getPlugin(): ValidatorPlugin
    {
        return new ValidatorPlugin(new FieldValueGuard());
    }

    private function getFieldViewModel(bool $disabled, array $blockData = []): FieldViewModel
    {
        $viewModel = $this->createMock(FieldViewModel::class);
        $viewModel->method('isDisabled')->willReturn($disabled);
        $viewModel->method('getBlock')->willReturn($this->getBlockMock('loki.test.field', $blockData));

        return $viewModel;
    }

    private function getComponent(ComponentViewModelInterface $viewModel): ComponentInterface
    {
        $component = $this->createMock(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);

        return $component;
    }
}
