<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class UserCreditMigration extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->addColumn('sf_guard_user', 'credit', 'integer', '1',
      array(
        'notnull' => '1',
        'default' => '1'
      )
    );
  }

  public function down()
  {
    $this->removeColumn('sf_guard_user', 'credit');
  }
}