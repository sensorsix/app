<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddActionsToLogs extends Doctrine_Migration_Base
{
    public function up()
    {
      $this->changeColumn('log', 'action', 'enum', '',
        array('values'  =>
                array(
                  0   => 'login',
                  1   => 'project_create',
                  2   => 'folder_create',
                  3   => 'item_create',
                  4   => 'item_update',
                  5   => 'criteria_create',
                  6   => 'criteria_update',
                  7   => 'survey_create',
                  8   => 'survey_update',
                  9   => 'survey_answered',
                  10  => 'budget_create',
                  11  => 'budget_update',
                  12  => 'release_create',
                  13  => 'release_update',
                  14  => 'wall_update',
                  15  => 'wall_visit',
                  16  => 'roadmap_create',
                  17  => 'roadmap_update'
                ),
              'default' => 'login',
        )
      );
    }

    public function down()
    {
      $this->changeColumn('log', 'action', 'enum', '',
        array('values'  =>
                array(
                  0   => 'login',
                  1   => 'project_create',
                  2   => 'folder_create',
                  3   => 'item_create',
                  4   => 'item_update',
                  5   => 'criteria_create',
                  6   => 'criteria_update',
                  7   => 'survey_create',
                  8   => 'survey_update',
                  9   => 'survey_answered',
                  10  => 'budget_create',
                  11  => 'budget_update',
                  12  => 'release_create',
                  13  => 'release_update',
                  14  => 'wall_update',
                  15  => 'wall_visit'
                ),
              'default' => 'login',
        )
      );
    }
}