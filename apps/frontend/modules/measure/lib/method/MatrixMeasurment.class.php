<?php

class MatrixMeasurement extends MeasurementMethod
{
  protected
    $alternatives = null,
    $criteria = null,
    $measurement = array();

  public function __construct($map_data, $load = true)
  {
    parent::__construct($map_data, 'matrix');

    if ($load)
    {
      $plannedAlternativeMeasurement = Doctrine::getTable('PlannedAlternativeMeasurement')->findByRoleId($map_data['role_id']);
      $planned_criterion_ids = array();
      $planned_alternative_ids = array();
      foreach ($plannedAlternativeMeasurement as $measurement)
      {
        $planned_criterion_ids[$measurement->criterion_id] = true;
        $planned_alternative_ids[$measurement->alternative_id] = true;
        $this->measurement[$measurement->criterion_id][$measurement->alternative_id] = array($measurement->criterion_id, $measurement->alternative_id);
      }

      $this->alternatives = Doctrine::getTable('Alternative')->getPlanned(array_keys($planned_alternative_ids));
      $this->criteria = Doctrine::getTable('Criterion')->getPlanned(array_keys($planned_criterion_ids));
    }
    $this->values = sfContext::getInstance()->getUser()->getAttribute('values', array(), $this->namespace);
  }

  public function hasData()
  {
    return $this->alternatives->count() > 0 && $this->criteria->count() > 0;
  }

  public function render()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');
    include_partial('matrix_measurement', array(
        'alternatives' => $this->alternatives,
        'criteria'     => $this->criteria,
        'measurement'  => $this->measurement,
        'values'       => $this->values
      )
    );
  }

  public function save()
  {
    foreach ($this->values as $criterion_id => $alternatives)
    {
      foreach ($alternatives as $alternative_id => $score)
      {
        if ($this->role->updateable)
        {
          $alternativeMeasurement = AlternativeMeasurementTable::getInstance()->getOneForSave($this->response_id, $alternative_id, $criterion_id);
          if (!$alternativeMeasurement)
          {
            $alternativeMeasurement = new AlternativeMeasurement();
          }
        }
        else
        {
          $alternativeMeasurement = new AlternativeMeasurement();
        }

        $alternativeMeasurement->alternative_head_id = $alternative_id;
        $alternativeMeasurement->criterion_id = $criterion_id;
        $alternativeMeasurement->rating_method = 'five point scale';
        $alternativeMeasurement->response_id = $this->response_id;
        $alternativeMeasurement->score = $score;
        $alternativeMeasurement->save();
      }
    }
  }
}