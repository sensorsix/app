<?php

abstract class DirectFloatMeasurement extends MeasurementMethod
{
  protected
    $collection = null,
    $comments  = null,
    $errors = array();

  public function __construct($map_data, $namespace)
  {
    parent::__construct($map_data, $namespace . '/direct_float');
  }

  public function setValues($values, $store)
  {
    foreach ($values as $id => $value)
    {
      $value = str_replace(',', '.', $value);
      $values[$id] = $value;
      if ($value && !is_numeric($value))
      {
        $this->errors[$id] = sprintf('"%s" is not an number.', $value);
      }
    }

    parent::setValues($values, $store);
  }

  public function hasData()
  {
    return $this->collection->count() > 0;
  }

  /**
   * @return bool
   */
  public function hasError()
  {
    return count($this->errors) > 0;
  }

  public function render()
  {
    include_partial('direct_float', array(
        'collection'     => $this->collection,
        'values'         => $this->values,
        'errors'         => $this->errors,
        'comments'       => $this->comments,
        'prioritization' => $this->prioritization,
        'role_id'        => $this->role->id
      )
    );
  }
}
