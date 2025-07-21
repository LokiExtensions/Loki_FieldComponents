<?php declare(strict_types=1);

namespace Loki\Field\Component\Base\Field;

use Magento\Customer\Model\Session as CustomerSession;
use Loki\Components\Component\ComponentContextInterface;

class FieldContext implements ComponentContextInterface
{
    public function __construct(
        private readonly CustomerSession $customerSession,
    ) {
    }

    public function getCustomerSession(): CustomerSession
    {
        return $this->customerSession;
    }
}
