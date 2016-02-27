// Reload page if the ajax response have the code "401"
$.ajaxSetup({
  error: function (xObj) {
    if (xObj.status == 401) location.reload(true);
  }
});

$(function () {
  $('#ajax-indicator').ajaxSend(function (event, request, settings) {
    $(this).attr('class', 'label label-info');
    $(this).text('Processing ...');
    $(this).show();
  }).ajaxSuccess(function (event, request, settings) {
    $(this).hide();
  }).ajaxError(function (event, request, settings) {
    if (request.readyState){
      $(this).attr('class', 'label label-important')
      $(this).text('Error');
    }
  });
  $('#ajax-indicator').center("top");

  $('.alert').alert();

  // scroll to named anchor
  $('a[href^=#]').on('click', function () {
    if (!$.isFunction($.fn.scrollTo)) return true;
    var name = $(this).attr('href').replace('#', '');
    var speed = 1000;
    if (name.length == 0) return true;
    var $anchor = $('a[name=' + name + ']');
    if ($anchor.length != 1) return true;
    $(window).scrollTo($anchor.offset().top - $anchor.position().top, speed)
  });

  /* */
  $('.appbar1 a').mouseover(function(){
    $(this).find('i').addClass('animated pulse')
  }).mouseout(function(){
    $(this).find('i').removeClass('animated pulse')
  });
  /* */

  //resize projects dropdown accordingy
  $('#projects-dropdown-link').click(function(){
    $("#projects-dropdown").css('width', $(window).width() - 140);
  });

  $('#roadmaps-dropdown-link').click(function(){
    $("#roadmaps-dropdown").css('width', $(window).width() - 140);
  });
});

// center element on screen $(element).center();
jQuery.fn.center = function (pos) {
  this.css("position","fixed");
  if(pos != "top"){
    this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) + $(window).scrollTop()) + "px");
  }
  this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) + $(window).scrollLeft()) + "px");
  return this;
};