<?php
include("includes/functions.php");

$getEmployeeStatus = getEmployeeStatus();

$form = "";

if (isset($_REQUEST["form"])) {
	$form = $_REQUEST["form"];

	switch ($form) {



			/************************************************************
		 * 						LOGIN
		 * *********************************************************/
		case "login":

			$login_email = $_POST['login_email'];
			$login_password = $_POST['login_password'];

			if (isset($_POST['login_rm'])) {
				$login_rm = $_POST['login_rm'];
			} else {
				$login_rm = "off";
			}

			$login_query = $db->prepare("
				SELECT employee_id, employee_login_email, employee_password, employee_key, employee_status
				FROM idk_employee
				WHERE employee_login_email = :employee_login_email AND employee_active != :employee_active");

			$login_query->execute(array(
				':employee_login_email' => $login_email,
				':employee_active' => 0
			));

			$user = $login_query->fetch();

			if (md5($login_password) == $user['employee_password'] and $user['employee_status'] == 1) {

				if ($login_rm == "on") {
					$month = time() + 2 * 60 * 60 + 60 * 60 * 24 * 30;
					setcookie('idk_session', $user['employee_key'], $month, '/crm');
				} else {
					$day = time() + 2 * 60 * 60 + 60 * 60 * 24;
					setcookie('idk_session', $user['employee_key'], $day, '/crm');
				}

				//Add to log
				$employee_id = $user['employee_id'];
				$log_desc = "Zaposlenik se prijavio";
				$log_date = date('Y-m-d H:i:s');
				addLog($employee_id, $log_desc, $log_date);

				header("Location: " . getSiteUrlr() . "idkadmin/");
			} else {
				header("Location: login?mess=2");
			}
			break;



			/************************************************************
			 * 						LOGOUT
			 * *********************************************************/
		case "logout":

			//Add to log
			$log_desc = "Zaposlenik se odjavio";
			$log_date = date('Y-m-d H:i:s');
			addLog($logged_employee_id, $log_desc, $log_date);

			unset($_COOKIE['idk_session']);
			setcookie('idk_session', '', time() + 2 * 60 * 60 - 60 * 60 * 24 * 30, '/crm');

			header("Location: login?mess=1");

			break;



			/*-----------------------------------------------------------------------------------------
								EMPLOYEE START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 							ADD NEW EMPLOYEE
			 * *********************************************************/
		case "add_employee":

			if ($getEmployeeStatus == 1) {

				$employee_login_email = $_POST['employee_login_email'];

				//Check if user exists
				$check_query = $db->prepare("
					SELECT employee_login_email
					FROM idk_employee
					WHERE employee_login_email = :employee_login_email");

				$check_query->execute(array(
					':employee_login_email' => $employee_login_email
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {

					$employee_first_name = $_POST['employee_first_name'];
					$employee_last_name = $_POST['employee_last_name'];
					$employee_password = MD5($_POST['employee_password']);
					$employee_key = MD5(rand());
					$employee_color = $_POST['employee_color'];
					$employee_rfid = $_POST['employee_rfid'];
					$employee_position = $_POST['employee_position'];
					$employee_commercialist_type = !empty($_POST['employee_commercialist_type']) ? $_POST['employee_commercialist_type'] : NULL;
					$employee_address = $_POST['employee_address'];
					$employee_city = $_POST['employee_city'];
					$employee_region = $_POST['employee_region'];
					$employee_country = $_POST['employee_country'];
					$employee_other_info = $_POST['employee_other_info'];
					$employee_status = $_POST['employee_status'];
					$created_at = date('Y-m-d H:i:s');
					$updated_at = date('Y-m-d H:i:s');
					if (!empty($_POST['employee_jmbg'])) {
						$employee_jmbg = $_POST['employee_jmbg'];
					} else {
						$employee_jmbg = null;
					}
					if (!empty($_POST['employee_dob'])) {
						$employee_dob = date('Y-m-d', strtotime($_POST['employee_dob']));
					} else {
						$employee_dob = null;
					}
					if (!empty($_POST['employee_doe'])) {
						$employee_doe = date('Y-m-d', strtotime($_POST['employee_doe']));
					} else {
						$employee_doe = null;
					}

					//Upload and save employee_image
					$employee_image_final = uploadImage('employee', 0, 0, 0);

					//Add user to db
					$query = $db->prepare("
						INSERT INTO idk_employee
							(employee_first_name, employee_last_name, employee_login_email, employee_password, employee_key, employee_position, employee_commercialist_type, employee_status, employee_image, employee_jmbg, employee_rfid, employee_color, employee_address, employee_city, employee_region, employee_country, employee_other_info, employee_dob, employee_doe, created_at, updated_at)
						VALUES
							(:employee_first_name, :employee_last_name, :employee_login_email, :employee_password, :employee_key, :employee_position, :employee_commercialist_type, :employee_status, :employee_image, :employee_jmbg, :employee_rfid, :employee_color, :employee_address, :employee_city, :employee_region, :employee_country, :employee_other_info, :employee_dob, :employee_doe, :created_at, :updated_at)");

					$query->execute(array(
						':employee_first_name' => $employee_first_name,
						':employee_last_name' => $employee_last_name,
						':employee_login_email' => $employee_login_email,
						':employee_password' => $employee_password,
						':employee_key' => $employee_key,
						':employee_position' => $employee_position,
						':employee_commercialist_type' => $employee_commercialist_type,
						':employee_status' => $employee_status,
						':employee_jmbg' => $employee_jmbg,
						':employee_rfid' => $employee_rfid,
						':employee_color' => $employee_color,
						':employee_address' => $employee_address,
						':employee_city' => $employee_city,
						':employee_region' => $employee_region,
						':employee_country' => $employee_country,
						':employee_other_info' => $employee_other_info,
						':employee_dob' => $employee_dob,
						':employee_doe' => $employee_doe,
						':employee_image' => $employee_image_final,
						':created_at' => $created_at,
						':updated_at' => $updated_at
					));

					/* Add to log */
					$log_desc = "Dodao novog zaposlenika: " . $employee_first_name . " " . $employee_last_name . " ";
					$log_date = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $log_date);

					header("Location: employees?page=list&mess=1");
				} else {
					header("Location: employees?page=list&mess=2");
				}
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							EDIT EMPLOYEE
			 * *********************************************************/
		case "edit_employee":

			$employee_id = $_POST['employee_id'];

			if ($getEmployeeStatus == 1) {

				$employee_first_name = $_POST['employee_first_name'];
				$employee_last_name = $_POST['employee_last_name'];
				$employee_login_email = $_POST['employee_login_email'];
				$employee_color = $_POST['employee_color'];
				$employee_rfid = $_POST['employee_rfid'];
				$employee_position = $_POST['employee_position'];
				$employee_commercialist_type = !empty($_POST['employee_commercialist_type']) ? $_POST['employee_commercialist_type'] : NULL;
				$employee_address = $_POST['employee_address'];
				$employee_city = $_POST['employee_city'];
				$employee_region = $_POST['employee_region'];
				$employee_country = $_POST['employee_country'];
				$employee_other_info = $_POST['employee_other_info'];
				$employee_status = $_POST['employee_status'];
				$updated_at = date('Y-m-d H:i:s');
				if (!empty($_POST['employee_jmbg'])) {
					$employee_jmbg = $_POST['employee_jmbg'];
				} else {
					$employee_jmbg = null;
				}
				if (!empty($_POST['employee_dob'])) {
					$employee_dob = date('Y-m-d', strtotime($_POST['employee_dob']));
				} else {
					$employee_dob = null;
				}
				if (!empty($_POST['employee_doe'])) {
					$employee_doe = date('Y-m-d', strtotime($_POST['employee_doe']));
				} else {
					$employee_doe = null;
				}

				//Upload and save employee_image
				$employee_image_final = uploadImage('employee', 1, $employee_id, 0);

				if (!empty($_POST['employee_password'])) {
					$employee_password = MD5($_POST['employee_password']);
					$query_statement = "
						UPDATE idk_employee
						SET	employee_first_name = :employee_first_name, employee_last_name = :employee_last_name, employee_password = :employee_password, employee_jmbg = :employee_jmbg, employee_login_email = :employee_login_email, employee_color = :employee_color, employee_rfid = :employee_rfid, employee_position = :employee_position, employee_commercialist_type = :employee_commercialist_type, employee_dob = :employee_dob, employee_doe = :employee_doe, employee_address = :employee_address, employee_city = :employee_city, employee_region = :employee_region, employee_country = :employee_country, employee_other_info = :employee_other_info, employee_status = :employee_status, employee_image = :employee_image, updated_at = :updated_at
						WHERE employee_id = :employee_id";

					$query_array = [
						':employee_first_name' => $employee_first_name,
						':employee_last_name' => $employee_last_name,
						':employee_password' => $employee_password,
						':employee_login_email' => $employee_login_email,
						':employee_position' => $employee_position,
						':employee_commercialist_type' => $employee_commercialist_type,
						':employee_status' => $employee_status,
						':employee_jmbg' => $employee_jmbg,
						':employee_rfid' => $employee_rfid,
						':employee_color' => $employee_color,
						':employee_address' => $employee_address,
						':employee_city' => $employee_city,
						':employee_region' => $employee_region,
						':employee_country' => $employee_country,
						':employee_other_info' => $employee_other_info,
						':employee_dob' => $employee_dob,
						':employee_doe' => $employee_doe,
						':employee_image' => $employee_image_final,
						':updated_at' => $updated_at,
						':employee_id' => $employee_id
					];
				} else {
					$query_statement = "
						UPDATE idk_employee
						SET	employee_first_name = :employee_first_name, employee_last_name = :employee_last_name, employee_jmbg = :employee_jmbg, employee_login_email = :employee_login_email, employee_color = :employee_color, employee_rfid = :employee_rfid, employee_position = :employee_position, employee_commercialist_type = :employee_commercialist_type, employee_dob = :employee_dob, employee_doe = :employee_doe, employee_address = :employee_address, employee_city = :employee_city, employee_region = :employee_region, employee_country = :employee_country, employee_other_info = :employee_other_info, employee_status = :employee_status, employee_image = :employee_image, updated_at = :updated_at
						WHERE employee_id = :employee_id";

					$query_array = [
						':employee_first_name' => $employee_first_name,
						':employee_last_name' => $employee_last_name,
						':employee_jmbg' => $employee_jmbg,
						':employee_login_email' => $employee_login_email,
						':employee_color' => $employee_color,
						':employee_rfid' => $employee_rfid,
						':employee_position' => $employee_position,
						':employee_commercialist_type' => $employee_commercialist_type,
						':employee_dob' => $employee_dob,
						':employee_doe' => $employee_doe,
						':employee_address' => $employee_address,
						':employee_city' => $employee_city,
						':employee_region' => $employee_region,
						':employee_country' => $employee_country,
						':employee_other_info' => $employee_other_info,
						':employee_status' => $employee_status,
						':employee_image' => $employee_image_final,
						':updated_at' => $updated_at,
						':employee_id' => $employee_id
					];
				}

				$query = $db->prepare($query_statement);

				$query->execute($query_array);

				//Add to log
				$log_desc = "Uredio profil zaposlenika: " . $employee_first_name . " " . $employee_last_name . " ";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: employees?page=list&mess=3");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							EDIT PROFILE
			 * *********************************************************/
		case "edit_profile":

			$employee_id = $_POST['employee_id'];

			if ($employee_id == $logged_employee_id) {

				$employee_first_name = $_POST['employee_first_name'];
				$employee_last_name = $_POST['employee_last_name'];
				$employee_login_email = $_POST['employee_login_email'];
				$employee_color = $_POST['employee_color'];
				$employee_rfid = $_POST['employee_rfid'];
				$employee_address = $_POST['employee_address'];
				$employee_city = $_POST['employee_city'];
				$employee_region = $_POST['employee_region'];
				$employee_country = $_POST['employee_country'];
				$updated_at = date('Y-m-d H:i:s');
				if (!empty($_POST['employee_jmbg'])) {
					$employee_jmbg = $_POST['employee_jmbg'];
				} else {
					$employee_jmbg = null;
				}
				if (!empty($_POST['employee_dob'])) {
					$employee_dob = date('Y-m-d', strtotime($_POST['employee_dob']));
				} else {
					$employee_dob = null;
				}
				if (!empty($_POST['employee_doe'])) {
					$employee_doe = date('Y-m-d', strtotime($_POST['employee_doe']));
				} else {
					$employee_dob = null;
				}

				//Upload and save employee_image
				$employee_image_final = uploadImage('employee', 1, $employee_id, 0);

				if (!empty($_POST['employee_password'])) {
					$employee_password = MD5($_POST['employee_password']);
					$query_statement = "
						UPDATE idk_employee
						SET	employee_first_name = :employee_first_name, employee_last_name = :employee_last_name, employee_password = :employee_password, employee_jmbg = :employee_jmbg, employee_login_email = :employee_login_email, employee_color = :employee_color, employee_rfid = :employee_rfid, employee_dob = :employee_dob, employee_doe = :employee_doe, employee_address = :employee_address, employee_city = :employee_city, employee_region = :employee_region, employee_country = :employee_country, employee_image = :employee_image, updated_at = :updated_at
						WHERE employee_id = :employee_id";

					$query_array = [
						':employee_first_name' => $employee_first_name,
						':employee_last_name' => $employee_last_name,
						':employee_password' => $employee_password,
						':employee_login_email' => $employee_login_email,
						':employee_jmbg' => $employee_jmbg,
						':employee_rfid' => $employee_rfid,
						':employee_color' => $employee_color,
						':employee_address' => $employee_address,
						':employee_city' => $employee_city,
						':employee_region' => $employee_region,
						':employee_country' => $employee_country,
						':employee_dob' => $employee_dob,
						':employee_doe' => $employee_doe,
						':employee_image' => $employee_image_final,
						':updated_at' => $updated_at,
						':employee_id' => $employee_id
					];
				} else {
					$query_statement = "
							UPDATE idk_employee
							SET	employee_first_name = :employee_first_name, employee_last_name = :employee_last_name, employee_jmbg = :employee_jmbg, employee_login_email = :employee_login_email, employee_color = :employee_color, employee_rfid = :employee_rfid, employee_dob = :employee_dob, employee_doe = :employee_doe, employee_address = :employee_address, employee_city = :employee_city, employee_region = :employee_region, employee_country = :employee_country, employee_image = :employee_image, updated_at = :updated_at
							WHERE employee_id = :employee_id";

					$query_array =
						[
							':employee_first_name' => $employee_first_name,
							':employee_last_name' => $employee_last_name,
							':employee_login_email' => $employee_login_email,
							':employee_jmbg' => $employee_jmbg,
							':employee_rfid' => $employee_rfid,
							':employee_color' => $employee_color,
							':employee_address' => $employee_address,
							':employee_city' => $employee_city,
							':employee_region' => $employee_region,
							':employee_country' => $employee_country,
							':employee_dob' => $employee_dob,
							':employee_doe' => $employee_doe,
							':employee_image' => $employee_image_final,
							':updated_at' => $updated_at,
							':employee_id' => $employee_id
						];
				}

				$query = $db->prepare($query_statement);

				$query->execute($query_array);

				//Add to log
				$log_desc = "Uredio lični profil: " . $employee_first_name . " " . $employee_last_name . " ";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: employees?page=edit_profile&mess=1");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							ADD EMPLOYEE NOTE
			 * *********************************************************/
		case "add_employee_note":

			$employee_id = $_POST['employee_id'];

			if ($employee_id == $logged_employee_id or $getEmployeeStatus == 1) {

				$note_txt = $_POST['note_txt'];
				$note_datetime = date('Y-m-d H:i:s');
				$note_group = 1;
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');

				$query = $db->prepare("
					INSERT INTO idk_note
						(note_txt, note_datetime, note_group, employee_id, note_created_by_id, created_at, updated_at)
					VALUES
						(:note_txt, :note_datetime, :note_group, :employee_id, :note_created_by_id, :created_at, :updated_at)");

				$query->execute(array(
					':note_txt' => $note_txt,
					':note_datetime' => $note_datetime,
					':note_group' => $note_group,
					':employee_id' => $employee_id,
					':created_at' => $created_at,
					':updated_at' => $updated_at,
					':note_created_by_id' => $logged_employee_id
				));

				$query_employee = $db->prepare("
					SELECT employee_first_name, employee_last_name
					FROM idk_employee
					WHERE employee_id = :employee_id");

				$query_employee->execute(array(
					':employee_id' => $employee_id
				));

				while ($employee = $query_employee->fetch()) {
					$employee_first_name = $employee['employee_first_name'];
					$employee_last_name = $employee['employee_last_name'];
				}

				//Add to log
				$log_desc = "Dodao bilješku za korisnika: " . $employee_first_name . " " . $employee_last_name . " ";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: employees?page=open&id=$employee_id&mess=1");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							ADD EMPLOYEE DOCUMENT
			 * *********************************************************/
		case "add_employee_doc":

			$employee_id = $_POST['employee_id'];

			if ($employee_id == $logged_employee_id or $getEmployeeStatus == 1) {

				//Upload document
				$doc_file = $_FILES['doc_file'];

				//File properties
				$file_name = $doc_file['name'];
				$file_tmp = $doc_file['tmp_name'];

				//File extension
				$file_ext = explode('.', $file_name);
				$file_ext = strtolower(end($file_ext));

				$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

				if (in_array($file_ext, $allowed)) {

					$doc_final = uniqid() . '.' . $file_ext;
					$file_destination = "files/employees/documents/" . $doc_final;

					if (move_uploaded_file($file_tmp, $file_destination)) {
					}
				}

				$doc_name = $_POST['doc_name'];
				$doc_desc = $_POST['doc_desc'];
				$doc_datetime = date('Y-m-d H:i:s');
				$doc_group = 1;
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');

				$query = $db->prepare("
					INSERT INTO idk_document
						(doc_name, doc_desc, doc_file, doc_icon, doc_datetime, doc_group, doc_created_by_id, created_at, updated_at)
					VALUES
						(:doc_name, :doc_desc, :doc_file, :doc_icon, :doc_datetime, :doc_group, :doc_created_by_id, :created_at, :updated_at)");

				$query->execute(array(
					':doc_name' => $doc_name,
					':doc_desc' => $doc_desc,
					':doc_file' => $doc_final,
					':doc_icon' => $file_ext,
					':doc_datetime' => $doc_datetime,
					':doc_group' => $doc_group,
					':created_at' => $created_at,
					':updated_at' => $updated_at,
					':doc_created_by_id' => $logged_employee_id
				));

				//Get last ID
				$doc_id = $db->lastInsertId();

				$query = $db->prepare("
					INSERT INTO idk_employee_document
						(employee_id, doc_id)
					VALUES
						(:employee_id, :doc_id)");

				$query->execute(array(
					':employee_id' => $employee_id,
					':doc_id' => $doc_id
				));

				$query_employee = $db->prepare("
					SELECT employee_first_name, employee_last_name
					FROM idk_employee
					WHERE employee_id = :employee_id");

				$query_employee->execute(array(
					':employee_id' => $employee_id
				));

				$employee = $query_employee->fetch();

				$employee_first_name = $employee['employee_first_name'];
				$employee_last_name = $employee['employee_last_name'];

				//Add to log
				$log_desc = "Dodao novi dokument za zaposlenika: " . $employee_first_name . " " . $employee_last_name . " - " . $doc_name . " ";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: employees?page=open&id=$employee_id&mess=2");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}
			break;



			/************************************************************
			 * 							SAVE EMPLOYEE IMPORTANT NOTE
			 * *********************************************************/
		case "save_employee_important_note":

			$employee_id = $_POST['employee_id'];

			if ($employee_id == $logged_employee_id or $getEmployeeStatus == 1) {

				$employee_important_note = base64_encode($_POST['employee_important_note']);

				//Get
				$query_select = $db->prepare("
					SELECT employee_first_name, employee_last_name
					FROM idk_employee
					WHERE employee_id = :employee_id");

				$query_select->execute(array(
					':employee_id' => $employee_id
				));

				$row_select = $query_select->fetch();

				$employee_first_name = $row_select['employee_first_name'];
				$employee_last_name = $row_select['employee_last_name'];

				//Save
				$query = $db->prepare("
					UPDATE idk_employee
					SET	employee_important_note = :employee_important_note
					WHERE employee_id = :employee_id");

				$query->execute(array(
					':employee_important_note' => $employee_important_note,
					':employee_id' => $employee_id
				));

				//Add to log
				$log_desc = "Snimio važne napomene za zaposlenika: " . $employee_first_name . " " . $employee_last_name . " ";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: employees?page=open&id=$employee_id&mess=5");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							ADD EMPLOYEE PHONE
			 * *********************************************************/
		case "add_employee_phone":

			$employee_id = $_POST['employee_id'];

			if ($employee_id == $logged_employee_id or $getEmployeeStatus == 1) {

				$ei_group = 1;
				$ei_title = $_POST['ei_title'];
				$ei_data = $_POST['ei_data'];
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');

				//Check if primary exists
				$check_query = $db->prepare("
					SELECT ei_id
					FROM idk_employee_info
					WHERE ei_group = :ei_group AND ei_primary = :ei_primary AND employee_id = :employee_id");

				$check_query->execute(array(
					':ei_group' => $ei_group,
					':employee_id' => $employee_id,
					':ei_primary' => 1
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {
					$ei_primary = 1; // if there is no primary make this one primary
				} else {
					$ei_primary = 0; // else don't make it primary
				}

				$query = $db->prepare("
					INSERT INTO idk_employee_info
						(ei_group, ei_title, ei_data, ei_primary, employee_id, created_at, updated_at)
					VALUES
						(:ei_group, :ei_title, :ei_data, :ei_primary, :employee_id, :created_at, :updated_at)");

				$query->execute(array(
					':ei_group' => $ei_group,
					':ei_title' => $ei_title,
					':ei_data' => $ei_data,
					':ei_primary' => $ei_primary,
					':created_at' => $created_at,
					':updated_at' => $updated_at,
					':employee_id' => $employee_id
				));

				header("Location: employees?page=open&id=$employee_id&mess=14");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							SET EMPLOYEE PRIMARY PHONE
			 * *********************************************************/
		case "set_primary_phone_employee":

			$employee_id = $_GET['employee_id'];

			if ($employee_id == $logged_employee_id or $getEmployeeStatus == 1) {

				$ei_id = $_GET['ei_id'];
				$updated_at = date('Y-m-d H:i:s');

				//Remove default primary phone
				$query = $db->prepare("
					UPDATE idk_employee_info
					SET	ei_primary = :ei_primary, updated_at = :updated_at
					WHERE employee_id = :employee_id AND ei_primary = :ei_primary_current AND ei_group = :ei_group");

				$query->execute(array(
					':ei_primary' => 0,
					':ei_group' => 1,
					':employee_id' => $employee_id,
					':updated_at' => $updated_at,
					':ei_primary_current' => 1
				));

				//Add primary phone
				$query = $db->prepare("
					UPDATE idk_employee_info
					SET	ei_primary = :ei_primary, updated_at = :updated_at
					WHERE ei_id = :ei_id");

				$query->execute(array(
					':ei_primary' => 1,
					':updated_at' => $updated_at,
					':ei_id' => $ei_id
				));

				header("Location: employees?page=open&id=$employee_id&mess=15");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							ADD EMPLOYEE EMAIL
			 * *********************************************************/
		case "add_employee_email":

			$employee_id = $_POST['employee_id'];

			if ($employee_id == $logged_employee_id or $getEmployeeStatus == 1) {

				$ei_group = 2;
				$ei_title = $_POST['ei_title'];
				$ei_data = $_POST['ei_data'];
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');

				//Check if primary exists
				$check_query = $db->prepare("
					SELECT ei_id
					FROM idk_employee_info
					WHERE ei_group = :ei_group AND ei_primary = :ei_primary AND employee_id = :employee_id");

				$check_query->execute(array(
					':ei_group' => $ei_group,
					':employee_id' => $employee_id,
					':ei_primary' => 1
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {
					$ei_primary = 1; // if there is no primary make this one primary
				} else {
					$ei_primary = 0; // else don't make it primary
				}

				$query = $db->prepare("
					INSERT INTO idk_employee_info
					(ei_group, ei_title, ei_data, ei_primary, employee_id, created_at, updated_at)
					VALUES
					(:ei_group, :ei_title, :ei_data, :ei_primary, :employee_id, :created_at, :updated_at)");

				$query->execute(array(
					':ei_group' => $ei_group,
					':ei_title' => $ei_title,
					':ei_data' => $ei_data,
					':ei_primary' => $ei_primary,
					':created_at' => $created_at,
					':updated_at' => $updated_at,
					':employee_id' => $employee_id
				));

				header("Location: employees?page=open&id=$employee_id&mess=17");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							SET EMPLOYEE PRIMARY EMAIL
			 * *********************************************************/
		case "set_primary_email_employee":

			$employee_id = $_GET['employee_id'];

			if ($employee_id == $logged_employee_id or $getEmployeeStatus == 1) {

				$ei_id = $_GET['ei_id'];
				$updated_at = date('Y-m-d H:i:s');

				//Remove default primary phone
				$query = $db->prepare("
					UPDATE idk_employee_info
					SET	ei_primary = :ei_primary, updated_at = :updated_at
					WHERE employee_id = :employee_id AND ei_primary = :ei_primary_current AND ei_group = :ei_group");

				$query->execute(array(
					':ei_primary' => 0,
					':ei_group' => 2,
					':employee_id' => $employee_id,
					':updated_at' => $updated_at,
					':ei_primary_current' => 1
				));

				//Add primary phone
				$query = $db->prepare("
					UPDATE idk_employee_info
					SET	ei_primary = :ei_primary, updated_at = :updated_at
					WHERE ei_id = :ei_id");

				$query->execute(array(
					':ei_primary' => 1,
					':updated_at' => $updated_at,
					':ei_id' => $ei_id
				));

				header("Location: employees?page=open&id=$employee_id&mess=18");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							ADD EMPLOYEE OTHER
			 * *********************************************************/
		case "add_employee_other":

			$employee_id = $_POST['employee_id'];

			if ($employee_id == $logged_employee_id or $getEmployeeStatus == 1) {

				$ei_group = 3;
				$ei_title = $_POST['ei_title'];
				$ei_data = $_POST['ei_data'];
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');

				//Check if primary exists
				$check_query = $db->prepare("
					SELECT ei_id
					FROM idk_employee_info
					WHERE ei_group = :ei_group AND ei_primary = :ei_primary");

				$check_query->execute(array(
					':ei_group' => $ei_group,
					':ei_primary' => 1
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {
					$ei_primary = 1; // if there is no primary make this one primary
				} else {
					$ei_primary = 0; // else don't make it primary
				}


				$query = $db->prepare("
					INSERT INTO idk_employee_info
					(ei_group, ei_title, ei_data, ei_primary, employee_id, created_at, updated_at)
					VALUES
					(:ei_group, :ei_title, :ei_data, :ei_primary, :employee_id, :created_at, :updated_at)");

				$query->execute(array(
					':ei_group' => $ei_group,
					':ei_title' => $ei_title,
					':ei_data' => $ei_data,
					':ei_primary' => $ei_primary,
					':created_at' => $created_at,
					':updated_at' => $updated_at,
					':employee_id' => $employee_id
				));

				header("Location: employees?page=open&id=$employee_id&mess=19");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							SET EMPLOYEE PRIMARY OTHER
			 * *********************************************************/
		case "set_primary_other_employee":

			$employee_id = $_GET['employee_id'];

			if ($employee_id == $logged_employee_id or $getEmployeeStatus == 1) {

				$ei_id = $_GET['ei_id'];
				$updated_at = date('Y-m-d H:i:s');

				//Remove default primary phone
				$query = $db->prepare("
					UPDATE idk_employee_info
					SET	ei_primary = :ei_primary, updated_at = :updated_at
					WHERE employee_id = :employee_id AND ei_primary = :ei_primary_current AND ei_group = :ei_group");

				$query->execute(array(
					':ei_primary' => 0,
					':ei_group' => 3,
					':employee_id' => $employee_id,
					':updated_at' => $updated_at,
					':ei_primary_current' => 1
				));

				//Add primary phone
				$query = $db->prepare("
					UPDATE idk_employee_info
					SET	ei_primary = :ei_primary, updated_at = :updated_at
					WHERE ei_id = :ei_id");

				$query->execute(array(
					':ei_primary' => 1,
					':updated_at' => $updated_at,
					':ei_id' => $ei_id
				));

				header("Location: employees?page=open&id=$employee_id&mess=20");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;

			/*-----------------------------------------------------------------------------------------
									EMPLOYEE END
-----------------------------------------------------------------------------------------*/



			/*-----------------------------------------------------------------------------------------
									CLIENT START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 							ADD NEW CLIENT
			 * *********************************************************/
		case "add_client":

			//Admin and commercialist can add client
			if ($getEmployeeStatus == 1 or $getEmployeeStatus == 2) {

				$client_name = $_POST['client_name'];
				$client_code = $_POST['client_code'];
				$client_username = $_POST['client_username'];
				$client_password = MD5($_POST['client_password']);
				$client_key = MD5(rand());

				//Check if client exists
				$check_query = $db->prepare("
					SELECT client_id
					FROM idk_client
					WHERE client_name = :client_name OR client_username = :client_username");

				$check_query->execute(array(
					':client_name' => $client_name,
					':client_username' => $client_username
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {

					$client_id_number = !empty($_POST['client_id_number']) ? $_POST['client_id_number'] : NULL;
					$client_business_type = !empty($_POST['client_business_type']) ? $_POST['client_business_type'] : NULL;
					$client_pdv_number = !empty($_POST['client_pdv_number']) ? $_POST['client_pdv_number'] : NULL;
					$client_bank_account = !empty($_POST['client_bank_account']) ? $_POST['client_bank_account'] : NULL;
					$client_responsible_person = !empty($_POST['client_responsible_person']) ? $_POST['client_responsible_person'] : NULL;
					$client_address = !empty($_POST['client_address']) ? $_POST['client_address'] : NULL;
					$client_postal_code = !empty($_POST['client_postal_code']) ? $_POST['client_postal_code'] : NULL;
					$client_city = !empty($_POST['client_city']) ? $_POST['client_city'] : NULL;
					$client_region = !empty($_POST['client_region']) ? $_POST['client_region'] : NULL;
					$client_country = !empty($_POST['client_country']) ? $_POST['client_country'] : NULL;
					$client_other_info = !empty($_POST['client_other_info']) ? $_POST['client_other_info'] : NULL;
					$client_type = !empty($_POST['client_type']) ? $_POST['client_type'] : NULL;
					$client_max_rabat = !empty($_POST['client_max_rabat']) ? $_POST['client_max_rabat'] : NULL;
					if (isset($_POST['client_show_price'])) {
						$client_show_price = $_POST['client_show_price'];
					} else {
						$client_show_price = 0;
					}
					if (isset($_POST['client_show_quantity'])) {
						$client_show_quantity = $_POST['client_show_quantity'];
					} else {
						$client_show_quantity = 0;
					}
					$client_added_by_id = $logged_employee_id;
					$created_at = date('Y-m-d H:i:s');
					$updated_at = date('Y-m-d H:i:s');

					//Upload and save client_image
					$client_image_final = uploadImage('client', 0, 0, 0);

					//Add client to db
					if (!empty($_POST['client_username']) and !empty($_POST['client_password'])) {
						$query_statement = "
							INSERT INTO idk_client
								(client_name, client_code, client_id_number, client_business_type, client_username, client_password, client_key, client_pdv_number, client_bank_account, client_responsible_person, client_postal_code, client_image, client_address, client_city, client_country, client_other_info, client_region, client_type, client_max_rabat, client_added_by_id, client_show_price, client_show_quantity, created_at, updated_at)
							VALUES
								(:client_name, :client_code, :client_id_number, :client_business_type, :client_username, :client_password, :client_key, :client_pdv_number, :client_bank_account, :client_responsible_person, :client_postal_code, :client_image, :client_address, :client_city, :client_country, :client_other_info, :client_region, :client_type, :client_max_rabat, :client_added_by_id, :client_show_price, :client_show_quantity, :created_at, :updated_at)";

						$query_array = [
							':client_name' => $client_name,
							':client_code' => $client_code,
							':client_id_number' => $client_id_number,
							':client_business_type' => $client_business_type,
							':client_username' => $client_username,
							':client_password' => $client_password,
							':client_key' => $client_key,
							':client_pdv_number' => $client_pdv_number,
							':client_bank_account' => $client_bank_account,
							':client_responsible_person' => $client_responsible_person,
							':client_postal_code' => $client_postal_code,
							':client_image' => $client_image_final,
							':client_address' => $client_address,
							':client_city' => $client_city,
							':client_country' => $client_country,
							':client_other_info' => $client_other_info,
							':client_region' => $client_region,
							':client_type' => $client_type,
							':client_max_rabat' => $client_max_rabat,
							':client_added_by_id' => $client_added_by_id,
							':client_show_price' => $client_show_price,
							':client_show_quantity' => $client_show_quantity,
							':created_at' => $created_at,
							':updated_at' => $updated_at
						];
					} else {
						$query_statement = "
							INSERT INTO idk_client
								(client_name, client_code, client_id_number, client_business_type, client_pdv_number, client_bank_account, client_responsible_person, client_postal_code, client_image, client_address, client_city, client_country, client_other_info, client_region, client_type, client_max_rabat, client_added_by_id, client_show_price, client_show_quantity, created_at, updated_at)
							VALUES
								(:client_name, :client_code, :client_id_number, :client_business_type, :client_pdv_number, :client_bank_account, :client_responsible_person, :client_postal_code, :client_image, :client_address, :client_city, :client_country, :client_other_info, :client_region, :client_type, :client_max_rabat, :client_added_by_id, :client_show_price, :client_show_quantity, :created_at, :updated_at)";

						$query_array = [
							':client_name' => $client_name,
							':client_code' => $client_code,
							':client_id_number' => $client_id_number,
							':client_business_type' => $client_business_type,
							':client_pdv_number' => $client_pdv_number,
							':client_bank_account' => $client_bank_account,
							':client_responsible_person' => $client_responsible_person,
							':client_postal_code' => $client_postal_code,
							':client_image' => $client_image_final,
							':client_address' => $client_address,
							':client_city' => $client_city,
							':client_country' => $client_country,
							':client_other_info' => $client_other_info,
							':client_region' => $client_region,
							':client_type' => $client_type,
							':client_max_rabat' => $client_max_rabat,
							':client_added_by_id' => $client_added_by_id,
							':client_show_price' => $client_show_price,
							':client_show_quantity' => $client_show_quantity,
							':created_at' => $created_at,
							':updated_at' => $updated_at
						];
					}

					$query = $db->prepare($query_statement);

					$query->execute($query_array);

					/* ADD CLIENT INFO */

					//Get last ID
					$client_id = $db->lastInsertId();

					if (!empty($_POST['client_phone'])) {

						//Add primary phone
						$ci_group = 1; //group=1 is for phone
						$ci_primary = 1;
						$ci_title = "Primarni";
						$ci_data = $_POST['client_phone'];


						$query_info = $db->prepare("
							INSERT INTO idk_client_info
								(ci_group, ci_title, ci_data, ci_primary, client_id, created_at, updated_at)
							VALUES
								(:ci_group, :ci_title, :ci_data, :ci_primary, :client_id, :created_at, :updated_at)");

						//add phone:
						$query_info->execute(array(
							':ci_group' => $ci_group,
							':ci_title' => $ci_title,
							':ci_data' => $ci_data,
							':ci_primary' => $ci_primary,
							':created_at' => $created_at,
							':updated_at' => $updated_at,
							':client_id' => $client_id
						));
					}

					if (!empty($_POST['client_email'])) {

						//Add primary email
						$ci_group = 2; //group=2 is for email
						$ci_primary = 1;
						$ci_title = "Primarni";
						$ci_data = $_POST['client_email'];

						$query_info = $db->prepare("
							INSERT INTO idk_client_info
								(ci_group, ci_title, ci_data, ci_primary, client_id, created_at, updated_at)
							VALUES
								(:ci_group, :ci_title, :ci_data, :ci_primary, :client_id, :created_at, :updated_at)");

						//add email:
						$query_info->execute(array(
							':ci_group' => $ci_group,
							':ci_title' => $ci_title,
							':ci_data' => $ci_data,
							':ci_primary' => $ci_primary,
							':created_at' => $created_at,
							':updated_at' => $updated_at,
							':client_id' => $client_id
						));
					}

					//Update clients statistics
					updateClientsStats();

					/* Add to log */
					$log_desc = "Dodao novog klijenta: " . $client_name . " ";
					$log_date = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $log_date);

					header("Location: clients?page=list&mess=1");
				} else {
					header("Location: clients?page=list&mess=2");
				}
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							EDIT CLIENT
			 * *********************************************************/
		case "edit_client":

			$client_id = $_POST['client_id'];

			if ($getEmployeeStatus == 1) {

				$client_name = !empty($_POST['client_name']) ? $_POST['client_name'] : NULL;
				$client_code = !empty($_POST['client_code']) ? $_POST['client_code'] : NULL;
				$client_id_number = !empty($_POST['client_id_number']) ? $_POST['client_id_number'] : NULL;
				$client_business_type = !empty($_POST['client_business_type']) ? $_POST['client_business_type'] : NULL;
				$client_pdv_number = !empty($_POST['client_pdv_number']) ? $_POST['client_pdv_number'] : NULL;
				$client_bank_account = !empty($_POST['client_bank_account']) ? $_POST['client_bank_account'] : NULL;
				$client_responsible_person = !empty($_POST['client_responsible_person']) ? $_POST['client_responsible_person'] : NULL;
				$client_address = !empty($_POST['client_address']) ? $_POST['client_address'] : NULL;
				$client_postal_code = !empty($_POST['client_postal_code']) ? $_POST['client_postal_code'] : NULL;
				$client_city = !empty($_POST['client_city']) ? $_POST['client_city'] : NULL;
				$client_region = !empty($_POST['client_region']) ? $_POST['client_region'] : NULL;
				$client_country = !empty($_POST['client_country']) ? $_POST['client_country'] : NULL;
				$client_other_info = !empty($_POST['client_other_info']) ? $_POST['client_other_info'] : NULL;
				$client_type = !empty($_POST['client_type']) ? $_POST['client_type'] : NULL;
				$client_max_rabat = !empty($_POST['client_max_rabat']) ? $_POST['client_max_rabat'] : NULL;
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');
				if (isset($_POST['client_show_price'])) {
					$client_show_price = $_POST['client_show_price'];
				} else {
					$client_show_price = 0;
				}
				if (isset($_POST['client_show_quantity'])) {
					$client_show_quantity = $_POST['client_show_quantity'];
				} else {
					$client_show_quantity = 0;
				}

				//Upload and save client_image
				$client_image_final = uploadImage('client', 1, $client_id, 0);

				//Add client to db
				if (!empty($_POST['client_username']) and !empty($_POST['client_password'])) {
					$client_username = $_POST['client_username'];
					$client_password = MD5($_POST['client_password']);
					$client_key = MD5(rand());
					$query_statement = "
						UPDATE idk_client
						SET client_name = :client_name, client_code = :client_code, client_id_number = :client_id_number, client_business_type = :client_business_type, client_username = :client_username, client_password = :client_password, client_key = :client_key, client_pdv_number = :client_pdv_number, client_bank_account = :client_bank_account, client_responsible_person = :client_responsible_person, client_postal_code = :client_postal_code, client_image = :client_image, client_address = :client_address, client_city = :client_city, client_country = :client_country, client_other_info = :client_other_info, client_region = :client_region, client_type = :client_type, client_max_rabat = :client_max_rabat, client_show_price = :client_show_price, client_show_quantity = :client_show_quantity, updated_at = :updated_at
						WHERE client_id = :client_id";

					$query_array = [
						':client_id' => $client_id,
						':client_name' => $client_name,
						':client_code' => $client_code,
						':client_id_number' => $client_id_number,
						':client_business_type' => $client_business_type,
						':client_username' => $client_username,
						':client_password' => $client_password,
						':client_key' => $client_key,
						':client_pdv_number' => $client_pdv_number,
						':client_bank_account' => $client_bank_account,
						':client_responsible_person' => $client_responsible_person,
						':client_postal_code' => $client_postal_code,
						':client_image' => $client_image_final,
						':client_address' => $client_address,
						':client_city' => $client_city,
						':client_country' => $client_country,
						':client_other_info' => $client_other_info,
						':client_region' => $client_region,
						':client_type' => $client_type,
						':client_max_rabat' => $client_max_rabat,
						':client_show_price' => $client_show_price,
						':client_show_quantity' => $client_show_quantity,
						':updated_at' => $updated_at
					];
				} else {
					$query_statement = "
						UPDATE idk_client
						SET client_name = :client_name, client_code = :client_code, client_id_number = :client_id_number, client_business_type = :client_business_type, client_pdv_number = :client_pdv_number, client_bank_account = :client_bank_account, client_responsible_person = :client_responsible_person, client_postal_code = :client_postal_code, client_image = :client_image, client_address = :client_address, client_city = :client_city, client_country = :client_country, client_other_info = :client_other_info, client_region = :client_region, client_type = :client_type, client_max_rabat = :client_max_rabat, client_show_price = :client_show_price, client_show_quantity = :client_show_quantity, updated_at = :updated_at
						WHERE client_id = :client_id";

					$query_array = [
						':client_id' => $client_id,
						':client_name' => $client_name,
						':client_code' => $client_code,
						':client_id_number' => $client_id_number,
						':client_business_type' => $client_business_type,
						':client_pdv_number' => $client_pdv_number,
						':client_bank_account' => $client_bank_account,
						':client_responsible_person' => $client_responsible_person,
						':client_postal_code' => $client_postal_code,
						':client_image' => $client_image_final,
						':client_address' => $client_address,
						':client_city' => $client_city,
						':client_country' => $client_country,
						':client_other_info' => $client_other_info,
						':client_region' => $client_region,
						':client_type' => $client_type,
						':client_max_rabat' => $client_max_rabat,
						':client_show_price' => $client_show_price,
						':client_show_quantity' => $client_show_quantity,
						':updated_at' => $updated_at
					];
				}

				$query = $db->prepare($query_statement);

				$query->execute($query_array);

				/* UPDATE CLIENT INFO */

				//Update primary phone
				$ci_group = 1; //group=1 is for phone
				$ci_primary = 1;
				$ci_data = $_POST['client_phone'];

				//Check if primary phone exists
				$check_phone_query = $db->prepare("
					SELECT ci_id
					FROM idk_client_info
					WHERE client_id = :client_id AND ci_group = :ci_group AND ci_primary = :ci_primary");

				$check_phone_query->execute(array(
					':ci_group' => $ci_group,
					':ci_primary' => $ci_primary,
					':client_id' => $client_id
				));

				$number_of_rows = $check_phone_query->rowCount();

				if ($number_of_rows == 0) {
					//Add primary phone
					$ci_title = "Primarni";

					$query_info = $db->prepare("
						INSERT INTO idk_client_info
							(ci_group, ci_title, ci_data, ci_primary, client_id, created_at, updated_at)
						VALUES
							(:ci_group, :ci_title, :ci_data, :ci_primary, :client_id, :created_at, :updated_at)");

					//add phone:
					$query_info->execute(array(
						':ci_group' => $ci_group,
						':ci_title' => $ci_title,
						':ci_data' => $ci_data,
						':ci_primary' => $ci_primary,
						':created_at' => $created_at,
						':updated_at' => $updated_at,
						':client_id' => $client_id
					));
				} else {
					$query_phone = $db->prepare("
						UPDATE idk_client_info
						SET ci_data = :ci_data, updated_at = :updated_at
						WHERE client_id = :client_id AND ci_group = :ci_group AND ci_primary = :ci_primary");

					$query_phone->execute(array(
						':ci_group' => $ci_group,
						':ci_data' => $ci_data,
						':ci_primary' => $ci_primary,
						':updated_at' => $updated_at,
						':client_id' => $client_id
					));
				}


				//Update primary email
				$ci_group = 2; //group=2 is for email
				$ci_primary = 1;
				$ci_data = $_POST['client_email'];

				//Check if primary phone exists
				$check_email_query = $db->prepare("
					SELECT ci_id
					FROM idk_client_info
					WHERE client_id = :client_id AND ci_group = :ci_group AND ci_primary = :ci_primary");

				$check_email_query->execute(array(
					':ci_group' => $ci_group,
					':ci_primary' => $ci_primary,
					':client_id' => $client_id
				));

				$number_of_rows = $check_email_query->rowCount();

				if ($number_of_rows == 0) {
					$ci_title = "Primarni";

					$query_info = $db->prepare("
						INSERT INTO idk_client_info
							(ci_group, ci_title, ci_data, ci_primary, client_id, created_at, updated_at)
						VALUES
							(:ci_group, :ci_title, :ci_data, :ci_primary, :client_id, :created_at, :updated_at)");

					//add email:
					$query_info->execute(array(
						':ci_group' => $ci_group,
						':ci_title' => $ci_title,
						':ci_data' => $ci_data,
						':ci_primary' => $ci_primary,
						':created_at' => $created_at,
						':updated_at' => $updated_at,
						':client_id' => $client_id
					));
				} else {
					$query_email = $db->prepare("
						UPDATE idk_client_info
						SET ci_data = :ci_data, updated_at = :updated_at
						WHERE client_id = :client_id AND ci_group = :ci_group AND ci_primary = :ci_primary");

					$query_email->execute(array(
						':ci_group' => $ci_group,
						':ci_data' => $ci_data,
						':ci_primary' => $ci_primary,
						':updated_at' => $updated_at,
						':client_id' => $client_id
					));
				}

				/* Add to log */
				$log_desc = "Uredio profil klijenta: " . $client_name . " ";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: clients?page=list&mess=3");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							ADD CLIENT NOTE
			 * *********************************************************/
		case "add_client_note":

			$client_id = $_POST['client_id'];

			if ($getEmployeeStatus == 1) {

				$note_txt = $_POST['note_txt'];
				$note_datetime = date('Y-m-d H:i:s');
				$note_group = 1;
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');

				$query = $db->prepare("
					INSERT INTO idk_note
						(note_txt, note_datetime, note_group, client_id, note_created_by_id, created_at, updated_at)
					VALUES
						(:note_txt, :note_datetime, :note_group, :client_id, :note_created_by_id, :created_at, :updated_at)");

				$query->execute(array(
					':note_txt' => $note_txt,
					':note_datetime' => $note_datetime,
					':note_group' => $note_group,
					':client_id' => $client_id,
					':created_at' => $created_at,
					':updated_at' => $updated_at,
					':note_created_by_id' => $logged_employee_id
				));

				$query_client = $db->prepare("
					SELECT client_name
					FROM idk_client
					WHERE client_id = :client_id");

				$query_client->execute(array(
					':client_id' => $client_id
				));

				$client = $query_client->fetch();

				$client_name = $client['client_name'];

				//Add to log
				$log_desc = "Dodao bilješku za klijenta: " . $client_name . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: clients?page=open&id=$client_id&mess=1");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							ADD CLIENT DOCUMENT
			 * *********************************************************/
		case "add_client_doc":

			$client_id = $_POST['client_id'];

			if ($getEmployeeStatus == 1) {

				//Upload document
				$doc_file = $_FILES['doc_file'];

				//File properties
				$file_name = $doc_file['name'];
				$file_tmp = $doc_file['tmp_name'];

				//File extension
				$file_ext = explode('.', $file_name);
				$file_ext = strtolower(end($file_ext));

				$allowed = array('jpg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png');

				if (in_array($file_ext, $allowed)) {

					$doc_final = uniqid() . '.' . $file_ext;
					$file_destination = "./files/clients/documents/" . $doc_final;

					if (move_uploaded_file($file_tmp, $file_destination)) {
					}
				}

				$doc_name = $_POST['doc_name'];
				$doc_desc = $_POST['doc_desc'];
				$doc_datetime = date('Y-m-d H:i:s');
				$doc_group = 1;
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');

				$query = $db->prepare("
					INSERT INTO idk_document
						(doc_name, doc_desc, doc_file, doc_icon, doc_datetime, doc_group, doc_created_by_id, created_at, updated_at)
					VALUES
						(:doc_name, :doc_desc, :doc_file, :doc_icon, :doc_datetime, :doc_group, :doc_created_by_id, :created_at, :updated_at)");

				$query->execute(array(
					':doc_name' => $doc_name,
					':doc_desc' => $doc_desc,
					':doc_file' => $doc_final,
					':doc_icon' => $file_ext,
					':doc_datetime' => $doc_datetime,
					':doc_group' => $doc_group,
					':created_at' => $created_at,
					':updated_at' => $updated_at,
					':doc_created_by_id' => $logged_employee_id
				));

				//Get last ID
				$doc_id = $db->lastInsertId();

				$query = $db->prepare("
					INSERT INTO idk_client_document
						(client_id, doc_id)
					VALUES
						(:client_id, :doc_id)");

				$query->execute(array(
					':client_id' => $client_id,
					':doc_id' => $doc_id
				));

				$query_client = $db->prepare("
					SELECT client_name
					FROM idk_client
					WHERE client_id = :client_id");

				$query_client->execute(array(
					':client_id' => $client_id
				));

				$client = $query_client->fetch();

				$client_name = $client['client_name'];

				//Add to log
				$log_desc = "Dodao novi dokument za klijenta: " . $client_name . " - " . $doc_name . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: clients?page=open&id=$client_id&mess=2");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}
			break;



			/************************************************************
			 * 							SAVE CLIENT IMPORTANT NOTE
			 * *********************************************************/
		case "save_client_important_note":

			$client_id = $_POST['client_id'];

			if ($getEmployeeStatus == 1) {

				$client_important_note = base64_encode($_POST['client_important_note']);

				//Get
				$query_select = $db->prepare("
					SELECT client_name
					FROM idk_client
					WHERE client_id = :client_id");

				$query_select->execute(array(
					':client_id' => $client_id
				));

				$row_select = $query_select->fetch();

				$client_name = $row_select['client_name'];

				//Save
				$query = $db->prepare("
					UPDATE idk_client
					SET	client_important_note = :client_important_note
					WHERE client_id = :client_id");

				$query->execute(array(
					':client_important_note' => $client_important_note,
					':client_id' => $client_id
				));

				//Add to log
				$log_desc = "Snimio važne napomene za klijenta: " . $client_name . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: clients?page=open&id=$client_id&mess=5");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							ADD CLIENT PHONE
			 * *********************************************************/
		case "add_client_phone":

			$client_id = $_POST['client_id'];

			if ($getEmployeeStatus == 1) {

				$ci_group = 1;
				$ci_title = $_POST['ci_title'];
				$ci_data = $_POST['ci_data'];
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');

				//Check if primary exists
				$check_query = $db->prepare("
					SELECT ci_id
					FROM idk_client_info
					WHERE ci_group = :ci_group AND ci_primary = :ci_primary");

				$check_query->execute(array(
					':ci_group' => $ci_group,
					':ci_primary' => 1
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {
					$ci_primary = 1; // if there is no primary make this one primary
				} else {
					$ci_primary = 0; // else don't make it primary
				}

				$query = $db->prepare("
					INSERT INTO idk_client_info
						(ci_group, ci_title, ci_data, ci_primary, client_id, created_at, updated_at)
					VALUES
						(:ci_group, :ci_title, :ci_data, :ci_primary, :client_id, :created_at, :updated_at)");

				$query->execute(array(
					':ci_group' => $ci_group,
					':ci_title' => $ci_title,
					':ci_data' => $ci_data,
					':ci_primary' => $ci_primary,
					':created_at' => $created_at,
					':updated_at' => $updated_at,
					':client_id' => $client_id
				));

				header("Location: clients?page=open&id=$client_id&mess=14");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							SET CLIENT PRIMARY PHONE
			 * *********************************************************/
		case "set_primary_phone_client":

			$client_id = $_GET['client_id'];

			if ($getEmployeeStatus == 1) {

				$ci_id = $_GET['ci_id'];
				$updated_at = date('Y-m-d H:i:s');

				//Remove default primary phone
				$query = $db->prepare("
					UPDATE idk_client_info
					SET	ci_primary = :ci_primary, updated_at = :updated_at
					WHERE client_id = :client_id AND ci_primary = :ci_primary_current AND ci_group = :ci_group");

				$query->execute(array(
					':ci_primary' => 0,
					':ci_group' => 1,
					':client_id' => $client_id,
					':updated_at' => $updated_at,
					':ci_primary_current' => 1
				));

				//Add primary phone
				$query = $db->prepare("
					UPDATE idk_client_info
					SET	ci_primary = :ci_primary, updated_at = :updated_at
					WHERE ci_id = :ci_id");

				$query->execute(array(
					':ci_primary' => 1,
					':updated_at' => $updated_at,
					':ci_id' => $ci_id
				));

				header("Location: clients?page=open&id=$client_id&mess=15");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							ADD CLIENT EMAIL
			 * *********************************************************/
		case "add_client_email":

			$client_id = $_POST['client_id'];

			if ($getEmployeeStatus == 1) {

				$ci_group = 2;
				$ci_title = $_POST['ci_title'];
				$ci_data = $_POST['ci_data'];
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');

				//Check if primary exists
				$check_query = $db->prepare("
					SELECT ci_id
					FROM idk_client_info
					WHERE ci_group = :ci_group AND ci_primary = :ci_primary");

				$check_query->execute(array(
					':ci_group' => $ci_group,
					':ci_primary' => 1
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {
					$ci_primary = 1; // if there is no primary make this one primary
				} else {
					$ci_primary = 0; // else don't make it primary
				}


				$query = $db->prepare("
					INSERT INTO idk_client_info
						(ci_group, ci_title, ci_data, ci_primary, client_id, created_at, updated_at)
					VALUES
						(:ci_group, :ci_title, :ci_data, :ci_primary, :client_id, :created_at, :updated_at)");

				$query->execute(array(
					':ci_group' => $ci_group,
					':ci_title' => $ci_title,
					':ci_data' => $ci_data,
					':ci_primary' => $ci_primary,
					':created_at' => $created_at,
					':updated_at' => $updated_at,
					':client_id' => $client_id
				));

				header("Location: clients?page=open&id=$client_id&mess=14");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							SET CLIENT PRIMARY EMAIL
			 * *********************************************************/
		case "set_primary_email_client":

			$client_id = $_GET['client_id'];

			if ($getEmployeeStatus == 1) {

				$ci_id = $_GET['ci_id'];
				$updated_at = date('Y-m-d H:i:s');

				//Remove default primary phone
				$query = $db->prepare("
					UPDATE idk_client_info
					SET	ci_primary = :ci_primary, updated_at = :updated_at
					WHERE client_id = :client_id AND ci_primary = :ci_primary_current AND ci_group = :ci_group");

				$query->execute(array(
					':ci_primary' => 0,
					':ci_group' => 2,
					':client_id' => $client_id,
					':updated_at' => $updated_at,
					':ci_primary_current' => 1
				));

				//Add primary phone
				$query = $db->prepare("
					UPDATE idk_client_info
					SET	ci_primary = :ci_primary, updated_at = :updated_at
					WHERE ci_id = :ci_id");

				$query->execute(array(
					':ci_primary' => 1,
					':updated_at' => $updated_at,
					':ci_id' => $ci_id
				));

				header("Location: clients?page=open&id=$client_id&mess=15");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							ADD CLIENT OTHER
			 * *********************************************************/
		case "add_client_other":

			$client_id = $_POST['client_id'];

			if ($getEmployeeStatus == 1) {

				$ci_group = 3;
				$ci_title = $_POST['ci_title'];
				$ci_data = $_POST['ci_data'];
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');

				//Check if primary exists
				$check_query = $db->prepare("
					SELECT ci_id
					FROM idk_client_info
					WHERE ci_group = :ci_group AND ci_primary = :ci_primary");

				$check_query->execute(array(
					':ci_group' => $ci_group,
					':ci_primary' => 1
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {
					$ci_primary = 1; // if there is no primary make this one primary
				} else {
					$ci_primary = 0; // else don't make it primary
				}


				$query = $db->prepare("
					INSERT INTO idk_client_info
						(ci_group, ci_title, ci_data, ci_primary, client_id, created_at, updated_at)
					VALUES
						(:ci_group, :ci_title, :ci_data, :ci_primary, :client_id, :created_at, :updated_at)");

				$query->execute(array(
					':ci_group' => $ci_group,
					':ci_title' => $ci_title,
					':ci_data' => $ci_data,
					':ci_primary' => $ci_primary,
					':created_at' => $created_at,
					':updated_at' => $updated_at,
					':client_id' => $client_id
				));

				header("Location: clients?page=open&id=$client_id&mess=14");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							SET CLIENT PRIMARY OTHER
			 * *********************************************************/
		case "set_primary_other_client":

			$client_id = $_GET['client_id'];

			if ($getEmployeeStatus == 1) {

				$ci_id = $_GET['ci_id'];
				$updated_at = date('Y-m-d H:i:s');

				//Remove default primary phone
				$query = $db->prepare("
					UPDATE idk_client_info
					SET	ci_primary = :ci_primary, updated_at = :updated_at
					WHERE client_id = :client_id AND ci_primary = :ci_primary_current AND ci_group = :ci_group");

				$query->execute(array(
					':ci_primary' => 0,
					':ci_group' => 3,
					':client_id' => $client_id,
					':updated_at' => $updated_at,
					':ci_primary_current' => 1
				));

				//Add primary phone
				$query = $db->prepare("
					UPDATE idk_client_info
					SET	ci_primary = :ci_primary, updated_at = :updated_at
					WHERE ci_id = :ci_id");

				$query->execute(array(
					':ci_primary' => 1,
					':updated_at' => $updated_at,
					':ci_id' => $ci_id
				));

				header("Location: clients?page=open&id=$client_id&mess=15");
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 							IMPORT JSON CLIENTS
			 * *********************************************************/
		case "import_json_clients":

			$date = date('d_m_Y_H_i_s');
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');
			$ci_group = 1; //group=1 is for phone
			$ci_primary = 1;
			$ci_title = "Primarni";

			//Upload document
			$json_file = $_FILES['json_file'];

			//File properties
			$file_name = $json_file['name'];
			$file_tmp = $json_file['tmp_name'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('json');

			if (in_array($file_ext, $allowed)) {

				$json_final = explode('.', $file_name)[0] . '_' . $date . '.' . $file_ext;
				$file_destination = "./files/json/" . $json_final;

				if (move_uploaded_file($file_tmp, $file_destination)) {
				}
			}

			//Get json file and modify content(delete '<' and '>')
			$json = file_get_contents($file_destination);
			$json_formatted = str_replace('<', '', $json);
			$json_formatted = str_replace('>', '', $json_formatted);
			$data = json_decode($json_formatted);

			if (isset($data) and gettype($data) == "object") {

				$kupci = $data->kupci;
				$poslovneJedinice = $data->poslovneJedinice;
				$mjesta = $data->mjesta;

				try {
					foreach ($kupci as $kupac) {

						//Add clients to db
						$query_kupac = $db->prepare("
								INSERT INTO idk_client
									(client_id_number, client_code, client_country, client_name, client_address, client_postal_code, client_image, client_business_type, created_at, updated_at)
								VALUES
									(:client_id_number, :client_code, :client_country, :client_name, :client_address, :client_postal_code, :client_image, :client_business_type, :created_at, :updated_at)");

						$query_kupac->execute(array(
							':client_id_number' => $kupac->idBroj,
							':client_code' => $kupac->internaSifra,
							':client_country' => "Bosna i Hercegovina",
							':client_name' => $kupac->naziv,
							':client_address' => $kupac->adresa,
							':client_postal_code' => $kupac->mjestoId,
							':client_image' => "none.jpg",
							':client_business_type' => 2,
							':created_at' => $created_at,
							':updated_at' => $updated_at
						));

						$client_id = $db->lastInsertId();

						//Add primary phone
						if ($kupac->telefon != "") {
							$ci_data = $kupac->telefon;

							$query_info = $db->prepare("
									INSERT INTO idk_client_info
										(ci_group, ci_title, ci_data, ci_primary, client_id, created_at, updated_at)
									VALUES
										(:ci_group, :ci_title, :ci_data, :ci_primary, :client_id, :created_at, :updated_at)");

							//add phone:
							$query_info->execute(array(
								':ci_group' => $ci_group,
								':ci_title' => $ci_title,
								':ci_data' => $ci_data,
								':ci_primary' => $ci_primary,
								':created_at' => $created_at,
								':updated_at' => $updated_at,
								':client_id' => $client_id
							));
						}
					}

					foreach ($poslovneJedinice as $poslovnaJedinica) {

						//Add poslovne jedinice to db
						$query_poslovne_jedinice = $db->prepare("
								INSERT INTO idk_client
									(client_code, client_address, client_postal_code, client_image, client_business_type, created_at, updated_at)
								VALUES
									(:client_code, :client_address, :client_postal_code, :client_image, :client_business_type, :created_at, :updated_at)");

						$query_poslovne_jedinice->execute(array(
							':client_code' => $poslovnaJedinica->kupacId,
							':client_address' => $poslovnaJedinica->adresa,
							':client_postal_code' => $poslovnaJedinica->mjestoId,
							':client_image' => "none.jpg",
							':client_business_type' => 2,
							':created_at' => $created_at,
							':updated_at' => $updated_at
						));

						$client_id = $db->lastInsertId();

						//Get client_name for main client
						$query_get_client_name = $db->prepare("
									SELECT client_name
									FROM idk_client
									WHERE client_code = :client_code");

						$query_get_client_name->execute(array(
							':client_code' => $poslovnaJedinica->kupacId
						));

						$number_of_rows = $query_get_client_name->rowCount();

						if ($number_of_rows !== 0) {

							$row_get_client_name = $query_get_client_name->fetch();

							$client_name = $row_get_client_name['client_name'];

							//Update client_name with main client_name and poslovnaJedinica->naziv
							$query_update_client_name = $db->prepare("
									UPDATE idk_client
									SET client_name = :client_name
									WHERE client_id = :client_id");

							$query_update_client_name->execute(array(
								':client_id' => $client_id,
								':client_name' => $client_name . " - " . $poslovnaJedinica->naziv
							));
						} else {

							//Update client_name with only poslovnaJedinica->naziv
							$query_update_client_name = $db->prepare("
									UPDATE idk_client
									SET client_name = :client_name
									WHERE client_id = :client_id");

							$query_update_client_name->execute(array(
								':client_id' => $client_id,
								':client_name' => $poslovnaJedinica->naziv
							));
						}
					}

					foreach ($mjesta as $mjesto) {

						//Update client_city for every added client
						$query_update_client_city = $db->prepare("
									UPDATE idk_client
									SET client_city = :client_city
									WHERE client_postal_code = :client_postal_code");

						$query_update_client_city->execute(array(
							':client_postal_code' => $mjesto->id,
							':client_city' => $mjesto->naziv
						));

						//Add cities to idk_location, will keep duplicates if exist
						$query_add_cities = $db->prepare("
								INSERT INTO idk_location
									(location_type, location_name)
								VALUES
									(:location_type, :location_name)");

						$query_add_cities->execute(array(
							':location_type' => 1,
							':location_name' => $mjesto->naziv
						));
					}
				} catch (Exception $e) {
					echo $e->getMessage();
				}

				//Add to log
				$log_desc = "Importovao json datoteku: " . $file_name . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: clients?page=list&mess=8");
			} else {
				header("Location: clients?page=list&mess=9");
			}

			break;



			/************************************************************
			 * 							IMPORT CSV CLIENTS
			 * *********************************************************/
		case "import_csv_clients":

			$date = date('d_m_Y_H_i_s');
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');
			$data_array = [];

			//Upload document
			$csv_file = $_FILES['csv_file'];

			//File properties
			$file_name = $csv_file['name'];
			$file_tmp = $csv_file['tmp_name'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('csv');

			if (in_array($file_ext, $allowed)) {

				$csv_final = explode('.', $file_name)[0] . '_' . $date . '.' . $file_ext;
				$file_destination = "./files/csv/" . $csv_final;

				if (move_uploaded_file($file_tmp, $file_destination)) {
					// Open the file for reading
					if (($h = fopen("./files/csv/" . $csv_final, "r")) !== FALSE) {
						// Each line in the file is converted into an individual array that we call $data
						// The items of the array are comma separated
						while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
							// Each individual array is being pushed into the nested array
							$data_array[] = $data;
						}

						// Close the file
						fclose($h);

						// Add clients to db
						try {
							foreach ($data_array as $row) {
								$check_client = $db->prepare("
									SELECT client_id
									FROM idk_client
									WHERE client_name = :client_name AND client_city = :client_city");

								$check_client->execute(array(
									':client_name' => $row[0],
									':client_city' => $row[1]
								));

								$number_of_rows = $check_client->rowCount();

								if ($number_of_rows == 0) {

									$query_client = $db->prepare("
									INSERT INTO idk_client
										(client_name, client_business_type, client_city, client_image, created_at, updated_at)
									VALUES
										(:client_name, :client_business_type, :client_city, :client_image, :created_at, :updated_at)");

									$query_client->execute(array(
										':client_name' => $row[0],
										':client_city' => $row[1],
										':client_business_type' => 2,
										':client_image' => 'none.jpg',
										':created_at' => $created_at,
										':updated_at' => $updated_at
									));
								}
							}

							//Add to log
							$log_desc = "Importovao CSV datoteku: " . $file_name . "";
							$log_date = date('Y-m-d H:i:s');
							addLog($logged_employee_id, $log_desc, $log_date);

							header("Location: clients?page=list&mess=10");
						} catch (Exception $e) {
							echo $e->getMessage();
							header("Location: clients?page=list&mess=11");
						}
					}
				} else {

					header("Location: clients?page=list&mess=11");
				}
			} else {

				header("Location: clients?page=list&mess=11");
			}

			break;



			/************************************************************
			 * 							ADD CLIENT OFFICE
			 * *********************************************************/
		case "add_client_office":

			$client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : NULL;
			$client_office_id = !empty($_POST['client_office_id']) ? $_POST['client_office_id'] : NULL;

			if (isset($client_id) and isset($client_office_id)) {

				// Add client_id as parent of client_office_id
				$query = $db->prepare("
					UPDATE idk_client
					SET client_parent = :client_parent
					WHERE client_id = :client_id");

				$query->execute(array(
					':client_parent' => $client_id,
					':client_id' => $client_office_id
				));

				//Add to log
				$log_desc = "Dodao poslovnicu ID: " . $client_office_id . " za klijenta ID: " . $client_id . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header('Location: clients?page=open&id=' . $client_id . '&mess=22');
			} else {
				header('Location: clients?page=open&id=' . $client_id . '&mess=24');
			}

			break;



			/************************************************************
			 * 							DELETE CLIENT OFFICE
			 * *********************************************************/
		case "delete_client_office":

			$client_id = !empty($_GET['client_id']) ? $_GET['client_id'] : NULL;
			$client_office_id = !empty($_GET['client_office_id']) ? $_GET['client_office_id'] : NULL;

			if (isset($client_id) and isset($client_office_id)) {

				// Remove client_id as parent of client_office_id
				$query = $db->prepare("
					UPDATE idk_client
					SET client_parent = NULL
					WHERE client_id = :client_id");

				$query->execute(array(
					':client_id' => $client_office_id
				));

				//Add to log
				$log_desc = "Obrisao poslovnicu ID: " . $client_office_id . " za klijenta ID: " . $client_id . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header('Location: clients?page=open&id=' . $client_id . '&mess=23');
			} else {
				header('Location: clients?page=open&id=' . $client_id . '&mess=24');
			}

			break;


			/*-----------------------------------------------------------------------------------------
									CLIENT END
-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
									CATEGORY START
-----------------------------------------------------------------------------------------*/
			/************************************************************
			 * 							ADD CATEGORY
			 * *********************************************************/
		case "add_category":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['category_name'])) {

					$category_name = $_POST['category_name'];
					$category_sub = $_POST['category_sub'];
					$created_at = date('Y-m-d H:i:s');
					$updated_at = date('Y-m-d H:i:s');

					//Upload and save category_image
					$category_image_final = uploadImage('category', 0, 0, 0);

					$query = $db->prepare("
						INSERT INTO idk_category
							(category_name, category_sub, category_image, created_at, updated_at)
						VALUES
							(:category_name, :category_sub, :category_image, :created_at, :updated_at)");

					$query->execute(array(
						':category_name' => $category_name,
						':category_sub' => $category_sub,
						':category_image' => $category_image_final,
						':created_at' => $created_at,
						':updated_at' => $updated_at
					));

					//Add to log
					$log_desc = "Dodao kategoriju: " . $category_name . "";
					$log_date = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $log_date);

					header("Location: categories?page=list&mess=1");
				} else {
					header("Location: categories?page=list&mess=2");
				}
			}

			break;



			/************************************************************
			 * 							EDIT CATEGORY
			 * *********************************************************/
		case "edit_category":

			$category_id = $_POST['category_id'];

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['category_name'])) {

					$category_name = $_POST['category_name'];
					$category_sub = $_POST['category_sub'];
					$updated_at = date('Y-m-d H:i:s');

					//Upload and save category_image
					$category_image_final = uploadImage('category', 1, $category_id, 0);

					$query = $db->prepare("
						UPDATE idk_category
						SET	category_name = :category_name, category_image = :category_image, category_sub = :category_sub, updated_at = :updated_at
						WHERE category_id = :category_id");

					$query->execute(array(
						':category_name' => $category_name,
						':category_image' => $category_image_final,
						':category_sub' => $category_sub,
						':updated_at' => $updated_at,
						':category_id' => $category_id
					));

					//Add to log
					$log_desc = "Uredio kategoriju: " . $category_name . "";
					$log_date = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $log_date);

					header("Location: categories?page=list&mess=4");
				} else {
					header("Location: categories?page=list&mess=2");
				}
			}

			break;

			/*-----------------------------------------------------------------------------------------
									CATEGORY END
-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
									PRODUCT START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 							ADD PRODUCT
			 * *********************************************************/
		case "add_product":

			if ($getEmployeeStatus == 1) {

				if (isset($_POST['table_page'])) {
					$table_page = $_POST['table_page'];
				} else {
					$table_page = 0;
				}

				if (!empty($_POST['product_name']) and !empty($_POST['product_api_id'])) {

					$product_name = $_POST['product_name'];
					$product_api_id = $_POST['product_api_id'];

					//Check if product exists
					$check_query = $db->prepare("
						SELECT product_id
						FROM idk_product
						WHERE product_api_id = :product_api_id");

					$check_query->execute(array(
						':product_api_id' => $product_api_id
					));

					$number_of_rows = $check_query->rowCount();

					if ($number_of_rows == 0) {

						$product_price = $_POST['product_price'];
						$product_categories = $_POST['product_categories']; // Array
						$product_desc = $_POST['product_desc'];
						$product_currency = $_POST['product_currency'];
						$product_tax = explode(",", $_POST['product_tax']);
						$product_tax_name = $product_tax[0];
						$product_tax_percentage = $product_tax[1];
						$product_unit = isset($_POST['product_unit']) ? $_POST['product_unit'] : NULL;
						$product_supplier = isset($_POST['product_supplier']) ? $_POST['product_supplier'] : NULL;
						$product_sku = isset($_POST['product_sku']) ? $_POST['product_sku'] : NULL;
						$created_at = date('Y-m-d H:i:s');
						$updated_at = date('Y-m-d H:i:s');
						if (isset($_POST['product_quantity'])) {
							$product_quantity = $_POST['product_quantity'];
						} else {
							$product_quantity = 0;
						}
						if (isset($_POST['product_featured'])) {
							$product_featured = $_POST['product_featured'];
						} else {
							$product_featured = 0;
						}

						//Upload and save product_image
						$product_image_final = uploadImage('product', 0, 0, 0);

						$query = $db->prepare("
							INSERT INTO idk_product
								(product_name, product_api_id, product_price, product_quantity, product_active, product_image, product_desc, product_currency, product_tax_name, product_tax_percentage, product_featured, product_unit, product_supplier, product_sku, created_at, updated_at)
							VALUES
								(:product_name, :product_api_id, :product_price, :product_quantity, :product_active, :product_image, :product_desc, :product_currency, :product_tax_name, :product_tax_percentage, :product_featured, :product_unit, :product_supplier, :product_sku, :created_at, :updated_at)");

						$query->execute(array(
							':product_api_id' => $product_api_id,
							':product_name' => $product_name,
							':product_price' => $product_price,
							':product_quantity' => $product_quantity,
							':product_active' => 1,
							':product_image' => $product_image_final,
							':product_desc' => $product_desc,
							':product_currency' => $product_currency,
							':product_tax_name' => $product_tax_name,
							':product_tax_percentage' => $product_tax_percentage,
							':product_featured' => $product_featured,
							':product_unit' => $product_unit,
							':product_supplier' => $product_supplier,
							':product_sku' => $product_sku,
							':created_at' => $created_at,
							':updated_at' => $updated_at
						));


						/* ADD PRODUCT CATEGORY */
						$product_id = $db->lastInsertId();

						foreach ($product_categories as $product_category) {
							$query_category = $db->prepare("
							INSERT INTO idk_product_category
								(product_id, category_id)
							VALUES
								(:product_id, :category_id)");

							$query_category->execute(array(
								':product_id' => $product_id,
								':category_id' => $product_category
							));
						}

						//Add to log
						$log_desc = "Dodao proizvod: " . $product_name . "";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: products?page=list&mess=1&table_page=${table_page}");
					} else {
						header("Location: products?page=list&mess=2&table_page=${table_page}");
					}
				} else {
					header("Location: products?page=list&mess=7&table_page=${table_page}");
				}
			}

			break;



			/************************************************************
			 * 							EDIT PRODUCT
			 * *********************************************************/
		case "edit_product":

			$product_id = !empty($_POST['product_id']) ? $_POST['product_id'] : NULL;
			if (isset($_POST['table_page'])) {
				$table_page = $_POST['table_page'];
			} else {
				$table_page = 0;
			}

			if ($getEmployeeStatus == 1) {

				if (isset($product_id) and !empty($_POST['product_name']) and !empty($_POST['product_api_id'])) {

					$product_api_id = $_POST['product_api_id'];
					$product_name = $_POST['product_name'];

					//Check if product exists
					$check_query = $db->prepare("
						SELECT product_id
						FROM idk_product
						WHERE product_api_id = :product_api_id AND product_id != :product_id");

					$check_query->execute(array(
						':product_api_id' => $product_api_id,
						':product_id' => $product_id
					));

					$number_of_rows = $check_query->rowCount();

					if ($number_of_rows == 0) {

						$product_price = $_POST['product_price'];
						$product_categories = $_POST['product_categories']; // Array
						$product_desc = $_POST['product_desc'];
						$product_currency = $_POST['product_currency'];
						$product_unit = isset($_POST['product_unit']) ? $_POST['product_unit'] : NULL;
						$product_tax = explode(",", $_POST['product_tax']);
						$product_tax_name = $product_tax[0];
						$product_tax_percentage = $product_tax[1];
						$product_supplier = isset($_POST['product_supplier']) ? $_POST['product_supplier'] : NULL;
						$product_sku = isset($_POST['product_sku']) ? $_POST['product_sku'] : NULL;
						$updated_at = date('Y-m-d H:i:s');
						if (isset($_POST['product_quantity'])) {
							$product_quantity = $_POST['product_quantity'];
						} else {
							$product_quantity = 0;
						}
						if (isset($_POST['product_featured'])) {
							$product_featured = $_POST['product_featured'];
						} else {
							$product_featured = 0;
						}

						//Upload and save product_image
						$product_image_final = uploadImage('product', 1, $product_id, 0);

						$query = $db->prepare("
							UPDATE idk_product
							SET product_name = :product_name, product_api_id = :product_api_id, product_price = :product_price, product_quantity = :product_quantity, product_active = :product_active, product_image = :product_image, product_desc = :product_desc, product_currency = :product_currency, product_tax_name = :product_tax_name, product_tax_percentage = :product_tax_percentage, product_featured = :product_featured, product_unit = :product_unit, product_supplier = :product_supplier, product_sku = :product_sku, updated_at = :updated_at
							WHERE product_id = :product_id");

						$query->execute(array(
							':product_id' => $product_id,
							':product_api_id' => $product_api_id,
							':product_name' => $product_name,
							':product_price' => $product_price,
							':product_quantity' => $product_quantity,
							':product_active' => 1,
							':product_image' => $product_image_final,
							':product_desc' => $product_desc,
							':product_currency' => $product_currency,
							':product_tax_name' => $product_tax_name,
							':product_tax_percentage' => $product_tax_percentage,
							':product_featured' => $product_featured,
							':product_unit' => $product_unit,
							':product_supplier' => $product_supplier,
							':product_sku' => $product_sku,
							':updated_at' => $updated_at
						));


						/* ADD PRODUCT CATEGORY */
						// delete old product_categories from idk_product_category
						$product_cat_del_query = $db->prepare("
							DELETE FROM idk_product_category
							WHERE product_id = :product_id");

						$product_cat_del_query->execute(array(
							':product_id' => $product_id
						));

						// add new product_categories
						foreach ($product_categories as $product_category) {
							$query_category = $db->prepare("
								INSERT INTO idk_product_category
									(product_id, category_id)
								VALUES
									(:product_id, :category_id)");

							$query_category->execute(array(
								':product_id' => $product_id,
								':category_id' => $product_category
							));
						}

						//Add to log
						$log_desc = "Uredio proizvod: " . $product_name . "";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: products?page=open&id=${product_id}&mess=1");
					} else {
						header("Location: products?page=list&mess=11&table_page=${table_page}");
					}
				} else {
					header("Location: products?page=list&mess=7&table_page=${table_page}");
				}
			}

			break;

			/*-----------------------------------------------------------------------------------------
									PRODUCT END
-----------------------------------------------------------------------------------------*/


			/*-----------------------------------------------------------------------------------------
									IMPORT START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 							IMPORT JSON
			 * *********************************************************/
		case "import_json":

			$date = date('d_m_Y_H_i_s');

			//Upload document
			$json_file = $_FILES['json_file'];

			//File properties
			$file_name = $json_file['name'];
			$file_tmp = $json_file['tmp_name'];

			//File extension
			$file_ext = explode('.', $file_name);
			$file_ext = strtolower(end($file_ext));

			$allowed = array('json');

			if (in_array($file_ext, $allowed)) {

				$json_final = explode('.', $file_name)[0] . '_' . $date . '.' . $file_ext;
				$file_destination = "./files/json/" . $json_final;

				if (move_uploaded_file($file_tmp, $file_destination)) {
				}
			}

			$json = file_get_contents($file_destination);
			$json_formatted = preg_replace('/"naziv":"[\s\S]+?",/', '', $json);
			$json_formatted = preg_replace('/"jedinicaMjere":"[\s\S]+?",/', '', $json_formatted);
			$json_formatted = preg_replace('/"opisPakovanja":"[\s\S]+?",/', '', $json_formatted);
			$json_formatted = preg_replace('/"kataloskiBroj":"[\s\S]+?",/', '', $json_formatted);
			// $json_formatted = preg_replace('/"cijena":[\s\S]+?,/', '', $json_formatted);
			// $json_formatted = preg_replace('/,"kategorijaArtiklaId":"[\s\S]+?",/', '}', $json_formatted);
			$data = json_decode($json_formatted);

			if (isset($data) and gettype($data) == "object") {

				$artikli = $data->artikli;

				try {
					foreach ($artikli as $artikl) {

						if ($artikl->barcode == "") {
							$query_artikl = $db->prepare("
							UPDATE idk_product
							SET product_quantity = " . $artikl->stanje . " WHERE product_sku = " . $artikl->id);
						} else {
							$query_artikl = $db->prepare("
							UPDATE idk_product
							SET product_quantity = " . $artikl->stanje . ", product_barcode = " . $artikl->barcode . " WHERE product_sku = " . $artikl->id);
						}

						$query_artikl->execute();
					}
				} catch (Exception $e) {
					echo $e->getMessage();
				}

				//Add to log
				$log_desc = "Importovao json datoteku: " . $file_name . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: products?page=list&mess=8");
			} else {
				header("Location: products?page=list&mess=9");
			}

			break;
//GET ONLINE ORDERS

case "get_online_orders":
	
	$username = 'ck_3b6e9f33f410d9543ad06933d91e00f42ecccad4'; // Add your own Consumer Key here 
	$password = 'cs_b3d425031868daf338bd6ca999b5ca50c7e4d4d5'; // Add your own Consumer Secret here
	$url = 'https://vzv.ba/wp-json/wc/v3/orders';

	$per_page = 100;

	$ch = curl_init();
	$headers = array(
		'Accept: application/json',
		'Content-Type: application/json',
		);
	
	curl_setopt($ch, CURLOPT_URL, $url.'?consumer_key='.$username.'&consumer_secret='.$password);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_POSTFIELDS, ['per_page' => '100']);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch);
	$orders = json_decode($result);

	
	$i = 1;
	foreach($orders as $order){
		$order_id = $order['id'];
		echo $i.'order id: '.$order_id.'</br>';
		$i++;
	}
	
	exit();

break;


			/************************************************************
			 * 						IMPORT JSON FROM URL - PRODUCTS
			 * *********************************************************/
		case "import_json_url_products":

			//Sending http GET request to Matrica
			$url = 'http://82.118.0.88:5000/api/artikli';
			$data = array();

			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'GET',
					'content' => http_build_query($data)
				)
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);

			$products = json_decode($result);
			$products_api_ids_array = array();

			foreach ($products as $product) {
				array_push($products_api_ids_array, $product->acIdent);
			}

			// Delete product from db if it is not on the API
			$delete_query = $db->prepare("
				SELECT product_id, product_api_id
				FROM idk_product");

			$delete_query->execute();

			while ($product_row = $delete_query->fetch()) {
				$product_id = $product_row['product_id'];
				$product_api_id = $product_row['product_api_id'];

				if (!in_array($product_api_id, $products_api_ids_array) or !isset($product_api_id) or $product_api_id == '') {
					// Delete product from db
					$delete_product_query = $db->prepare("
						DELETE
						FROM idk_product
						WHERE product_id = :product_id");

					$delete_product_query->execute(array(
						':product_id' => $product_id
					));
				}
			}

			foreach ($products as $product) {
				$product_api_id = $product->acIdent;
				$product_name = $product->acName;
				$product_price = $product->VPC;
				$product_unit = 'kom';
				$product_currency = 'KM';
				$product_tax_name = 'PDV';
				$product_tax_percentage = 17;
				$product_quantity = 1;
				$product_image = "none.jpg";

				if ($product_api_id != '' and $product_api_id != '0' and $product_name != '' and $product_price != '' and $product_price != '0') {
					// Check if product exists in db
					$check_query = $db->prepare("
						SELECT product_id
						FROM idk_product
						WHERE product_api_id = :product_api_id");

					$check_query->execute(array(
						':product_api_id' => $product_api_id
					));

					$number_of_rows = $check_query->rowCount();

					if ($number_of_rows == 0) {

						// Insert new product in db
						$product_query = $db->prepare("
							INSERT INTO idk_product
								(product_api_id, product_name, product_price, product_unit, product_currency, product_tax_name, product_tax_percentage, product_quantity, product_image)
							VALUES
								(:product_api_id, :product_name, :product_price, :product_unit, :product_currency, :product_tax_name, :product_tax_percentage, :product_quantity, :product_image)");

						$product_query->execute(array(
							':product_api_id' => $product_api_id,
							':product_name' => $product_name,
							':product_price' => $product_price,
							':product_unit' => $product_unit,
							':product_currency' => $product_currency,
							':product_tax_name' => $product_tax_name,
							':product_tax_percentage' => $product_tax_percentage,
							':product_quantity' => $product_quantity,
							':product_image' => $product_image
						));

						$product_id = $db->lastInsertId();

						// Delete product_category from db
						$product_category_query = $db->prepare("
							DELETE
							FROM idk_product_category
							WHERE product_id = :product_id");

						$product_category_query->execute(array(
							':product_id' => $product_id
						));

						// Insert new product_category in db
						$product_category_query = $db->prepare("
							INSERT INTO idk_product_category
								(product_id, category_id)
							VALUES
								(:product_id, :category_id)");

						$product_category_query->execute(array(
							':product_id' => $product_id,
							':category_id' => 1
						));
					} else {

						// Update product in db
						$product_query = $db->prepare("
							UPDATE idk_product
							SET product_name = :product_name, product_price = :product_price, product_active = :product_active
							WHERE product_api_id = :product_api_id");

						$product_query->execute(array(
							':product_api_id' => $product_api_id,
							':product_name' => $product_name,
							':product_price' => $product_price,
							':product_active' => 1
						));
					}
				}
			}

			//Sending http GET request to Matrica
			$url = 'http://82.118.0.88:5000/api/zalihe';
			$data = array();

			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'GET',
					'content' => http_build_query($data)
				)
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);

			$products = json_decode($result);

			foreach ($products as $product) {
				$product_api_id = $product->acIdent;
				$product_quantity = $product->anStock;

				if ($product_api_id != '' and $product_api_id != '0') {
					// Update product in db
					$product_query = $db->prepare("
						UPDATE idk_product
						SET product_quantity = :product_quantity
						WHERE product_api_id = :product_api_id");

					$product_query->execute(array(
						':product_api_id' => $product_api_id,
						':product_quantity' => $product_quantity
					));
				}
			}

			echo 'Success';

			header('Location: products?mess=10');

			break;



			/************************************************************
			 * 						IMPORT JSON FROM URL - CLIENTS
			 * *********************************************************/
		case "import_json_url_clients":

			//Sending http GET request to Matrica
			$url = 'http://82.118.0.88:5000/api/partneri';
			$data = array();

			$options = array(
				'http' => array(
					'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
					'method'  => 'GET',
					'content' => http_build_query($data)
				)
			);
			$context  = stream_context_create($options);
			$result = file_get_contents($url, false, $context);

			$clients = json_decode($result);

			foreach ($clients as $client) {
				$client_name = $client->acSubject;
				$client_name_2 = $client->acName2;
				$client_business_type = 2;
				$client_image = "none.jpg";
				$client_added_by_id = 1;

				if ($client_name != '' and $client_name_2 != '') {
					// Check if client exists in db
					$check_query = $db->prepare("
						SELECT client_id
						FROM idk_client
						WHERE client_name = :client_name");

					$check_query->execute(array(
						':client_name' => $client_name
					));

					$number_of_rows = $check_query->rowCount();

					if ($number_of_rows == 0) {

						// Insert new client in db
						$client_query = $db->prepare("
							INSERT INTO idk_client
								(client_name, client_api_id, client_business_type, client_added_by_id, client_image)
							VALUES
								(:client_name, :client_api_id, :client_business_type, :client_added_by_id, :client_image)");

						$client_query->execute(array(
							':client_name' => $client_name,
							':client_api_id' => $client_name,
							':client_business_type' => $client_business_type,
							':client_added_by_id' => $client_added_by_id,
							':client_image' => $client_image
						));
					} else {

						// Update client in db
						$client_query = $db->prepare("
							UPDATE idk_client
							SET client_api_id = :client_api_id
							WHERE client_name = :client_name");

						$client_query->execute(array(
							':client_name' => $client_name,
							':client_api_id' => $client_name
						));
					}
				}
			}

			header('Location: clients?mess=12');

			break;



			/************************************************************
			 * 						UPDATE PRODUCT CATEGORIES - UNCATEGORIZED
			 * *********************************************************/
		case "update_product_categories_uncategorized":

			// Delete product_category from db
			$product_category_delete_query = $db->prepare("
				DELETE
				FROM idk_product_category");

			$product_category_delete_query->execute();

			// Check if product exists in db
			$check_query = $db->prepare("
				SELECT product_id
				FROM idk_product");

			$check_query->execute();

			$number_of_rows = $check_query->rowCount();

			if ($number_of_rows != 0) {

				while ($product = $check_query->fetch()) {

					$product_id = $product['product_id'];

					// Insert new product_category in db
					$product_category_query = $db->prepare("
						INSERT INTO idk_product_category
							(product_id, category_id)
						VALUES
							(:product_id, :category_id)");

					$product_category_query->execute(array(
						':product_id' => $product_id,
						':category_id' => 1
					));
				}
			}

			header('Location: products?page=list');

			break;

			/************************************************************
			 * 							CREATE ORDER FROM OFFER
			 * *********************************************************/
		case "create_order_from_offer":

			$offer_id = $_POST['offer_id'];
			$offer_pay_method = $_POST['offer_pay_method'];
			$order_status = 1;

			if (isset($offer_id) and isset($offer_pay_method)) {

				//Get data from offer
				$offer_query = $db->prepare("
					SELECT *
					FROM idk_offer
					WHERE offer_id = :offer_id AND offer_status != 0");

				$offer_query->execute(array(
					':offer_id' => $offer_id
				));

				$offer = $offer_query->fetch();

				//Get data from product offer
				$product_offer_query = $db->prepare("
					SELECT *
					FROM idk_product_offer
					WHERE offer_id = :offer_id");

				$product_offer_query->execute(array(
					':offer_id' => $offer_id
				));

				$product_get_out_of_stock = 0;

				while ($product_offer = $product_offer_query->fetch()) {
					//Get data from product
					$product_get_query = $db->prepare("
						SELECT product_quantity
						FROM idk_product
						WHERE product_id = :product_id");

					$product_get_query->execute(array(
						':product_id' => $product_offer['product_id']
					));

					$product_get = $product_get_query->fetch();

					if ($product_get['product_quantity'] <= 0) {
						$product_get_out_of_stock++;
					}
				}

				//Check if product that is out of stock has been ordered
				if ($product_get_out_of_stock > 0) {
					$order_status = 5;
				}

				//Insert new row in order
				$order_query = $db->prepare("
					INSERT INTO idk_order
						(client_id, order_note, employee_id, order_status, order_total_price, order_total_tax, order_total_rabat, order_to_pay, order_key, order_type, order_pay_method, created_at, updated_at)
					VALUES
						(:client_id, :order_note, :employee_id, :order_status, :order_total_price, :order_total_tax, :order_total_rabat, :order_to_pay, :order_key, :order_type, :order_pay_method, :created_at, :updated_at)");

				$order_query->execute(array(
					':client_id' => $offer['client_id'],
					':order_note' => $offer['offer_note'],
					':employee_id' => $offer['employee_id'],
					':order_total_price' => $offer['offer_total_price'],
					':order_total_tax' => $offer['offer_total_tax'],
					':order_total_rabat' => $offer['offer_total_rabat'],
					':order_to_pay' => $offer['offer_to_pay'],
					':order_key' => $offer['offer_key'],
					':order_type' => $offer['offer_type'],
					':order_status' => $order_status,
					':order_pay_method' => $offer_pay_method,
					':created_at' => $offer['created_at'],
					':updated_at' => $offer['updated_at']
				));

				$order_id = $db->lastInsertId();

				//Get client name
				$client_id = $offer['client_id'];

				$client_query = $db->prepare("
				SELECT client_name
				FROM idk_client
				WHERE client_id = :client_id");

				$client_query->execute(array(
					':client_id' => $client_id
				));

				$client = $client_query->fetch();
				$client_name = $client['client_name'];

				//Get data from product offer
				$product_offer_query = $db->prepare("
				SELECT *
				FROM idk_product_offer
				WHERE offer_id = :offer_id");

				$product_offer_query->execute(array(
					':offer_id' => $offer_id
				));

				while ($product_offer = $product_offer_query->fetch()) {
					//Insert new row in product order
					$product_order_query = $db->prepare("
					INSERT INTO idk_product_order
						(order_id, product_id, product_name, product_currency, product_quantity, product_unit, product_price, product_tax_name, product_tax_percentage, product_tax_value, product_rabat_percentage, product_rabat_value, product_in_stock, product_quantity_in_db)
					VALUES
						(:order_id, :product_id, :product_name, :product_currency, :product_quantity, :product_unit, :product_price, :product_tax_name, :product_tax_percentage, :product_tax_value, :product_rabat_percentage, :product_rabat_value, :product_in_stock, :product_quantity_in_db)");

					$product_order_query->execute(array(
						':order_id' => $order_id,
						':product_id' => $product_offer['product_id'],
						':product_name' => $product_offer['product_name'],
						':product_currency' => $product_offer['product_currency'],
						':product_quantity' => $product_offer['product_quantity'],
						':product_unit' => $product_offer['product_unit'],
						':product_price' => $product_offer['product_price'],
						':product_tax_name' => $product_offer['product_tax_name'],
						':product_tax_percentage' => $product_offer['product_tax_percentage'],
						':product_tax_value' => $product_offer['product_tax_value'],
						':product_rabat_percentage' => $product_offer['product_rabat_percentage'],
						':product_rabat_value' => $product_offer['product_rabat_value'],
						':product_in_stock' => $product_offer['product_in_stock'],
						':product_quantity_in_db' => $product_offer['product_quantity_in_db']
					));

					//Update quantity in idk_product
					$update_product_quantity_query = $db->prepare("
						UPDATE idk_product
						SET	product_quantity = product_quantity - :temp_product_quantity
						WHERE product_id = :product_id");

					$update_product_quantity_query->execute(array(
						':temp_product_id' => $product_offer['product_id'],
						':product_quantity' => $product_offer['product_quantity']
					));
				}

				//Get employee name
				$select_employee_query = $db->prepare("
					SELECT employee_first_name, employee_last_name
					FROM idk_employee
					WHERE employee_id = :employee_id");

				$select_employee_query->execute(array(
					':employee_id' => $offer['employee_id']
				));

				$employee = $select_employee_query->fetch();

				$employee_first_name = $employee['employee_first_name'];
				$employee_last_name = $employee['employee_last_name'];
				$employee_first_name_initial = substr($employee_first_name, 0, 1);
				$employee_last_name_initial = substr($employee_last_name, 0, 1);

				//Add notification to all administrators

				//Get client_email
				$select_client_email_query = $db->prepare("
					SELECT ci_data
					FROM idk_client_info
					WHERE ci_group = :ci_group AND ci_primary = :ci_primary AND client_id = :client_id");

				$select_client_email_query->execute(array(
					':ci_group' => 2,
					':client_id' => $client_id,
					':ci_primary' => 1
				));

				$number_of_rows_email = $select_client_email_query->rowCount();

				if ($number_of_rows_email !== 0) {

					$select_client_email = $select_client_email_query->fetch();
					$client_email = $select_client_email['ci_data'];

					//Send confirmation e-mail with order
					$mail_email = $client_email;
					$mail_name = $client_name;
					$mail_subject = "Unaviva d.o.o. B2B - Potvrda narudžbe";
					$mail_body = "Poštovani,<br><br>Obavještavamo Vas da je Vaša narudžba uspješno zaprimljena.<br>Narudžbu možete isprintati na sljedećem <a href='" . getSiteUrlr() . "idkadmin/print_order?id=" . $order_id . "&order=" . $offer['offer_key'] . "'>linku</a>.<br><br>Hvala na povjerenju,<br>Vaša Unaviva d.o.o.";
					$mail_altbody = "Poštovani,<br><br>Obavještavamo Vas da je Vaša narudžba uspješno zaprimljena.<br>Narudžbu možete isprintati na sljedećem <a href='" . getSiteUrlr() . "idkadmin/print_order?id=" . $order_id . "&order=" . $offer['offer_key'] . "'>linku</a>.<br><br>Hvala na povjerenju,<br>Vaša Unaviva d.o.o.";

					sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
				}

				//Get administrators
				$select_employees_query = $db->prepare("
					SELECT employee_id
					FROM idk_employee
					WHERE employee_status = :employee_status");

				$select_employees_query->execute(array(
					':employee_status' => 1
				));

				while ($select_employees = $select_employees_query->fetch()) {

					$notification_employeeid = $select_employees['employee_id'];
					$notification_datetime = date('Y-m-d H:i:s');
					$notification_title = "Nova narudžba<br>Klijent: ${client_name}<br>Komercijalista: ${employee_first_name_initial}. ${employee_last_name_initial}.";
					$notification_icon = "shopping-cart";
					$notification_link = "" . getSiteUrlr() . "idkadmin/orders?page=open&order_id=${order_id}";
					$notification_type = 3;

					addNotification($notification_title, $notification_icon, $notification_link, $notification_employeeid, $notification_type);
				}

				//Update report
				$report_id = NULL;
				$order_id = $order_id;
				$report_start_time = NULL;
				$report_end_time = date('Y-m-d H:i:s');
				addReport(
					$report_id,
					$logged_employee_id,
					$client_id,
					$order_id,
					$report_start_time,
					$report_end_time
				);


				updateOrdersStats();

				//Archive offer
				$query = $db->prepare("
				UPDATE idk_offer
				SET offer_status = 0
				WHERE offer_id = :offer_id");

				$query->execute(array(
					':offer_id' => $offer_id
				));

				header("Location: offers?page=list&mess=7");
			} else {
				header("Location: index");
			}

			break;



			/************************************************************
			 * 							ORDER FISCALIZATION
			 * *********************************************************/
		case "order_fiscalization":

			$order_id = $_GET['id'];


			if (isset($order_id)) {

				//Get data from client
				$client_query = $db->prepare("
				SELECT t1.client_name
				FROM idk_client t1
				INNER JOIN idk_order t2
				ON t1.client_id = t2.client_id
				WHERE t2.order_id = :order_id");

				$client_query->execute(array(
					':order_id' => $order_id
				));

				$client = $client_query->fetch();
				$client_name = $client['client_name'];

				//Get data from product_order
				$product_order_query = $db->prepare("
					SELECT t1.*, t2.*
					FROM idk_order t1
					INNER JOIN idk_product_order t2
					ON t1.order_id = t2.order_id
					WHERE t1.order_id = :order_id");

				$product_order_query->execute(array(
					':order_id' => $order_id
				));

				$order_query = $db->prepare("

					SELECT *
					FROM idk_order
					WHERE order_id = :order_id");

				$order_query->execute(array(
					':order_id' => $order_id
				));

				$order = $order_query->fetch();
				$orderTotalPrice = number_format(($order['order_total_price']), 3, '.', '');
				$orderToPay = number_format(($order['order_to_pay']), 3, '.', '');
				$orderTotalTax = number_format(($order['order_total_tax']), 3, '.', '');
				$orderTotalRabat = number_format(($order['order_total_rabat']), 3, '.', '');
				$orderNote = isset($order['order_note']) ? $order['order_note'] : 'N';
				$orderClient = $client_name;
				$orderPayMethod = 2;
				$orderPayMethodLabel = 'VIR';


				$data = array(
					'orderTotalPrice' => $orderTotalPrice,
					'orderToPay' => $orderToPay,
					'orderTotalTax' => $orderTotalTax,
					'orderTotalRabat' => $orderTotalRabat,
					'orderNote' => $orderNote,
					'orderClient' => $orderClient,
					'orderPayMethod' => $orderPayMethod,
					'orderPayMethodLabel' => $orderPayMethodLabel,
					'externalOrderID' => $order_id
				);

				$url = 'http://82.118.0.88:5000/api/narudzba';

				$options = array(
					'http' => array(
						'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
						'method'  => 'POST',
						'content' => http_build_query($data)
					)
				);
				$context  = stream_context_create($options);
				$result = file_get_contents($url, false, $context);
				$_result = json_decode($result, true);
				$acKey = $_result["acKey"];

				$anNo = 1;
				while ($product_order = $product_order_query->fetch()) {

					// $order_pay_method = $product_order['order_pay_method'];
					$product_id = $product_order['product_id'];
					$product_price = $product_order['product_price'];
					$product_rabat_percentage = $product_order['product_rabat_percentage'];
					$product_quantity = $product_order['product_quantity'];
					$product_name = $product_order['product_name'];
					$product_unit = $product_order['product_unit'];

					$product_api_id_query = $db->prepare("
						SELECT product_api_id
						FROM idk_product
						WHERE product_id = :product_id");

					$product_api_id_query->execute(array(
						':product_id' => $product_id
					));

					$product_api_id_row = $product_api_id_query->fetch();
					$product_api_id = $product_api_id_row['product_api_id'];

					//Sending http POST request to Matrica
					$url = 'http://82.118.0.88:5000/api/narudzbaitem';

					$acIdent = $product_api_id;
					$anQty = $product_quantity;
					$acExternalOrderID = $order_id;
					$anPrice = $product_price;
					$anRebate = isset($product_rabat_percentage) ? $product_rabat_percentage : 0.00;
					$acUM = $product_unit;

					$data = array(
						'acKey' => $acKey,
						'anNo' => $anNo,
						'acIdent' => $acIdent,
						'acName' => $product_name,
						'anQty' => $anQty,
						'anPrice' => $anPrice,
						'anRebate' => $anRebate,
						'acUM' => $acUM
					);


					$options = array(
						'http' => array(
							'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
							'method'  => 'POST',
							'content' => http_build_query($data)
						)
					);
					$context  = stream_context_create($options);
					$result = file_get_contents($url, false, $context);
					$anNo++;
				}

				$update_order_query = $db->prepare("
					UPDATE idk_order
					SET order_fiscalized = :order_fiscalized
					WHERE order_id = :order_id");

				$update_order_query->execute(array(
					':order_id' => $order_id,
					':order_fiscalized' => 1
				));

				header("Location: orders?page=open&order_id=${order_id}&mess=3");
			} else {
				header("Location: orders?page=open&order_id=${order_id}&mess=4");
			}

			break;

			/************************************************************
			 * 							OFFER CHANGE CLIENT
			 * *********************************************************/
		case "offer_change_client":

			$offer_id = !empty($_POST['offer_id']) ? $_POST['offer_id'] : NULL;
			$client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : NULL;

			if (isset($offer_id) and isset($client_id)) {

				//Update order client
				$client_query = $db->prepare("
					UPDATE idk_offer
					SET client_id = :client_id
					WHERE offer_id = :offer_id");

				$client_query->execute(array(
					':client_id' => $client_id,
					':offer_id' => $offer_id
				));


				//Add to log
				$log_desc = "Uredio ponudu #${offer_id}";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: offers?page=edit&offer_id=${offer_id}&mess=1");
			} elseif (isset($offer_id)) {
				header("Location: offers?page=edit&offer_id=${offer_id}&mess=2");
			} else {
				header("Location: offers?page=list&mess=2");
			}

			break;

			/************************************************************
			 * 							OFFER ADD PRODUCT
			 * *********************************************************/
		case "offer_add_product":

			$offer_id = !empty($_POST['offer_id']) ? $_POST['offer_id'] : NULL;
			$product_id = !empty($_POST['product_id']) ? $_POST['product_id'] : NULL;
			$product_quantity = !empty($_POST['product_quantity']) ? $_POST['product_quantity'] : NULL;
			$product_rabat_percentage = !empty($_POST['product_rabat_percentage']) ? $_POST['product_rabat_percentage'] : NULL;
			$client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : NULL;

			if (isset($client_id) and isset($offer_id) and isset($product_id) and isset($product_quantity) and isset($product_rabat_percentage)) {

				//Get max rabat for client
				$check_client_query = $db->prepare("
						SELECT client_max_rabat
						FROM idk_client
						WHERE client_id = :client_id");

				$check_client_query->execute(array(
					':client_id' => $client_id
				));

				$number_of_rows = $check_client_query->rowCount();

				if ($number_of_rows == 1) {
					$client_max_rabat_row = $check_client_query->fetch();
					$client_max_rabat = isset($client_max_rabat_row['client_max_rabat']) ? floatval($client_max_rabat_row['client_max_rabat']) : NULL;
				} else {
					$client_max_rabat = NULL;
				}

				if (isset($client_max_rabat) and $client_max_rabat < $product_rabat_percentage) {
					$product_rabat_percentage = $client_max_rabat;
				}

				//Check if product order exists
				$check_query = $db->prepare("
						SELECT product_id
						FROM idk_product_offer
						WHERE product_id = :product_id AND offer_id = :offer_id");

				$check_query->execute(array(
					':product_id' => $product_id,
					':offer_id' => $offer_id
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {

					//Get product info
					$product_query = $db->prepare("
							SELECT *
							FROM idk_product
							WHERE product_id = :product_id");

					$product_query->execute(array(
						':product_id' => $product_id
					));

					$product = $product_query->fetch();

					$product_name = $product['product_name'];
					$product_currency = $product['product_currency'];
					$product_unit = $product['product_unit'];
					$product_price = $product['product_price'];
					$product_tax_name = $product['product_tax_name'];
					$product_tax_percentage = $product['product_tax_percentage'];
					$product_quantity_in_db = $product['product_quantity'];
					$product_rabat_value = $product_price * $product_rabat_percentage / 100;
					$product_tax_value = ($product_price - $product_rabat_value) * $product_tax_percentage / 100;
					$product_in_stock = 1;


					$order_status = NULL;

					//Add product to order
					$query = $db->prepare("
							INSERT INTO idk_product_offer
								(offer_id, product_id, product_name, product_currency, product_quantity, product_unit, product_price, product_tax_name, product_tax_percentage, product_tax_value, product_rabat_percentage, product_rabat_value, product_in_stock, product_quantity_in_db)
							VALUES
								(:offer_id, :product_id, :product_name, :product_currency, :product_quantity, :product_unit, :product_price, :product_tax_name, :product_tax_percentage, :product_tax_value, :product_rabat_percentage, :product_rabat_value, :product_in_stock, :product_quantity_in_db)");

					$query->execute(array(
						':offer_id' => $offer_id,
						':product_id' => $product_id,
						':product_name' => $product_name,
						':product_currency' => $product_currency,
						':product_quantity' => $product_quantity,
						':product_unit' => $product_unit,
						':product_price' => $product_price,
						':product_tax_name' => $product_tax_name,
						':product_tax_percentage' => $product_tax_percentage,
						':product_tax_value' => $product_tax_value,
						':product_rabat_percentage' => $product_rabat_percentage,
						':product_rabat_value' => $product_rabat_value,
						':product_in_stock' => $product_in_stock,
						':product_quantity_in_db' => $product_quantity_in_db
					));


					//Get product order info
					$product_offer_query = $db->prepare("
							SELECT *
							FROM idk_product_offer
							WHERE offer_id = :offer_id");

					$product_offer_query->execute(array(
						':offer_id' => $offer_id
					));

					$offer_total_price = 0.000;
					$offer_total_tax = 0.000;
					$offer_total_rabat = 0.000;
					$offer_to_pay = 0.000;

					while ($product_offer = $product_offer_query->fetch()) {

						$product_price = $product_offer['product_price'];
						$product_quantity = $product_offer['product_quantity'];
						$product_tax_percentage = $product_offer['product_tax_percentage'];
						$product_rabat_percentage = $product_offer['product_rabat_percentage'];
						$product_rabat_value = $product_price * $product_rabat_percentage / 100;
						$product_tax_value = ($product_price - $product_rabat_value) * $product_tax_percentage / 100;

						//Calculate product to pay again
						// $product_price = $product_price - ($product_price * $product_rabat_percentage / 100); //Calculate price with rabat
						$product_total_price = $product_price * $product_quantity; //Price without rabat
						$product_total_tax = $product_tax_value * $product_quantity;
						$product_total_rabat = $product_rabat_value * $product_quantity; //Calculate total rabat value
						$product_to_pay = $product_total_price + $product_total_tax - $product_total_rabat;

						$offer_total_price += $product_total_price;
						$offer_total_tax += $product_total_tax;
						$offer_total_rabat += $product_total_rabat;
						$offer_to_pay += $product_to_pay;
						$offer_to_pay = round($offer_to_pay * 2, 1) / 2;
					}


					//Update total price and tax of order
					$update_total_price_tax_query = $db->prepare("
							UPDATE idk_offer
							SET	offer_total_price = :offer_total_price, offer_total_tax = :offer_total_tax, offer_total_rabat = :offer_total_rabat, offer_to_pay = :offer_to_pay
							WHERE offer_id = :offer_id");

					$update_total_price_tax_query->execute(array(
						':offer_id' => $offer_id,
						':offer_total_price' => $offer_total_price,
						':offer_total_tax' => $offer_total_tax,
						':offer_total_rabat' => $offer_total_rabat,
						':offer_to_pay' => $offer_to_pay
					));


					//Add to log
					$log_desc = "Uredio narudžbu #${order_id}";
					$log_date = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $log_date);

					header("Location: offers?page=edit&offer_id=${offer_id}&mess=3");
				}
			}
			break;


			/************************************************************
			 * 							ORDER CHANGE CLIENT
			 * *********************************************************/
		case "order_change_client":

			$order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : NULL;
			$client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : NULL;

			if (isset($order_id) and isset($client_id)) {

				//Update order client
				$client_query = $db->prepare("
					UPDATE idk_order
					SET client_id = :client_id
					WHERE order_id = :order_id");

				$client_query->execute(array(
					':client_id' => $client_id,
					':order_id' => $order_id
				));

				//Add to log
				$log_desc = "Uredio narudžbu #${order_id}";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: orders?page=edit&order_id=${order_id}&mess=1");
			} elseif (isset($order_id)) {
				header("Location: orders?page=edit&order_id=${order_id}&mess=2");
			} else {
				header("Location: orders?page=list&mess=2");
			}

			break;


		case "duplicate_order":

			$order_id = $_POST['order_id'];
			$order_status = 1;

			if (isset($order_id)) {

				//Get data from offer
				$order_query = $db->prepare("
						SELECT *
						FROM idk_order
						WHERE order_id = :order_id");

				$order_query->execute(array(
					':order_id' => $order_id
				));

				$order = $order_query->fetch();

				//Get data from product offer
				$product_order_query = $db->prepare("
						SELECT *
						FROM idk_product_order
						WHERE order_id = :order_id");

				$product_order_query->execute(array(
					':order_id' => $order_id
				));

				$product_get_out_of_stock = 0;

				while ($product_order = $product_order_query->fetch()) {
					//Get data from product
					$product_get_query = $db->prepare("
							SELECT product_quantity
							FROM idk_product
							WHERE product_id = :product_id");

					$product_get_query->execute(array(
						':product_id' => $product_order['product_id']
					));

					$product_get = $product_get_query->fetch();

					if ($product_get['product_quantity'] <= 0) {
						$product_get_out_of_stock++;
					}
				}

				//Check if product that is out of stock has been ordered
				if ($product_get_out_of_stock > 0) {
					$order_status = 5;
				}

				//Insert new row in order
				$order_query = $db->prepare("
						INSERT INTO idk_order
							(client_id, order_note, employee_id, order_status, order_total_price, order_total_tax, order_total_rabat, order_to_pay, order_key, order_type, order_pay_method, created_at, updated_at)
						VALUES
							(:client_id, :order_note, :employee_id, :order_status, :order_total_price, :order_total_tax, :order_total_rabat, :order_to_pay, :order_key, :order_type, :order_pay_method, :created_at, :updated_at)");

				$order_query->execute(array(
					':client_id' => $order['client_id'],
					':order_note' => $order['order_note'],
					':employee_id' => $order['employee_id'],
					':order_total_price' => $order['order_total_price'],
					':order_total_tax' => $order['order_total_tax'],
					':order_total_rabat' => $order['order_total_rabat'],
					':order_to_pay' => $order['order_to_pay'],
					':order_key' => $order['order_key'],
					':order_type' => $order['order_type'],
					':order_status' => $order_status,
					':order_pay_method' => $order['order_pay_method'],
					':created_at' => $order['created_at'],
					':updated_at' => $order['updated_at']
				));

				$_order_id = $db->lastInsertId();

				//Get client name
				$client_id = $order['client_id'];

				$client_query = $db->prepare("
					SELECT client_name
					FROM idk_client
					WHERE client_id = :client_id");

				$client_query->execute(array(
					':client_id' => $client_id
				));

				$client = $client_query->fetch();
				$client_name = $client['client_name'];

				//Get data from product offer
				$product_order_query = $db->prepare("
					SELECT *
					FROM idk_product_order
					WHERE order_id = :order_id");

				$product_order_query->execute(array(
					':order_id' => $order_id
				));

				while ($product_order = $product_order_query->fetch()) {
					//Insert new row in product order
					$product_order_insert_query = $db->prepare("
						INSERT INTO idk_product_order
							(order_id, product_id, product_name, product_currency, product_quantity, product_unit, product_price, product_tax_name, product_tax_percentage, product_tax_value, product_rabat_percentage, product_rabat_value, product_in_stock, product_quantity_in_db)
						VALUES
							(:order_id, :product_id, :product_name, :product_currency, :product_quantity, :product_unit, :product_price, :product_tax_name, :product_tax_percentage, :product_tax_value, :product_rabat_percentage, :product_rabat_value, :product_in_stock, :product_quantity_in_db)");

					$product_order_insert_query->execute(array(
						':order_id' => $_order_id,
						':product_id' => $product_order['product_id'],
						':product_name' => $product_order['product_name'],
						':product_currency' => $product_order['product_currency'],
						':product_quantity' => $product_order['product_quantity'],
						':product_unit' => $product_order['product_unit'],
						':product_price' => $product_order['product_price'],
						':product_tax_name' => $product_order['product_tax_name'],
						':product_tax_percentage' => $product_order['product_tax_percentage'],
						':product_tax_value' => $product_order['product_tax_value'],
						':product_rabat_percentage' => $product_order['product_rabat_percentage'],
						':product_rabat_value' => $product_order['product_rabat_value'],
						':product_in_stock' => $product_order['product_in_stock'],
						':product_quantity_in_db' => $product_order['product_quantity_in_db']
					));


					$update_product_quantity_query = $db->prepare("
							UPDATE idk_product
							SET	product_quantity = product_quantity - :temp_product_quantity
							WHERE product_id = :product_id");

					$update_product_quantity_query->execute(array(
						':temp_product_id' => $product_order['product_id'],
						':product_quantity' => $product_order['product_quantity']
					));
				}

				//Get employee name
				$select_employee_query = $db->prepare("
						SELECT employee_first_name, employee_last_name
						FROM idk_employee
						WHERE employee_id = :employee_id");

				$select_employee_query->execute(array(
					':employee_id' => $order['employee_id']
				));

				$employee = $select_employee_query->fetch();

				$employee_first_name = $employee['employee_first_name'];
				$employee_last_name = $employee['employee_last_name'];
				$employee_first_name_initial = substr($employee_first_name, 0, 1);
				$employee_last_name_initial = substr($employee_last_name, 0, 1);

				//Add notification to all administrators

				//Get client_email
				$select_client_email_query = $db->prepare("
						SELECT ci_data
						FROM idk_client_info
						WHERE ci_group = :ci_group AND ci_primary = :ci_primary AND client_id = :client_id");

				$select_client_email_query->execute(array(
					':ci_group' => 2,
					':client_id' => $client_id,
					':ci_primary' => 1
				));

				$number_of_rows_email = $select_client_email_query->rowCount();

				if ($number_of_rows_email !== 0) {

					$select_client_email = $select_client_email_query->fetch();
					$client_email = $select_client_email['ci_data'];

					//Send confirmation e-mail with order
					$mail_email = $client_email;
					$mail_name = $client_name;
					$mail_subject = "Unaviva d.o.o. B2B - Potvrda narudžbe";
					$mail_body = "Poštovani,<br><br>Obavještavamo Vas da je Vaša narudžba uspješno zaprimljena.<br>Narudžbu možete isprintati na sljedećem <a href='" . getSiteUrlr() . "idkadmin/print_order?id=" . $order_id . "&order=" . $offer['offer_key'] . "'>linku</a>.<br><br>Hvala na povjerenju,<br>Vaša Unaviva d.o.o.";
					$mail_altbody = "Poštovani,<br><br>Obavještavamo Vas da je Vaša narudžba uspješno zaprimljena.<br>Narudžbu možete isprintati na sljedećem <a href='" . getSiteUrlr() . "idkadmin/print_order?id=" . $order_id . "&order=" . $offer['offer_key'] . "'>linku</a>.<br><br>Hvala na povjerenju,<br>Vaša Unaviva d.o.o.";

					sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);
				}

				//Get administrators
				$select_employees_query = $db->prepare("
						SELECT employee_id
						FROM idk_employee
						WHERE employee_status = :employee_status");

				$select_employees_query->execute(array(
					':employee_status' => 1
				));

				while ($select_employees = $select_employees_query->fetch()) {

					$notification_employeeid = $select_employees['employee_id'];
					$notification_datetime = date('Y-m-d H:i:s');
					$notification_title = "Nova narudžba<br>Klijent: ${client_name}<br>Komercijalista: ${employee_first_name_initial}. ${employee_last_name_initial}.";
					$notification_icon = "shopping-cart";
					$notification_link = "" . getSiteUrlr() . "idkadmin/orders?page=open&order_id=${order_id}";
					$notification_type = 3;

					addNotification($notification_title, $notification_icon, $notification_link, $notification_employeeid, $notification_type);
				}

				//Update report
				$report_id = NULL;
				$order_id = $order_id;
				$report_start_time = NULL;
				$report_end_time = date('Y-m-d H:i:s');
				addReport(
					$report_id,
					$logged_employee_id,
					$client_id,
					$order_id,
					$report_start_time,
					$report_end_time
				);


				updateOrdersStats();

				header("Location: orders?page=list&mess=7");
			} else {
				header("Location: index");
			}

			break;



			/************************************************************
			 * 							ORDER FISCALIZATION
			 * *********************************************************/
		case "order_fiscalization":

			$order_id = $_GET['id'];

			var_dump($order_id);
			exit();


			if (isset($order_id)) {

				//Get data from client
				$client_query = $db->prepare("
					SELECT t1.client_name
					FROM idk_client t1
					INNER JOIN idk_order t2
					ON t1.client_id = t2.client_id
					WHERE t2.order_id = :order_id");

				$client_query->execute(array(
					':order_id' => $order_id
				));

				$client = $client_query->fetch();
				$client_name = $client['client_name'];

				//Get data from product_order
				$product_order_query = $db->prepare("
						SELECT t1.*, t2.*
						FROM idk_order t1
						INNER JOIN idk_product_order t2
						ON t1.order_id = t2.order_id
						WHERE t1.order_id = :order_id");

				$product_order_query->execute(array(
					':order_id' => $order_id
				));

				$order_query = $db->prepare("
					SELECT *
					FROM idk_order
					WHERE order_id = :order_id");

				$order_query->execute(array(
					':order_id' => $order_id
				));

				$order = $order_query->fetch();
				$orderTotalPrice = $order['order_total_price'];
				$orderToPay = $order['order_to_pay'];
				$orderTotalTax = $order['order_total_tax'];
				$orderTotalRabat = $order['order_total_rabat'];
				$orderNote = $order['order_note'];
				$orderClient = $client_name;
				$orderPayMethod = 2;
				$orderPayMethodLabel = 'Virmansko';

				var_dump($orderTotalPrice);
				exit();


				//create order in Pantheon
				//Sending http POST request to Matrica
				$url = 'http://82.118.0.88:5000/api/narudzba';

				$data = array(
					'orderTotalPrice' => $orderTotalPrice,
					'orderToPay' => $orderToPay,
					'orderTotalTax' => $orderTotalTax,
					'orderTotalRabat' => $orderTotalRabat,
					'orderNote' => $orderNote,
					'orderClient' => $orderClient,
					'orderPayMethod' => $orderPayMethod,
					'orderPayMethodLabel' => $orderPayMethodLabel

				);


				$make_call = callAPI('POST', 'http://82.118.0.88:5000/api/narudzba', json_encode($data));
				$response = json_decode($make_call, true);
				$errors = $response['response']['errors'];
				$data_response = $response['response']['data'][0];

				var_dump($data_response);
				exit();


				while ($product_order = $product_order_query->fetch()) {

					// $order_pay_method = $product_order['order_pay_method'];
					$product_id = $product_order['product_id'];
					$product_price = $product_order['product_price'];
					$product_rabat_percentage = $product_order['product_rabat_percentage'];
					$product_quantity = $product_order['product_quantity'];

					$product_api_id_query = $db->prepare("
							SELECT product_api_id
							FROM idk_product
							WHERE product_id = :product_id");

					$product_api_id_query->execute(array(
						':product_id' => $product_id
					));

					$product_api_id_row = $product_api_id_query->fetch();
					$product_api_id = $product_api_id_row['product_api_id'];

					//Sending http POST request to Matrica
					$url = 'http://82.118.0.88:5000/api/narudzba';
					$acConsignee = $client_name;
					$acIdent = $product_api_id;
					$anQty = $product_quantity;
					$acExternalOrderID = $order_id;
					$anPrice = $product_price;
					$anRebate = isset($product_rabat_percentage) ? $product_rabat_percentage : 0.00;

					$data = array(
						'acConsignee' => $acConsignee,
						'acIdent' => $acIdent,
						'anQty' => $anQty,
						'acExternalOrderID' => $acExternalOrderID,
						'anPrice' => $anPrice,
						'anRebate' => $anRebate
					);


					$make_call = callAPI('POST', 'http://82.118.0.88:5000/api/narudzba', json_encode($data));
					$response = json_decode($make_call, true);
					$errors = $response['response']['errors'];
					$data_response = $response['response']['data'][0];

					sleep(1);
				}

				$update_order_query = $db->prepare("
						UPDATE idk_order
						SET order_fiscalized = :order_fiscalized
						WHERE order_id = :order_id");

				$update_order_query->execute(array(
					':order_id' => $order_id,
					':order_fiscalized' => 1
				));

				header("Location: orders?page=open&order_id=${order_id}&mess=3");
			} else {
				header("Location: orders?page=open&order_id=${order_id}&mess=4");
			}

			break;

			/************************************************************
			 * 							OFFER CHANGE CLIENT
			 * *********************************************************/
		case "offer_change_client":

			$offer_id = !empty($_POST['offer_id']) ? $_POST['offer_id'] : NULL;
			$client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : NULL;

			if (isset($offer_id) and isset($client_id)) {

				//Update order client
				$client_query = $db->prepare("
						UPDATE idk_offer
						SET client_id = :client_id
						WHERE offer_id = :offer_id");

				$client_query->execute(array(
					':client_id' => $client_id,
					':offer_id' => $offer_id
				));


				//Add to log
				$log_desc = "Uredio ponudu #${offer_id}";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: offers?page=edit&offer_id=${offer_id}&mess=1");
			} elseif (isset($offer_id)) {
				header("Location: offers?page=edit&offer_id=${offer_id}&mess=2");
			} else {
				header("Location: offers?page=list&mess=2");
			}

			break;

			/************************************************************
			 * 							OFFER ADD PRODUCT
			 * *********************************************************/
		case "offer_add_product":

			$offer_id = !empty($_POST['offer_id']) ? $_POST['offer_id'] : NULL;
			$product_id = !empty($_POST['product_id']) ? $_POST['product_id'] : NULL;
			$product_quantity = !empty($_POST['product_quantity']) ? $_POST['product_quantity'] : NULL;
			$product_rabat_percentage = !empty($_POST['product_rabat_percentage']) ? $_POST['product_rabat_percentage'] : NULL;
			$client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : NULL;

			if (isset($client_id) and isset($offer_id) and isset($product_id) and isset($product_quantity) and isset($product_rabat_percentage)) {

				//Get max rabat for client
				$check_client_query = $db->prepare("
							SELECT client_max_rabat
							FROM idk_client
							WHERE client_id = :client_id");

				$check_client_query->execute(array(
					':client_id' => $client_id
				));

				$number_of_rows = $check_client_query->rowCount();

				if ($number_of_rows == 1) {
					$client_max_rabat_row = $check_client_query->fetch();
					$client_max_rabat = isset($client_max_rabat_row['client_max_rabat']) ? floatval($client_max_rabat_row['client_max_rabat']) : NULL;
				} else {
					$client_max_rabat = NULL;
				}

				if (isset($client_max_rabat) and $client_max_rabat < $product_rabat_percentage) {
					$product_rabat_percentage = $client_max_rabat;
				}

				//Check if product order exists
				$check_query = $db->prepare("
							SELECT product_id
							FROM idk_product_offer
							WHERE product_id = :product_id AND offer_id = :offer_id");

				$check_query->execute(array(
					':product_id' => $product_id,
					':offer_id' => $offer_id
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {

					//Get product info
					$product_query = $db->prepare("
								SELECT *
								FROM idk_product
								WHERE product_id = :product_id");

					$product_query->execute(array(
						':product_id' => $product_id
					));

					$product = $product_query->fetch();

					$product_name = $product['product_name'];
					$product_currency = $product['product_currency'];
					$product_unit = $product['product_unit'];
					$product_price = $product['product_price'];
					$product_tax_name = $product['product_tax_name'];
					$product_tax_percentage = $product['product_tax_percentage'];
					$product_quantity_in_db = $product['product_quantity'];
					$product_rabat_value = $product_price * $product_rabat_percentage / 100;
					$product_tax_value = ($product_price - $product_rabat_value) * $product_tax_percentage / 100;
					$product_in_stock = 1;


					$order_status = NULL;

					//Add product to order
					$query = $db->prepare("
								INSERT INTO idk_product_offer
									(offer_id, product_id, product_name, product_currency, product_quantity, product_unit, product_price, product_tax_name, product_tax_percentage, product_tax_value, product_rabat_percentage, product_rabat_value, product_in_stock, product_quantity_in_db)
								VALUES
									(:offer_id, :product_id, :product_name, :product_currency, :product_quantity, :product_unit, :product_price, :product_tax_name, :product_tax_percentage, :product_tax_value, :product_rabat_percentage, :product_rabat_value, :product_in_stock, :product_quantity_in_db)");

					$query->execute(array(
						':offer_id' => $offer_id,
						':product_id' => $product_id,
						':product_name' => $product_name,
						':product_currency' => $product_currency,
						':product_quantity' => $product_quantity,
						':product_unit' => $product_unit,
						':product_price' => $product_price,
						':product_tax_name' => $product_tax_name,
						':product_tax_percentage' => $product_tax_percentage,
						':product_tax_value' => $product_tax_value,
						':product_rabat_percentage' => $product_rabat_percentage,
						':product_rabat_value' => $product_rabat_value,
						':product_in_stock' => $product_in_stock,
						':product_quantity_in_db' => $product_quantity_in_db
					));


					//Get product order info
					$product_offer_query = $db->prepare("
								SELECT *
								FROM idk_product_offer
								WHERE offer_id = :offer_id");

					$product_offer_query->execute(array(
						':offer_id' => $offer_id
					));

					$offer_total_price = 0.000;
					$offer_total_tax = 0.000;
					$offer_total_rabat = 0.000;
					$offer_to_pay = 0.000;

					while ($product_offer = $product_offer_query->fetch()) {

						$product_price = $product_offer['product_price'];
						$product_quantity = $product_offer['product_quantity'];
						$product_tax_percentage = $product_offer['product_tax_percentage'];
						$product_rabat_percentage = $product_offer['product_rabat_percentage'];
						$product_rabat_value = $product_price * $product_rabat_percentage / 100;
						$product_tax_value = ($product_price - $product_rabat_value) * $product_tax_percentage / 100;

						//Calculate product to pay again
						// $product_price = $product_price - ($product_price * $product_rabat_percentage / 100); //Calculate price with rabat
						$product_total_price = $product_price * $product_quantity; //Price without rabat
						$product_total_tax = $product_tax_value * $product_quantity;
						$product_total_rabat = $product_rabat_value * $product_quantity; //Calculate total rabat value
						$product_to_pay = $product_total_price + $product_total_tax - $product_total_rabat;

						$offer_total_price += $product_total_price;
						$offer_total_tax += $product_total_tax;
						$offer_total_rabat += $product_total_rabat;
						$offer_to_pay += $product_to_pay;
						$offer_to_pay = round($offer_to_pay * 2, 1) / 2;
					}


					//Update total price and tax of order
					$update_total_price_tax_query = $db->prepare("
								UPDATE idk_offer
								SET	offer_total_price = :offer_total_price, offer_total_tax = :offer_total_tax, offer_total_rabat = :offer_total_rabat, offer_to_pay = :offer_to_pay
								WHERE offer_id = :offer_id");

					$update_total_price_tax_query->execute(array(
						':offer_id' => $offer_id,
						':offer_total_price' => $offer_total_price,
						':offer_total_tax' => $offer_total_tax,
						':offer_total_rabat' => $offer_total_rabat,
						':offer_to_pay' => $offer_to_pay
					));


					//Add to log
					$log_desc = "Uredio narudžbu #${order_id}";
					$log_date = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $log_date);

					header("Location: offers?page=edit&offer_id=${offer_id}&mess=3");
				}
			}
			break;


			/************************************************************
			 * 							ORDER CHANGE CLIENT
			 * *********************************************************/
		case "order_change_client":

			$order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : NULL;
			$client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : NULL;

			if (isset($order_id) and isset($client_id)) {

				//Update order client
				$client_query = $db->prepare("
						UPDATE idk_order
						SET client_id = :client_id
						WHERE order_id = :order_id");

				$client_query->execute(array(
					':client_id' => $client_id,
					':order_id' => $order_id
				));

				//Add to log
				$log_desc = "Uredio narudžbu #${order_id}";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: orders?page=edit&order_id=${order_id}&mess=1");
			} elseif (isset($order_id)) {
				header("Location: orders?page=edit&order_id=${order_id}&mess=2");
			} else {
				header("Location: orders?page=list&mess=2");
			}

			break;




			/************************************************************
			 * 							ORDER ADD PRODUCT
			 * *********************************************************/
		case "order_add_product":

			$order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : NULL;
			$product_id = !empty($_POST['product_id']) ? $_POST['product_id'] : NULL;
			$product_quantity = !empty($_POST['product_quantity']) ? $_POST['product_quantity'] : NULL;
			$product_rabat_percentage = !empty($_POST['product_rabat_percentage']) ? $_POST['product_rabat_percentage'] : NULL;
			$client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : NULL;

			if (isset($client_id) and isset($order_id) and isset($product_id) and isset($product_quantity) and isset($product_rabat_percentage)) {
				//Get max rabat for client
				$check_client_query = $db->prepare("
					SELECT client_max_rabat
					FROM idk_client
					WHERE client_id = :client_id");

				$check_client_query->execute(array(
					':client_id' => $client_id
				));

				$number_of_rows = $check_client_query->rowCount();

				if ($number_of_rows == 1) {
					$client_max_rabat_row = $check_client_query->fetch();
					$client_max_rabat = isset($client_max_rabat_row['client_max_rabat']) ? floatval($client_max_rabat_row['client_max_rabat']) : NULL;
				} else {
					$client_max_rabat = NULL;
				}

				if (isset($client_max_rabat) and $client_max_rabat < $product_rabat_percentage) {
					$product_rabat_percentage = $client_max_rabat;
				}

				//Check if product order exists
				$check_query = $db->prepare("
					SELECT product_id
					FROM idk_product_order
					WHERE product_id = :product_id AND order_id = :order_id");

				$check_query->execute(array(
					':product_id' => $product_id,
					':order_id' => $order_id
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {
					//Get product info
					$product_query = $db->prepare("
						SELECT *
						FROM idk_product
						WHERE product_id = :product_id");

					$product_query->execute(array(
						':product_id' => $product_id
					));

					$product = $product_query->fetch();

					$product_name = $product['product_name'];
					$product_currency = $product['product_currency'];
					$product_unit = $product['product_unit'];
					$product_price = $product['product_price'];
					$product_tax_name = $product['product_tax_name'];
					$product_tax_percentage = $product['product_tax_percentage'];
					$product_quantity_in_db = $product['product_quantity'];
					$product_rabat_value = $product_price * $product_rabat_percentage / 100;
					$product_tax_value = ($product_price - $product_rabat_value) * $product_tax_percentage / 100;
					if ($product_quantity_in_db > 0) {
						$product_in_stock = 1;
						if ($product_quantity > $product_quantity_in_db) {
							$product_in_stock = 2;
						}
					} else {
						$product_in_stock = 0;
					}
					$order_status = NULL;

					if ($product_in_stock == 1) {
						//Update quantity in idk_product
						$update_product_quantity_query = $db->prepare("
							UPDATE idk_product
							SET	product_quantity = product_quantity - :product_quantity
							WHERE product_id = :product_id");

						$update_product_quantity_query->execute(array(
							':product_id' => $product_id,
							':product_quantity' => $product_quantity
						));
					}

					//Add product to order
					$query = $db->prepare("
						INSERT INTO idk_product_order
							(order_id, product_id, product_name, product_currency, product_quantity, product_unit, product_price, product_tax_name, product_tax_percentage, product_tax_value, product_rabat_percentage, product_rabat_value, product_in_stock, product_quantity_in_db)
						VALUES
							(:order_id, :product_id, :product_name, :product_currency, :product_quantity, :product_unit, :product_price, :product_tax_name, :product_tax_percentage, :product_tax_value, :product_rabat_percentage, :product_rabat_value, :product_in_stock, :product_quantity_in_db)");

					$query->execute(array(
						':order_id' => $order_id,
						':product_id' => $product_id,
						':product_name' => $product_name,
						':product_currency' => $product_currency,
						':product_quantity' => $product_quantity,
						':product_unit' => $product_unit,
						':product_price' => $product_price,
						':product_tax_name' => $product_tax_name,
						':product_tax_percentage' => $product_tax_percentage,
						':product_tax_value' => $product_tax_value,
						':product_rabat_percentage' => $product_rabat_percentage,
						':product_rabat_value' => $product_rabat_value,
						':product_in_stock' => $product_in_stock,
						':product_quantity_in_db' => $product_quantity_in_db
					));

					//Get product order info
					$product_order_query = $db->prepare("
						SELECT *
						FROM idk_product_order
						WHERE order_id = :order_id");

					$product_order_query->execute(array(
						':order_id' => $order_id
					));

					$order_total_price = 0.000;
					$order_total_tax = 0.000;
					$order_total_rabat = 0.000;
					$order_to_pay = 0.000;

					while ($product_order = $product_order_query->fetch()) {
						if ($product_order['product_in_stock'] != 1) {
							$order_status = 5;
						}

						$product_price = $product_order['product_price'];
						$product_quantity = $product_order['product_quantity'];
						$product_tax_percentage = $product_order['product_tax_percentage'];
						$product_rabat_percentage = $product_order['product_rabat_percentage'];
						$product_rabat_value = $product_price * $product_rabat_percentage / 100;
						$product_tax_value = ($product_price - $product_rabat_value) * $product_tax_percentage / 100;

						//Calculate product to pay again
						// $product_price = $product_price - ($product_price * $product_rabat_percentage / 100); //Calculate price with rabat
						$product_total_price = $product_price * $product_quantity; //Price without rabat
						$product_total_tax = $product_tax_value * $product_quantity;
						$product_total_rabat = $product_rabat_value * $product_quantity; //Calculate total rabat value
						$product_to_pay = $product_total_price + $product_total_tax - $product_total_rabat;

						$order_total_price += $product_total_price;
						$order_total_tax += $product_total_tax;
						$order_total_rabat += $product_total_rabat;
						$order_to_pay += $product_to_pay;
						$order_to_pay = round($order_to_pay * 2, 1) / 2;
					}

					//Update total price and tax of order
					$update_total_price_tax_query = $db->prepare("
						UPDATE idk_order
						SET	order_total_price = :order_total_price, order_total_tax = :order_total_tax, order_total_rabat = :order_total_rabat, order_to_pay = :order_to_pay
						WHERE order_id = :order_id");

					$update_total_price_tax_query->execute(array(
						':order_id' => $order_id,
						':order_total_price' => $order_total_price,
						':order_total_tax' => $order_total_tax,
						':order_total_rabat' => $order_total_rabat,
						':order_to_pay' => $order_to_pay
					));

					//Update order status if added product is out of stock
					if (isset($order_status) and $order_status == 5) {
						$update_order_query = $db->prepare("
							UPDATE idk_order
							SET	order_status = 5
							WHERE order_id = :order_id");

						$update_order_query->execute(array(
							':order_id' => $order_id
						));
					}

					//Add to log
					$log_desc = "Uredio narudžbu #${order_id}";
					$log_date = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $log_date);

					header("Location: orders?page=edit&order_id=${order_id}&mess=3");
				} else {
					header("Location: orders?page=edit&order_id=${order_id}&mess=4");
				}
			} elseif (isset($order_id)) {
				header("Location: orders?page=edit&order_id=${order_id}&mess=2");
			} else {
				header("Location: orders?page=list&mess=2");
			}

			break;



			/************************************************************
			 * 							EDIT ORDER
			 * *********************************************************/
		case "edit_order":

			$order_id = !empty($_POST['order_id']) ? $_POST['order_id'] : NULL;
			$client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : NULL;
			$products_rabats_array_strings = $_POST['products_rabats_array'];
			$products_quantities_array_old_strings = $_POST['products_quantities_array_old'];
			$products_quantities_array_strings = $_POST['products_quantities_array'];
			$products_prices_array_strings = $_POST['products_prices_array'];
			$products_tax_percentages_array_strings = $_POST['products_tax_percentages_array'];
			$products_ids_array_strings = $_POST['products_ids_array'];

			if (isset($client_id) and isset($order_id) and isset($products_rabats_array_strings) and isset($products_quantities_array_strings) and isset($products_prices_array_strings) and isset($products_ids_array_strings)) {
				//Get order info
				$order_query = $db->prepare("
					SELECT order_status
					FROM idk_order
					WHERE order_id = :order_id");

				$order_query->execute(array(
					':order_id' => $order_id
				));

				$order = $order_query->fetch();

				$order_status_old = $order['order_status'];
				$order_status = NULL;

				//Get max rabat for client
				$check_client_query = $db->prepare("
					SELECT client_max_rabat
					FROM idk_client
					WHERE client_id = :client_id");

				$check_client_query->execute(array(
					':client_id' => $client_id
				));

				$number_of_rows = $check_client_query->rowCount();

				if ($number_of_rows == 1) {
					$client_max_rabat_row = $check_client_query->fetch();
					$client_max_rabat = isset($client_max_rabat_row['client_max_rabat']) ? floatval($client_max_rabat_row['client_max_rabat']) : NULL;
				} else {
					$client_max_rabat = NULL;
				}

				if (isset($client_max_rabat) and $client_max_rabat < $product_rabat_percentage) {
					$product_rabat_percentage = $client_max_rabat;
				}

				$products_rabats_array_strings = explode(",", $products_rabats_array_strings[0]);
				$products_quantities_array_old_strings = explode(",", $products_quantities_array_old_strings[0]);
				$products_quantities_array_strings = explode(",", $products_quantities_array_strings[0]);
				$products_prices_array_strings = explode(",", $products_prices_array_strings[0]);
				$products_tax_percentages_array_strings = explode(",", $products_tax_percentages_array_strings[0]);
				$products_ids_array_strings = explode(",", $products_ids_array_strings[0]);

				$order_total_price = 0.000;
				$order_total_tax = 0.000;
				$order_total_rabat = 0.000;
				$order_to_pay = 0.000;

				for ($i = 0; $i < count($products_ids_array_strings); $i++) {
					$product_quantity_old = floatval($products_quantities_array_old_strings[$i]);
					$product_quantity_new = floatval($products_quantities_array_strings[$i]);
					$product_quantity_difference = $product_quantity_new - $product_quantity_old;

					var_dump($product_quantity_difference);
					// exit;

					//Update quantity in idk_product
					$update_product_quantity_query = $db->prepare("
						UPDATE idk_product
						SET	product_quantity = product_quantity - :product_quantity
						WHERE product_id = :product_id");

					$update_product_quantity_query->execute(array(
						':product_id' => $product_id,
						':product_quantity' => $product_quantity_difference
					));

					//Get product
					$product_query = $db->prepare("
						SELECT product_quantity
						FROM idk_product
						WHERE product_id = :product_id");

					$product_query->execute(array(
						':product_id' => $products_ids_array_strings[$i]
					));

					$product_row = $product_query->fetch();
					$product_quantity_in_db = $product_row['product_quantity'];

					$product_rabat_percentage = floatval($products_rabats_array_strings[$i]);
					if (isset($client_max_rabat) and $client_max_rabat < $product_rabat_percentage) {
						$product_rabat_percentage = $client_max_rabat;
					}
					$product_quantity = floatval($products_quantities_array_strings[$i]);
					$product_price = floatval($products_prices_array_strings[$i]);
					$product_tax_percentage = floatval($products_tax_percentages_array_strings[$i]);
					$product_rabat_value = $product_price * $product_rabat_percentage / 100;
					$product_tax_value = ($product_price - $product_rabat_value) * $product_tax_percentage / 100;
					$product_id = intval($products_ids_array_strings[$i]);
					if ($product_quantity_in_db > 0) {
						$product_in_stock = 1;
						if ($product_quantity > $product_quantity_in_db) {
							$product_in_stock = 2;
						}
					} else {
						$product_in_stock = 0;
					}

					if ($product_in_stock != 1) {
						//Update quantity in idk_product
						$update_product_quantity_query = $db->prepare("
							UPDATE idk_product
							SET	product_quantity = product_quantity + :product_quantity
							WHERE product_id = :product_id");

						$update_product_quantity_query->execute(array(
							':product_id' => $product_id,
							':product_quantity' => $product_quantity_old
						));
					}

					//Update product order
					$product_order_query = $db->prepare("
						UPDATE idk_product_order
						SET	product_rabat_percentage = :product_rabat_percentage, product_quantity = :product_quantity, product_rabat_value = :product_rabat_value, product_in_stock = :product_in_stock, product_quantity_in_db = :product_quantity_in_db
						WHERE product_id = :product_id AND order_id = :order_id");

					$product_order_query->execute(array(
						':product_id' => $product_id,
						':order_id' => $order_id,
						':product_quantity' => $product_quantity,
						':product_rabat_percentage' => $product_rabat_percentage,
						':product_rabat_value' => $product_rabat_value,
						':product_in_stock' => $product_in_stock,
						':product_quantity_in_db' => $product_quantity_in_db
					));

					//Calculate product to pay again
					// $product_price = $product_price - ($product_price * $product_rabat_percentage / 100); //Calculate price with rabat
					$product_total_price = $product_price * $product_quantity; //Price without rabat
					$product_total_tax = $product_tax_value * $product_quantity;
					$product_total_rabat = $product_rabat_value * $product_quantity; //Calculate total rabat value
					$product_to_pay = $product_total_price + $product_total_tax - $product_total_rabat;

					$order_total_price += $product_total_price;
					$order_total_tax += $product_total_tax;
					$order_total_rabat += $product_total_rabat;
					$order_to_pay += $product_to_pay;
					$order_to_pay = round($order_to_pay * 2, 1) / 2;
				}

				//Update total price and tax of order temp
				$update_total_price_tax_query = $db->prepare("
					UPDATE idk_order
					SET	order_total_price = :order_total_price, order_total_tax = :order_total_tax, order_total_rabat = :order_total_rabat, order_to_pay = :order_to_pay
					WHERE order_id = :order_id");

				$update_total_price_tax_query->execute(array(
					':order_id' => $order_id,
					':order_total_price' => $order_total_price,
					':order_total_tax' => $order_total_tax,
					':order_total_rabat' => $order_total_rabat,
					':order_to_pay' => $order_to_pay
				));

				//Update order status
				if (isset($order_status) and $order_status == 5) {
					$update_order_query = $db->prepare("
						UPDATE idk_order
						SET	order_status = 5
						WHERE order_id = :order_id");

					$update_order_query->execute(array(
						':order_id' => $order_id
					));
				} elseif ($order_status_old == 5 and !isset($order_status)) {
					$update_order_query = $db->prepare("
						UPDATE idk_order
						SET	order_status = 1
						WHERE order_id = :order_id");

					$update_order_query->execute(array(
						':order_id' => $order_id
					));
				}

				//Add to log
				$log_desc = "Uredio narudžbu #${order_id}";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: orders?page=edit&order_id=${order_id}&mess=7");
			} else {
				header("Location: orders?page=list&mess=2");
			}

			break;

			/*-----------------------------------------------------------------------------------------
									ORDER END
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 							EDIT OFFER
			 * *********************************************************/
		case "edit_offer":

			$offer_id = !empty($_POST['offer_id']) ? $_POST['offer_id'] : NULL;
			$client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : NULL;
			$products_rabats_array_strings = $_POST['products_rabats_array'];
			$products_quantities_array_old_strings = $_POST['products_quantities_array_old'];
			$products_quantities_array_strings = $_POST['products_quantities_array'];
			$products_prices_array_strings = $_POST['products_prices_array'];
			$products_tax_percentages_array_strings = $_POST['products_tax_percentages_array'];
			$products_ids_array_strings = $_POST['products_ids_array'];

			if (isset($client_id) and isset($offer_id) and isset($products_rabats_array_strings) and isset($products_quantities_array_strings) and isset($products_prices_array_strings) and isset($products_ids_array_strings)) {

				//Get max rabat for client
				$check_client_query = $db->prepare("
						SELECT client_max_rabat
						FROM idk_client
						WHERE client_id = :client_id");

				$check_client_query->execute(array(
					':client_id' => $client_id
				));

				$number_of_rows = $check_client_query->rowCount();

				if ($number_of_rows == 1) {
					$client_max_rabat_row = $check_client_query->fetch();
					$client_max_rabat = isset($client_max_rabat_row['client_max_rabat']) ? floatval($client_max_rabat_row['client_max_rabat']) : NULL;
				} else {
					$client_max_rabat = NULL;
				}

				if (isset($client_max_rabat) and $client_max_rabat < $product_rabat_percentage) {
					$product_rabat_percentage = $client_max_rabat;
				}

				$products_rabats_array_strings = explode(",", $products_rabats_array_strings[0]);
				$products_quantities_array_old_strings = explode(",", $products_quantities_array_old_strings[0]);
				$products_quantities_array_strings = explode(",", $products_quantities_array_strings[0]);
				$products_prices_array_strings = explode(",", $products_prices_array_strings[0]);
				$products_tax_percentages_array_strings = explode(",", $products_tax_percentages_array_strings[0]);
				$products_ids_array_strings = explode(",", $products_ids_array_strings[0]);

				$offer_total_price = 0.000;
				$offer_total_tax = 0.000;
				$offer_total_rabat = 0.000;
				$offer_to_pay = 0.000;

				for ($i = 0; $i < count($products_ids_array_strings); $i++) {
					$product_quantity_old = floatval($products_quantities_array_old_strings[$i]);
					$product_quantity_new = floatval($products_quantities_array_strings[$i]);
					$product_quantity_difference = $product_quantity_new - $product_quantity_old;
					$product_in_stock = 1;

					$product_rabat_percentage = floatval($products_rabats_array_strings[$i]);
					if (isset($client_max_rabat) and $client_max_rabat < $product_rabat_percentage) {
						$product_rabat_percentage = $client_max_rabat;
					}
					$product_quantity = floatval($products_quantities_array_strings[$i]);
					$product_price = floatval($products_prices_array_strings[$i]);
					$product_tax_percentage = floatval($products_tax_percentages_array_strings[$i]);
					$product_rabat_value = $product_price * $product_rabat_percentage / 100;
					$product_tax_value = ($product_price - $product_rabat_value) * $product_tax_percentage / 100;
					$product_id = intval($products_ids_array_strings[$i]);


					//Update product order
					$product_offer_query = $db->prepare("
							UPDATE idk_product_offer
							SET	product_rabat_percentage = :product_rabat_percentage, product_quantity = :product_quantity, product_rabat_value = :product_rabat_value
							WHERE product_id = :product_id AND offer_id = :offer_id");

					$product_offer_query->execute(array(
						':product_id' => $product_id,
						':offer_id' => $offer_id,
						':product_quantity' => $product_quantity,
						':product_rabat_percentage' => $product_rabat_percentage,
						':product_rabat_value' => $product_rabat_value
					));

					//Calculate product to pay again
					// $product_price = $product_price - ($product_price * $product_rabat_percentage / 100); //Calculate price with rabat
					$product_total_price = $product_price * $product_quantity; //Price without rabat

					$product_total_tax = $product_tax_value * $product_quantity;
					$product_total_rabat = $product_rabat_value * $product_quantity; //Calculate total rabat value
					$product_to_pay = $product_total_price + $product_total_tax - $product_total_rabat;

					$offer_total_price += $product_total_price;
					$offer_total_tax += $product_total_tax;
					$offer_total_rabat += $product_total_rabat;
					$offer_to_pay += $product_to_pay;
					$offer_to_pay = round($offer_to_pay * 2, 1) / 2;
				}

				//Update total price and tax of order temp
				$update_total_price_tax_query = $db->prepare("
						UPDATE idk_offer
						SET	offer_total_price = :offer_total_price, offer_total_tax = :offer_total_tax, offer_total_rabat = :offer_total_rabat, offer_to_pay = :offer_to_pay
						WHERE offer_id = :offer_id");

				$update_total_price_tax_query->execute(array(
					':offer_id' => $offer_id,
					':offer_total_price' => $offer_total_price,
					':offer_total_tax' => $offer_total_tax,
					':offer_total_rabat' => $offer_total_rabat,
					':offer_to_pay' => $offer_to_pay
				));

				//Add to log
				$log_desc = "Uredio narudžbu #${order_id}";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: offers?page=edit&offer_id=${offer_id}&mess=6");
			} else {
				header("Location: offers?page=list&mess=2");
			}

			break;

			/*-----------------------------------------------------------------------------------------
										OFFER END
	-----------------------------------------------------------------------------------------*/





			/*-----------------------------------------------------------------------------------------
							MILEAGE START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					RESTART MILEAGE
			 * *********************************************************/
		case "restart_mileage":

			$mileage_employee_id = isset($_POST['employee_id']) ? $_POST['employee_id'] : NULL;
			$mileage_amount_start = !empty($_POST['mileage_amount_start']) ? $_POST['mileage_amount_start'] : NULL;
			$mileage_start_time = date('Y-m-d H:i:s');
			$mileage_end_time = date('Y-m-d H:i:s');

			$check_mileage_query = $db->prepare("
				SELECT mileage_id, mileage_amount_start
				FROM idk_mileage
				WHERE mileage_employee_id = :mileage_employee_id AND mileage_end_time IS NULL");

			$check_mileage_query->execute(array(
				':mileage_employee_id' => $mileage_employee_id
			));

			$number_of_rows = $check_mileage_query->rowCount();

			if ($number_of_rows != 0) {

				$mileage = $check_mileage_query->fetch();
				$mileage_id = $mileage['mileage_id'];
				$mileage_amount_start_from_db = $mileage['mileage_amount_start'];

				if ($mileage_amount_start >= $mileage_amount_start_from_db) {
					$query = $db->prepare("
						UPDATE idk_mileage
						SET mileage_amount_end = :mileage_amount_end, mileage_end_time = :mileage_end_time
						WHERE mileage_id = :mileage_id");

					$query->execute(array(
						':mileage_id' => $mileage_id,
						':mileage_end_time' => $mileage_end_time,
						':mileage_amount_end' => $mileage_amount_start
					));
				} else {
					header("Location: mileage?page=list&mess=2&mileage=${mileage_amount_start_from_db}");
					exit;
				}
			}

			$query = $db->prepare("
				INSERT INTO idk_mileage
					(mileage_employee_id, mileage_amount_start, mileage_start_time)
				VALUES
					(:mileage_employee_id, :mileage_amount_start, :mileage_start_time)");

			$query->execute(array(
				':mileage_employee_id' => $mileage_employee_id,
				':mileage_amount_start' => 0,
				':mileage_start_time' => $mileage_start_time
			));

			$mileage_id = $db->lastInsertId();

			//Add to log
			$log_desc = "Restartovao kilometražu";
			$datetime = date('Y-m-d H:i:s');
			addLog($logged_employee_id, $log_desc, $datetime);

			header("Location: mileage?page=list&mess=1&employee_id=${mileage_employee_id}");

			break;

			/*-----------------------------------------------------------------------------------------
								MILEAGE END
	-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
							ROUTES START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					ADD ROUTE
			 * *********************************************************/
		case "add_route":

			$route_day = !empty($_POST['route_day']) ? $_POST['route_day'] : NULL;
			$route_week = !empty($_POST['route_week']) ? $_POST['route_week'] : 0;
			$route_employee_id = !empty($_POST['route_employee_id']) ? $_POST['route_employee_id'] : NULL;
			$route_client_ids = !empty($_POST['route_client_id']) ? $_POST['route_client_id'] : NULL; //Array
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');

			$check_route_query = $db->prepare("
				SELECT route_id
				FROM idk_route
				WHERE route_day = :route_day AND route_employee_id = :route_employee_id AND route_week = :route_week");

			$check_route_query->execute(array(
				':route_day' => $route_day,
				':route_week' => $route_week,
				':route_employee_id' => $route_employee_id
			));

			$number_of_rows_route = $check_route_query->rowCount();

			if ($number_of_rows_route == 0) {

				$check_query = $db->prepare("
					SELECT route_id
					FROM idk_route
					WHERE route_day = :route_day AND route_employee_id = :route_employee_id AND route_week = :route_week");

				$check_query->execute(array(
					':route_day' => $route_day,
					':route_week' => 0,
					':route_employee_id' => $route_employee_id
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0 and isset($route_client_ids) and count($route_client_ids) > 0) {

					$query = $db->prepare("
						INSERT INTO idk_route
							(route_day, route_week, route_employee_id, created_at, updated_at)
						VALUES
							(:route_day, :route_week, :route_employee_id, :created_at, :updated_at)");

					$query->execute(array(
						':route_day' => $route_day,
						':route_week' => $route_week,
						':route_employee_id' => $route_employee_id,
						':created_at' => $created_at,
						':updated_at' => $updated_at
					));

					$route_id = $db->lastInsertId();

					for ($i = 0; $i < count($route_client_ids); $i++) {
						$query = $db->prepare("
							INSERT INTO idk_route_client
								(rc_route_id, rc_client_id, rc_client_position)
							VALUES
								(:rc_route_id, :rc_client_id, :rc_client_position)");

						$query->execute(array(
							':rc_route_id' => $route_id,
							':rc_client_id' => $route_client_ids[$i],
							':rc_client_position' => ($i + 1)
						));
					}

					$check_route_employee_query = $db->prepare("
						SELECT employee_first_name, employee_last_name
						FROM idk_employee
						WHERE employee_id = :employee_id");

					$check_route_employee_query->execute(array(
						':employee_id' => $route_employee_id
					));

					$employee_row = $check_route_employee_query->fetch();
					$employee_first_name = $employee_row['employee_first_name'];
					$employee_last_name = $employee_row['employee_last_name'];

					//Add to log
					$log_desc = "Dodao novu rutu za komercijalistu: ${employee_first_name} ${employee_last_name}";
					$datetime = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $datetime);

					header("Location: routes?page=list&mess=1");
				} else {

					header("Location: routes?page=list&mess=9");
				}
			} else {

				$check_route_archived_query = $db->prepare("
					SELECT route_id
					FROM idk_route
					WHERE route_day = :route_day AND route_employee_id = :route_employee_id AND route_active = :route_active AND route_week = :route_week");

				$check_route_archived_query->execute(array(
					':route_day' => $route_day,
					':route_employee_id' => $route_employee_id,
					':route_active' => 0,
					':route_week' => $route_week
				));

				$number_of_rows_route_archived = $check_route_archived_query->rowCount();

				if ($number_of_rows_route_archived != 0) {

					header("Location: routes?page=list&mess=8");
				} else {

					header("Location: routes?page=list&mess=2");
				}
			}

			break;



			/************************************************************
			 * 					EDIT ROUTE
			 * *********************************************************/
		case "edit_route":

			$route_id = !empty($_POST['route_id']) ? $_POST['route_id'] : NULL;
			$route_day = !empty($_POST['route_day']) ? $_POST['route_day'] : NULL;
			$route_week = !empty($_POST['route_week']) ? $_POST['route_week'] : 0;
			$route_employee_id = !empty($_POST['route_employee_id']) ? $_POST['route_employee_id'] : NULL;
			$route_client_ids = !empty($_POST['route_client_id']) ? $_POST['route_client_id'] : NULL; //Array
			$updated_at = date('Y-m-d H:i:s');

			$check_route_query = $db->prepare("
				SELECT route_id
				FROM idk_route
				WHERE route_id = :route_id");

			$check_route_query->execute(array(
				':route_id' => $route_id
			));

			$number_of_rows_route = $check_route_query->rowCount();

			if ($number_of_rows_route != 0 and isset($route_client_ids) and count($route_client_ids) > 0) {

				$check_query = $db->prepare("
					SELECT route_id
					FROM idk_route
					WHERE route_day = :route_day AND route_employee_id = :route_employee_id AND route_week = :route_week AND route_id != :route_id");

				$check_query->execute(array(
					':route_day' => $route_day,
					':route_employee_id' => $route_employee_id,
					':route_week' => 0,
					':route_id' => $route_id
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {

					$check_query = $db->prepare("
						SELECT route_id
						FROM idk_route
						WHERE route_day = :route_day AND route_employee_id = :route_employee_id AND route_week = :route_week AND route_id != :route_id");

					$check_query->execute(array(
						':route_day' => $route_day,
						':route_employee_id' => $route_employee_id,
						':route_week' => $route_week,
						':route_id' => $route_id
					));

					$number_of_rows = $check_query->rowCount();

					if ($number_of_rows == 0) {

						$query = $db->prepare("
							UPDATE idk_route
							SET route_day = :route_day, route_week = :route_week, route_employee_id = :route_employee_id, updated_at = :updated_at
							WHERE route_id = :route_id");

						$query->execute(array(
							':route_id' => $route_id,
							':route_day' => $route_day,
							':route_week' => $route_week,
							':route_employee_id' => $route_employee_id,
							':updated_at' => $updated_at
						));

						$query = $db->prepare("
							DELETE FROM idk_route_client
							WHERE rc_route_id = :rc_route_id");

						$query->execute(array(
							':rc_route_id' => $route_id
						));

						for ($i = 0; $i < count($route_client_ids); $i++) {
							$query = $db->prepare("
								INSERT INTO idk_route_client
									(rc_route_id, rc_client_id, rc_client_position)
								VALUES
									(:rc_route_id, :rc_client_id, :rc_client_position)");

							$query->execute(array(
								':rc_route_id' => $route_id,
								':rc_client_id' => $route_client_ids[$i],
								':rc_client_position' => ($i + 1)
							));
						}

						$check_route_employee_query = $db->prepare("
							SELECT employee_first_name, employee_last_name
							FROM idk_employee
							WHERE employee_id = :employee_id");

						$check_route_employee_query->execute(array(
							':employee_id' => $route_employee_id
						));

						$employee_row = $check_route_employee_query->fetch();
						$employee_first_name = $employee_row['employee_first_name'];
						$employee_last_name = $employee_row['employee_last_name'];

						//Add to log
						$log_desc = "Uredio rutu s ID brojem: ${route_id}";
						$datetime = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $datetime);

						header("Location: routes?page=list&mess=3");
					} else {

						header("Location: routes?page=list&mess=2");
					}
				} else {

					header("Location: routes?page=list&mess=9");
				}
			} else {

				header("Location: routes?page=list&mess=5");
			}

			break;

			/*-----------------------------------------------------------------------------------------
								ROUTES END
	-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
									SETTINGS START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 							TAX SETTINGS
			 * *********************************************************/

			// ADD TAX
		case "add_tax":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['tax_name'])) {

					$od_data = $_POST['tax_name'];

					//Check if tax exists
					$check_query = $db->prepare("
						SELECT od_data
						FROM idk_product_otherdata
						WHERE od_data = :od_data");

					$check_query->execute(array(
						':od_data' => $od_data
					));

					$number_of_rows = $check_query->rowCount();

					//If it doesn't exist add new:
					if ($number_of_rows == 0) {

						$od_title = 'Porez';
						$od_value = $_POST['tax_value'];
						$od_group = 1;

						$query = $db->prepare("
							INSERT INTO idk_product_otherdata
								(od_title, od_data, od_value, od_group)
							VALUES
								(:od_title, :od_data, :od_value, :od_group)");

						$query->execute(array(
							':od_data' 	=> $od_data,
							':od_title' => $od_title,
							':od_value' => $od_value,
							':od_group' => $od_group
						));

						//Add to log
						$log_desc = "Dodao novu vrstu poreza: " . $od_data . "";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: settings?page=tax&mess=2");
					} else {
						header("Location: settings?page=tax&mess=4");
					}
				} else {
					header("Location: settings?page=tax&mess=1");
				}
			}

			break;



			// DELETE TAX
		case "delete_tax":

			$tax_id = $_GET['tax_id'];

			if ($getEmployeeStatus == 1) {


				//Get tax name
				$tax_open_query = $db->prepare("
					SELECT od_data
					FROM idk_product_otherdata
					WHERE od_id = :od_id");

				$tax_open_query->execute(array(
					':od_id' => $tax_id
				));

				$tax_open = $tax_open_query->fetch();

				$od_data = $tax_open['od_data'];


				//Add to log
				$log_desc = "Obrisao porez: " . $od_data . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				//Delete tax from db
				$tax_del_query = $db->prepare("
					DELETE FROM idk_product_otherdata
					WHERE od_id = :od_id");

				$tax_del_query->execute(array(
					':od_id' => $tax_id
				));

				header("Location: settings?page=tax&mess=3");
			}

			break;



			// SET PRIMARY TAX
		case "set_primary_tax":

			$tax_id = $_GET['tax_id'];

			if ($getEmployeeStatus == 1) {


				//Remove default primary tax
				$query = $db->prepare("
					UPDATE idk_product_otherdata
					SET	od_primary = :od_primary
					WHERE od_group = :od_group");

				$query->execute(array(
					':od_primary' => 0,
					':od_group' => 1
				));

				//Add primary tax
				$query = $db->prepare("
					UPDATE idk_product_otherdata
					SET	od_primary = :od_primary
					WHERE od_id = :od_id");

				$query->execute(array(
					':od_primary' => 1,
					':od_id' => $tax_id
				));

				header("Location: settings?page=tax&mess=5");
			}

			break;



			/************************************************************
			 * 							CURRENCY SETTINGS
			 * *********************************************************/

			// ADD CURRENCY
		case "add_currency":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['currency_name']) and !empty($_POST['currency_code'])) {

					$od_data = $_POST['currency_name'];

					//Check if currency exists
					$check_query = $db->prepare("
						SELECT od_data
						FROM idk_product_otherdata
						WHERE od_data = :od_data");

					$check_query->execute(array(
						':od_data' => $od_data
					));

					$number_of_rows = $check_query->rowCount();

					//If it doesn't exist add new:
					if ($number_of_rows == 0) {

						$od_title = 'Valuta';
						$od_other_info = $_POST['currency_code'];
						$od_group = 2;

						$query = $db->prepare("
							INSERT INTO idk_product_otherdata
								(od_title, od_data, od_other_info, od_group)
							VALUES
								(:od_title, :od_data, :od_other_info, :od_group)");

						$query->execute(array(
							':od_data' 	=> $od_data,
							':od_title' => $od_title,
							':od_other_info' => $od_other_info,
							':od_group' => $od_group
						));

						//Add to log
						$log_desc = "Dodao novu valutu: " . $od_data . " - " . $od_other_info . "";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: settings?page=currency&mess=2");
					} else {
						header("Location: settings?page=currency&mess=4");
					}
				} else {
					header("Location: settings?page=currency&mess=1");
				}
			}

			break;



			// DELETE CURRENCY
		case "delete_currency":

			$curr_id = $_GET['curr_id'];

			if ($getEmployeeStatus == 1) {

				//Get currency name
				$currency_open_query = $db->prepare("
					SELECT od_data
					FROM idk_product_otherdata
					WHERE od_id = :od_id");

				$currency_open_query->execute(array(
					':od_id' => $curr_id
				));

				$currency_open = $currency_open_query->fetch();

				$od_data = $currency_open['od_data'];

				//Add to log
				$log_desc = "Obrisao valutu: " . $od_data . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				//Delete currency from db
				$currency_del_query = $db->prepare("
					DELETE FROM idk_product_otherdata
					WHERE od_id = :od_id");

				$currency_del_query->execute(array(
					':od_id' => $curr_id
				));

				header("Location: settings?page=currency&mess=3");
			}

			break;



			// SET PRIMARY CURRENCY
		case "set_primary_currency":

			$curr_id = $_GET['curr_id'];

			if ($getEmployeeStatus == 1) {

				//Remove default primary currency
				$query = $db->prepare("
					UPDATE idk_product_otherdata
					SET	od_primary = :od_primary
					WHERE od_group = :od_group");

				$query->execute(array(
					':od_primary' => 0,
					':od_group' => 2
				));

				//Add primary currency
				$query = $db->prepare("
					UPDATE idk_product_otherdata
					SET	od_primary = :od_primary
					WHERE od_id = :od_id");

				$query->execute(array(
					':od_primary' => 1,
					':od_id' => $curr_id
				));

				header("Location: settings?page=currency&mess=5");
			}

			break;



			/************************************************************
			 * 							UNITS SETTINGS
			 * *********************************************************/

			// ADD UNIT
		case "add_unit":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['unit_name']) and !empty($_POST['unit_code'])) {

					$od_data = $_POST['unit_name'];

					//Check if currency exists
					$check_query = $db->prepare("
						SELECT od_data
						FROM idk_product_otherdata
						WHERE od_data = :od_data");

					$check_query->execute(array(
						':od_data' => $od_data
					));

					$number_of_rows = $check_query->rowCount();

					//If it doesn't exist add new:
					if ($number_of_rows == 0) {

						$od_title = 'Mjerna jedinica';
						$od_other_info = $_POST['unit_code'];
						$od_group = 5;

						$query = $db->prepare("
							INSERT INTO idk_product_otherdata
								(od_title, od_data, od_other_info, od_group)
							VALUES
								(:od_title, :od_data, :od_other_info, :od_group)");

						$query->execute(array(
							':od_data' 	=> $od_data,
							':od_title' => $od_title,
							':od_other_info' => $od_other_info,
							':od_group' => $od_group
						));

						//Add to log
						$log_desc = "Dodao novu mjernu jedinicu: " . $od_data . " - " . $od_other_info . "";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: settings?page=units&mess=2");
					} else {
						header("Location: settings?page=units&mess=4");
					}
				} else {
					header("Location: settings?page=units&mess=1");
				}
			}

			break;



			// DELETE UNIT
		case "delete_unit":

			if ($getEmployeeStatus == 1) {

				$unit_id = $_GET['unit_id'];

				//Get unit name
				$unit_open_query = $db->prepare("
					SELECT od_data
					FROM idk_product_otherdata
					WHERE od_id = :od_id");

				$unit_open_query->execute(array(
					':od_id' => $unit_id
				));

				$unit_open = $unit_open_query->fetch();

				$od_data = $unit_open['od_data'];


				//Add to log
				$log_desc = "Obrisao mjernu jedinicu: " . $od_data . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				//Delete unit from db
				$unit_del_query = $db->prepare("
					DELETE FROM idk_product_otherdata
					WHERE od_id = :od_id");

				$unit_del_query->execute(array(
					':od_id' => $unit_id
				));

				header("Location: settings?page=units&mess=3");
			}

			break;



			// SET PRIMARY UNIT
		case "set_primary_unit":

			if ($getEmployeeStatus == 1) {

				$unit_id = $_GET['unit_id'];

				//Remove default primary unit
				$query = $db->prepare("
					UPDATE idk_product_otherdata
					SET	od_primary = :od_primary
					WHERE od_group = :od_group");

				$query->execute(array(
					':od_primary' => 0,
					':od_group' => 5
				));

				//Add primary unit
				$query = $db->prepare("
					UPDATE idk_product_otherdata
					SET	od_primary = :od_primary
					WHERE od_id = :od_id");

				$query->execute(array(
					':od_primary' => 1,
					':od_id' => $unit_id
				));

				header("Location: settings?page=units&mess=5");
			}

			break;



			/************************************************************
			 * 							EMPLOYEE POSITIONS SETTINGS
			 * *********************************************************/

			// ADD EMPLOYEE POSITION
		case "add_employee_position":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['employee_position_name'])) {

					$od_data = $_POST['employee_position_name'];
					$od_title = 'Pozicija zaposlenika';
					$od_group = 1;

					//Check if currency exists
					$check_query = $db->prepare("
						SELECT od_data
						FROM idk_employee_otherdata
						WHERE od_group = :od_group AND od_data = :od_data");

					$check_query->execute(array(
						':od_group' => $od_group,
						':od_data' => $od_data
					));

					echo $number_of_rows = $check_query->rowCount();

					//If it doesn't exist add new:
					if ($number_of_rows == 0) {

						$query = $db->prepare("
							INSERT INTO idk_employee_otherdata
								(od_title, od_data, od_group)
							VALUES
								(:od_title, :od_data, :od_group)");

						$query->execute(array(
							':od_data' 	=> $od_data,
							':od_title' => $od_title,
							':od_group' => $od_group
						));

						//Add to log
						$log_desc = "Dodao novu poziciju zaposlenika: " . $od_data . "";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: settings?page=employee_positions&mess=2");
					} else {
						header("Location: settings?page=employee_positions&mess=4");
					}
				} else {
					header("Location: settings?page=employee_positions&mess=1");
				}
			}

			break;



			// DELETE EMPLOYEE POSITION
		case "delete_employee_position":

			if ($getEmployeeStatus == 1) {

				$employee_position_id = $_GET['employee_position_id'];

				//Get employee position name
				$employee_position_open_query = $db->prepare("
					SELECT od_data
					FROM idk_employee_otherdata
					WHERE od_id = :od_id");

				$employee_position_open_query->execute(array(
					':od_id' => $employee_position_id
				));

				$employee_position_open = $employee_position_open_query->fetch();

				$od_data = $employee_position_open['od_data'];

				//Add to log
				$log_desc = "Obrisao poziciju zaposlenika: " . $od_data . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				//Delete employee position from db
				$employee_position_del_query = $db->prepare("
					DELETE FROM idk_employee_otherdata
					WHERE od_id = :od_id");

				$employee_position_del_query->execute(array(
					':od_id' => $employee_position_id
				));

				header("Location: settings?page=employee_positions&mess=3");
			}

			break;



			/************************************************************
			 * 						ORDERS SETTINGS
			 * *********************************************************/

			// ADD ORDER STATUS
		case "add_order_status":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['order_status_name']) and !empty($_POST['order_status_color'])) {

					$od_data = $_POST['order_status_name'];
					$od_value = $_POST['order_status_color'];

					//Check if currency exists
					$check_query = $db->prepare("
						SELECT od_data
						FROM idk_order_otherdata
						WHERE od_data = :od_data");

					$check_query->execute(array(
						':od_data' => $od_data
					));

					$number_of_rows = $check_query->rowCount();

					//If it doesn't exist add new:
					if ($number_of_rows == 0) {

						$od_title = 'Status narudžbe';
						$od_group = 1;

						$query = $db->prepare("
							INSERT INTO idk_order_otherdata
								(od_title, od_data, od_value, od_group)
							VALUES
								(:od_title, :od_data, :od_value, :od_group)");

						$query->execute(array(
							':od_data' 	=> $od_data,
							':od_title' => $od_title,
							':od_value' => $od_value,
							':od_group' => $od_group
						));

						//Add to log
						$log_desc = "Dodao novi status narudžbe: " . $od_data . " - " . $od_value . "";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: settings?page=order_status&mess=2");
					} else {
						header("Location: settings?page=order_status&mess=4");
					}
				} else {
					header("Location: settings?page=order_status&mess=1");
				}
			}

			break;



			// DELETE ORDER STATUS
		case "delete_order_status":

			if ($getEmployeeStatus == 1) {

				$order_status_id = $_GET['order_status_id'];

				//Get order status name
				$order_status_open_query = $db->prepare("
					SELECT od_data, od_value
					FROM idk_order_otherdata
					WHERE od_id = :od_id");

				$order_status_open_query->execute(array(
					':od_id' => $order_status_id
				));

				$order_status_open = $order_status_open_query->fetch();

				$od_data = $order_status_open['od_data'];
				$od_value = $order_status_open['od_value'];

				//Add to log
				$log_desc = "Obrisao status narudžbe: " . $od_data . " - " . $od_value;
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				//Delete order status from db
				$order_status_del_query = $db->prepare("
					DELETE FROM idk_order_otherdata
					WHERE od_id = :od_id");

				$order_status_del_query->execute(array(
					':od_id' => $order_status_id
				));

				header("Location: settings?page=order_status&mess=3");
			}

			break;



			// CHANGE ORDER STATUS
		case "change_order_status":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['order_status']) and !empty($_POST['order_id'])) {

					$order_id = $_POST['order_id'];
					$order_status = $_POST['order_status'];

					$query = $db->prepare("
						UPDATE idk_order
						SET	order_status = :order_status
						WHERE order_id = :order_id");

					$query->execute(array(
						':order_id' => $order_id,
						':order_status' => $order_status
					));

					//Add to log
					$log_desc = "Promijenio status narudžbe s ID brojem: " . $order_id . "";
					$log_date = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $log_date);

					header("Location: orders?page=list&mess=1");
				} else {
					// header("Location: orders?page=list&mess=2");
					var_dump($_POST['order_status']);
					var_dump($_POST['order_id']);
				}
			}

			break;



			/************************************************************
			 * 					IDENTITY SETTINGS
			 * *********************************************************/

			// ADD OWNER
		case "add_owner":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['owner_name'])) {

					$owner_name = $_POST['owner_name'];
					$owner_business_type = $_POST['owner_business_type'];
					$owner_id_number = $_POST['owner_id_number'];
					$owner_pdv_number = $_POST['owner_pdv_number'];
					$owner_address = $_POST['owner_address'];
					$owner_postal_code = $_POST['owner_postal_code'];
					$owner_city = $_POST['owner_city'];
					$owner_region = $_POST['owner_region'];
					$owner_country = $_POST['owner_country'];
					$owner_other_info = $_POST['owner_other_info'];
					$owner_color = $_POST['owner_color'];
					$created_at = date('Y-m-d H:i:s');
					$updated_at = date('Y-m-d H:i:s');

					//Upload and save owner_image
					$owner_image_final = uploadImage('owner', 0, 0, 0);

					$query_statement = "
						INSERT INTO idk_owner
							(owner_name, owner_id_number, owner_business_type, owner_pdv_number, owner_postal_code, owner_image, owner_color, owner_address, owner_city, owner_country, owner_region, owner_other_info, created_at, updated_at)
						VALUES
							(:owner_name, :owner_id_number, :owner_business_type, :owner_pdv_number, :owner_postal_code, :owner_image, :owner_color, :owner_address, :owner_city, :owner_country, :owner_region, :owner_other_info, :created_at, :updated_at)";

					$query_array = [
						':owner_name' => $owner_name,
						':owner_id_number' => $owner_id_number,
						':owner_business_type' => $owner_business_type,
						':owner_pdv_number' => $owner_pdv_number,
						':owner_postal_code' => $owner_postal_code,
						':owner_image' => $owner_image_final,
						':owner_color' => $owner_color,
						':owner_address' => $owner_address,
						':owner_city' => $owner_city,
						':owner_country' => $owner_country,
						':owner_other_info' => $owner_other_info,
						':owner_region' => $owner_region,
						':created_at' => $created_at,
						':updated_at' => $updated_at
					];


					$query = $db->prepare($query_statement);

					$query->execute($query_array);

					/* Add to log */
					$log_desc = "Dodao identitet vlasnika: " . $owner_name . "";
					$log_date = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $log_date);

					header("Location: settings?page=identity&mess=2");
				} else {
					header("Location: settings?page=identity&mess=1");
				}
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			// EDIT OWNER
		case "edit_owner":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['owner_name'])) {

					$owner_id = $_POST['owner_id'];
					$owner_name = $_POST['owner_name'];
					$owner_business_type = $_POST['owner_business_type'];
					$owner_id_number = $_POST['owner_id_number'];
					$owner_pdv_number = $_POST['owner_pdv_number'];
					$owner_address = $_POST['owner_address'];
					$owner_postal_code = $_POST['owner_postal_code'];
					$owner_city = $_POST['owner_city'];
					$owner_region = $_POST['owner_region'];
					$owner_country = $_POST['owner_country'];
					$owner_other_info = $_POST['owner_other_info'];
					$owner_color = $_POST['owner_color'];
					$updated_at = date('Y-m-d H:i:s');

					//Upload and save owner_image
					$owner_image_final = uploadImage('owner', 1, $owner_id, 0);

					$query_statement = "
						UPDATE idk_owner
						SET owner_name = :owner_name, owner_id_number = :owner_id_number, owner_business_type = :owner_business_type, owner_pdv_number = :owner_pdv_number, owner_postal_code = :owner_postal_code, owner_image = :owner_image, owner_color = :owner_color, owner_address = :owner_address, owner_city = :owner_city, owner_country = :owner_country, owner_other_info = :owner_other_info, owner_region = :owner_region, updated_at = :updated_at
						WHERE owner_id = :owner_id";

					$query_array = [
						':owner_id' => $owner_id,
						':owner_name' => $owner_name,
						':owner_id_number' => $owner_id_number,
						':owner_business_type' => $owner_business_type,
						':owner_pdv_number' => $owner_pdv_number,
						':owner_postal_code' => $owner_postal_code,
						':owner_image' => $owner_image_final,
						':owner_color' => $owner_color,
						':owner_address' => $owner_address,
						':owner_city' => $owner_city,
						':owner_country' => $owner_country,
						':owner_other_info' => $owner_other_info,
						':owner_region' => $owner_region,
						':updated_at' => $updated_at
					];


					$query = $db->prepare($query_statement);

					$query->execute($query_array);

					/* Add to log */
					$log_desc = "Uredio identitet vlasnika: " . $owner_name . "";
					$log_date = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $log_date);

					header("Location: settings?page=identity&mess=3");
				} else {
					header("Location: settings?page=identity&mess=1");
				}
			} else {
				echo '
					<div class="alert material-alert material-alert_danger">
						<h4>NEMATE PRIVILEGIJE!</h4>
						<p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
						<br>
						<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
					</div>
				';
			}

			break;



			/************************************************************
			 * 					PRODUCTS DEFAULT PRIMARY IMAGE SETTINGS
			 * *********************************************************/

			// PRODUCT DEFAULT PRIMARY IMAGE
		case "set_product_default_primary_image":

			if ($getEmployeeStatus == 1) {

				$od_title = 'Zadana primarna slika proizvoda';
				$od_data = 'Primarna slika';
				$od_group = 4;

				//Upload and save product_default_primary_image
				if (isset($_FILES['product_default_primary_image']) and $_FILES['product_default_primary_image']['size'] !== 0) {

					//Don't delete old image

					$product_default_primary_image = $_FILES['product_default_primary_image'];

					//File properties
					$file_name = $product_default_primary_image['name'];
					$file_tmp = $product_default_primary_image['tmp_name'];
					$file_size = $product_default_primary_image['size'];
					$file_error = $product_default_primary_image['error'];

					//File extension
					$file_ext = explode('.', $file_name);
					$file_ext = strtolower(end($file_ext));

					$allowed = array('jpg', 'png');

					if (in_array($file_ext, $allowed)) {
						if ($file_error === 0) {
							//Size 2MB
							if ($file_size <= 2097152) {

								$product_default_primary_image_final = uniqid('', true) . '.' . $file_ext;
								$file_destination = './files/products/images/' . $product_default_primary_image_final;

								if (move_uploaded_file($file_tmp, $file_destination)) {
									$path_to_image_directory = "./files/products/images/";
									$path_to_thumbs_directory = "./files/products/thumbs/";
									$final_width_of_image = 200;

									if (preg_match('/[.](jpg)$/', $product_default_primary_image_final)) {
										$im = imagecreatefromjpeg($path_to_image_directory . $product_default_primary_image_final);
									} else if (preg_match('/[.](gif)$/', $product_default_primary_image_final)) {
										$im = imagecreatefromgif($path_to_image_directory . $product_default_primary_image_final);
									} else if (preg_match('/[.](png)$/', $product_default_primary_image_final)) {
										$im = imagecreatefrompng($path_to_image_directory . $product_default_primary_image_final);
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

									imagejpeg($nm, $path_to_thumbs_directory . $product_default_primary_image_final);
								}
							}
						}
					}
				} else {
					$product_default_primary_image_final = NULL;
				}

				//Check if row exists
				$check_query = $db->prepare("
					SELECT od_title
					FROM idk_product_otherdata
					WHERE od_title = :od_title AND od_group = :od_group");

				$check_query->execute(array(
					':od_title' => $od_title,
					':od_group' => $od_group
				));

				$number_of_rows = $check_query->rowCount();

				//If it doesn't exist add new:
				if ($number_of_rows == 0) {

					$query = $db->prepare("
						INSERT INTO idk_product_otherdata
							(od_title, od_data, od_other_info, od_group, od_primary)
						VALUES
							(:od_title, :od_data, :od_other_info, :od_group, :od_primary)");

					$query->execute(array(
						':od_data' 	=> $od_data,
						':od_title' => $od_title,
						':od_other_info' => $product_default_primary_image_final,
						':od_group' => $od_group,
						':od_primary' => 1
					));

					//Add to log
					$log_desc = "Postavio zadanu primarnu sliku proizvoda: " . $product_default_primary_image_final . "";
					$log_date = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $log_date);

					header("Location: settings?page=products_default_primary_image&mess=2");
				} else {
					$query = $db->prepare("
						UPDATE idk_product_otherdata
						SET	od_other_info = :od_other_info
						WHERE od_title = :od_title AND od_group = :od_group");

					$query->execute(array(
						':od_title' => $od_title,
						':od_other_info' => $product_default_primary_image_final,
						':od_group' => $od_group
					));

					//Add to log
					$log_desc = "Postavio zadanu primarnu sliku proizvoda: " . $product_default_primary_image_final . "";
					$log_date = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $log_date);

					header("Location: settings?page=products_default_primary_image&mess=2");
				}
			}

			break;



			/************************************************************
			 * 					DATACOLLECTIONS SETTINGS
			 * *********************************************************/

			// ADD DATACOLLECTION TYPE
		case "add_datacollection_type":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['dc_type_name'])) {

					$dc_type_name = $_POST['dc_type_name'];

					//Check if currency exists
					$check_query = $db->prepare("
							SELECT dc_type_name
							FROM idk_datacollection_type
							WHERE dc_type_name = :dc_type_name");

					$check_query->execute(array(
						':dc_type_name' => $dc_type_name
					));

					echo $number_of_rows = $check_query->rowCount();

					//If it doesn't exist add new:
					if ($number_of_rows == 0) {

						$query = $db->prepare("
								INSERT INTO idk_datacollection_type
									(dc_type_name)
								VALUES
									(:dc_type_name)");

						$query->execute(array(
							':dc_type_name' 	=> $dc_type_name
						));

						//Add to log
						$log_desc = "Dodao novu vrstu informacije: " . $dc_type_name . "";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: settings?page=datacollection_type&mess=2");
					} else {
						header("Location: settings?page=datacollection_type&mess=4");
					}
				} else {
					header("Location: settings?page=datacollection_type&mess=1");
				}
			}

			break;



			// DELETE DATACOLLECTION TYPE
		case "delete_datacollection_type":

			if ($getEmployeeStatus == 1) {

				$dc_type_id = $_GET['dc_type_id'];

				//Get employee position name
				$open_query = $db->prepare("
						SELECT dc_type_name
						FROM idk_datacollection_type
						WHERE dc_type_id = :dc_type_id");

				$open_query->execute(array(
					':dc_type_id' => $dc_type_id
				));

				$open_row = $open_query->fetch();

				$dc_type_name = $open_row['dc_type_name'];

				//Add to log
				$log_desc = "Obrisao vrstu informacije sa terena: " . $dc_type_name . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				//Delete employee position from db
				$del_query = $db->prepare("
						DELETE FROM idk_datacollection_type
						WHERE dc_type_id = :dc_type_id");

				$del_query->execute(array(
					':dc_type_id' => $dc_type_id
				));

				header("Location: settings?page=datacollection_type&mess=3");
			}

			break;



			/************************************************************
			 * 					CITY SETTINGS
			 * *********************************************************/

			// ADD CITY
		case "add_city":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['city_name'])) {

					$city_name = $_POST['city_name'];

					//Check if city exists
					$check_query = $db->prepare("
							SELECT location_id
							FROM idk_location
							WHERE location_name = :location_name AND location_type = :location_type");

					$check_query->execute(array(
						':location_name' => $city_name,
						':location_type' => 1
					));

					echo $number_of_rows = $check_query->rowCount();

					//If it doesn't exist add new:
					if ($number_of_rows == 0) {

						$query = $db->prepare("
								INSERT INTO idk_location
									(location_name, location_type)
								VALUES
									(:location_name, :location_type)");

						$query->execute(array(
							':location_name' 	=> $city_name,
							':location_type' 	=> 1
						));

						//Add to log
						$log_desc = "Dodao novu općinu: " . $city_name . "";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: settings?page=city&mess=2");
					} else {
						header("Location: settings?page=city&mess=4");
					}
				} else {
					header("Location: settings?page=city&mess=1");
				}
			}

			break;



			// DELETE CITY
		case "delete_city":

			if ($getEmployeeStatus == 1) {

				$location_id = $_GET['location_id'];

				//Get city name
				$open_query = $db->prepare("
						SELECT location_name
						FROM idk_location
						WHERE location_id = :location_id");

				$open_query->execute(array(
					':location_id' => $location_id
				));

				$open_row = $open_query->fetch();

				$location_name = $open_row['location_name'];

				//Add to log
				$log_desc = "Obrisao općinu: " . $location_name . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				//Delete city from db
				$del_query = $db->prepare("
						DELETE FROM idk_location
						WHERE location_id = :location_id");

				$del_query->execute(array(
					':location_id' => $location_id
				));

				header("Location: settings?page=city&mess=3");
			}

			break;



			/************************************************************
			 * 					REGION SETTINGS
			 * *********************************************************/

			// ADD REGION
		case "add_region":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['region_name'])) {

					$region_name = $_POST['region_name'];

					//Check if region exists
					$check_query = $db->prepare("
							SELECT location_id
							FROM idk_location
							WHERE location_name = :location_name AND location_type = :location_type");

					$check_query->execute(array(
						':location_name' => $region_name,
						':location_type' => 2
					));

					echo $number_of_rows = $check_query->rowCount();

					//If it doesn't exist add new:
					if ($number_of_rows == 0) {

						$query = $db->prepare("
								INSERT INTO idk_location
									(location_name, location_type)
								VALUES
									(:location_name, :location_type)");

						$query->execute(array(
							':location_name' 	=> $region_name,
							':location_type' 	=> 2
						));

						//Add to log
						$log_desc = "Dodao novu regiju: " . $region_name . "";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: settings?page=region&mess=2");
					} else {
						header("Location: settings?page=region&mess=4");
					}
				} else {
					header("Location: settings?page=region&mess=1");
				}
			}

			break;



			// DELETE REGION
		case "delete_region":

			if ($getEmployeeStatus == 1) {

				$location_id = $_GET['location_id'];

				//Get region name
				$open_query = $db->prepare("
						SELECT location_name
						FROM idk_location
						WHERE location_id = :location_id");

				$open_query->execute(array(
					':location_id' => $location_id
				));

				$open_row = $open_query->fetch();

				$location_name = $open_row['location_name'];

				//Add to log
				$log_desc = "Obrisao regiju: " . $location_name . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				//Delete region from db
				$del_query = $db->prepare("
						DELETE FROM idk_location
						WHERE location_id = :location_id");

				$del_query->execute(array(
					':location_id' => $location_id
				));

				header("Location: settings?page=region&mess=3");
			}

			break;



			/************************************************************
			 * 					COUNTRY SETTINGS
			 * *********************************************************/

			// ADD COUNTRY
		case "add_country":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['country_name'])) {

					$country_name = $_POST['country_name'];

					//Check if country exists
					$check_query = $db->prepare("
							SELECT location_id
							FROM idk_location
							WHERE location_name = :location_name AND location_type = :location_type");

					$check_query->execute(array(
						':location_name' => $country_name,
						':location_type' => 3
					));

					echo $number_of_rows = $check_query->rowCount();

					//If it doesn't exist add new:
					if ($number_of_rows == 0) {

						$query = $db->prepare("
								INSERT INTO idk_location
									(location_name, location_type)
								VALUES
									(:location_name, :location_type)");

						$query->execute(array(
							':location_name' 	=> $country_name,
							':location_type' 	=> 3
						));

						//Add to log
						$log_desc = "Dodao novu državu: " . $country_name . "";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: settings?page=country&mess=2");
					} else {
						header("Location: settings?page=country&mess=4");
					}
				} else {
					header("Location: settings?page=country&mess=1");
				}
			}

			break;



			// DELETE COUNTRY
		case "delete_country":

			if ($getEmployeeStatus == 1) {

				$location_id = $_GET['location_id'];

				//Get region name
				$open_query = $db->prepare("
						SELECT location_name
						FROM idk_location
						WHERE location_id = :location_id");

				$open_query->execute(array(
					':location_id' => $location_id
				));

				$open_row = $open_query->fetch();

				$location_name = $open_row['location_name'];

				//Add to log
				$log_desc = "Obrisao državu: " . $location_name . "";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				//Delete region from db
				$del_query = $db->prepare("
						DELETE FROM idk_location
						WHERE location_id = :location_id");

				$del_query->execute(array(
					':location_id' => $location_id
				));

				header("Location: settings?page=country&mess=3");
			}

			break;

			/*-----------------------------------------------------------------------------------------
			SETTINGS END
			-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
									SUPPLIERS START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 							ADD SUPPLIER
			 * *********************************************************/
		case "add_supplier":

			if ($getEmployeeStatus == 1) {

				if (!empty($_POST['supplier_name'])) {

					$supplier_name = $_POST['supplier_name'];

					//Check if tax exists
					$check_query = $db->prepare("
						SELECT supplier_id
						FROM idk_supplier
						WHERE supplier_name = :supplier_name");

					$check_query->execute(array(
						':supplier_name' => $supplier_name
					));

					$number_of_rows = $check_query->rowCount();

					//If it doesn't exist add new:
					if ($number_of_rows == 0) {

						$query = $db->prepare("
							INSERT INTO idk_supplier
								(supplier_name)
							VALUES
								(:supplier_name)");

						$query->execute(array(
							':supplier_name' 	=> $supplier_name
						));

						//Add to log
						$log_desc = "Dodao novog dobavljača: ${supplier_name}";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: suppliers?page=list&mess=2");
					} else {
						header("Location: suppliers?page=list&mess=4");
					}
				} else {
					header("Location: suppliers?page=list&mess=1");
				}
			}

			break;



			/************************************************************
			 * 							DELETE SUPPLIER
			 * *********************************************************/
		case "delete_supplier":

			if ($getEmployeeStatus == 1) {

				$supplier_id = $_GET['supplier_id'];

				//Get supplier name
				$supplier_open_query = $db->prepare("
					SELECT supplier_name
					FROM idk_supplier
					WHERE supplier_id = :supplier_id");

				$supplier_open_query->execute(array(
					':supplier_id' => $supplier_id
				));

				$supplier_open = $supplier_open_query->fetch();

				$supplier_name = $supplier_open['supplier_name'];

				//Add to log
				$log_desc = "Obrisao dobavljača: ${supplier_name}";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				//Delete supplier from db
				$supplier_del_query = $db->prepare("
					DELETE FROM idk_supplier
					WHERE supplier_id = :supplier_id");

				$supplier_del_query->execute(array(
					':supplier_id' => $supplier_id
				));

				header("Location: suppliers?page=list&mess=3");
			}

			break;

			/*-----------------------------------------------------------------------------------------
							SUPPLIERS END
							-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
							NOTIFICATIONS START
							-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					DELETE ALL ORDERS NOTIFICATIONS
			 * *********************************************************/

		case "del_orders_notifications":

			$query = $db->prepare("
				DELETE FROM idk_notifications
				WHERE notification_employeeid = :notification_employeeid AND notification_type = :notification_type");

			$query->execute(array(
				':notification_employeeid' => $logged_employee_id,
				':notification_type' => 3
			));

			header("Location: {$_SERVER['HTTP_REFERER']}");

			break;



			/************************************************************
			 * 					DELETE ALL OTHER NOTIFICATIONS
			 * *********************************************************/

		case "del_other_notifications":

			$query = $db->prepare("
				DELETE FROM idk_notifications
				WHERE notification_employeeid = :notification_employeeid AND notification_type = :notification_type");

			$query->execute(array(
				':notification_employeeid' => $logged_employee_id,
				':notification_type' => 4
			));

			header("Location: {$_SERVER['HTTP_REFERER']}");

			break;



			/************************************************************
			 * 					MARK ORDERS NOTIFICATIONS READ
			 * *********************************************************/

		case "orders_notifications_mark_read":

			$notification_employeeid = (int) $_POST['id'];

			$query = $db->prepare("
				UPDATE idk_notifications
				SET	notification_status = :notification_status
				WHERE notification_employeeid = :notification_employeeid AND notification_datetime <= NOW() AND notification_type = :notification_type");

			$query->execute(array(
				':notification_status' => 2,
				':notification_employeeid' => $notification_employeeid,
				':notification_type' => 3
			));

			break;



			/************************************************************
			 * 					MARK OTHER NOTIFICATIONS READ
			 * *********************************************************/

		case "other_notifications_mark_read":

			$notification_employeeid = (int) $_POST['id'];

			$query = $db->prepare("
				UPDATE idk_notifications
				SET	notification_status = :notification_status
				WHERE notification_employeeid = :notification_employeeid AND notification_datetime <= NOW() AND notification_type = :notification_type");

			$query->execute(array(
				':notification_status' => 2,
				':notification_employeeid' => $notification_employeeid,
				':notification_type' => 4
			));

			break;

			/*-----------------------------------------------------------------------------------------
							NOTIFICATIONS END
-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
							MESSAGES START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					SEND MESSAGE
			 * *********************************************************/
		case "send_message":

			$message_subject = $_POST['message_subject'];
			$message_text = $_POST['message_text'];
			$message_sentid = $_POST['message_sentid'];
			$message_datetime = date('Y-m-d H:i:s');

			$query_message = $db->prepare("
				INSERT INTO idk_messages
					(message_subject, message_text, message_sentid, message_status, message_datetime)
				VALUES
					(:message_subject, :message_text, :message_sentid, :message_status, :message_datetime)");

			$query_message->execute(array(
				':message_subject' => $message_subject,
				':message_text' => $message_text,
				':message_sentid' => $message_sentid,
				':message_datetime' => $message_datetime,
				':message_status' => 1
			));

			//Get last ID
			$mu_messageid = $db->lastInsertId();

			//Who receives email
			foreach ($_POST['mu_employeeid'] as $mess_to_array) {

				$query_send = $db->prepare("
					INSERT INTO idk_messages_users
						(mu_messageid, mu_employeeid, mu_status)
					VALUES
						(:mu_messageid, :mu_employeeid, :mu_status)");

				$query_send->execute(array(
					':mu_messageid' => $mu_messageid,
					':mu_employeeid' => $mess_to_array,
					':mu_status' => 0
				));

				//Get User Info
				$user_query = $db->prepare("
					SELECT employee_first_name, employee_last_name, employee_login_email
					FROM idk_employee
					WHERE employee_id = :employee_id");

				$user_query->execute(array(
					':employee_id' => $mess_to_array
				));

				$user = $user_query->fetch();

				$employee_first_name = $user['employee_first_name'];
				$employee_last_name = $user['employee_last_name'];
				$employee_login_email = $user['employee_login_email'];

				//Send email to user
				// 		$mail_email = $employee_login_email;
				// 		$mail_name = $employee_first_name . ' ' . $employee_last_name;
				// 		$mail_subject = "Imate novu poruku - IDK CRM";
				// 		$mail_url = "" . getSiteUrlr() . "idkadmin/messages?page=open&id=" . $mu_messageid . "";
				// 		$mail_body = "
				// 				<p>Imate novu poruku: " . $message_text . "</p>
				// 				<p>Detalji poruke: " . $mail_url . "</p>
				// ";
				// 		$mail_altbody = "
				// 				<p>Imate novu poruku: " . $message_text . "</p>
				// 				<p>Detalji poruke: " . $mail_url . "</p>
				// ";

				// 		sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);

				//Add to log
				$log_desc = "Poslao poruku korisniku: ${employee_first_name} ${employee_last_name}";
				addLog($logged_employee_id, $log_desc, $message_datetime);
			}

			header("Location: messages?page=list&mess=1");

			break;



			/************************************************************
			 * 					REPLY TO MESSAGE
			 * *********************************************************/
		case "reply_message":

			$message_subject = $_POST['message_subject'];
			$message_text = $_POST['message_text'];
			$reply_text = $_POST['reply_text'];
			$message_text_new = $reply_text . '|' . $message_text;
			$message_sentid = $_POST['message_sentid'];
			$message_datetime = date('Y-m-d H:i:s');

			$query_message = $db->prepare("
				INSERT INTO idk_messages
					(message_subject, message_text, message_sentid, message_status, message_datetime)
				VALUES
					(:message_subject, :message_text, :message_sentid, :message_status, :message_datetime)");

			$query_message->execute(array(
				':message_subject' => $message_subject,
				':message_text' => $message_text_new,
				':message_sentid' => $message_sentid,
				':message_datetime' => $message_datetime,
				':message_status' => 1
			));

			//Get last ID
			$mu_messageid = $db->lastInsertId();

			//Who receives email
			foreach ($_POST['mu_employeeid'] as $mess_to_array) {

				$query_send = $db->prepare("
					INSERT INTO idk_messages_users
						(mu_messageid, mu_employeeid, mu_status)
					VALUES
						(:mu_messageid, :mu_employeeid, :mu_status)");

				$query_send->execute(array(
					':mu_messageid' => $mu_messageid,
					':mu_employeeid' => $mess_to_array,
					':mu_status' => 0
				));

				//Get User Info
				$user_query = $db->prepare("
					SELECT employee_first_name, employee_last_name, employee_login_email
					FROM idk_employee
					WHERE employee_id = :employee_id");

				$user_query->execute(array(
					':employee_id' => $mess_to_array
				));

				$user = $user_query->fetch();

				$employee_first_name = $user['employee_first_name'];
				$employee_last_name = $user['employee_last_name'];
				$employee_login_email = $user['employee_login_email'];

				//Send email to user
				// 		$mail_email = $employee_login_email;
				// 		$mail_name = $employee_first_name . ' ' . $employee_last_name;
				// 		$mail_subject = "Imate novu poruku - IDK CRM";
				// 		$mail_url = "" . getSiteUrlr() . "idkadmin/messages?page=open&id=" . $mu_messageid . "";
				// 		$mail_body = "
				// 				<p>Imate novu poruku: " . $message_text . "</p>
				// 				<p>Detalji poruke: " . $mail_url . "</p>
				// ";
				// 		$mail_altbody = "
				// 				<p>Imate novu poruku: " . $message_text . "</p>
				// 				<p>Detalji poruke: " . $mail_url . "</p>
				// ";

				// 		sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);

				//Add to log
				$log_desc = "Poslao poruku korisniku: ${employee_first_name} ${employee_last_name}";
				addLog($logged_employee_id, $log_desc, $message_datetime);
			}

			header("Location: messages?page=list&mess=1");

			break;


			/*-----------------------------------------------------------------------------------------
							MESSAGES END
-----------------------------------------------------------------------------------------*/
	}
}
