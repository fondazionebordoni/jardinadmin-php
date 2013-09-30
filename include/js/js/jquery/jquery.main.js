$(document).ready(function(){
	
    $('#menu a').click(function(){	
	   	var page = $(this).attr('href');	
		$('#content').load(page+' #content').slideDown();		
		return false;
	});

});