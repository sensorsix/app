<?php
 
class Dashboard
{
  private $criteria = array();

  private $body = array();

  public function load(sfGuardUser $user, $decision_id)
  {
    $this->criteria = CriterionTable::getInstance()->getArrayForUser($user, $decision_id);
    $alternatives   = AlternativeTable::getInstance()->getArrayForUser($user, $decision_id);
    $values         = AlternativeMeasurementTable::getInstance()->getForDashboard($user, $decision_id);

    foreach ($alternatives as $alternative) {
      $this->body[$alternative['id']][0] = $alternative['name'];
      foreach ($this->criteria as $criterion) {
        $cell = new stdClass();
        $cell->measurement = str_replace(' ', '_', $criterion['measurement']);
        $cell->value = isset($values[$alternative['id']][$criterion['id']]) ? $values[$alternative['id']][$criterion['id']] : null;
        $this->body[$alternative['id']][$criterion['id']] = $cell;
      }
    }
  }

  /**
   * @return array
   */
  public function getCriteria()
  {
    return $this->criteria;
  }

  /**
   * @return array
   */
  public function getBodyData()
  {
    return $this->body;
  }
}