<?php declare(strict_types=1);

namespace Loki\Field\Component\Base\Field;

use Loki\Components\Component\Component;

/**
 * @method FieldRepository getRepository()
 * @method FieldViewModel getViewModel()
 */
class FieldComponent extends Component
{
    public function getValidators(array $validators = []): array
    {
        $viewModel = $this->getViewModel();
        $validators[] = $viewModel->getFieldName();

        if ($viewModel->isRequired()) {
            $validators[] = 'required';
        }

        if ($viewModel->hasMinLength() || $viewModel->hasMaxLength()) {
            $validators[] = 'length';
        }

        // @todo: Improve this logic
        return parent::getValidators($validators);
    }
}
