<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Validator;

use Loki\Field\Component\Base\Field\FieldViewModel;
use Loki\Field\Test\Unit\Mock\BlockMock;
use Loki\Field\Validator\FieldValueGuard;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FieldValueGuardTest extends TestCase
{
    use BlockMock;

    #[DataProvider('getValues')]
    public function testGetError(array $blockData, mixed $value, bool $expectError): void
    {
        $error = (new FieldValueGuard())->getError($this->getViewModel($blockData), $value);
        $this->assertSame($expectError, $error !== null);
    }

    public static function getValues(): array
    {
        $long = str_repeat('a', FieldValueGuard::DEFAULT_MAX_LENGTH + 1);

        return [
            'short string' => [[], 'foo', false],
            'long string' => [[], $long, true],
            'long string in textarea' => [['field_type' => 'textarea'], $long, false],
            'long string with maxlength' => [['maxlength' => 1000], $long, false],
            'long string with maxlength attribute' => [['field_attributes' => ['maxlength' => 1000]], $long, false],
            'long string with max_value_length' => [['max_value_length' => 300], $long, false],
            'max_value_length 0 disables check' => [['max_value_length' => 0], $long, false],
            'max_value_length is enforced' => [['max_value_length' => 2], 'foo', true],
            'array without field type' => [[], ['a'], false],
            'array for single select' => [['field_type' => 'select'], ['a'], true],
            'array for multiple select' => [['field_type' => 'select', 'field_attributes' => ['multiple' => true]], ['a'], false],
            'array allowed by block' => [['field_type' => 'input', 'allow_array_value' => true], ['a'], false],
            'checkbox true' => [['field_type' => 'checkbox'], true, false],
            'checkbox string 1' => [['field_type' => 'switch'], '1', false],
            'checkbox other string' => [['field_type' => 'checkbox'], 'yes', true],
            'integer value' => [[], 42, false],
        ];
    }

    private function getViewModel(array $blockData): FieldViewModel
    {
        $viewModel = $this->createMock(FieldViewModel::class);
        $viewModel->method('getBlock')->willReturn($this->getBlockMock('loki.test.field', $blockData));

        return $viewModel;
    }
}
