<?php declare(strict_types=1);

namespace Loki\Field\Validator;

use Loki\Field\Component\Base\Field\FieldViewModel;
use Loki\Field\Field\FieldTypeManager;
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

    private const TEXT_FIELD_TYPES = ['input', 'password', 'password_repeat', 'text', 'textarea'];

    public function __construct(
        private readonly FieldTypeManager $fieldTypeManager,
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

        $fieldType = $this->fieldTypeManager->getFieldTypeFromViewModel($viewModel);

        if (is_array($value)) {
            if ($this->rejectArray($block, $fieldType)) {
                return __('Not a valid value');
            }

            return $this->getArrayLengthError($block, $fieldType, $value);
        }

        if (in_array($fieldType, self::BOOLEAN_FIELD_TYPES, true)) {
            return $this->isBooleanValue($value) ? null : __('Not a valid value');
        }

        if ($fieldType === 'range') {
            return $this->isValidRangeValue($block, $value) ? null : __('Not a valid value');
        }

        if (in_array($fieldType, self::TEXT_FIELD_TYPES, true)) {
            return $this->getTextError($block, $fieldType, $value);
        }

        if (false === is_string($value)) {
            return null;
        }

        return $this->getLengthError($block, $fieldType, $value);
    }

    private function isValidRangeValue(AbstractBlock $block, mixed $value): bool
    {
        if ($value === null || $value === '') {
            return true;
        }

        if (false === (is_string($value) || is_int($value) || is_float($value)) || false === is_numeric($value)) {
            return false;
        }

        $min = $this->getRangeBound($block, 'min');
        if ($min !== null && (float)$value < $min) {
            return false;
        }

        $max = $this->getRangeBound($block, 'max');

        return $max === null || (float)$value <= $max;
    }

    private function getRangeBound(AbstractBlock $block, string $name): ?float
    {
        $bound = $block->getData($name);
        if ($bound === null || $bound === '') {
            $fieldAttributes = (array)$block->getFieldAttributes();
            $bound = $fieldAttributes[$name] ?? null;
        }

        return is_numeric($bound) ? (float)$bound : null;
    }

    private function getTextError(AbstractBlock $block, string $fieldType, mixed $value): ?Phrase
    {
        if ($value === null) {
            return null;
        }

        if (is_string($value)) {
            return $this->getLengthError($block, $fieldType, $value);
        }

        if (is_int($value) || is_float($value)) {
            return $this->getLengthError($block, $fieldType, (string)$value);
        }

        return __('Not a valid value');
    }

    private function getArrayLengthError(AbstractBlock $block, string $fieldType, array $values): ?Phrase
    {
        foreach ($values as $value) {
            if (false === is_string($value) && false === is_int($value) && false === is_float($value)) {
                return __('Not a valid value');
            }

            $error = $this->getLengthError($block, $fieldType, (string)$value);
            if ($error !== null) {
                return $error;
            }
        }

        return null;
    }

    private function getLengthError(AbstractBlock $block, string $fieldType, string $value): ?Phrase
    {
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

        $explicitMaxLength = $this->getExplicitMaxLength($block);
        if ($explicitMaxLength > 0) {
            return $explicitMaxLength;
        }

        if ($fieldType === 'textarea') {
            return $this->defaultTextareaMaxLength;
        }

        return $this->defaultMaxLength;
    }

    private function getExplicitMaxLength(AbstractBlock $block): int
    {
        if ((int)$block->getMaxlength() > 0) {
            return (int)$block->getMaxlength();
        }

        $fieldAttributes = (array)$block->getFieldAttributes();

        return (int)($fieldAttributes['maxlength'] ?? 0);
    }
}
