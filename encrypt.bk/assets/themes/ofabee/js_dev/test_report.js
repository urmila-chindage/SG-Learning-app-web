/* Answer filtering ul li on click function*/

$(document).on('click','#filter_answer li',function(){

	var q_cnt = 1;

	var className  = this.id;
	var filterText = this.id;

	if(filterText=='all'){
		filterText = 'All questions';
	}
	
	$('#filter_button').contents().first()[0].textContent = filterText.charAt(0).toUpperCase() + filterText.slice(1);

	$('.single-choice-wraper').css('display','none');

	if($('.'+className).length != 0) {

	  $('.'+className).css('display','block');

	  $('.'+className+' '+'.no-in-round').each(function( index ) {

        $(this).text(q_cnt);
        q_cnt++;

      });

    $('.filter_count').text($('.'+className).length);

	}

	else{
		$('.filter_count').text($('.'+className).length);
		if(className=='all'){

			$('.single-choice-wraper').css('display','block');
			$('.single-choice-wraper'+' '+'.no-in-round').each(function( index ) {

		        $(this).text(q_cnt);
		        q_cnt++;
		        $('.filter_count').text($('.single-choice-wraper').length);
	        });
		}
	}

});

/* Answer filtering ul li on click function ends */ 
