<?php

class CriterionTenPointScaleMeasurement extends CriterionPointScaleMeasurement
{
  public function __construct($map_data)
  {
    $this->stars = 10;
    $this->prioritization = true;
    parent::__construct($map_data);
  }

  public function save()
  {
    $this->criterionMeasurementMethod->save($this->values, 'ten point scale', $this->response_id);
  }
}