<?php declare(strict_types=1);

namespace Loki\Field\Test\Unit\Validator;

use Loki\Field\Component\Base\Field\FieldViewModel;
use Loki\Field\Field\FieldTypeManager;
use Loki\Field\Test\Unit\Mock\BlockMock;
use Loki\Field\Validator\FieldValueGuard;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FieldValueGuardTest extends TestCase
{
    use BlockMock;

    private const FIELD_TYPES = [
        'checkbox',
        'combobox',
        'input',
        'password',
        'password_repeat',
        'radio',
        'range',
        'select',
        'switch',
        'text',
        'textarea',
    ];

    #[DataProvider('getValues')]
    public function testGetError(array $blockData, mixed $value, bool $expectError): void
    {
        $error = $this->getGuard()->getError($this->getViewModel($blockData), $value);
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
            'array without field type' => [[], ['a'], true],
            'array for text_select' => [['field_type' => 'text_select'], ['a'], true],
            'array for unknown field type' => [['field_type' => 'foobar'], ['a'], true],
            'array allowed by multiple block flag' => [['field_type' => 'select', 'multiple' => true], ['a', 'b'], false],
            'array with too long element' => [['field_type' => 'select', 'multiple' => true], ['a', $long], true],
            'array for single select' => [['field_type' => 'select'], ['a'], true],
            'array for multiple select' => [['field_type' => 'select', 'field_attributes' => ['multiple' => true]], ['a'], false],
            'array allowed by block' => [['field_type' => 'input', 'allow_array_value' => true], ['a'], false],
            'checkbox true' => [['field_type' => 'checkbox'], true, false],
            'checkbox string 1' => [['field_type' => 'switch'], '1', false],
            'checkbox other string' => [['field_type' => 'checkbox'], 'yes', true],
            'integer value' => [[], 42, false],
            'float value' => [['field_type' => 'text'], 4.2, false],
            'null value' => [['field_type' => 'textarea'], null, false],
            'boolean value for input' => [[], true, true],
            'boolean value for password' => [['field_type' => 'password'], false, true],
            'object value for textarea' => [['field_type' => 'textarea'], new \stdClass(), true],
            'long integer value' => [['max_value_length' => 2], 12345, true],
            'range integer' => [['field_type' => 'range'], 5, false],
            'range numeric string' => [['field_type' => 'range'], '5.5', false],
            'range null' => [['field_type' => 'range'], null, false],
            'range non-numeric string' => [['field_type' => 'range'], 'abc', true],
            'range boolean' => [['field_type' => 'range'], true, true],
            'range within bounds' => [['field_type' => 'range', 'min' => 1, 'max' => 10], '10', false],
            'range below min' => [['field_type' => 'range', 'min' => 1, 'max' => 10], 0, true],
            'range above max' => [['field_type' => 'range', 'min' => 1, 'max' => 10], 10.5, true],
            'range above max attribute' => [['field_type' => 'range', 'field_attributes' => ['max' => 10]], 11, true],
            'range below zero min' => [['field_type' => 'range', 'min' => 0], -1, true],
            'long integer value with maxlength attribute' => [['field_attributes' => ['maxlength' => 3]], 12345, true],
            'string above maxlength' => [['maxlength' => 3], 'abcd', true],
            'string above maxlength attribute' => [['field_attributes' => ['maxlength' => 3]], 'abcd', true],
            'string within maxlength attribute' => [['field_attributes' => ['maxlength' => 3]], 'abc', false],
            'string above maxlength for select' => [['field_type' => 'select', 'maxlength' => 3], 'abcd', true],
            'array element above maxlength' => [
                ['field_type' => 'select', 'multiple' => true, 'maxlength' => 3],
                ['a', 'abcd'],
                true
            ],
            'array with nested array' => [['field_type' => 'select', 'multiple' => true], ['a', ['b']], true],
            'array with boolean' => [['field_type' => 'select', 'multiple' => true], ['a', true], true],
            'array with null' => [['field_type' => 'select', 'multiple' => true], ['a', null], true],
            'array with object' => [['field_type' => 'select', 'multiple' => true], ['a', new \stdClass()], true],
            'array with numbers' => [['field_type' => 'select', 'multiple' => true], ['a', 1, 2.5], false],
            'array with too long number' => [
                ['field_type' => 'select', 'multiple' => true, 'maxlength' => 3],
                ['a', 12345],
                true
            ],
        ];
    }

    private function getGuard(): FieldValueGuard
    {
        $fieldTypes = [];
        foreach (self::FIELD_TYPES as $fieldType) {
            $fieldTypes[$fieldType] = 'Loki_FieldComponents::form/field/' . $fieldType . '.phtml';
        }

        return new FieldValueGuard(new FieldTypeManager($fieldTypes));
    }

    private function getViewModel(array $blockData): FieldViewModel
    {
        $viewModel = $this->createMock(FieldViewModel::class);
        $viewModel->method('getBlock')->willReturn($this->getBlockMock('loki.test.field', $blockData));

        return $viewModel;
    }
}
