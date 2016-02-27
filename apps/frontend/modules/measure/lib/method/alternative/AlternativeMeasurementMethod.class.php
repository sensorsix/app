<?php

class AlternativeMeasurementMethod
{
  /**
   * @var Role
   */
  private $role;

  private
    $alternatives = null,
    $criterion = null,
    $criterion_id,
    $comments = array();

  public function __construct($criterion_id)
  {
    $this->criterion_id = $criterion_id;
  }

  public function load()
  {
    $this->alternatives = Doctrine::getTable('Alternative')->getPlannedForMeasurement($this->role->id, $this->criterion_id);
    $this->criterion = Doctrine::getTable('Criterion')->find($this->criterion_id);
    $this->comments = CommentTable::getInstance()
      ->getArrayForAlternatives($this->criterion_id, $this->alternatives->getPrimaryKeys(), $this->role);
  }

  public function render($text)
  {
    include_partial('alternative_measurement', array('criterion' => $this->criterion, 'text' => $text));
  }

  public function save($values, $method, $response_id)
  {
    foreach ($values as $id => $score)
    {

      $alternativeMeasurement = AlternativeMeasurementTable::getInstance()->getOneForSave($response_id, $id, $this->criterion_id);
      if (!$alternativeMeasurement)
      {
        $alternativeMeasurement = new AlternativeMeasurement();
      }

      $alternativeMeasurement->alternative_head_id = $id;
      $alternativeMeasurement->criterion_id = $this->criterion_id;
      $alternativeMeasurement->score = $score;
      $alternativeMeasurement->rating_method = $method;
      $alternativeMeasurement->response_id = $response_id;
      $alternativeMeasurement->save();
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
        $values = AlternativeMeasurementTable::getInstance()->findByResponseIdAndCriterionId($response->id, $this->criterion_id);
        /** @var CriterionPrioritization $object */
        foreach ($values as $object)
        {
          $return[$object->alternative_head_id] = $object->score;
        }
      }
    }

    return $return;
  }

  /**
   * @param Role $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }

  public function getAlternatives()
  {
    return $this->alternatives;
  }

  public function getComments()
  {
    return $this->comments;
  }
}