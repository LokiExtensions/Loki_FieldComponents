<?php declare(strict_types=1);

namespace Loki\Field\Component\Base\Field;

use Loki\Components\Component\ComponentContext;
use Loki\Field\Util\OptionGroupInterface;
use Loki\Field\Util\OptionInterface;
use Loki\Field\Util\OptionModelInterface;
use Loki\Components\Component\ComponentRepository;

/**
 * @method ComponentContext getContext()
 */
abstract class FieldRepository extends ComponentRepository
{
    public function getFieldName(): string
    {
        $blockName = $this->getComponentName();
        $blockNameParts = explode('.', $blockName);
        return array_pop($blockNameParts);
    }

    public function getDefaultValue(): mixed
    {
        $defaultValue = $this->getComponent()->getBlock()?->getDefaultValue();
        if ($defaultValue !== null && $defaultValue !== '') {
            return $defaultValue;
        }

        $viewModel = $this->getComponent()->getViewModel();
        if (false === $viewModel instanceof FieldViewModelInterface) {
            return $defaultValue;
        }

        $optionModel = $viewModel->getOptionModel();
        if (false === $optionModel instanceof OptionModelInterface) {
            return $defaultValue;
        }

        $options = $this->flattenOptions($optionModel->getOptions($viewModel));
        if (empty($options)) {
            return $defaultValue;
        }

        foreach ($options as [, $option]) {
            if ($option instanceof OptionInterface && $option->isSelected() && false === $option->isDisabled()) {
                return $option->getValue();
            }
        }

        foreach ($options as [$optionIndex, $option]) {
            if (false === $option instanceof OptionInterface) {
                return $optionIndex;
            }

            if (false === $option->isDisabled()) {
                return $option->getValue();
            }
        }

        return $defaultValue;
    }

    /**
     * @return array<int, array{0: int|string, 1: mixed}>
     */
    private function flattenOptions(array $options): array
    {
        $flattenedOptions = [];
        foreach ($options as $optionIndex => $option) {
            if (false === $option instanceof OptionGroupInterface) {
                $flattenedOptions[] = [$optionIndex, $option];
                continue;
            }

            if ($option->isDisabled() || false === method_exists($option, 'getOptions')) {
                continue;
            }

            foreach ((array)$option->getOptions() as $subOptionIndex => $subOption) {
                $flattenedOptions[] = [$subOptionIndex, $subOption];
            }
        }

        return $flattenedOptions;
    }
}
