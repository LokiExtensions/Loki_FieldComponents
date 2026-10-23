<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Plugin;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentViewModelInterface;
use Loki\Components\Validator\Validator;
use Loki\Field\Component\Base\Field\FieldViewModel;
use Loki\Field\Plugin\ValidatorPlugin;
use PHPUnit\Framework\TestCase;

class ValidatorPluginTest extends TestCase
{
    public function testDisabledFieldIsInvalidWithoutCallingValidator(): void
    {
        $component = $this->getComponent($this->getFieldViewModel(true));

        $result = (new ValidatorPlugin())->aroundValidate(
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

        $result = (new ValidatorPlugin())->aroundValidate(
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

        $result = (new ValidatorPlugin())->aroundValidate(
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
        $result = (new ValidatorPlugin())->aroundValidate(
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

    private function getFieldViewModel(bool $disabled): FieldViewModel
    {
        $viewModel = $this->createMock(FieldViewModel::class);
        $viewModel->method('isDisabled')->willReturn($disabled);

        return $viewModel;
    }

    private function getComponent(ComponentViewModelInterface $viewModel): ComponentInterface
    {
        $component = $this->createMock(ComponentInterface::class);
        $component->method('getViewModel')->willReturn($viewModel);

        return $component;
    }
}
