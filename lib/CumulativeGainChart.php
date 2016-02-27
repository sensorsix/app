<?php
 
class CumulativeGainChart
{
  protected
    $data = array(),
    $decision_id,
    $x_label,
    $measurement = array(),
    $cost_data = array(),
    $alternative_ids = array(),
    $alternative_names = array(),
    $criterion_names = array();

  public function load()
  {
    if (count($this->measurement) && count($this->cost_data)) {
      foreach ($this->cost_data as $criterion_id => &$alternatives) {
        $this->data[$criterion_id] = array(0);
        $b_score_sum = 0;
        $cost_sum = 0;

        $alternatives = Utility::sortArrayByArray($alternatives, array_reverse($this->alternative_ids));
        foreach ($alternatives as $alternative_id => $cost) {
          $value = $this->getSum($alternative_id) + $b_score_sum;
          $b_score_sum = $value;
          $cost = $cost + $cost_sum;
          $cost_sum = $cost;

          $this->cost_data[$criterion_id][$alternative_id] = $cost;
          $this->data[$criterion_id][$alternative_id] = $value;
        }

        $alternatives[0] = 0;
      }
    }
  }

  /**
   * @param int $alternative_id
   * @return float|int
   */
  private function getSum($alternative_id)
  {
    $sum = 0;

    if (isset($this->measurement[$alternative_id])) {
      foreach ($this->measurement[$alternative_id] as $value) {
        $sum += $value;
      }
    }

    return $sum;
  }

  public function render()
  {
    include_partial('cumulative_chart', array('chart' => $this));
  }

  /**
   * @param $measurement
   */
  public function setMeasurement($measurement)
  {
    $this->measurement = $measurement;
  }

  /**
   * @param $alternative_ids
   */
  public function setSortedAlternativeIds($alternative_ids)
  {
    $this->alternative_ids = $alternative_ids;
  }

  /**
   * @param $cost_data
   */
  public function setCostData($cost_data)
  {
    $this->cost_data = $cost_data;
  }

  /**
   * @return array
   */
  public function getCriteriaNames()
  {
    return $this->criterion_names;
  }

  /**
   * @param int $decision_id
   */
  public function setDecisionId($decision_id)
  {
    $this->decision_id = $decision_id;
  }

  /**
   * @return string
   */
  public function getJsonData()
  {
    return json_encode($this->data);
  }

  /**
   * @return mixed|string|void
   */
  public function getJsonCostData()
  {
    return json_encode($this->cost_data);
  }

  /**
   * @return string
   */
  public function getAlternativesJson()
  {
    return json_encode($this->alternative_names);
  }

  /**
   * @return bool
   */
  public function hasData()
  {
    return count($this->cost_data) > 0 && count($this->data) > 0;
  }

  /**
   * @param $criterion_id
   */
  public function setXLabelById($criterion_id)
  {
    if ($criterion = Doctrine::getTable('Criterion')->find($criterion_id)) {
      $this->x_label = $criterion->name;
    }
  }

  /**
   * @return mixed
   */
  public function getXLabel()
  {
    return $this->x_label;
  }

  /**
   * @param $data
   */
  public function setPointsXY($data)
  {
    foreach ($data as $point) {
      $this->data[] = array(floatval($point[0]), floatval($point[1]), $point[2]);
    }
  }

  /**
   * @param $alternative_names+
   */
  public function setAlternativeNames($alternative_names)
  {
    $this->alternative_names = $alternative_names;
    $this->alternative_names[0] = '';
  }

  /**
   * @param $criterion_names
   */
  public function setCriterionNames($criterion_names)
  {
    $this->criterion_names = $criterion_names;
  }
}
