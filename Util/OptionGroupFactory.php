<?php declare(strict_types=1);

namespace Loki\Field\Util;

use Magento\Framework\ObjectManagerInterface;

class OptionGroupFactory
{
    public function __construct(
        private ObjectManagerInterface $objectManager
    ) {
    }

    public function create(string $label, array $options = [], bool $disabled = false): OptionGroup
    {
        return $this->objectManager->create(OptionGroup::class, [
            'label' => $label,
            'options' => $options,
            'disabled' => $disabled
        ]);
    }
}
