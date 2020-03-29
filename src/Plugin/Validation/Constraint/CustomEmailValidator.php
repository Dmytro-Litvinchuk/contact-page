<?php

namespace Drupal\contact_entity\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Custom validator for email.
 *
 * @package Drupal\contact_entity\Plugin\Validation\Constraint
 */
class CustomEmailValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {
    foreach ($items as $item) {
      // Trim value.
      $value = trim($item->value);
      // Check empty or not string.
      if (empty($value) || !is_string($value)) {
        $this->context->addViolation($constraint->notCorrect, ['%value' => $item->value]);
      }
      // Validate email.
      if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
        $this->context->addViolation($constraint->notEmail, ['%value' => $value]);
      }
      // Check only gmail domain.
      if (strpos($value, '@gmail.com') === FALSE) {
        $this->context->addViolation($constraint->notGmail, ['%value' => $value]);
      }
    }
  }

}
