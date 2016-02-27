<?php
/**
 * Ticket #13695
 */
class UpgradeRole extends Doctrine_Migration_Base
{
    public function up()
    {
      $this->addColumn('role', 'active', 'boolean', '25', array('default' => '1'));
    }

    public function down()
    {
      $this->removeColumn('role', 'active');
    }
}