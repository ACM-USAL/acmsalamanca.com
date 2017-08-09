$(document).ready(function() {
    $("#feed_image").change(function() {
        var src = $(this).val();

        $("#feed_im_prew").attr('src', "../data/thumbs/thumbsm." + src );
    });
    $("#feed_icon").change(function() {
        var src = $(this).val();

        $("#feed_ico_prew").attr('src',  src );
    });

});
