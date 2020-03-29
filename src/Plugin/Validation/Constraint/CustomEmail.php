<?php

namespace Drupal\contact_entity\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;

/**
 * The Class need for validation via constraint.
 *
 * @Constraint(
 *   id = "CustomEmail",
 *   label = @Translation("Simple check email", context = "Validation"),
 *   type = "string"
 * )
 */
class CustomEmail extends Constraint {
  /**
   * Error message.
   *
   * @var string
   */
  public $notCorrect = '%value is not string or empty';
  /**
   * @var string
   */
  public $notEmail = '%value is not Email';
  /**
   * @var string
   */
  public $notGmail = '%value is not Gmail';
}
