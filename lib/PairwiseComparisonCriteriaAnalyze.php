<?php

/**
 * @property Response[]|Doctrine_Collection $responses
 */
class PairwiseComparisonCriteriaAnalyze
{
  private
    $collection = array(),
    $responses = null,
    $decision_id = null;

  public function load()
  {
    $this->responses = Doctrine_Query::create()->from('Response r')
      ->select('r.id, cp.id, cp.score, cp.criterion_tail_id, cp.criterion_head_id')
      ->leftJoin('r.CriterionPrioritization cp')
      ->where('r.decision_id = ?', $this->decision_id)
      ->andWhere('cp.rating_method = "pairwise comparison"')
      ->execute();
  }

  public function hasData()
  {
    return $this->responses->count() > 0;
  }

  public function prepareData()
  {
    $pairs = array();
    foreach ($this->responses as $i => $response)
    {
      /** @var CriterionPrioritization $criterionPrioritization */
      foreach ($response->CriterionPrioritization as $criterionPrioritization)
      {
        if ($i && isset($pairs[$criterionPrioritization->criterion_head_id][$criterionPrioritization->criterion_tail_id]))
        {
          $matrixRecord = $pairs[$criterionPrioritization->criterion_head_id][$criterionPrioritization->criterion_tail_id];
        }
        else
        {
          $matrixRecord = new PairwiseComparisonMatrixRecord();
        }

        $matrixRecord->addScore($criterionPrioritization->score);
        $pairs[$criterionPrioritization->criterion_head_id][$criterionPrioritization->criterion_tail_id] = $matrixRecord;
      }
    }

    $prioritizedCriteria =  Doctrine_Query::create()
      ->from('Criterion c')
      ->select('c.id, , c.name')
      ->leftJoin('c.HeadPrioritization hp')
      ->leftJoin('c.TailPrioritization tp')
      ->where('c.decision_id = ?', $this->decision_id)
      ->andWhere('hp.id IS NOT NULL OR tp.id IS NOT NULL') // Has relation with "CriterionPrioritization"
      ->execute();

    $sum = 0;
    $matrix_row_sum = array();
    $criteria_names = array();
    foreach ($prioritizedCriteria as $criterion)
    {
      $matrix_row_sum[$criterion->id] = 0;
      $criteria_names[$criterion->id] = $criterion->name;
      foreach ($prioritizedCriteria as $rowPrioritizedCriterion)
      {
        if ($criterion->id == $rowPrioritizedCriterion->id)
        {
          $matrix_row_sum[$criterion->id] += 1;
        }
        else if (isset($pairs[$criterion->id][$rowPrioritizedCriterion->id]))
        {
          $matrix_row_sum[$criterion->id] += 1 / $pairs[$criterion->id][$rowPrioritizedCriterion->id]->getAverage();
        }
        else
        {
          $matrix_row_sum[$criterion->id] += $pairs[$rowPrioritizedCriterion->id][$criterion->id]->getAverage();
        }
      }
      $sum += $matrix_row_sum[$criterion->id];
    }

    $percent_sum = 0;
    foreach ($matrix_row_sum as $criterion_id => $row_sum)
    {
      $percent = round($row_sum  / $sum * 100, 1);
      $this->collection[] = array(
        'title' => $criteria_names[$criterion_id],
        'value' => $percent,
        'id'    => $criterion_id
      );

      $percent_sum += $percent;
    }

    return $percent_sum;
  }
  
  public function getCollection()
  {
    return $this->collection;
  }

  public function setDecisionId($decision_id)
  {
    $this->decision_id = $decision_id;
  }
}
