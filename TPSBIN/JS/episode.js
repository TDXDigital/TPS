$("#playlistNum").on('change paste input', function(){
 
    if($(this).val().length == 4)
    {
    	var playlistNum = $(this).val();
    	$.ajax({
            url: "./searchSong/" + playlistNum,
            type: 'POST',
            }).done(function(data) {
            	var albumInfo = JSON.parse(data);
            	$("#artist").val(albumInfo.artist);
            	$("#album").val(albumInfo.album);
            	$('#DDLNormal option[value="'+albumInfo.governmentCategory +'"]')
            	if(albumInfo.CanCon == 1)
            		$("#ccin").prop('checked', true);
            	else
            		$("#ccin").prop('checked', false);
            }).fail(function(data){
                alert(JSON.stringfy(data));
            });
    }

});