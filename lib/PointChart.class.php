<?php

class PointChart extends AlternativesAnalyze
{
  protected
    $graph_type_id = 3,
    $x_label = '',
    $y_label = '';

  public function load()
  {
    $graphDefinitions = $this->getGraphDefinitions();
    if (count($graphDefinitions)) {
      $measurement = array();
      $alternatives_list = array();
      $this->criteria_names[0] = 'Total benefit';
      foreach ($graphDefinitions as $graphDefinition) {
        $this->alternative_names[$graphDefinition['alternative_id']] = Utility::teaser($graphDefinition['alternative_name'], 55);
        $this->criteria_names[$graphDefinition['criterion_id']] = Utility::teaser($graphDefinition['criterion_name'], 55);
        $measurement[$graphDefinition['criterion_id']][$graphDefinition['alternative_id']] = (float)$graphDefinition['number'];

        $alternatives_list[$graphDefinition['alternative_id']] = 0;
      }

      $alternatives_numb = count($alternatives_list);
      foreach ($measurement as $criterion_id => $alternatives) {
        // Sets 0 for alternatives without responses
        if (count($measurement[$criterion_id]) != $alternatives_numb) {
          $this->data[$criterion_id] = $measurement[$criterion_id] + $alternatives_list;
        } else {
          $this->data[$criterion_id] = $measurement[$criterion_id];
        }
      }
    } else {
      $this->prepareData();
    }
  }

  public function prepareData()
  {
    /** @var Response[]|Doctrine_Collection $responses  */
    $responses = $this->getBaseQuery()->execute();

    $this->alternative_names = array();
    $measurement = array();
    $alternatives_list = array();
    $this->criteria_names[0] = 'Total benefit';
    // TODO: Response can have different "Rating method"
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

          if (!isset($measurement[$criterion->id][$alternative->id])) {
            $measurement[$criterion->id][$alternative->id] = new AnalyzeAverageScore();
          }

          $measurement[$criterion->id][$alternative->id]->addScore($alternativeMeasurement->score);
          $alternatives_list[$alternative->id] = 0;
        }
      }
    }

    if (count($measurement)) {
      $graph = $this->saveGraph();
      $alternatives_numb = count($alternatives_list);

      foreach ($measurement as $criterion_id => $alternatives) {
        $this->data[$criterion_id] = array();

        /** @var PlannedAlternativeMeasurement $measurement  */
        foreach ($alternatives as $alternative_id => $measurement) {
          $value = $measurement->getAverage();
          $this->data[$criterion_id][$alternative_id] = $value;
          if ($criterion_id) {
            $this->graph_definitions[] = array($graph->id, $criterion_id, $alternative_id, $value);
          }
        }
        // Sets 0 for alternatives without responses
        if (count($this->data[$criterion_id]) && count($this->data[$criterion_id]) != $alternatives_numb) {
          $this->data[$criterion_id] = $this->data[$criterion_id] + $alternatives_list;
        }

        if (!count($this->data[$criterion_id])) {
          unset($this->data[$criterion_id]);
        }
      }
      $this->saveGraphDefinitions();
    }
  }

  /**
   * @param $data
   */
  public function setData($data)
  {
    $this->data = $data;
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
   * @param $criterion_id
   */
  public function setXLabelById($criterion_id)
  {
    if ($criterion_id == '0') {
      $this->x_label = 'Total benefit';
    } else {
      if ($criterion = Doctrine::getTable('Criterion')->find($criterion_id)) {
        $this->x_label = $criterion->name;
      }
    }
  }

  /**
   * @param $criterion_id
   */
  public function setYLabelById($criterion_id)
  {
    if ($criterion_id == '0') {
      $this->y_label = 'Total benefit';
    } else {
      if ($criterion = Doctrine::getTable('Criterion')->find($criterion_id)) {
        $this->y_label = $criterion->name;
      }
    }
  }

  /**
   * @return string
   */
  public function getXLabel()
  {
    return $this->x_label;
  }

  /**
   * @return string
   */
  public function getYLabel()
  {
    return $this->y_label;
  }

  /**
   * @param $values
   */
  public function setTotalBenefit($values)
  {
    foreach ($values as $alternative_id => $value)
    {
      $this->data[0][$alternative_id] = $value;
    }
  }

  /**
   * @param $labels
   */
  public function setAlternativesNames($labels)
  {
    $this->alternative_names = $labels;
  }

  public function render()
  {
    include_partial('point_chart', array('chart' => $this));
  }
}
