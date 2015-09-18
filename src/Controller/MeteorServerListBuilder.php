<?php
/**
 * @file
 * Contains Drupal\meteor\Controller\MeteorServerListBuilder.
 */

namespace Drupal\meteor\Controller;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of Meteor server entities.
 *
 * List Controllers provide a list of entities in a tabular form. The base
 * class provides most of the rendering logic for us. The key functions
 * we need to override are buildHeader() and buildRow(). These control what
 * columns are displayed in the table, and how each row is displayed
 * respectively.
 *
 * Drupal locates the list controller by looking for the "list" entry under
 * "controllers" in our entity type's annotation. We define the path on which
 * the list may be accessed in our module's *.routing.yml file. The key entry
 * to look for is "_entity_list". In *.routing.yml, "_entity_list" specifies
 * an entity type ID. When a user navigates to the URL for that router item,
 * Drupal loads the annotation for that entity type. It looks for the "list"
 * entry under "controllers" for the class to load.
 *
 * @package Drupal\meteor\Controller
 *
 * @ingroup meteor
 */
class MeteorServerListBuilder extends ConfigEntityListBuilder {

  /**
   * Builds the header row for the entity listing.
   *
   * @return array
   *   A render array structure of header strings.
   *
   * @see Drupal\Core\Entity\EntityListController::render()
   */
  public function buildHeader() {
    $header['label'] = $this->t('Server');
    $header['machine_name'] = $this->t('Machine Name');
    $header['app_key'] = $this->t('Application key');
    return $header + parent::buildHeader();
  }

  /**
   * Builds a row for an entity in the entity listing.
   *
   * @param EntityInterface $entity
   *   The entity for which to build the row.
   *
   * @return array
   *   A render array of the table row for displaying the entity.
   *
   * @see Drupal\Core\Entity\EntityListController::render()
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $this->getLabel($entity);
    $row['machine_name'] = $entity->id();
    $row['app_key'] = $entity->getAppKey();

    return $row + parent::buildRow($entity);
  }

  /**
   * Adds some descriptive text to our entity list.
   *
   * Typically, there's no need to override render(). You may wish to do so,
   * however, if you want to add markup before or after the table.
   *
   * @return array
   *   Renderable array.
   */
  public function render() {
    $build['description'] = array(
      '#markup' => $this->t("<p>The Meteor module defines a Meteor Server"
        . " entity type. This is a list of the Meteor Server entities currently"
        . " in your Drupal site.</p><p>By default, when you enable this"
        . " module, one entity is created from configuration. This is why we"
        . " call them Config Entities. Marvin, the paranoid server, is created"
        . " in the database when the module is enabled.</p><p>You can view a"
        . " list of Meteor Server here. You can also use the 'Operations' column to"
        . " edit and delete Meteor Servers.</p>"),
    );
    $build[] = parent::render();
    return $build;
  }

}
