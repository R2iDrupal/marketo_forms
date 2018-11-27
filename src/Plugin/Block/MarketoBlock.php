<?php

/**
 * @file
 * Contains \Drupal\marketo_forms\Plugin\Block\MarketoBlock.
 */

namespace Drupal\marketo_forms\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\marketo_forms\MarketoFormsCore;

/**
 * Display Marketo Form.
 *
 * @Block(
 *   id = "marketo_forms",
 *   admin_label = @Translation("Marketo form")
 * )
 */
class MarketoBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'form_id' => '',
      'description' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['description'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Description'),
      '#description'   => $this->t('Optional descriptive text to explain what this form is about.'),
      '#default_value' => $this->configuration['description'],
      '#maxlength'     => 255,
      '#required'      => FALSE,
    ];
    $form['form_id'] = [
      '#type'          => 'textfield',
      '#title'         => $this->t('Marketo Form'),
      '#description'   => $this->t('The Marketo form ID (Usually in the format XXXX).'),
      '#default_value' => $this->configuration['form_id'],
      '#required'      => TRUE,
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    parent::blockValidate($form, $form_state);

    // Assert the form ID is valid
    if (!preg_match('/^[A-Za-z0-9]+[A-Za-z0-9\\-_]*$/xi', $form_state->getValue('form_id'))) {
      $form_state->setErrorByName('form_id', $this->t('%form_id is not a valid Marketo form ID.', ['%form_id' => $form_state->getValue('form_id')]));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['description'] = trim($form_state->getValue('description'));
    $this->configuration['form_id'] = trim($form_state->getValue('form_id'));
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = \Drupal::config('marketo_forms.settings');
    $description = trim($this->configuration['description']);
    $form_id = trim($this->configuration['form_id']);
    $host = trim($config->get('marketo_host_key'));
    $api_key = trim($config->get('marketo_api_key'));
    $langcode = \Drupal::languageManager()->getCurrentLanguage()->getId();
    return [
      '#theme'       => 'marketo_form',
      '#host'        => $host,
      '#api_key'     => $api_key,
      '#form_id'     => $form_id,
      '#description' => $description,
      '#locale'      => $langcode,
      '#cache' => array(
        'tags' => ['marketo_form:' . marketo_forms_safe_form_id($form_id)] // https://www.drupal.org/developing/api/8/cache/tags
      ),
    ];
  }

}
