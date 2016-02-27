<?php

abstract class AlternativesAnalyze
{
  protected
    $measurement = array(),
    $graph_type_id = 0,
    $data = array(),
    $decision_id,
    $alternative_names = array(),
    $filtered_alternatives_ids = array(),
    $criteria_names = array(),
    $criteria_values = array(),
    $role_filter_data= array(),
    $status_filter_data= array(),
    $tag_filter_data= array(),
    $graph_definitions = array();

  /**
   * @return bool
   */
  public function hasData()
  {
    return count($this->data) > 0;
  }

  /**
   * @return array
   */
  protected function getGraphDefinitions()
  {
    return GraphDefinitionTable::getInstance()->createQuery('gd')
      ->select('gd.id, gd.criterion_id, gd.alternative_id, gd.number, c.name as criterion_name, a.name as alternative_name')
      ->leftJoin('gd.Graph g')
      ->leftJoin('gd.Criterion c')
      ->leftJoin('gd.Alternative a')
      ->where('g.decision_id = ? AND g.graph_type_id = ?', array($this->decision_id, $this->graph_type_id))
      ->fetchArray();
  }

  /**
   * @return Doctrine_Query
   */
  protected function getBaseQuery()
  {
    $query = Doctrine_Query::create()->from('Response r')
      ->select('r.id, am.id, ah.id, a.id, a.name, c.name, am.score')
      ->leftJoin('r.AlternativeMeasurement am')
      ->leftJoin('am.Alternative a')
      ->leftJoin('am.Criterion c')
      ->where("r.decision_id = ? AND am.rating_method != 'comment'", $this->decision_id);

    if (count($this->role_filter_data)) {
      $query->andWhere('r.role_id IN ? OR r.role_id IS NULL', array($this->role_filter_data));
    }

    if (count($this->status_filter_data)) {
      $query->andWhere('a.status NOT IN ?', array($this->status_filter_data));
    }

    if (count($this->tag_filter_data)) {
      $query->andWhere('a.id NOT IN ?', array($this->tag_filter_data));
    }

    return $query;
  }

  /**
   * @return Graph|Doctrine_Record
   */
  protected function saveGraph()
  {
    $graph = new Graph();
    $graph->decision_id = $this->decision_id;
    $graph->graph_type_id = $this->graph_type_id;
    $graph->save();

    return $graph;
  }

  protected function saveGraphDefinitions()
  {
    if (count($this->graph_definitions)) {
      $values = array();
      foreach ($this->graph_definitions as $definition) {
        $values[] = '(' . implode(',', $definition) . ')';
      }
      $values = implode(',', $values);
      $this->graph_definitions = array();

      Doctrine_Manager::connection()->exec('INSERT INTO graph_definition (graph_id, criterion_id, alternative_id, `number`) VALUES ' . $values );
    }
  }

  /**
   * @param int $decision_id
   */
  public function setDecisionId($decision_id)
  {
    $this->decision_id = $decision_id;
  }

  /**
   * @param int $criterion_id
   * @return int
   */
  protected function getCriterionValue($criterion_id)
  {
    $result = 1;
    if (count($this->criteria_values)) {
      if (isset($this->criteria_values[$criterion_id])) {
        $result = $this->criteria_values[$criterion_id] / 100;
      }
    } else {
      $result = 1 / count($this->criteria_names);
    }

    return $result;
  }

  /**
   * @return array
   */
  public function getData()
  {
    return $this->data;
  }

  /**
   * @return string
   */
  public function getJsonData()
  {
    return json_encode($this->data);
  }

  /**
   * @return array
   */
  public function getAlternativesNames()
  {
    return $this->alternative_names;
  }

  /**
   * @return string
   */
  public function getAlternativesJson()
  {
    return json_encode($this->alternative_names);
  }

  /**
   * @return array
   */
  public function getCriteriaNames()
  {
    return $this->criteria_names;
  }

  /**
   * @param array $values
   */
  public function setCriteriaValues($values)
  {
    $this->criteria_values = $values;
  }

  public function setFilteredAlternativesIds($filtered_alternatives_ids)
  {
    $this->filtered_alternatives_ids = $filtered_alternatives_ids;
  }

  public function setRoleFilterData($data)
  {
    $this->role_filter_data = $data;
  }

  public function setStatusFilterData($data)
  {
    $this->status_filter_data = $data;
  }

  public function setTagFilterData($data)
  {
    $this->tag_filter_data = $data;
  }

  abstract public function render();
}
