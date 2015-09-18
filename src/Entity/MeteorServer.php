<?php

/**
 * @file
 * Contains Drupal\meteor\Entity\MeteorServer.
 *
 * This contains our entity class.
 *
 * Originally based on code from blog post at
 * http://previousnext.com.au/blog/understanding-drupal-8s-config-entities
 */

namespace Drupal\meteor\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the Meteor server entity.
 *
 * The lines below, starting with '@ConfigEntityType,' are a plugin annotation.
 * These define the entity type to the entity type manager.
 *
 * The properties in the annotation are as follows:
 *  - id: The machine name of the entity type.
 *  - label: The human-readable label of the entity type. We pass this through
 *    the "@Translation" wrapper so that the multilingual system may
 *    translate it in the user interface.
 *  - handlers: An array of entity handler classes, keyed by handler type.
 *    - access: The class that is used for access checks.
 *    - list_builder: The class that provides listings of the entity.
 *    - form: An array of entity form classes keyed by their operation.
 *  - entity_keys: Specifies the class properties in which unique keys are
 *    stored for this entity type. Unique keys are properties which you know
 *    will be unique, and which the entity manager can use as unique in database
 *    queries.
 *  - links: entity URL definitions. These are mostly used for Field UI.
 *    Arbitrary keys can set here. For example, User sets cancel-form, while
 *    Node uses delete-form.
 *
 * @see http://previousnext.com.au/blog/understanding-drupal-8s-config-entities
 * @see annotation
 * @see Drupal\Core\Annotation\Translation
 *
 * @ingroup meteor
 *
 * @ConfigEntityType(
 *   id = "meteor_server",
 *   label = @Translation("Meteor Server"),
 *   admin_permission = "administer Meteor servers",
 *   handlers = {
 *     "access" = "Drupal\meteor\MeteorServerAccessController",
 *     "list_builder" = "Drupal\meteor\Controller\MeteorServerListBuilder",
 *     "form" = {
 *       "add" = "Drupal\meteor\Form\MeteorServerAddForm",
 *       "edit" = "Drupal\meteor\Form\MeteorServerEditForm",
 *       "delete" = "Drupal\meteor\Form\MeteorServerDeleteForm"
 *     }
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "edit-form" = "/meteor/manage/{meteor_server}",
 *     "delete-form" = "/meteor/manage/{meteor_server}/delete"
 *   }
 * )
 */
class MeteorServer extends ConfigEntityBase {

  /**
   * The Meteor Server ID.
   *
   * @var string
   */
  public $id;

  /**
   * The Meteor Server UUID.
   *
   * @var string
   */
  public $uuid;

  /**
   * The Meteor Server label.
   *
   * @var string
   */
  public $label;

  /**
   * The Meteor Server application key, for access control.
   *
   * @var string
   */
  public $appKey;

  /**
   * Getter for appKey.
   *
   * @return string
   */
  public function getAppKey() {
    return $this->appKey;
  }

}
