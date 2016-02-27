<?php

/**
 * @property AlternativeMeasurementMethod $alternativeMeasurementMethod
 */
class AlternativeForcedRankingMeasurement extends ForcedRankingMeasurement
{
  protected $alternativeMeasurementMethod = null;

  public function __construct($map_data)
  {
    parent::__construct($map_data, 'alternative');

    $this->alternativeMeasurementMethod = new AlternativeMeasurementMethod($map_data['criterion_id']);
  }

  public function load()
  {
    $this->alternativeMeasurementMethod->setRole($this->role);
    $this->alternativeMeasurementMethod->load();
    $this->collection = $this->alternativeMeasurementMethod->getAlternatives();
    $this->comments = $this->alternativeMeasurementMethod->getComments();
  }

  public function render()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
    $this->alternativeMeasurementMethod->render('');
    parent::render();
  }

  public function save()
  {
    $this->alternativeMeasurementMethod->save($this->values, 'forced ranking', $this->response_id);
  }
}