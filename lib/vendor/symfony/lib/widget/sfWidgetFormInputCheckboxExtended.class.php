<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfWidgetFormInputCheckboxExtended represents an HTML checkbox tag.
 *
 * @package    symfony
 * @subpackage widget
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: sfWidgetFormInputCheckboxExtended.class.php 30762 2010-08-25 12:33:33Z fabien $
 */
class sfWidgetFormInputCheckboxExtended extends sfWidgetFormInputCheckbox
{

  /**
   * Renders the widget.
   *
   * @param  string $name        The element name
   * @param  string $value       The this widget is checked if value is not null
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    if (null !== $value && $value !== false && $value !== "")
    {
      $attributes['checked'] = 'checked';
    }

    if (!isset($attributes['value']) && null !== $this->getOption('value_attribute_value'))
    {
      $attributes['value'] = $this->getOption('value_attribute_value');
    }

    return parent::render($name, null, $attributes, $errors);
  }
}
