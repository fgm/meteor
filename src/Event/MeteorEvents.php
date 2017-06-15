<?php

namespace Drupal\meteor\Event;

/**
 * Class MeteorEvents listrs the names of the events emitted by the package.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2017 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 *
 * @see \Drupal\meteor\Event\UserInfoEvent
 */
class MeteorEvents {

  /**
   * Name of the event fired when generating a "whoami" information set.
   *
   * The event listener method receives a \Drupal\meteor\Event\UserInfoEvent
   * instance.
   *
   * @Event
   *
   * @see \Drupal\meteor\Event\UserInfoEvent
   *
   * @var string
   */
  const USER_INFO = 'meteor.user_info';

}
