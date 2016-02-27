<?php

/**
 * Scripts filter form base class.
 *
 * @package    dmp
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseScriptsFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'backend_top'     => new sfWidgetFormFilterInput(),
      'backend_bottom'  => new sfWidgetFormFilterInput(),
      'frontend_top'    => new sfWidgetFormFilterInput(),
      'frontend_bottom' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'backend_top'     => new sfValidatorPass(array('required' => false)),
      'backend_bottom'  => new sfValidatorPass(array('required' => false)),
      'frontend_top'    => new sfValidatorPass(array('required' => false)),
      'frontend_bottom' => new sfValidatorPass(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('scripts_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'Scripts';
  }

  public function getFields()
  {
    return array(
      'id'              => 'Number',
      'backend_top'     => 'Text',
      'backend_bottom'  => 'Text',
      'frontend_top'    => 'Text',
      'frontend_bottom' => 'Text',
    );
  }
}
