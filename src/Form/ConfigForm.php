<?php

/**
 * @file
 * Contains Drupal\meteor\Form\ConfigForm.
 */

namespace Drupal\meteor\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ConfigForm.
 *
 * @package Drupal\meteor\Form
 */
class ConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'meteor.settings'
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'meteor_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('meteor.settings');
    $form['meteor_server'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Meteor server URL'),
      '#description' => $this->t('The server base URL, without a terminating slash.'),
      '#maxlength' => 255,
      '#size' => 60,
      '#default_value' => $config->get('meteor_server'),
    );

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('meteor.settings')
      ->set('meteor_server', $form_state->getValue('meteor_server'))
      ->save();
  }

}
