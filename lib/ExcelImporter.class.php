<?php
 
abstract class ExcelImporter
{
  /**
   * @var sfValidatedFile
   */
  protected  $file;

  /**
   * @var Decision
   */
  protected $decision;

  /**
   * @param sfValidatedFile $file
   */
  public function setFile($file)
  {
    $this->file = $file;
  }

  /**
   * @param Decision $decision
   */
  public function setDecision($decision)
  {
    $this->decision = $decision;
  }

  abstract public function import();
}
 