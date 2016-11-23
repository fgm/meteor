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
   *
   * @see \Drupal\Core\EventSubscriber\AuthenticationSubscriber::getSubscribedEvents()
   */
  public static function getSubscribedEvents() {
    $ret = [
      // Trigger before AuthenticationSubscriber::onKernelRequestAuthenticate,
      // to prevent a redirect loop between Meteor and Drupal.
      KernelEvents::REQUEST => ['onRequest', 301],
      KernelEvents::TERMINATE => ['onTerminate'],
    ];
    return $ret;
  }

}
