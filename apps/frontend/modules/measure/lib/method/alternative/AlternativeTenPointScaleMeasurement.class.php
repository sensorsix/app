<?php

class AlternativeTenPointScaleMeasurement extends AlternativePointScaleMeasurement
{
  public function __construct($map_data)
  {
    $this->stars = 10;
    parent::__construct($map_data);
  }

  public function save()
  {
    $this->alternativeMeasurementMethod->save($this->values, 'ten point scale', $this->response_id);
  }
}