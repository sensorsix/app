<?php

/**
 * decision actions.
 *
 * @package    dmp
 * @subpackage decision
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class decisionActions extends BackendDefaultActions
{
  protected $model = 'Decision';

  public function executeIndex(sfWebRequest $request)
  {
    $manager_id = 0;
    if ($this->getUser()->getGuardUser()->TeamMember->getFirst()) {
      $manager_id = $this->getUser()->getGuardUser()->TeamMember->getFirst()->manager_id;
    }

    $this->redirectIf($this->getUser()->getGuardUser()->wizard, '@wizard');
    $this->collection_json = DecisionTable::getInstance()->getForUserJSON($this->getUser()->getGuardUser());
    $this->folders_json = FolderTable::getInstance()->getForUserJSON($this->getUser()->getGuardUser());
    $this->form     = new DecisionForm(null, array('guard_user_id' => $this->getUser()->getGuardUser()->getId(), 'manager_id' => $manager_id));
    $this->routes   = $this->getContext()->getRouting()->getRoutes();
    $this->base_url = substr($this->getContext()->getRouting()->generate('homepage'), 0, -1);
    $this->getUser()->setAttribute('decision_id', null, 'sfGuardSecurityUser');
    $this->getResponse()->setTitle('Projects');
    $this->getResponse()->setSlot('footer_bg', true);
    $this->upload_widget = new laWidgetFileUpload(array('module_partial' => 'wizard/upload_widget'));

    $this->external_ids = array();
    $external_decisions = DecisionTable::getInstance()->createQuery('d')
        ->where('d.external_id IS NOT NULL')
        ->andWhereIn('d.user_id', sfGuardUserTable::getInstance()->getUsersInTeamIDs($this->getUser()->getGuardUser()))
        ->execute();

    foreach ($external_decisions as $external_decision){
      $this->external_ids[] = $external_decision->getExternalId();
    }
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $decision = new Decision();
    $decision->setUserId($this->getUser()->getGuardUser()->getId());
    $form = new DecisionForm($decision);

    return $this->renderPartial('form', array('form' => $form));
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));

    $decision = new Decision();
    $decision->setUserId($this->getUser()->getGuardUser()->getId());
    $form = new DecisionForm($decision, array('guard_user_id' => $this->getUser()->getGuardUser()->getId(), 'type' => 'new'));

    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $form->save();

      return $this->renderText(json_encode(array('status' => 'success', 'redirect' => $this->generateUrl('dashboard', array('decision_id' => $decision->getId())) )));
    } else {
      return $this->renderText(json_encode(array('status' => 'error', 'html' => $this->getPartial('form', array('form' => $form)))));
    }
  }

  public function executeNewFolder(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $folder = new Folder();
    $folder->setName( 'New ' . InterfaceLabelTable::getInstance()->get($this->getuser()->getGuardUser(), InterfaceLabelTable::FOLDER_TYPE) );
    $folder->setUser($this->getUser()->getGuardUser());
    $folder->setType(Folder::TYPE_PROJECT);

    if (!Folder::getInstance()->getNotDeletableForUser($this->getUser()->getGuardUser(), Folder::TYPE_PROJECT, false)) {
      $folder->setDeletable(false);
    }

    $folder->save();

    // Create log
    $log = new Log();
    $log->injectDataAndPersist($folder, $this->getUser()->getGuardUser(), array('action' => 'new'));

    return $this->renderText(json_encode($folder->getRowData()));
  }

  public function executeAddToFolder(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $project = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $request->getParameter('id'));
    $folder = FolderTable::getInstance()->getForUser($request->getParameter('folder_id'), $this->getUser()->getGuardUser(), Folder::TYPE_PROJECT);
    $this->forward404Unless(is_object($project));
    $this->forward404Unless(is_object($folder));

    $project->setFolderId($request->getParameter('folder_id'));
    $project->save();

    return $this->renderText(json_encode($project->getRowData()));
  }

  public function executeEditFolder(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $folder = $this->getRoute()->getObject();
    $form = new FolderForm($folder);

    return $this->renderPartial('folder_form', array('form' => $form));
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($decision = Doctrine_Core::getTable('Decision')->find(array($request->getParameter('id'))), sprintf('Object decision does not exist (%s).', $request->getParameter('id')));
    $form = new DecisionForm($decision, array('type' => 'edit'));

    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $form->save();

      $tags_request = json_decode($request->getParameter('tags'));
      $tags = array();
      foreach($form->getObject()->getTagDecision() as $tag) {
        $tags[] = $tag->Tag->name;
      }

      foreach(array_diff($tags_request, $tags) as $result) {
        Tag::newTag($this->getUser()->getGuardUser(), $request->getParameter('id'), $result, 'decision');
      }

      foreach(array_diff($tags, $tags_request) as $result) {
        Tag::removeTag($this->getUser()->getGuardUser(), $request->getParameter('id'), $result, 'decision');
      }

      return $this->renderText(json_encode(array(
        'status' => 'success',
        'data'   => $form->getObject()->getRowData()
      )));
    }

    return $this->renderText(json_encode(array(
      'status' => 'error',
      'html'   => $this->getPartial('form', array('form' => $form))
    )));
  }

  public function executeUpdateFolder(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $object = FolderTable::getInstance()->getForUser($request->getParameter('id'), $this->getUser()->getGuardUser());
    $this->forward404Unless($object);
    $form = new FolderForm($object);
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid()) {
      $form->save();

      return sfView::NONE;
    }

    return $this->renderPartial('folder_form', array('form' => $form));
  }

  public function executeUpdateFolderState(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $object = FolderTable::getInstance()->getForUser($request->getParameter('id'), $this->getUser()->getGuardUser());
    $this->forward404Unless($object);

    try
    {
      $object->open = intval($request->getParameter('state'));
      $object->save();

      echo  'success';
    } catch (Exception $e) {
      echo $e->getMessage();
    }

    return sfView::NONE;
  }

  public function executeDeleteFolder(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $object = FolderTable::getInstance()->getForUser($request->getParameter('id'), $this->getUser()->getGuardUser());
    $this->forward404Unless($object);
    if ($object->deletable == 0) {
      $this->forward404();
    }

    // TODO delete this, because it will be NULL by relation
    Doctrine_Query::create()
      ->update('decision d')
      ->set('d.folder_id', FolderTable::getInstance()->getNotDeletableForUser($this->getUser()->getGuardUser(), Folder::TYPE_PROJECT)->getId())
      ->where('d.folder_id=?', $object->id)
      ->execute();

    $object->delete();
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    return $this->renderText(json_encode(array('status' => 1)));
  }

  public function executeUpload(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var Decision $decision */
    $decision = $this->getRoute()->getObject();

    $fileValidator = new sfValidatorFile(array(
      'required'   => true,
      'mime_types' => array('application/zip')
    ));

    $decisionImporter = new DecisionImporter();
    $decisionImporter->setDecision($decision);
    $decisionImporter->setCreatedAndUpdatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));

    foreach ($request->getFiles('files') as $file) {
      $validatedFile = $fileValidator->clean($file);
      $decisionImporter->setFile($validatedFile);
      $decisionImporter->import();
    }

    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
    return $this->renderText(json_encode(array(array())));
  }

  public function executeDemo(sfWebRequest $request)
  {
    /** @var sfGuardUser $user */
    $user = $this->getUser()->getGuardUser();
    $demoData = new DemoData(sfConfig::get('sf_data_dir') . '/decision_example.yml');
    $demoData->load($user);
    $user->save();
    $this->redirect('@decision\load?decision_id=' . $user->Decisions->getLast()->id);
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeModalUpload(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $result = array();

    $decision_id = $this->getUser()->getAttribute('decision_id', null, 'sfGuardSecurityUser');

    if (!empty($decision_id)) {
      $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
      $this->forward404Unless(is_object($decision));
    } else {
      $this->forward404();
    }

    $fileValidator = new sfValidatorFile(array(
      'required'   => true
    ));

    $decisionImporter = new DecisionImporter();
    $decisionImporter->setDecision($decision);
    $decisionImporter->setCreatedAndUpdatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));

    foreach ($request->getFiles('files') as $file) {
      $validatedFile = $fileValidator->clean($file);
      $decisionImporter->setFile($validatedFile);
      $result = $decisionImporter->import();
    }

    $dashboard_role = $decision->getDashboardRole();
    if ($dashboard_role){
      foreach ($decisionImporter->getAlternatives() as $alternative) {
        foreach ($decision->getCriterion() as $criterion){
          $planned_alternative_measurement = new PlannedAlternativeMeasurement();
          $planned_alternative_measurement->setAlternative($alternative);
          $planned_alternative_measurement->setCriterion($criterion);
          $dashboard_role->PlannedAlternativeMeasurement->add($planned_alternative_measurement);
        }
      }
      $dashboard_role->PlannedAlternativeMeasurement->save();
    }

    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    return $this->renderText(json_encode(array(
      'status' => 'success',
      'items'  => $result
    )));
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeItems(sfWebRequest $request)
  {
    $this->items = Doctrine_Core::getTable('Alternative')
      ->createQuery('a')
      ->leftJoin('a.Decision d')
      ->whereIn('d.user_id', sfGuardUserTable::getInstance()->getUsersInTeamIDs($this->getUser()->getGuardUser()))
      ->orderBy('id DESC')
      ->execute();
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeSkip(sfWebRequest $request)
  {
    $decision_id = $this->getUser()->getAttribute('decision_id', null, 'sfGuardSecurityUser');

    $this->getUser()->getAttributeHolder()->remove('decision_id', null, 'sfGuardSecurityUser');

    if (!empty($decision_id)) {
      $this->redirect('dashboard', array('decision_id' => $decision_id));
    } else {
      $this->redirect('@decision');
    }
  }


  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeImportFromTrello(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    if (!DecisionTable::getInstance()->verifyAvailableName($this->getUser()->getGuardUser(), $request->getParameter('board_name'))){
      return $this->renderText(json_encode(array(
        'status'  => 'error',
        'message' => 'A project with that name already exists'
      )));
    }

    $decision = new Decision();
    $decision->setName($request->getParameter('board_name'));
    $decision->setUserId($this->getUser()->getGuardUser()->getId());
    $decision->setExternalId($request->getParameter('board_id'));
    $decision->setTypeId(2);
    $type_template = TypeTemplate::getInstance()->createQuery('t')
      ->where('t.user_id is NULL')
      ->andWhere('t.type_id = ?', 2)
      ->andWhere('t.name = ?', 'Default')
      ->fetchOne();
    $decision->setTemplateId($type_template->getId()); // Default template
    $decision->setFolderId(FolderTable::getInstance()->getNotDeletableForUser($this->getUser()->getGuardUser(), Folder::TYPE_PROJECT)->getId());

    $decision->save();

    $dashboard_role = $decision->getDashboardRole();

    foreach (json_decode($request->getParameter('cards', '{}')) as $card) {
      $alternative = new Alternative();
      $alternative->setName($card->name);
      $alternative->setDecisionId($decision->getId());
      $alternative->setAdditionalInfo($card->desc);
      $alternative->setNotes($card->notes);
      $alternative->setExternalId($card->id);
      $alternative->setCreatedBy($request->getParameter('full_name') . ' (via Trello)');
      $alternative->setUpdatedBy($request->getParameter('full_name') . ' (via Trello)');

      if ($card->due) {
        $date = DateTime::createFromFormat('Y-m-d\TH:i:s.000Z', $card->due);
        $alternative->setDueDate($date->format('Y-m-d H:i:s'));
        $alternative->setNotifyDate($date->format('Y-m-d H:i:s'));
      }

      $alternative->save();

      foreach ($card->labels as $label) {
        Tag::newTag($this->getUser()->getGuardUser(), $alternative->getId(), $label, 'alternative');
      }

      if ($dashboard_role){
        foreach ($decision->getCriterion() as $criterion){
          $planned_alternative_measurement = new PlannedAlternativeMeasurement();
          $planned_alternative_measurement->setAlternative($alternative);
          $planned_alternative_measurement->setCriterion($criterion);
          $dashboard_role->PlannedAlternativeMeasurement->add($planned_alternative_measurement);
        }
      }
    }

    $dashboard_role->PlannedAlternativeMeasurement->save();

    if ($request->getParameter('wizard')) {
      $user = $this->getUser()->getGuardUser();
      $user->setWizard(false);
      $user->save();
    }

    // Create log
    $log = new Log();
    $log->setAction('project_create');
    $log->setUserId($this->getUser()->getGuardUser()->id);
    $log->setInformation(json_encode(array(
      'decision_name'     => $decision->getName(),
      'decision_type'     => $decision->getTypeId(),
      'decision_template' => $decision->getTemplateId(),
      'imported'          => 'Trello'
    )));
    $log->save();

    return $this->renderText(json_encode(array(
      'status'        => 'success',
      'dashboard_url' => $this->generateUrl('dashboard', array('decision_id' => $decision->getId()))
    )));
  }

  public function executePortfolio()
  {

  }

  public function executePersonas()
  {

  }

  public function executeStrategy()
  {

  }

}
