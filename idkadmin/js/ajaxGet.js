//callNumberOfOrdersNotifications Start
function callNumberOfOrdersNotifications() {
	$.ajax({
		url: 'get.php?case=getNumberOfOrdersNotifications',
		success: function (result) {
			$('.getNumberOfOrdersNotifications').html(result);
		}
	});
}

callNumberOfOrdersNotifications();
setInterval(callNumberOfOrdersNotifications, 2 * 1000);
//callNumberOfOrdersNotifications End

//callOrdersNotifications_20 Start
function callOrdersNotifications_20() {
	$.ajax({
		url: 'get.php?case=getOrdersNotifications_20',
		success: function (result) {
			$('.getOrdersNotifications_20').html(result);
		}
	});
}

callOrdersNotifications_20();
setInterval(callOrdersNotifications_20, 2 * 1000);
//callOrdersNotifications_20 End

function orders_notifications_mark_read() {
	callNumberOfOrdersNotifications();
	callOrdersNotifications_20();
}

//callNumberOfOtherNotifications Start
function callNumberOfOtherNotifications() {
	$.ajax({
		url: 'get.php?case=getNumberOfOtherNotifications',
		success: function (result) {
			$('.getNumberOfOtherNotifications').html(result);
		}
	});
}

callNumberOfOtherNotifications();
setInterval(callNumberOfOtherNotifications, 2 * 1000);
//callNumberOfOtherNotifications End

//callOtherNotifications_20 Start
function callOtherNotifications_20() {
	$.ajax({
		url: 'get.php?case=getOtherNotifications_20',
		success: function (result) {
			$('.getOtherNotifications_20').html(result);
		}
	});
}

callOtherNotifications_20();
setInterval(callOtherNotifications_20, 2 * 1000);
//callOtherNotifications_20 End

function other_notifications_mark_read() {
	callNumberOfOtherNotifications();
	callOtherNotifications_20();
}

//callNumberOfMessages Start
function callNumberOfMessages() {
	$.ajax({
		url: 'get.php?case=getNumberOfMessages',
		success: function (result) {
			$('.getNumberOfMessages').html(result);
		}
	});
}

callNumberOfMessages();
setInterval(callNumberOfMessages, 2 * 1000);
//callNumberOfMessages End

//callMessages_10 Start
function callMessages_10() {
	$.ajax({
		url: 'get.php?case=getMessages_10',
		success: function (result) {
			$('.getMessages_10').html(result);
		}
	});
}

callMessages_10();
setInterval(callMessages_10, 2 * 1000);
// callMessages_10 End
