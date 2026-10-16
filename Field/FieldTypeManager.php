<?php declare(strict_types=1);

namespace Loki\Field\Field;

use Loki\Components\Component\ComponentViewModelInterface;
use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Loki\Field\Component\Base\Field\FieldViewModelInterface;
use Loki\Field\Util\OptionModelInterface;

class FieldTypeManager implements ArgumentInterface
{
    public function __construct(
        private array $fieldTypes = []
    ) {
    }

    public function showLabel(FieldViewModelInterface $viewModel): bool
    {
        $blockShowLabel =$viewModel->getBlock()->getShowLabel();
        if (is_bool($blockShowLabel)) {
            return $blockShowLabel;
        }

        return !in_array($this->getFieldTypeFromViewModel($viewModel), ['checkbox']);
    }

    public function getFieldTypeFromViewModel(FieldViewModelInterface $viewModel): string
    {
        $block = $viewModel->getBlock();
        if (false === $block instanceof AbstractBlock) {
            return 'input';
        }

        $fieldType = $block->getFieldType();
        if (empty($fieldType)) {
            return 'input';
        }

        if ($fieldType === 'text_select') {
            $fieldType = $this->getFieldTypeFromTextSelect($viewModel);
        }

        if (array_key_exists($fieldType, $this->getFieldTemplateMapping())) {
            return $fieldType;
        }

        return 'input';
    }

    public function getFieldTemplateFromViewModel(FieldViewModelInterface $viewModel): string
    {
        $block = $viewModel->getBlock();
        if (false === $block instanceof AbstractBlock) {
            return $this->getFieldTemplateByType('input');
        }

        $fieldTemplate = $block->getFieldTemplate();
        if (false === empty($fieldTemplate)) {
            return $fieldTemplate;
        }

        $fieldType = $this->getFieldTypeFromViewModel($viewModel);
        return $this->getFieldTemplateByType($fieldType);
    }

    public function getFieldTemplateByType(string $type): string
    {
        $fieldTemplateMapping = $this->getFieldTemplateMapping();
        if (array_key_exists($type, $fieldTemplateMapping)) {
            return $fieldTemplateMapping[$type];
        }

        return $fieldTemplateMapping['input'] ?? 'Loki_FieldComponents::form/field/input.phtml';
    }

    public function getFieldTemplateMapping(): array
    {
        return $this->fieldTypes;
    }

    /**
     * @param FieldViewModelInterface $viewModel
     * @return string
     * @todo: Allow for multiple field template locators
     */
    private function getFieldTypeFromTextSelect(FieldViewModelInterface $viewModel): string
    {
        $optionModel = $viewModel->getOptionModel();

        if (false === $optionModel instanceof OptionModelInterface) {
            return 'text';
        }

        if (count($optionModel->getOptions($viewModel)) === 0) {
            return 'text';
        }

        return 'select';
    }
}
