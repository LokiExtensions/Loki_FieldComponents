<?php declare(strict_types=1);

namespace Loki\Field\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class PasswordComplexity implements ArgumentInterface
{
    public const XML_PATH_MINIMUM_PASSWORD_LENGTH = 'customer/password/minimum_password_length';

    public function __construct(
        private int $minimum = 8,
        private int $minimumNumeric = 2,
        private int $minimumOther = 2,
        private ?ScopeConfigInterface $scopeConfig = null,
    ) {
    }

    public function getMinimum(): int
    {
        if (false === $this->scopeConfig instanceof ScopeConfigInterface) {
            return $this->minimum;
        }

        $configuredMinimum = (int)$this->scopeConfig->getValue(
            self::XML_PATH_MINIMUM_PASSWORD_LENGTH,
            ScopeInterface::SCOPE_STORE
        );

        return max($this->minimum, $configuredMinimum);
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
