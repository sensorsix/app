<?php

require_once dirname(__FILE__).'/../lib/BasesfGuardRegisterActions.class.php';

/**
 * sfGuardRegister actions.
 *
 * @package    guard
 * @subpackage sfGuardRegister
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12479 2008-10-31 10:54:40Z jwage $
 */
class sfGuardRegisterActions extends BasesfGuardRegisterActions
{
  public function executeQuick(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated())
    {
      $this->getUser()->setFlash('notice', 'You are already registered and signed in!');
      $this->redirect('/project');
    }

    $form = new sfGuardQuickRegisterForm();

    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid())
    {
      $user = $form->save();
      $this->sendConfirmationEmail($user);
    }
    $this->getContext()->set('registration_form', $form);
    $this->getResponse()->addStylesheet('dist/landing.css?v=3', 'last');
    $this->getResponse()->addStylesheet('/libs/jquery-colorbox/colorbox.css?v=1.5.14', 'last');
    $this->getResponse()->addJavascript('mailchimp_subscribe.js', 'last');
    $this->getResponse()->addJavascript('/libs/jquery-colorbox/jquery.colorbox-min.js?v=1.5.14', 'last');
    $this->setLayout('homepage');
    $this->setTemplate('index', 'page');
  }

  public function executeIndex(sfWebRequest $request)
  {
    if ($this->getUser()->isAuthenticated() && $this->getUser()->getGuardUser()->account_type != 'Trial') {
      $this->getUser()->setFlash('notice', 'You are already registered and signed in!');
      $this->redirect('/project');
    }

    if ($this->getUser()->isAuthenticated()) {
      $user = $this->getUser()->getGuardUser();
      $user->email_address = '';
      $user->account_type = 'Free';
      $this->form = new sfGuardRegisterForm($user);
    } else {
      $this->form = new sfGuardRegisterForm();

      if ($this->getUser()->getAttribute('google_token')){
        $google_token = json_decode($this->getUser()->getAttribute('google_token'));

        $browser = new sfWebBrowser(array(), null, array('ssl_verify_host' => false, 'ssl_verify' => false));
        $result = $browser->get('https://www.googleapis.com/oauth2/v1/userinfo?access_token=' . $google_token->access_token);
        if ($result->getResponseCode() == 200){
          $response_text = json_decode($result->getResponseText());
          if (property_exists($response_text, 'email')){
            $user_exists = sfGuardUserTable::getInstance()->createQuery('u')
              ->where('email_address = ?', $response_text->email)
              ->fetchOne();

            if (is_object($user_exists)) {
              $this->getUser()->setAttribute('google_token', null);

              if ($user_exists->is_active) {
                $this->getUser()->signIn($user_exists);
                $this->redirect('/project');
              }else{
                $this->getUser()->setFlash('notice', 'Check your e-mail! You should verify your email address.');
                $this->redirect('@sf_guard_signin');
              }
            }

            $this->getUser()->setAttribute('google_token_info', array($response_text->email => array(
              'given_name' => $response_text->given_name,
              'family_name' => $response_text->family_name,
            )));

            $this->form->setDefault('email_address', $response_text->email);
//            $this->form->getWidget('email_address')->setAttribute('readonly', 'readonly');
          }
        }
      }
    }

    if ($request->isMethod('post')) {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid()) {
        $user = $this->form->save();

        $google_token_info = $this->getUser()->getAttribute('google_token_info');
        $this->getUser()->setAttribute('google_token', null);
        $this->getUser()->setAttribute('google_token_info', null);
        if (is_array($google_token_info) && array_key_exists($user->email_address, $google_token_info)){
          $user->first_name = $google_token_info[$user->email_address]['given_name'];
          $user->last_name = $google_token_info[$user->email_address]['family_name'];
          $user->is_active = true;
          @$user->save();
          $this->getUser()->signIn($user);
          $this->redirect('/project');
        }else{
          $this->sendConfirmationEmail($user);
        }
      }
    }
  }

  private function sendConfirmationEmail(sfGuardUser $user)
  {
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

  public function executeConfirmation(sfWebRequest $request)
  {
    $token = $request->getParameter('token');
    $this->forward404Unless($token);
    /** @var sfGuardUser $user */
    $user = Doctrine::getTable('sfGuardUser')->findOneBy('salt', $token);
    $this->forward404Unless(is_object($user));
    if (!$user->is_active) {
      $user->is_active = true;
      $user->save();
      $this->getUser()->setFlash('notice', 'Your email is confirmed');
    }
    $this->redirect('@sf_guard_signin');
  }
}