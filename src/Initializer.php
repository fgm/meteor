<?php

namespace Drupal\meteor;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class Initializer forces session cookies not to be HTTP Only.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class Initializer implements EventSubscriberInterface {
  const VAR_NAME = 'session.cookie_httponly';

  protected $savedHttpOnly;

  /**
   * Event handler for Request.
   */
  public function onRequest() {
    $this->savedHttpOnly = ini_get(static::VAR_NAME);
    ini_set(static::VAR_NAME, 0);
  }

  /**
   * Event handler for Terminate.
   */
  public function onTerminate() {
    ini_set(static::VAR_NAME, $this->savedHttpOnly);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $ret = [
      KernelEvents::REQUEST => ['onRequest'],
      KernelEvents::TERMINATE => ['onTerminate'],
    ];
    return $ret;
  }

}
