<?php declare(strict_types=1);

namespace Loki\Field\Util\Component;

use Magento\Framework\View\Element\AbstractBlock;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Loki\Field\Component\Base\Field\FieldViewModelInterface;
use Loki\Field\Util\OptionModelInterface;

// @todo: Rename this to FieldType
class FieldTemplateLocator implements ArgumentInterface
{
    public function __construct(
        private array $fieldTypes = []
    ) {
    }

    public function getFieldTypeFromViewModel(FieldViewModelInterface $viewModel): string
    {
        $block = $viewModel->getBlock();
        if (false === $block instanceof AbstractBlock) {
            return 'text';
        }

        $fieldType = $block->getFieldType();
        if ($fieldType === 'text_select') {
            $fieldType = $this->getFieldTypeFromTextSelect($block);
        }

        if (array_key_exists($fieldType, $this->getFieldTemplateMapping())) {
            return $fieldType;
        }

        return 'text';
    }

    public function getFieldTemplateFromViewModel(FieldViewModelInterface $viewModel): string
    {
        $block = $viewModel->getBlock();
        if (false === $block instanceof AbstractBlock) {
            return $this->getFieldTemplateByType('text');
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
        return $this->getFieldTemplateMapping()[$type];
    }

    public function getFieldTemplateMapping(): array
    {
        return $this->fieldTypes;
    }

    /**
     * @param AbstractBlock $block
     * @return string
     * @todo: Allow for multiple field template locators
     */
    private function getFieldTypeFromTextSelect(AbstractBlock $block): string
    {
        $viewModel = $block->getViewModel();
        $optionModel = $block->getOptionModel();

        if (false === $optionModel instanceof OptionModelInterface) {
            return 'text';
        }

        if (count($optionModel->getOptions($viewModel)) === 0) {
            return 'text';
        }

        $block->setOptionModel($optionModel);
        return 'select';
    }
}
