<?php
/**
 * PaymentMailer - provides a function to send booking invoice by mail
 *
 * @author karovkin
 */
class PaymentMailer
{
  
  public static function sendInvoice($booking, $sendto)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');

    $mailer =  sfContext::getInstance()->getMailer();
    $message = $mailer->compose(
      array('maakler@lkm.ee' => 'lkm.ee'),
        $sendto,
        'Invoice'
    );
    $message->setBody(get_partial('Payment/invoice_email', array('booking'=>$booking)), 'text/html');

    $mailer->send($message);
  }

  public static function sendAdminNotification($booking)
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Partial');

    $mailer =  sfContext::getInstance()->getMailer();
    $body = '<html><body>'.get_component('Payment', 'adminNotificationEmail', array('booking'=>  $booking) ).'</body></html>';
    $message = $mailer->compose(
      array('maakler@lkm.ee' => 'lkm.ee'),
        sfConfig::get('app_admin_email'),
        ''
    );
    $message->setBody($body, 'text/html');

    $mailer->send($message);
  }
}
