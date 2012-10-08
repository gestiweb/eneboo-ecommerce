/** @no_class */

$(document).ready(function() {
	$('.ampliar').fancybox({'titlePosition':'inside'});
	$('.ampliarRec').fancybox({
 		'onStart': function() {
			$('#recomendarArticulo .campo input').val('');
			$('#recomendarArticulo textarea').val('');
			$('#recomendarArticulo .aviso').text('');
		}
	});
	
	//$('#stylishSlider img').reflect();
	$('#stylishSlider').data('pos', 1);
	
});

function reloadSecurimage(_webRoot) {
	$('#securimage').attr('src', _webRoot + 'includes/securimage/securimage_show.php?sid=' + Math.random());
}

function sliderBwd() {
	
	var posActual = $('#stylishSlider').data('pos');
	
	if (posActual <= 5)
		$('#sliderBwd').fadeOut();
	
	if (posActual == 1)
		return;
	
	posActual -= 4;
	
	$('#stylishSlider').data('pos', posActual);
	
	$('#stylishSlider').animate({ left : "+=948" }, 500);
	$('#sliderFwd').fadeIn();
}

function sliderFwd(maxPasos) {
	
	maxPasos -= 4;
	
	if (maxPasos <= 0)
		return;
	
	var posActual = $('#stylishSlider').data('pos');
	
	if (posActual >= maxPasos)
		$('#sliderFwd').fadeOut();
	
	if (posActual > maxPasos)
		return;
	
	posActual += 4;
	
	$('#stylishSlider').data('pos', posActual);
		
	$('#stylishSlider').animate({ left : "-=948" }, 500);
	$('#sliderBwd').fadeIn();
}