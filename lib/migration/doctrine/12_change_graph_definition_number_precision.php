<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ChangeGraphDefinitionNumberPrecision extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->changeColumn('graph_definition', 'number', 'decimal', '20', array('scale' => '1'));
  }

  public function down()
  {

  }
}