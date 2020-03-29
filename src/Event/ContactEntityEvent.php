<?php

namespace Drupal\contact_entity\Event;

use Symfony\Component\EventDispatcher\Event;

class ContactEntityEvent extends Event {

  const CONTACT_FORM = 'contact_entity_event';

  /**
   * @var
   */
  protected $variables;

  /**
   * ContactEntityEvent constructor.
   *
   * @param $variables
   */
  public function __construct($variables) {
    $this->variables = $variables;
  }

  /**
   * @return mixed
   */
  public function getVariables() {
    return $this->variables;
  }

}
