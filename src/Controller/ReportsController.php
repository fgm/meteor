<?php

namespace Drupal\meteor\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class ReportsController.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class ReportsController extends ControllerBase {

  /**
   * Controller for meteor.overview.
   */
  public function overview() {
    $ret = [
      '#markup' => t('Overview'),
    ];
    return $ret;
  }

}
