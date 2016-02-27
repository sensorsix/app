<?php

abstract class DirectRatingMeasurement extends MeasurementMethod
{
  protected
    $collection = null,
    $errors = array(),
    $comments  = null;

  public function __construct($map_data, $namespace)
  {
    parent::__construct($map_data, $namespace . '/direct_rating');
  }

  public function setValues($values, $store)
  {
    foreach ($values as $id => $value)
    {
      if ($value && !is_numeric($value))
      {
        $this->errors[$id] = sprintf('"%s" is not an integer.', $value);
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
    include_partial('direct_rating', array(
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
