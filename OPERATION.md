# Meteor module operation

## Events and hooks

The module rests on the hypotheses in the following table of user-related events
and a selection of their respective hooks.


Hook \ Event                       | Add  |  Login   |  Logout   |  Update   | Cancel as block | Cancel as delete |  Add fields | Update fields | Delete fields |
-----------------------------------|:----:|:--------:|:---------:|:---------:|:---------------:|:----------------:|:-----------:|:-------------:|:-------------:|
hook_user_login                    |      |     1    |           |           |                 |                  |             |               |               |
hook_user_logout                   |      |          |      1    |           |                 |                  |             |               |               |
hook_user_cancel                   |      |          |           |           |        1        |         1        |             |               |               |
hook_field_storage_config_presave  |      |          |           |           |                 |                  |       1     |        1      |               |
hook_user_presave                  |   1  |          |           |      1    |        2        |                  |             |               |               |
hook_entity_presave                |   2  |          |           |      2    |        3        |                  |       2     |        2      |               |           
hook_user_insert                   |   3  |          |           |           |                 |                  |             |               |               |
hook_field_storage_config_insert   |      |          |           |           |                 |                  |       3     |               |               |
hook_entity_insert                 |   4  |          |           |           |                 |                  |       4     |               |               |
hook_field_storage_config_update   |      |          |           |           |                 |                  |             |        3      |               |
hook_user_update                   |      |          |           |      3    |        4        |                  |             |               |               |
hook_entity_update                 |      |          |           |      4    |        5        |                  |             |        4      |               |
hook_field_storage_config_predelete|      |          |           |           |                 |                  |             |               |        1      |
hook_user_predelete                |      |          |           |           |                 |         2        |             |               |               |
hook_entity_predelete              |      |          |           |           |                 |         3        |             |               |        2      |
hook_field_storage_config_delete   |      |          |           |           |                 |                  |             |               |        3      |
hook_user_delete                   |      |          |           |           |                 |         4        |             |               |               |
hook_entity_delete                 |      |          |           |           |                 |         5        |             |               |        4      |
module handling                    | None | HU Login | HU Logout | HU Update |    HU Update    |    HU delete     | HFSC insert |  HFSC update  |  HFSC delete  |


* The numbers represent the order in which hooks are fired by Drupal.
* The "module handling" line show which hook the Meteor module uses to catch the event.
* The table skips irrelavant EVD/EFD/FC hooks: only FSC changes can affect the exposed data 
* Entity hooks are for user, except FC = FieldConfig, EVD = EntityViewDisplay, EFD = EntityFormDisplay
* HU = hook_user_*, HFC = hook_field_config_*

## Handling
### New user creation

* Cannot affect existing connections: do nothing

### User login

* User A logs in: 
  * Notify the login for handling after the page can be expected to have completed: add a refresh delay
  * Meteor will notify users already using account A, and users without a valid login
  
### User logout

* User A logs out
  * Notify the logout immediately: the account is no longer valid
  * Meteor will notify users already using account A
  
### User update

* User A updated, including canceling
  * Notify the update for handling after the page can be expected to have 
    completed, because multiple updates may occur during the same page cycle and 
    there is no point in sending multiple notifications: add a refresh delay
  * Meteor will notify users already using account A
  
### User deletion

* User A canceled with deletion
  * Notify the deletion immediately: the account no longer exists.
  * Meteor will notify users already using account A

### Field changes

* Fields added/removed
  * All accounts are likely to be affected: notify with a refresh delay
  * Meteor will notify all connected accounts, hopefully not at the same time

