<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AddItemIdToAlternative extends Doctrine_Migration_Base
{
    public function up()
    {
      $this->addColumn('alternative', 'item_id', 'varchar', '8');
    }

    public function down()
    {
      $this->removeColumn('alternative', 'item_id');
    }
}