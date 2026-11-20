<?php declare(strict_types=1);

namespace Loki\Field\Component\Base\Field;

use Loki\Components\Component\Component;
use Loki\Field\Util\OptionModelInterface;
use Magento\Framework\View\Element\AbstractBlock;

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

        if ($this->hasLengthLimit($viewModel->getBlock())) {
            $validators[] = 'length';
        }

        if ($viewModel->getBlock()->getFieldType() === 'range') {
            $validators[] = 'number';
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
        if (false === $this->isSecretField($this->getViewModel())) {
            return $filters;
        }

        // Passwords are never rendered, while filtering them would silently change them
        return array_values(array_diff($filters, self::SECRET_FIELD_SKIPPED_FILTERS));
    }

    private function hasLengthLimit(AbstractBlock $block): bool
    {
        if ((int)$block->getMinlength() > 0 || (int)$block->getMaxlength() > 0) {
            return true;
        }

        $fieldAttributes = (array)$block->getFieldAttributes();

        return !empty($fieldAttributes['minlength']) || !empty($fieldAttributes['maxlength']);
    }

    private function isSecretField(mixed $viewModel): bool
    {
        return $viewModel instanceof FieldViewModel && $viewModel->isSecretField();
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
