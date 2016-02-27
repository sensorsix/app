<?php

/**
 * PluginPaymentTransaction form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginPaymentTransactionForm extends BasePaymentTransactionForm
{
  public function setUp()
  {
    parent::setUp();

    $this->useFields(array(
      'refnumber',
      'is_payed',
      'date_payed',
      'amount',
    ));

    $this->widgetSchema['refnumber']->setAttribute('disabled', 'disabled');
    $this->validatorSchema['refnumber'] = new sfValidatorPass(array('required'=>false));
  }

  public function  bind(array $taintedValues = null, array $taintedFiles = null)
  {
    $taintedValues['refnumber'] = $this->getObject()->refnumber;
    parent::bind($taintedValues, $taintedFiles);
  }
  
}
