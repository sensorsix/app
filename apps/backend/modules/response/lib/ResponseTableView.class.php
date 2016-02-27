<?php

class ResponseTableView
{
  private
    $head = array(),
    $body = array(),
    $footer = array();

  /**
   * @param int $decision_id
   */
  public function load($decision_id)
  {
    /** @var Response[]|Doctrine_Collection $responses  */
    $responses = Doctrine_Query::create()
      ->from('Response r')
      ->leftJoin('r.User')
      ->leftJoin('r.AlternativeMeasurement am')
      ->leftJoin('am.Alternative a')
      ->leftJoin('r.CriterionPrioritization cp')
      ->leftJoin('cp.CriterionHead ch')
      ->leftJoin('cp.CriterionTail ct')
      ->where('r.decision_id = ?', $decision_id)
      ->orderBy('r.created_at DESC')
      ->execute();

    $rating_methods = sfConfig::get('app_rating_method');
    $rating_methods['pairwise comparison'] = 'Pairwise comparison';
    $rating_methods['direct rating'] = 'Direct Measure';
    $rating_methods['direct float'] = 'Direct Float';
    $rating_methods['comment'] = 'Comment';

    $criterion_head = array();
    $criterion_body = array();
    $criterion_footer = array();
    $alternative_head = array();
    $alternative_body = array();
    $alternative_footer = array();
    $alternatives = array();
    $criteria = array();

    foreach ($responses as $response)
    {
      // Response information
      $this->body[$response->id][] = $response->relatedExists('User') ? $response->User->email_address : $response->email_address;
      $this->body[$response->id][] = $response->ip_address;
      $this->body[$response->id][] = $response->Role->name;
      $this->body[$response->id][] = $response->updated_at;

      /** @var CriterionPrioritization $criterionPrioritization */
      foreach ($response->CriterionPrioritization as $criterionPrioritization)
      {
        // Prioritize criteria responses
        $planned_measure_id = $criterionPrioritization->CriterionHead->id;
        // Table header and footer data
        if (!isset($criterion_head[$planned_measure_id]))
        {
          if ($criterionPrioritization->rating_method == 'pairwise comparison')
          {
            $criterion_head[$planned_measure_id . '/' . $criterionPrioritization->CriterionTail->id] = $criterionPrioritization->CriterionHead->name  . '/'
              . $criterionPrioritization->CriterionTail->name;
          }
          else
          {
            $criterion_head[$planned_measure_id] = $criterionPrioritization->CriterionHead->name;
          }

          $criterion_footer[$planned_measure_id] = $rating_methods[$criterionPrioritization->rating_method];
        }
        // Table body data
        if ($criterionPrioritization->rating_method == 'pairwise comparison')
        {
          $tail_id = $criterionPrioritization->CriterionTail->id;
          $criteria[$planned_measure_id . '/' . $tail_id] = '';
          $criterion_body[$response->id][$planned_measure_id . '/' . $tail_id] = $criterionPrioritization->score;
        }
        else
        {
          $criteria[$planned_measure_id] = ''; // Prepare criterion row
          $criterion_body[$response->id][$planned_measure_id] = $criterionPrioritization->score;
        }
      }

      // Alternatives responses
      /** @var AlternativeMeasurement $alternativeMeasurement */
      foreach ($response->AlternativeMeasurement as $alternativeMeasurement)
      {
        // Table header and footer data
        $key = $alternativeMeasurement->alternative_head_id . '- ' . $alternativeMeasurement->criterion_id;
        if (!isset($this->head[$key]))
        {
          $alternative_head[$key] = $alternativeMeasurement->Alternative->name . '/' . $alternativeMeasurement->Criterion->name;
          $alternative_footer[$key] = $rating_methods[$alternativeMeasurement->rating_method];
        }
        // Table body data
        $alternatives[$key] = ''; // Prepare alternative row
        $alternative_body[$response->id][$key] = $alternativeMeasurement->score;
      }
    }
    
    $this->head = array_merge(array('User', 'IP address', 'Survey', 'Time'), $criterion_head, $alternative_head);
    $this->footer = array_merge(array_fill(0, 3, ''), $criterion_footer, $alternative_footer);
    
    unset($criterion_head, $alternative_head, $criterion_footer, $alternative_footer);    

    $responses = $responses->getPrimaryKeys();
    // The row length should be fixed but for some response criteria could be without data
    foreach ($responses as $response_id)
    {
      if (isset($criterion_body[$response_id]))
      {
        if (count($criterion_body[$response_id]) != count($criteria))
        {
          $row = $criterion_body[$response_id];
          $criterion_body[$response_id] = $criteria;
          foreach ($row as $key => $value)
          {
            $criterion_body[$response_id][$key] = $value;
          }
        }
        $this->body[$response_id] = array_merge($this->body[$response_id], array_values($criterion_body[$response_id]));
      }
      else
      {
        $this->body[$response_id] = array_merge($this->body[$response_id], $criteria);
      }
    }
    unset($criteria, $criterion_body);

    foreach ($responses as $response_id)
    {
      if (isset($alternative_body[$response_id]))
      {
        if (count($alternative_body[$response_id]) != count($alternatives))
        {
          $row = $alternative_body[$response_id];
          $alternative_body[$response_id] = $alternatives;
          foreach ($row as $key => $value)
          {
            $alternative_body[$response_id][$key] = $value;
          }
        }
        $this->body[$response_id] = array_merge($this->body[$response_id], array_values($alternative_body[$response_id]));
      }
      else
      {
        $this->body[$response_id] = array_merge($this->body[$response_id], $alternatives);
      }
    }
  }

  /**
   * @return bool
   */
  public function hasData()
  {
    return count($this->body) > 0;
  }

  /**
   * @return array
   */
  public function getHeaderData()
  {
    return $this->head;
  }

  /**
   * @return array
   */
  public function getBodyData()
  {
    return $this->body;
  }

  /**
   * @return array
   */
  public function getFooterData()
  {
    return $this->footer;
  }

  public function render()
  {
    include_partial('response_table_view', array('table' => $this));
  }
}
