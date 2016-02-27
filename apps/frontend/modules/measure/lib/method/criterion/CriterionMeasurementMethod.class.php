<?php

class CriterionMeasurementMethod
{
  /**
   * @var Role
   */
  private $role;

  private
    $criteria,
    $decision,
    $comments;

  public function __construct()
  {
  }

  public function load()
  {
    $this->criteria = Doctrine::getTable('Criterion')->getPlannedForPrioritization($this->role->id);

    $this->decision = Doctrine_Query::create()
      ->from('Decision d')
      ->where('d.Roles.id = ?', $this->role->id)
      ->fetchOne();

    $this->comments = CommentTable::getInstance()->getArrayByCriteria($this->criteria->getPrimaryKeys());
  }

  public function render($text)
  {
    include_partial('criterion_prioritization', array('decision' => $this->decision, 'text' => $text));
  }

  public function save($values, $method, $response_id)
  {
    foreach ($values as $id => $score)
    {
      $criterionPrioritization = CriterionPrioritizationTable::getInstance()->getOneForSave($response_id, $id);
      if (!$criterionPrioritization)
      {
        $criterionPrioritization = new CriterionPrioritization();
      }

      $criterionPrioritization->criterion_head_id = $id;
      $criterionPrioritization->rating_method = $method;
      $criterionPrioritization->response_id = $response_id;
      $criterionPrioritization->score = $score;
      $criterionPrioritization->save();
    }
  }

  /**
   * Return the values from the database or empty array
   */
  public function getValues()
  {
    $return = array();

    if ($this->role and $this->role->Response->count() and $this->role->updateable and !$this->role->anonymous) {

      $context = sfContext::getInstance();
      $user = $context->getUser();

      // find response by user_id
      if ($user->isAuthenticated())
      {
        $response = ResponseTable::getInstance()->findByRoleIdAndUserId($this->role->id, $user->getGuardUser()->id)->getFirst();
      }
      // find response by email
      else
      {
        $email = $user->getAttribute('email_address', null, 'measurement/email/' . $this->role->id);
        $response = ResponseTable::getInstance()->findByRoleIdAndEmailAddress($this->role->id, $email)->getFirst();
      }

      if ($response instanceof Response)
      {
        $values = CriterionPrioritizationTable::getInstance()->findByResponseId($response->id);
        /** @var CriterionPrioritization $object */
        foreach ($values as $object)
        {
          $return[$object->criterion_head_id] = $object->score;
        }
      }
    }

    return $return;
  }

  public function getCriteria()
  {
    return $this->criteria;
  }

  public function getComments()
  {
    return $this->comments;
  }

  /**
   * @param Role $role
   */
  public function setRole(Role $role)
  {
    $this->role = $role;
  }
}