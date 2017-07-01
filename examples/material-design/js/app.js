var base = '/examples/material-design/';
var pages = [
    'index.php',
    'login.php',
    'register.php',
    'home.php',
    'manage-devices.php',
    'change.php',
    'reset.php',
    'profile.php'
];

$.each(pages, function(i, page) {
    $.router.add(base + page, function() {
        $.get(base + page, function(html) {
            var htmlObj = $(html);

            $('title').replaceWith(htmlObj.filter('title'));
            $('.container').replaceWith(htmlObj.filter('.container'));

            window.history.replaceState({}, '', htmlObj.filter('meta[name=page-path]').attr('content'));
        });
    });
});

$(document).ready(function() {
    $(document).on('click', 'a[data-ajax]', function(e) {
        e.preventDefault();
        $.router.go($(this).attr('href'));
    });
});
