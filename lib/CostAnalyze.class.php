<?php

class CostAnalyze extends AlternativesAnalyze
{
  private
    $alternative_ids = array(),
    $pool = 0,
    $b_scrore = 0,
    $unallocated = 0,
    $criterion_id,
    $cumulative_data = array(),
    $red_line = array('alternative_id' => null, 'class' => ''),
    $order = array();

  public function load()
  {
    /** @var Response[]|Doctrine_Collection $responses  */
    $responses = $this->getBaseQuery()
      ->andWhere("c.variable_type = 'Cost'")
      ->execute();

    foreach ($responses as $response) {
      foreach ($response->AlternativeMeasurement as $alternativeMeasurement) {
        /** @var AlternativeMeasurement $alternativeMeasurement */
        $alternative = $alternativeMeasurement->Alternative;
        $criterion = $alternativeMeasurement->Criterion;

        if (!isset($this->criteria_names[$criterion->id])) {
          $this->criteria_names[$criterion->id] = $criterion->name;
        }

        if (!in_array($alternative->id, $this->filtered_alternatives_ids)) {
          if (!isset($this->alternative_names[$alternative->id])) {
            $this->alternative_names[$alternative->id] = $alternative->name;
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
      foreach ($this->measurement as $criterion_id => $alternatives) {
        /** @var PlannedAlternativeMeasurement $measurement  */
        foreach ($alternatives as $alternative_id => $measurement) {
          if (!isset($this->data[$criterion_id])) {
            $this->data[$criterion_id] = array();
          }
          $this->data[$criterion_id][$alternative_id] = $measurement->getAverage();
        }
      }
    }
  }

  public function getCriteria()
  {
    return $this->criteria_names;
  }

  /**
   * @return array
   */
  public function getAlternativeNames()
  {
    if (count($this->alternative_ids)) {
      $this->alternative_names = Utility::sortArrayByArray($this->alternative_names, array_reverse($this->alternative_ids));
    }

    return $this->alternative_names;
  }

  /**
   * @return mixed|string|void
   */
  public function getAlternativesJson()
  {
    if (count($this->alternative_ids)) {
      $this->alternative_names = Utility::sortArrayByArray($this->alternative_names, array_reverse($this->alternative_ids));
    }

    return json_encode($this->alternative_names);
  }

  public function getAlternativeOrderJson()
  {
     $alternative_ids = Doctrine_Query::create()
      ->select('r.id, m.id, pm.id, a.id')
      ->from('Alternative a')
      ->leftJoin('a.Measurement m')
      ->leftJoin('m.Criterion c')
      ->leftJoin('m.Response r')
      ->where('r.decision_id = ?', $this->decision_id)
      ->andWhere("c.variable_type = 'Cost'")
      ->execute()
      ->getPrimaryKeys();

    if (count($alternative_ids)) {
      $alternative_ids = Utility::sortArrayByArray(array_combine($alternative_ids, $alternative_ids), $this->alternative_ids);
    }

    return json_encode(array_values($alternative_ids));
  }

  public function render()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
    include_partial('cost_analyze', array('analyze' => $this));
  }

  /**
   * @param $alternative_ids
   */
  public function setSortedAlternativeIds($alternative_ids)
  {
    $this->alternative_ids = $alternative_ids;
  }

  /**
   * @param float $pool
   */
  public function setPool($pool)
  {
    $this->pool = $pool;
  }

  /**
   * @param float $unallocated
   */
  public function setUnallocated($unallocated)
  {
    $this->unallocated = $unallocated;
  }

  /**
   * @param int $criterion_id
   */
  public function setCriterionId($criterion_id)
  {
    $this->criterion_id = $criterion_id;
  }

  /**
   * @param array $order
   */
  public function setOrder(array $order)
  {
    $alternatives_ids = array(0);
    foreach ($order as $item) {
      $alternatives_ids[] = $item['id'];
    }

    $alternatives = Doctrine_Query::create()->from('Alternative a INDEXBY a.id')
      ->select('a.id, a.name')
      ->whereIn('a.id', $alternatives_ids)
      ->execute();

    foreach ($order as $i => $item) {
      $order[$i]['name'] = $alternatives->get($item['id'])->name;
    }

    $this->order = $order;
  }

  /**
   * @return int
   */
  public function getPool()
  {
    return $this->pool;
  }

  /**
   * @return int
   */
  public function getUnallocated()
  {
    return $this->unallocated;
  }

  /**
   * @return string
   */
  public function getCriterionName()
  {
    $criterion = Doctrine::getTable('Criterion')->find($this->criterion_id);

    return $criterion ? $criterion->name : '';
  }

  /**
   * @return array
   */
  public function getOrder()
  {
    return $this->order;
  }

  /**
   * @param $data
   */
  public function setCumulativeData($data)
  {
    foreach ($data as $alternative_id => $criteria) {
      foreach ($criteria as $value) {
        if (!isset($this->cumulative_data[$alternative_id])) {
          $this->cumulative_data[$alternative_id] = 0;
        }
        $this->cumulative_data[$alternative_id] += $value;
      }
    }
  }

  /**
   * @return mixed|string|void
   */
  public function getCumulativeJsonData()
  {
    return json_encode($this->cumulative_data);
  }

  /**
   * @param $score
   */
  public function setBScore($score)
  {
    $this->b_scrore = $score;
  }

  /**
   * @return int
   */
  public function getBScore()
  {
    return $this->b_scrore;
  }

  /**
   * @return array
   */
  public function getData()
  {
    return $this->data;
  }

  /**
   * @param array $red_line
   */
  public function setRedLine(array $red_line)
  {
    if (isset($red_line['alternative_id']) && $red_line['top']) {
      $this->red_line['alternative_id'] = $red_line['alternative_id'];
      // $red_line['top'] contains values 'true' or 'false' as string
      $this->red_line['class'] = filter_var($red_line['top'], FILTER_VALIDATE_BOOLEAN) ? ' red-line-top' : ' red-line-bottom';
    }
  }

  /**
   * @return array
   */
  public function getRedLine()
  {
    return $this->red_line;
  }
}