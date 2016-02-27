<span id="fileupload-top">
  <a data-toggle="tooltip" title="Import" data-placement="bottom" class="btn btn-default fileinput-button help-info">
    <i class="fa fa-upload"></i> Import
    <input type="file" name="files[]" multiple>
  </a>
</span><br>

<!-- The template to display files available for upload -->
<script id="template-upload-top" type="text/x-tmpl">
</script>
<!-- The template to display files available for download -->
<script id="template-download-top" type="text/x-tmpl">
</script>

<?php
if ($widget->getOption('include_js'))
{
  foreach ($widget->getJavaScripts() as $javascript)
  {
    echo javascript_include_tag($javascript);
  }
}
?>