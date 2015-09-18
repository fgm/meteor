<?php

/**
 * @file
 * Test case for testing the meteor module.
 */

namespace Drupal\meteor\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Test the Meteor module.
 *
 * @group meteor
 * @group meteor
 *
 * @ingroup meteor
 */
class MeteorTest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array('meteor');

  /**
   * The installation profile to use with this test.
   *
   * We need the 'minimal' profile in order to make sure the Tool block is
   * available.
   *
   * @var string
   */
  protected $profile = 'minimal';

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name' => 'Meteor functional test',
      'description' => 'Test the Meteor module.',
      'group' => 'Meteor',
    );
  }

  /**
   * Various functional test of the Config Entity Example module.
   *
   * 1) Verify that the Marvin entity was created when the module was installed.
   *
   * 2) Verify that permissions are applied to the various defined paths.
   *
   * 3) Verify that we can manage entities through the user interface.
   *
   * 4) Verify that the entity we add can be re-edited.
   */
  public function testConfigEntityExample() {
    // 1) Verify that the Marvin entity was created when the module was
    // installed.
    $entity = entity_load('meteor_server', 'marvin');
    $this->assertNotNull($entity, 'Marvin was created during installation.');

    // 2) Verify that permissions are applied to the various defined paths.
    // Define some paths. Since the Marvin entity is defined, we can use it
    // in our management paths.
    $forbidden_paths = array(
      '/meteor',
      '/meteor/add',
      '/meteor/manage/marvin',
      '/meteor/manage/marvin/delete',
    );
    // Check each of the paths to make sure we don't have access. At this point
    // we haven't logged in any users, so the client is anonymous.
    foreach ($forbidden_paths as $path) {
      $this->drupalGet($path);
      $this->assertResponse(403, "Access denied to anonymous for path: $path");
    }

    // Create a user with no permissions.
    $noperms_user = $this->drupalCreateUser();
    $this->drupalLogin($noperms_user);
    // Should be the same result for forbidden paths, since the user needs
    // special permissions for these paths.
    foreach ($forbidden_paths as $path) {
      $this->drupalGet($path);
      $this->assertResponse(403, "Access denied to generic user for path: $path");
    }

    // Create a user who can administer Meteor servers.
    $admin_user = $this->drupalCreateUser(array('administer meteor servers'));
    $this->drupalLogin($admin_user);
    // Forbidden paths aren't forbidden any more.
    foreach ($forbidden_paths as $unforbidden) {
      $this->drupalGet($unforbidden);
      $this->assertResponse(200, "Access granted to admin user for path: $unforbidden");
    }

    // Now that we have the admin user logged in, check the menu links.
    $this->drupalGet('');
    $this->assertLinkByHref('meteor');

    // 3) Verify that we can manage entities through the user interface.
    // We still have the admin user logged in, so we'll create, update, and
    // delete an entity.
    // Go to the list page.
    $this->drupalGet('/meteor');
    $this->clickLink('Add Meteor server');
    $machine_name = 'server_name';
    $this->drupalPostForm(
      NULL,
      array(
        'label' => $machine_name,
        'id' => $machine_name,
        'appKey' => '2222',
      ),
      t('Create Meteor Server')
    );

    // 4) Verify that our Meteor server appears when we edit it.
    $this->drupalGet('/meteor/manage/' . $machine_name);
    $this->assertField('label');
    // $this->assertFieldChecked('edit-floopy');
  }

}
