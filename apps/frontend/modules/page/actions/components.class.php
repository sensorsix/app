<?php

class pageComponents extends sfComponents
{
  public function executeRegistration(sfWebRequest $request)
  {
    if ($this->getContext()->has('registration_form'))
    {
      $this->form = $this->getContext()->get('registration_form');
    }
    else
    {
      $this->form = new sfGuardQuickRegisterForm();
    }
  }

  public function executeScheduleDemo(sfWebRequest $request){
    $this->schedule_demo_form = new ScheduleDemoForm();

    if ($request->isMethod('post')) {
      $this->schedule_demo_form->bind($request->getParameter($this->schedule_demo_form->getName()));
      if ($this->schedule_demo_form->isValid())
      {
        $message = Swift_Message::newInstance()
          ->setFrom(sfConfig::get('app_sf_guard_plugin_default_from_email', 'noreply@sensorsix.com'))
          ->setTo(sfConfig::get('app_info_email'))
          ->setSubject('Schedule a demo')
          ->setBody($this->schedule_demo_form->getValue('email'))
          ->setContentType('text/html')
        ;

        $this->getMailer()->send($message);

        $this->getUser()->setFlash('schedule_notice', 'Thank you! We will get back to you as soon as possible');
      }
    }
  }
}