<?php

class RoleFilterView
{
  /**
   * @var array
   */
  private $data = array();

  /**
   * @var Role[]
   */
  private $roles;

  /**
   * @var int
   */
  private $decision_id;

  public function load()
  {
    /** @var RoleFilter[] $roleFilters  */
    $roleFilters = RoleFilterTable::getInstance()->findBy('decision_id', $this->decision_id);
    $this->roles = RoleTable::getInstance()->findBy('decision_id', $this->decision_id);

    foreach ($roleFilters as $roleFilter) {
      $this->data[] = $roleFilter->role_id;
    }
  }

  public function render()
  {
    include_partial('role_filter', array('roles' => $this->roles, 'data' => $this->data));
  }

  /**
   * @param int $decision_id
   */
  public function setDecisionId($decision_id)
  {
    $this->decision_id = $decision_id;
  }

  /**
   * @return array
   */
  public function getData()
  {
    return $this->data;
  }
}