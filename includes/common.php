<?php
if (isset($_COOKIE['idk_session_front'])) {
} elseif (isset($_COOKIE['idk_session_front_employee'])) {

	if (getEmployeeStatus() == 1) {
		if (isset($_COOKIE['idk_session_front_client'])) {
		} else {
			header("Location: selectClient");
		}
	} else {
		if (isset($_COOKIE['idk_session_front_client'])) {
		} else {
			header("Location: selectClient");
		}
	}
} elseif (isset($_COOKIE['idk_session_front_employee_skladistar'])) {
	header("Location: orders");
} else {
	header("Location: login");
}
