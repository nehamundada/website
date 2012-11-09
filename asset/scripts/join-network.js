//TODO: Need to refactor repeated code

function submitForm() {

	var result;
	if ($('input[name="source"]').val() == "contactUs")
		result = isContactUsFormValid();
	else
		result = isJoinNetworkFormValid();

	if (result == 0) {
		return false;
	} else {

		var form = $('#form');
		form.ajaxForm({

			url : "/sendemail.php",
			type : 'POST',
			dataType : 'json',
			cache : false,
			success : function(response) {
				
				alert(response.result);
				if (response.success) {
					form.resetForm();
				}
			},

		});
		form.submit();
	}
}

function isContactUsFormValid() {
	return (isValidField('contactform-name') & isValidEmail('contactform-email') & isValidField('contactform-message'));
}

function isJoinNetworkFormValid() {
	return (isValidField('joinform-name') & isValidEmail('joinform-email') & isValidField('joinform-expertise'));

}

function isValidField(id) {
	fullName = $('#' + id).val();

	if (fullName == '') {
		$('#' + id).addClass("error");

		return false;
	}
	return true;
}

function isValidEmail(id) {
	email = $('#' + id).val();
	var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;

	if (email == '') {
		$('#' + id).addClass("error");
		return false;
	}
	if (!emailPattern.test(email)) {
		$('#' + id).addClass("error");
		return false;
	}

	return true;
}

function clearFieldError(element) {
	$('#' + element.id).removeClass("error");
}


$(document).ready(function() {

});

function showProfile(element) {
	$('.arrow').css('padding-left', element.offsetLeft);
};
