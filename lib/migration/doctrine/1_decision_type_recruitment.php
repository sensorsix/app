<?php

class DecisionTypeRecruitmentMigration extends Doctrine_Migration_Base
{
  public function up()
  {
    $decisionType = new DecisionType();
    $decisionType->name = 'Recruitment';
    $decisionType->alternative_alias = 'Candidate';
    $decisionType->alternative_plural_alias = 'Candidates';
    $decisionType->save();
  }

  public function down()
  {
    Doctrine::getTable('DecisionType')->findOneBy('name', 'Recruitment')->delete();
  }
}
