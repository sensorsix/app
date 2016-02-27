<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class GuardUserAddAccountType extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->addColumn('sf_guard_user', 'last_payment_date', 'timestamp', '25', array());
    $this->addColumn('sf_guard_user', 'account_type', 'enum', '', array(
        'values'  =>
        array(
          0 => 'Trial',
          1 => 'Basic',
          2 => 'Enterprise',
          3 => 'Consultant',
          4 => 'Free',
        ),
        'default' => 'Trial',
      ));
  }

  public function down()
  {
    $this->removeColumn('sf_guard_user', 'last_payment_date');
    $this->removeColumn('sf_guard_user', 'account_type');
  }
}