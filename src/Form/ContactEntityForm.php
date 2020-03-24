<?php

namespace Drupal\contact_entity\Form;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Component\Utility\EmailValidator;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form controller for Contact entity edit forms.
 *
 * @ingroup contact_entity
 */
class ContactEntityForm extends ContentEntityForm {

  /**
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * @var \Drupal\Component\Utility\EmailValidator
   */
  protected $emailValidator;

  /**
   * ContactEntityForm constructor.
   *
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   * @param \Drupal\Core\Session\AccountInterface $account
   * @param \Drupal\Component\Utility\EmailValidator $email_validator
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface|NULL $entity_type_bundle_info
   * @param \Drupal\Component\Datetime\TimeInterface|NULL $time
   */
  public function __construct(EntityRepositoryInterface $entity_repository, AccountInterface $account, EmailValidator $email_validator, EntityTypeBundleInfoInterface $entity_type_bundle_info = NULL, TimeInterface $time = NULL) {
    parent::__construct($entity_repository, $entity_type_bundle_info, $time);
    $this->account = $account;
    $this->emailValidator = $email_validator;
  }

  /**
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return \Drupal\Core\Entity\ContentEntityForm|\Drupal\Core\Entity\EntityForm|static
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.repository'),
      $container->get('current_user'),
      $container->get('email.validator'),
      $container->get('entity_type.bundle.info'),
      $container->get('datetime.time')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var \Drupal\contact_entity\Entity\ContactEntity $entity */
    $form = parent::buildForm($form, $form_state);
    /*$vid = 'tags';
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($vid);
    foreach ($terms as $term) {
      $options[$term->tid] = $term->tid;
    }
    $form['speciality'] = [
      '#type' => 'select',
      '#title' => $this->t('Submission category'),
      '#options' => $options,
    ];
    $form['telephone']['widget'][0]['value']['#type'] = 'tel';*/
    // Check Authentication.
    if ($this->account->isAuthenticated()) {
      $form['email']['#attributes']['hidden'] = TRUE;
    }
    else {
      $form['user_id']['#attributes']['hidden'] = TRUE;
    }
    // Add validation for email.
    // $form['#validate'][] = '::validateEmail';
    return $form;
  }

  /**
   * Check user email.
   */
  public function validateEmail(array &$form, FormStateInterface $form_state) {
    if (!$form_state->isValueEmpty('email') && !$this->emailValidator->isValid($form_state->getValue('email'))) {
      $form_state->setErrorByName('email', t('The email address %mail is not valid.', ['%mail' => $form_state->getValue('email')]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = parent::save($form, $form_state);
    // Redirect after save in DB.
    $form_state->setRedirect('entity.contact_entity.canonical', ['contact_entity' => $entity->id()]);
  }

}
