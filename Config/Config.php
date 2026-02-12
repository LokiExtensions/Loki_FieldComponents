<?php declare(strict_types=1);

namespace Loki\Field\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class Config implements ArgumentInterface
{
    public function __construct(
        private ScopeConfigInterface $scopeConfig,
    ) {
    }

    public function useRequiredBlock(): bool
    {
        return (bool)$this->scopeConfig->getValue(
            'loki_field_components/fields/use_required_block',
            ScopeInterface::SCOPE_STORE
        );
    }
}
