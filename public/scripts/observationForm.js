var iGene;
var iCompound;
var iEnvironment;

$(document).ready(function() {
    iGene = parseInt($('#iGene').val());
    iCompound = parseInt($('#iCompound').val());
    iEnvironment = parseInt($('#iEnvironment').val());

    $('.addGene').click(function() {
        // grab the tr dom node
        var trElement = $('#geneTemplate table tbody tr').clone(true);

        // update the element id and names
        var inputElements = trElement.find('input, select');
        inputElements.each(function() {
            var newId = 'genes-' + iGene + '-' + $(this).attr('id');
            $(this).attr('id', newId);
            
            var newName = 'genes[' + iGene + ']'
                + '[' + $(this).attr('name') + ']';
            $(this).attr('name', newName);
        });

        $('#geneTable').append(trElement);
        $('#geneTable tr').fadeIn(500);
        iGene++;

        return false;
    });

    $('.addCompound').click(function() {
        // grab the tr dom node
        var trElement = $('#compoundTemplate table tbody tr').clone(true);

        // update the element id and names
        var inputElements = trElement.find('input, select');
        inputElements.each(function() {
            var newId = 'compounds-' + iCompound
                + '-' + $(this).attr('id');
            $(this).attr('id', newId);

            var newName = 'compounds[' + iCompound + ']'
                + '[' + $(this).attr('name') + ']';
            $(this).attr('name', newName);
        });

        $('#compoundTable').append(trElement);
        $('#compoundTable tr').fadeIn(500);
        iCompound++;

        return false;
    });


    $('.addEnvironment').click(function() {
        // grab the tr dom node
        var trElement = $('#environmentTemplate table tbody tr').clone(true);

        // update the element id and names
        var inputElements = trElement.find('input, select, textarea');
        inputElements.each(function() {
            var newId = 'environments-' + iEnvironment
                + '-' + $(this).attr('id');
            $(this).attr('id', newId);

            var newName = 'environments[' + iEnvironment + ']'
                + '[' + $(this).attr('name') + ']';
            $(this).attr('name', newName);
        });

        $('#environmentTable').append(trElement);
        $('#environmentTable tr').fadeIn(500);
        iEnvironment++;

        return false;
    });


    $('.getGeneSymbol').click(function() {
        var ncbiGeneIdElement = $(this).prev('input');

        var elementId = ncbiGeneIdElement.attr('id');
        var matches = elementId.match(/genes-(\d+)-ncbiGeneId/);
        var i = null;
        if (matches) {
            i = matches[1];
        }

        var targetId = 'genes-' + i + '-symbol';
        var ncbiGeneId = ncbiGeneIdElement.val();
        var url = '/service/get-gene-symbol?'
            + 'ncbiGeneId=' + ncbiGeneId + '&'
            + 'targetId=' + targetId;
        $.get(url, function(data) {
            var dataObj = JSON.parse(data);
            $('#'+dataObj.targetId).val(dataObj.symbol);
        });
        return false;
    });

    $('.getCitationData').click(function() {
		$('#updateIndicator').show();
        var pubmedId = $('#citationPubmedId').val();
		$.getJSON("/service/get-citation-data", { pubmedId: pubmedId }, function(data) {
            for (key in data) {
                if (key == 'year') {
                    $('#citationYear').val(data.year);
                }
                if (key == 'author') {
                    $('#citationAuthor').val(data.author);
                }
                if (key == 'title') {
                    $('#citationTitle').val(data.title);
                }
                if (key == 'source') {
                    $('#citationSource').val(data.source);
                }
            }
            return false;
        });
        return false;
	});

    $("fieldset legend").click(function(){
        divHandle = $(this).next('div.content');
        if (divHandle.css('display') == 'none') {
            divHandle.show();
        } else {
            divHandle.hide();
        }
    });

    $("#lifespanBase, #lifespan").change(function() {
        var lifespanVal = $("#lifespan").val();
        var baseVal = $("#lifespanBase").val();
        if (lifespanVal >= 0 && baseVal > 0) {
            var percentChange = (lifespanVal - baseVal) / baseVal * 100;
            percentChange = Math.round(percentChange * 100) / 100;
            $("#lifespanChange").val(percentChange);

            if (percentChange > 0) {
                $("#lifespanEffect-increased").attr("checked", "checked");
            } else if (percentChange < 0) {
                $("#lifespanEffect-decreased").attr("checked", "checked");
            }
        }

    });

});
