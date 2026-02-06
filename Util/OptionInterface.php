<?php
declare(strict_types=1);

namespace Loki\Field\Util;

interface OptionInterface
{
    public function getValue(): mixed;

    public function getLabel(): string;
    public function isDisabled(): bool;
}
