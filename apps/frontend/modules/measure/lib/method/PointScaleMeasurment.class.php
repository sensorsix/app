<?php

abstract class PointScaleMeasurement extends MeasurementMethod
{
  protected
    $stars = 0,
    $collection,
    $comments;

  public function __construct($map_data, $namespace)
  {
    parent::__construct($map_data, $namespace . '/point_scale');
  }

  public function hasData()
  {
    return $this->collection->count() > 0;
  }

  public function render()
  {
    include_partial('point_scale',
      array(
        'collection'     => $this->collection,
        'stars'          => $this->stars,
        'values'         => $this->values,
        'comments'       => $this->comments,
        'prioritization' => $this->prioritization,
        'role_id'        => $this->role->id
      )
    );
  }
}
