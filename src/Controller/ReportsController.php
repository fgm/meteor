<?php

/**
 * @file
 * ReportsController.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\meteor\Controller;


use Drupal\Core\Controller\ControllerBase;

/**
 * Class ReportsController.
 *
 * @package Drupal\meteor\Controller
 */
class ReportsController extends ControllerBase {

  /**
   * Controller for meteor.overview.
   */
  public function overview() {
    $ret = [
      '#markup' => t('Overview R'),
    ];
    return $ret;
  }

}
