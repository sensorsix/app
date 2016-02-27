<?php

class sfWidgetFormSchemaFormatterBootstrapHorizontal extends sfWidgetFormSchemaFormatterBootstrap
{
    protected
        $rowFormat = "<div class=\"form-group %error_class%\">\n  %error%%label%\n  <div class='%input-wrapper-class%'>%field%%help%\n%hidden_fields%</div></div>\n",
        $errorListFormatInARow = "  <div class=\"alert alert-danger controls\">\n%errors%  </div>\n",
        $errorRowFormat = "<span>\n%errors%</span>\n",
        $helpFormat = '<span class="help-inline">%help%</span>',
        $decoratorFormat = "<ul>\n  %content%</ul>",
        $validatorSchema = null;

    protected $labelWidth = 4;

    /**
     * Generates label tag
     * Updates label class
     *
     * @param string $name
     * @param array  $attributes
     *
     * @return string
     */
    public function generateLabel($name, $attributes = array())
    {
        $classes             = array_key_exists('class', $attributes) ? explode(' ', $attributes['class']) : array();
        $classes[]           = 'col-xs-' . $this->labelWidth;
        $classes[]           = 'control-label';
        $attributes['class'] = implode(' ', array_unique($classes));

        return parent::generateLabel($name, $attributes);
    }

    /**
     * Gets row format
     * Replaces input wrapper class
     *
     * @return string
     */
    public function getRowFormat()
    {
        return strtr($this->rowFormat, array('%input-wrapper-class%' => 'col-xs-' . (12 - $this->labelWidth)));
    }

}
