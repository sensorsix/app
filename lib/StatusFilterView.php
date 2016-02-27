<?php

class StatusFilterView
{
  /**
   * @var array
   */
  private $data = array();

  /**
   * @var Statuses[]
   */
  private $statuses = array();

  /**
   * @var int
   */
  private $decision_id;

  public function load()
  {
//  $this->statuses = array('Draft', 'Reviewed', 'Planned', 'Doing', 'Finished', 'Parked');

    /** @var RoleFilter[] $roleFilters  */
    $alternatives = AlternativeTable::getInstance()->findBy('decision_id', $this->decision_id);

    foreach ($alternatives as $alternative) {
      $this->statuses[] = $alternative->status;
    }

    /** @var RoleFilter[] $roleFilters  */
    $statusFilters = StatusFilterTable::getInstance()->findBy('decision_id', $this->decision_id);

    foreach ($statusFilters as $statusFilter) {
      $this->data[] = $statusFilter->status;
    }

    $this->statuses = array_unique($this->statuses);
  }

  public function render()
  {
    include_partial('status_filter', array('statuses' => $this->statuses, 'data' => $this->data));
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