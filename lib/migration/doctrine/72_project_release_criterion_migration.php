<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class ProjectReleaseCriterionMigration extends Doctrine_Migration_Base
{
  public function up()
  {
    $this->changeColumn('project_release', 'criterion_id', 'integer', '8', array());
  }

  public function down()
  {
  }
}