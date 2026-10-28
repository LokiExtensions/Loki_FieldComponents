<?php declare(strict_types=1);

namespace Loki\Field\Validator;

use Loki\Field\Component\Base\Field\FieldViewModel;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\AbstractBlock;

class FieldValueGuard
{
    public const DEFAULT_MAX_LENGTH = 255;
    public const DEFAULT_TEXTAREA_MAX_LENGTH = 65535;

    private const SINGLE_VALUE_FIELD_TYPES = [
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

    private const BOOLEAN_FIELD_TYPES = ['checkbox', 'switch'];

    public function __construct(
        private readonly int $defaultMaxLength = self::DEFAULT_MAX_LENGTH,
        private readonly int $defaultTextareaMaxLength = self::DEFAULT_TEXTAREA_MAX_LENGTH,
    ) {
    }

    public function getError(FieldViewModel $viewModel, mixed $value): ?Phrase
    {
        $block = $viewModel->getBlock();
        if (false === $block instanceof AbstractBlock) {
            return null;
        }

        $fieldType = (string)$block->getFieldType();

        if (is_array($value)) {
            return $this->rejectArray($block, $fieldType) ? __('Not a valid value') : null;
        }

        if (in_array($fieldType, self::BOOLEAN_FIELD_TYPES, true)) {
            return $this->isBooleanValue($value) ? null : __('Not a valid value');
        }

        if (false === is_string($value)) {
            return null;
        }

        $maxLength = $this->getMaxLength($block, $fieldType);
        if ($maxLength > 0 && mb_strlen($value) > $maxLength) {
            return __('Value should have no more than %1 characters', $maxLength);
        }

        return null;
    }

    private function rejectArray(AbstractBlock $block, string $fieldType): bool
    {
        if (true === $block->getAllowArrayValue()) {
            return false;
        }

        if (false === in_array($fieldType, self::SINGLE_VALUE_FIELD_TYPES, true)) {
            return false;
        }

        $fieldAttributes = (array)$block->getFieldAttributes();

        return empty($fieldAttributes['multiple']) && empty($block->getMultiple());
    }

    private function isBooleanValue(mixed $value): bool
    {
        return in_array($value, [true, false, null, 0, 1, '0', '1', ''], true);
    }

    private function getMaxLength(AbstractBlock $block, string $fieldType): int
    {
        if ($block->hasData('max_value_length')) {
            return (int)$block->getData('max_value_length');
        }

        // An explicit maxlength is handled by the length validator
        $fieldAttributes = (array)$block->getFieldAttributes();
        if ((int)$block->getMaxlength() > 0 || !empty($fieldAttributes['maxlength'])) {
            return 0;
        }

        if ($fieldType === 'textarea') {
            return $this->defaultTextareaMaxLength;
        }

        return $this->defaultMaxLength;
    }
}
