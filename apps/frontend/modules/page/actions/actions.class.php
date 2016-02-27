<?php

/**
 * default actions.
 *
 * @package    dmp
 * @subpackage default
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class pageActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
  }

  public function executeAbout(sfWebRequest $request)
  {
    $this->getResponse()->setSlot('disable_support', true);
  }

  public function executeSupport(sfWebRequest $request)
  {
    $this->getResponse()->setSlot('disable_support', true);
  }

  public function executeCustomers(sfWebRequest $request)
  {
  }

  public function executeContact(sfWebRequest $request)
  {
  }

  public function executeProducts(sfWebRequest $request)
  {
  }

  public function executePricing(sfWebRequest $request)
  {
  }

  public function executeTerms(sfWebRequest $request)
  {
  }

  public function executeSubscribe(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $email = $request->getParameter('email');
    $validator = new sfValidatorEmail();

    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
    try
    {
      $validator->clean($email);

      $MailChimp = new MailChimp(sfConfig::get('app_mailchimp_api_key'));
      $result = $MailChimp->call('lists/subscribe', array(
        'id'                => sfConfig::get('app_mailchimp_list_id'),
        'email'             => array('email'=> $email),
        'merge_vars'        => array(),
        'double_optin'      => false,
        'update_existing'   => true,
        'replace_interests' => false,
        'send_welcome'      => false,
      ));

      return $this->renderText(json_encode(array('status' => 'ok')));
    }
    catch (sfValidatorError $e)
    {
      return $this->renderText(json_encode(array('status' => 'invalid')));
    }
  }

  public function executePromo(sfWebRequest $request)
  {
    $this->form = new sfGuardPromoRegisterForm();

    if ($request->getMethod() === sfRequest::POST) {
      if ($this->getUser()->isAuthenticated()) {
        $this->getUser()->setFlash('notice', 'You are already registered and signed in!');
        $this->redirect('/project');
      }

      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid()) {
        $user = $this->form->save();

        $message = $this->getMailer()->compose(
          array(sfConfig::get('app_info_email') => sfConfig::get('app_sf_guard_plugin_default_from_email')),
          $user->email_address,
          '[Sensorsix] Confirm your email address'
        );

        $message->setBody($this->getPartial('confirmation_email', array('user' => $user)));
        $message->setContentType('text/html');

        $this->getMailer()->send($message);

        $this->getUser()->setFlash('notice', 'Check your e-mail! You should verify your email address.');

        $this->redirect('@sf_guard_signin');
      }
    }
  }
}
