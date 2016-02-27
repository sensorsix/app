<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ResponseIpAddressMigration extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->addColumn('response', 'ip_address', 'string', '16', array());
    $this->addColumn('response', 'user_id', 'integer', '8', array());
    $this->addColumn('response', 'email_address', 'string', 255);
    $this->addColumn('response', 'created_at', 'timestamp', '25', array('notnull' => '1'));
  }

  public function down()
  {
    $this->removeColumn('response', 'ip_address');
    $this->removeColumn('response', 'user_id');
    $this->removeColumn('response', 'email_address');
    $this->removeColumn('response', 'created_at');
  }
}