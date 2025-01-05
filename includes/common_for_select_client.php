<?php
if (isset($_COOKIE['idk_session_front_employee'])) {
} elseif (isset($_COOKIE['idk_session_front_employee_skladistar'])) {
	header("Location: orders");
} else {
	header("Location: index");
}
