<?php

class roadmapComponents extends sfComponents
{
  /**
   * Get all user roadmaps for top menu
   **/
  public function executeTopMenu()
  {
    $this->roadmaps = RoadmapTable::getInstance()->getForUser($this->getUser()->getGuardUser());
  }

  public function executeAlternativePopupInfo()
  {

  }
}