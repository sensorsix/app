<?php

class CriteriaAnalyze
{
  const GRAPH_WIDTH_RATIO = 30;
  const GRAPH_HEIGHT_RATIO = 4;
  const GRAPH_BOTTOM_HEIGHT_RATIO = 6;

  private
    $collection = array(),
    $response_number = 0,
    $decision_id = null,
    $save_graph = true,
    $max_criterion_name_length = 0,
    $graph_heigth = 0,
    $graph_width = 0,
    $graph_bottom_delta = 0;

  public function load()
  {
    $graph = GraphTable::getInstance()->createQuery('g')
      ->select('g.id')
      ->where('g.decision_id = ?', $this->decision_id)
      ->andWhere('g.graph_type_id = ?', 1)
      ->limit(1)
      ->fetchArray();

    if (count($graph)) {
      $graphDefinitions = Doctrine_Query::create()
        ->select('gd.id, gd.criterion_id, gd.number, c.name AS name')
        ->from('GraphDefinition gd')
        ->leftJoin('gd.Criterion c')
        ->where('gd.graph_id = ?', $graph[0]['id'])
        ->fetchArray();

      foreach ($graphDefinitions as $graphDefinition) {
        if ($this->max_criterion_name_length < strlen($graphDefinition['name'])) {
          $this->max_criterion_name_length = strlen($graphDefinition['name']);
        }

        $this->collection[] = array(
          'title' => $graphDefinition['name'],
          'value' => $graphDefinition['number'],
          'id'    => $graphDefinition['criterion_id']
        );
      }
    } else if ($this->prepareData() && $this->save_graph) {
      $graph = new Graph();
      $graph->decision_id = $this->decision_id;
      $graph->graph_type_id = 1;
      $graph->save();

      foreach($this->collection as $item) {
        if ($this->max_criterion_name_length < strlen($item['title'])) {
          $this->max_criterion_name_length = strlen($item['title']);
        }

        $graphDefinition = new GraphDefinition();
        $graphDefinition->graph_id = $graph->id;
        $graphDefinition->criterion_id = $item['id'];
        $graphDefinition->number = $item['value'];
        $graphDefinition->save();
      }
    }else{
      $criterion = CriterionTable::getInstance()->createQuery('c')
        ->select('c.id, c.name')
        ->where('c.decision_id = ?', $this->decision_id)
        ->andWhere('c.variable_type = ?', 'Benefit')
        ->fetchArray();

      foreach ($criterion as $criteria) {
          $this->collection[] = array(
            'title' => $criteria['name'],
            'value' => round(100 / count($criterion), 1),
            'id'    => $criteria['id']
          );
      }
    }
    $this->calculateGraphSizes();

    $this->response_number = Doctrine::getTable('Response')->findBy('decision_id', $this->decision_id)->count();
  }

  /**
   * Save the data in to database (graph_changes table)
   */
  public function saveData()
  {
    $graph = GraphTable::getInstance()->createQuery('g')
        ->select('g.id')
        ->where('g.decision_id = ?', $this->decision_id)
        ->andWhere('g.graph_type_id IS NULL')
        ->limit(1)
        ->fetchArray();

    // try update changes
    if (count($graph)) {
      $graphChanges = Doctrine_Query::create()
          ->select('gd.id, gd.criterion_id, gd.number, c.name AS name')
          ->from('GraphChanges gd')
          ->leftJoin('gd.Criterion c')
          ->where('gd.graph_id = ?', $graph[0]['id'])
          ->execute();

      foreach ($this->collection as $item) {
        $updated = false;

        // update
        foreach ($graphChanges as $graphChange) {
          if ($item['id'] == $graphChange->criterion_id) {
            $graphChange->number = $item['value'];
            $graphChange->save();
            $updated = true;
          }
        }

        // insert
        if (!$updated) {
          $gc = new GraphChanges();
          $gc->graph_id = $graph[0]['id'];
          $gc->criterion_id = $item['id'];
          $gc->number = $item['value'];
          $gc->save();
        }
      }

    // or create new changes
    } else {
      $graph = new Graph();
      $graph->decision_id = $this->decision_id;
      $graph->save();

      foreach ($this->collection as $item) {
        // insert
        $gc = new GraphChanges();
        $gc->graph_id = $graph->id;
        $gc->criterion_id = $item['id'];
        $gc->number = $item['value'];
        $gc->save();
      }
    }
  }

  /**
   * Load data from database
   */
  public function loadData()
  {
    $graph = GraphTable::getInstance()->createQuery('g')
        ->select('g.id')
        ->where('g.decision_id = ?', $this->decision_id)
        ->andWhere('g.graph_type_id IS NULL')
        ->limit(1)
        ->fetchArray();

    if (count($graph)) {
      $graphChanges = Doctrine_Query::create()
          ->select('gd.id, gd.criterion_id, gd.number, c.name AS name')
          ->from('GraphChanges gd')
          ->leftJoin('gd.Criterion c')
          ->where('gd.graph_id = ?', $graph[0]['id'])
          ->execute();

      foreach ($graphChanges as $graphChange) {
        $this->collection[] = array(
          'title' => $graphChange->name,
          'id'    => $graphChange->criterion_id,
          'value' => $graphChange->number
        );
      }

    // when no data in database, get default
    } else {
      $this->load();
    }
  }

  public function setData($data)
  {
    $this->collection = $data;
  }

  public function hasData()
  {
    return count($this->collection) > 0;
  }

  public function prepareData()
  {
    $percent_sum = 0;

    $objects = CriterionPrioritizationTable::getInstance()->createQuery('cp')
      ->select('cp.rating_method')
      ->distinct()
      ->leftJoin('cp.Response r')
      ->where('r.decision_id = ?', $this->decision_id)
      ->fetchArray();

    foreach ($objects as $object) {
      if ($object['rating_method'] == 'pairwise comparison') {
        $analyzeMethod = new PairwiseComparisonCriteriaAnalyze();
      } else {
        $analyzeMethod = new BasicCriteriaAnalyze();
        $analyzeMethod->setRatingMethod($object['rating_method']);
      }

      $analyzeMethod->setDecisionId($this->decision_id);
      $analyzeMethod->load();

      if ($analyzeMethod->hasData()) {
        $percent_sum += $analyzeMethod->prepareData();
        $this->collection = $analyzeMethod->getCollection();
      }
    }

    if (count($objects)) {
      $percent_sum /= count($objects);
    }

    $has_data = false;
    if ($percent_sum) {
      $has_data = true;
      // Corrects round
      $delta = 100 - $percent_sum > 0 ? 0.1 : -0.1;
      $steps = round(abs(100 - $percent_sum) * 10);
      for ($i = 0; $i < $steps; $i++) {
        $this->collection[$i]['value'] += $delta;
      }
    }

    return $has_data;
  }

  public function getCriteriaValues()
  {
    $result = array();

    foreach ($this->collection as $item) {
      $result[$item['id']] = $item['value'];
    }

    return $result;
  }

  public function setCriteriaValues($criteria)
  {
    $criteria_ids = array(0);
    $criteria_values = array();
    foreach ($criteria as $criterion) {
      $criteria_ids[] = $criterion['id'];
      $criteria_values[$criterion['id']] = $criterion['value'] / 10;
    }

    $collection =  CriterionTable::getInstance()->createQuery('c')
      ->select('c.id, c.name')
      ->whereIn('c.id', $criteria_ids)
      ->fetchArray();

    foreach ($collection as $item) {
      if ($this->max_criterion_name_length < strlen($item['name'])) {
        $this->max_criterion_name_length = strlen($item['name']);
      }

      $this->collection[] = array(
        'title' => $item['name'],
        'value' => $criteria_values[$item['id']],
        'id'    => $item['id']
      );
    }
    $this->calculateGraphSizes();
  }

  public function setDecisionId($decision_id)
  {
    $this->decision_id = $decision_id;
  }

  public function getJsonData()
  {
    return json_encode($this->collection);
  }

  public function getResponseNumber()
  {
    return $this->response_number;
  }

  public function getGraphHeight()
  {
    return $this->graph_heigth > 450 ? $this->graph_heigth : 450;
  }

  public function getGraphWidth()
  {
    return $this->graph_width > 60 ? $this->graph_width : 600;
  }

  public function getGraphBottomDelta()
  {
    return $this->graph_bottom_delta;
  }

  public function render()
  {
    $decision = DecisionTable::getInstance()->find($this->decision_id);

    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
    include_partial('criteria_chart', array('analyze' => $this, 'decision' => $decision));
  }

  /**
   * @param boolean $save_graph
   */
  public function setSaveGraph($save_graph)
  {
    $this->save_graph = $save_graph;
  }

  private function calculateGraphSizes()
  {
    $this->graph_width        = self::GRAPH_WIDTH_RATIO * $this->max_criterion_name_length;
    $this->graph_bottom_delta = self::GRAPH_BOTTOM_HEIGHT_RATIO * $this->max_criterion_name_length;
    $this->graph_heigth       = self::GRAPH_HEIGHT_RATIO * $this->graph_bottom_delta;
  }
}
