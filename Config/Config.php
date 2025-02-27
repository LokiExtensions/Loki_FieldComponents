<?php declare(strict_types=1);

namespace Yireo\LokiFieldComponents\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Config implements ArgumentInterface
{
    public function __construct(
        private ScopeConfigInterface $scopeConfig,
    ) {
    }

    public function useRequiredBlock(): bool
    {
        return (bool)$this->scopeConfig->getValue('yireo_loki_field_components/fields/use_required_block');
    }
}
