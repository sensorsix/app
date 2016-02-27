<?php

/**
 * Payment actions.
 *
 * @package    lkms
 * @subpackage Payment
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class PaymentActions extends sfActions
{
  public function executePaypalSet(sfWebRequest $request)
  {
    $payment_type = $request->getParameter('payment_type');
    $price_list = PaymentTransaction::getPriceList();

    $payPalURL = '';
    if (array_key_exists($payment_type, $price_list))
    {
      /** @var sfGuardUser $user */
      $user = $this->getUser()->getGuardUser();
      $user->getLastNotPayedTransaction($payment_type);

      $gtw = new PaypalGateway(array(
        'payer_id'    => $user->id,
        'amount'      => $price_list[$payment_type],
        'success_url' => $this->generateUrl('payment\success', array('type' => $payment_type), true),
        'cancel_url'  => $this->generateUrl('payment\cancel', array(), true),
      ));

      $payPalURL = $gtw->setExpessCheckout();
    }

    if ($payPalURL)
    {
      $this->redirect($payPalURL);
    }
    else
    {
      $this->getUser()->setFlash('error', 'PayPal connection error');
      $this->redirect('/project/user/account');
    }
  }

  public function executePaymentSuccess(sfWebRequest $request)
  {
    $payment_type = $request->getParameter('type');
    /** @var sfGuardUser $user */
    $user = $this->getUser()->getGuardUser();
    /** @var PaymentTransaction $payment */
    $payment = $user->getLastNotPayedTransaction($payment_type);

    $gtw = new PaypalGateway(array(
      'amount' => $payment->amount,
      'success_url' => $this->generateUrl('payment\success', array('type' => $payment_type)),
      'cancel_url' => $this->generateUrl('payment\cancel'),
      'token' => $request->getParameter('token'),
      'payer_id' => $request->getParameter('PayerID'),
    ));
    
    $res = $gtw->doExpressCheckout();
    
    if ($res)
    {
      $payment->is_payed = true;
      $payment->date_payed = date('Y-m-d H:i:s');
      $payment->status_code = 'success';
      $payment->stamp = $request->getParameter('PayerID');
      $payment->save();
      $user->account_type = $payment->type;
      $user->credit = $payment->type == 'basic' ? 12 : -1;
      $user->last_payment_date = date('Y-m-d H:i:s');
      $user->save();

      $this->getUser()->setFlash('notice', 'The payment is accepted, thank you!');
    }
    else
    {
      $this->getUser()->setFlash('error', 'PayPal connection error');
    }
    $this->redirect('/project/user/account');
  }

  public function executePaymentCancel(sfWebRequest $request)
  {
    $this->payment = $payment = $this->getUser()->getGuardUser()->getLastNotPayedTransaction();
    if ($payment)
    {
      $payment->is_payed = false;
      $payment->status_code = 'cancel';
      $payment->date_payed = date('Y-m-d H:i:s');
      $payment->save();
    }
  }

  public function executePaymentReject(sfWebRequest $request)
  {
    $this->payment = $payment = $this->getUser()->getGuardUser()->getLastNotPayedTransaction();
    if ($payment)
    {
      $payment->is_payed = false;
      $payment->status_code = 'reject';
      $payment->date_payed = date('Y-m-d H:i:s');
      $payment->save();
    }
  }
}
