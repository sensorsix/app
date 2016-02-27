<?php
/**
 * @var sfWebResponse $sf_response
 * @var DecisionForm $form
 * @var laWidgetFileUpload $upload_widget
 * @var string $base_url
 * @var array $routes
 */
use_stylesheets_for_form($form);
use_javascripts_for_form($form);

$sf_response->setSlot('menu_decision_active', true);
decorate_with('steps_layout');
?>

<?php slot('sidebar'); ?>
  <?php include_partial("global/leftSidebar", array());?>
<?php end_slot(); ?>

<?php slot('app_name'); ?>
  <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?></p> overview
<?php end_slot(); ?>

<?php slot('app_toolbar'); ?>
<a id="add-project" href="javascript:void(0)" class="btn btn-primary"><i class="fa fa-plus"></i>  Add <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?></p></a>

<a id="add-folder" href="javascript:void(0)" class="btn btn-default"><i class="fa fa-plus"></i>  Add <p class='label_capitalize'><?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::FOLDER_TYPE) ?></p></a>
<?php end_slot(); ?>

<!-- Modal -->
<div class="modal fade" id="editRowModal" tabindex="-1" role="dialog" aria-labelledby="editRowModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" id="editRowContent">

    </div>
  </div>
</div>

<table id="project-table" class="table">
  <tbody class="main"></tbody>
</table>

<script type="text/template" id="folderTemplate">
  <td colspan="3">
    <div class="panel panel-default">
      <div style="padding-bottom:10px" class="panel-heading">
        <table class="header-table" style="width:100%;">
          <tr class="folder">
            <td class="small">
              <a class="folder-icon-wrapper" href="javascript:void(0)">
                <span data-url="<%- state_url %>" class="fa <%- open ? 'fa-minus-square-o' : 'fa-plus-square-o' %> folder-icon"></span>
              </a>&nbsp;
              <span class="folder-name"><%- name %></span>
              <a href="javascript:void(0)" title="Edit" class="edit">Edit</a>
            </td>
          </tr>
        </table>
      </div>
      <div class="panel-body" style="padding: 0;<%- open ? '' : 'display:none;' %>">
        <div class="table-view-holder-wrapper">
          <div class="table-view-holder" data-id="<%- id %>"></div>
        </div>
      </div>
    </div>
  </td>
</script>

<script type="text/javascript">
  /*<![CDATA[*/
  $(function () {
    var $features_list       = $('#features-list'),
        $feature_name        = $('#feature-name'),
        $feature_add         = $('#add-feature'),
        $modal               = $('#myModal'),
        $message_not_import  = $('#message-not-import'),
        $message_import      = $('#message-import'),
        dashboard_url        = '',
        $projectName         = $("#project-name"),
        $firstStepError      = $('#first-step-error');

    // Popovers initialization.
    $('.help-info').tooltip();

    // Excel import
    $('#fileupload').fileupload({
      add: function(e, data) {
        var uploadErrors = [];
        var acceptFileTypes = /^application\/(vnd\.openxmlformats-officedocument\.spreadsheetml\.sheet|vnd\.ms-excel|msexcel|x-msexcel|x-ms-excel|x-excel|x-dos_ms_excel|xls|x-xls)$/;
        if(data.originalFiles[0]['type'].length && !acceptFileTypes.test(data.originalFiles[0]['type'])) {
          uploadErrors.push('Not an accepted file type (.xls or .xlsx)');
        }
        if(data.originalFiles[0]['size'].length && data.originalFiles[0]['size'] > 20000000) {
          uploadErrors.push('File size is too big');
        }
        if(uploadErrors.length > 0) {
          alert(uploadErrors.join("\n"));
        } else {
          $message_not_import.hide();
          $message_import.show();
          data.submit();
        }
      },
      url            : '<?php echo url_for('@decision\import') ?>',
      autoUpload     : true
    }).on('fileuploaddone', function (e, data) {
      if (data.result.status == 'success'){
        $.each( data.result.items, function( index, value ){
          $features_list.prepend( $('<li/>').addClass('list-group-item').text(value) );
        });
      }
      $message_import.hide();
      $message_not_import.show();
      alert('The file was successfully imported');
    }).on('fileuploadfail', function () {
      $message_import.hide();
      $message_not_import.show();
      alert('There was a problem with the import of your file');
    });

    $modal.on('shown.bs.modal', function(){
      var lh = $('.modal-content').css('height').replace('px', '') - 200 + "px";
      dashboard_url = '';
      $('.step-next, .step-prev').css('line-height', '510px');
      $('#next_1').attr('href', 'javascript:void(0)');
      $('#next_2').attr('href', '<?php echo url_for('@decision\skip') ?>');

      $firstStepError.text('').hide();
    }).on('hidden.bs.modal', function () {
      if (dashboard_url) {
        window.location.href = dashboard_url;
      }
    });

    $('#advanced_option_for_wizard_btn').click(function(){
      var $icon = $(this).find('i');
      $('#advanced_option_for_wizard').slideToggle('200');

      if ( $icon.hasClass('fa fa-plus-square-o') ) {
        $icon.attr('class','fa fa-minus-square-o')
      } else  {
        $icon.attr('class','fa fa-plus-square-o')
      }

    });
    $('#decision_type_id :nth-child(2)').prop('selected', true);

    $("#next_1").click(function() {
      $("#step_1").hide();

      $firstStepError.text('').hide();

      if ($.trim($projectName.val()) == '') {
        $projectName.val('New <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?>')
      }

      $.ajax({
        type      : "POST",
        url       : '<?php echo url_for('wizard/decisionModalSave'); ?>',
        dataType  : 'json',
        data      : {
          "name"       : $projectName.val(),
          "type_id"    : $("#decision_type_id").val(),
          "template_id": $("#decision_template_id").val()
        },
        beforeSend: function (data) {
          $("#preloader").show();
        },
        success   : function (data) {
          if (data.status === 'success') {
            dashboard_url = data.dashboard_url;
            $("#preloader").hide();
            $("#step_2").show();
          }else{
            $("#preloader").hide();
            $("#step_1").show();

            $firstStepError.text(data.message).show();
          }
        },
        error     : function (data) {
          $("#preloader").hide();
          $("#step_1").show();
        }
      });
    });

    $("#prev").click(function() {
      $("#step_2").hide();
      $("#step_1").show();
    });

// Show only templates related to selected type


    $feature_add.on('click', function() {
      var name = $feature_name.val();
      $feature_name.val('');

      if (name) {
        var url = '<?php echo url_for('wizard/alternativeModalSave'); ?>';
        $.ajax({
          type: "POST",
          url: url,
          data: {"name": name},
          beforeSend:function(data){

          },
          success:function(data){
            $features_list.prepend( $('<li/>').addClass('list-group-item').text(name) );
          },
          error:function(data){
            console.log(data.error);
          }
        });
      }
    });

    $feature_name.on('keyup', function (e) {
      if (e.keyCode == 13) {
        $feature_add.trigger('click');
      }
    });

    //////////////////////////////////////////////
    var folderCollection  = new RowCollection,
        tableView         = new TableView({el: $("#project-table"), type: 'decision', addToFolderURL: '<?php echo url_for('decision\addToFolder') ?>' });
    folderCollection.reset(<?php echo $sf_data->getRaw('folders_json') ?>);
    tableView.addFolders(folderCollection);
    //////////////////////////////////////////////

    $('#add-project').on('click', function() {
      $.get('<?php echo url_for('decision\new') ?>', function (response) {
        tableView.addNewProject(response);
      });
    });

    $('#add-folder').on('click', function () {
      $.get('<?php echo url_for('decision\newFolder') ?>', function (response) {
        tableView.addNewFolder(response);
      }, 'json');
    });
  });

  setTimeout(function () {
    var a = document.createElement("script");
    var b = document.getElementsByTagName("script")[0];
    a.src = document.location.protocol + "//dnn506yrbagrg.cloudfront.net/pages/scripts/0020/3732.js?" + Math.floor(new Date().getTime() / 3600000);
    a.async = true;
    a.type = "text/javascript";
    b.parentNode.insertBefore(a, b)
  }, 1);
  /*]]>*/
</script>
