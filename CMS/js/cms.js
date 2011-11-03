$(document).ready(function(){
	$('h3.options').click(function(){
		$('dl.options',$(this).parent()).slideToggle();
	});
});