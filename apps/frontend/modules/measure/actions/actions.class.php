<?php

/**
 * measure actions.
 *
 * @package    dmp
 * @subpackage measure
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 *
 * @property Decision $decision
 * @property Measurement $measurement
 */
class measureActions extends sfActions
{
  public function preExecute()
  {
    $context = $this->getContext();
    if ($context->getRequest()->hasParameter('token')) {
      $context->getRouting()->setDefaultParameter('token', $context->getRequest()->getParameter('token'));
    }
  }

  public function executeIndex(sfWebRequest $request)
  {
    /** @var Role $role */
    $role = Doctrine::getTable('Role')->findOneByTokenAndActive($request->getParameter('token', false), true);
    $this->getUser()->setCulture($role->language);
    $this->forward404Unless(is_object($role));

    $map = new MeasurementMap();
    $map->clean();

    $this->redirectIf($map->hasData(), '@measure\measure');

    $email_address = $this->getUser()->getAttribute('email_address', null, 'measurement/email/' . $role->id);
    if ($this->getUser()->isAuthenticated() || $role->anonymous || $email_address) {
      $map->build($role);
      $map->save();

      if ($role->comment || $role->Files->count()) {
        $this->redirect('@measure\comments');
      } else if ($role->collect_items) {
        $this->redirect('@measure\collectItems');
      } else {
        $this->redirect($this->generateUrl('measure\measure', array('embed' => $request->getParameter('embed'))));
      }
    }

    $this->decision = $role->Decision;

    $this->form = new MeasurementStartForm();
  }

  public function executeStart(sfWebRequest $request)
  {
    /** @var Role $role */
    $role = Doctrine::getTable('Role')->findOneBy('token', $request->getParameter('token', false));
    $this->forward404Unless($role);

    $this->form = new MeasurementStartForm();
    $this->form->bind($request->getParameter($this->form->getName()));

    if ($this->form->isValid()) {
      $this->form->setRoleId($role->id);
      $this->form->save();

      $map = new MeasurementMap();
      $map->build($role);
      $map->save();

      if ($role->comment || $role->Files->count()) {
        $this->redirect('@measure\comments');
      } else if ($role->collect_items) {
        $this->redirect('@measure\collectItems');
      } else {
        $this->redirect('@measure\measure');
      }
    }

    $this->decision = $role->Decision;
    $this->setTemplate('index');
  }

  public function executeComments(sfWebRequest $request)
  {
    /** @var Role $role */
    $this->role = Doctrine::getTable('Role')->findOneBy('token', $request->getParameter('token', false));
    $this->forward404Unless($this->role);

    $this->comment = nl2br(Utility::tag_links($this->role->comment));
    $this->files = $this->role->Files;
  }

  public function executeCommentSave(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var Role $role */
    $role = Doctrine::getTable('Role')->findOneBy('token', $request->getParameter('token', false));
    $this->forward404Unless($role);

    $comment = new Comment();
    $comment->decision_id = $role->decision_id;
    $comment->text = strip_tags($request->getParameter('text'), '<br>');

    if (intval($request->getParameter('prioritization'))) {
      $comment->criterion_id = $request->getParameter('id');
    } else {
      $comment->alternative_id = $request->getParameter('id');
      $this->measurement = new Measurement($role);
      $step = $this->measurement->getMap()->getCurrentStep();
      $comment->criterion_id = $step[1]['criterion_id'];
    }

    if ($this->getUser()->isAuthenticated()) {
      $comment->User = $this->getUser()->getGuardUser();
    } else {
      $comment->email = $this->getUser()->getAttribute('email_address', null, 'measurement/email/' . $role->id);
    }

    $comment->save();

    return sfView::NONE;
  }

  /**
   * Executes download action
   *
   * @param sfWebRequest $request A request object
   * @return string
   */
  public function executeDownload(sfWebRequest $request)
  {
    /** @var UploadedFile $uploadedFile */
    $uploadedFile = $this->getRoute()->getObject();
    /** @var Role $role */
    $role = Doctrine::getTable('Role')->findOneBy('token', $request->getParameter('token', false));
    $this->forward404Unless($role);

    $valid = in_array($uploadedFile->id, $role->Files->getPrimaryKeys());
    if (!$valid) {
      foreach ($role->Decision->Alternative as $alternative) {
        if (in_array($uploadedFile->id, $alternative->Files->getPrimaryKeys())) {
          $valid = true;
          break;
        }
      }
    }
    $this->forward404Unless($valid);

    /** @var sfWebResponse $response */
    $response = $this->getResponse();
    $response->clearHttpHeaders();
    $response->addCacheControlHttpHeader('Cache-control', 'must-revalidate, post-check=0, pre-check=0');
    $response->setContentType($uploadedFile->mime_type);
    $response->setHttpHeader('Content-Disposition', 'attachment; filename="' . $uploadedFile->name . '"');
    $response->setContent(file_get_contents($uploadedFile->getAbsolutePath()));

    return sfView::NONE;
  }

  public function executeMeasure(sfWebRequest $request)
  {
    if ($request->hasParameter('embed')){
      $this->setLayout('iframe_layout');
    }
    /** @var Role $this->role */
    $this->role = Doctrine::getTable('Role')->findOneByTokenAndActive($request->getParameter('token', false), true);

    $this->forward404Unless($this->role);
    $this->measurement = new Measurement($this->role);
    $this->redirectUnless(!$this->measurement->isFinished(), $this->generateUrl('measure\finish', array('embed' => $request->getParameter('embed'))));
    $step = $this->measurement->getMap()->getCurrentStep();

    $this->forward404Unless(is_array($step));

    list($methodClassName, $data) = $step;
    /** @var MeasurementMethod $methodObjectName */
    $methodObjectName = new $methodClassName($data);
    $methodObjectName->setRole($this->role);
    $methodObjectName->load();

    // load saved data from database and show the previous response
    if ($this->role->updateable)
    {
      if ($this->role->Response->count())
      {
        if ($measurementMethod = $methodObjectName->getMeasurementMethod())
        {
          $methodObjectName->setDefaultValues($measurementMethod->getValues());
        }
      }
    }

    $this->measurement->setMethodObject($methodObjectName);

    $this->formURL = $this->generateUrl('measure\measureSave', array('embed' => $request->getParameter('embed')));
  }

  public function executeMeasureSave(sfWebRequest $request)
  {
    $this->executeMeasure($request);
    $this->measurement->setValues($request->getParameter('measurement', array()), $request->hasParameter('back'));

    if (!$this->measurement->hasError()) {
      if ($request->hasParameter('next')) {
        $this->measurement->hasNextStep() ? $this->measurement->next() : $this->measurement->save();
      } else {
        $this->measurement->back();
      }

      if ($this->measurement->isFinished()) {
        // Create log
        $log = new Log();
        $log->action = 'survey_answered';
        $log->information = json_encode(array());
        $log->user_id = $this->getUser()->getGuardUser()->id;
        $log->save();

        $this->role->continue_url ? $this->redirect($this->role->continue_url) : $this->redirect($this->generateUrl('measure\finish', array('embed' => $request->getParameter('embed'))));
      } else {
        $this->redirect($this->generateUrl('measure\measure', array('embed' => $request->getParameter('embed'))));
      }
    }

    $this->setTemplate('measure');
  }

  public function executeFinish(sfWebRequest $request)
  {
    if ($request->hasParameter('embed')){
      $this->setLayout('iframe_layout');
    }

    $this->role = Doctrine::getTable('Role')->findOneByTokenAndActive($request->getParameter('token', false), true);
    $this->forward404Unless($this->role);

    if ($this->role->show_criteria_weights || $this->role->show_alternatives_score) {
      $this->criteriaAnalyze = new CriteriaAnalyze();
      $this->criteriaAnalyze->setDecisionId($this->role->decision_id);
      $this->criteriaAnalyze->load();
    }

    if ($this->role->show_alternatives_score) {
      $this->stackedBarChart = new StackedBarChart();
      $this->stackedBarChart->setDecisionId($this->role->decision_id);
      $this->stackedBarChart->setCriteriaValues($this->criteriaAnalyze->getCriteriaValues());
      $this->stackedBarChart->load();
    }
  }

  public function executeChartRevert(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var Role $role */
    $role = Doctrine::getTable('Role')->findOneBy('token', $request->getParameter('token', false));
    $this->forward404Unless($role);

    $criteriaAnalyze = new CriteriaAnalyze();
    $criteriaAnalyze->setDecisionId($role->decision_id);
    $criteriaAnalyze->load();

    return $this->getUpdateActionResponse($criteriaAnalyze, $role->decision_id, true);
  }

  public function executeChartUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var Role $role */
    $role = Doctrine::getTable('Role')->findOneBy('token', $request->getParameter('token', false));
    $this->forward404Unless($role);

    $criteriaAnalyze = new CriteriaAnalyze();
    $criteriaAnalyze->setDecisionId($role->decision_id);
    $criteriaAnalyze->setSaveGraph(false);
    $criteriaAnalyze->setData($request->getParameter('graph', array()));

    return $this->getUpdateActionResponse($criteriaAnalyze, $role->decision_id);
  }

  private function getUpdateActionResponse($criteriaAnalyze, $decision_id, $revert = false, $response = array())
  {
    // When user changes the weights of criteria all dependent values
    // should be recalculated automatically in the alternatives chart
    $stackedBarChart = new StackedBarChart();
    $stackedBarChart->setDecisionId($decision_id);
    $stackedBarChart->setSaveGraph(false);
    $stackedBarChart->setCriteriaValues($criteriaAnalyze->getCriteriaValues());
    $stackedBarChart->load();

    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    $stacked_bar_chart[] = '"data":' . $stackedBarChart->getJsonData();
    $stacked_bar_chart[] = '"alternatives":' . $stackedBarChart->getAlternativesJson();
    $stacked_bar_chart[] = '"criteria":' . $stackedBarChart->getCriteriaJson();

    if ($revert) {
      $response[] = '"criteriaData":' . $criteriaAnalyze->getJsonData();
    }

    $response[] = '"stackedBarChart":{' . implode(',', $stacked_bar_chart) . '}';

    return $this->renderText('{' . implode(',', $response) . '}');
  }

  public function executeCollectItems(sfWebRequest $request)
  {
    $this->role = Doctrine::getTable('Role')->findOneBy('token', $request->getParameter('token', false));
    $this->forward404Unless(is_object($this->role));
    $this->form = new ItemSuggestionForm();
    $this->alternatives = AlternativeTable::getInstance()->getReviewedList($this->role->decision_id);
    $this->decision = $this->role->Decision;
  }

  public function executeItemSuggestionSave(sfWebRequest $request)
  {
    /** @var Role $role */
    $role = Doctrine::getTable('Role')->findOneBy('token', $request->getParameter('token', false));
    $this->forward404Unless(is_object($role));

    $alternative = new Alternative();
    $alternative->Decision = $role->Decision;
    $this->form = new ItemSuggestionForm($alternative);
    $this->form->bind($request->getParameter($this->form->getName()));
    if ($this->form->isValid()) {
      $this->form->save();
      $this->redirect('@measure\collectItems');
    }

    $this->alternatives = AlternativeTable::getInstance()->getReviewedList($role->decision_id);
    $this->setTemplate('collectItems');
  }

  public function executeAlternativeVote(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $alternative_id = $request->getParameter('id');
    $voted_items_ids = $this->getUser()->getAttribute('voted_items_ids', array());

    if (!in_array($alternative_id, $voted_items_ids)) {
      /** @var Alternative $alternative */
      $alternative = AlternativeTable::getInstance()->getForVote($alternative_id);
      $alternative->score++;
      $alternative->save();
      $voted_items_ids[] = $alternative->id;
      $this->getUser()->setAttribute('voted_items_ids', $voted_items_ids);

      return $this->renderText($alternative->score);
    }

    return sfView::NONE;
  }
}
