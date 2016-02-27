<?php
 
abstract class CommentMeasurement extends MeasurementMethod
{
  protected
    $collection = null,
    $comments  = null;

  public function __construct($map_data, $namespace)
  {
    parent::__construct($map_data, $namespace . '/comment');
  }

  public function hasData()
  {
    return $this->collection->count() > 0;
  }

  public function render()
  {
    include_partial('comment', array(
      'collection'     => $this->collection,
      'values'         => $this->values,
      'comments'       => $this->comments,
      'prioritization' => $this->prioritization
      )
    );
  }
}
