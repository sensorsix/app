<?php

class StackedBarChart extends AlternativesAnalyze
{
  const MAX_ALTERNATIVES_NUMBER = 20;

  private $total_benefit = array();

  protected
    $graph_type_id = 2,
    $sorted_alternative_ids = array(),
    $cumulative_gain = array();

  private $save_graph = true;

  public function load()
  {
    $graphDefinitions = $this->getGraphDefinitions();
    if (count($graphDefinitions)) {
      $measurement = array();
      foreach ($graphDefinitions as $graphDefinition) {
        $this->alternative_names[$graphDefinition['alternative_id']] = Utility::teaser($graphDefinition['alternative_name'], 55);
        $this->criteria_names[$graphDefinition['criterion_id']] = Utility::teaser($graphDefinition['criterion_name'], 55);
        $measurement[$graphDefinition['alternative_id']][$graphDefinition['criterion_id']] = $graphDefinition['number'];
      }

      foreach ($measurement as $alternative_id => $criteria) {
        foreach ($criteria as $criterion_id => $score) {
          if (!isset($this->data[$criterion_id])) {
            $this->data[$criterion_id] = array();
          }
          $value = $score / 100 * $this->getCriterionValue($criterion_id);

          $this->data[$criterion_id][$alternative_id] = $value;
          $this->cumulative_gain[$alternative_id][$criterion_id] = $value;

          if (!isset($this->total_benefit[$alternative_id])) {
            $this->total_benefit[$alternative_id] = 0;
          }
          $this->total_benefit[$alternative_id] += $value;

          if (!isset($this->sorted_alternative_ids[$alternative_id])) {
            $this->sorted_alternative_ids[$alternative_id] = 0;
          }

          $this->sorted_alternative_ids[$alternative_id] += $value;
        }
      }
    } else {
      $this->prepareData();
    }

    // Sort descending by alternative benefit
    asort($this->sorted_alternative_ids);
    $alternatives_numb = count($this->sorted_alternative_ids);
    $this->sorted_alternative_ids = array_keys($this->sorted_alternative_ids);

    foreach ($this->data as $criterion_id => $alternatives) {
      for ($i = 0; $i < $alternatives_numb; $i++) {
        if (!array_key_exists($this->sorted_alternative_ids[$i], $this->data[$criterion_id])) {
          $this->data[$criterion_id][$this->sorted_alternative_ids[$i]] = 0;
        }
      }
      $this->data[$criterion_id] = Utility::sortArrayByArray($this->data[$criterion_id], $this->sorted_alternative_ids);
      $this->data[$criterion_id] = array_values($this->data[$criterion_id]);
    }
    $this->alternative_names = Utility::sortArrayByArray($this->alternative_names, $this->sorted_alternative_ids);
  }

  /**
   * @param int $criterion_id
   * @return float|int
   */
  public function getSum($criterion_id)
  {
    $average_sum = 0;
    /** @var PlannedAlternativeMeasurement $measurement  */
    foreach ($this->measurement[$criterion_id] as $measurement) {
      $average_sum += $measurement->getAverage();
    }

    return $average_sum;
  }

  public function prepareData()
  {
    /** @var Response[]|Doctrine_Collection $responses  */
    $responses = $this->getBaseQuery()
      ->andWhere("c.variable_type = 'Benefit'")
      ->execute();

    $this->alternative_names = array();
    foreach ($responses as $response) {
      foreach ($response->AlternativeMeasurement as $alternativeMeasurement) {
        /** @var AlternativeMeasurement $alternativeMeasurement */
        $alternative = $alternativeMeasurement->Alternative;
        $criterion = $alternativeMeasurement->Criterion;

        if (!isset($this->criteria_names[$criterion->id])) {
          $this->criteria_names[$criterion->id] = Utility::teaser($criterion->name, 55);
        }

        if (!in_array($alternative->id, $this->filtered_alternatives_ids)) {
          if (!isset($this->alternative_names[$alternative->id])) {
            $this->alternative_names[$alternative->id] = Utility::teaser($alternative->name, 55);
          }

          if (isset($this->measurement[$criterion->id][$alternative->id])) {
            $this->measurement[$criterion->id][$alternative->id]->addScore($alternativeMeasurement->score);
          } else {
            $this->measurement[$criterion->id][$alternative->id] = new AnalyzeAverageScore();
            $this->measurement[$criterion->id][$alternative->id]->addScore($alternativeMeasurement->score);
          }
        }
      }
    }

    if (count($this->measurement)) {
      if ($this->save_graph) {
        $graph = $this->saveGraph();
      }

      foreach ($this->measurement as $criterion_id => $alternatives) {
        $sum = $this->getSum($criterion_id);

        if ($sum) {
          $criterion_value = $this->getCriterionValue($criterion_id);
          /** @var PlannedAlternativeMeasurement $measurement  */
          foreach ($alternatives as $alternative_id => $measurement) {
            if (!isset($this->data[$criterion_id])) {
              $this->data[$criterion_id] = array();
            }

            $value = $measurement->getAverage() / $sum;
            $this->data[$criterion_id][$alternative_id] = $value * $criterion_value;
            $this->cumulative_gain[$alternative_id][$criterion_id] = $value * $criterion_value;

            if (!isset($this->sorted_alternative_ids[$alternative_id])) {
              $this->sorted_alternative_ids[$alternative_id] = 0;
            }

            if (!isset($this->total_benefit[$alternative_id])) {
              $this->total_benefit[$alternative_id] = 0;
            }
            $this->total_benefit[$alternative_id] += $value * $criterion_value;
            $this->sorted_alternative_ids[$alternative_id] += $value * $criterion_value;
            $this->graph_definitions[] = array($graph->id, $criterion_id, $alternative_id, $value * 100);
          }
        } else {
          unset($this->criteria_names[$criterion_id]);
        }
      }

      if ($this->save_graph) {
        $this->saveGraphDefinitions();
      }
    }
  }

  public function render()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
    include_partial('stacked_bar_chart', array('analyze' => $this));
  }

  /**
   * @return string
   */
  public function getJsonData()
  {
    foreach ($this->data as $criterion_id => $alternatives) {
      $this->data[$criterion_id] = array_slice($alternatives, -self::MAX_ALTERNATIVES_NUMBER, self::MAX_ALTERNATIVES_NUMBER);
    }

    return json_encode(array_values($this->data));
  }

  /**
   * @return string
   */
  public function getAlternativesJson()
  {
    return json_encode(array_values(array_slice($this->alternative_names, -self::MAX_ALTERNATIVES_NUMBER, self::MAX_ALTERNATIVES_NUMBER)));
  }

  /**
   * @return string
   */
  public function getCriteriaJson()
  {
    $result = array();
    foreach ($this->criteria_names as $name) {
      $result[] = array('label' => $name);
    }

    return json_encode($result);
  }

  /**
   * @return array
   */
  public function getSortedAlternativeIds()
  {
    return $this->sorted_alternative_ids;
  }

  public function getCumulativeData()
  {
    return $this->cumulative_gain;
  }

  /**
   * @param boolean $save_graph
   */
  public function setSaveGraph($save_graph)
  {
    $this->save_graph = $save_graph;
  }

  /**
   * @return array
   */
  public function getTotalBenefit()
  {
    return $this->total_benefit;
  }
}
