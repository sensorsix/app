<?php

abstract class PairwiseComparisonMeasurement extends MeasurementMethod
{
  protected
    $head = null,
    $tail = null;

  public function __construct($map_data, $namespace)
  {
    parent::__construct($map_data, $namespace . '/pairwise_comparison', 1);
  }

  public function hasData()
  {
    return is_object($this->head) && is_object($this->tail);
  }

  public function render()
  {
    include_partial('pairwise_comparison', array('head' => $this->head, 'tail' => $this->tail, 'value' => $this->values));
  }
}
