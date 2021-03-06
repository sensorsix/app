<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ExtendItemStatusList extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->changeColumn('alternative', 'status', 'enum', '',
      array(
        'values'  =>
          array(
            0 => 'Draft',
            1 => 'Reviewed',
            2 => 'Planned',
            3 => 'Doing',
            4 => 'Finished',
            5 => 'Parked'
          ),
        'default' => 'Draft',
      )
    );
  }

  public function postUp(){
    Doctrine::getTable('Alternative')->createQuery('a')
      ->update()
      ->set('a.status', '"Draft"')
      ->where('a.status != "Reviewed"')
      ->execute();
  }

  public function down()
  {
    $this->changeColumn('alternative', 'status', 'enum', '',
      array(
        'values'  =>
          array(
            0 => 'New',
            1 => 'Reviewed'
          ),
        'default' => 'New',
      )
    );
  }

  public function postDown(){
    Doctrine::getTable('Alternative')->createQuery('a')
      ->update()
      ->set('a.status', '"New"')
      ->where('a.status != "Reviewed"')
      ->execute();
  }
}