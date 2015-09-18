<?php

/**
 * @file
 * Contains \Drupal\meteor\MeteorServerAccessController.
 */

namespace Drupal\meteor;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an access controller for the Meteor server entity.
 *
 * We set this to be the access controller in MeteorServer's entity annotation.
 *
 * @see \Drupal\meteor\Entity\MeteorServer
 *
 * @ingroup meteor
 */
class MeteorServerAccessController extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    // The $opereration parameter tells you what sort of operation access is
    // being checked for.
    if ($operation == 'view') {
      return TRUE;
    }
    // Other than the view operation, we're going to be insanely lax about
    // access. Don't try this at home!
    return parent::checkAccess($entity, $operation, $langcode, $account);
  }

}
