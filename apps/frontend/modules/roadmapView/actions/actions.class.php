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
class roadmapViewActions extends sfActions
{
  public function executeIndex(sfWebRequest $request)
  {
    /** @var Roadmap $roadmap */
    $this->roadmap = RoadmapTable::getInstance()->createQuery('r')
      ->where('token = ?', $request->getParameter('token', false))
      ->andWhere('active = ?', true)
      ->fetchOne();

    $this->forward404Unless(is_object($this->roadmap));

    if ($this->roadmap->getWorkspaceMode() == 'list') {
      $this->prepareDataForListView();
    }else{
      $this->prepareDataForTimelineView();
    }
  }

  private function prepareDataForListView()
  {
    $alternative_relations = array();
    $related_decisions     = array();

    if ($this->roadmap->getShowDependencies()) {
      foreach ($this->roadmap->getRoadmapDecision() as $roadmap_decision) {
        foreach ($roadmap_decision->getDecision()->getAlternative() as $alternative) {
          /** @var $alternative Alternative */
          if (count($alternative->getAlternativeRelation())) {
            $alternative_relations[$alternative->getDecisionId()][$alternative->getId()]['alternative'] = $alternative;
            foreach ($alternative->getAlternativeRelation() as $relation) {
              /** @var $relation AlternativeRelation */
              $related_decisions[$alternative->getDecisionId()][$relation->getAlternativeTo()->getDecisionId()]                                                                  = $relation->getAlternativeTo()->getDecision()->getName();
              $alternative_relations[$alternative->getDecisionId()][$alternative->getId()]['relations'][$relation->getAlternativeTo()->getDecisionId()]['decision']              = $relation->getAlternativeTo()->getDecision();
              $alternative_relations[$alternative->getDecisionId()][$alternative->getId()]['relations'][$relation->getAlternativeTo()->getDecisionId()]['linked_alternatives'][] = $relation->getAlternativeTo();
            }
          }
        }
      }
    }

    $this->alternative_relations = $alternative_relations;
    $this->related_decisions     = $related_decisions;
  }

  private function prepareDataForTimelineView()
  {

    $timeline_data = array('timeline' => array(
      'headline' => $this->roadmap->getName(),
      'type'     => 'default',
      'text'     => $this->roadmap->getDescription(),
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
          $releases[] = $project_release->getName();
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
                $temp_linked_alternatives[] .= $linked_alternative->getName();
              }
              $relation_text[] =  '<li> - ' . $alternative_relation['alternative']->getName()." has dependency to " . implode(', ', $temp_linked_alternatives) . " in project " . $relation['decision']->getName() ."</li>";
            }
          }

          $alternative_relations_text = "<b>This project has dependency to project(s) ".implode(', ', $related_decisions).'</b><ul>' . implode('', $relation_text);
        }

        // Delete link to itself
        if (array_key_exists($roadmap_decision->getDecision()->getId(), $related_decisions)){
          unset($related_decisions[$roadmap_decision->getDecision()->getId()]);
        }

        $timeline_data['timeline']['date'][] = array(
          'startDate'   => $start_date->format('Y,m,j'),
          'endDate'     => $end_date ? $end_date->format('Y,m,j') : '',
          'labelText'   => $roadmap_decision->getDecision()->getName(),
          'headline'    => $roadmap_decision->getDecision()->getName(),
          'text'        => $roadmap_decision->getDecision()->getObjective() . '<div class="mr-top-15">' . implode(' ', $tags) . '</div>' . '<div class="mr-top-25 timeline-alternative-relations">' . $alternative_relations_text . '</div>',
          'labelColor'  => $roadmap_decision->getDecision()->getColor() ? $roadmap_decision->getDecision()->getColor() : '#CCCCCC',
          'status'      => $roadmap_decision->getDecision()->getStatus(),
          'decisionId' => $roadmap_decision->getDecision()->getId(),
          'linkedTo'    => array_keys($related_decisions),
          'asset'       => array(
            'media' => count($releases)? '<h2>Releases</h2><ul><li>'. implode('</li><li>', $releases) .'</li></ul>' : ' '
          )
        );
      }
    }

    if ($min_start_date && $max_end_date){
      $timeline_data['timeline']['startDate'] = date('Y,m,j', strtotime($min_start_date) + ((strtotime($max_end_date) - strtotime($min_start_date)) / 2));
    }

    $this->timeline_data = $timeline_data;
  }

}
