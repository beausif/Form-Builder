(function($) {
	$(function() {
		setEvents();
	});
	
	function setEvents(){
		$('#fb-form').on('submit', function(e) {
			var event = e.originalEvent;
			event.preventDefault ? e.preventDefault() : event.returnValue = false;
			$(this).ajaxSubmit({
				url:		   'php/main.php',
				type:		   'POST',
				dataType:	   'JSON',
				beforeSubmit:  showRequest,
				success:       showResponse,
				error:		   showError
			});
			return false;
		});

		$('input, select').off('keydown');
		
		$('input, select').on('keydown', function(e) {
			if (e.keyCode == 13) {
				return false;
			}
		});
	}

	function showRequest(formData, jqForm, options){
		resetErrors();
		var noError = true;
		$('input[type=submit]').prop('disabled', true);

		

		if(noError){
			createMessage('success', 'Please wait form is being submitted');
		} else {
			$('input[type=submit]').prop('disabled', false);
		}
		return noError;
	}

	function showResponse(response, status, xhr, $form){
		if(response.success){
			$('#fb-form')[0].reset();
			createMessage('success', response.text);
		} else {
			createMessage('error', response.text);
		}
		$('input[type=submit]').prop('disabled', false);
	}

	function showError(jqXHR, textStatus, errorThrown) {
		$('input[type=submit]').prop('disabled', false);
		createMessage('error', 'Problem submitting the form : ' +  jqXHR + ' | ' + textStatus + ' | ' + errorThrown);
	}

	function resetErrors(){
		$('.errorLabel').text('');
		$('.errorBorder').removeClass('errorBorder');
	}

	function checkInput(input, error){
		var errors = false;
		$(input).each(function(index, element){
			if($.trim($(element).val()) == ''){
				errors = true;
				$(element).addClass('errorBorder');
				$(element).parent().next('.errorLabel').text(error);
			}
		});
		return errors;
	}

	function setError(response){
		if(typeof response == 'string'){
			createMessage('error', response);
		} else {
			createMessage('error', 'Unknown Error Occured')
		}
	}

	function createMessage(type, message){
		var alert_type = null;
		var alert_msg = null;
		var html = null;
		if(type == 'success'){
			alert_type = 'success';
			alert_msg = 'Success!';
		} else {
			alert_type = 'danger';
			alert_msg = 'Error!';
		}

		html = ' \
		<div class="col-sm-6 col-sm-offset-3"> \
			<div class="alert alert-' + alert_type + ' fade in"> \
				<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> \
				<strong>' + alert_msg + ' </strong><p class="inline">' + message + '<p> \
			</div> \
		</div>';


		$('#message-div').html(html);
		window.scrollTo(0,0);
	}

})( jQuery );