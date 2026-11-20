<?php declare(strict_types=1);

namespace Loki\Field\Validator;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Validator\ValidatorInterface;
use Loki\Field\Component\Base\Field\FieldViewModelInterface;
use Loki\Field\Util\OptionGroupInterface;
use Loki\Field\Util\OptionModelInterface;
use Loki\Field\Util\OptionValue;

class OptionValidator implements ValidatorInterface
{
    private const BOOLEAN_FIELD_TYPES = ['checkbox', 'switch'];

    public function __construct(
        private readonly OptionValue $optionValue
    ) {
    }

    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        if ($value === null || $value === '') {
            return true;
        }

        $block = $component?->getBlock();
        if (is_bool($value)) {
            if (in_array((string)$block?->getFieldType(), self::BOOLEAN_FIELD_TYPES, true)) {
                return true;
            }

            $value = $value ? '1' : '0';
        }

        $viewModel = $component?->getViewModel();
        if (false === $viewModel instanceof FieldViewModelInterface) {
            return true;
        }

        $optionModel = $viewModel->getOptionModel();
        if (false === $optionModel instanceof OptionModelInterface) {
            return true;
        }

        $options = $optionModel->getOptions($viewModel);
        if ($options === []) {
            return true;
        }

        if (false === is_scalar($value)) {
            return [__('Not a valid option')];
        }

        $value = (string)$value;
        $hasZeroOption = false;
        foreach ($this->getOptions($options) as [$optionIndex, $option, $disabled]) {
            $optionValue = $this->optionValue->getValue($option, $optionIndex);
            $hasZeroOption = $hasZeroOption || $optionValue === '0';
            if ($optionValue !== $value) {
                continue;
            }

            if ($disabled || $this->optionValue->isDisabled($option)) {
                return [__('This option is not available')];
            }

            return true;
        }

        if ($value === '0' && false === $hasZeroOption && $block?->getAllowZeroPlaceholder() === true) {
            return true;
        }

        return [__('Not a valid option')];
    }

    /**
     * @return array<int, array{0: int|string, 1: mixed, 2: bool}>
     */
    private function getOptions(array $options): array
    {
        $flattenedOptions = [];
        foreach ($options as $optionIndex => $option) {
            if (false === $option instanceof OptionGroupInterface) {
                $flattenedOptions[] = [$optionIndex, $option, false];
                continue;
            }

            if (false === method_exists($option, 'getOptions')) {
                continue;
            }

            foreach ((array)$option->getOptions() as $subOptionIndex => $subOption) {
                $flattenedOptions[] = [$subOptionIndex, $subOption, $option->isDisabled()];
            }
        }

        return $flattenedOptions;
    }
}
