<?php

//Setting the default timezone
date_default_timezone_set('Europe/Sarajevo');


//Error log enabled
ini_set('display_errors', 1);
ini_set('error_log', 'error_log');
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


//PHPMailer
require 'mail/Exception.php';
require 'mail/PHPMailer.php';
require 'mail/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


//Connect to db
ob_start();
include("includes/connect.php");


//Language
include("lang/bs.php");


//User LogIn Session
if (isset($_COOKIE['idk_session'])) {
	global $db;
	$employee_key = $_COOKIE['idk_session'];

	$query = $db->prepare("
		SELECT employee_id
		FROM idk_employee
		WHERE employee_key = :employee_key");

	$query->execute(array(
		':employee_key' => $employee_key
	));

	$number_of_rows = $query->rowCount();

	if ($number_of_rows !== 0) {
		$employee = $query->fetch();

		global $logged_employee_id;
		$logged_employee_id = $employee['employee_id'];
	} else {
		$client_key = $_COOKIE['idk_session'];

		$query = $db->prepare("
			SELECT client_id
			FROM idk_client
			WHERE client_key = :client_key");

		$query->execute(array(
			':client_key' => $client_key
		));

		$client = $query->fetch();

		global $logged_client_id;
		$logged_client_id = $client['client_id'];
	}
} else {
	$logged_employee_id = 0;
	$logged_client_id = 0;
}

function callAPI($method, $url, $data){
	$curl = curl_init();
	switch ($method){
	   case "POST":
		  curl_setopt($curl, CURLOPT_POST, 1);
		  if ($data)
			 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		  break;
	   case "PUT":
		  curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
		  if ($data)
			 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
		  break;
	   default:
		  if ($data)
			 $url = sprintf("%s?%s", $url, http_build_query($data));
	}
	// OPTIONS:
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	   'Content-Type: application/json'
	));
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
	curl_setopt($ch, CURLOPT_TIMEOUT, 0);
	// EXECUTE:
	$result = curl_exec($curl);
	if(!$result){die("Connection Failure");}
	curl_close($curl);
	return $result;
 }


//Site URL
function getSiteUrl()
{
	global $db;

	$setting_query = $db->prepare("
		SELECT setting_value
		FROM idk_setting
		WHERE setting_name = :setting_name");

	$setting_query->execute(array(
		':setting_name' => 'crm_url_b2b'
	));

	$setting = $setting_query->fetch();

	$site_url = $setting['setting_value'];

	$site_url_crm = $site_url;

	echo $site_url_crm;
}


//Site url return
function getSiteUrlr()
{
	global $db;

	$setting_query = $db->prepare("
		SELECT setting_value
		FROM idk_setting
		WHERE setting_name = :setting_name");

	$setting_query->execute(array(
		':setting_name' => 'crm_url_b2b'
	));

	$setting = $setting_query->fetch();

	$site_url = $setting['setting_value'];

	$site_url_crm = $site_url;

	return $site_url_crm;
}


//Get Name of CRM
function getCrmNamer()
{
	global $db;

	$setting_query = $db->prepare("
		SELECT setting_value
		FROM idk_setting
		WHERE setting_name = :setting_name");

	$setting_query->execute(array(
		':setting_name' => 'crm_name_b2b'
	));

	$setting = $setting_query->fetch();

	return $crm_name = $setting['setting_value'];
}


//Get City of CRM
function getCrmCityr()
{
	global $db;

	$setting_query = $db->prepare("
		SELECT setting_value
		FROM idk_setting
		WHERE setting_name = :setting_name");

	$setting_query->execute(array(
		':setting_name' => 'crm_city_b2b'
	));

	$setting = $setting_query->fetch();

	return $crm_city = $setting['setting_value'];
}


//Subdomain name return
function getSubdomainr()
{
	global $db;

	$settings_query = $db->prepare("
		SELECT settings_value
		FROM idk_settings
		WHERE settings_name = :settings_name");

	$settings_query->execute(array(
		':settings_name' => 'crm_subdomain'
	));

	$settings = $settings_query->fetch();

	return $settings['settings_value'];
}


//DB size return
function getDBSize()
{
	global $db;

	$size = 0;
	$query = $db->prepare("SHOW TABLE STATUS");
	$query->execute();
	$result = $query->fetchAll();

	foreach ($result as $row) {
		$size += $row["Data_length"] + $row["Index_length"];
	}

	return $size;
}


//Number of users return
function getNumberUsers()
{
	global $db;

	$query = $db->prepare("
		SELECT COUNT(employee_id) AS employee_total
		FROM idk_employees
		WHERE employee_status != :employee_status");

	$query->execute(array(
		':employee_status' => 0
	));

	$row = $query->fetch();

	return $row['employee_total'];
}


//CRM Folder size return
function getCrmFolderSizer()
{
	global $db;

	$settings_query = $db->prepare("
		SELECT settings_value
		FROM idk_settings
		WHERE settings_name = :settings_name");

	$settings_query->execute(array(
		':settings_name' => 'crm_folder_size'
	));

	$settings = $settings_query->fetch();

	return $settings['settings_value'];
}


//Copyright
function getCopyright()
{
	echo "<p>©" . date('Y') . " Sva prava pridržana - IDK Studio | Licenca za " . getCrmNamer() . " - Bez prava daljnje distribucije. Verzija: 1.0</p>";
}


//Get title
function getTitle()
{
	echo "" . getCrmNamer() . " - Administracija";
}


//Get employee full name
function getEmployeeFullname()
{
	global $db;
	global $logged_employee_id;

	$query = $db->prepare("
		SELECT employee_first_name, employee_last_name
		FROM idk_employee
		WHERE employee_id = :employee_id");

	$query->execute(array(
		':employee_id' => $logged_employee_id
	));

	$employee = $query->fetch();

	echo $employee['employee_first_name'] . " " . $employee['employee_last_name'];
}


//Get employee email
function getEmployeeEmail()
{
	global $db;
	global $logged_employee_id;

	$query = $db->prepare("
		SELECT employee_email
		FROM idk_employee
		WHERE employee_id = :employee_id");

	$query->execute(array(
		':employee_id' => $logged_employee_id
	));

	$employee = $query->fetch();

	echo $employee['employee_email'];
}


//Get employee image
function getEmployeeImage()
{
	global $db;
	global $logged_employee_id;

	$query = $db->prepare("
		SELECT employee_image
		FROM idk_employee
		WHERE employee_id = :employee_id");

	$query->execute(array(
		':employee_id' => $logged_employee_id
	));

	$employee = $query->fetch();

	if ($employee['employee_image'] == "none") {
		echo "none.jpg";
	} else {
		echo $employee['employee_image'];
	}
}


//Get employee position
function getEmployeePosition()
{
	global $db;
	global $logged_employee_id;

	$query = $db->prepare("
		SELECT t1.od_data
		FROM idk_employee_otherdata t1, idk_employee t2
		WHERE t1.od_group = :od_group AND t1.od_id = t2.employee_position AND t2.employee_id = :employee_id");

	$query->execute(array(
		':od_group' => 1,
		':employee_id' => $logged_employee_id
	));

	$employee = $query->fetch();

	if (isset($employee['od_data'])) echo $employee['od_data'];
	else echo "";
}


//Get employee status
function getEmployeeStatus()
{
	global $db;
	global $logged_employee_id;

	$query = $db->prepare("
		SELECT employee_status
		FROM idk_employee
		WHERE employee_id = :employee_id");

	$query->execute(array(
		':employee_id' => $logged_employee_id
	));

	$user = $query->fetch();

	return $user['employee_status'];
}


//Get categories
function getCategories($category_sub = 0)
{
	global $db;

	$cat_query = $db->prepare("
		SELECT category_id, category_name, category_sub, category_image
		FROM idk_category
		WHERE category_sub = :category_sub
		ORDER BY category_sub ASC");

	$cat_query->execute(array(
		':category_sub' => $category_sub
	));

	echo "<ul>";

	while ($cat = $cat_query->fetch()) {

		$category_id = $cat['category_id'];
		$category_name = $cat['category_name'];
		$category_sub = $cat['category_sub'];
		$category_image = $cat['category_image'];
		if (!isset($category_image)) {
			$category_image = "none.jpg";
		}

		echo "<li class='list-group-item vb_hoverbg'>
					<div class='pull-left'><img src='" . getSiteUrlr() . "idkadmin/files/categorys/images/" . $category_image . "' alt='" . $category_name . "slika' style='height: 34px; margin-right: 10px;'></div>
					<div class='pull-left' style='line-height: 34px;'>" . $category_name . "</div>
					<div class='pull-right text-center'>
						<div class='btn-group material-btn-group'>
						<button
						class='dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table'
						data-toggle='dropdown'><i class='fa fa-cogs fa-lg' aria-hidden='true'></i> <span class='caret material-btn__caret'></span></button>
							<ul class='dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table' role='menu'>
						
							<li>
							<a href='categories?page=edit&category_id=" . $category_id . "'
							class='material-dropdown-menu__link'><i class='fa fa-pencil-square-o'
								aria-hidden='true'></i> Uredi</a>
							</li>

							<li class='idk_dropdown_danger'>
							<a onclick='getHref()' href='#' data='categories?page=del&category_id=" . $category_id . "' data-toggle='modal' data-target='#modalDelete'
							class='obrisi material-dropdown-menu__link'><i class='fa fa-trash'
								aria-hidden='true'></i> Obriši</a>
							</li>

							</ul>
						</div>
					</div><div class='clearfix'></div></li>";

		getCategories($category_id);
	}

	echo "</ul>";
}


//Add log
function addLog($logged_employee_id, $log_desc, $log_date)
{
	global $db;

	$log_query = $db->prepare("
		INSERT INTO idk_log
			(employee_id, log_desc, log_date)
		VALUES
			(:employee_id, :log_desc, :log_date)");

	$log_query->execute(array(
		':employee_id' => $logged_employee_id,
		':log_desc' => $log_desc,
		':log_date' => $log_date
	));
}


//Add notification
function addNotification($notification_title, $notification_icon, $notification_link, $notification_employeeid, $notification_type)
{
	global $db;

	$query_notification = $db->prepare("
		INSERT INTO idk_notifications
			(notification_datetime, notification_title, notification_icon, notification_link, notification_employeeid, notification_status, notification_type)
		VALUES
			(:notification_datetime, :notification_title, :notification_icon, :notification_link, :notification_employeeid, :notification_status, :notification_type)");

	$query_notification->execute(array(
		':notification_datetime' => date('Y-m-d H:i:s'),
		':notification_title' => $notification_title,
		':notification_icon' => $notification_icon,
		':notification_link' => $notification_link,
		':notification_employeeid' => $notification_employeeid,
		':notification_status' => 1,
		':notification_type' => $notification_type
	));
}


//Add report
function addReport($employee_id, $client_id, $order_id, $report_start_time, $report_end_time)
{
	global $db;

	if (!isset($report_end_time)) {
		$query_report_get = $db->prepare("
			SELECT employee_id
			FROM idk_report
			WHERE employee_id = :employee_id AND report_end_time IS NULL");

		$query_report_get->execute(array(
			':employee_id' => $employee_id
		));

		$number_of_rows = $query_report_get->rowCount();

		if ($number_of_rows > 0) {
			$update_report_query = $db->prepare("
				UPDATE idk_report
				SET	order_id = :order_id, report_start_time = :report_start_time, report_end_time = :report_end_time
				WHERE employee_id = :employee_id AND report_end_time IS NULL");

			$update_report_query->execute(array(
				':employee_id' => $employee_id,
				':order_id' => $order_id,
				':report_start_time' => $report_start_time,
				':report_end_time' => $report_end_time
			));
		} else {
			$query_report = $db->prepare("
				INSERT INTO idk_report
					(employee_id, client_id, order_id, report_start_time, report_end_time)
				VALUES
					(:employee_id, :client_id, :order_id, :report_start_time, :report_end_time)");

			$query_report->execute(array(
				':employee_id' => $employee_id,
				':client_id' => $client_id,
				':order_id' => $order_id,
				':report_start_time' => $report_start_time,
				':report_end_time' => $report_end_time
			));
		}
	} else {
		$update_report_query = $db->prepare("
			UPDATE idk_report
			SET	order_id = :order_id, report_end_time = :report_end_time
			WHERE employee_id = :employee_id AND client_id = :client_id AND report_end_time IS NULL");

		$update_report_query->execute(array(
			':employee_id' => $employee_id,
			':client_id' => $client_id,
			':order_id' => $order_id,
			':report_end_time' => $report_end_time
		));
	}
}


//Update orders statistics
function updateOrdersStats()
{
	global $db;

	$stat_month = date('Y-m-01');

	$check_date_query = $db->prepare("
		SELECT stat_id
		FROM idk_stat
		WHERE stat_month = :stat_month");

	$check_date_query->execute(array(
		':stat_month' => $stat_month
	));

	$check_date = $check_date_query->rowCount();

	if ($check_date > 0) {
		$update_stats_query = $db->prepare("
			UPDATE idk_stat
			SET	stat_b2b_orders = stat_b2b_orders + 1
			WHERE stat_month = :stat_month");

		$update_stats_query->execute(array(
			':stat_month' => $stat_month
		));
	} else {
		$add_stats_query = $db->prepare("
			INSERT INTO idk_stat
				(stat_month, stat_b2b_orders)
			VALUES
				(:stat_month, :stat_b2b_orders)");

		$add_stats_query->execute(array(
			':stat_month' => $stat_month,
			':stat_b2b_orders' => 1
		));
	}
}


//Update visits statistics
function updateVisitsStats()
{
	global $db;

	$stat_month = date('Y-m-01');

	$check_date_query = $db->prepare("
		SELECT stat_id
		FROM idk_stat
		WHERE stat_month = :stat_month");

	$check_date_query->execute(array(
		':stat_month' => $stat_month
	));

	$check_date = $check_date_query->rowCount();

	if ($check_date > 0) {
		$update_stats_query = $db->prepare("
			UPDATE idk_stat
			SET	stat_b2b_visits = stat_b2b_visits + 1
			WHERE stat_month = :stat_month");

		$update_stats_query->execute(array(
			':stat_month' => $stat_month
		));
	} else {
		$add_stats_query = $db->prepare("
			INSERT INTO idk_stat
				(stat_month, stat_b2b_visits)
			VALUES
				(:stat_month, :stat_b2b_visits)");

		$add_stats_query->execute(array(
			':stat_month' => $stat_month,
			':stat_b2b_visits' => 1
		));
	}
}


//Update clients statistics
function updateClientsStats()
{
	global $db;

	$stat_month = date('Y-m-01');

	$check_date_query = $db->prepare("
		SELECT stat_id
		FROM idk_stat
		WHERE stat_month = :stat_month");

	$check_date_query->execute(array(
		':stat_month' => $stat_month
	));

	$check_date = $check_date_query->rowCount();

	if ($check_date > 0) {
		$update_stats_query = $db->prepare("
			UPDATE idk_stat
			SET	stat_b2b_clients = stat_b2b_clients + 1
			WHERE stat_month = :stat_month");

		$update_stats_query->execute(array(
			':stat_month' => $stat_month
		));
	} else {
		$add_stats_query = $db->prepare("
			INSERT INTO idk_stat
				(stat_month, stat_b2b_clients)
			VALUES
				(:stat_month, :stat_b2b_clients)");

		$add_stats_query->execute(array(
			':stat_month' => $stat_month,
			':stat_b2b_clients' => 1
		));
	}
}


//Get orders statistics
function getOrdersStats()
{
	global $db;

	$stat_month = date('Y-m-01');

	$get_stats_query = $db->prepare("
			SELECT stat_b2b_orders
			FROM idk_stat
			WHERE stat_month = :stat_month");

	$get_stats_query->execute(array(
		':stat_month' => $stat_month
	));

	$number_of_rows = $get_stats_query->rowCount();

	//If it doesn't exist add new:
	if ($number_of_rows != 0) {
		$get_stat_orders = $get_stats_query->fetch();
		$stat_orders = $get_stat_orders['stat_b2b_orders'];
	}

	if (isset($stat_orders)) {
		echo $stat_orders;
	} else {
		echo '0';
	}
}


//Get visits statistics
function getVisitsStats()
{
	global $db;

	$stat_month = date('Y-m-01');

	$get_stats_query = $db->prepare("
			SELECT stat_b2b_visits
			FROM idk_stat
			WHERE stat_month = :stat_month");

	$get_stats_query->execute(array(
		':stat_month' => $stat_month
	));

	$number_of_rows = $get_stats_query->rowCount();

	//If it doesn't exist add new:
	if ($number_of_rows != 0) {
		$get_stat_visits = $get_stats_query->fetch();
		$stat_visits = $get_stat_visits['stat_b2b_visits'];
	}

	if (isset($stat_visits)) {
		echo $stat_visits;
	} else {
		echo '0';
	}
}

//Get clients statistics
function getClientsStats()
{
	global $db;

	$stat_month = date('Y-m-01');

	$get_stats_query = $db->prepare("
			SELECT stat_b2b_clients
			FROM idk_stat
			WHERE stat_month = :stat_month");

	$get_stats_query->execute(array(
		':stat_month' => $stat_month
	));

	$number_of_rows = $get_stats_query->rowCount();

	//If it doesn't exist add new:
	if ($number_of_rows != 0) {
		$get_stat_clients = $get_stats_query->fetch();
		$stat_clients = $get_stat_clients['stat_b2b_clients'];
	}

	if (isset($stat_clients)) {
		echo $stat_clients;
	} else {
		echo '0';
	}
}


//Upload and save image
function uploadImage($image_type, $edit, $id, $front)
{
	global $db;
	$image_final = NULL;

	if (isset($_FILES['' . $image_type . '_image']) and $_FILES['' . $image_type . '_image']['size'] !== 0) {

		// If this is on front (b2b shop) change image path
		if ($front) {
			$front_path = "./idkadmin/";
		} else {
			$front_path = "./";
		}

		// If this is in case edit delete old image first
		if ($edit) {

			//Delete old image
			$del_old_img_query = $db->prepare("
				SELECT ${image_type}_image
				FROM idk_${image_type}
				WHERE ${image_type}_id = :id");

			$del_old_img_query->execute(array(
				':id' => $id
			));

			$del_old_img = $del_old_img_query->fetch();

			$image = $del_old_img['' . $image_type . '_image'];

			if ($image == "" or $image == "none" or $image == "none.jpg") {
			} else {
				unlink("${front_path}files/${image_type}s/images/${image}");
				unlink("${front_path}files/${image_type}s/thumbs/${image}");
			}
		}

		// New image
		$image = $_FILES['' . $image_type . '_image'];

		//File properties
		$file_name = $image['name'];
		$file_tmp = $image['tmp_name'];
		$file_size = $image['size'];
		$file_error = $image['error'];

		//File extension
		$file_ext = explode('.', $file_name);
		$file_ext = strtolower(end($file_ext));

		$allowed = array('jpg', 'png');

		if (in_array($file_ext, $allowed)) {
			if ($file_error === 0) {
				//Size 2MB
				if ($file_size <= 2097152) {

					$image_final = uniqid('', true) . '.' . $file_ext;
					$file_destination = "${front_path}files/${image_type}s/images/${image_final}";

					if (move_uploaded_file($file_tmp, $file_destination)) {
						$path_to_image_directory = "${front_path}files/${image_type}s/images/";
						$path_to_thumbs_directory = "${front_path}files/${image_type}s/thumbs/";
						$final_width_of_image = 200;

						if (preg_match('/[.](jpg)$/', $image_final)) {
							$im = imagecreatefromjpeg($path_to_image_directory . $image_final);
						} else if (preg_match('/[.](gif)$/', $image_final)) {
							$im = imagecreatefromgif($path_to_image_directory . $image_final);
						} else if (preg_match('/[.](png)$/', $image_final)) {
							$im = imagecreatefrompng($path_to_image_directory . $image_final);
						}

						$ox = imagesx($im);
						$oy = imagesy($im);

						$nx = $final_width_of_image;
						$ny = floor($oy * ($final_width_of_image / $ox));

						$nm = imagecreatetruecolor($nx, $ny);

						imagecopyresized($nm, $im, 0, 0, 0, 0, $nx, $ny, $ox, $oy);

						if (!file_exists($path_to_thumbs_directory)) {
							if (!mkdir($path_to_thumbs_directory)) {
								die("There was a problem. Please try again!");
							}
						}

						imagejpeg($nm, $path_to_thumbs_directory . $image_final);
					}
				}
			}
		}
	} else {
		$image_final = "none.jpg";
		if ($edit) {
			$image_final = $_POST['' . $image_type . '_image_url'];
		} else {
			if ($image_type == "product") {
				//Look for default image
				$check_default_image_query = $db->prepare("
					SELECT od_other_info
					FROM idk_product_otherdata
					WHERE od_data = :od_data AND od_group = :od_group");

				$check_default_image_query->execute(array(
					':od_data' => 'Primarna slika',
					':od_group' => 4
				));

				$number_of_rows = $check_default_image_query->rowCount();

				//If it doesn't exist add new:
				if ($number_of_rows != 0) {
					$check_default_image = $check_default_image_query->fetch();
					$image_final = $check_default_image['od_other_info'];
				}
			}
		}
	}

	return $image_final;
}


//Get current month and year
function getCurrentMonthAndYear()
{
	$month = date("m");

	if ($month == 1) {
		$month = "januar";
	} elseif ($month == 2) {
		$month = "februar";
	} elseif ($month == 3) {
		$month = "mart";
	} elseif ($month == 4) {
		$month = "april";
	} elseif ($month == 5) {
		$month = "maj";
	} elseif ($month == 6) {
		$month = "juni";
	} elseif ($month == 7) {
		$month = "juli";
	} elseif ($month == 8) {
		$month = "august";
	} elseif ($month == 9) {
		$month = "septembar";
	} elseif ($month == 10) {
		$month = "oktobar";
	} elseif ($month == 11) {
		$month = "novembar";
	} elseif ($month == 12) {
		$month = "decembar";
	}

	$year = date("Y");

	echo "${month} ${year}";
}


//Get date for idkadmin/index
function getAdminDate()
{
	$day = date("l");

	if ($day == "Monday") {
		$day = "Ponedjeljak";
	} elseif ($day == "Tuesday") {
		$day = "Utorak";
	} elseif ($day == "Wednesday") {
		$day = "Srijeda";
	} elseif ($day == "Thursday") {
		$day = "Četvrtak";
	} elseif ($day == "Friday") {
		$day = "Petak";
	} elseif ($day == "Saturday") {
		$day = "Subota";
	} elseif ($day == "Sunday") {
		$day = "Nedjelja";
	}

	$todayDate = date("H:i");
	$currentTime = $todayDate;

	echo $day, date(", d.m.Y.");
}


//Get temperature for idkadmin/index
function getTemperature()
{
	$xml = simplexml_load_file("http://api.openweathermap.org/data/2.5/weather?q=" . getCrmCityr() . "&units=metric&mode=xml&appid=33d81ab4b496f28eabc7f40fb79d0500") or die("Error: Cannot create object");

	$temperature = $xml->temperature[0]->attributes();
	echo round($temperature);
}


//Get weather icon for idkadmin/index
function getWeatherIcon()
{
	//Code: http://openweathermap.org/weather-conditions
	$xml = simplexml_load_file("http://api.openweathermap.org/data/2.5/weather?q=Bihac&units=metric&mode=xml&appid=33d81ab4b496f28eabc7f40fb79d0500") or die("Error: Cannot create object");

	$icon_code = $xml->weather[0]->attributes();

	if ($icon_code == "802" or $icon_code == "803" or $icon_code == "804") {
		$weather_icon = "w_icon3.png";
	} elseif ($icon_code == "600") {
		$weather_icon = "w_icon7.png";
	} elseif ($icon_code == "800") {
		$weather_icon = "w_icon1.png";
	} elseif ($icon_code == "801") {
		$weather_icon = "w_icon2.png";
	} elseif ($icon_code == "500" or $icon_code == "501") {
		$weather_icon = "w_icon5.png";
	} elseif ($icon_code == "601") {
		$weather_icon = "w_icon8.png";
	} else {
		$weather_icon = "w_icon3.png";
	}

	echo $weather_icon;
}


//Create slug
function create_slug($slug)
{
	$bad = array('Š', 'Ž', 'š', 'ž', 'ć', 'Ć', 'č', 'Č', 'đ', 'Đ', 'Ä', 'Ö', 'Ü', 'ẞ', 'ä', 'ö', 'ü', 'ß', ' ', '"', ',', '.', ':');
	$good = array('S', 'Z', 's', 'z', 'c', 'C', 'c', 'C', 'd', 'D', 'A', 'O', 'U', 'S', 'a', 'o', 'u', 's', '-', '', '', '', '');

	$slug = str_replace($bad, $good, $slug);
	$slug = preg_replace('~[^\\pL\d]+~u', '-', $slug);
	$slug = trim($slug, '-');
	$slug = iconv('utf-8', 'us-ascii//TRANSLIT', $slug);
	$slug = strtolower($slug);
	$slug = preg_replace('~[^-\w]+~', '', $slug);

	if (empty($slug)) {
		return 'n-a';
	}

	return $slug;
}


// Send email
function sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody)
{

	$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
	try {
		//Server setting
		$mail->Host = 'mail.idkserver.com;mail.idkserver.com';
		$mail->SMTPAuth = true;
		$mail->Username = 'noreply@unaviva.ba';
		$mail->Password = 'zN!sds3qGSE';
		$mail->SMTPSecure = 'TLS';
		$mail->Port = 465;
		$mail->CharSet = 'UTF-8';

		//Recipients
		$mail->setFrom('noreply@unaviva.ba', 'Unaviva B2B');
		$mail->addAddress($mail_email, $mail_name);     // Add a recipient

		//Content
		$mail->isHTML(true);                                  // Set email format to HTML
		$mail->Subject = $mail_subject;
		$mail->Body    = $mail_body;
		$mail->AltBody = $mail_altbody;

		$mail->send();
	} catch (Exception $e) {
		echo 'Message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
	}
}
