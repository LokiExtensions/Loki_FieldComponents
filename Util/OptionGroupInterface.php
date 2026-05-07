<?php
declare(strict_types=1);

namespace Loki\Field\Util;

interface OptionGroupInterface
{
    public function getLabel(): string;
    public function isDisabled(): bool;
}
