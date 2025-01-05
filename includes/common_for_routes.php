<?php
if (isset($_COOKIE['idk_session_front_employee'])) {
} else {
	header("Location: login");
}
