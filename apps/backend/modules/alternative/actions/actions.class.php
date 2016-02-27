<?php

/**
 * criterion actions.
 *
 * @package    dmp
 * @subpackage criterion
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class alternativeActions extends BackendDefaultActions
{
  protected $model = 'Alternative';

  public function preExecute()
  {
    $context = $this->getContext();
    if ($context->getRequest()->hasParameter('decision_id')) {
      $context->getRouting()->setDefaultParameter('decision_id', $context->getRequest()->getParameter('decision_id'));
    }

    $this->forward404Unless($this->getUser()->verifyLightAccess($this->getContext()));
  }

  public function executeIndex(sfWebRequest $request)
  {
    parent::executeIndex($request);

    $this->tags = AlternativeTable::getInstance()->getTagsForProject($this->getUser()->getGuardUser(), $this->decision->getId());
  }

  public function executeNew(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());

    $alternative = new Alternative();
    $alternative->name = $request->getParameter('title', '');
    $alternative->decision_id = $request->getParameter('decision_id');

    $form = new AlternativeForm($alternative, array('user' => $this->getUser()));
    return $this->renderPartial('form', array('form' => $form, 'type' => 'new'));
  }

  public function executeCreate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));

    $alternative = new Alternative();
    $alternative->decision_id = $request->getParameter('decision_id');
    $alternative->setCreatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));
    $alternative->setUpdatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));

    $form = new AlternativeForm($alternative, array('user' => $this->getUser()));

    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $form->save();

      // Create log
      $log = new Log();
      $log->injectDataAndPersist($alternative, $this->getUser()->getGuardUser(), array('action' => 'new'));

      // Process tags
      $tags_request = json_decode($request->getParameter('tags'));
      foreach ($tags_request as $tag_request) {
        Tag::newTag($this->getUser()->getGuardUser(), $alternative->id, $tag_request, 'alternative');
      }

      // Process links
      foreach (json_decode($request->getParameter('links'), true) as $link_request) {
        $link = new AlternativeLink();
        $link->setLink($link_request['link']);
        $link->setAlternative($alternative);
        $link->save();
      }

      // Process related alternatives
      $related_alternatives = json_decode($request->getParameter('related_alternatives'), true);
      if ($related_alternatives) {
        foreach ($related_alternatives as $result) {
          if (AlternativeTable::getInstance()->getOneForUser($this->getUser()->getGuardUser(), $result)) {
            $alternative_relation = new AlternativeRelation();
            $alternative_relation->setFromId($alternative->getId());
            $alternative_relation->setToId($result);
            $alternative_relation->setCreatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));
            $alternative_relation->save();
          }
        }
      }

      $alternative->refresh(true);
      return $this->renderText(json_encode($alternative->getRowData()));
    } else {
      return $this->renderPartial('form', array('form' => $form, 'type' => 'new'));
    }
  }

  public function executeUpdate(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->forward404Unless($request->isMethod(sfRequest::POST) || $request->isMethod(sfRequest::PUT));
    $this->forward404Unless($alternative = Doctrine_Core::getTable($this->model)->find(array($request->getParameter('id'))), sprintf('Object decision does not exist (%s).', $request->getParameter('id')));
    $form = new AlternativeForm($alternative, array('user' => $this->getUser()));

    $alternative->setUpdatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));

    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid()) {
      $form->save();

      // Create log
      $log = new Log();
      $log->injectDataAndPersist($alternative, $this->getUser()->getGuardUser(), array('action' => 'edit'));

      // Process tags
      $tags_request = json_decode($request->getParameter('tags'));
      $tags = array();
      foreach ($alternative->getTagAlternative() as $tag) {
        $tags[] = $tag->Tag->name;
      }

      foreach (array_diff($tags_request, $tags) as $result) {
        Tag::newTag($this->getUser()->getGuardUser(), $request->getParameter('id'), $result, 'alternative');
      }

      foreach (array_diff($tags, $tags_request) as $result) {
        Tag::removeTag($this->getUser()->getGuardUser(), $request->getParameter('id'), $result, 'alternative');
      }

      // Process links
      $links = array();
      foreach ($alternative->getAlternativeLink() as $link) {
        $links[$link->id] = $link->link;
      }

      $links_request_new = array();
      $links_request_old = array();
      $links_request = json_decode($request->getParameter('links'), true);
      foreach ($links_request as $link_request) {
        if (is_array($link_request) && array_key_exists('id', $link_request)) {
          $links_request_old[$link_request['id']] = $link_request['link'];
        } else {
          $links_request_new[] = $link_request;
        }
      }

      foreach ($links_request_new as $link_request_new) {
        $link = new AlternativeLink();
        $link->link = $link_request_new['link'];
        $link->alternative_id = $request->getParameter('id');
        $link->save();
      }

      foreach (array_diff(array_keys($links), array_keys($links_request_old)) as $result) {
        AlternativeLinkTable::getInstance()->find($result)->delete();
      }

      foreach ($links_request_old as $key => $link) {
        if ($links_request_old[$key] !== $links[$key]) {
          $alternative_link = AlternativeLinkTable::getInstance()->find($key);
          $alternative_link->link = $links_request_old[$key];
          $alternative_link->save();
        }
      }

      // Process related alternatives
      $related_alternatives_request = json_decode($request->getParameter('related_alternatives'), true);
      if (!$related_alternatives_request){
        $related_alternatives_request = array();
      }
      $related_alternatives = array();
      foreach ($alternative->getAlternativeRelation() as $related_alternative) {
        $related_alternatives[] = $related_alternative->to_id;
      }

      foreach (array_diff($related_alternatives_request, $related_alternatives) as $result) {
        if (AlternativeTable::getInstance()->getOneForUser($this->getUser()->getGuardUser(), $result)){
          $alternative_relation = new AlternativeRelation();
          $alternative_relation->setFromId($alternative->getId());
          $alternative_relation->setToId($result);
          $alternative_relation->setCreatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));
          $alternative_relation->save();
        }
      }

      foreach (array_diff($related_alternatives, $related_alternatives_request) as $result) {
        AlternativeRelationTable::getInstance()->findByFromIdAndToId($alternative->getId(), $result)->delete();
      }

      $alternative->refresh(true);
      return $this->renderText(json_encode($alternative->getRowData()));
    } else {
      return $this->renderPartial('form', array('form' => $form));
    }
  }

  public function executeUpload(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    /** @var Alternative $alternative */
    $alternative = $this->getRoute()->getObject();
    $response = array();

    // Load files
    if ($request->getMethod() == 'GET') {
      foreach ($alternative->Files as $uploadedFile)
      {
        $response[] = $uploadedFile->getResponseObject();
      }
    } else {
      // Upload files

      $dir_path = '/alternative';

      $fileValidator = new sfValidatorFile(array(
        'required'   => true,
        'path'       => sfConfig::get('sf_upload_dir') . $dir_path,
      ));

      foreach ($request->getFiles('files') as $file) {
        $validatedFile = $fileValidator->clean($file);
        $uploadedFile = new UploadedFile();
        $uploadedFile->path = $dir_path . '/' . $validatedFile->save();
        $uploadedFile->mime_type = $validatedFile->getType();
        $uploadedFile->name = $validatedFile->getOriginalName();
        $uploadedFile->save();
        $alternative->Files->add($uploadedFile);
        $response[] = $uploadedFile->getResponseObject();
      }

      $alternative->save();
    }

    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    return $this->renderText(json_encode($response));
  }

  public function executeExcelExport(sfWebRequest $request)
  {
    $decision_id = $request->getParameter('decision_id', false);

    /** @var Decision $decision  */
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $decision_id);
    $this->forward404Unless($decision);

    $average_analyze = new AverageAnalyze();
    $average_analyze->setDecisionId($decision_id);
    $average_analyze->load();
    $measurement = $average_analyze->getMeasurement();

    $objPHPExcel = new sfPhpExcel();

    header('Content-type: application/force-download');
    header('Content-Disposition: attachment; filename="Alternatives (' . $decision->name . ').xlsx"');

    // Set properties
    $objPHPExcel->getProperties()->setCreator("SensorSix");
    $objPHPExcel->getProperties()->setLastModifiedBy("SensorSix");

    // Add some data
    $objPHPExcel->setActiveSheetIndex(0);

    $index = 1;
    $column_index = 0;
    $sheet = $objPHPExcel->getActiveSheet();

    // Add default header
    foreach (array('ID', 'Name', 'Status', 'Work progress', 'Tags', 'Additional info', 'Notes', 'Due date', 'Notify date', 'Created at', 'Created by', 'Updated at', 'Updated by', 'Custom fields') as $value){
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $value);
    }

    // Add criterias in header
    foreach ($average_analyze->getCriteriaNames() as $criteria_name) {
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $criteria_name);
    }

    foreach ($decision->getAlternative() as $alternative) {
      /** @var Alternative $alternative */
      $index++;
      $column_index = 0;

      $tags = array();
      foreach ($alternative->getTagAlternative() as $tag){
        $tags[] = $tag->Tag->name;
      }

      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $alternative->getItemId());
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $alternative->name);
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $alternative->status);
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $alternative->work_progress);
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, implode(', ', $tags));
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $alternative->additional_info);
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $alternative->notes);
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $alternative->due_date);
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $alternative->notify_date);
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $alternative->created_at);
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $alternative->created_by);
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $alternative->updated_at);
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, $alternative->updated_by);

      $custom_fields = array();
      foreach (json_decode($alternative->custom_fields, true) as $key => $value) {
        $custom_fields[] = $key . ': ' . $value;
      }
      $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index++) . $index, implode(PHP_EOL, $custom_fields));

      foreach ($average_analyze->getCriteriaNames() as $criteria_id => $criteria_name) {
        if (array_key_exists($alternative->getId(), $measurement[$criteria_id])) {
          $sheet->setCellValue(PHPExcel_Cell::stringFromColumnIndex($column_index) . $index, $measurement[$criteria_id][$alternative->getId()]->getAverage());
        }
        $column_index++;
      }
    }

    for ($i = 0; $i <= $column_index; $i++) {
      $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
    }

    $sheet->calculateColumnWidths();

    // Rename sheet
    $objPHPExcel->getActiveSheet()->setTitle('Alternatives');

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    // Save Excel 2007 file
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');

    exit;
  }

  public function executeImport(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $decision = DecisionTable::getInstance()->getDecisionForUser($this->getUser()->getGuardUser(), $request->getParameter('decision_id', false));

    $fileValidator = new sfValidatorFile(array(
      'required'   => true
    ));

    $importer = new AlternativeImporter();
    $importer->setDecision($decision);
    $importer->setCreatedAndUpdatedBy(Alternative::generateUpdateAndCreatedBy($this->getUser()->getGuardUser()));

    foreach ($request->getFiles('files') as $file) {
      $validatedFile = $fileValidator->clean($file);
      $importer->setFile($validatedFile);
      $importer->setGuardUser($this->getUser()->getGuardUser());
      $importer->advancedImport();
    }

    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');

    return $this->renderText(json_encode(array(array())));
  }
}
