$(function () {
  $('.bootstrap-tooltip').click(function (e) {
    e.stopPropagation(e);
    var tooltip = $('#tooltip-' + $(this).data('tooltip_id')).html();
    if (tooltip.length) {
      $(this).attr('data-original-title', tooltip);
      $('.tooltip').hide();
      $(this).tooltip('show');
    }
  }).tooltip({ trigger: 'manual' });

  $('body').click(function () {
    $('.tooltip').hide();
  });
});