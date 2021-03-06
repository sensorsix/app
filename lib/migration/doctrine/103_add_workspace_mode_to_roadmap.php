<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddWorkspaceModeToRoadmap extends Doctrine_Migration_Base
{
    public function up()
    {
      $this->addColumn('roadmap', 'workspace_mode', 'enum', '',
        array(
          'values'  =>
            array(
              0 => 'timeline',
              1 => 'list'
            ),
          'default' => 'timeline',
        ));
    }

    public function down()
    {
      $this->removeColumn('roadmap', 'workspace_mode');
    }
}