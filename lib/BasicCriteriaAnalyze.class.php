<?php

/**
 * @property Criterion[] $criteria
 * @property Response[]|Doctrine_Collection $responses
 */
class BasicCriteriaAnalyze
{
  private
    $criteria = array(),
    $collection = array(),
    $rating_method = '',
    $responses = null,
    $decision_id = null;

  private function getSum()
  {
    $average_sum = 0;
    foreach ($this->criteria as $data)
    {
      $average_sum += $data['score']->getAverage();
    }

    return $average_sum;
  }

  public function load()
  {
    /** @var Response[]|Doctrine_Collection $responses  */
    $this->responses = Doctrine_Query::create()->from('Response r')
      ->select('r.id, cp.id, ch.id, c.id, c.name, cp.score')
      ->leftJoin('r.CriterionPrioritization cp')
      ->leftJoin('cp.CriterionHead ch')
      ->where('r.decision_id = ?', $this->decision_id)
      ->andWhere('cp.rating_method = ?', $this->rating_method)
      ->execute();
  }

  public function hasData()
  {
    return $this->responses->count() > 0;
  }

  public function prepareData()
  {
    foreach ($this->responses as $response)
    {
      foreach ($response->CriterionPrioritization as $criterionPrioritization)
      {
        /** @var CriterionPrioritization $criterionPrioritization */
        $criterion = $criterionPrioritization->CriterionHead;

        if (isset($this->criteria[$criterion->id]))
        {
          $this->criteria[$criterion->id]['score']->addScore($criterionPrioritization->score);
        }
        else
        {
          $this->criteria[$criterion->id]['name'] = $criterion->name;
          $this->criteria[$criterion->id]['score'] = new AnalyzeAverageScore();
          $this->criteria[$criterion->id]['score']->addScore($criterionPrioritization->score);
        }
      }
    }

    $percent_sum = 0;
    $average_sum = $this->getSum();

    foreach ($this->criteria as $criterion_id => $data)
    {
      $percent = round($data['score']->getAverage() / $average_sum * 100, 1);
      $this->collection[] = array(
        'title' => $data['name'],
        'value' => $percent,
        'id'    => $criterion_id
      );
      $percent_sum += $percent;
    }

    return $percent_sum;
  }

  public function getCriteriaValues()
  {
    $result = array();
    foreach ($this->collection as $item)
    {
      $result[$item['id']] = $item['value'];
    }

    return $result;
  }

  public function setRatingMethod($rating_method)
  {
    $this->rating_method = $rating_method;
  }

  public function setDecisionId($decision_id)
  {
    $this->decision_id = $decision_id;
  }

  public function getCollection()
  {
    return $this->collection;
  }
}