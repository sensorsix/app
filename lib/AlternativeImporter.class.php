<?php

class AlternativeImporter extends ExcelImporter
{
  /** @var array Alternative[] */
  private $alternatives = array();

  protected $created_and_updated_by = '';

  private $guard_user;

  public function import()
  {
    $objPHPExcel = @PHPExcel_IOFactory::load($this->file->getTempName());

    /** @var PHPExcel_Worksheet $worksheet */
    foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
      $rowIterator = $worksheet->getRowIterator();

      /** @var PHPExcel_Worksheet_Row $row */
      foreach ($rowIterator as $row) {
        $alternative = new Alternative();
        $alternative->setDecision($this->decision);
        $alternative->setStatus('Reviewed');

        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells();

        /** @var PHPExcel_Cell $cell */
        foreach ($cellIterator as $cell) {
          if (!is_null($cell)) {
            $column = $cell->getColumn();
            if ($column == 'A') {
              $alternative->setName($cell->getValue());
            } else if ($column == 'B') {
              $alternative->setAdditionalInfo($cell->getValue());
            }
          }
        }

        if ($alternative->getName()){
          $alternative->setCreatedBy($this->created_and_updated_by);
          $alternative->setUpdatedBy($this->created_and_updated_by);
          $alternative->save();
          $this->alternatives[] = $alternative;
        }
      }
    }
  }

  public function advancedImport()
  {
    foreach ($this->prepareData() as $item) {
      $alternative = null;
      if (array_key_exists('id', $item) && !empty($item['id'])) {
        $alternative = AlternativeTable::getInstance()->createQuery('a')
          ->leftJoin('a.Decision d')
          ->leftJoin('d.User u')
          ->leftJoin('u.TeamMember tm')
          ->whereIn('d.user_id', sfGuardUserTable::getInstance()->getUsersInTeamIDs($this->guard_user))
          ->andWhere('a.item_id = ?', $item['id'])
          ->andWhere('d.id = ?', $this->decision->getId())
          ->fetchOne();
      }
      if (!is_object($alternative)) {
        $alternative = new Alternative();
        $alternative->setDecision($this->decision);
        $alternative->setCreatedBy($this->created_and_updated_by);
        $alternative->setUpdatedBy($this->created_and_updated_by);
      }

      $alternative->setName($item['name']);

      foreach (array('status', 'work progress', 'additional info', 'notes', 'due date', 'notify date') as $prop) {
        if (array_key_exists($prop, $item) && !empty($item[$prop])) {
          $alternative->{str_replace(' ', '_', $prop)} = $item[$prop];
        }
      }

      if (array_key_exists('tags', $item)) {
        // Process tags
        $tags_request = array_map('trim', explode(',', $item['tags']));
        $tags = array();
        foreach ($alternative->getTagAlternative() as $tag) {
          $tags[] = $tag->Tag->name;
        }

        foreach (array_diff($tags_request, $tags) as $result) {
          Tag::newTag($this->guard_user, $alternative->getId(), $result, 'alternative');
        }

        foreach (array_diff($tags, $tags_request) as $result) {
          Tag::removeTag($this->guard_user, $alternative->getId(), $result, 'alternative');
        }
      }

      $alternative->save();
    }
  }

  /**
   * @return mixed
   */
  public function prepareData()
  {
    $objPHPExcel = @PHPExcel_IOFactory::load($this->file->getTempName());

    return $this->removeIgnoredColumns($this->excelToArray($objPHPExcel, true));
  }

  /**
   * @param $data
   * @return mixed
   */
  private function removeIgnoredColumns($data)
  {
    foreach ($data as $key => $row) {
      unset($data[$key]['created at'], $data[$key]['created by'], $data[$key]['updated at'], $data[$key]['updated by'], $data[$key]['']);
    }

    return $data;
  }

  private function excelToArray(PHPExcel $objPHPExcel, $header=true)
  {
    //Get worksheet and built array with first row as header
    $objWorksheet = $objPHPExcel->getActiveSheet();

    //excel with first row header, use header as key
    if($header){
      $highestRow = $objWorksheet->getHighestRow();
      $highestColumn = $objWorksheet->getHighestColumn();
      $headingsArray = $objWorksheet->rangeToArray('A1:'.$highestColumn.'1',null, true, true, true);
      $headingsArray = $headingsArray[1];

      $r = -1;
      $namedDataArray = array();
      for ($row = 2; $row <= $highestRow; ++$row) {
        $dataRow = $objWorksheet->rangeToArray('A'.$row.':'.$highestColumn.$row,null, true, true, true);
//        if ((isset($dataRow[$row][array_search(strtolower('name'), array_map('strtolower', $headingsArray))])) && ($dataRow[$row][array_search(strtolower('name'), array_map('strtolower', $headingsArray))] > '')) {
          ++$r;
          foreach($headingsArray as $columnKey => $columnHeading) {
            $namedDataArray[$r][strtolower($columnHeading)] = $dataRow[$row][$columnKey];
          }
//        }
      }
    }
    else{
      //excel sheet with no header
      $namedDataArray = $objWorksheet->toArray(null,true,true,true);
    }

    return $namedDataArray;
  }

  /**
   * @return Alternative[]
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

  /**
   * @param $guard_user
   */
  public function setGuardUser($guard_user) {
    $this->guard_user = $guard_user;
  }
}
 