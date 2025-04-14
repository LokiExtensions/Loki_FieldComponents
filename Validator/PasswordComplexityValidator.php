<?php declare(strict_types=1);

namespace Yireo\LokiFieldComponents\Validator;

use Yireo\LokiFieldComponents\ViewModel\PasswordComplexity;
use Yireo\LokiComponents\Component\ComponentInterface;
use Yireo\LokiComponents\Validator\ValidatorInterface;

class PasswordComplexityValidator implements ValidatorInterface
{
    public function __construct(
        private PasswordComplexity $passwordComplexity
    ) {
    }

    public function validate(mixed $value, ?ComponentInterface $component = null): true|array
    {
        if (empty($value)) {
            return true;
        }

        $errors = [];

        $minLength = $this->passwordComplexity->getMinimum();
        if (strlen($value) < $minLength) {
            $errors[] = (string)__('Your password needs to have more than %1 characters.', $minLength);
        }

        $minNumeric = $this->passwordComplexity->getMinimumNumeric();
        if (preg_match_all( '/[0-9]{1}/', $value) < $minNumeric) {
            $errors[] = (string)__('Your password needs to contain at least %1 numbers.', $minNumeric);
        }

        $minOther = $this->passwordComplexity->getMinimumOther();
        if (preg_match_all( '/[^a-z0-9]{1}/', $value) < $minOther) {
            $errors[] = (string)__('Your password needs to contain at least %1 non-alphanumeric characters.', $minOther);
        }

        if (!empty($errors)) {
            return $errors;
        }

        return true;
    }
}
