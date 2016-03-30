<?php

/**
 * @file
 * Contains \Drupal\meteor\Controller\ServiceController.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\meteor\Controller;


use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\Url;
use Drupal\meteor\UserInfoEvent;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ServiceController is a web service controller.
 */
class ServiceController extends ControllerBase {

  /**
   * The account proxy for the current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $accountProxy;

  /**
   * The meteor.settings configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $meteorSettings;

  /**
   * The user.settings configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected $userSettings;

  /**
   * The serializer service.
   *
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * The session manager service.
   *
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  protected $sessionManager;

  /**
   * The event dispatcher service.
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account_proxy
   *   The account proxy for the current user.
   * @param \Drupal\Core\Config\ImmutableConfig $user_settings
   *   The user.settings configuration object.
   * @param \Drupal\Core\Config\ImmutableConfig $meteor_settings
   *   The meteor.settings configuration object.
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   *   The session manager service.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher service.
   */
  public function __construct(AccountProxyInterface $account_proxy,
    ImmutableConfig $user_settings, ImmutableConfig $meteor_settings,
    SessionManagerInterface $session_manager, EventDispatcherInterface $event_dispatcher
  ) {
    $this->accountProxy = $account_proxy;
    $this->userSettings = $user_settings;
    $this->meteorSettings = $meteor_settings;
    $this->sessionManager = $session_manager;
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * Controller factory.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *   The DIC.
   *
   * @return static
   *   The created controller object instance.
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Session\AccountProxyInterface $current_user */
    $current_user = $container->get('current_user');

    /** @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $container->get('config.factory');

    $user_settings = $config_factory->get('user.settings');

    $meteor_settings = $config_factory->get('meteor.settings');

    /** @var \Drupal\Core\Session\SessionManagerInterface $session_manager */
    $session_manager = $container->get('session_manager');

    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher */
    $event_dispatcher = $container->get('event_dispatcher');

    return new static($current_user, $user_settings, $meteor_settings, $session_manager, $event_dispatcher);
  }

  /**
   * Controller for meteor.overview.
   */
  public function siteInfo() {
    $result = [
      'cookieName' => $this->sessionManager->getName(),
      'anonymousName' => $this->userSettings->get('anonymous'),
    ];

    return new JsonResponse($result);
  }

  /**
   * The controller for the meteor.whoami route.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   A JSON response.
   */
  public function whoami() {
    $account = $this->accountProxy->getAccount();
    $uid = $account->id();
    $name = $account->getAccountName();
    $display_name = $account->getDisplayName();
    $roles = $this->accountProxy->getRoles();

    $account_data = [
      'uid' => $uid,
      'name' => $name,
      'displayName' => $display_name,
      'roles' => $roles,
    ];

    $e = new UserInfoEvent($account_data);
    /** @var \Drupal\meteor\UserInfoEvent $event */
    $event = $this->eventDispatcher->dispatch('meteor.user_info', $e);
    $new_account_data = $event->getUserInfo();
    return new JsonResponse($new_account_data);
  }

  /**
   * The controller for the meteor.backlink route.
   *
   * @return array
   *   The render array for the Meteor backlink.
   */
  public function backlink() {
    $server_uri = Url::fromUri($this->meteorSettings->get('meteor_server'));
    $ret = Link::fromTextAndUrl("Go to Meteor", $server_uri)->toRenderable();
    return $ret;
  }

}
