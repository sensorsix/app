
<!--
TODO needs to remove this complatey from IndexSuccess
<div id="fileupload">
  <span style="top: 0" data-toggle="tooltip" title="Import"
  data-placement="bottom" class="glyphicon glyphicon-import btn btn-default fileinput-button help-info">
    <i class="icon-plus icon-white"></i>
    <input type="file" name="files[]" multiple>
  </span>
-->
  <!-- The loading indicator is shown during file processing -->
  <div class="fileupload-loading"></div>
  <!-- The table listing the files available for upload/download -->
  <table role="presentation" class="table table-striped">
    <tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody>
  </table>
</div>

<!-- modal-gallery is the modal dialog used for the image gallery -->
<div id="modal-gallery" class="modal modal-gallery hide fade" data-filter=":odd" tabindex="-1">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">&times;</a>

    <h3 class="modal-title"></h3>
  </div>
  <div class="modal-body">
    <div class="modal-image"></div>
  </div>
  <div class="modal-footer">
    <a class="btn modal-download" target="_blank">
      <i class="icon-download"></i>
      <span>Download</span>
    </a>
    <a class="btn btn-success modal-play modal-slideshow" data-slideshow="5000">
      <i class="icon-play icon-white"></i>
      <span>Slideshow</span>
    </a>
    <a class="btn btn-info modal-prev">
      <i class="icon-arrow-left icon-white"></i>
      <span>Previous</span>
    </a>
    <a class="btn btn-primary modal-next">
      <span>Next</span>
      <i class="icon-arrow-right icon-white"></i>
    </a>
  </div>
</div>

<!-- The template to display files available for upload -->
<script id="template-upload-top" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="preview"><span class="fade"></span></td>
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button title="Start" class="btn btn-primary pull-right">
                    <i class="icon-upload icon-white"></i>
                    <span>Start</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button title="Cancel" class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>Cancel</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}

</script>
<!-- The template to display files available for download -->
<script id="template-download-top" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
  {% if (file.name) { %}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else { %}
            <td class="preview">{% if (file.thumbnail_url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" rel="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
            {% } %}</td>
            <td class="name">
                <a href="{%=file.url%}" title="{%=file.name%}" rel="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td></td>
        {% } %}
        <td class="delete" colspan="2" style="text-align:right">
            <button title="Delete" class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}">
                <i class="icon-trash icon-white"></i>
                <span>Delete</span>
            </button>
        </td>
    </tr>
  {% } %}
{% } %}

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
