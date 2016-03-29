<?php

/**
 * @file
 * Contains \Drupal\Meteor\UserInfoEvent.
 */

namespace Drupal\meteor;

use Symfony\Component\EventDispatcher\Event;

/**
 * Represents user information as event.
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
  public function __construct($user_info) {
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
  public function setUserInfo($user_info) {
    $this->userInfo = $user_info;
  }

}
