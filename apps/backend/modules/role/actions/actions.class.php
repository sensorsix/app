<?php

/**
 * role actions.
 *
 * @package    dmp
 * @subpackage collaborate
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class roleActions extends BackendDefaultActions
{
  protected $model = 'Role';

  public function preExecute()
  {
    $context = $this->getContext();
    if ($context->getRequest()->hasParameter('decision_id')) {
      $context->getRouting()->setDefaultParameter('decision_id', $context->getRequest()->getParameter('decision_id'));
    }
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $role              = new Role();
    $role->name        = $request->getParameter('title', '');
    $role->decision_id = $request->getParameter('decision_id');

    $form = new RoleForm($role);

    return $this->renderPartial('form', array('form' => $form, 'type' => 'new'));
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));

    $role              = new Role();
    $role->decision_id = $request->getParameter('decision_id');
    $form              = new RoleForm($role);
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $form->save();

      // Create log
      $log = new Log();
      $log->injectDataAndPersist($role, $this->getUser()->getGuardUser(), array('action' => 'new'));

      $matrix = json_decode($request->getParameter('matrix'), true);
      $role   = $this->plannedMeasurementSaveNew($role, $matrix);

      $role->refresh(true);
      return $this->renderText(json_encode($role->getRowData()));
    } else {
      return $this->renderPartial('form', array('form' => $form, 'type' => 'new'));
    }
  }

  public function executeEdit(sfWebRequest $request)
  {
    /** @var Role $role */
    $this->forward404Unless($role = Doctrine_Core::getTable('Role')->find(array($request->getParameter('id'))));
    $form = new RoleForm($role);

    $expertPanel = new ExpertPanel($role->decision_id, $request->getParameter('id'));
    $expertPanel->load();

    return $this->renderPartial('form', array('form' => $form, 'expertPanel' => $expertPanel));
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $output = parent::executeUpdate($request);

    $this->expertPanel = new ExpertPanel($this->form->getObject()->decision_id, $request->getParameter('id'));
    $this->expertPanel->load();

    if ($this->form_valid) {
      $matrix = json_decode($request->getParameter('matrix'), true);
      $this->plannedMeasurementSave($matrix);

      return $this->renderText(json_encode($this->form->getObject()->getRowData()));
    } else {
      return $output;
    }
  }

  /**
   * @param $matrix
   */
  private function plannedMeasurementSave($matrix)
  {
    /**
     * @var $object Role
     */
    $object = $this->form->getObject();
    $object->loadPlannedMeasurements();

    foreach ($matrix as $el) {
      if (!$object->hasPlannedMeasurement($el['criterion_id'], $el['alternative_id']) && $el['checked']) {
        $plannedAlternativeMeasurement                 = new PlannedAlternativeMeasurement();
        $plannedAlternativeMeasurement->role_id        = $object->id;
        $plannedAlternativeMeasurement->criterion_id   = $el['criterion_id'];
        $plannedAlternativeMeasurement->alternative_id = $el['alternative_id'];
        $object->PlannedAlternativeMeasurement->add($plannedAlternativeMeasurement);
      } elseif ($object->hasPlannedMeasurement($el['criterion_id'], $el['alternative_id']) && !$el['checked']) {
        Doctrine::getTable('PlannedAlternativeMeasurement')
          ->createQuery()
          ->delete()
          ->where('role_id = ? AND criterion_id = ? AND alternative_id = ?', array($object->id, $el['criterion_id'], $el['alternative_id']))
          ->execute();
      }
    }

    $object->PlannedAlternativeMeasurement->save();
  }

  /**
   * @param Role $role
   * @param $matrix
   * @return Role
   */
  private function plannedMeasurementSaveNew(Role $role, $matrix)
  {
    foreach ($matrix as $el) {
      if ($el['checked']) {
        $plannedAlternativeMeasurement                 = new PlannedAlternativeMeasurement();
        $plannedAlternativeMeasurement->role_id        = $role->id;
        $plannedAlternativeMeasurement->criterion_id   = $el['criterion_id'];
        $plannedAlternativeMeasurement->alternative_id = $el['alternative_id'];
        $role->PlannedAlternativeMeasurement->add($plannedAlternativeMeasurement);
      }
    }
    $role->PlannedAlternativeMeasurement->save();
    return $role;
  }

  public function executeUpload(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var Role $role */
    $role     = $this->getRoute()->getObject();
    $response = array();

    // Load files
    if ($request->getMethod() == 'GET') {
      foreach ($role->Files as $uploadedFile) {
        $response[] = $uploadedFile->getResponseObject();
      }
    } // Upload files
    else {
      $dir_path = '/role';

      $fileValidator = new sfValidatorFile(array(
        'required' => true,
        'path'     => sfConfig::get('sf_upload_dir') . $dir_path,
      ));

      foreach ($request->getFiles('files') as $file) {
        $validatedFile           = $fileValidator->clean($file);
        $uploadedFile            = new UploadedFile();
        $uploadedFile->path      = $dir_path . '/' . $validatedFile->save();
        $uploadedFile->mime_type = $validatedFile->getType();
        $uploadedFile->name      = $validatedFile->getOriginalName();
        $uploadedFile->save();
        $role->Files->add($uploadedFile);
        $response[] = $uploadedFile->getResponseObject();
      }

      $role->save();
    }

    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
    return $this->renderText(json_encode($response));
  }

  public function executeRateYourself(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision_id = $request->getParameter('decision_id', false);
    /** @var Decision $decision */
    $decision = Doctrine::getTable('Decision')->findOneByIdAndUserId($decision_id, $this->getUser()->getGuardUser()->id);
    $this->forward404Unless(is_object($decision));
    $role              = new Role();
    $role->decision_id = $decision_id;
    $role->name        = 'Admin';
    $role->prioritize  = true;

    foreach ($decision->Criterion as $criterion) {
      foreach ($decision->Alternative as $alternative) {
        $measurement              = new PlannedAlternativeMeasurement();
        $measurement->Alternative = $alternative;
        $measurement->Criterion   = $criterion;
        $role->PlannedAlternativeMeasurement->add($measurement);
      }
    }

    $role->save();

    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
    return $this->renderText(json_encode(array('url' => $this->getContext()->getConfiguration()->generateFrontendUrl('measure', array('token' => $role->token)))));
  }

  public function executeHireExperts(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var Role $role */
    $role    = $this->getRoute()->getObject();
    $message = $this->getMailer()->compose(
      array(sfConfig::get('app_info_email') => sfConfig::get('app_sf_guard_plugin_default_from_email')),
      sfConfig::get('app_info_email'),
      'Hire experts'
    );

    $expertPanel = new ExpertPanel($role->decision_id, $role->id);
    $expertPanel->load();

    $message->setBody($this->getPartial('hire_experts_email', array('role' => $role, 'expertPanel' => $expertPanel)));
    $message->setContentType('text/html');

    $this->getMailer()->send($message);

    return sfView::NONE;
  }
}
