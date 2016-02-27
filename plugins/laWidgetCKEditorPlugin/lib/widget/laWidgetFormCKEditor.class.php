<?php

/**
 * laWidgetFormCKEditor
 *
 * @uses sfWidgetFormTextarea
 * @package laWidgetCKEditorPlugin
 * @copyright Ladela Interactive
 */
class laWidgetFormCKEditor extends sfWidgetFormTextarea
{
/**
 * Constructor.
 *
 * Available options:
 *
 *  * theme:  The CKEditor theme
 *  * width:  Width
 *  * height: Height
 *  * config: The javascript configuration
 *
 * @param array $options     An array of options
 * @param array $attributes  An array of default HTML attributes
 *
 * @see sfWidgetForm
 */
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    $this->addOption('theme', 'advanced');
    $this->addOption('width');
    $this->addOption('height');
    //config for CK editor
    $this->addOption('config', array());
    $this->addOption('with_ckfinder', true);
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The value selected in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $name     = isset($attributes['name']) ? $attributes['name'] : $name;
    $textarea = parent::render($name, $value, $attributes, $errors);

    $id = $this->generateId($name, null);
    $config = json_encode(
      array_merge(
        array(
          'toolbar' => 'ShortToolbar'
        ),
        $this->getOption('config')
      )
    );

    $js = <<<HTML
<script type="text/javascript">
  $(function(){
    var editor_{$id} = CKEDITOR.replace( '{$name}',
      {$config}
    );
    var saveChanges = function () {
      var textarea = $('#{$id}');
      if (textarea.val() !=  editor_{$id}.getData()) {
        $('#{$id}').val(editor_{$id}.getData()).trigger('change');
      }
    };
    editor_{$id}.on('destroy', function () {
      editor_{$id}.removeListener('blur', saveChanges);
    });
    editor_{$id}.on('blur', saveChanges);
HTML;

    if ($this->getOption('with_ckfinder'))
    {
      $js .= "CKFinder.setupCKEditor( editor_{$id}, '/laWidgetCKEditorPlugin/ckfinder/' );";
    }

    $js .= <<<HTML
  });
</script>
HTML;

    return $js . $textarea;
  }

  public function getJavascripts()
  {
    return $this->getOption('with_ckfinder')
      ? array(
          '/laWidgetCKEditorPlugin/ckeditor/ckeditor.js',
          '/laWidgetCKEditorPlugin/ckfinder/ckfinder.js'
        )
      : array('/laWidgetCKEditorPlugin/ckeditor/ckeditor.js');
  }
}
