<?php

/**
 * uploaded_file actions.
 *
 * @package    dmp
 * @subpackage uploaded_file
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class uploaded_fileActions extends sfActions
{
 /**
  * Executes delete action
  *
  * @param sfWebRequest $request A request object
  * @return string
  */
  public function executeDelete(sfWebRequest $request)
  {
    $this->forward404Unless($request->isXmlHttpRequest());
    $this->getRoute()->getObject()->delete();
    $this->setLayout(false);
    $this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
    return $this->renderText(json_encode(true));
  }

  /**
   * Executes download action
   *
   * @param sfWebRequest $request A request object
   * @return string
   */
  public function executeDownload(sfWebRequest $request)
  {
    /** @var UploadedFile $uploadedFile  */
    $uploadedFile = $this->getRoute()->getObject();

    /** @var sfWebResponse $response */
    $response = $this->getResponse();
    $response->clearHttpHeaders();
    $response->addCacheControlHttpHeader('Cache-control','must-revalidate, post-check=0, pre-check=0');
    $response->setContentType($uploadedFile->mime_type);
    $response->setHttpHeader('Content-Disposition', 'attachment; filename="' . $uploadedFile->name . '"');
    $response->setContent(file_get_contents($uploadedFile->getAbsolutePath()));

    return sfView::NONE;
  }
}
