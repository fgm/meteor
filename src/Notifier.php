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
   * @param string $path
   *   The path reached in meteor.
   * @param array $query
   *   Optional query params.
   */
  public function notify($path, $query = []) {
    $host = $this->config->get('meteor_server');
    $url = rtrim($host, '/') . '/' . ltrim($path, '/');
    if (!$this->isUrlValid($url)) {
      $this->logger->error("Can't notify meteor on @url (invalid url).", [
        '@url' => $url,
      ]);
      return;
    }
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
   * Meteor url validation.
   *
   * From https://gist.github.com/dperini/729294
   *
   * @param string  $url
   *   The url to validate.
   * @return bool
   *   If the url is valide or not.
   */
  public function isUrlValid($url) {
    $valid_url_regex = '_^' .
      // protocol identifier
      '(?:https?://)' .
      // user:pass authentication
      '(?:\S+(?::\S*)?@)?' .
      '(?:' .
        // IP address exclusion
        // private & local networks
        '(?!(?:10|127)(?:\.\d{1,3}){3})' .
        '(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})' .
        '(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})' .
        // IP address dotted notation octets
        // excludes loopback network 0.0.0.0
        // excludes reserved space >= 224.0.0.0
        // excludes network & broacast addresses
        // (first & last IP address of each class)
        '(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])' .
        '(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}' .
        '(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))' .
      '|' .
        // host name
        '(?:(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)' .
        // domain name
        '(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)*' .
        // TLD identifier
        '(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,}))' .
      ')' .
      // port number
      '(?::\d{2,5})?' .
      // resource path (we prevent query params and anchors)
      '(?:/[^\s\?#]*)?' .
      '$_iuS';

    return preg_match($valid_url_regex, $url) ? TRUE : FALSE;
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
