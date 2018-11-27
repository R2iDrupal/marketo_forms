<?php

/**
 * @file
 * Contains Drupal\marketo_forms\Plugin\Field\FieldType\MarketoFormItem.
 */

namespace Drupal\marketo_forms\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;

/**
 * Plugin implementation of the 'field_marketo_form' field type.
 *
 * @FieldType(
 *   id = "field_marketo_form",
 *   label = @Translation("Marketo Form"),
 *   module = "marketo_forms",
 *   description = @Translation("Display Marketo form."),
 *   default_widget = "field_marketo_select",
 *   default_formatter = "field_marketo_form_formatter",
 *   category = "Marketo"
 * )
 */
class MarketoFormItem extends FieldItemBase {
  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [
      'columns' => [
        'form_id' => [
          'type'     => 'varchar',
          'length'   => 255,
          'not null' => TRUE,
          'default'  => '',
        ],
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function isEmpty() {
    $value = $this->get('form_id')->getValue();
    return $value === NULL || $value === '';
  }

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    $properties['form_id'] = DataDefinition::create('string')
      ->setLabel(t('Marketo form ID'));

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function getConstraints() {
    $constraints = parent::getConstraints();

    $constraint_manager = \Drupal::typedDataManager()->getValidationConstraintManager();
    $constraints[] = $constraint_manager->create('ComplexData', [
      'form_id' => [
        'Regex' => [
          'pattern' => '/^[A-Za-z0-9]+[A-Za-z0-9\\-_]*$/xi',
          'message' => t('%name: can only contain letters, digits ([0-9]), hyphens ("-"), and underscores ("_"). Must begin with letter or digit.', ['%name' => $this->getFieldDefinition()->getLabel()]),
        ],
      ],
    ]);

    return $constraints;
  }
}
