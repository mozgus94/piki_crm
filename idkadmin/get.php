<?php
include("includes/functions.php");
include("includes/common.php");

$case = "";

if (isset($_REQUEST["case"])) {
	$case = $_REQUEST["case"];

	switch ($case) {



			// Get number of unread other notifications
		case "getNumberOfOtherNotifications":

			$query = $db->prepare("
				SELECT COUNT(notification_id) AS notification_total
				FROM idk_notifications
				WHERE notification_employeeid = :notification_employeeid AND notification_status = :notification_status AND notification_datetime <= NOW() AND notification_type = :notification_type");

			$query->execute(array(
				':notification_status' => 1,
				':notification_employeeid' => $logged_employee_id,
				':notification_type' => 4
			));

			$row = $query->fetch();

			if ($row['notification_total'] > 0) {
				echo '<span>' . $row['notification_total'] . '</span>';
			} else {
				echo '';
			}

			break;



			// Get last 20 other notifications
		case "getOtherNotifications_20":

			$query = $db->prepare("
				SELECT notification_id, notification_title, notification_icon, notification_link, notification_status, notification_datetime
				FROM idk_notifications
				WHERE notification_employeeid = :notification_employeeid AND notification_datetime <= NOW() AND notification_type = :notification_type
				ORDER BY notification_datetime DESC
				LIMIT 20");

			$query->execute(array(
				':notification_employeeid' => $logged_employee_id,
				':notification_type' => 4
			));

			while ($row = $query->fetch()) {

				$notification_id = $row['notification_id'];
				$notification_title = $row['notification_title'];
				$notification_icon = $row['notification_icon'];
				$notification_link = $row['notification_link'];
				$notification_datetime = $row['notification_datetime'];
				if ($row['notification_status'] == 1) {
					$notification_status = 'class="idk_highlight_bg"';
				} else {
					$notification_status = '';
				}

				echo '<li ' . $notification_status . '><a href="' . $notification_link . '&nid=' . $notification_id . '"><i class="fa fa-' . $notification_icon . '"></i></i> ' . $notification_title . '<br><small><em>' . date('d.m.Y. H:i', strtotime($notification_datetime)) . '</em></small></a></li>';
			}

			break;



			// Get number of unread orders notifications
		case "getNumberOfOrdersNotifications":

			$query = $db->prepare("
				SELECT COUNT(notification_id) AS notification_total
				FROM idk_notifications
				WHERE notification_employeeid = :notification_employeeid AND notification_status = :notification_status AND notification_datetime <= NOW() AND notification_type = :notification_type");

			$query->execute(array(
				':notification_status' => 1,
				':notification_employeeid' => $logged_employee_id,
				':notification_type' => 3
			));

			$row = $query->fetch();

			if ($row['notification_total'] > 0) {
				echo '<span>' . $row['notification_total'] . '</span>';
			} else {
				echo '';
			}

			break;



			// Get last 20 orders notifications
		case "getOrdersNotifications_20":

			$query = $db->prepare("
				SELECT notification_id, notification_title, notification_icon, notification_link, notification_status, notification_datetime
				FROM idk_notifications
				WHERE notification_employeeid = :notification_employeeid AND notification_datetime <= NOW() AND notification_type = :notification_type
				ORDER BY notification_datetime DESC
				LIMIT 20");

			$query->execute(array(
				':notification_employeeid' => $logged_employee_id,
				':notification_type' => 3
			));

			while ($row = $query->fetch()) {

				$notification_id = $row['notification_id'];
				$notification_title = $row['notification_title'];
				$notification_icon = $row['notification_icon'];
				$notification_link = $row['notification_link'];
				$notification_datetime = $row['notification_datetime'];
				if ($row['notification_status'] == 1) {
					$notification_status = 'class="idk_highlight_bg"';
				} else {
					$notification_status = '';
				}

				echo '<li ' . $notification_status . '><a href="' . $notification_link . '&nid=' . $notification_id . '"><i class="fa fa-' . $notification_icon . '"></i></i> ' . $notification_title . '<br><small><em>' . date('d.m.Y. H:i', strtotime($notification_datetime)) . '</em></small></a></li>';
			}

			break;



			// Get number of unread messages
		case "getNumberOfMessages":

			$query = $db->prepare("
							SELECT COUNT(mu_id) AS messages_total
							FROM idk_messages_users
							WHERE mu_employeeid = :mu_employeeid AND mu_status = :mu_status");

			$query->execute(array(
				':mu_status' => 0,
				':mu_employeeid' => $logged_employee_id
			));

			$row = $query->fetch();

			if ($row['messages_total'] > 0) {
				echo '<span>' . $row['messages_total'] . '</span>';
			} else {
				echo '';
			}

			break;



			// Get last messages
		case "getMessages_10":

			$inbox_query = $db->prepare("
								SELECT message_id, message_subject, message_datetime, employee_first_name, employee_last_name, employee_image, mu_status
								FROM idk_messages
								INNER JOIN idk_messages_users ON idk_messages.message_id = idk_messages_users.mu_messageid
								INNER JOIN idk_employee ON idk_messages.message_sentid = idk_employee.employee_id
								WHERE mu_employeeid = :mu_employeeid AND mu_status != :mu_status
								ORDER BY message_id DESC");

			$inbox_query->execute(array(
				':mu_employeeid' => $logged_employee_id,
				':mu_status' => 2
			));

			while ($inbox_row = $inbox_query->fetch()) {

				$message_id = $inbox_row['message_id'];
				$employee_fullname = $inbox_row['employee_first_name'] . ' ' . $inbox_row['employee_last_name'];
				$employee_image = $inbox_row['employee_image'];
				$message_subject = $inbox_row['message_subject'];
				$message_datetime = $inbox_row['message_datetime'];
				$message_datetime_f = date('d.m.Y H:i', strtotime($inbox_row['message_datetime']));
				$mu_status = $inbox_row['mu_status'];

				if ($mu_status == 0) {
					$mess_status_style = 'class="idk_highlight_bg"';
				} else {
					$mess_status_style = '';
				}

				echo '<li ' . $mess_status_style . '>
				<a href="' . getSiteUrlr() . 'idkadmin/messages?page=open&id=' . $message_id . '">
					<div class="pull-left"><img src="./files/employees/images/' . $employee_image . '" class="img-circle" alt="User Image"></div>
					<h4>' . $employee_fullname . '</h4>
					<p>' . $message_subject . '</p>
				</a>
			</li>';
			}

			break;
	}
}
