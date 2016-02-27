<?php

/**
 * @property AlternativeMeasurementMethod $alternativeMeasurementMethod
 */
class AlternativeDirectFloatMeasurement extends DirectFloatMeasurement
{
  /**
   * @var AlternativeMeasurementMethod
   */
  protected $alternativeMeasurementMethod;

  public function __construct($map_data)
  {
    parent::__construct($map_data, 'alternative');

    $this->alternativeMeasurementMethod = new AlternativeMeasurementMethod($map_data['criterion_id']);
  }

  public function render()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
    $this->alternativeMeasurementMethod->render('');
    parent::render();
  }

  public function save()
  {
    $this->alternativeMeasurementMethod->save($this->values, 'direct float', $this->response_id);
  }

  public function load()
  {
    $this->alternativeMeasurementMethod->setRole($this->role);
    $this->alternativeMeasurementMethod->load();
    $this->collection = $this->alternativeMeasurementMethod->getAlternatives();
    $this->comments = $this->alternativeMeasurementMethod->getComments();
  }
}