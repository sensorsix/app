<?php

class dashboardComponents extends sfComponents
{
  public function executeImportTrello(sfWebRequest $request)
  {
    $this->external_ids = array();

    $external_decisions = DecisionTable::getInstance()->createQuery('d')
      ->select('d.external_id')
      ->where('d.external_id IS NOT NULL')
      ->andWhereIn('d.user_id', sfGuardUserTable::getInstance()->getUsersInTeamIDs($this->getUser()->getGuardUser()))
      ->fetchArray();

    foreach ($external_decisions as $external_decision){
      $this->external_ids[] = $external_decision['external_id'];
    }
  }
}