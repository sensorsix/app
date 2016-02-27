<div id="fileupload">
	<a data-toggle="tooltip" title="" data-placement="bottom" class="btn btn-default fileinput-button help-info" data-original-title="Import">
    <i class="fa fa-upload"></i> Import
    <input type="file" name="files[]" multiple=""> 
  </a>
</div>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
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