<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Component\Base\Field;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Component\ComponentRepositoryInterface;
use Loki\Components\Filter\Filter;
use Loki\Field\Component\Base\Field\FieldViewModel;
use Loki\Field\Test\Unit\Mock\BlockMock;
use Magento\Framework\Escaper;
use PHPUnit\Framework\MockObject\MockObject;
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

    /**
     * @dataProvider getFalsyButFilledValues
     */
    public function testFalsyValueIsNotReplacedByDefaultValue(mixed $value): void
    {
        $repository = $this->createMock(ComponentRepositoryInterface::class);
        $repository->method('getValue')->willReturn($value);
        $repository->method('getDefaultValue')->willReturn('default');
        $repository->expects($this->never())->method('saveValue');

        $viewModel = $this->getViewModel([], $repository);

        $this->assertSame($value, $viewModel->getValue());
    }

    /**
     * @dataProvider getIsEmptyValues
     */
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

    /**
     * @dataProvider getEmptyValues
     */
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

    private function getViewModel(
        array $blockData = [],
        ?ComponentRepositoryInterface $repository = null
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

        $viewModel = new class extends FieldViewModel {
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
