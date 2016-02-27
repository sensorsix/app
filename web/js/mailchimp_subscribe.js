$(function () {
  var $email = $('#subscriber-email');

  $email.popover({
    trigger: 'manual',
    placement: 'top'
  });

  $('#subscribe-button').on('click', function () {
    $email.attr('readonly', true);
    $.post('/subscribe', {email: $email.val()}, function (response) {
      $email.attr('readonly', false);
      if (response.status == 'ok') {
        $email.attr('data-content', 'Thanks');
        $email.val('');
      } else {
        $email.attr('data-content', 'Invalid email address');
      }
      $email.popover('show');
      setTimeout(function () { $email.popover('hide'); }, 2000);
    });
  });
});