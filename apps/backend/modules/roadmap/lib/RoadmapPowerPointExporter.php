<?php

class RoadmapPowerPointExporter
{
  /**
   * @var Roadmap
   */
  private $roadmap;

  public function export()
  {
    $objPHPPowerPoint = new PHPPowerPoint();
    $objPHPPowerPoint->getProperties()->setCreator("SensorSix");
    $objPHPPowerPoint->getProperties()->setLastModifiedBy("SensorSix");

    $currentSlide = $objPHPPowerPoint->getActiveSlide();

    $this->buildTitleSlide($currentSlide);

    foreach ($this->roadmap->getOrderedRoadmapDecision() as $roadmap_decision) {
      $currentSlide = $objPHPPowerPoint->createSlide();
      $this->buildSlide($currentSlide, $roadmap_decision->getDecision());
    }

    $objWriter = PHPPowerPoint_IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
    $objWriter->save('php://output');
  }

  /**
   * @param PHPPowerPoint_Slide $currentSlide
   */
  private function buildTitleSlide(PHPPowerPoint_Slide $currentSlide)
  {
    // Title
    $shape = $currentSlide->createRichTextShape();
    $shape->setHeight(30);
    $shape->setWidth(600);
    $shape->setOffsetX(180);
    $shape->setOffsetY(50);
    $shape->createText($this->roadmap->getName());
    $shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );

    // Status
    $shape = $currentSlide->createRichTextShape();
    $shape->setHeight(25);
    $shape->setWidth(180);
    $shape->setOffsetX(760);
    $shape->setOffsetY(80);
    $shape->createText($this->roadmap->getStatus());
    $shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_RIGHT );

    // Description
    if ($this->roadmap->getShowDescription()){
      $shape = $currentSlide->createRichTextShape();
      $shape->setHeight(500);
      $shape->setWidth(920);
      $shape->setOffsetX(20);
      $shape->setOffsetY(140);
      $shape->createText(strip_tags($this->roadmap->getDescription()));
      $shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );
    }
  }

  /**
   * @param PHPPowerPoint_Slide $currentSlide
   * @param Decision $decision
   */
  private function buildSlide(PHPPowerPoint_Slide $currentSlide, Decision $decision)
  {
    // Title
    $shape = $currentSlide->createRichTextShape();
    $shape->setHeight(30);
    $shape->setWidth(600);
    $shape->setOffsetX(180);
    $shape->setOffsetY(50);
    $shape->createText($decision->getName());
    $shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );

    // Other info
    $shape = $currentSlide->createRichTextShape();
    $shape->setHeight(500);
    $shape->setWidth(920);
    $shape->setOffsetX(20);
    $shape->setOffsetY(80);
    $shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_LEFT );

    $textRun = $shape->createTextRun('Start/End date');
    $textRun->getFont()->setBold(true);
    $shape->createTextRun(': ');

    if ($decision->getStartDate()){
      $shape->createTextRun(DateTime::createFromFormat('Y-m-d H:i:s', $decision->getStartDate())->format('j M Y'));
    }else{
      $textRun = $shape->createTextRun('not set');
      $textRun->getFont()->setItalic(true);
    }

    $shape->createTextRun(' - ');

    if ($decision->getEndDate()){
      $shape->createTextRun(DateTime::createFromFormat('Y-m-d H:i:s', $decision->getEndDate())->format('j M Y'));
    }else{
      $textRun = $shape->createTextRun('not set');
      $textRun->getFont()->setItalic(true);
    }

    $shape->createBreak();

    $textRun = $shape->createTextRun('Status');
    $textRun->getFont()->setBold(true);
    $shape->createTextRun(': ' . $decision->getStatus());

    $shape->createBreak();

    $tags = array();
    foreach ($decision->getTagDecision() as $tag_decision){
      $tags[] = $tag_decision->getTag()->getName();
    }

    $textRun = $shape->createTextRun('Tags');
    $textRun->getFont()->setBold(true);
    $shape->createTextRun(': ' . implode(', ', $tags));

    $shape->createBreak();
    $shape->createBreak();

    $shape->createTextRun(strip_tags($decision->getObjective()));

    if ($this->roadmap->getShowReleases() && count($decision->getProjectRelease())){
      $shape->createBreak();
      $shape->createBreak();
      $textRun = $shape->createTextRun('Releases');
      $textRun->getFont()->setSize(14);
      $shape->createBreak();

      foreach ($decision->getProjectRelease() as $project_release){
        /** @var ProjectRelease $project_release */
        $textRun = $shape->createTextRun('    ' . $project_release->getName());
        $textRun->getFont()->setSize(12);
        $shape->createBreak();

        if ($this->roadmap->getShowItems()){
          foreach ($project_release->getProjectReleaseAlternative() as $project_release_alternative){
            /** @var ProjectReleaseAlternative $project_release_alternative */
            $shape->createTextRun('        ' . $project_release_alternative->getAlternative()->getName());
            $shape->createBreak();
          }
        }
      }
    }

    if ($this->roadmap->getShowDependencies()){
      $alternative_relations_text = '';
      $alternative_relations = array();
      $related_decisions = array();
      foreach ($decision->getAlternative() as $alternative) {
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

      if (count($alternative_relations)){
        $shape->createBreak();
        $shape->createTextRun('This project has dependency to project(s) '. implode(', ', $related_decisions))->getFont()->setBold(true);

        foreach ($alternative_relations as $alternative_relation){
          foreach ($alternative_relation['relations'] as $relation) {
            $temp_linked_alternatives = array();
            foreach ($relation['linked_alternatives'] as $linked_alternative){
              $temp_linked_alternatives[] .= $linked_alternative->getName();
            }

            $shape->createBreak();
            $shape->createTextRun(' - '.$alternative_relation['alternative']->getName()." has dependency to " . implode(', ', $temp_linked_alternatives) . " in project " . $relation['decision']->getName());
          }
        }
      }
    }
  }

  /**
   * @param \Roadmap $roadmap
   */
  public function setRoadmap($roadmap)
  {
    $this->roadmap = $roadmap;
  }
}