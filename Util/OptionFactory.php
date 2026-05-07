<?php declare(strict_types=1);

namespace Loki\Field\Util;

use Magento\Framework\ObjectManagerInterface;

class OptionFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager
    ) {
    }

    public function create(
        mixed $value,
        string $label,
        bool $disabled = false,
        bool $selected = false
    ): Option {
        return $this->objectManager->create(Option::class, [
            'value' => $value,
            'label' => $label,
            'disabled' => $disabled,
            'selected' => $selected,
        ]);
    }
}
