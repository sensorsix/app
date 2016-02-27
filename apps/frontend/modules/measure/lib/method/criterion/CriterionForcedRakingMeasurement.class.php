<?php

/**
 * @property criterionMeasurementMethod $criterionMeasurement
 */
class CriterionForcedRankingMeasurement extends ForcedRankingMeasurement
{
  private $criterionMeasurementMethod = null;

  public function __construct($map_data, $load = true)
  {
    parent::__construct($map_data, 'criterion');

    $this->prioritization = true;

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
    $this->criterionMeasurementMethod->save($this->values, 'forced ranking', $this->response_id);
  }
}