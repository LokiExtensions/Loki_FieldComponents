<?php declare(strict_types=1);

namespace Loki\Field\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class PasswordComplexity implements ArgumentInterface
{
    public function __construct(
        private int $minimum = 8,
        private int $minimumNumeric = 2,
        private int $minimumOther = 2,
    ) {
    }

    public function getMinimum(): int
    {
        return $this->minimum;
    }

    public function getMinimumNumeric(): int
    {
        return $this->minimumNumeric;
    }

    public function getMinimumOther(): int
    {
        return $this->minimumOther;
    }
}
