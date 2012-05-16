$(document).ready(function() {

    var relativeUrl = '/agingdb';

    $.ajaxSetup ({
		cache: false
	});

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
            contentDiv.load(relativeUrl+'/genes/get-go-table', "id="+ncbiGeneId);
        }
    });

    $('#ppodHomologsHeader').click(function() {
        var contentDiv = $('#ppodHomologsHeader').next('.content');
        if (contentDiv.html() == '') {
            contentDiv.html('<span class="loading-large">Loading...</span>');
            
            var ncbiGeneId = $('#ncbiGeneId').val();
            contentDiv.load(relativeUrl+'/genes/get-homolog-table', "id="+ncbiGeneId);
        }
    });
});
