<?php

class MeasurementMap
{
  private
    $map = array(),
    $step = 0,
    $role_id = null;

  /**
   * @return array
   * @throws sfException
   */
  public function getCurrentStep()
  {
    return isset($this->map[$this->step]) ? array_values($this->map[$this->step]) : false;
  }

  /**
   * @param string $type
   * @param int|null $criterion_id
   */
  private function addStep($type, $criterion_id = null)
  {
    $this->map[] = array(
      'class'      => $type . 'Measurement',
      'data' => array(
        'criterion_id' => $criterion_id,
        'role_id'   => $this->role_id
      )
    );
  }

  /**
   * @param string $type
   * @param array $object_ids
   * @param int|null $criterion_id
   */
  private function addPairwiseComparisonSteps($type, $object_ids, $criterion_id = null)
  {
    $iterations = count($object_ids) - 1;
    for ($i = 0; $i < $iterations; $i++)
    {
      for ($j = $i + 1; $j <= $iterations; $j++)
      {
        $this->map[] = array(
          'class' => $type  . 'Measurement',
          'data' => array(
            'object_head_id' => $object_ids[$i],
            'object_tail_id' => $object_ids[$j],
            'role_id'        => $this->role_id,
            'criterion_id'   => $criterion_id
          )
        );
      }
    }
  }

  /**
   * Builds map
   *
   * @param Role $role
   */
  public function build(Role $role)
  {
    $this->role_id = $role->id;

    if ($role->prioritize && $role->PlannedCriteria->count())
    {
      $prioritization_method = str_replace(' ', '', ucwords($role->prioritization_method));
      if ($prioritization_method == 'PairwiseComparison')
      {
        $criterion_ids = Doctrine_Query::create()
          ->from('Criterion c')
          ->leftJoin('c.PlannedCriterionPrioritization pcp')
          ->where('pcp.role_id = ?', $role->id)
          ->execute()
          ->getPrimaryKeys();

        $this->addPairwiseComparisonSteps('CriterionPairwiseComparison', $criterion_ids);
      }
      else
      {
        $this->addStep('Criterion' . $prioritization_method);
      }
    }

    if ($role->view_matrix)
    {
      $this->addStep('Matrix', $role->id);
    }
    else
    {
      foreach ($role->getMeasurements() as $criterion_id => $value)
      {
        if (is_array($value))
        {
          $this->addPairwiseComparisonSteps('AlternativePairwiseComparison', $value, $criterion_id);
        }
        else
        {
          $this->addStep('Alternative' . $value, $criterion_id);
        }
      }
    }
  }

  /**
   * Save into the user session
   */
  public function save()
  {
    sfContext::getInstance()->getUser()->setAttribute('map', $this->map, 'measurement/map/' . $this->role_id);
    sfContext::getInstance()->getUser()->setAttribute('step', $this->step, 'measurement/map/' . $this->role_id);
  }

  /**
   * Load data from session
   */
  public function load($role_id)
  {
    $this->role_id = $role_id;
    $this->map = sfContext::getInstance()->getUser()->getAttribute('map', array(), 'measurement/map/' . $role_id);
    $this->step = sfContext::getInstance()->getUser()->getAttribute('step', 0, 'measurement/map/' . $role_id);
  }

  /**
   * Remove data form session
   */
  public function clean()
  {
    $this->map = array();
    sfContext::getInstance()->getUser()->getAttributeHolder()->remove('map', array(), 'measurement/map/' . $this->role_id);
    sfContext::getInstance()->getUser()->getAttributeHolder()->remove('step', 0, 'measurement/map/' . $this->role_id);
  }

  /**
   * @return boolean
   */
  public function hasData()
  {
    return sfContext::getInstance()->getUser()->hasAttribute('map');
  }

  /**
   * Moves the position on the map on the next step
   */
  public function next()
  {
    if ($this->hasNextStep())
    {
      $this->step++;
    }
  }

  /**
   * Moves the position on the map on the previous step
   */
  public function back()
  {
    if ($this->hasPreviousStep())
    {
      $this->step--;
    }
  }

  public function hasNextStep()
  {
    return isset($this->map[$this->step + 1]);
  }

  public function hasPreviousStep()
  {
    return isset($this->map[$this->step - 1]);
  }

  public function getStepsNumber()
  {
    return count($this->map);
  }

  public function  getStep()
  {
    return $this->step + 1;
  }
}
