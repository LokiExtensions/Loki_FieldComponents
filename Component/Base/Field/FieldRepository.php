<?php declare(strict_types=1);

namespace Loki\Field\Component\Base\Field;

use Loki\Field\Util\OptionModelInterface;
use Loki\Components\Component\ComponentRepository;

/**
 * @method FieldContext getContext()
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
        $viewModel = $this->getComponent()->getViewModel();
        if (false === $viewModel instanceof FieldViewModelInterface) {
            return null;
        }

        $optionModel = $viewModel->getOptionModel();
        if (false === $optionModel instanceof OptionModelInterface) {
            return null;
        }

        $options = $optionModel->getOptions($viewModel);
        if (empty($options)) {
            return null;
        }

        return array_key_first($options);
    }
}
