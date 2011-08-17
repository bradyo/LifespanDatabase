$(document).ready(function() {
	$('#updateSubmit').hide();
	$('#updateSubmit').before('<a id="updateLink">query</a>');
	$('#updateLink').click(function() {
		$('#updateIndicator').show();
		$.getJSON("/agingdb/index.php/citation/getData", { id: $('#citation_pubmed_id').val() }, 
			function(data) {
				$('#updateIndicator').hide();
				for (key in data) {
					if (key == 'author') {
						$('#citation_author').val(data.author);
					}
					if (key == 'title') {
						$('#citation_title').val(data.title);
					}
					if (key == 'source') {
						$('#citation_source').val(data.source);
					}
					if (key == 'year') {
						$('#citation_year').val(data.year);
					}
				}
				return false;
			}
		);
	});
});
