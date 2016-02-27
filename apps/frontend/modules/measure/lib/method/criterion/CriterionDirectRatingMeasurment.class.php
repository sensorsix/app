<?php

/**
 * @property CriterionMeasurementMethod $criterionMeasurement
 */
class CriterionDirectRatingMeasurement extends DirectRatingMeasurement
{
  /**
   * @var CriterionMeasurementMethod
   */
  private $criterionMeasurementMethod;

  public function __construct($map_data, $load = true)
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
    $this->criterionMeasurementMethod->render('');
    parent::render();
  }

  public function save()
  {
    $this->criterionMeasurementMethod->save($this->values, 'direct rating', $this->response_id);
  }
}