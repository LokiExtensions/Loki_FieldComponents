<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Component\Base\Field;

use Loki\Components\Component\ComponentInterface;
use Loki\Field\Component\Base\Field\FieldRepository;
use Loki\Field\Component\Base\Field\FieldViewModelInterface;
use Loki\Field\Test\Unit\Mock\BlockMock;
use Loki\Field\Util\Option;
use Loki\Field\Util\OptionGroup;
use Loki\Field\Util\OptionModelInterface;
use PHPUnit\Framework\TestCase;

class FieldRepositoryTest extends TestCase
{
    use BlockMock;

    public function testDefaultValueWithoutOptions(): void
    {
        $repository = $this->getRepository(['default_value' => 'foo'], null);
        $this->assertSame('foo', $repository->getDefaultValue());
    }

    public function testDefaultValueFromBlockWinsOverFirstOption(): void
    {
        $repository = $this->getRepository(['default_value' => 'b'], ['a' => 'A', 'b' => 'B']);
        $this->assertSame('b', $repository->getDefaultValue());
    }

    public function testFirstOptionKeyIsUsedForStringOptions(): void
    {
        $repository = $this->getRepository([], ['a' => 'A', 'b' => 'B']);
        $this->assertSame('a', $repository->getDefaultValue());
    }

    public function testFirstOptionValueIsUsedForOptionObjects(): void
    {
        $repository = $this->getRepository([], [
            new Option('x', 'X'),
            new Option('y', 'Y'),
        ]);

        $this->assertSame('x', $repository->getDefaultValue());
    }

    public function testSelectedOptionIsUsed(): void
    {
        $repository = $this->getRepository([], [
            new Option('x', 'X'),
            new Option('y', 'Y', false, true),
        ]);

        $this->assertSame('y', $repository->getDefaultValue());
    }

    public function testDisabledOptionIsSkipped(): void
    {
        $repository = $this->getRepository([], [
            new Option('x', 'X', true),
            new Option('y', 'Y'),
        ]);

        $this->assertSame('y', $repository->getDefaultValue());
    }

    public function testOptionGroupsAreTraversed(): void
    {
        $repository = $this->getRepository([], [
            'group1' => new OptionGroup('Group 1', [new Option('x', 'X'), new Option('y', 'Y', false, true)]),
        ]);

        $this->assertSame('y', $repository->getDefaultValue());
    }

    private function getRepository(array $blockData, ?array $options): FieldRepository
    {
        $block = $this->getBlockMock('loki.test.field', $blockData);

        $optionModel = null;
        if (is_array($options)) {
            $optionModel = $this->createMock(OptionModelInterface::class);
            $optionModel->method('getOptions')->willReturn($options);
        }

        $viewModel = $this->createMock(FieldViewModelInterface::class);
        $viewModel->method('getOptionModel')->willReturn($optionModel);

        $component = $this->createMock(ComponentInterface::class);
        $component->method('getBlock')->willReturn($block);
        $component->method('getViewModel')->willReturn($viewModel);

        $repository = new class extends FieldRepository {
            public function getValue(): mixed
            {
                return null;
            }

            public function saveValue(mixed $value): void
            {
            }
        };

        $repository->setComponent($component);

        return $repository;
    }
}
