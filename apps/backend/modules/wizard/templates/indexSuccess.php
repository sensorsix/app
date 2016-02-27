<?php
/**
 * @var sfWebResponse $sf_response
 * @var DecisionForm $form
 * @var string $value
 */
$sf_response->setTitle('Wizard Step 1');
decorate_with('wizard');
?>

<?php slot('navigation_links'); ?>
<a id="next" class="steps-navigation step-next step-next-disabled wizard-steps-navigation" href="javascript:void(0)"><i
  class="fa fa-arrow-circle-o-right"></i></a>
<?php end_slot(); ?>

<div class="row">
  <div class="col-md-12">
    <div class="wizard">
      <div class="table-row">
        <div class="table-cell">
          <form id="wizard" class="form col-md-offset-2 col-md-8" role="form" action="<?php echo url_for('@wizard\decisionSave') ?>" method="post">
            <!--
            <style media="all">
              #welcome-txt {
                -webkit-animation-duration: 1s;
                -webkit-animation-delay: 1s;

              }
              #step1-ani {

                -webkit-animation-duration: 1s;
                -webkit-animation-delay: 1s;

              }
            </style>
            <p id="welcome-txt" class="lead animated flipInX">
              Welcome to SensorSix! <br>
              It looks like you don't have any projects yet on your profile. Let us help you to create one so you can get started.
            </p>
          -->
            <div id="step1-ani" class="animated flipInX">
              <div class="row">
                <div class="col-md-12">
                  <h2>Welcome to SensorSix</h2>
                  <p>To get started let's create a project. You can name your project anything or if you are using Trello you can import one of your Trello boards to get started.</p>
                  <hr>
                </div>
              </div>
              <div class="row">
                <div class="col-md-5">


                  <label class="project-name" for="project-name"><b>Type the name of your <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?></b></label>
                  <div>
                    <input type="text" name="name" class="form-control input-lg" id="project-name" value="<?php echo $value ?>" placeholder="New <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?>"/>
                  </div>

                  <div style="display:none">
                    <hr>
                    <label class="project-name" for="project-name"><b>Choose <?php echo InterfaceLabelTable::getInstance()->get($sf_data->getRaw('sf_user')->getGuardUser(), InterfaceLabelTable::PROJECT_TYPE) ?> layout</b></label>
                    <?php echo $form['type_id'] ?>
                    <?php echo $form['template_id'] ?>
                    <script type="text/javascript" charset="utf-8">
                      $('#decision_type_id :nth-child(2)').prop('selected', true);
                    </script>
                  </div>
                </div>
                <div class="col-md-5 col-md-offset-2">
                  <?php if (!$form->getObject()->getId()): ?>


                    <label class="project-name" for="project-name"><b>...or import from Trello</b></label>
                    <div style="margin-top: 29px;">
                      <div id="trello-logged-out" class="text-center">
                        <a id="connectLink" href="javascript:void(0)" class="btn btn-default">Connect To Trello</a>
                      </div>

                      <div id="trello-logged-in" style="display: none;">

                          <a id="disconnect" href="javascript:void(0)" class="btn btn-warning pull-right">Disconnect from Trello</a>
                          <p>Logged in to as <span id="fullName"></span></p>
                          <hr>


                        <div id="trello-output"></div>

                        <p id="message-import" class="bg-info mr-top-15"><b>Importing. Please wait...</b></p>

                        <a id="submit-import" class="btn btn-default pull-right mr-top-15" href="javascript:void(0)" style="display: none;">Import</a>
                      </div>

                      <div id="trello-errors"></div>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
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

    if( $('#project-name').val().length != 0 ) {
      $('.step-next').removeClass('step-next-disabled')
    }

    $('#project-name').keyup(function() {
      if($(this).val().length == 0){
        $('.step-next').addClass('step-next-disabled')
      }else{
        $('.step-next').removeClass('step-next-disabled')
      }
    });

    if(inIframe()){
      $('header, .skip-wizard').css('display','none')
    }

    $('#next').on('click', function() {
      $('#wizard').submit();
    });
    var screen_size = $(window).height();
    $('.wizard').css('height', screen_size-225 + 'px')

    var templates = <?php echo $form->getTemplatesJson() ?>, template_select = $('#decision_template_id');

    // Show only templates related to selected type
    $('#decision_type_id').change(function () {
      var type_id = $(this).val(), type_templates = templates[type_id];
      template_select.html('');
      for (var template_id in type_templates) {
        template_select.append($('<option/>').val(template_id).text(type_templates[template_id]));
      }
      template_select.trigger('change');
    }).change();


    <?php if (!$form->getObject()->getId()): ?>
    //TRELLO

    var trello_member,
      organizations = [];

    function loadTrelloData(){
      // Output a list of all of the cards that the member
      // is assigned to
      var $boards = $("<div>");

      $("#trello-output")
        .html($('<label/>').addClass('project-name').attr('for', 'project-name').html($('<b/>').text('Choose Trello board to import')))
        .append($boards);

      $boards.html($("<div>").text("Loading Boards..."));

      var deferreds_3 = [],
        $selector = $("<select>")
          .addClass('form-control input-lg trello-boards-selector')
          .append($("<optgroup/>").attr({label: 'My Boards', id: 'opt_main'}));

      $.each(trello_member.idOrganizations, function(ix, idOrganization) {
        var d3 = new $.Deferred();
        deferreds_3.push(d3);

        Trello.get("organizations/" + idOrganization, function(organization) {
          $selector.append(
            $("<optgroup/>").attr({label: organization.displayName, id: 'opt_' + idOrganization})
          );
          d3.resolve();
        });
      });

      var d4 = new $.Deferred();

      $.when.apply($, deferreds_3).done(function() {
        Trello.get("members/me/boards", function (boards) {
          $.each(boards, function (ix, board) {
            if (board.closed === false) {
              if (board.idOrganization == null) {
                $selector.find('optgroup[id="opt_main"]').append(
                  $("<option/>").attr("value", board.id).text(board.name)
                );
              } else {
                $selector.find('optgroup[id="opt_' + board.idOrganization + '"]').append(
                  $("<option/>").attr("value", board.id).text(board.name)
                );
              }
            }
          });

          d4.resolve();
        });
      });

      $.when( d4 ).done( function() {
        $selector.find('optgroup:empty').remove();

        $boards.html($selector);

        $('#submit-import').show();
      });
    }

    var onAuthorize = function() {
      updateLoggedIn();
      $("#trello-output").empty();

      Trello.members.get("me", function(member){
        trello_member = member;

        $("#fullName").text(member.fullName);

        loadTrelloData();
      });
    };

    var updateLoggedIn = function() {
      var isLoggedIn = Trello.authorized();
      $("#trello-logged-out").toggle(!isLoggedIn);
      $("#trello-logged-in").toggle(isLoggedIn);
    };

    var logout = function() {
      Trello.deauthorize();
      updateLoggedIn();
      trello_member = null;
      $('#submit-import').hide();
    };

    Trello.authorize({
      interactive:false,
      success: onAuthorize
    });

    $("#connectLink")
      .click(function(){
        Trello.authorize({
          type: "popup",
          name: 'Sensorsix',
          success: onAuthorize
        });
      });

    $("#disconnect").click(logout);

    $('#submit-import').on('click', function(){
      var trello_boards_selector = $('.trello-boards-selector'),
        $button = $(this);

      if (trello_boards_selector.length) {
        $button.hide();
        $('#message-import').show();
        $('#trello-importing-msg').show();

        var board_id = trello_boards_selector.find('option:selected').val(),
          cards = [],
          labels,
          notes;

        Trello.get("boards/" + board_id + '/cards', function(trello_cards){
          var deferreds = [], deferreds_2 = [];

          $.each(trello_cards, function(ix, trello_card) {
            labels = [];
            notes = [];

            $.each(trello_card.labels, function(ix, trello_label) {
              labels.push(trello_label.name);
            });

            var d = new $.Deferred();
            deferreds.push(d);

            deferreds_2 = [];
            var d2 = new $.Deferred();
            deferreds_2.push(d2);

            Trello.get("cards/" + trello_card.id + '/actions', function(trello_card_actions){
              $.each(trello_card_actions, function(ix, trello_card_comment) {
                notes.push(trello_card_comment.data.text);
              });
              d.resolve();
              d2.resolve();
            });

            $.when.apply($, deferreds_2).done(function() {
              cards.push({
                id:     trello_card.id,
                name:   trello_card.name,
                desc:   trello_card.desc,
                due:    trello_card.due,
                labels: labels,
                notes:  notes.join('<br><br>')
              });
            });

          });

          $.when.apply($, deferreds).done(function() {
            $.ajax({
              type: "POST",
              url: '<?php echo url_for('decision\importFromTrello') ?>',
              dataType: 'json',
              data: {
                "board_id"   : board_id,
                "board_name" : trello_boards_selector.find('option:selected').text(),
                "full_name"  : trello_member.fullName,
                "cards"      : JSON.stringify(cards),
                "wizard"     : true
              },
              success:function(data){
                window.location.href = data.dashboard_url;
              },
              error:function(data){
                $button.show();
                $('#trello-importing-msg').hide();
              }
            });
          });
        }, function(){
          $button.show();
          $('#trello-importing-msg').hide();
        });
      }
    });
    <?php endif; ?>

  });
</script>
