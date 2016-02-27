<?php

class sfWidgetFormSchemaFormatterBootstrap extends sfWidgetFormSchemaFormatter
{
  protected
    $rowFormat       = "<div class=\"form-group %error_class%\">\n  %error%%label%\n  %field%%help%\n%hidden_fields%</div>\n",
    $errorListFormatInARow  = "  <ul class=\"alert alert-danger controls input-xlarge\">\n%errors%  </ul>\n",
    $errorRowFormat  = "<li>\n%errors%</li>\n",
    $helpFormat      = '<span class="help-inline">%help%</span>',
    $decoratorFormat = "<ul>\n  %content%</ul>",
    $validatorSchema = null;


  /**
   * Constructor
   * @param sfWidgetFormSchema $widgetSchema
   * @param sfValidatorSchema $validatorSchema
   */
  public function __construct(sfWidgetFormSchema $widgetSchema, sfValidatorSchema $validatorSchema)
  {
    $this->widgetSchema = $widgetSchema;
    $this->validatorSchema = $validatorSchema;
  }

  public function generateLabelName($name)
  {
    $labelName = parent::generateLabelName($name);

    if (isset($this->validatorSchema[$name]) && $this->validatorSchema[$name]->getOption('required'))
    {
      $labelName .= '<sup class="required">*</sup>';
    }

    return $labelName;
  }

  public function formatRow($label, $field, $errors = array(), $help = '', $hiddenFields = null)
  {
    return strtr($this->getRowFormat(), array(
      '%label%'         => $label,
      '%field%'         => $field,
      '%error_class%'   => $errors ? ' error' : '',
      '%error%'         => $this->formatErrorsForRow($errors),
      '%help%'          => $this->formatHelp($help),
      '%hidden_fields%' => null === $hiddenFields ? '%hidden_fields%' : $hiddenFields,
    ));
  }
}
