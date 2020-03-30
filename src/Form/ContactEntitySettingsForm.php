<?php

namespace Drupal\contact_entity\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ContactEntitySettingsForm.
 *
 * @ingroup contact_entity
 */
class ContactEntitySettingsForm extends ConfigFormBase {

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
   * @inheritDoc
   */
  protected function getEditableConfigNames() {
    return [
      'contactentity.settings',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('contactentity.settings');
    $form['text'] = [
      '#markup' => 'Limited operating mode:',
    ];
    $form['login_mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Set the site into Login Only mode'),
      '#default_value' => $config->get('login_mode'),
    ];
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('contactentity.settings')
      ->set('login_mode', $form_state->getValue('login_mode'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
