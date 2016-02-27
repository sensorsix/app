<?php

abstract class ForcedRankingMeasurement extends MeasurementMethod
{
  protected
    $collection = null,
    $comments  = null;

  public function __construct($map_data, $namespace)
  {
    parent::__construct($map_data, $namespace . '/forced_rating');
  }

  public function hasData()
  {
    return $this->collection->count() > 0;
  }

  public function render()
  {
    include_partial('forced_rating',
      array(
        'collection' => $this->collection,
        'comments'       => $this->comments,
        'prioritization' => $this->prioritization,
        'values' => $this->values
      ));
  }
}