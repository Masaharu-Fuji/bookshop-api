<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class MinimalPropertiesValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\MinimalProperties $constraint */
        //アトリビュート書けた変数に対して、どのような検証をかけるか
        //どこのバリデーション？

/*         if (null === $value || '' === $value) {
            return;
        }

        if ($this->formatTypeOf($value) === "string") {
            $this->context
                ->buildViolation($this->formatTypeOf($value))
                ->addViolation();
        }

        if ($this->formatTypeOf($value) === "Doctrine\\Common\\Collections\\ArrayCollection") {
            $this->context
                ->buildViolation("fuga")
                ->addViolation();
        } */
        /* if (!str_starts_with($uri, "tweet/api/")) {
                } */
    }
}
