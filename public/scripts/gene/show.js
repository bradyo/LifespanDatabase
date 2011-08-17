// baseUrl var is set by front controller
if (!baseUrl) {
    var baseUrl = '';
}

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

	$('.collapsable h2.region').click(function() {
        if ($(this).hasClass('collapsed')) {
            $(this).next('div').show();
            $(this).removeClass('collapsed');
            $(this).addClass('expanded');
        } else {
            $(this).next('div').hide();
            $(this).removeClass('expanded');
            $(this).addClass('collapsed');
        }
		return false;
	});

    $('#gosHeader').click(function() {
        var contentDiv = $('#gosHeader').next('.content');
        if (contentDiv.html() == '') {
            contentDiv.html('<span class="loading-large">Loading...</span>');

            var ncbiGeneId = $('#ncbiGeneId').val();
            contentDiv.load(baseUrl+'/genes/get-go-table', "id="+ncbiGeneId);
        }
    });

    $('#ppodHomologsHeader').click(function() {
        var contentDiv = $('#ppodHomologsHeader').next('.content');
        if (contentDiv.html() == '') {
            contentDiv.html('<span class="loading-large">Loading...</span>');
            
            var ncbiGeneId = $('#ncbiGeneId').val();
            contentDiv.load(baseUrl+'/genes/get-homolog-table', "id="+ncbiGeneId);
        }
    });

    $('#interactionsHeader').click(function() {
        var contentDiv = $('#interactionsHeader').next('.content');
        if (contentDiv.html() == '') {
            contentDiv.html('Not available.');
        }
    });

    $('#substratesHeader').click(function() {
        var contentDiv = $('#substratesHeader').next('.content');
        if (contentDiv.html() == '') {
            contentDiv.html('Not available.');
        }
    });

    $("#commentForm").hide();
    $("#commentForm").before('<a id="showCommentForm" href="#">Leave a comment</a>');
    $('#showCommentForm').click(function() {
        $("#showCommentForm").hide();
        $("#commentForm").show();
    });
});
