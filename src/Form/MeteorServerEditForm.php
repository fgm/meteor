<?php

/**
 * @file
 * Contains Drupal\meteor\Form\MeteorServerEditForm.
 */

namespace Drupal\meteor\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Class MeteorServerEditForm.
 *
 * Provides the edit form for our Meteor server entity.
 *
 * @package Drupal\meteor\Form
 *
 * @ingroup meteor
 */
class MeteorServerEditForm extends MeteorServerFormBase {

  /**
   * Returns the actions provided by this form.
   *
   * For the edit form, we only need to change the text of the submit button.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   *
   * @return array
   *   An array of supported actions for the current entity form.
   */
  public function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    $actions['submit']['#value'] = t('Update Meteor server');
    return $actions;
  }

}
