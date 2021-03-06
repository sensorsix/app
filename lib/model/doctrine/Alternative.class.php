<?php

/**
 * Alternative
 *
 * This class has been auto-generated by the Doctrine ORM Framework
 *
 * @package    dmp
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class Alternative extends BaseAlternative
{
  public function preSave($event)
  {
    if ($this->isNew()) {
      $this->type_id = $this->Decision->type_id;

      do{
        $id = mt_rand(1, 99999999);
      }while(count(Doctrine_Core::getTable('Alternative')->findByItemId($id)));

      $this->item_id = $id;
    }

    if (empty($this->due_date)){
      $this->due_date = null;
    }

    if (empty($this->notify_date)){
      $this->notify_date = null;
    }
  }

  /**
   * @param $event
   */
  public function postSave($event)
  {
    if (!$this->getNode()->isValidNode()) {
      $treeObject = $this->getTable()->getTree();
      $treeObject->createRoot($this);
    }
  }

  /**
   * @return string
   */
  public function getTooltip()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
    $result = '';

    foreach ($this->Files as $file) {
      if ($file->isImage()) {
        $result .= sprintf("<p><a class=\"colorbox\" href=\"/uploads%s\"><img style=\"max-width: 415px; max-height: 200px\" src='/uploads%s' alt='%s'/></a></p>", $file->path, $file->path, $file->name);
      } else {
        $result .= sprintf("<p><a href='%s'>%s</a></p>", url_for('@measure\download?id=' . $file->id), $file->name);
      }
    }

    foreach ($this->AlternativeLink as $link) {
      $href = preg_match('/^(http|ftp|https):\/\//', $link->link) ? '' : 'http://';
      $href .= $link->link;
      $result .= sprintf('<p><a href="%s" target="_blank">%s</a></p>', $href, $link->link);
    }

    return $this->additional_info . $result;
  }

  /**
   * @param $event
   */
  public function preDelete($event)
  {
    foreach ($this->Files as $file) {
      $file->delete();
    }
  }

  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->additional_info;
  }

  /**
   * @return array
   */
  public function getAPIData()
  {
    return array(
      'id'          => $this->id,
      'name'        => $this->name,
      'description' => $this->additional_info,
      'status'      => $this->status,
      'votes'       => $this->score
    );
  }


  /**
   * @return array
   */
  public function getRowData()
  {
    sfContext::getInstance()->getConfiguration()->loadHelpers('Url');
    sfContext::getInstance()->getConfiguration()->loadHelpers('Escaping');
    $links = $files = array();

    foreach ($this->Files as $file) {
      if ($file->isImage()) {
        $files[] = sprintf("<a class=\"colorbox\" href=\"/uploads%s\">%s</a>", $file->path, $file->name);
      } else {
        $links[] = sprintf("<a href='%s'>%s</a>", url_for('@uploaded_file\download?id=' . $file->id), $file->name);
      }
    }

    foreach ($this->AlternativeLink as $link) {
      $href = preg_match('/^(http|ftp|https):\/\//', $link->link) ? '' : 'http://';
      $href .= $link->link;
      $links[] = sprintf('<a href="%s" target="_blank">%s</a>', htmlentities(esc_js($href)), htmlentities(esc_js($link->link)));
    }

    $routing = sfContext::getInstance()->getRouting();
    return array(
      '_element_type'       => 'alternative',
      'id'                  => $this->id,
      'name'                => $this->name,
      'description'         => $this->additional_info,
      'description_preview' => substr($this->additional_info, 0, 500) . ((strlen($this->additional_info) > 500) ? " ..." : ''),
      'status'              => $this->status,
      'votes'               => $this->score,
      'assigned_to'         => $this->Assigned->getUsername(),
      'work_progress'       => $this->work_progress,
      'files'               => implode('</br>', $files),
      'links'               => implode('</br>', $links),
      'fetch_url'           => $routing->generate('alternative\fetch', array('id' => $this->id)),
      'edit_url'            => $routing->generate('alternative\edit', array('id' => $this->id)),
      'delete_url'          => $routing->generate('alternative\delete')
    );
  }

  /**
   * @param sfGuardUser $user
   * @return string
   */
  public static function generateUpdateAndCreatedBy($user)
  {
    return $user->first_name . ' ' . $user->last_name . ' (' . $user->username . ')';
  }
}
