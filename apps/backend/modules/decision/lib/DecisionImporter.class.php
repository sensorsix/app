<?php

class DecisionImporter extends ExcelImporter
{
  /**
   * @var Alternative[]
   */
  private $alternatives;

  protected $created_and_updated_by = '';

  /**
   * @return array
   */
  public function import()
  {
    $result = array();
    $objPHPExcel = @PHPExcel_IOFactory::load($this->file->getTempName());

    /** @var PHPExcel_Worksheet $worksheet */
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
      $rowIterator = $worksheet->getRowIterator();

      /** @var PHPExcel_Worksheet_Row $row */
      foreach ($rowIterator as $row) {
        $row_index = $row->getRowIndex();

        if ($row_index == 1) {
          $response = new Response();
          $response->decision_id = $this->decision->id;
          $response->ip_address = '';
          $response->email_address = 'import';
          $response->save();
        }

        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells();

        /** @var PHPExcel_Cell $cell */
        foreach ($cellIterator as $cell) {
          if (!is_null($cell)) {
            $column = $cell->getColumn();

            // The first column - alternative
            if ($column == 'A') {
              $alternative = new Alternative();
              $alternative->setDecisionId($this->decision->id);
              $alternative->setName($cell->getValue());
              $alternative->setCreatedBy($this->created_and_updated_by);
              $alternative->setUpdatedBy($this->created_and_updated_by);
              $alternative->save();
              $this->alternatives[$row_index] = $alternative;

              $result[] = $alternative->getName();
            } else if ($column == 'B') {
              if (array_key_exists($row_index, $this->alternatives)){
                $this->alternatives[$row_index]->additional_info = $cell->getValue();
                $this->alternatives[$row_index]->save();
              }
            }
          }
        }
      }
    }

    return $result;
  }

  /**
   * @return \Alternative[]
   */
  public function getAlternatives()
  {
    return $this->alternatives;
  }

  /**
   * @param string $created_and_updated_by
   */
  public function setCreatedAndUpdatedBy($created_and_updated_by)
  {
    $this->created_and_updated_by = $created_and_updated_by;
  }
}
