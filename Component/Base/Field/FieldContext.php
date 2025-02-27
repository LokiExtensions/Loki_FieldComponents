<?php declare(strict_types=1);

namespace Yireo\LokiFieldComponents\Component\Base\Field;

use Magento\Customer\Model\Session as CustomerSession;
use Yireo\LokiFieldComponents\Util\Component\FieldTemplateLocator;
use Yireo\LokiComponents\Component\ComponentContextInterface;

class FieldContext implements ComponentContextInterface
{
    public function __construct(
        private readonly FieldTemplateLocator $fieldTemplateLocator,
        private readonly CustomerSession $customerSession,
    ) {
    }

    public function getFieldTemplateLocator(): FieldTemplateLocator
    {
        return $this->fieldTemplateLocator;
    }

    public function getCustomerSession(): CustomerSession
    {
        return $this->customerSession;
    }
}
