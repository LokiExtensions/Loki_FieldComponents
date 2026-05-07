<?php declare(strict_types=1);

namespace Loki\Field\Util;

class OptionGroup implements OptionGroupInterface
{
    public function __construct(
        private readonly string $label,
        private readonly array $options = [],
        private readonly bool $disabled = false,
    ) {
    }

    public function getLabel(): string
    {
        return (string)__($this->label);
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
