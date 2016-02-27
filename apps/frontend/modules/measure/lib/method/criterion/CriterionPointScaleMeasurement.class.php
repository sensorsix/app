<?php

/**
 * @property criterionMeasurementMethod $criterionMeasurement
 */
abstract class CriterionPointScaleMeasurement extends PointScaleMeasurement
{
  protected $criterionMeasurementMethod = null;

  public function __construct($map_data)
  {
    $this->prioritization = true;

    parent::__construct($map_data, 'criterion');

    $this->criterionMeasurementMethod = new CriterionMeasurementMethod();
  }

  public function load()
  {
    $this->criterionMeasurementMethod->setRole($this->role);
    $this->criterionMeasurementMethod->load();
    $this->collection = $this->criterionMeasurementMethod->getCriteria();
    $this->comments = $this->criterionMeasurementMethod->getComments();
  }

  public function render()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
    $this->criterionMeasurementMethod->render('Assign each criterion importance with regards to the Decision Objective');
    parent::render();
  }
}