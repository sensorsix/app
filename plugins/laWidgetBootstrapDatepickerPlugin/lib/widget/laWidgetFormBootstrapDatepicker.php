<?php

class laWidgetFormBootstrapDatepicker extends sfWidgetFormInputText
{
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);
    $this->addOption('format', 'dd-mm-yyyy');
  }

  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The value displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    use_helper('JavascriptBase');
    $element_id = $this->generateId($name, $value);

    $value = $value ? date('d-m-Y', strtotime($value)) : '';

    $js_script = javascript_tag(sprintf('$(function(){$("#%s").datepicker({weekStart:1, format: "%s"});});', $element_id, $this->getOption('format')));

    return parent::render($name, $value, $attributes, $errors) . $js_script;
  }

  public function getStylesheets()
  {
    return array('/laWidgetBootstrapDatepickerPlugin/css/datepicker.css' => 'all');
  }

  public function getJavaScripts()
  {
    return array(
      '/laWidgetBootstrapDatepickerPlugin/js/bootstrap-datepicker.js',
    );
  }
}
