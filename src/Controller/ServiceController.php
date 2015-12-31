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
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

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
   * Constructor.
   *
   * @param \Drupal\Core\Session\AccountProxyInterface $account_proxy
   *   The account proxy for the current user.
   * @param \Drupal\Core\Config\ImmutableConfig $user_settings
   *   The user.settings configuration object.
   * @param \Drupal\Core\Config\ImmutableConfig $meteor_settings
   *   The meteor.settings configuration object.
   * @param \Symfony\Component\Serializer\SerializerInterface $serializer
   *   The serializer service.
   * @param \Drupal\Core\Session\SessionManagerInterface $session_manager
   *   The session manager service.
   */
  public function __construct(AccountProxyInterface $account_proxy, ImmutableConfig $user_settings, ImmutableConfig $meteor_settings, SerializerInterface $serializer, SessionManagerInterface $session_manager) {
    $this->accountProxy = $account_proxy;
    $this->userSettings = $user_settings;
    $this->meteorSettings = $meteor_settings;
    $this->serializer = $serializer;
    $this->sessionManager = $session_manager;
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

    /** @var \Symfony\Component\Serializer\SerializerInterface $serializer */
    $serializer = $container->get('serializer');

    /** @var \Drupal\Core\Session\SessionManagerInterface $session_manager */
    $session_manager = $container->get('session_manager');

    return new static($current_user, $user_settings, $meteor_settings, $serializer, $session_manager);
  }

  /**
   * Controller for meteor.overview.
   */
  public function siteInfo() {
    $result = [
      'cookieName' => $this->sessionManager->getName(),
      'anonymousName' => $this->userSettings->get('anonymous'),
    ];

    $result = $this->serializer->serialize($result, 'json');
    $response = new Response($result, Response::HTTP_OK);
    $response->headers->set('Content-type', 'application/json');
    return $response;
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
    $result = $this->serializer->serialize([
      'uid' => $uid,
      'name' => $name,
      'displayName' => $display_name,
      'roles' => $roles,
    ], 'json');

    $response = new Response($result, Response::HTTP_OK);
    $response->headers->set('Content-type', 'application/json');
    return $response;
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
