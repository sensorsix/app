<?php
 
class ResponseExcelExporter
{
  /**
   * @var ResponseTableView
   */
  private $responseTableView;

  /**
   * @var PHPExcel_Worksheet
   */
  private $sheet;

  private $row_index = 1;

  public function __construct(ResponseTableView $responseTableView)
  {    
    $this->responseTableView = $responseTableView;
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
    $this->buildFooter();
    
    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Releases');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Save Excel 2007 file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');    
  }
  
  private function buildHead()
  {
    $column_index = 0;
    foreach ($this->responseTableView->getHeaderData() as $header)
    {
      $column_name = Utility::getNameFromNumber($column_index++);
      $this->sheet->setCellValue($column_name . $this->row_index, $header);
      $this->sheet->getColumnDimension($column_name)->setWidth(30,0);
      $this->sheet->getStyle($column_name . $this->row_index)->getFont()->setBold(true);
    }
    $this->row_index++;
  }
  
  private function buildBody()
  {
    foreach ($this->responseTableView->getBodyData() as $row)
    {
      $column_index = 0;
      foreach ($row as $value)
      {
        $column_name = Utility::getNameFromNumber($column_index++);
        $this->sheet->setCellValue($column_name . $this->row_index, $value);
      }
      $this->row_index++;
    }
  }

  private function buildFooter()
  {
    $column_index = 0;
    foreach ($this->responseTableView->getFooterData() as $footer)
    {
      $column_name = Utility::getNameFromNumber($column_index++);
      $this->sheet->setCellValue($column_name . $this->row_index, $footer);
      $this->sheet->getStyle($column_name . $this->row_index)->getFont()->setBold(true);
    }
  }
}
