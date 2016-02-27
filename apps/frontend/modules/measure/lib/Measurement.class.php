<?php

/**
 * @property MeasurementMap $map
 * @property MeasurementMethod $methodObject
 */
class Measurement
{
  private
    $role = null,
    $methodObject = null,
    $map = null;

  public function __construct(Role $role)
  {
    $this->role = $role;
    $this->map = new MeasurementMap();
    $this->map->load($role->id);
  }

  public function render()
  {
    if ($this->methodObject)
    {
      $this->methodObject->render();
    }
  }

  public function setValues($values, $store)
  {
    $this->methodObject->setValues($values, $this->map->hasNextStep() || $store);
  }

  public function hasError()
  {
    return $this->methodObject->hasError();
  }

  public function save()
  {
    $context = sfContext::getInstance();
    $user = $context->getUser();

    // When the role marked as "updateable" anyone with the link can enter the response and update it.
    /** @var Response $response */
    if ($this->role->updateable)
    {
      if ($this->role->Response->count() && !$this->role->anonymous)
      {
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

        if (!$response instanceof Response)
        {
          $response = new Response();
          $response->role_id = $this->role->id;
        }
      }
      else
      {
        $response = new Response();
        $response->role_id = $this->role->id;
      }
    }
    else
    {
      $response = new Response();
      $response->role_id = $this->role->id;
    }

    $response->decision_id = $this->role->decision_id;
    $response->ip_address = $context->getRequest()->getHttpHeader ('addr','remote');

    if ($user->isAuthenticated())
    {
      $response->user_id = $user->getGuardUser()->id;
    }
    else
    {
      if ($this->role->anonymous)
      {
        $response->email_address = 'anonymous';
      }
      else
      {
        $response->email_address = $user->getAttribute('email_address', null, 'measurement/email/' . $this->role->id);
      }

      if (!$this->role->continue_url)
      {
        $user->getAttributeHolder()->remove('email_address', 0, 'measurement/email/' . $this->role->id);
      }
    }

    $response->save();

    // Saves data from the last step
    $this->methodObject->setResponseId($response->id);
    $this->methodObject->save();
    $this->methodObject->clean();

    // Saves data from the previous steps
    while ($this->map->hasPreviousStep())
    {
      $this->map->back();
      list($methodClassName, $data) = $this->map->getCurrentStep();
      /** @var MeasurementMethod $methodObjectName */
      $methodObjectName = new $methodClassName($data);
      $methodObjectName->setRole($this->role);
      $methodObjectName->setResponseId($response->id);
      $methodObjectName->save();
      $methodObjectName->clean();
    }

    // Update charts in "Analyze" tab
    Doctrine_Query::create()
      ->delete()
      ->from('Graph')
      ->where('decision_id = ?', $response->decision_id)
      ->execute();

    $this->map->clean();
  }

  public function next()
  {
    $this->map->next();
    $this->map->save();
  }

  public function back()
  {
    $this->map->back();
    $this->map->save();
  }

  public function hasNextStep()
  {
    return $this->map->hasNextStep();
  }

  public function hasPreviousStep()
  {
    return $this->map->hasPreviousStep();
  }

  public function getMethodObject()
  {
    return $this->methodObject;
  }

  public function isFinished()
  {
    return $this->map->getStepsNumber() == 0;
  }

  public function setMethodObject(MeasurementMethod $methodObject)
  {
    $this->methodObject = $methodObject;
  }

  public function getMap()
  {
    return $this->map;
  }

  /**
   * @return string
   */
  public function getProgress()
  {
    return ceil($this->map->getStep() / $this->map->getStepsNumber() * 100) . '%';
  }
}