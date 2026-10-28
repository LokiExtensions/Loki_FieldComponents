<?php declare(strict_types=1);

namespace Loki\Field\Component\Base\Field;

use Loki\Components\Component\Component;
use Loki\Field\Util\OptionModelInterface;

/**
 * @method FieldRepository getRepository()
 * @method FieldViewModel getViewModel()
 */
class FieldComponent extends Component
{
    public const OPTIONS_VALIDATOR = 'field_options';

    private const SECRET_FIELD_SKIPPED_FILTERS = ['security', 'trim'];

    public function getValidators(array $validators = []): array
    {
        $viewModel = $this->getViewModel();
        $validators[] = $viewModel->getFieldName();

        if ($viewModel->isRequired()) {
            $validators[] = 'required';
        }

        if ((int)$viewModel->getBlock()->getMinlength() > 0 || (int)$viewModel->getBlock()->getMaxlength() > 0) {
            $validators[] = 'length';
        }

        if ($this->validateOptions($viewModel)) {
            $validators[] = self::OPTIONS_VALIDATOR;
        }

        // @todo: Improve this logic
        return parent::getValidators($validators);
    }

    public function getFilters(array $filters = []): array
    {
        $filters = parent::getFilters($filters);
        $viewModel = $this->getViewModel();
        if (false === $viewModel instanceof FieldViewModel || false === $viewModel->isSecretField()) {
            return $filters;
        }

        // Passwords are never rendered, while filtering them would silently change them
        return array_values(array_diff($filters, self::SECRET_FIELD_SKIPPED_FILTERS));
    }

    private function validateOptions(mixed $viewModel): bool
    {
        if (false === $viewModel instanceof FieldViewModelInterface) {
            return false;
        }

        if (false === $viewModel->getBlock()?->getValidateOptions()) {
            return false;
        }

        return $viewModel->getOptionModel() instanceof OptionModelInterface;
    }
}
