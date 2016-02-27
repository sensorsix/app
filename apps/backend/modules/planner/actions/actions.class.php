<?php

/**
 * planner actions.
 *
 * @package    dmp
 * @subpackage planner
 * @author     Igor Mancos
 */
class plannerActions extends BackendDefaultActions
{
  protected $model = 'ProjectRelease';

  /**
   *
   */
  public function preExecute()
  {
    $context = $this->getContext();
    if ($context->getRequest()->hasParameter('decision_id')) {
      $context->getRouting()->setDefaultParameter('decision_id', $context->getRequest()->getParameter('decision_id'));
    }

    $this->forward404Unless($this->getUser()->verifyLightAccess($this->getContext()));
  }

  /**
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);
    $this->decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless($this->decision);

    $this->forward404Unless($this->getUser()->verifyLightAccess($this->getContext()));

    $this->criteriaAnalyze = new CriteriaAnalyze();
    $this->criteriaAnalyze->setDecisionId($decision_id);
    $this->criteriaAnalyze->load();

    $this->logicalFilter = new LogicalFilterView();
    $this->logicalFilter->setDecisionId($decision_id);
    $this->logicalFilter->load();

    $this->roleFilter = new RoleFilterView();
    $this->roleFilter->setDecisionId($decision_id);
    $this->roleFilter->load();

    $this->statusFilter = new StatusFilterView();
    $this->statusFilter->setDecisionId($decision_id);
    $this->statusFilter->load();

    $this->tagFilter = new TagFilterView();
    $this->tagFilter->setDecisionId($decision_id);
    $this->tagFilter->load();

    $this->stackedBarChart = new StackedBarChart();
    $this->stackedBarChart->setDecisionId($decision_id);
    $this->stackedBarChart->setCriteriaValues($this->criteriaAnalyze->getCriteriaValues());
    $this->stackedBarChart->setRoleFilterData($this->roleFilter->getData());
    $this->stackedBarChart->setStatusFilterData($this->statusFilter->getData());
    $this->stackedBarChart->setTagFilterData($this->tagFilter->getDataForSQL());
    $this->stackedBarChart->setFilteredAlternativesIds($this->logicalFilter->getFilteredAlternativesIds());
    $this->stackedBarChart->load();

    $this->costAnalyze = new CostAnalyze();
    $this->costAnalyze->setDecisionId($decision_id);
    $this->costAnalyze->setSortedAlternativeIds($this->stackedBarChart->getSortedAlternativeIds());
    $this->costAnalyze->setRoleFilterData($this->roleFilter->getData());
    $this->costAnalyze->setStatusFilterData($this->statusFilter->getData());
    $this->costAnalyze->setTagFilterData($this->tagFilter->getDataForSQL());
    $this->costAnalyze->setCumulativeData($this->stackedBarChart->getCumulativeData());
    $this->costAnalyze->setFilteredAlternativesIds($this->logicalFilter->getFilteredAlternativesIds());
    $this->costAnalyze->load();


    // release planner
    $this->alternatives_json = AlternativeTable::getInstance()->getDashboardReleaseJSON($decision_id, $this->stackedBarChart->getSortedAlternativeIds());
    $this->releases = ProjectReleaseTable::getInstance()->getList($decision_id);
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeAddReleaseItem(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $releaseAlternative = new ProjectReleaseAlternative();
    $releaseAlternative->release_id = $request->getParameter('release_id');
    $releaseAlternative->alternative_id = $request->getParameter('alternative_id');
    $releaseAlternative->value = $request->getParameter('value');
    $releaseAlternative->save();

    $releaseAlternative->ProjectRelease->updateValue();
    $releaseAlternative->ProjectRelease->save();

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   * @return string
   */
  public function executeRemoveReleaseItem(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $release_id = $request->getParameter('release_id');

    /** @var ProjectRelease $projectRelease */
    $projectRelease = ProjectReleaseTable::getInstance()->find($release_id);
    /** @var ProjectReleaseAlternative $projectReleaseAlternative */
    $projectReleaseAlternative = ProjectReleaseAlternativeTable::getInstance()
        ->findByDql('release_id = ? AND alternative_id = ?', array($release_id, $request->getParameter('alternative_id')))
        ->getFirst();

    $projectRelease->value -= $projectReleaseAlternative->value;
    $projectRelease->save();

    $projectReleaseAlternative->delete();

    return sfView::NONE;
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeNewRelease(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    /** @var Decision $decision  */
    $decision = DecisionTable::getInstance()->find($request->getParameter('decision_id'));
    $this->forward404Unless($decision);

    $this->forward404Unless($request->isXmlHttpRequest());

    $criterion_id = $request->getParameter('criterion_id');
    $release_number = Doctrine_Query::create()
            ->from('ProjectRelease')
            ->where('decision_id = ? AND criterion_id = ?', array($decision->id, $criterion_id))
            ->count() + 1;

    $release = new ProjectRelease();
    $release->decision_id = $decision->id;
    $release->criterion_id = $criterion_id;
    $release->name = $decision->getPartitionerAlias() . ' ' . $release_number;

    $form = new ProjectReleaseForm($release);
    return $this->renderPartial('form', array('form' => $form, 'type' => 'new'));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeCreateRelease(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));

    /** @var Decision $decision  */
    $decision = DecisionTable::getInstance()->find($request->getParameter('decision_id'));
    $this->forward404Unless($decision);

    $release = new ProjectRelease();
    $release->decision_id = $decision->id;
    $release->criterion_id = $request->getParameter('criterion_id');

    $form = new ProjectReleaseForm($release);
    $form->bind($request->getParameter($form->getName()));
    if ($form->isValid()) {
      $form->save();

      // Process tags
      $tags_request = json_decode($request->getParameter('tags'));
      foreach ($tags_request as $tag_request) {
        Tag::newTag($this->getUser()->getGuardUser(), $release->id, $tag_request, 'release');
      }

      $release->refresh(true);

      // Create log
      $log = new Log();
      $log->injectDataAndPersist($release, $this->getUser()->getGuardUser(), array('action' => 'new'));

      return $this->renderText(json_encode(array(
        'html'  => $this->getPartial('release', array('release' => $release)),
        'id'    => $release->id
      )));
    } else {
      return $this->renderPartial('form', array('form' => $form, 'type' => 'new'));
    }
  }

  /**
   * @param sfWebRequest $request
   * @return sfView|string
   */
  public function executeUpdateRelease(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($object = Doctrine_Core::getTable($this->model)->find(array($request->getParameter('id'))), sprintf('Object decision does not exist (%s).', $request->getParameter('id')));
    $formClass = $this->model . 'Form';
    $this->form = new $formClass($object, array('user' => $this->getUser()));

    $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));
    if ($this->form->isValid()) {
      $this->form->save();
      $this->form_valid = true;

      // Process tags
      $tags_request = json_decode($request->getParameter('tags'));
      $tags = array();
      foreach ($object->getTagRelease() as $tag) {
        $tags[] = $tag->Tag->name;
      }

      foreach (array_diff($tags_request, $tags) as $result) {
        Tag::newTag($this->getUser()->getGuardUser(), $request->getParameter('id'), $result, 'release');
      }

      foreach (array_diff($tags, $tags_request) as $result) {
        Tag::removeTag($this->getUser()->getGuardUser(), $request->getParameter('id'), $result, 'release');
      }

      $object->refresh(true);

      // Create log
      $log = new Log();
      $log->injectDataAndPersist($object, $this->getUser()->getGuardUser(), array('action' => 'edit'));

      return $this->renderText(json_encode(array('id' => $object->id, 'name' => $object->name)));
    }

    return $this->renderPartial('form', array('form' => $this->form));
  }

  /**
   * @param sfWebRequest $request
   * @return sfView
   */
  public function executeLogPinToWall(sfWebRequest $request){
    // Create log
    $log = new Log();
    $log->action = 'budget_create';
    $log->information = json_encode(array('criterion_id' => $request->getParameter('criterion_id')));
    $log->user_id = $this->getUser()->getGuardUser()->id;
    $log->save();

    return $this->renderText('');
  }

}
