<?php declare(strict_types=1);

namespace Loki\Field\Util;

class Option implements OptionInterface
{
    public function __construct(
        private mixed $value,
        private string $label,
        private bool $disabled = false
    ) {
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }
}
