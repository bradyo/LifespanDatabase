
$(document).ready(function() {
    $('.sidepane-header').click(function() {
        $(this).next('.sidepane-bottom').toggle();
        
        toggleImg = $(this).find('.toggle-image');
        if (toggleImg.hasClass('collapse')) {
            toggleImg.removeClass('collapse');
            toggleImg.addClass('expand');
        } else {
            toggleImg.removeClass('expand');
            toggleImg.addClass('collapse');
        }
        return false;
    });

    $("#commentForm").hide();
    $("#commentForm").before('<a id="showCommentForm" href="#">Leave a comment</a>');
    $('#showCommentForm').click(function() {
        $("#showCommentForm").hide();
        $("#commentForm").show();
    });
});
