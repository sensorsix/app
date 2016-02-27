<?php

class AlternativeCommentMeasurement extends CommentMeasurement
{
  private $alternativeMeasurementMethod = null;

  public function __construct($map_data)
  {
    parent::__construct($map_data, 'alternative');

    $this->alternativeMeasurementMethod = new AlternativeMeasurementMethod($map_data['criterion_id']);
  }

  public function render()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
    $this->alternativeMeasurementMethod->render('Please comment on the alternatives');
    parent::render();
  }

  public function save()
  {
    $this->alternativeMeasurementMethod->save($this->values, 'comment', $this->response_id);
  }

  public function load()
  {
    $this->alternativeMeasurementMethod->setRole($this->role);
    $this->alternativeMeasurementMethod->load();
    $this->collection = $this->alternativeMeasurementMethod->getAlternatives();
    $this->comments = $this->alternativeMeasurementMethod->getComments();
  }
}
