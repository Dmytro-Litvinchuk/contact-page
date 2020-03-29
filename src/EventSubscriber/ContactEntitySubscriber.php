<?php

namespace Drupal\contact_entity\EventSubscriber;

use Drupal\contact_entity\Event\ContactEntityEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ContactEntitySubscriber
 *
 * @package Drupal\contact_entity\EventSubscriber
 */
class ContactEntitySubscriber implements EventSubscriberInterface {

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['someH'],
    ];
  }

  public function someH(GetResponseEvent $event) {
    $messenger = \Drupal::service('messenger');
    $messenger->addMessage('$vasa');
  }

}
