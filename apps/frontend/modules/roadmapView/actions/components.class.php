<?php

class roadmapViewComponents extends sfComponents
{
  public function executeLogo(sfWebRequest $request)
  {
    /** @var Roadmap $roadmap */
    $roadmap = Doctrine::getTable('Roadmap')->findOneBy('token', $request->getParameter('token', false));
    $this->user = $roadmap->getUser();
    $this->response->setSlot('disable_top_menu', true);
  }
}