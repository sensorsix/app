<?php

class ChangeMeasurementRemoveRelations extends Doctrine_Migration_Base
{
  private
  $alternatives = array(),
  $criteria = array();

  public function preUp()
  {
  $collection = Doctrine_Query::create()->from('PlannedAlternativeMeasurement')->execute();

  foreach ($collection as $item) {
  $this->alternatives[$item->id]['alternative_id'] = $item->alternative_id;
  $this->alternatives[$item->id]['criterion_id']   = $item->criterion_id;
  }

  $collection = Doctrine_Query::create()->from('PlannedCriterionPrioritization')->execute();

  foreach ($collection as $item) {
  $this->criteria[$item->id] = $item->criterion_id;
  }
  }

  public function up()
  {
  $this->addColumn('alternative_measurement', 'criterion_id', 'integer', '8', array());

  $this->createForeignKey('alternative_measurement',
  'alternative_measurement_criterion_id_criterion_id',
  array(
  'name'   => 'alternative_measurement_criterion_id_criterion_id',
  'local'  => 'criterion_id',
  'foreign'  => 'id',
  'foreignTable' => 'criterion',
  'onUpdate'   => '',
  'onDelete'   => 'CASCADE',
  ));

  $this->addIndex('alternative_measurement',
  'criterion_id',
  array(
  'fields' =>
    array(
    0 => 'criterion_id',
    ),
  ));

  $this->dropForeignKey('alternative_measurement', 'aapi');
  $this->dropForeignKey('alternative_measurement', 'aapi_1');
  $this->dropForeignKey('criterion_prioritization', 'ccpi');
  $this->dropForeignKey('criterion_prioritization', 'ccpi_1');
  }

  public function postUp()
  {
  $measurements = Doctrine::getTable('AlternativeMeasurement')->findAll();

  foreach ($measurements as $measurement) {
  $measurement->criterion_id  = $this->alternatives[$measurement->alternative_head_id]['criterion_id'];
  $measurement->alternative_head_id = $this->alternatives[$measurement->alternative_head_id]['alternative_id'];

  $measurement->save();
  }

  $measurements = Doctrine::getTable('CriterionPrioritization')->findAll();

  foreach ($measurements as $measurement) {
  $measurement->criterion_head_id = $this->criteria[$measurement->criterion_head_id];
  if ($measurement->criterion_tail_id) {
  $measurement->criterion_tail_id = $this->criteria[$measurement->criterion_tail_id];
  }
  $measurement->save();
  }
  }

  public function down()
  {
  $this->dropForeignKey('alternative_measurement', 'alternative_measurement_criterion_id_criterion_id');

  $this->removeIndex('alternative_measurement', 'criterion_id',
  array(
  'fields' =>
    array(
    0 => 'criterion_id',
    ),
  ));

  $this->removeColumn('alternative_measurement', 'criterion_id');
  }
}


