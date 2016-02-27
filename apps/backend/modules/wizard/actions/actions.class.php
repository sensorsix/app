<?php

/**
 * wizard actions
 */
class wizardActions extends sfActions
{
  public function preExecute()
  {
    $context = $this->getContext();
    if ($context->getRequest()->hasParameter('decision_id')) {
      $context->getRouting()->setDefaultParameter('decision_id', $context->getRequest()->getParameter('decision_id'));
    }
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    $decision_id = $this->getUser()->getAttribute('decision_id', 'null', 'sfGuardSecurityUser');
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->value = '';
    if (is_object($decision)) {
      $this->value = $decision->name;
    }

    if (is_object($decision)) {
      $this->form = new DecisionForm($decision);
    } else {
      $this->form = new DecisionForm();
    }
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeDecisionSave(sfWebRequest $request)
  {
    $decision_id = $this->getUser()->getAttribute('decision_id', null, 'sfGuardSecurityUser');
    if ($decision_id) {
      $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
      $this->forward404Unless(is_object($decision));
    } else {
      $decision = new Decision();
      $decision->type_id = 2; // Product development
      $type_template = TypeTemplate::getInstance()->createQuery('t')
        ->where('t.user_id is NULL')
        ->andWhere('t.type_id = ?', 2)
        ->andWhere('t.name = ?', 'Default')
        ->execute();
      $decision->template_id = $type_template[0]->getId(); // Default template

      $folder = Folder::getInstance()->createQuery('t')
        ->where('t.user_id = ?', $this->getUser()->getGuardUser()->id)
        ->andWhere('t.deletable = ?', 0)
        ->execute();

      if (!empty($folder)) {
        $decision->setFolderId($folder[0]->getId());
      }
    }

    $decision->name = $request->getParameter('name');
    if ($decision->name == '') {
      $decision->name = 'New project';
    }

    $decision->save();
    $this->getUser()->setAttribute('decision_id', $decision->id, 'sfGuardSecurityUser');
    $this->redirect('@wizard\alternatives?decision_id=' . $decision->id);
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeDecisionModalSave(sfWebRequest $request)
  {
    $decision_id = $this->getUser()->getAttribute('decision_id', null, 'sfGuardSecurityUser');
    if (!empty($decision_id)) {
      $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    }

    if (empty($decision) || !is_object($decision)) {
      if (!DecisionTable::getInstance()->verifyAvailableName($this->getUser()->getGuardUser(), $request->getParameter('name'))){
        return $this->renderText(json_encode(array(
          'status'  => 'error',
          'message' => 'A project with that name already exists'
        )));
      }

      $decision = new Decision();

      $folder = FolderTable::getInstance()->getNotDeletableForUser($this->getUser()->getGuardUser(), Folder::TYPE_PROJECT);
      if (!empty($folder)) {
        $decision->setFolderId($folder->getId());
      }

      // Create log
      $log = new Log();
      $log->setAction('project_create');
      $log->setUserId($this->getUser()->getGuardUser()->id);
      $log->setInformation(json_encode(array(
        'decision_name'     => $request->getParameter('name'),
        'decision_type'     => $request->getParameter('type_id'),
        'decision_template' => $request->getParameter('template_id')
      )));
      $log->save();
    }

    $decision->setName($request->getParameter('name', ''));
    $decision->setTypeId($request->getParameter('type_id'));
    $decision->setTemplateId($request->getParameter('template_id'));
    $decision->save();

    $this->getUser()->setAttribute('decision_id', $decision->getId(), 'sfGuardSecurityUser');

    return $this->renderText(json_encode(array(
      'status'        => 'success',
      'dashboard_url' => $this->generateUrl('dashboard', array('decision_id' => $decision->getId()))
    )));
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeAlternatives(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id');
    $this->decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless(is_object($this->decision));
    $this->upload_widget = new laWidgetFileUpload(array(
      'module_partial' => 'upload_widget'
    ));
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeAlternativeSave(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $decision_id = $request->getParameter('decision_id');
    $this->decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless(is_object($this->decision));

    $alternative = new Alternative();
    $alternative->setName($request->getParameter('name'));
    $alternative->setDecision($this->decision);
    $alternative->setCreatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));
    $alternative->setUpdatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));
    $alternative->save();

    $dashboard_role = $this->decision->getDashboardRole();
    if ($dashboard_role){
      foreach ($this->decision->getCriterion() as $criterion){
        $planned_alternative_measurement = new PlannedAlternativeMeasurement();
        $planned_alternative_measurement->setAlternative($alternative);
        $planned_alternative_measurement->setCriterion($criterion);
        $dashboard_role->PlannedAlternativeMeasurement->add($planned_alternative_measurement);
      }

      $dashboard_role->PlannedAlternativeMeasurement->save();
    }


    $additional_roles = Doctrine_Query::create()
      ->from('Role r')
      ->leftJoin('r.Decision d')
      ->where('d.id = ?', $this->decision->getId())
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->execute();

    $criterion_estimates = Doctrine_Query::create()
      ->from('Criterion c')
      ->leftJoin('c.Decision d')
      ->where('d.id = ?', $this->decision->getId())
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->andWhere('c.name = ?', 'Hour Estimate')
      ->fetchOne();

    $criterion_value = Doctrine_Query::create()
      ->from('Criterion c')
      ->leftJoin('c.Decision d')
      ->where('d.id = ?', $this->decision->getId())
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->andWhere('c.name = ?', 'Value')
      ->fetchOne();

    $criterion_usability = Doctrine_Query::create()
      ->from('Criterion c')
      ->leftJoin('c.Decision d')
      ->where('d.id = ?', $this->decision->getId())
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->andWhere('c.name = ?', 'Usability')
      ->fetchOne();

    $criterion_feasibility = Doctrine_Query::create()
      ->from('Criterion c')
      ->leftJoin('c.Decision d')
      ->where('d.id = ?', $this->decision->getId())
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->andWhere('c.name = ?', 'Feasibility')
      ->fetchOne();

    foreach ($additional_roles as $additional_role){
      if ($additional_role->getName() == 'Collect Estimates' && $criterion_estimates) {
        $planned_alternative_measurement = new PlannedAlternativeMeasurement();
        $planned_alternative_measurement->setAlternative($alternative);
        $planned_alternative_measurement->setCriterion($criterion_estimates);
        $planned_alternative_measurement->setRole($additional_role);
        $planned_alternative_measurement->save();
      }elseif ($additional_role->getName() == 'Collect input on Value' && $criterion_value) {
        $planned_alternative_measurement = new PlannedAlternativeMeasurement();
        $planned_alternative_measurement->setAlternative($alternative);
        $planned_alternative_measurement->setCriterion($criterion_value);
        $planned_alternative_measurement->setRole($additional_role);
        $planned_alternative_measurement->save();
      }elseif ($additional_role->getName() == 'Collect input on Usability' && $criterion_usability) {
        $planned_alternative_measurement = new PlannedAlternativeMeasurement();
        $planned_alternative_measurement->setAlternative($alternative);
        $planned_alternative_measurement->setCriterion($criterion_usability);
        $planned_alternative_measurement->setRole($additional_role);
        $planned_alternative_measurement->save();
      }elseif ($additional_role->getName() == 'Collect input on Feasibility' && $criterion_feasibility) {
        $planned_alternative_measurement = new PlannedAlternativeMeasurement();
        $planned_alternative_measurement->setAlternative($alternative);
        $planned_alternative_measurement->setCriterion($criterion_feasibility);
        $planned_alternative_measurement->setRole($additional_role);
        $planned_alternative_measurement->save();
      }
    }

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeAlternativeModalSave(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $decision_id = $this->getUser()->getAttribute('decision_id', null, 'sfGuardSecurityUser');

    if (!empty($decision_id)) {
      $this->decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
      $this->forward404Unless(is_object($this->decision));
    } else {
      $this->forward404();
    }

    $alternative = new Alternative();
    $alternative->setName($request->getParameter('name'));
    $alternative->setDecision($this->decision);
    $alternative->setCreatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));
    $alternative->setUpdatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));
    $alternative->save();

    $dashboard_role = $this->decision->getDashboardRole();
    if ($dashboard_role){
      foreach ($this->decision->getCriterion() as $criterion){
        $planned_alternative_measurement = new PlannedAlternativeMeasurement();
        $planned_alternative_measurement->setAlternative($alternative);
        $planned_alternative_measurement->setCriterion($criterion);
        $dashboard_role->PlannedAlternativeMeasurement->add($planned_alternative_measurement);
      }

      $dashboard_role->PlannedAlternativeMeasurement->save();
    }

    $additional_roles = Doctrine_Query::create()
      ->from('Role r')
      ->leftJoin('r.Decision d')
      ->where('d.id = ?', $this->decision->getId())
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->execute();

    $criterion_estimates = Doctrine_Query::create()
      ->from('Criterion c')
      ->leftJoin('c.Decision d')
      ->where('d.id = ?', $this->decision->getId())
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->andWhere('c.name = ?', 'Hour Estimate')
      ->fetchOne();

    $criterion_value = Doctrine_Query::create()
      ->from('Criterion c')
      ->leftJoin('c.Decision d')
      ->where('d.id = ?', $this->decision->getId())
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->andWhere('c.name = ?', 'Value')
      ->fetchOne();

    $criterion_usability = Doctrine_Query::create()
      ->from('Criterion c')
      ->leftJoin('c.Decision d')
      ->where('d.id = ?', $this->decision->getId())
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->andWhere('c.name = ?', 'Usability')
      ->fetchOne();

    $criterion_feasibility = Doctrine_Query::create()
      ->from('Criterion c')
      ->leftJoin('c.Decision d')
      ->where('d.id = ?', $this->decision->getId())
      ->andWhere('d.user_id = ?', $this->getUser()->getGuardUser()->id)
      ->andWhere('c.name = ?', 'Feasibility')
      ->fetchOne();

    foreach ($additional_roles as $additional_role){
      if ($additional_role->getName() == 'Collect Estimates' && $criterion_estimates) {
        $planned_alternative_measurement = new PlannedAlternativeMeasurement();
        $planned_alternative_measurement->setAlternative($alternative);
        $planned_alternative_measurement->setCriterion($criterion_estimates);
        $planned_alternative_measurement->setRole($additional_role);
        $planned_alternative_measurement->save();
      }elseif ($additional_role->getName() == 'Collect input on Value' && $criterion_value) {
        $planned_alternative_measurement = new PlannedAlternativeMeasurement();
        $planned_alternative_measurement->setAlternative($alternative);
        $planned_alternative_measurement->setCriterion($criterion_value);
        $planned_alternative_measurement->setRole($additional_role);
        $planned_alternative_measurement->save();
      }elseif ($additional_role->getName() == 'Collect input on Usability' && $criterion_usability) {
        $planned_alternative_measurement = new PlannedAlternativeMeasurement();
        $planned_alternative_measurement->setAlternative($alternative);
        $planned_alternative_measurement->setCriterion($criterion_usability);
        $planned_alternative_measurement->setRole($additional_role);
        $planned_alternative_measurement->save();
      }elseif ($additional_role->getName() == 'Collect input on Feasibility' && $criterion_feasibility) {
        $planned_alternative_measurement = new PlannedAlternativeMeasurement();
        $planned_alternative_measurement->setAlternative($alternative);
        $planned_alternative_measurement->setCriterion($criterion_feasibility);
        $planned_alternative_measurement->setRole($additional_role);
        $planned_alternative_measurement->save();
      }
    }

    // Create log
    $log = new Log();
    $log->injectDataAndPersist($alternative, $this->getUser()->getGuardUser(), array('action' => 'new'));

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeSkip(sfWebRequest $request)
  {
    $this->getUser()->getAttributeHolder()->remove('decision_id', null, 'sfGuardSecurityUser');
    $user = $this->getUser()->getGuardUser();
    $user->wizard = false;
    $user->save();

    $this->redirect('@decision');
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeFinish(sfWebRequest $request)
  {
    $decision_id = $this->getUser()->getAttribute('decision_id', null, 'sfGuardSecurityUser');
    $this->getUser()->getAttributeHolder()->remove('decision_id', null, 'sfGuardSecurityUser');
    $user = $this->getUser()->getGuardUser();
    $user->wizard = false;
    $user->save();

    $this->redirect('@dashboard?decision_id=' . $decision_id);
  }

  public function executeAlternativeImport(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision_id = $request->getParameter('decision_id', false);
    /** @var Decision $decision */
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);

    $fileValidator = new sfValidatorFile(array(
      'required'   => true
    ));

    $importer = new AlternativeImporter();
    $importer->setDecision($decision);
    $importer->setCreatedAndUpdatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));

    foreach ($request->getFiles('files') as $file) {
      $validatedFile = $fileValidator->clean($file);
      $importer->setFile($validatedFile);
      $importer->import();
    }

    $dashboard_role = $decision->getDashboardRole();
    if ($dashboard_role){
      foreach ($importer->getAlternatives() as $alternative) {
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

    return $this->renderText(json_encode(array(array())));
  }
}
 