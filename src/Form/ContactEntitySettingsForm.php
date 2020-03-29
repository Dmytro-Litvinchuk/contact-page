<?php

namespace Drupal\contact_entity\Form;

use Drupal\contact_entity\Event\ContactEntityEvent;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ContactEntitySettingsForm.
 *
 * @ingroup contact_entity
 */
class ContactEntitySettingsForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'contactentity_settings';
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $dispatcher = \Drupal::service('event_dispatcher');
    $variables = $form_state->getValue('message');
    $event = new ContactEntityEvent($variables);
    $dispatcher->dispatch(ContactEntityEvent::CONTACT_FORM, $event);
  }

  /**
   * Defines the settings form for Contact entity entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['contactentity_settings']['#markup'] = 'Settings form for Contact entity entities. Manage field settings here.';
    $form['message'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Message'),
    ];

    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];
    return $form;
  }

}
