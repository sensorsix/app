<?php

class measureComponents extends sfComponents
{
  public function executeLogo(sfWebRequest $request)
  {
    /** @var Role $role */
    $role = Doctrine::getTable('Role')->findOneBy('token', $request->getParameter('token', false));
    $this->user = $role->Decision->User;
    $this->response->setSlot('disable_top_menu', true);
  }
}