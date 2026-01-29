<?php declare(strict_types=1);

namespace Loki\Field\Component\Base\Field;

use Loki\Components\Component\ComponentContext;
use Loki\Field\Field\FieldTypeManager;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Element\AbstractBlock;
use Loki\Components\Component\ComponentViewModel;
use Loki\Field\Util\OptionModelInterface;

/**
 * @method ComponentContext getContext()
 */
class FieldViewModel extends ComponentViewModel implements FieldViewModelInterface
{
    protected bool $isRequiredByDefault = true;
    protected bool $skipValidation = false;

    public function getFieldName(): string
    {
        $fieldName = (string)$this->getBlock()->getFieldName();
        if ($fieldName) {
            return $fieldName;
        }

        $parts = explode('.', $this->getNameInLayout());

        return array_pop($parts);
    }

    public function getJsComponentName(): ?string
    {
        return 'LokiFieldComponent';
    }

    public function getFieldLabel(): string
    {
        $fieldLabel = (string)$this->getBlock()->getFieldLabel();
        if ($fieldLabel) {
            return $fieldLabel;
        }

        return strtoupper($this->getFieldName());
    }

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

    public function getFieldAttributes(array $customAttributes = []): array
    {
        $fieldAttributes = [
            'id' => $this->getFieldId(),
            'aria-label' => $this->getFieldLabel(),
            'aria-errormessage' => $this->getElementId() . '-messages',
            'data-type' => 'field',
            'x-ref' => 'field',
            ':disabled' => 'disabled',
            ':aria-invalid' => 'isAriaInvalid',
            ':data-valid' => 'isValid'
        ];

        $fieldAttributes['required'] = $this->isRequired();
        $fieldAttributes['disabled'] = $this->isDisabled();

        $fieldAttributes['autocomplete'] = 'off';
        if ($this->hasAutocomplete()) {
            $fieldAttributes['autocomplete'] = $this->getAutocomplete();
        }

        if ($this->getPlaceholder()) {
            $fieldAttributes['placeholder'] = $this->getPlaceholder();
        }

        $fieldAttributesFromBlock = (array)$this->getBlock()->getFieldAttributes();

        return array_merge($fieldAttributes, $fieldAttributesFromBlock, $customAttributes);
    }

    public function getFieldAttributesAsString(array $customAttributes = []): string
    {
        $fieldAttributesAsString = '';
        foreach ($this->getFieldAttributes($customAttributes) as $fieldAttributeName => $fieldAttributeValue) {
            if ($fieldAttributeValue === false) {
                continue;
            }

            if ($fieldAttributeValue === null || $fieldAttributeValue === true) {
                $fieldAttributesAsString .= ' ' .$fieldAttributeName;
                continue;
            }

            $fieldAttributesAsString .= ' '.$fieldAttributeName . '="' . $fieldAttributeValue . '"';
        }

        return trim($fieldAttributesAsString);
    }

    public function getJsData(): array
    {
        $jsDataFromBlock = (array)$this->getBlock()->getJsData();

        return [
            ...parent::getJsData(),
            ...$jsDataFromBlock,
            'skipValidation' => $this->skipValidation,
            'disabled' => $this->isDisabled(),
            'fieldName' => $this->getFieldName(),
        ];
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

        return $defaultValue;
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
        $required = $this->getBlock()->getRequired();
        if (is_bool($required)) {
            return $required;
        }

        return $this->isRequiredByDefault;
    }

    public function isEmpty(): bool
    {
        return empty($this->getValue());
    }

    public function isValid(): bool
    {
        if ($this->isRequired() && $this->isEmpty()) {
            return false;
        }

        return parent::isValid();
    }

    public function getHelpText(): string
    {
        return (string)$this->getBlock()->getHelpText();
    }

    public function getComment(): string
    {
        return (string)$this->getBlock()->getComment();
    }

    public function isDisabled(): bool
    {
        return (bool)$this->getBlock()->getDisabled();
    }

    public function getNameInLayout(): string
    {
        return (string)$this->getBlock()?->getNameInLayout();
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

    public function useLabelTagForLabel(AbstractBlock $block): bool
    {
        $fieldTypeManager = ObjectManager::getInstance()->get(FieldTypeManager::class);
        if (in_array($fieldTypeManager->getFieldTypeFromViewModel($this), ['radio'])) {
            return false;
        }

        if (false === $block->getUseLabelTag()) {
            return false;
        }

        return true;
    }
}
