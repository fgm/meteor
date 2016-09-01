<?php

namespace Drupal\meteor;

use Drupal\Core\Entity\EntityTypeEvent;
use Drupal\Core\Entity\EntityTypeEvents;
use Drupal\Core\Field\FieldStorageDefinitionEvent;
use Drupal\Core\Field\FieldStorageDefinitionEvents;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EventListener listens to entity type and field storage update events.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2015 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 *
 * Its goal is to force a login refresh on any of these events: it seems too
 * complex to analyze the changes before deciding, considering how rare such
 * events are expected to be anyway, mostly occurring during deployments.
 *
 * To avoid multiple refreshes, it only accumulates events, triggering the
 * actual refresh on destruction only if at least one update event occurred.
 */
class IdentityListener implements EventSubscriberInterface {
  const ENTITY_FIELD_UPDATE = 'entity_field_update';
  const FIELD_DELETE = 'field_delete';
  const FIELD_INSERT = 'field_insert';
  const FIELD_UPDATE = 'field_update';
  const USER_DELETE = 'delete';
  const USER_LOGIN = 'login';
  const USER_LOGOUT = 'logout';
  const USER_UPDATE = 'update';

  /**
   * The number of entity type update events received.
   *
   * @var int
   */
  public $etuCount = 0;

  /**
   * The number of field storage definition events received.
   *
   * @var int
   */
  public $fsduCount = 0;

  /**
   * The logger.channel.meteor channel.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * IdentityListener constructor.
   *
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger.channel.meteor channel.
   */
  public function __construct(LoggerInterface $logger) {
    $this->logger = $logger;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[EntityTypeEvents::CREATE][] = ["onEntityTypeUpdate", 100];
    $events[EntityTypeEvents::DELETE][] = ["onEntityTypeUpdate", 100];
    $events[EntityTypeEvents::UPDATE][] = ["onEntityTypeUpdate", 100];
    $events[FieldStorageDefinitionEvents::CREATE] = ["onFieldStorageDefinitionUpdate", 100];
    $events[FieldStorageDefinitionEvents::DELETE] = ["onFieldStorageDefinitionUpdate", 100];
    $events[FieldStorageDefinitionEvents::UPDATE] = ["onFieldStorageDefinitionUpdate", 100];
    return $events;
  }

  /**
   * Handler for EntityTypeEvents::UPDATE.
   *
   * @param EntityTypeEvent $event
   *   The actual update event.
   * @param string $event_name
   *   The name of the event.
   */
  public function onEntityTypeUpdate(EntityTypeEvent $event, $event_name) {
    $this->etuCount++;
  }

  /**
   * Handler for FieldStorageDefinitionEvents::UPDATE.
   *
   * @param FieldStorageDefinitionEvent $event
   *   The actual update event.
   * @param string $event_name
   *   The name of the event.
   */
  public function onFieldStorageDefinitionUpdate(FieldStorageDefinitionEvent $event, $event_name) {
    $this->fsduCount++;
  }

  /**
   * Destructor: send a refresh request if needed.
   */
  public function __destruct() {
    if ($this->etuCount + $this->fsduCount > 0) {
      $this->logger->debug("Entity type updates: @etu, Field storage updates: @fsdu.", [
        '@etu' => $this->etuCount,
        '@fsdu' => $this->fsduCount,
      ]);

      _meteor_notify(IdentityListener::ENTITY_FIELD_UPDATE);
    }
  }

}
