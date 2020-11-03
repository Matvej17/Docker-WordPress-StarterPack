/* eslint-disable no-undef */
function animateMenuIcon() {
  const btn = $('.animate-menu-icon');

  btn.on('click', function() {
    $(this).toggleClass('open');
  });
}

$(document).ready( function() {
  animateMenuIcon();
});