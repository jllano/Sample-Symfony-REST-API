<?php

namespace App\Validator;

use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TaxNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var TaxNumber $constraint */

        if (!$constraint instanceof TaxNumber) {
            throw new UnexpectedTypeException($constraint, TaxNumber::class);
        }

        // other constraints (NotBlank, NotNull, etc.) to take care of that
        if (null === $value || '' === $value) {
            return;
        }

        // Ensure the Country Code is Valid
        $countryCode = substr($value, 0, 2);
        if (!array_key_exists($countryCode, $constraint->countries)) {
            $this->context->buildViolation('Invalid Country Code.')
                ->setParameter('{{ value }}', $value)
                ->addViolation();
            return;
        }

        // Validate via regular expressions
        $expectedFormat = $constraint->countries[$countryCode];
        $regex = '/^' . str_replace(['X', 'Y'], ['\d', '\p{L}'], preg_quote($expectedFormat, '/')) . '$/';

        if (!preg_match($regex, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
