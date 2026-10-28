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
    public function __construct(
        private readonly OptionValue $optionValue
    ) {
    }

    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        if ($value === null || $value === '' || is_bool($value)) {
            return true;
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
        $knownValues = [];
        foreach ($this->getOptions($options) as [$optionIndex, $option, $disabled]) {
            $optionValue = $this->optionValue->getValue($option, $optionIndex);
            $knownValues[$optionValue] = true;
            if ($optionValue !== $value) {
                continue;
            }

            if ($disabled || $this->optionValue->isDisabled($option)) {
                return [__('This option is not available')];
            }

            return true;
        }

        // A value of "0" is a common placeholder for "nothing selected yet"
        if ($value === '0' && false === array_key_exists('0', $knownValues)) {
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
