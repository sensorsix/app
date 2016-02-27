<?php

class decisionComponents extends sfComponents
{
  public function executeBulkActions(sfWebRequest $request)
  {
    $query = Doctrine_Query::create()
      ->from('Decision')
      ->where('user_id = ? AND id != ?', array($this->getUser()->getGuardUser()->id, $request->getParameter('decision_id')))
      ->orderBy('id DESC');

    $this->widget = new sfWidgetFormDoctrineChoice(array('model' => 'Decision', 'query' => $query), array('class' => 'form-control'));
  }

  /**
   * Get all user projects for top menu
   *
   **/
  public function executeTopMenu()
  {
    $this->decisions = Doctrine_Query::create()
      ->from('Decision')
      ->where('user_id = ?', array($this->getUser()->getGuardUser()->id))
      ->orderBy('id DESC')
      ->execute();
  }
}