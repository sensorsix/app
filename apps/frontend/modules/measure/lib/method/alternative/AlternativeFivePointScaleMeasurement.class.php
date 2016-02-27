<?php

class AlternativeFivePointScaleMeasurement extends AlternativePointScaleMeasurement
{
  public function __construct($map_data)
  {
    $this->stars = 5;
    parent::__construct($map_data);
  }

  public function save()
  {
    $this->alternativeMeasurementMethod->save($this->values, 'five point scale', $this->response_id);
  }
}