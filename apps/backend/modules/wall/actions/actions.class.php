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
 * @property string $url
 */
class wallActions extends sfActions
{
  public function preExecute()
  {
    $context = $this->getContext();
    if ($context->getRequest()->hasParameter('decision_id')) {
      $context->getRouting()->setDefaultParameter('decision_id', $context->getRequest()->getParameter('decision_id'));
    }

    $this->forward404Unless($this->getUser()->verifyLightAccess($this->getContext()));
  }

  public function executeIndex(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $this->decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless(is_object($this->decision));

    $this->chartsHandler = new ChartsHandler($this->decision);

    $this->wall = Doctrine::getTable('Wall')->findOneBy('decision_id', $decision_id);
    $this->posts = Doctrine::getTable('WallPost')->findBy('wall_id', $this->wall->id);
    $this->url = $this->getContext()->getConfiguration()->generateFrontendUrl('wall', array('token' => $this->wall->token));
  }

  public function executeSaveComment(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($post = Doctrine::getTable('WallPost')->find($request->getParameter('id', false)));
    $post->title = $request->getParameter('title', '');
    $post->comment = $request->getParameter('comment', '');
    $post->save();

    $this->setLayout(false);
    return sfView::NONE;
  }

  public function executeDeletePost(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($post_id = $request->getParameter('id', false));
    if ($post = Doctrine::getTable('WallPost')->find($post_id)) {
      $post->delete();
    }

    return sfView::NONE;
  }

  public function executeExport(sfWebRequest $request)
  {
    //$this->forward404Unless(in_array($this->getUser()->getGuardUser()->account_type, array('Pro', 'Enterprise')));

    $decision_id = $request->getParameter('decision_id', false);
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless(is_object($decision));

    header('Content-type: application/force-download');
    header('Content-Disposition: attachment; filename="' . $decision->name . '.pptx"');

    $exporter = new WallPowerPointExporter();
    $exporter->setDecisionId($decision_id);
    $exporter->load();
    $exporter->export();

    exit;
  }
}
