<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ResponseAddUpdatedAtMigration extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->addColumn('response', 'updated_at', 'timestamp', '25',  array('notnull' => '1'));
  }

  public function postUp()
  {
    Doctrine_Query::create()
      ->delete()
      ->from('Response')
      ->where('role_id IS NULL')
      ->execute();

    foreach (Doctrine::getTable('Response')->findAll() as $response)
    {
      $response->updated_at = $response->created_at;
      $response->save();
    }
  }

  public function down()
  {
    $this->removeColumn('response', 'updated_at');
  }
}