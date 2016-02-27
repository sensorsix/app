<?php

class roleComponents extends sfComponents
{
  public function executeMatrix(sfWebRequest $request)
  {
    $this->alternatives = Doctrine::getTable('Alternative')->getList($this->decision_id);
    $this->criteria = Doctrine::getTable('Criterion')->getList($this->decision_id);
    if ($this->role){
      $this->role->loadPlannedMeasurements();
    }
  }
}