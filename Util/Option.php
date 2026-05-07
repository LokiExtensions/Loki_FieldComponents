<?php declare(strict_types=1);

namespace Loki\Field\Util;

class Option implements OptionInterface
{
    public function __construct(
        private readonly mixed $value,
        private readonly string $label,
        private readonly bool $disabled = false,
        private readonly bool $selected = false
    ) {
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getLabel(): string
    {
        return (string)__($this->label);
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function isSelected(): bool
    {
        return $this->selected;
    }
}
