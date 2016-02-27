<?php

class WallPowerPointExporter
{
  /**
   * @var int
   */
  private $decision_id;

  /**
   * @var WallPost[]
   */
  private $posts;

  public function load()
  {
    $this->posts = WallPostTable::getInstance()->createQuery('wp')
      ->leftJoin('wp.Wall w')
      ->where('w.decision_id = ?', $this->decision_id)
      ->execute();
  }

  public function export()
  {
    $objPHPPowerPoint = new PHPPowerPoint();
    $objPHPPowerPoint->getProperties()->setCreator("Maarten Balliauw");
    $objPHPPowerPoint->getProperties()->setLastModifiedBy("Maarten Balliauw");

    $currentSlide = $objPHPPowerPoint->getActiveSlide();
    foreach ($this->posts as $post)
    {
      if (!$currentSlide)
      {
        $currentSlide = $objPHPPowerPoint->createSlide();
      }

      $this->buildSlide($currentSlide, $post);
      $currentSlide = null;
    }

    $objWriter = PHPPowerPoint_IOFactory::createWriter($objPHPPowerPoint, 'PowerPoint2007');
    $objWriter->save('php://output');
  }

  private function buildSlide(PHPPowerPoint_Slide $currentSlide, WallPost $post)
  {
    // Graph image
    $shape = $currentSlide->createDrawingShape();
    $shape->setName($post->title);
    $shape->setDescription($post->title);
    $shape->setPath($post->getFile());
    $shape->setHeight(405);
    $shape->setWidth(792);
    $shape->setOffsetX(70);
    $shape->setOffsetY(135);

    // Title
    $shape = $currentSlide->createRichTextShape();
    $shape->setHeight(30);
    $shape->setWidth(600);
    $shape->setOffsetX(180);
    $shape->setOffsetY(50);
    $shape->createText($post->title);
    $shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );

    // Comment
    $shape = $currentSlide->createRichTextShape();
    $shape->setHeight(100);
    $shape->setWidth(600);
    $shape->setOffsetX(180);
    $shape->setOffsetY(550);
    $shape->createText($post->comment);
    $shape->getAlignment()->setHorizontal( PHPPowerPoint_Style_Alignment::HORIZONTAL_CENTER );
  }

  /**
   * @param int $decision_id
   */
  public function setDecisionId($decision_id)
  {
    $this->decision_id = $decision_id;
  }
}