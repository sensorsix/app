<?php

class roadMapActions extends BackendDefaultActions
{
  protected $model = 'Roadmap';

  public function preExecute(){
    parent::preExecute();

    $this->forward404Unless($this->getUser()->hasRoadmapAccess());
  }

  public function executeIndex(sfWebRequest $request)
  {
    $this->collection_json = RoadmapTable::getInstance()->getForUserJSON($this->getUser()->getGuardUser());
    $this->folders_json    = FolderTable::getInstance()->getForUserJSON($this->getUser()->getGuardUser(), 'roadmap');
  }

  public function executeNewFolder(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $folder = new Folder();
    $folder->setName( 'New ' . InterfaceLabelTable::getInstance()->get($this->getuser()->getGuardUser(), InterfaceLabelTable::FOLDER_TYPE) );
    $folder->setUser($this->getUser()->getGuardUser());
    $folder->setType(Folder::TYPE_ROADMAP);

    if (!Folder::getInstance()->getNotDeletableForUser($this->getUser()->getGuardUser(), Folder::TYPE_ROADMAP, false)) {
      $folder->setDeletable(false);
    }

    $folder->save();

    // Create log
    $log = new Log();
    $log->injectDataAndPersist($folder, $this->getUser()->getGuardUser(), array('action' => 'new'));

    return $this->renderText(json_encode($folder->getRowData()));
  }

  public function executeDeleteFolder(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $object = FolderTable::getInstance()->getForUser($request->getParameter('id'), $this->getUser()->getGuardUser());
    $this->forward404Unless($object);
    if ($object->deletable == 0) {
      $this->forward404();
    }

    Doctrine_Query::create()
      ->update('roadmap r')
      ->set('r.folder_id', FolderTable::getInstance()->getNotDeletableForUser($this->getUser()->getGuardUser(), Folder::TYPE_ROADMAP)->getId())
      ->where('r.folder_id=?', $object->id)
      ->execute();

    $object->delete();
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    return $this->renderText(json_encode(array('status' => 1)));
  }

  public function executeEditFolder(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $folder = $this->getRoute()->getObject();
    $form   = new FolderForm($folder);

    return $this->renderPartial('folder_form', array('form' => $form));
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

  /**
   * @param sfWebRequest $request
   * @return sfView
   * @throws sfError404Exception
   */
  public function executeAddToFolder(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $roadmap = RoadmapTable::getInstance()->getRoadmapForUser($this->getUser()->getGuardUser(), $request->getParameter('id'));
    $folder = FolderTable::getInstance()->getForUser($request->getParameter('folder_id'), $this->getUser()->getGuardUser(), Folder::TYPE_ROADMAP);
    $this->forward404Unless(is_object($roadmap));
    $this->forward404Unless(is_object($folder));

    $roadmap->setFolderId($request->getParameter('folder_id'));
    $roadmap->save();

    return $this->renderText(json_encode($roadmap->getRowData()));
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $roadmap = new Roadmap();

    $form = new RoadmapForm($roadmap, array('user' => $this->getUser()));
    return $this->renderPartial('form_new', array('form' => $form, 'type' => 'new'));
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));

    $to_step = $request->getParameter('to_step', 0);
    if ($request->getParameter('roadmap_id')) {
      $roadmap = RoadmapTable::getInstance()->find($request->getParameter('roadmap_id'));
      $this->forward404Unless(is_object($roadmap));
    } else {
      $roadmap = new Roadmap();
    }

    if ($to_step == 3) { // Finishing (from 2)
      $roadmap_decisions = json_decode($request->getParameter('roadmap_decisions', '{}'));

      foreach ($roadmap_decisions as $roadmap_decision) {
        if (DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $roadmap_decision)) {
          $roadmap->addDecision($roadmap_decision);
        }
      }

      $roadmap->RoadmapDecision->save();

      /** @var sfWebResponse $response */
      $response = $this->getResponse();
      $response->setStatusCode(200);
      $response->setContentType('text/json');

      return $this->renderText(json_encode(array(
        'status' => 'success'
      )));
    } elseif ($to_step == 2) { // From 1 to 2
      $form = new RoadmapCreateFirstStepForm($roadmap);
      $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
      if ($form->isValid()) {
        $form->save();

        $decisions = DecisionTable::getInstance()->getForUser($this->getUser()->getGuardUser(), true);

        /** @var sfWebResponse $response */
        $response = $this->getResponse();
        $response->setStatusCode(200, 'success');
        $response->setContentType('text/json');

        return $this->renderText(json_encode(array(
          'status'  => 'success',
          'roadmap' => $roadmap->getRowData(),
          'folder'  => $roadmap->getFolder()->getRowData(),
          'html'    => $this->getPartial('form_new_2', array('roadmap' => $roadmap, 'decisions' => $decisions))
        )));
      } else {
        $response = $this->getResponse();
        $response->setStatusCode(200);
        $response->setContentType('text/json');

        return $this->renderText(json_encode(array(
          'status' => 'validation_error',
          'html'   => $this->getPartial('form_new', array('form' => $form))
        )));
      }
    } elseif ($to_step == 1) { // From 2 to 1
      $form = new RoadmapCreateFirstStepForm($roadmap);
      return $this->renderPartial('form_new', array('form' => $form));
    }
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($roadmap = Doctrine_Core::getTable('Roadmap')->find(array($request->getParameter('id'))), sprintf('Object decision does not exist (%s).', $request->getParameter('id')));
    $form = new RoadmapForm($roadmap);

    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $form->save();

      // Create log
      $log = new Log();
      $log->injectDataAndPersist($roadmap, $this->getUser()->getGuardUser(), array('action' => 'edit'));

      // Process decisions
      $decisions_request = json_decode($request->getParameter('decisions_id'), true);
      $decisions         = array();
      foreach ($form->getObject()->getRoadmapDecision() as $roadmap_decision) {
        $decisions[] = $roadmap_decision->getDecision()->getId();
      }

      foreach (array_diff($decisions_request, $decisions) as $result) {
        if (DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $result)) {
          $form->getObject()->addDecision($result);
        }
      }

      $form->getObject()->RoadmapDecision->save();

      foreach (array_diff($decisions, $decisions_request) as $result) {
        RoadmapDecisionTable::getInstance()->findByRoadmapIdAndDecisionId($form->getObject()->getId(), $result)->delete();
      }

      return $this->renderText(json_encode($form->getObject()->getRowData()));
    }

    return $this->executeEdit($request, $form);
  }

  public function executeDashboard(sfWebRequest $request)
  {
    $this->roadmap = RoadmapTable::getInstance()->getRoadmapForUser($this->getUser()->getGuardUser(), $request->getParameter('id'));
    $this->forward404Unless(is_object($this->roadmap));

    $alternative_relations = array();
    $related_decisions = array();

    if ($this->roadmap->getShowDependencies()){
      foreach ($this->roadmap->getRoadmapDecision() as $roadmap_decision){
        foreach ($roadmap_decision->getDecision()->getAlternative() as $alternative) {
          /** @var $alternative Alternative */
          if (count($alternative->getAlternativeRelation())){
            $alternative_relations[$alternative->getDecisionId()][$alternative->getId()]['alternative'] = $alternative;
            foreach ($alternative->getAlternativeRelation() as $relation) {
              /** @var $relation AlternativeRelation */
              $related_decisions[$alternative->getDecisionId()][$relation->getAlternativeTo()->getDecisionId()] = $relation->getAlternativeTo()->getDecision()->getName();
              $alternative_relations[$alternative->getDecisionId()][$alternative->getId()]['relations'][$relation->getAlternativeTo()->getDecisionId()]['decision'] = $relation->getAlternativeTo()->getDecision();
              $alternative_relations[$alternative->getDecisionId()][$alternative->getId()]['relations'][$relation->getAlternativeTo()->getDecisionId()]['linked_alternatives'][] = $relation->getAlternativeTo();
            }
          }
        }
      }
    }

    $this->alternative_relations = $alternative_relations;
    $this->related_decisions = $related_decisions;
  }

  public function executeTimeline(sfWebRequest $request)
  {
    $this->roadmap = RoadmapTable::getInstance()->getRoadmapForUser($this->getUser()->getGuardUser(), $request->getParameter('id'));
    $this->forward404Unless(is_object($this->roadmap));

    $this->un_finished_decisions = array();

    $this->timeline_data = array('timeline' => array(
      'headline' => $this->prepare_string_to_json($this->roadmap->getName()),
      'type'     => 'default',
      'text'     => $this->prepare_string_to_json($this->roadmap->getDescription()),
      'date'     => array()
    ));

    $min_start_date = '';
    $max_end_date = '';

    $roadmap_decisions = RoadmapDecisionTable::getInstance()->createQuery('rd')
      ->leftJoin('rd.Decision d')
      ->leftJoin('d.Alternative a')
      ->where('rd.roadmap_id = ?', $this->roadmap->getId())
      ->orderBy('d.start_date ASC')
      ->execute();

    foreach ($roadmap_decisions as $roadmap_decision) {
      /** @var $roadmap_decision RoadmapDecision */
      if ($roadmap_decision->getDecision()->getStartDate()){
        $start_date = new DateTime($roadmap_decision->getDecision()->getStartDate());
        $end_date   = $roadmap_decision->getDecision()->getEndDate()? new DateTime($roadmap_decision->getDecision()->getEndDate()) : null;

        if (empty($min_start_date) || strtotime($min_start_date) > strtotime($roadmap_decision->getDecision()->getStartDate())){
          $min_start_date = $roadmap_decision->getDecision()->getStartDate();
        }

        if (empty($max_end_date) || ($roadmap_decision->getDecision()->getEndDate() && strtotime($max_end_date) < strtotime($roadmap_decision->getDecision()->getEndDate()))){
          $max_end_date = $roadmap_decision->getDecision()->getEndDate();
        }

        $releases = array();
        foreach ($roadmap_decision->getDecision()->getProjectRelease() as $project_release) {
          /** @var $project_release ProjectRelease */
          $releases[] = $project_release->getName() . ' - <a href="'. $this->getContext()->getRouting()->generate('planner', array('decision_id' => $roadmap_decision->getDecision()->getId())) . '">Edit</a>';
        }

        $tags = array();
        foreach ($roadmap_decision->getDecision()->getTagDecision() as $tag_decision) {
          /** @var $tag_decision TagDecision */
          $tags[] = '<span class="tag label label-info">' . $tag_decision->getTag()->getName() . '</span>';
        }

        $alternative_relations_text = '';
        $alternative_relations = array();
        $related_decisions = array();
        foreach ($roadmap_decision->getDecision()->getAlternative() as $alternative) {
          /** @var $alternative Alternative */
          if (count($alternative->getAlternativeRelation())){
            $alternative_relations[$alternative->getId()]['alternative'] = $alternative;
            foreach ($alternative->getAlternativeRelation() as $relation) {
              /** @var $relation AlternativeRelation */
              $related_decisions[$relation->getAlternativeTo()->getDecisionId()] = $relation->getAlternativeTo()->getDecision()->getName();
              $alternative_relations[$alternative->getId()]['relations'][$relation->getAlternativeTo()->getDecisionId()]['decision'] = $relation->getAlternativeTo()->getDecision();
              $alternative_relations[$alternative->getId()]['relations'][$relation->getAlternativeTo()->getDecisionId()]['linked_alternatives'][] = $relation->getAlternativeTo();
            }
          }
        }

        $relation_text = array();
        if (count($alternative_relations)){
          foreach ($alternative_relations as $alternative_relation){
            foreach ($alternative_relation['relations'] as $relation) {
              $temp_linked_alternatives = array();
              foreach ($relation['linked_alternatives'] as $linked_alternative){
                $temp_linked_alternatives[] .= '<a href="javascript:void(0)" data-edit-url="' . $this->getContext()->getRouting()->generate('alternative\edit', array('id' => $linked_alternative->getId())) . '" data-delete-url="' . $this->getContext()->getRouting()->generate('alternative\delete', array('id' => $linked_alternative->getId())) . '" class="alternative_edit" data-toggle="popover" data-content="'. $this->getComponent("roadmap", "alternativePopupInfo", array('alternative' => $linked_alternative)) .'">' . $linked_alternative->getName().'</a>';
              }
              $relation_text[] =  '<li> - <a href="javascript:void(0)" data-edit-url="' . $this->getContext()->getRouting()->generate('alternative\edit', array('id' => $alternative_relation['alternative']->getId())) . '" data-delete-url="' . $this->getContext()->getRouting()->generate('alternative\delete', array('id' => $alternative_relation['alternative']->getId())) . '" class="alternative_edit" data-toggle="popover" data-content="'. $this->getComponent("roadmap", "alternativePopupInfo", array('alternative' => $alternative_relation['alternative'])) .'" >' . $alternative_relation['alternative']->getName()."</a> has dependency to " . implode(', ', $temp_linked_alternatives) . " in project " . $relation['decision']->getName() ."</li>";
            }
          }

          $alternative_relations_text = "<b>This project has dependency to project(s) ".implode(', ', $related_decisions).'</b><ul>' . implode('', $relation_text);
        }

        // Delete link to itself
        if (array_key_exists($roadmap_decision->getDecision()->getId(), $related_decisions)){
          unset($related_decisions[$roadmap_decision->getDecision()->getId()]);
        }

        $this->timeline_data['timeline']['date'][] = array(
          'startDate'   => $start_date->format('Y,m,j'),
          'endDate'     => $end_date ? $end_date->format('Y,m,j') : '',
          'labelText'   => $this->prepare_string_to_json($roadmap_decision->getDecision()->getName()),
          'headline'    => $this->prepare_string_to_json($roadmap_decision->getDecision()->getName()) . ' <span style="font-size: 14px;">- <a href="javascript: void(0);" class="edit-decision" data-edit-url = "' . $this->getContext()->getRouting()->generate('decision\edit', array('id' => $roadmap_decision->getDecision()->getId())) .'" data-delete-url = "'. $this->getContext()->getRouting()->generate('decision\delete', array('id' => $roadmap_decision->getDecision()->getId())) . '">Edit</a></span>',
          'text'        => $this->prepare_string_to_json($roadmap_decision->getDecision()->getObjective() . '<div class="mr-top-15">' . implode(' ', $tags) . '</div>' . '<div class="mr-top-25 timeline-alternative-relations">' . $alternative_relations_text . '</div>'),
          'labelColor'  => $this->prepare_string_to_json($roadmap_decision->getDecision()->getColor() ? $roadmap_decision->getDecision()->getColor() : '#CCCCCC'),
          'status'      => $this->prepare_string_to_json($roadmap_decision->getDecision()->getStatus()),
          'decisionId' => $roadmap_decision->getDecision()->getId(),
          'linkedTo'    => array_keys($related_decisions),
          'asset'       => array(
            'media' => $this->prepare_string_to_json(count($releases)? '<h2>Releases</h2><ul><li>'. implode('</li><li>', $releases) .'</li></ul>' : ' ')
          )
        );
      } else {
        $this->un_finished_decisions[] = $roadmap_decision->getDecision();
      }
    }

/*    if (count($this->timeline_data['timeline']['date']) <=6) {
      $i = 1;
      foreach ($this->timeline_data['timeline']['date'] as $key => $date) {
        $this->timeline_data['timeline']['date'][$key]['tag'] = $i++;
      }
    }*/

    if ($min_start_date && $max_end_date){
      $this->timeline_data['timeline']['startDate'] = date('Y,m,j', strtotime($min_start_date) + ((strtotime($max_end_date) - strtotime($min_start_date)) / 2));
    }

    $this->timeline_data = json_encode($this->timeline_data);
  }

  private function prepare_string_to_json($string) {
//    $search = array('\\',"\n","\r","\f","\t","\b", "'", '"');
//    $replace = array('\\\\',"\\n", "\\r","\\f","\\t","\\b", '&#39;', '&#34;');
//    $string = str_replace($search,$replace,$string);

    $string = str_replace(PHP_EOL,'<br>',$string);
    $string = str_replace('"','&#34;',$string);
    $string = str_replace("'",'&#39;',$string);

    return $string;
  }

  public function executeEdit(sfWebRequest $request, $form = false)
  {
    $this->forward404Unless($roadmap = Doctrine_Core::getTable('Roadmap')->find(array($request->getParameter('id'))), sprintf('Object decision does not exist (%s).', $request->getParameter('id')));
    if (!$form) {
      $form = new RoadmapForm($roadmap);
    }

    $decisions            = DecisionTable::getInstance()->getForUser($this->getUser()->getGuardUser(), true);
    $un_grouped_decisions = $grouped_decisions = array();

    foreach ($decisions as $decision) {
      if ($decision->getFolderId()) {
        $grouped_decisions[$decision->getFolder()->getName()][] = $decision;
      } else {
        $un_grouped_decisions[] = $decision;
      }
    }

    $roadmap_decisions = array();
    foreach ($roadmap->getRoadmapDecision() as $roadmap_decision) {
      $roadmap_decisions[] = $roadmap_decision->getDecision()->getId();
    }

    return $this->renderPartial('form', array(
      'form'                 => $form,
      'grouped_decisions'    => $grouped_decisions,
      'un_grouped_decisions' => $un_grouped_decisions,
      'roadmap_decisions'    => $roadmap_decisions,
    ));
  }

  public function executeExport(sfWebRequest $request)
  {
    $roadmap = RoadmapTable::getInstance()->getRoadmapForUser($this->getUser()->getGuardUser(), $request->getParameter('id'));
    $this->forward404Unless(is_object($roadmap));

    header('Content-type: application/force-download');
    header('Content-Disposition: attachment; filename="' . $roadmap->name . '.pptx"');

    $exporter = new RoadmapPowerPointExporter();
    $exporter->setRoadmap($roadmap);
    $exporter->export();

    exit;
  }
}