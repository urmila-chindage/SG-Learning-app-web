//side nav bar toggle
$(document).ready(function () {
    $('.menu-icon').on('click', function () {
        $('.left').toggleClass('active');
    });
});
// side nav bar hide on outside click
$(document).ready(function () {
    $('.chat-container').on('click', function () {
        $('.left').removeClass('active');
    });
});
// side nav bar hide on chat user selection
$(document).ready(function () {
    $('.person').on('click', function () {
        $('.left').removeClass('active');
    });
});