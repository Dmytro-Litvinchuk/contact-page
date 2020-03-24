<?php

namespace Drupal\contact_entity\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Contact entity entity.
 *
 * @ingroup contact_entity
 *
 * @ContentEntityType(
 *   id = "contact_entity",
 *   label = @Translation("Contact entity"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\contact_entity\ContactEntityListBuilder",
 *     "views_data" = "Drupal\contact_entity\Entity\ContactEntityViewsData",
 *     "translation" = "Drupal\contact_entity\ContactEntityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\contact_entity\Form\ContactEntityForm",
 *       "add" = "Drupal\contact_entity\Form\ContactEntityForm",
 *       "edit" = "Drupal\contact_entity\Form\ContactEntityForm",
 *       "delete" = "Drupal\contact_entity\Form\ContactEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\contact_entity\ContactEntityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\contact_entity\ContactEntityAccessControlHandler",
 *   },
 *   base_table = "contact_entity",
 *   data_table = "contact_entity_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer contact entity entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "email",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/contact_entity/{contact_entity}",
 *     "add-form" = "/admin/structure/contact_entity/add",
 *     "edit-form" = "/admin/structure/contact_entity/{contact_entity}/edit",
 *     "delete-form" = "/admin/structure/contact_entity/{contact_entity}/delete",
 *     "collection" = "/admin/structure/contact_entity",
 *   },
 *   field_ui_base_route = "contact_entity.settings"
 * )
 */
class ContactEntity extends ContentEntityBase implements ContactEntityInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * @inheritDoc
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
      'email' => \Drupal::currentUser()->getEmail(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['email'] = BaseFieldDefinition::create('email')
      ->setLabel(t('Email Address'))
      ->setDescription(t('The Email Address.'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 100,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'email_default',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['telephone'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Telephone Number'))
      ->setDescription(t('The Telephone Number.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'telephone_default',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['message'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Message'))
      ->setDescription(t('Your message'))
      ->setSettings([
        'default_value' => '',
        'max_length' => 1000,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['submission_category'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Submission category'))
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler_settings', [
        'target_bundles' => ['tags' => 'tags'],
      ])
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 3,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '10',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Contact entity is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User Name'))
      ->setDescription(t('The Name of the associated user.'))
      ->setReadOnly(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'entity_reference',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => 60,
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
