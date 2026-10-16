<?php declare(strict_types=1);

namespace Loki\Field\Validator;

use Loki\Field\ViewModel\PasswordComplexity;
use Loki\Components\Component\ComponentInterface;
use Loki\Components\Validator\ValidatorInterface;

class PasswordComplexityValidator implements ValidatorInterface
{
    public function __construct(
        private PasswordComplexity $passwordComplexity
    ) {
    }

    public function validate(mixed $value, ?ComponentInterface $component = null): bool|array
    {
        if ($value === null || $value === '' || false === is_scalar($value)) {
            return true;
        }

        $value = (string)$value;
        $errors = [];

        $minLength = $this->passwordComplexity->getMinimum();
        if (mb_strlen($value) < $minLength) {
            $errors[] = (string)__('Your password needs to have at least %1 characters.', $minLength);
        }

        $minNumeric = $this->passwordComplexity->getMinimumNumeric();
        if (preg_match_all('/[0-9]/', $value) < $minNumeric) {
            $errors[] = (string)__('Your password needs to contain at least %1 numbers.', $minNumeric);
        }

        $minOther = $this->passwordComplexity->getMinimumOther();
        if (preg_match_all('/[^a-zA-Z0-9]/u', $value) < $minOther) {
            $errors[] = (string)__('Your password needs to contain at least %1 non-alphanumeric characters.', $minOther);
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }
}
