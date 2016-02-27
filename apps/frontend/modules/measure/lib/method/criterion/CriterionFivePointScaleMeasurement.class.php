<?php

class CriterionFivePointScaleMeasurement extends CriterionPointScaleMeasurement
{
  public function __construct($map_data)
  {
    $this->stars = 5;
    $this->prioritization = true;
    parent::__construct($map_data);
  }

  public function save()
  {
    $this->criterionMeasurementMethod->save($this->values, 'five point scale', $this->response_id);
  }
}