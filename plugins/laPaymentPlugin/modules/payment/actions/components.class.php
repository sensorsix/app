<?php

/**
 * default actions.
 *
 * @package    onsite
 * @subpackage default
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z fabien $
 */
class PaymentComponents extends sfComponents
{
  /**
   * payment form
   *
   * @param sfWebRequest $request
   */
  public function executePaymentForm(sfWebRequest $request) 
  {
    $choices = array();
    $account_type = $this->getUser()->getGuardUser()->account_type;
    switch ($account_type)
    {
      case 'Trial':
        $choices = array_merge($choices, array('basic' => 'Basic - $19'));
      case 'Basic':
        $choices = array_merge($choices, array('pro' => 'Pro - $99'));
      case 'Pro':
        $choices = array_merge($choices, array('enterprise' => 'Enterprise - $499'));
    }

    if ($account_type != 'Enterprise')
    {
      $this->widget = new sfWidgetFormChoice(array('choices' => $choices), array('class' => 'form-control'));
    }

    $this->gtw = new PaypalGateway();
  }

  public function executeAdminNotificationEmail(sfWebRequest $request)
  {
    $this->form = $form =  new PaymentTransaction($this->payment);
  }
}
