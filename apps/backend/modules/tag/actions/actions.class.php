<?php

/**
 * tag actions.
 *
 * @package    dmp
 * @subpackage tag
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class tagActions extends BackendDefaultActions
{
  protected $model = 'Tag';

  public function executeIndex(sfWebRequest $request)
  {
    $this->page = $request->getParameter('page') ?: 1;

    $team_members = sfGuardUserTable::getInstance()->getUsersInTeamQuery($this->getUser()->getGuardUser())->execute();
    $team_members_id = array();
    foreach($team_members as $team_member){
      $team_members_id[] = $team_member->id;
    }

    $this->tags = Doctrine_Core::getTable('Tag')
      ->createQuery('a')
      ->whereIn('a.user_id', $team_members_id)
      ->limit('10')->orderBy('id DESC')->offset(($this->page - 1) * 10)
      ->execute();

    $this->tags_count = Doctrine_Core::getTable('Tag')
      ->createQuery('a')
      ->whereIn('a.user_id', $team_members_id)
      ->execute()->count();
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->form = new TagForm();
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST));

    $this->form = new TagForm();

    $this->processForm($request, $this->form);

    $this->setTemplate('new');
  }

  public function executeEdit(sfWebRequest $request)
  {
    $this->forward404Unless($tag = Doctrine_Core::getTable('Tag')->find(array($request->getParameter('id'))), sprintf('Object tag does not exist (%s).', $request->getParameter('id')));

    $team_members = sfGuardUserTable::getInstance()->getUsersInTeamQuery($this->getUser()->getGuardUser())->execute();
    $team_members_id = array();
    foreach($team_members as $team_member){
      $team_members_id[] = $team_member->id;
    }

    if (!in_array($tag->user_id, $team_members_id)){
      $this->forward404Unless(array(), sprintf('Object tag does not exist (%s).', $request->getParameter('id')));
    }

    $this->form = new TagForm($tag);
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($tag = Doctrine_Core::getTable('Tag')->find(array($request->getParameter('id'))), sprintf('Object tag does not exist (%s).', $request->getParameter('id')));
    $this->form = new TagForm($tag);

    $team_members = sfGuardUserTable::getInstance()->getUsersInTeamQuery($this->getUser()->getGuardUser())->execute();
    $team_members_id = array();
    foreach($team_members as $team_member){
      $team_members_id[] = $team_member->id;
    }

    if (in_array($tag->user_id, $team_members_id)){
      $this->processForm($request, $this->form);
    }

    $this->setTemplate('edit');
  }

  public function executeDelete(sfWebRequest $request)
  {
    $request->checkCSRFProtection();

    $this->forward404Unless($tag = Doctrine_Core::getTable('Tag')->find(array($request->getParameter('id'))), sprintf('Object tag does not exist (%s).', $request->getParameter('id')));

    $team_members = sfGuardUserTable::getInstance()->getUsersInTeamQuery($this->getUser()->getGuardUser())->execute();
    $team_members_id = array();
    foreach($team_members as $team_member){
      $team_members_id[] = $team_member->id;
    }

    if (in_array($tag->user_id, $team_members_id)){
      $tagAlternatives = TagAlternativeTable::getInstance()->findByTagId($tag->id);
      foreach($tagAlternatives as $tagAlternative){
        Doctrine_Query::create()->delete('Graph')->where('decision_id = ?', $tagAlternative->Alternative->decision_id)->execute();
      }

      Doctrine_Query::create()->delete('TagAlternative')->where('tag_id = ?', $tag->id)->execute();
      Doctrine_Query::create()->delete('TagDecision')->where('tag_id = ?', $tag->id)->execute();

      $tag->delete();
    }

    $this->redirect('tag/index');
  }

  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $tag = $form->save();

      $this->redirect('tag/edit?id='.$tag->getId());
    }
  }
}
