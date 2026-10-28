<?php
declare(strict_types=1);

namespace Loki\Field\Plugin;

use Loki\Components\Component\ComponentInterface;
use Loki\Components\Validator\Validator;
use Loki\Field\Component\Base\Field\FieldViewModel;
use Loki\Field\Validator\FieldValueGuard;

class ValidatorPlugin
{
    public function __construct(
        private readonly FieldValueGuard $fieldValueGuard
    ) {
    }

    public function aroundValidate(
        Validator $validator,
        callable $proceed,
        ComponentInterface $component,
        mixed $data = null,
        string $scope = '',
        int $depth = 0
    ) {
        $viewModel = $component->getViewModel();
        if (false === $viewModel instanceof FieldViewModel) {
            return $proceed($component, $data, $scope, $depth);
        }

        if ($viewModel->isDisabled()) {
            return false;
        }

        if ($depth === 0) {
            $error = $this->fieldValueGuard->getError($viewModel, $data);
            if ($error !== null) {
                $this->addError($component, (string)$error);
                return false;
            }
        }

        return $proceed($component, $data, $scope, $depth);
    }

    private function addError(ComponentInterface $component, string $message): void
    {
        if (false === $component->dispatchLocalMessages()) {
            $component->getGlobalMessageRegistry()->addError($message);
            return;
        }

        $component->getLocalMessageRegistry()->addError($message);
    }
}
