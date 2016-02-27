<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class AlternativeStatus extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->addColumn('alternative', 'status', 'enum', '',
      array(
        'values'  =>
          array(
            0 => 'New',
            1 => 'Reviewed',
          ),
        'default' => 'New',
      )
    );
  }

  public function down()
  {
    $this->removeColumn('alternative', 'status');
  }
}