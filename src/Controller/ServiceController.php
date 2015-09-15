<?php

/**
 * @file
 * ServiceController.php
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\meteor\Controller;


use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\ConfigManagerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Session\SessionManagerInterface;
use Drupal\Core\Url;
use Drupal\user\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

class ServiceController extends ControllerBase {

  /**
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $accountProxy;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * @var \Symfony\Component\Serializer\SerializerInterface
   */
  protected $serializer;

  /**
   * @var \Drupal\Core\Session\SessionManagerInterface
   */
  protected $sessionManager;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\Session\AccountInterface $account_proxy
   */
  public function __construct(AccountProxyInterface $account_proxy, ConfigFactoryInterface $config_factory, SerializerInterface $serializer, SessionManagerInterface $session_manager) {
    $this->accountProxy = $account_proxy;
    $this->configFactory = $config_factory;
    $this->serializer = $serializer;
    $this->sessionManager = $session_manager;
  }

  /**
   * Controller factory.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
   *
   * @return static
   */
  public static function create(ContainerInterface $container) {
    /** @var \Drupal\Core\Session\AccountProxyInterface $current_user */
    $current_user = $container->get('current_user');

    /** @var \Drupal\Core\Config\ConfigFactoryInterface $config_factory */
    $config_factory = $container->get('config.factory');

    /** @var \Symfony\Component\Serializer\SerializerInterface $serializer */
    $serializer = $container->get('serializer');

    /** @var \Drupal\Core\Session\SessionManagerInterface $session_manager */
    $session_manager = $container->get('session_manager');
    return new static($current_user, $config_factory, $serializer, $session_manager);
  }

  /**
   * Controller for meteor.overview.
   */
  public function siteInfo() {
    $result = [
      'cookieName' => $this->sessionManager->getName(),
      'anonymousName' => $this->configFactory->get('user.settings')->get('anonymous'),
    ];

    $result = $this->serializer->serialize($result, 'json');
    $response = new Response($result, Response::HTTP_OK);
    $response->headers->set('Content-type', 'application/json');
    return $response;
  }

  public function whoami() {
    $account = $this->accountProxy->getAccount();
    $uid = $account->id();
    $name = $account->getUsername();

    $roles = $this->accountProxy->getRoles();
    $result = $this->serializer->serialize([
      'uid' => $uid,
      'name' => $name,
      'roles' => $roles,
    ], 'json');

    $response = new Response($result, Response::HTTP_OK);
    $response->headers->set('Content-type', 'application/json');
    return $response;
  }

  public function backlink() {
    $ret = [
      '#markup' => \Drupal::l("Go to Meteor", Url::fromUri("http://drop8:3000/")),
    ];
    return $ret;
  }
}
