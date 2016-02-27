<?php

/**
 * osWidgetFormText
 *
 * @package
 * @subpackage
 * @author     spin
 * @version    SVN: $Id$
 *
 *
 */
class laWidgetFormText extends sfWidgetFormInput
{
  protected function configure($options = array(), $attributes = array())
  {
    $this->addRequiredOption('model');
    $this->addOption('method', '__toString');
    $this->addOption('object', null);
    $this->addOption('store_value', false);
    $this->addOption('content_tag', 'div');
    $this->addOption('additional_text', '');

    parent::configure($options, $attributes);
  }

  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $method = $this->getOption('method');
    $object = $this->getOption('object') ? $this->getOption('object') : Doctrine::getTable($this->getOption('model'))->find($value);
    if (!$object)
    {
      return $this->renderContentTag($this->getOption('content_tag'), '', $attributes);
    }

    $value_html = '';

    if(false !== $this->getOption('store_value'))
    {
      $value_html = $this->renderTag('input', array('type' => 'hidden', 'name' => $name, 'value' => $value));
    }

    if ($this->getOption('additional_text'))
    {
      $additional_text = '&nbsp;' . $this->getOption('additional_text');
    }
    else
    {
      $additional_text = '';
    }
    return $this->renderContentTag($this->getOption('content_tag'), $object->$method() . $additional_text, $attributes) . $value_html;
  }
}