<?php
foreach ($upload_widget->getStylesheets() as $stylesheet => $media) {
  use_stylesheet($stylesheet);
}

foreach ($upload_widget->getJavaScripts() as $script) {
  use_javascript($script);
}

$sf_response->setTitle('Wizard Step 2');

decorate_with('wizard');
?>

<?php slot('navigation_links'); ?>
  <a class="steps-navigation step-prev wizard-steps-navigation" href="<?php echo url_for('@wizard') ?>?popup=true"><i class="fa fa-arrow-circle-o-left"></i></a>
  <a class="steps-navigation step-next wizard-steps-navigation" href="<?php echo url_for('@wizard\finish') ?>?popup=true"><i class="fa fa-arrow-circle-o-right"></i></a>
<?php end_slot(); ?>

<div class="row">
  <div class="col-md-9">
    <div id="wizard" class="form col-md-offset-3 col-md-6" role="form">
      <div class="wizard">
        <div class="table-row">
          <div class="table-cell">
            <div id="step1-ani" class="lead animated flipInX">
              <h3>Step 2/2 </h3>
              <label class="project-name" for="project-name"><b>Create the proposed <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?> of your <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?></b></label>
              <div class="input-group">
                <input type="text" name="name" class="form-control" id="feature-name" placeholder="New <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE) ?>"/>
              <span class="input-group-btn">
                <a style="line-height: 20px; top: 0;" id="add-feature" href="javascript:void(0)" title="Add feature" data-placement="bottom" class="add glyphicon glyphicon-plus btn btn-primary btn-small"></a>
              </span>
              </div>
              <ul id="features-list"  class="list-group">
                <?php foreach ($decision->Alternative as $alternative) : ?>
                  <li class="list-group-item"><i class="fa fa-tasks"></i> <?php echo $alternative->name ?></li>
                <?php endforeach ?>
              </ul>
              <!--
              <a class="btn btn-link skip-wizard" href="<?php echo url_for('@wizard\skip') ?>">Skip this wizard</a>
              -->
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-md-3">
    <div class="panel panel-default pull-right">
      <div class="panel-heading">
        <h3 class="panel-title">Import</h3>
      </div>
      <div class="panel-body">
        <p>You can import data from an Excel spreadsheet. Mandatory Fields that spreadsheet needs to have</p>
        <ul>
          <li>Column A:  Name</li>
          <li>Column B: Description</li>
        </ul>
        <p id="message-not-import"><b>Click here to import.</b></p>
        <p id="message-import" class="bg-info"><b>Importing <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::ITEM_TYPE, true) ?>. Please wait...</b></p>
        <?php $upload_widget->render('import') ?>
        <div class="file-upload"> </div>
      </div>
    </div>
  </div>
</div>

<script>
  function inIframe () {
    try {
      return window.self !== window.top;
    } catch (e) {
      return true;
    }
  }

  $(function () {

    $(".navbar-nav").hide();
    var lh = $(document).height()  + "px";

    $('.step-next, .step-prev').css('line-height',lh)


    if(inIframe()){
      $('header, .skip-wizard').css('display','none')
    }

    // Popovers initialization.
    $('.help-info').tooltip();

    var $features_list       = $('#features-list'),
        $feature_name        = $('#feature-name'),
        $feature_add         = $('#add-feature'),
        $message_not_import  = $('#message-not-import'),
        $message_import      = $('#message-import');

    $feature_add.on('click', function() {
      var name = $feature_name.val();
      $feature_name.val('');

      if (name) {
        $.post('<?php echo url_for('@wizard\alternativeSave') ?>', { name: name }, function () {
          $features_list.prepend( $('<li/>').addClass('list-group-item').text(name) );
        });
      }
    });

    $feature_name.on('keyup', function (e) {
      if (e.keyCode == 13) {
        $feature_add.trigger('click');
      }
    });

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
      url            : '<?php echo url_for('@wizard\alternativeImport') ?>',
      autoUpload     : true
    }).on('fileuploaddone', function () {
      $message_import.hide();
      $message_not_import.show();
      alert('The file was successfully imported');
      window.location = window.location;
    }).on('fileuploadfail', function () {
      $message_import.hide();
      $message_not_import.show();
      alert('There was a problem with the import of your file');
    });

    var screen_size = $(window).height();
    $('.wizard').css('height', screen_size-225 + 'px')
  });
</script>
