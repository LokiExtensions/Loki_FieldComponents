<?php declare(strict_types=1);

namespace Yireo\LokiFieldComponents\Component\Base\Field;

use Magento\Framework\View\Element\AbstractBlock;
use Yireo\LokiComponents\Component\ComponentViewModel;
use Yireo\LokiFieldComponents\Util\OptionModelInterface;
use Yireo\LokiComponents\Component\Behaviour\LengthBehaviourInterface;

/**
 * @method FieldContext getContext()
 */
abstract class FieldViewModel extends ComponentViewModel implements FieldViewModelInterface,LengthBehaviourInterface
{
    public function getFieldName(): string
    {
        $parts = explode('.', $this->getNameInLayout());

        return array_pop($parts);
    }

    public function getJsComponentName(): ?string
    {
        return 'LokiFieldComponent';
    }

    abstract public function getFieldLabel(): string;

    public function showFieldLabel(): bool
    {
        $showFieldLabel = $this->getBlock()->getShowFieldLabel();
        if (is_bool($showFieldLabel)) {
            return $showFieldLabel;
        }

        return true;
    }

    public function getFieldData(): array
    {
        return [
            'alias' => 'field-container',
            'css_classes' => $this->getBlock()->getCssClasses(),
        ];
    }

    public function getFieldId(): string
    {
        return $this->getElementId().'-field';
    }

    public function getJsData(): array
    {
        return [
            ...parent::getJsData(),
            'fieldName' => $this->getFieldName(),
        ];
    }

    public function getFieldTemplate(): string
    {
        return $this->getContext()->getFieldTemplateLocator()->getFieldTemplateFromViewModel($this);
    }

    public function getFieldType(): string
    {
        return $this->getContext()->getFieldTemplateLocator()->getFieldTypeFromViewModel($this);
    }

    public function getValue(): mixed
    {
        $value = parent::getValue();
        if (!empty($value)) {
            return $value;
        }

        if (false === $this->getComponent()->hasRepository()) {
            return null;
        }

        $defaultValue = $this->getComponent()->getRepository()->getDefaultValue();
        if (!empty($defaultValue)) {
            $this->getComponent()->getRepository()->saveValue($defaultValue);
        }

        return $value;
    }

    public function getInputType(): string
    {
        $inputType = (string)$this->getBlock()->getInputType();
        if (!empty($inputType)) {
            return $inputType;
        }

        return 'text';
    }

    public function getInputLabel(): string
    {
        return (string)$this->getBlock()->getInputLabel();
    }

    public function isRequired(): bool
    {
        return false;
    }

    public function getHelpText(): string
    {
        return (string)$this->getBlock()->getHelpText();
    }

    public function isDisabled(): bool
    {
        return (bool)$this->getBlock()->getDisabled();
    }

    public function getNameInLayout(): string
    {
        return $this->getBlock()->getNameInLayout();
    }

    public function getPlaceholder(): string
    {
        return (string)$this->getBlock()->getPlaceholder();
    }

    public function hasAutocomplete(): bool
    {
        return !empty($this->getAutocomplete());
    }

    public function getAutocomplete(): string
    {
        return (string)$this->getBlock()->getAutocomplete();
    }

    public function hasMinLength(): bool
    {
        return $this->getMinLength() > 0;
    }

    public function getMinLength(): int
    {
        return (int)$this->getBlock()->getMinLength();
    }

    public function hasMaxLength(): bool
    {
        return $this->getMaxLength() > 0;
    }

    public function getMaxLength(): int
    {
        return (int)$this->getBlock()->getMaxLength();
    }

    public function hasMin(): bool
    {
        return $this->getMin() > 0;
    }

    public function getMin(): string
    {
        return (string)$this->getBlock()->getMin();
    }

    public function hasMax(): bool
    {
        return $this->getMax() > 0;
    }

    public function getMax(): string
    {
        return (string)$this->getBlock()->getMax();
    }

    public function getOptionModel(): ?OptionModelInterface
    {
        $block = $this->getBlock();
        if ($block->getOptionModel() instanceof OptionModelInterface) {
            return $block->getOptionModel();
        }

        $ancestorBlock = $block->getAncestorBlock();
        if ($ancestorBlock instanceof AbstractBlock) {
            if ($ancestorBlock->getOptionModel() instanceof OptionModelInterface) {
                return $ancestorBlock->getOptionModel();
            }
        }

        return null;
    }
}
