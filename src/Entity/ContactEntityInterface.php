<?php

namespace Drupal\contact_entity\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Contact entity entities.
 *
 * @ingroup contact_entity
 */
interface ContactEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Gets the Contact entity creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Contact entity.
   */
  public function getCreatedTime();

  /**
   * Sets the Contact entity creation timestamp.
   *
   * @param int $timestamp
   *   The Contact entity creation timestamp.
   *
   * @return \Drupal\contact_entity\Entity\ContactEntityInterface
   *   The called Contact entity entity.
   */
  public function setCreatedTime($timestamp);

}
