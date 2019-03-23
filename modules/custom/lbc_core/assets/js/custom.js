(function ($, Drupal) {
  'use strict';

  console.log("dans custom")

  var listMode = localStorage.getItem("mode");
  if(listMode === "list"){
    $('.wrapper').addClass('list-mode');
  }

  $('.show-list').click(function(){
  	$('.wrapper').addClass('list-mode');
    localStorage.setItem("mode", "list");
  });

  $('.hide-list').click(function(){
  	$('.wrapper').removeClass('list-mode');
    localStorage.setItem("mode", "tiles");
  });

})(jQuery, Drupal);
