<?php

abstract class MeasurementMethod
{
  /**
   * @var Role
   */
  protected $role;

  protected
    $response_id,
    $namespace,
    $prioritization = false,
    $values = array();

  /**
   * @param array $map_data
   * @param string $namespace
   * @param array $default
   */
  public function __construct($map_data, $namespace, $default = array())
  {
    $this->namespace .= 'measurement/' . $namespace . '/' . implode('/', array_filter($map_data));
    $this->values = sfContext::getInstance()->getUser()->getAttribute('values', $default, $this->namespace);
  }

  public function hasData()
  {
    return true;
  }

  /**
   * @param int $response_id
   */
  public function setResponseId($response_id)
  {
    $this->response_id = $response_id;
  }

  abstract public function render();

  /**
   * Load data from database (previous response)
   * if not in the session
   *
   * @param $values
   */
  public function setDefaultValues($values)
  {
    $session_values = sfContext::getInstance()->getUser()->getAttribute('values', false, $this->namespace);

    $this->values = $session_values ?: $values;
  }

  /**
   * @param $values
   * @param $store
   */
  public function setValues($values, $store)
  {
    if ($store)
    {
      sfContext::getInstance()->getUser()->setAttribute('values', $values, $this->namespace);
    }
    else
    {
      $this->values = $values;
    }
  }

  public function clean()
  {
    sfContext::getInstance()->getUser()->getAttributeHolder()->remove('values', array(), $this->namespace);
  }

  /**
   * @return bool
   */
  public function hasError()
  {
    return false;
  }

  public function load()
  {
  }

  /**
   * @param Role $role
   */
  public function setRole(Role $role)
  {
    $this->role = $role;
  }

  abstract public function save();

  /**
   * @return AlternativeMeasurementMethod|CriterionMeasurementMethod|bool
   */
  public function getMeasurementMethod()
  {
    if (isset($this->alternativeMeasurementMethod) and $this->alternativeMeasurementMethod instanceof AlternativeMeasurementMethod)
    {
      return $this->alternativeMeasurementMethod;
    }
    else if (isset($this->criterionMeasurementMethod) and $this->criterionMeasurementMethod instanceof CriterionMeasurementMethod)
    {
      return $this->criterionMeasurementMethod;
    }

    return false;
  }

}