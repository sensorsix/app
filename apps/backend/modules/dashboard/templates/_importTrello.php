<?php
/**
 * @var $external_ids
 * @var $modal_header
 * @var $url_import
 */

use_javascript('https://api.trello.com/1/client.js?key=' . sfConfig::get('app_trello_api_key') . '&dummy=.js');
?>

<!-- Trello Modal -->
<div id="TrelloModal" class="modal fade modal-wizard" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo $modal_header; ?></h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-6 col-sm-offset-1">
            <div id="trello-logged-out" class="text-center">
              <a id="connectLink" href="javascript:void(0)" class="btn btn-default">Connect To Trello</a>
            </div>

            <div id="trello-logged-in">
              <div id="header">
                <a id="disconnect" href="javascript:void(0)" class="btn btn-warning pull-right">Disconnect from Trello</a>
                <h3>Logged in to as <span id="fullName"></span></h3>
                <hr>
              </div>

              <div id="trello-output"></div>
            </div>

            <div id="trello-errors" class="mr-top-15"></div>
          </div>
          <div class="col-sm-1 col-sm-offset-4">
            <a id="trello-importing-msg" class="steps-navigation wizard-steps-navigation" href="#" onClick="return false;"><img src="<?php echo image_path('ajax-loader.gif'); ?>"></a>
            <a id="submit-import" class="steps-navigation step-next wizard-steps-navigation" href="#" onClick="return false;"><i class="fa fa-arrow-circle-o-right"></i></a>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
<!-- END Trello Modal -->

<script>
  $(function(){
    // Trello JS
    var $trelloModal        = $('#TrelloModal'),
      $trelloLoaded       = false,
      external_ids        = <?php echo json_encode($external_ids->getRawValue()); ?>,
      organizations       = [],
      $trelloImportingMsg = $('#trello-importing-msg'),
      $trelloErrors       = $('#trello-errors'),
      trello_member, $trello_boards_selector, $button, $trello_selector_option;

    $trelloModal.on('show.bs.modal', function(){
      if (!$trelloLoaded && Trello.authorized()){
        loadTrelloData();
        $trelloLoaded = true;
      }
    });

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
              $trello_selector_option = $("<option/>").attr("value", board.id).text(board.name);
              if ($.inArray(board.id, external_ids) > -1) {
                $trello_selector_option.attr('disabled', true);
              }

              if (board.idOrganization == null) {
                $selector.find('optgroup[id="opt_main"]').append(
                  $trello_selector_option
                );
              } else {
                $selector.find('optgroup[id="opt_' + board.idOrganization + '"]').append(
                  $trello_selector_option
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
      });
    }

    var onAuthorize = function(load_data) {
      updateLoggedIn();
      $('#submit-import').show();
      $("#trello-output").empty();

      Trello.members.get("me", function(member){
        trello_member = member;

        $("#fullName").text(member.fullName);

        if (typeof load_data !== 'undefined' && load_data === true){
          loadTrelloData();
        }
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
      $trelloLoaded = false;
      $('#submit-import').hide();
    };

    Trello.authorize({
      interactive: false,
      success    : onAuthorize,
      error      : function () {
        console.log('error');
        logout();
      }
    });

    $("#connectLink")
      .click(function(){
        Trello.authorize({
          type: "popup",
          name: 'Sensorsix',
          success: function(){
            onAuthorize(true);
          }
        });
      });

    $("#disconnect").click(logout);

    $('#submit-import').on('click', function(){
      $trello_boards_selector = $('.trello-boards-selector');
      $button = $(this);

      $trelloErrors.text('').hide();

      if ($trello_boards_selector.length) {
        $button.hide();
        $trelloImportingMsg.show();

        var board_id = $trello_boards_selector.find('option:selected').val(),
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
              d2.resolve();
            });

            $.when.apply($, deferreds_2).done(function() {
              cards.push({
                id:     trello_card.id,
                name:   trello_card.name,
                desc:   trello_card.desc,
                due:    trello_card.due,
                labels: labels,
                notes:  notes.join('<br><br>'),
                pos:    ix
              });
              d.resolve();
            });

          });

          $.when.apply($, deferreds).done(function() {
            cards.sort(function(a, b){return a.pos > b.pos});

            $.ajax({
              type: "POST",
              url: '<?php echo $url_import ?>',
              dataType: 'json',
              data: {
                "board_id"   : board_id,
                "board_name" : $trello_boards_selector.find('option:selected').text(),
                "full_name"  : trello_member.fullName,
                "cards"      : JSON.stringify(cards)
              },
              success:function(data){
                if (data.status === 'success') {
                  window.location.reload();
                }else{
                  $button.show();
                  $trelloImportingMsg.hide();
                  $trelloErrors.text(data.message).show();
                }
              },
              error:function(data){
                $button.show();
                $trelloImportingMsg.hide();
              }
            });
          });
        }, function(){
          $button.show();
          $trelloImportingMsg.hide();
        });
      }
    });

    $trelloModal.find('.modal-content').css({'margin-top': (($(window).height() - 620) / 2 < 0)? 0 : ($(window).height() - 620) / 2});
    $(window).resize(function(){
      $trelloModal.find('.modal-content').css({'margin-top': (($(window).height() - 620) / 2 < 0)? 0 : ($(window).height() - 620) / 2});
    });
  })
</script>