<?php

/**
 * Base project form.
 *
 * @package    dmp
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: BaseForm.class.php 20147 2009-07-13 11:46:57Z FabianLange $
 */
class BaseForm extends sfFormSymfony
{
    protected $formatterName = 'bootstrap';

    public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
    {
        parent::__construct($defaults, $options, $CSRFSecret);

        $bootstrap_decorator              = new sfWidgetFormSchemaFormatterBootstrap($this->getWidgetSchema(), $this->getValidatorSchema());
        $bootstrap_horizontal             = new sfWidgetFormSchemaFormatterBootstrapHorizontal($this->getWidgetSchema(), $this->getValidatorSchema());
        $bootstrap_horizontal_short_label = new sfWidgetFormSchemaFormatterBootstrapHorizontalShortLabel($this->getWidgetSchema(), $this->getValidatorSchema());
        $this->getWidgetSchema()->addFormFormatter('bootstrap', $bootstrap_decorator);
        $this->getWidgetSchema()->addFormFormatter('bootstrap_horizontal', $bootstrap_horizontal);
        $this->getWidgetSchema()->addFormFormatter('bootstrap_horizontal_shortlabel', $bootstrap_horizontal_short_label);
        $this->setFormatter();
    }

    /**
     * Sets widget formatter
     */
    public function setFormatter()
    {
        $this->getWidgetSchema()->setFormFormatterName($this->formatterName);
    }

}
