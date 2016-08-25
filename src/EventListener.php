<?php
/**
 * @file
 * Contains \Drupal\meteor\EventListener.
 *
 * @author: Frédéric G. MARAND <fgm@osinet.fr>
 *
 * @copyright (c) 2016 Ouest Systèmes Informatiques (OSInet).
 *
 * @license General Public License version 2 or later
 */

namespace Drupal\meteor;


use Drupal\Core\Entity\EntityTypeEvent;
use Drupal\Core\Entity\EntityTypeEvents;
use Drupal\Core\Field\FieldStorageDefinitionEvent;
use Drupal\Core\Field\FieldStorageDefinitionEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class EventListener listens to entity type and field storage update events.
 *
 * Its goal is to force a login refresh on any of these events: it seems too
 * complex to analyze the changes before deciding, considering how rare such
 * events are expected to be anyway, mostly occurring during deployments.
 *
 * To avoid multiple refreshes, it only accumulates events, triggering the
 * actual refresh on destruction only if at least one update event occurred.
 */
class EventListener implements EventSubscriberInterface {
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
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[EntityTypeEvents::UPDATE][] = ["onEntityTypeUpdate", 100];
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
    var_dump($event_name);
    ksm(__METHOD__, get_defined_vars());
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
    var_dump($event_name);
    ksm(__METHOD__, get_defined_vars());
    $this->fsduCount++;
  }

  /**
   * Destructor: send a refresh request if needed.
   */
  public function __destruct() {
    var_dump(__METHOD__);
    error_log($this->etuCount . " " . $this->fsduCount . "\n", 3, "/tmp/php_errors.log");
    if ($this->etuCount + $this->fsduCount > 0) {
      meteor_notify();
    }
  }

}
