<?php
if (isset($_COOKIE['idk_session_front_employee']) or isset($_COOKIE['idk_session_front_employee_skladistar'])) {
} else {
	header("Location: login");
}