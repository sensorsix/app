<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class Version114 extends Doctrine_Migration_Base
{
    public function up()
    {
        $this->createTable('tag_release', array(
             'id' => 
             array(
              'type' => 'integer',
              'length' => '8',
              'autoincrement' => '1',
              'primary' => '1',
             ),
             'tag_id' => 
             array(
              'type' => 'integer',
              'length' => '8',
             ),
             'release_id' => 
             array(
              'type' => 'integer',
              'length' => '8',
             ),
             ), array(
             'primary' => 
             array(
              0 => 'id',
             ),
             'collate' => 'utf8_general_ci',
             'charset' => 'utf8',
             ));
        $this->addColumn('project_release', 'status', 'enum', '', array(
             'values' => 
             array(
              0 => 'Draft',
              1 => 'Reviewed',
              2 => 'Planned',
              3 => 'Doing',
              4 => 'Finished',
              5 => 'Parked',
             ),
             'default' => 'Draft',
             ));
        $this->addColumn('project_release', 'start_date', 'timestamp', '25', array(
             'format' => 'Y-m-d H:i:s',
             ));
        $this->addColumn('project_release', 'end_date', 'timestamp', '25', array(
             'format' => 'Y-m-d H:i:s',
             ));
    }

    public function down()
    {
        $this->dropTable('tag_release');
        $this->removeColumn('project_release', 'status');
        $this->removeColumn('project_release', 'start_date');
        $this->removeColumn('project_release', 'end_date');
    }
}