<?php

class CriterionImporter extends ExcelImporter
{
  public function import()
  {
    $objPHPExcel = @PHPExcel_IOFactory::load($this->file->getTempName());

    /** @var PHPExcel_Worksheet $worksheet */
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet)
    {
      $rowIterator = $worksheet->getRowIterator();

      /** @var PHPExcel_Worksheet_Row $row  */
    	foreach ($rowIterator as $row)
      {
        $criterion = new Criterion();
        $criterion->Decision = $this->decision;

        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells();

        /** @var PHPExcel_Cell $cell*/
        foreach ($cellIterator as $cell)
        {
          if (!is_null($cell))
          {
            $column = $cell->getColumn();
            if ($column == 'A')
            {
              $criterion->name = $cell->getValue();
              $criterion->save();
            }
            else if ($column == 'B')
            {
              $criterion->description = $cell->getValue();
              $criterion->save();
            }
          }
        }
      }
    }
  }
}
 