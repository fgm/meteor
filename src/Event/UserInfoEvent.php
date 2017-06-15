<?php

namespace Drupal\meteor\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Represents user information as event.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015-2017 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class UserInfoEvent extends Event {

  /**
   * The user information.
   *
   * @var array
   */
  protected $userInfo;

  /**
   * Constructs a UserInfoEvent object.
   *
   * @param array $user_info
   *   The user informations.
   */
  public function __construct(array $user_info) {
    $this->setUserInfo($user_info);
  }

  /**
   * Gets the user informations.
   */
  public function getUserInfo() {
    return $this->userInfo;
  }

  /**
   * Sets the user informations.
   *
   * @param array $user_info
   *   The user informations.
   */
  public function setUserInfo(array $user_info) {
    $this->userInfo = $user_info;
  }

}
