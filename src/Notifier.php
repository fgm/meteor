<?php

namespace Drupal\meteor;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Session\AccountInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Notifier sends notifications to the Meteor instance.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2016 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */
class Notifier {
  const PATH = 'drupalUserEvent';

  /**
   * The http_client service.
   *
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * The immutable module configuration.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
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
   *   The immutable module configuration.
   * @param \Psr\Log\LoggerInterface $logger
   *   The module logger channel.
   */
  public function __construct(Client $client, ImmutableConfig $config, LoggerInterface $logger) {
    $this->client = $client;
    $this->config = $config;
    $this->logger = $logger;
  }

  /**
   * Send a notification to a Meteor server instance.
   *
   * @param string $event
   *   The name of the event to notify the instance about.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Optional. The account triggering the event.
   * @param int $usDelay
   *   Optional. The delay after which to act on the notification.
   */
  public function notify($event, AccountInterface $account = NULL, $usDelay = 0) {
    $query = [
      'event' => strval($event),
    ];

    $userId = $account instanceof AccountInterface ? intval($account->id()) : NULL;
    if (isset($userId)) {
      $query['userId'] = $userId;
    }

    if (isset($usDelay)) {
      $query['delay'] = abs(intval($usDelay));
    }

    $host = $this->config->get('meteor_server');
    $url = $host . '/' . static::PATH;
    $promise = $this->client->getAsync($url, ['query' => $query]);
    $this->logger->info("Notify meteor on @url with query @query.", [
      '@url' => $url,
      '@query' => $query ? var_export($query, TRUE) : '(empty)',
    ]);
    drupal_register_shutdown_function(function () use ($promise) {
      $this->finalizeWait($promise);
    });

  }

  /**
   * Shutdown function to perform a deferred HTTP request.
   *
   * @param \GuzzleHttp\Promise\PromiseInterface $promise
   *   The promise to fulfill.
   */
  public function finalizeWait(PromiseInterface $promise) {
    try {
      /* @var \GuzzleHttp\Psr7\Response $result */
      $result = $promise->wait(TRUE);
      $status = $result->getStatusCode();
      $body = $result->getBody();
      $this->logger->info("Notified got @status: @body", [
        '@status' => $status,
        '@body' => strval($body),
      ]);
    }
    catch (ConnectException $e) {
      watchdog_exception('meteor', $e);
    }
  }

}
