<?php
class ScheduleDemoForm extends sfForm
{

  public function configure()
  {

    $this->setWidgets(array(
      'email'   => new sfWidgetFormInputText()
    ));

    $this->setValidators(array(
      'email'   => new sfValidatorEmail()
    ));

    $this->widgetSchema->setNameFormat('schedule_demo[%s]');
  }
}