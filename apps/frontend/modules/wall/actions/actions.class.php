<?php

/**
 * wall actions.
 *
 * @package    dmp
 * @subpackage wall
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 * @property Wall $wall
 * @property Decision $decision
 * @property WallPost[]|Doctrine_Collection $posts
 */
class wallActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    $this->wall = Doctrine::getTable('Wall')->findOneBy('token', $request->getParameter('token', false));
    $this->forward404Unless($this->wall);
    $this->decision = $this->wall->Decision;
    $this->posts = Doctrine::getTable('WallPost')->findBy('wall_id', $this->wall->id);

    // Create log
    $log = new Log();
    $log->action = 'wall_visit';
    $log->information = json_encode(array('wall_id' => $this->wall->id));
    $log->user_id = $this->getUser()->getGuardUser()->id;
    $log->save();
  }
}
