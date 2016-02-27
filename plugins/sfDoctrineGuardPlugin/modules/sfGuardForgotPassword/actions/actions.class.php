<?php

require_once dirname(__FILE__).'/../lib/BasesfGuardForgotPasswordActions.class.php';

/**
 * sfGuardForgotPassword actions.
 * 
 * @package    sfGuardForgotPasswordPlugin
 * @subpackage sfGuardForgotPassword
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
class sfGuardForgotPasswordActions extends BasesfGuardForgotPasswordActions
{
  public function executeIndex($request)
  {
    $this->form = new sfGuardRequestForgotPasswordForm();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->user = $this->form->user;
        Doctrine_Core::getTable('sfGuardForgotPassword')
          ->createQuery('p')
          ->delete()
          ->where('p.user_id = ?', $this->user->id)
          ->execute();


        $forgotPassword = new sfGuardForgotPassword();
        $forgotPassword->user_id = $this->form->user->id;
        $forgotPassword->unique_key = md5(rand() + time());
        $forgotPassword->expires_at = new Doctrine_Expression('NOW()');
        $forgotPassword->save();

        $message = $this->getMailer()->compose(
          array(sfConfig::get('app_info_email') => sfConfig::get('app_sf_guard_plugin_default_from_email')),
          $this->form->user->email_address,
          'Forgot Password Request for '.$this->form->user->username
        );

        $message->setBody($this->getPartial('sfGuardForgotPassword/send_request', array('user' => $this->form->user, 'forgot_password' => $forgotPassword)));
        $message->setContentType('text/html');

        $this->getMailer()->send($message);

        $this->getUser()->setFlash('notice', 'Check your e-mail! You should receive something shortly!');
        $this->redirect('@sf_guard_signin');
      } else {
        $this->getUser()->setFlash('error', 'Invalid e-mail address!');
      }
    }
  }

  public function executeChange($request)
  {
    $this->forgotPassword = $this->getRoute()->getObject();
    $this->user = $this->forgotPassword->User;
    $this->form = new sfGuardChangeUserPasswordForm($this->user);

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $this->form->save();

        $this->_deleteOldUserForgotPasswordRecords();

        $this->getUser()->setFlash('notice', 'Password updated successfully!');
        $this->redirect('@sf_guard_signin');
      }
    }
  }

  private function _deleteOldUserForgotPasswordRecords()
  {
    Doctrine_Core::getTable('sfGuardForgotPassword')
      ->createQuery('p')
      ->delete()
      ->where('p.user_id = ?', $this->user->id)
      ->execute();
  }
}
