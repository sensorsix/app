<?php

class laWidgetFileUpload extends sfWidgetForm
{
  /**
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetFormInput
   */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addOption('partial');
    $this->addOption('buttons', array('add_file' => 'Add files...', 'upload' => 'Start upload', 'cancel' => 'Cancel upload'));
    $this->addOption('module_partial', false);
    $this->addOption('include_js', false);
  }

  public function getStylesheets()
  {
    return array('/laWidgetFileUploadPlugin/css/jquery.fileupload-ui.css' => 'all');
  }

  public function getJavaScripts()
  {
    return array(
      '/laWidgetFileUploadPlugin/js/vendor/jquery.ui.widget.js',
      '/laWidgetFileUploadPlugin/js/vendor/tmpl.min.js',
      '/laWidgetFileUploadPlugin/js/vendor/load-image.min.js',
      '/laWidgetFileUploadPlugin/js/jquery.iframe-transport.js',
      '/laWidgetFileUploadPlugin/js/jquery.fileupload.js',
      '/laWidgetFileUploadPlugin/js/jquery.fileupload-fp.js',
      '/laWidgetFileUploadPlugin/js/jquery.fileupload-ui.js',
    );
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $default_buttons = array('add_file' => 'Add files...', 'upload' => 'Start upload', 'cancel' => 'Cancel upload');
    $buttons = $this->getOption('buttons') + $default_buttons;

    $module_partial = $this->getOption('module_partial');
    if ($module_partial)
    {
      sfApplicationConfiguration::getActive()->loadHelpers('Partial');
      include_partial($module_partial, array(
          'widget'     => $this,
          'name'       => $name,
          'value'      => $value,
          'attributes' => $attributes,
          'errors'     => $errors,
          'buttons'    => $buttons
        )
      );
    }
    else
    {
      $context = sfContext::getInstance();
      $view = new sfPartialView($context, '', '', '');
      $plugin_path = sfConfig::get('sf_plugins_dir'). DIRECTORY_SEPARATOR . 'laWidgetFileUploadPlugin';
      $view->setTemplate($plugin_path . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . '_upload_widget.php');
      $view->setPartialVars(array('widget' => $this, 'buttons' => $buttons));
      echo $view->render();
    }
  }
}
