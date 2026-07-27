<?php
declare(strict_types=1);

namespace Loki\Field\Plugin;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Validator\Validator;
use Loki\Field\Component\Base\Field\FieldViewModel;

class ValidatorPlugin
{
    public function aroundValidate(
        Validator $validator,
        callable $proceed,
        ComponentInterface $component,
        mixed $data = null,
        string $scope = '',
        int $depth = 0
    ) {
        if ($component->getViewModel() instanceof FieldViewModel && $component->getViewModel()->isDisabled()) {
            return false;
        }

        return $proceed($component, $data, $scope, $depth);
    }
}
