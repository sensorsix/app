<?php
 
class AlternativesExcelExporter
{
  /**
   * @var AlternativesAnalyze
   */
  private $analyze;

  /**
   * @var PHPExcel_Worksheet
   */
  private $sheet;

  private $row_index = 1;

  public function __construct(AlternativesAnalyze $analyze)
  {
    $this->analyze = $analyze;
  }

  public function export()
  {
    $objPHPExcel = new sfPhpExcel();
    // Set properties
    $objPHPExcel->getProperties()->setCreator("SensorSix");
    $objPHPExcel->getProperties()->setLastModifiedBy("SensorSix");

    $objPHPExcel->setActiveSheetIndex(0);
    $this->sheet = $objPHPExcel->getActiveSheet();

    $this->buildHead();
    $this->buildBody();

    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Alternatives');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Save Excel 2007 file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
  }

  private function buildHead()
  {
    $column_index = 1;
    foreach ($this->analyze->getCriteriaNames() as $criterion_name)
    {
      $column_name = Utility::getNameFromNumber($column_index++);
      $this->sheet->setCellValue($column_name . $this->row_index, $criterion_name);
      $this->sheet->getColumnDimension($column_name)->setWidth(30,0);
      $this->sheet->getStyle($column_name . $this->row_index)->getFont()->setBold(true);
    }
    $this->row_index++;
  }

  private function buildBody()
  {
    $column_index = 0;
    $row_index = $this->row_index;
    $column_name = Utility::getNameFromNumber($column_index);
    foreach ($this->analyze->getAlternativesNames() as $alternative_name)
    {
      $this->sheet->setCellValue($column_name . $row_index, $alternative_name);
      $row_index++;
    }

    $column_index = 1;
    $column_name = Utility::getNameFromNumber($column_index);
    foreach ($this->analyze->getData() as $alternatives)
    {
      $row_index = $this->row_index;
      foreach ($alternatives as $value)
      {
        $this->sheet->setCellValue($column_name . $row_index++, $value);
      }
      $column_name = Utility::getNameFromNumber(++$column_index);
    }
  }
}
 