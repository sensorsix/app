<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddTableScripts extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->createTable('scripts', array(
      'id' =>
        array(
          'type' => 'integer',
          'length' => '8',
          'autoincrement' => '1',
          'primary' => '1',
        ),
      'top' =>
        array(
          'type' => 'varchar',
          'length' => '2000',
        ),
      'bottom' =>
        array(
          'type' => 'varchar',
          'length' => '2000',
        ),
    ), array(
      'primary' =>
        array(
          0 => 'id',
        ),
      'collate' => 'utf8_general_ci',
      'charset' => 'utf8',
    ));
  }

  public function down()
  {
    $this->dropTable('scripts');
  }
}