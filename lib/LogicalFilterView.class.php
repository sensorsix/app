<?php

class LogicalFilterView
{
  /**
   * @var int
   */
  private $decision_id;

  /**
   * @var array
   */
  private $data = array();

  /**
   * @var LogicalFilter[]|Doctrine_Collection
   */
  private $collection;

  private $filtered_alternatives_ids = array();

  public function load()
  {
    /**
     * @var LogicalFilter[]|Doctrine_Collection $collection
     */
    $this->collection = LogicalFilterTable::getInstance()->findBy('decision_id', $this->decision_id);
    foreach ($this->collection as $logicalFilter) {
      if(!isset($this->data[$logicalFilter->criterion_id])) {
        $this->data[$logicalFilter->criterion_id] = array();
      }
      $this->data[$logicalFilter->criterion_id][] = $logicalFilter;
    }

    $responses = Doctrine_Query::create()->from('Response r')
      ->select('r.id, am.id, ah.id, a.id, a.name, c.name, am.score')
      ->leftJoin('r.AlternativeMeasurement am')
      ->leftJoin('am.Alternative a')
      ->leftJoin('am.Criterion c')
      ->where("r.decision_id = ? AND am.rating_method != 'comment'", $this->decision_id)
      ->execute();

    $measurement = array();
    foreach ($responses as $response) {
      foreach ($response->AlternativeMeasurement as $alternativeMeasurement) {
        /** @var AlternativeMeasurement $alternativeMeasurement */
        $alternative = $alternativeMeasurement->Alternative;
        $criterion = $alternativeMeasurement->Criterion;

        if (isset($measurement[$criterion->id][$alternative->id])) {
          $measurement[$criterion->id][$alternative->id]->addScore($alternativeMeasurement->score);
        } else {
          $measurement[$criterion->id][$alternative->id] = new AnalyzeAverageScore();
          $measurement[$criterion->id][$alternative->id]->addScore($alternativeMeasurement->score);
        }
      }
    }

    if (count($measurement)) {
      foreach ($measurement as $criterion_id => $alternatives) {
        /** @var PlannedAlternativeMeasurement $measurement  */
        foreach ($alternatives as $alternative_id => $measurement) {
          if (!$this->applyLogicalFilter($criterion_id, $measurement->getAverage())) {
             $this->filtered_alternatives_ids[] = $alternative_id;
          }
        }
      }
    }
  }

  /**
   * @param $criterion_id
   * @param $value
   * @return bool|int
   */
  private function applyLogicalFilter($criterion_id, $value)
  {
    $result = true;

    if (isset($this->data[$criterion_id])) {
      foreach ($this->data[$criterion_id] as $logicalFilter) {
        /** @var LogicalFilter $logicalFilter */
        switch ($logicalFilter->logical_operator) {
          case '<':
            $result = $value < $logicalFilter->value;
            break;
          case '>':
            $result = $value > $logicalFilter->value;
            break;
          case '=':
            $result = $value = $logicalFilter->value;
            break;
        }

        if (!$result) {
          break;
        }
      }
    }
    return $result;
  }

  public function getFilteredAlternativesIds()
  {
    return $this->filtered_alternatives_ids;
  }

  public function render()
  {
    $logicalFilter = new LogicalFilter();
    $logicalFilter->decision_id = $this->decision_id;

    $form = new LogicalFilterForm($logicalFilter);

    include_partial('logical_filter', array('data' => $this->collection, 'form' => $form));
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