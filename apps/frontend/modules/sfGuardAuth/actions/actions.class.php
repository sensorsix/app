<?php

/*
 * This file is part of the symfony package.
 * (c) 2004-2006 Fabien Potencier <fabien.potencier@symfony-project.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once(sfConfig::get('sf_plugins_dir').'/sfDoctrineGuardPlugin/modules/sfGuardAuth/lib/BasesfGuardAuthActions.class.php');

/**
 *
 * @package    symfony
 * @subpackage plugin
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @version    SVN: $Id: actions.class.php 23319 2009-10-25 12:22:23Z Kris.Wallsmith $
 */
class sfGuardAuthActions extends BasesfGuardAuthActions
{

  public function executeSignin($request)
  {
    $user = $this->getUser();
    if ($user->isAuthenticated()) {
      return $this->redirect('@homepage');
    }

    $class = sfConfig::get('app_sf_guard_plugin_signin_form', 'sfGuardFormSignin');
    $this->form = new $class();

    if ($request->isMethod('post') && $request->hasParameter('signin')) {
      $this->form->bind($request->getParameter('signin'));
      if ($this->form->isValid()) {
        $values = $this->form->getValues();
        $this->getUser()->signin($values['user'], array_key_exists('remember', $values) ? $values['remember'] : false);

        // always redirect to a URL set in app.yml
        // or to the referer
        // or to the homepage
        $signinUrl = sfConfig::get('app_sf_guard_plugin_success_signin_url', $user->getReferer($request->getReferer()));

        // Create login log
        $log = new Log();
        $log->action = 'login';
        $log->user_id = $this->getUser()->getGuardUser()->id;
        $log->save();

        return $this->redirect('' != $signinUrl ? $signinUrl : '@homepage');
      }
    } else {
      if ($request->isXmlHttpRequest()) {
        $this->getResponse()->setHeaderOnly(true);
        $this->getResponse()->setStatusCode(401);

        return sfView::NONE;
      }

      // if we have been forwarded, then the referer is the current URL
      // if not, this is the referer of the current request
      $user->setReferer($this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());

      $module = sfConfig::get('sf_login_module');
      if ($this->getModuleName() != $module) {
        return $this->redirect($module.'/'.sfConfig::get('sf_login_action'));
      }

      $this->getResponse()->setStatusCode(401);
    }
  }

  /**
   * @param sfWebRequest $request
   * @throws sfStopException
   */
  public function executeAuth(sfWebRequest $request)
  {
    $client_id = sfConfig::get('app_google_api_client_id');
    $redirect_uri = $this->generateUrl('sf_guard_auth', array('auth_method' => 'google'), true);

    if ($request->hasParameter('code')) {
      $client_secret = sfConfig::get('app_google_api_client_secret');
      $this->redirect = false;

      $params = array(
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'grant_type' => 'authorization_code',
        'code' => $request->getParameter('code'),
        'redirect_uri' => $redirect_uri,
      );

      $browser = new sfWebBrowser(array(), null, array('ssl_verify_host' => false, 'ssl_verify' => false));
      $result = $browser->post('https://accounts.google.com/o/oauth2/token', $params);
      if ($result->getResponseCode() == 200){
        $this->redirect = true;
        $this->getUser()->setAttribute('google_token', $result->getResponseText());
      }
    }else{
      $scope = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';

      $this->redirect('https://accounts.google.com/o/oauth2/auth?client_id=' . $client_id . '&redirect_uri=' . urlencode($redirect_uri) . '&scope=' . $scope . '&response_type=code&access_type=offline');
    }
  }
}
