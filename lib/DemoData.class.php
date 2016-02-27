<?php

/**
 * @property Doctrine_Parser_Yml $yml;
 * @property Decision $decision
 */
class DemoData
{
  private
    $yml,
    $data_path,
    $decision;

  /**
   * @param $path
   */
  public function __construct($path)
  {
    $this->yml = new Doctrine_Parser_Yml();
    $this->data_path = $path;
  }

  /**
   * @param sfGuardUser $user
   */
  public function load(sfGuardUser $user)
  {
    $data = $this->yml->loadData($this->data_path);

    $folder = $this->createFolder();

    foreach ($data as $decision)
    {
      $this->createDecision($decision);

      $type_template = TypeTemplate::getInstance()->createQuery('t')
        ->where('t.user_id is NULL')
        ->andWhere('t.type_id = ?', $this->decision->type_id)
        ->andWhere('t.name = ?', 'Default')
        ->execute();
      $this->decision->template_id = $type_template[0]->getId(); // Default template

      $this->decision->setAssignedTo($user);
      $this->decision->setFolder($folder);
      $user->Decisions->add($this->decision);
      $user->DecisionGroups->add($folder);
    }
  }

  /**
   * @return Folder
   */
  private function createFolder(){
    $folder = new Folder();
    $folder->name = 'Projects';
    $folder->deletable = false;
    return $folder;
  }

  /**
   * @param array $data
   * @return Decision
   */
  private function createDecision(&$data)
  {
    $this->decision = new Decision();

    foreach ($data as $field => $value)
    {
      $method = 'create' . $field;
      if (method_exists($this, $method))
      {
        foreach ($value as $key => $item)
        {
          $this->decision->$field->add($this->$method($item), $key);
        }
      }
      else
      {
        $this->decision->$field = $value;
      }
    }

    return $this->decision;
  }

  /**
   * @param $data
   * @return Alternative
   */
  private function createAlternative(&$data)
  {
    $alternative = new Alternative();
    $alternative->status = 'Reviewed';

    foreach ($data as $field => $value)
    {
      $alternative->$field = $value;
    }

    return $alternative;
  }

  /**
   * @param $data
   * @return Criterion
   */
  private function createCriterion(&$data)
  {
    $criterion = new Criterion();

    foreach ($data as $field => $value)
    {
      $criterion->$field = $value;
    }

    return $criterion;
  }

  /**
   * @param $data
   * @return Role
   */
  private function createRoles(&$data)
  {
    $role = new Role();

    foreach ($data as $field => $value)
    {
      $method = 'create' . $field;
      if (method_exists($this, $method))
      {
        foreach ($value as $item)
        {
          $role->$field->add($this->$method($item));
        }
      }
      else
      {
        $role->$field = $value;
      }
    }

    if ($role->prioritize)
    {
      foreach ($this->decision->Criterion as $criterion)
      {
        if ($criterion->variable_type == 'Benefit')
        {
          $plannedCriterion = new PlannedCriterionPrioritization();
          $plannedCriterion->Criterion = $criterion;
          $role->PlannedCriteria->add($plannedCriterion);
        }
      }
    }

    return $role;
  }

  /**
   * @param $data
   * @return PlannedAlternativeMeasurement
   */
  private function createPlannedAlternativeMeasurement(&$data)
  {
    list($criterion_key, $alternative_key) = $data;
    $plannedMeasurement = new PlannedAlternativeMeasurement();
    $plannedMeasurement->Criterion = $this->decision->Criterion->get($criterion_key);
    $plannedMeasurement->Alternative = $this->decision->Alternative->get($alternative_key);
    return $plannedMeasurement;
  }

  /**
   * @param $data
   * @return Response
   */
  public function createResponse(&$data)
  {
    $response = new Response();
    $response->Decision = $this->decision;

    foreach ($data as $field => $value)
    {
      $method = 'create' . $field;
      if (method_exists($this, $method))
      {
        foreach ($value as $item)
        {
          $response->$field->add($this->$method($item));
        }
      }
      else
      {
        $response->$field = $value;
      }
    }

    return $response;
  }

  /**
   * @param $data
   * @return AlternativeMeasurement
   */
  public function createAlternativeMeasurement(&$data)
  {
    list($criterion_key, $alternative_key, $rating_method, $score) = $data;
    $alternativeMeasurement = new AlternativeMeasurement();

    $alternativeMeasurement->Alternative = $this->decision->Alternative->get($alternative_key);
    $alternativeMeasurement->Criterion = $this->decision->Criterion->get($criterion_key);
    $alternativeMeasurement->rating_method = $rating_method;
    $alternativeMeasurement->score = $score;

    return $alternativeMeasurement;
  }

  /**
   * @param $data
   * @return CriterionPrioritization
   */
  public function createCriterionPrioritization(&$data)
  {
    list($criterion_head_key, $criterion_tail_key, $rating_method, $score) = $data;
    $criterionPrioritization = new CriterionPrioritization();

    $criterionPrioritization->CriterionHead = $this->decision->Criterion->get($criterion_head_key);
    if ($criterion_tail_key)
    {
      $criterionPrioritization->CriterionTail = $this->decision->Criterion->get($criterion_tail_key);
    }
    $criterionPrioritization->rating_method = $rating_method;
    $criterionPrioritization->score = $score;

    return $criterionPrioritization;
  }
}
