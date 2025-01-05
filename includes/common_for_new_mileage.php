<?php
if (isset($_COOKIE['idk_session_front_employee']) and date('Y-m-d', strtotime(getMileageStartTime($logged_employee_id))) <= date('Y-m-d', strtotime('-1 day')) and getEmployeeStatus() != 1) {
} elseif (isset($_COOKIE['idk_session_front_employee']) and getEmployeeStatus() == 1) {
	header("Location: index");
} elseif (isset($_COOKIE['idk_session_front_employee_skladistar'])) {
	header("Location: orders");
} else {
	header("Location: index");
}
