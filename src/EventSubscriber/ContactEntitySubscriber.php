<?php

namespace Drupal\contact_entity\EventSubscriber;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Routing\RouteMatch;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class ContactEntitySubscriber
 *
 * @package Drupal\contact_entity\EventSubscriber
 */
class ContactEntitySubscriber implements EventSubscriberInterface {

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  protected $messenger;

  protected $configFactory;

  /**
   * ContactEntitySubscriber constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   */
  public function __construct(AccountInterface $account, MessengerInterface $messenger, ConfigFactory $configFactory) {
    $this->account = $account;
    $this->messenger = $messenger;
    $this->configFactory = $configFactory;
  }

  /**
   * @inheritDoc
   */
  public static function getSubscribedEvents() {
    return [
      KernelEvents::REQUEST => ['redirect'],
    ];
  }

  /**
   * Anonymous user can’t access any page except Login
   * and pages related to the password recovery;
   * Every node page, home page, etc. should not be
   * accessible for anonymous user;
   * Authenticated user can’t access any page except his profile page,
   * Contact page and My Contact Submission page;
   * Every node page, home page, etc. should not be accessible
   * for authenticated user.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   */
  public function redirect(GetResponseEvent $event) {
    $login_mode = $this->configFactory->get('contactentity.settings')->get('login_mode');
    $route_name = RouteMatch::createFromRequest($event->getRequest())->getRouteName();
    // Stop it if login mode not checked or user is administrator.
    if (empty($login_mode) || $this->account->hasPermission('administer site configuration')) {
      return;
    }
    // Route that authenticated user can access.
    $routes_authenticated = [
      'entity.user.canonical', 'entity.user.edit_form',
      'view.own_contact.page_1', 'entity.contact_entity.add_form_user_page',
      'user.logout', 'entity.contact_entity.canonical',
      'entity.contact_entity.edit_form', 'entity.contact_entity.delete_form',
    ];
    // Route that anonymous user can access.
    $routes_anonymous = [
      'user.register', 'user.login', 'user.pass', 'user.pass.http',
      'user.login', 'user.login.http',
    ];
    $name = $this->account->getAccountName();
    // Redirect to user page.
    if ($this->account->isAuthenticated() && !in_array($route_name, $routes_authenticated)) {
      $event->setResponse(new RedirectResponse(\Drupal::url('entity.user.canonical',
        ['user' => $this->account->id()], ['absolute' => TRUE]), 301, []));
      // Not working =(.
      // $this->messenger->addMessage('Sorry %name , the site operates in a restricted mode of operation...', ['%name' => $this->account->getAccountName()]);
      $this->messenger->addError('Sorry, the site operates in a restricted mode of operation...');
    }
    // Redirect to login page.
    elseif ($this->account->isAnonymous() && !in_array($route_name, $routes_anonymous)) {
      $event->setResponse(new RedirectResponse(\Drupal::url('user.login'), 301, []));
      $this->messenger->addMessage('The site operates in a restricted mode of operation...');
    }
  }

}
