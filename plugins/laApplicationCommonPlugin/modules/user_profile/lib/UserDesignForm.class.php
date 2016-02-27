<?php

class UserDesignForm extends BasesfGuardUserForm
{
  protected $formatterName = 'bootstrap_horizontal';

  public function configure()
  {
    $this->widgetSchema['logo_file']  = new sfWidgetFormInputFileEditable(array(
      'file_src'  => '/uploads/logo/' . $this->getObject()->logo_file,
      'is_image'  => true,
      'edit_mode' => !empty($this->getObject()->logo_file)
    ));
    $this->validatorSchema['logo_file_delete'] = new sfValidatorPass();
    $this->validatorSchema['logo_file'] = new sfValidatorFile(array(
      'mime_types' => 'web_images',
      'path' => sfConfig::get('sf_upload_dir') . '/logo',
      'required' => false
    ));

    $this->widgetSchema->setLabel('logo_url', 'Logo URL');
    $this->validatorSchema['logo_url'] = new sfValidatorUrl(array('max_length' => 255, 'required' => false));

    $this->useFields(array('header_color', 'logo_url', 'logo_file'));

    foreach ($this->widgetSchema->getFields() as $widget) {
      if (in_array($widget->getOption('type'), array( 'text', 'password' )) || $widget instanceof sfWidgetFormChoiceBase) {
        $widget->setAttribute('class', 'form-control');
      }
    }
  }
}