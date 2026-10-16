<?php declare(strict_types=1);

namespace Loki\Field\Component\Base\Field;

use Loki\Components\Component\ComponentContext;
use Loki\Components\Component\ComponentViewModel;
use Loki\Field\Field\FieldTypeManager;
use Loki\Field\Util\OptionModelInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\AbstractBlock;

/**
 * @method ComponentContext getContext()
 */
class FieldViewModel extends ComponentViewModel implements FieldViewModelInterface
{
    protected bool $isRequiredByDefault = false;

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

        return ucwords(str_replace('_', ' ', $this->getFieldName()));
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
            'aria-label' => (string)__($this->getFieldLabel()),
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
        if ($this->hasAutocomplete() && false === $this->isDisabled()) {
            $fieldAttributes['autocomplete'] = $this->getAutocomplete();
        }

        if ($this->getInputType() === 'date') {
            $maxDate = strtotime((string)$this->getBlock()->getMaxDate());
            if ($maxDate !== false) {
                $fieldAttributes['max'] = date('Y-m-d', $maxDate);
            }

            $minDate = strtotime((string)$this->getBlock()->getMinDate());
            if ($minDate !== false) {
                $fieldAttributes['min'] = date('Y-m-d', $minDate);
            }
        }

        if ($this->getPlaceholder()) {
            $fieldAttributes['placeholder'] = $this->getPlaceholder();
        }

        $fieldAttributesFromBlock = (array)$this->getBlock()->getFieldAttributes();
        return array_merge($fieldAttributes, $customAttributes, $fieldAttributesFromBlock);
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

            if ($fieldAttributeValue instanceof \Stringable) {
                $fieldAttributeValue = (string)$fieldAttributeValue;
            }

            if (false === is_string($fieldAttributeValue) && false === is_numeric($fieldAttributeValue)) {
                $fieldAttributeValue = (string)json_encode($fieldAttributeValue);
            }

            $fieldAttributesAsString .= ' ' . $fieldAttributeName . '="' . $this->escapeAttribute($fieldAttributeValue) . '"';
        }

        return trim($fieldAttributesAsString, " \f\n\r\t\v\x00");
    }

    private function escapeAttribute(string|int|float $value): string
    {
        return (string)$this->getEscaper()->escapeHtml((string)$value);
    }

    protected function getEscaper(): Escaper
    {
        return ObjectManager::getInstance()->get(Escaper::class);
    }

    public function getJsData(): array
    {
        $jsDataFromBlock = (array)$this->getBlock()->getJsData();

        return [
            ...parent::getJsData(),
            'required' => $this->isRequired(),
            'requiredErrorMessage' => $this->getBlock()->getRequiredErrorMessage(),
            'disabled' => $this->isDisabled(),
            'fieldName' => $this->getFieldName(),
            ...$jsDataFromBlock,
        ];
    }

    public function getValue(): mixed
    {
        $value = parent::getValue();
        if (false === $this->isEmptyValue($value)) {
            return $value;
        }

        if (false === $this->getComponent()->hasRepository()) {
            return $value;
        }

        $defaultValue = $this->getComponent()->getRepository()->getDefaultValue();
        if ($this->isEmptyValue($defaultValue)) {
            return $value;
        }

        $this->getComponent()->getRepository()->saveValue($defaultValue);

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

    public function getInputPrefix(): string
    {
        return (string)$this->getBlock()->getInputPrefix();
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
        $value = $this->getValue();

        // An unchecked checkbox is stored as false, which does not satisfy a required field
        return $value === false || $this->isEmptyValue($value);
    }

    private function isEmptyValue(mixed $value): bool
    {
        if (is_string($value)) {
            return trim($value, " \f\n\r\t\v\x00") === '';
        }

        return $value === null || $value === [];
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
