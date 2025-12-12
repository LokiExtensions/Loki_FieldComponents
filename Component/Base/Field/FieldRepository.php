<?php declare(strict_types=1);

namespace Loki\Field\Component\Base\Field;

use Loki\Components\Component\ComponentContext;
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
        $defaultValue = $this->getComponent()->getBlock()->getDefaultValue();

        $viewModel = $this->getComponent()->getViewModel();
        if (false === $viewModel instanceof FieldViewModelInterface) {
            return $defaultValue;
        }

        $optionModel = $viewModel->getOptionModel();
        if (false === $optionModel instanceof OptionModelInterface) {
            return $defaultValue;
        }

        $options = $optionModel->getOptions($viewModel);
        if (empty($options)) {
            return $defaultValue;
        }

        return array_key_first($options);
    }
}
