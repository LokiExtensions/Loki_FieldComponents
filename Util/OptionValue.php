<?php declare(strict_types=1);

namespace Loki\Field\Util;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class OptionValue implements ArgumentInterface
{
    public function getValue(mixed $option, int|string $optionIndex): string
    {
        $value = $option instanceof OptionInterface ? $option->getValue() : $optionIndex;

        return $this->toString($value);
    }

    public function getLabel(mixed $option): string
    {
        return $option instanceof OptionInterface ? $option->getLabel() : $this->toString($option);
    }

    public function isDisabled(mixed $option): bool
    {
        return $option instanceof OptionInterface && $option->isDisabled();
    }

    public function isSelected(mixed $option, int|string $optionIndex, mixed $currentValue): bool
    {
        // The selected flag of an option only applies when there is no current value yet
        if ($currentValue === null || $currentValue === '' || $currentValue === []) {
            return $option instanceof OptionInterface && $option->isSelected();
        }

        $optionValue = $this->getValue($option, $optionIndex);

        if (is_array($currentValue)) {
            foreach ($currentValue as $value) {
                if (is_scalar($value) && $this->toString($value) === $optionValue) {
                    return true;
                }
            }

            return false;
        }

        if (false === is_scalar($currentValue)) {
            return false;
        }

        return $this->toString($currentValue) === $optionValue;
    }

    private function toString(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_scalar($value) || $value instanceof \Stringable) {
            return (string)$value;
        }

        return '';
    }
}
