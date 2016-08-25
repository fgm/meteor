<?php

/**
 * @file
 * Contains \Drupal\meteor\Notifier.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2016 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\meteor;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Config\ImmutableConfig;
use GuzzleHttp\Client;
use Psr\Log\LoggerInterface;

/**
 * Class Notifier sends notifications to the Meteor instance.
 */
class Notifier {

  /**
   * The http_client service.
   *
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * The immutable module configuration.
   *
   * @var \Drupal\Core\Config\Config|\Drupal\Core\Config\ImmutableConfig
   */
  protected $config;

  /**
   * The module logger channel.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Notifier constructor.
   *
   * @param \GuzzleHttp\Client $client
   *   The http_client service.
   * @param \Drupal\Core\Config\ImmutableConfig $config
   * The immutable module configuration.
   * @param \Psr\Log\LoggerInterface $logger
   *   The module logger channel.
   */
  public function __construct(Client $client, ImmutableConfig $config, LoggerInterface $logger) {
    $this->client = $client;
    $this->config = $config;
    $this->logger = $logger;
  }
}
