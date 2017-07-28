var cardfields = { number: '#cardnumber', status: '#credit-status', image: '.credit-img', cardtype: '#cctype' };
var expirefields = {date: '#expire', status: '#expire-status'};
var cvvfields = {number: '#cvv'};


$(cardfields.number).payment('formatCardNumber'); $(cvvfields.number).payment('formatCardCVC'); $(expirefields.date).payment('formatCardExpiry');

$(function() {
	$(".page").on("change", "#orderhead-form .email", function(e) {
		var validemail = validateemail($(this).val());
		if (validemail) {
			$(this).closest('tr').removeClass("has-error").addClass("has-success");
		} else {
			$(this).closest('tr').removeClass("has-success").addClass("has-error");
		}
	});

	$(".page").on("change", "#orderhead-form .shipto-select", function(e) {
		e.preventDefault();
		var custid = $(this).data('custid');
		var shiptoid = $(this).val();
		var jsonurl = config.urls.json.getshipto+"?custID="+urlencode(custid)+"&shipID="+urlencode(shiptoid);
		$.get(jsonurl, function(json) {
			var shipto = json.response.shipto;
			$('.shipto-select').val(shiptoid); $('.shipto-name').val(shipto.name); $('.shipto-address').val(shipto.addr1);
			$('.shipto-address2').val(shipto.addr2);
			$('.shipto-city').val(shipto.ccity); $('.shipto-state').val(shipto.cst); $('.shipto-zip').val(shipto.czip);
		});
	});

	$(".page").on("submit", "#orderhead-form", function(e) {
		e.preventDefault();
		var formid = '#'+$(this).attr('id');
		var ordn = $(this).data('ordn');
		if ($(this).formiscomplete('tr')) {
			$(formid).postform({formdata: false, jsoncallback: false}, function() { //{formdata: data/false, jsoncallback: true/false}
				$.notify({
					icon: "glyphicon glyphicon-floppy-disk",
					message: "Your orderhead changes have been submitted",
				},{
					type: "info",
					onClose: function() {
						getorderheadresults(ordn, formid);
					}
				});
			});
		}
	});
});

$(cardfields.number).validateCreditCard(function(result) {
	if (result.card_type !== null && $(cardfields.number).val().length > 3) {
		if ($(cardfields.number).val().length >= 3 && result.length_valid === false) {
			$(cardfields.number).closest('tr').removeClass("has-success").addClass("has-error");
			$(cardfields.status).removeClass("has-success").addClass("has-error");
			$(cardfields.status).text('Credit Card Number must be 16 digits long');
			$(cardfields.image).html('');
		}  else if ($(cardfields.number).val().length >= 10 && result.luhn_valid === false) {
			$(cardfields.number).closest('tr').removeClass("has-success").addClass("has-error");
			$(cardfields.status).text('Please check your Credit Card Number');
			$(cardfields.image).html('');
		}  else if ($(cardfields.number).val().length === 0) {
			$(cardfields.status).removeClass("has-success").addClass("has-error");
			$(cardfields.status).text('Credit Card Number must be 16 digits long');
			$(cardfields.image).html('');
		}  else {
			$(cardfields.number).closest('tr').removeClass("has-error").addClass("has-success");
			$(cardfields.status).removeClass("has-error").addClass("has-success");
			$(cardfields.status).text('Card recognized as ' + result.card_type.display);
			$(cardfields.cardtype).val(result.card_type.code);
			$(cardfields.image).html('<img src="'+config.paths.assets.images+'credit/'+result.card_type.code+'-logo.png" height="33">');
		}
	}
	else if (result.card_type === null && $(cardfields.number).val().length > 6 ) {
		$(cardfields.status).removeClass("has-success").addClass("has-error");
		$(cardfields.status).text('Card Type Not recognized, please try again');
		$(cardfields.image).html('');
	}
}, { accept: ['mastercard', 'visa' , 'discover', 'amex'] } );

$(expirefields.date).change(function() {
	var datearray = $(expirefields.date).val().split(' / ');
	var month = datearray[0]; var year = datearray[1];
	var valid = $.payment.validateCardExpiry(month, year);
	if (!valid) {
		$(expirefields.date).closest('tr').removeClass('has-success').addClass('has-error');
		$(expirefields.status).removeClass("has-success").addClass("has-error");
		$(expirefields.status).text('Expiration date is invalid');
	} else {
		$(expirefields.status).removeClass("has-error").addClass("has-success");
		$(expirefields.status).text('');
		$(expirefields.date).closest('tr').removeClass("has-error").addClass("has-success");
	}
});

	$(".page").on("click", ".load-sales-docs, .load-sales-tracking", function(e) {
		e.preventDefault();
		var loadinto = $(this).data('loadinto');
		var focuson = $(this).data('focus');
		var geturl = $(this).attr('href');
		var clickon = $(this).data('click');
		$.get(geturl, function() {
			generateurl(function(url) {
				console.log(url);
				loadin(url, loadinto, function() {
					$(clickon).click();
					if (focuson.length > 0) { $('html, body').animate({scrollTop: $(focuson).offset().top - 60}, 1000); }

				} );
			});
		});
	});





/* =============================================================
	FUNCTIONS
============================================================ */
	function validateemail(email) {
		var emailregex = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/;
		return emailregex.test(email);
	}

	function showphone(intlYN) {
		if (intlYN == 'Y') {
			$('.international').removeClass('hidden');
			$('.domestic').addClass('hidden');
		} else {
			$('.domestic').removeClass('hidden');
			$('.international').addClass('hidden');
		}
	}

	window.addDashes = function addDashes(f) {
		var r = /(\D+)/g; var npa = ''; var nxx = ''; var last4 = '';
		f.value = f.value.replace(r, '');
		npa = f.value.substr(0, 3);
		nxx = f.value.substr(3, 3);
		last4 = f.value.substr(6, 4);
		f.value = npa + '-' + nxx + '-' + last4;
	};

	function showcredit(showcreditval) {
		if (showcreditval === 'cc') {
			$('#credit').removeClass('hidden');
		} else {
			$('#credit').addClass('hidden');
		}
	}



	function getorderheadresults(ordn, form) {
		console.log(config.urls.json.getorderhead+"?ordn="+ordn);
		$.getJSON(config.urls.json.getorderhead+"?ordn="+ordn, function( json ) {
			if (json.response.order.error === 'Y') {
				createalertpanel(form + ' .response', json.response.order.errormsg, "<i span='glyphicon glyphicon-floppy-remove'> </i> Error! ", "danger");
				$('html, body').animate({scrollTop: $(form + ' .response').offset().top - 120}, 1000);
			} else {
				$.notify({
					icon: "glyphicon glyphicon-floppy-saved",
					message: "Your orderhead changes have been saved" ,
				},{
					type: "success",
					onShow: function() {
						$('#salesdetail-link').click();
					}
				});
			}

		});
	}
