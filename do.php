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

			$login_email_or_username = $_POST['login_email_or_username'];
			$login_password = $_POST['login_password'];

			if (isset($_POST['login_rm'])) {
				$login_rm = $_POST['login_rm'];
			} else {
				$login_rm = "off";
			}

			//Check employees
			$login_query = $db->prepare("
				SELECT employee_id, employee_login_email, employee_password, employee_key, employee_status
				FROM idk_employee
				WHERE employee_login_email = :employee_login_email AND employee_active != :employee_active");

			$login_query->execute(array(
				':employee_login_email' => $login_email_or_username,
				':employee_active' => 0
			));

			$number_of_rows = $login_query->rowCount();

			if ($number_of_rows !== 0) {

				$user = $login_query->fetch();

				if (md5($login_password) == $user['employee_password']) {

					if ($login_rm == "on") {

						$month = time() + 2 * 60 * 60 + 60 * 60 * 24 * 30;

						if ($user['employee_status'] == 3) {
							setcookie('idk_session_front_employee_skladistar', $user['employee_key'], $month, '/crm');
						} else {
							setcookie('idk_session_front_employee', $user['employee_key'], $month, '/crm');
						}
					} else {

						$day = time() + 2 * 60 * 60 + 60 * 60 * 24;

						if ($user['employee_status'] == 3) {
							setcookie('idk_session_front_employee_skladistar', $user['employee_key'], $day, '/crm');
						} else {
							setcookie('idk_session_front_employee', $user['employee_key'], $day, '/crm');
						}
					}

					//Update visits statistics
					updateVisitsStats();

					//Add to log
					$employee_id = $user['employee_id'];
					$log_desc = "Zaposlenik se prijavio na stranicu shopa";
					$log_date = date('Y-m-d H:i:s');
					addLog($employee_id, $log_desc, $log_date);

					if ($user['employee_status'] == 3) {
						header("Location: " . getSiteUrlr() . "orders");
					} else {
						header("Location: " . getSiteUrlr() . "");
					}
				} else {
					header("Location: login?mess=2");
				}
			} else {

				//Check clients
				$login_query = $db->prepare("
					SELECT client_id, client_name, client_username, client_password, client_key
					FROM idk_client
					WHERE client_username = :client_username AND client_active != :client_active");

				$login_query->execute(array(
					':client_username' => $login_email_or_username,
					':client_active' => 0
				));

				$user = $login_query->fetch();

				if (md5($login_password) == $user['client_password']) {

					if ($login_rm == "on") {
						$month = time() + 2 * 60 * 60 + 60 * 60 * 24 * 30;
						setcookie('idk_session_front', $user['client_key'], $month, '/crm');
					} else {
						$day = time() + 2 * 60 * 60 + 60 * 60 * 24;
						setcookie('idk_session_front', $user['client_key'], $day, '/crm');
					}

					//Update visits statistics
					updateVisitsStats();

					header("Location: " . getSiteUrlr() . "");
				} else {
					header("Location: login?mess=2");
				}
			}

			break;



			/************************************************************
			 * 						LOGOUT
			 * *********************************************************/
		case "logout":

			if (isset($logged_employee_id) and $logged_employee_id != 0) {

				if (isset($_COOKIE['idk_session_front_client'])) {
					//Add report
					$client_id = $_COOKIE['idk_session_front_client'];
					$order_id = NULL;
					$report_start_time = NULL;
					$report_end_time = date('Y-m-d H:i:s');
					addReport(
						$logged_employee_id,
						$client_id,
						$order_id,
						$report_start_time,
						$report_end_time
					);
				}

				//Add to log
				$log_desc = "Zaposlenik se odjavio sa stranice shopa";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);
			}

			$mileage_start_time = getMileageStartTime($logged_employee_id);

			unset($_COOKIE['idk_session_front']);
			setcookie('idk_session_front', '', time() + 2 * 60 * 60 - 60 * 60 * 24 * 30, '/crm');

			unset($_COOKIE['idk_session_front_employee']);
			setcookie('idk_session_front_employee', '', time() + 2 * 60 * 60 - 60 * 60 * 24 * 30, '/crm');

			unset($_COOKIE['idk_session_front_employee_skladistar']);
			setcookie('idk_session_front_employee_skladistar', '', time() + 2 * 60 * 60 - 60 * 60 * 24 * 30, '/crm');

			unset($_COOKIE['idk_session_front_client']);
			setcookie('idk_session_front_client', '', time() + 2 * 60 * 60 - 60 * 60 * 24 * 30, '/crm');

			header("Location: login?mess=1");

			break;



			/************************************************************
			 * 						SELECT CLIENT
			 * *********************************************************/
		case "select_client":

			$client_id = $_POST['client_id'];

			//Check clients
			$client_query = $db->prepare("
				SELECT client_id, client_name
				FROM idk_client
				WHERE client_id = :client_id");

			$client_query->execute(array(
				':client_id' => $client_id
			));

			$number_of_rows = $client_query->rowCount();

			if ($number_of_rows !== 0) {

				$day = time() + 2 * 60 * 60 + 60 * 60 * 24;
				setcookie('idk_session_front_client', $client_id, $day, '/crm');

				$client = $client_query->fetch();
				$client_id = $client['client_id'];
				$client_name = $client['client_name'];

				//Add to log
				$log_desc = "Odabrao klijenta: ${client_name}";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				//Get logged employee name
				$employee_query = $db->prepare("
					SELECT employee_first_name, employee_last_name
					FROM idk_employee
					WHERE employee_id = :employee_id");

				$employee_query->execute(array(
					':employee_id' => $logged_employee_id
				));

				$employee = $employee_query->fetch();
				$employee_first_name = $employee['employee_first_name'];
				$employee_last_name = $employee['employee_last_name'];

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
					$notification_title = "Zaposlenik ${employee_first_name} ${employee_last_name} odabrao klijenta: ${client_name}!";
					$notification_icon = "briefcase";
					$notification_link = "" . getSiteUrlr() . "idkadmin/clients?page=open&id=${client_id}";
					$notification_type = 4;

					addNotification($notification_title, $notification_icon, $notification_link, $notification_employeeid, $notification_type);
				}

				if (isset($logged_employee_id) and $logged_employee_id != 0) {
					//Add report
					$order_id = NULL;
					$report_start_time = date('Y-m-d H:i:s');
					$report_end_time = NULL;
					addReport(
						$logged_employee_id,
						$client_id,
						$order_id,
						$report_start_time,
						$report_end_time
					);
				}

				header("Location: index");
			} else {
				header("Location: selectClient?mess=1");
			}

			break;



			/*-----------------------------------------------------------------------------------------
								EMPLOYEE START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 							EDIT EMPLOYEE PROFILE
			 * *********************************************************/
		case "edit_employee_profile":

			$employee_id = $_POST['employee_id'];

			if ($employee_id == $logged_employee_id) {

				$employee_first_name = $_POST['employee_first_name'];
				$employee_last_name = $_POST['employee_last_name'];
				$employee_login_email = $_POST['employee_login_email'];
				$employee_address = $_POST['employee_address'];
				$employee_city = $_POST['employee_city'];
				$employee_country = $_POST['employee_country'];
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
				$employee_image_final = uploadImage('employee', 1, $employee_id, 1);

				if (!empty($_POST['employee_password'])) {
					$employee_password = MD5($_POST['employee_password']);
					$query_statement = "
						UPDATE idk_employee
						SET	employee_first_name = :employee_first_name, employee_last_name = :employee_last_name, employee_password = :employee_password, employee_jmbg = :employee_jmbg, employee_login_email = :employee_login_email, employee_address = :employee_address, employee_city = :employee_city, employee_country = :employee_country, employee_image = :employee_image, updated_at = :updated_at
						WHERE employee_id = :employee_id";

					$query_array = [
						':employee_first_name' => $employee_first_name,
						':employee_last_name' => $employee_last_name,
						':employee_password' => $employee_password,
						':employee_login_email' => $employee_login_email,
						':employee_jmbg' => $employee_jmbg,
						':employee_address' => $employee_address,
						':employee_city' => $employee_city,
						':employee_country' => $employee_country,
						':employee_image' => $employee_image_final,
						':updated_at' => $updated_at,
						':employee_id' => $employee_id
					];
				} else {
					$query_statement = "
						UPDATE idk_employee
						SET	employee_first_name = :employee_first_name, employee_last_name = :employee_last_name, employee_jmbg = :employee_jmbg, employee_login_email = :employee_login_email, employee_address = :employee_address, employee_city = :employee_city, employee_country = :employee_country, employee_image = :employee_image, updated_at = :updated_at
						WHERE employee_id = :employee_id";

					$query_array =
						[
							':employee_first_name' => $employee_first_name,
							':employee_last_name' => $employee_last_name,
							':employee_login_email' => $employee_login_email,
							':employee_jmbg' => $employee_jmbg,
							':employee_address' => $employee_address,
							':employee_city' => $employee_city,
							':employee_country' => $employee_country,
							':employee_image' => $employee_image_final,
							':updated_at' => $updated_at,
							':employee_id' => $employee_id
						];
				}

				$query = $db->prepare($query_statement);
				$query->execute($query_array);

				/* UPDATE EMPLOYEE INFO */
				//Update primary phone
				$ei_group = 1; //group=1 is for phone
				$ei_title = 'Primarni telefon';
				$ei_primary = 1;
				$ei_data = $_POST['employee_phone'];

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

				if ($number_of_rows !== 0) {
					$query_phone = $db->prepare("
						UPDATE idk_employee_info
						SET ei_data = :ei_data, updated_at = :updated_at
						WHERE employee_id = :employee_id AND ei_group = :ei_group AND ei_primary = :ei_primary");

					$query_phone->execute(array(
						':ei_group' => $ei_group,
						':ei_data' => $ei_data,
						':updated_at' => $updated_at,
						':ei_primary' => $ei_primary,
						':employee_id' => $employee_id
					));
				} else {
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
				}

				//Add to log
				$log_desc = "Uredio lični profil: ${employee_first_name} ${employee_last_name}";
				$log_date = date('Y-m-d H:i:s');
				addLog($logged_employee_id, $log_desc, $log_date);

				header("Location: settings?mess=1");
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
			 * 							EDIT CLIENT PROFILE
			 * *********************************************************/
		case "edit_client_profile":

			$client_id = $_POST['client_id'];
			$client_name = $_POST['client_name'];
			$client_username = $_POST['client_username'];
			$client_id_number = $_POST['client_id_number'];
			$client_pdv_number = $_POST['client_pdv_number'];
			$client_address = $_POST['client_address'];
			$client_postal_code = $_POST['client_postal_code'];
			$client_city = $_POST['client_city'];
			$client_region = $_POST['client_region'];
			$client_country = $_POST['client_country'];
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');

			//Upload and save client_image
			$client_image_final = uploadImage('client', 1, $client_id, 1);

			//Add client to db
			if (!empty($_POST['client_username']) and !empty($_POST['client_password'])) {
				$client_password = MD5($_POST['client_password']);
				$query_statement = "
					UPDATE idk_client
					SET client_name = :client_name, client_id_number = :client_id_number, client_username = :client_username, client_password = :client_password, client_pdv_number = :client_pdv_number, client_postal_code = :client_postal_code, client_image = :client_image, client_address = :client_address, client_city = :client_city, client_country = :client_country, client_region = :client_region, updated_at = :updated_at
					WHERE client_id = :client_id";

				$query_array = [
					':client_id' => $client_id,
					':client_name' => $client_name,
					':client_id_number' => $client_id_number,
					':client_username' => $client_username,
					':client_password' => $client_password,
					':client_pdv_number' => $client_pdv_number,
					':client_postal_code' => $client_postal_code,
					':client_image' => $client_image_final,
					':client_address' => $client_address,
					':client_city' => $client_city,
					':client_country' => $client_country,
					':client_region' => $client_region,
					':updated_at' => $updated_at
				];
			} else {
				$query_statement = "
					UPDATE idk_client
					SET client_name = :client_name, client_username = :client_username, client_id_number = :client_id_number, client_pdv_number = :client_pdv_number, client_postal_code = :client_postal_code, client_image = :client_image, client_address = :client_address, client_city = :client_city, client_country = :client_country, client_region = :client_region, updated_at = :updated_at
					WHERE client_id = :client_id";

				$query_array = [
					':client_id' => $client_id,
					':client_name' => $client_name,
					':client_username' => $client_username,
					':client_id_number' => $client_id_number,
					':client_pdv_number' => $client_pdv_number,
					':client_postal_code' => $client_postal_code,
					':client_image' => $client_image_final,
					':client_address' => $client_address,
					':client_city' => $client_city,
					':client_country' => $client_country,
					':client_region' => $client_region,
					':updated_at' => $updated_at
				];
			}

			$query = $db->prepare($query_statement);
			$query->execute($query_array);

			/* UPDATE CLIENT INFO */
			//Update primary phone
			$ci_group = 1; //group=1 is for phone
			$ci_title = 'Primarni telefon';
			$ci_primary = 1;
			$ci_data = $_POST['client_phone'];

			//Check if primary exists
			$check_query = $db->prepare("
				SELECT ci_id
				FROM idk_client_info
				WHERE ci_group = :ci_group AND ci_primary = :ci_primary AND client_id = :client_id");

			$check_query->execute(array(
				':ci_group' => $ci_group,
				':client_id' => $client_id,
				':ci_primary' => 1
			));

			$number_of_rows = $check_query->rowCount();

			if ($number_of_rows !== 0) {
				$query_phone = $db->prepare("
					UPDATE idk_client_info
					SET ci_data = :ci_data, updated_at = :updated_at
					WHERE client_id = :client_id AND ci_group = :ci_group AND ci_primary = :ci_primary");

				$query_phone->execute(array(
					':ci_group' => $ci_group,
					':ci_data' => $ci_data,
					':updated_at' => $updated_at,
					':ci_primary' => $ci_primary,
					':client_id' => $client_id
				));
			} else {
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
			}

			//Update primary email
			$ci_group = 2; //group=2 is for email
			$ci_title = 'Primarni email';
			$ci_primary = 1;
			$ci_data = $_POST['client_email'];

			//Check if primary exists
			$check_query = $db->prepare("
				SELECT ci_id
				FROM idk_client_info
				WHERE ci_group = :ci_group AND ci_primary = :ci_primary AND client_id = :client_id");

			$check_query->execute(array(
				':ci_group' => $ci_group,
				':client_id' => $client_id,
				':ci_primary' => 1
			));

			$number_of_rows = $check_query->rowCount();

			if ($number_of_rows !== 0) {
				$query_phone = $db->prepare("
					UPDATE idk_client_info
					SET ci_data = :ci_data, updated_at = :updated_at
					WHERE client_id = :client_id AND ci_group = :ci_group AND ci_primary = :ci_primary");

				$query_phone->execute(array(
					':ci_group' => $ci_group,
					':ci_data' => $ci_data,
					':updated_at' => $updated_at,
					':ci_primary' => $ci_primary,
					':client_id' => $client_id
				));
			} else {
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
			}

			header("Location: settings?mess=1");

			break;

			/*-----------------------------------------------------------------------------------------
									CLIENT END
-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
									LIST START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 							ADD LIST
			 * *********************************************************/
		case "add_list":

			if (!empty($_POST['list_name'])) {

				$list_name = $_POST['list_name'];
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');
				$employee_id = $logged_employee_id ? $logged_employee_id : NULL;
				if (isset($logged_client_id) and $logged_client_id != 0) {
					$client_id = $logged_client_id;
				} else {
					$client_id = $_COOKIE['idk_session_front_client'];
				}

				$query = $db->prepare("
					INSERT INTO idk_list
						(list_name, client_id, employee_id, created_at, updated_at)
					VALUES
						(:list_name, :client_id, :employee_id, :created_at, :updated_at)");

				$query->execute(array(
					':list_name' => $list_name,
					':client_id' => $client_id,
					':employee_id' => $employee_id,
					':created_at' => $created_at,
					':updated_at' => $updated_at
				));

				header("Location: list?mess=1");
			} else {
				header("Location: list?mess=2");
			}

			break;



			/************************************************************
			 * 					ADD ITEM TO LIST
			 * *********************************************************/
		case "add_item_to_list":

			$product_id = $_POST['product_id'];

			if (!empty($_POST['list_id']) and $_POST['list_id'] != 0 and !empty($_POST['product_on_list_quantity'])) {

				$page = isset($_POST['page']) ? $_POST['page'] : "product";
				$search_parameter = isset($_POST['search_parameter']) ? $_POST['search_parameter'] : NULL;
				$main_category_id = isset($_POST['main_category_id']) ? $_POST['main_category_id'] : NULL;
				$list_id = $_POST['list_id'];
				$product_on_list_quantity = $_POST['product_on_list_quantity'];
				$product_unit = $_POST['product_unit'];
				switch ($page) {
					case 'search':
						$page = "search?search=${search_parameter}";
						break;

					case 'subcategories':
						$page = "subcategories?main_category_id=${main_category_id}";
						break;

					case 'products':
						$page = "products?id=${product_id}";
						break;

					default:
						$page = "product?id=${product_id}";
						break;
				}

				//Check if product exists on list
				$check_query = $db->prepare("
					SELECT product_id
					FROM idk_product_list
					WHERE product_id = :product_id AND list_id = :list_id");

				$check_query->execute(array(
					':product_id' => $product_id,
					':list_id' => $list_id
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {
					$query = $db->prepare("
						INSERT INTO idk_product_list
							(product_id, list_id, product_on_list_quantity, product_unit)
						VALUES
							(:product_id, :list_id, :product_on_list_quantity, :product_unit)");

					$query->execute(array(
						':product_id' => $product_id,
						':list_id' => $list_id,
						':product_on_list_quantity' => $product_on_list_quantity,
						':product_unit' => $product_unit
					));
				} else {
					$query = $db->prepare("
						UPDATE idk_product_list
						SET product_on_list_quantity = :product_on_list_quantity
						WHERE product_id = :product_id AND list_id = :list_id");

					$query->execute(array(
						':product_id' => $product_id,
						':list_id' => $list_id,
						':product_on_list_quantity' => $product_on_list_quantity
					));
				}

				header("Location: ${page}&mess=1");
			} else {
				header("Location: ${page}&mess=2");
			}

			break;



			/************************************************************
			 * 					DELETE LIST
			 * *********************************************************/
		case "delete_list":

			$list_id = $_GET['list_id'];

			if (isset($list_id)) {

				//Delete list from db
				$check_query = $db->prepare("
					DELETE
					FROM idk_list
					WHERE list_id = :list_id");

				$check_query->execute(array(
					':list_id' => $list_id
				));

				//Delete products on list from db
				$query = $db->prepare("
					DELETE
					FROM idk_product_list
					WHERE list_id = :list_id");

				$query->execute(array(
					':list_id' => $list_id
				));

				header("Location: list?mess=3");
			} else {
				header("Location: list?mess=4");
			}

			break;

			/*-----------------------------------------------------------------------------------------
									LIST END
-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
									CART START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					ADD ITEM TO CART TEMP
			 * *********************************************************/
		case "add_item_to_cart_temp":

			$product_id = $_POST['product_id'];

			if (!empty($_POST['product_id']) or !empty($_POST['product_in_cart_temp_quantity'])) {

				$employee_id = $logged_employee_id ? $logged_employee_id : NULL;
				$page = !empty($_POST['page']) ? $_POST['page'] : "product";
				$search_parameter_cart = !empty($_POST['search_parameter_cart']) ? $_POST['search_parameter_cart'] : NULL;
				$main_category_id = !empty($_POST['main_category_id']) ? $_POST['main_category_id'] : NULL;
				$product_name = $_POST['product_name'];
				$product_currency = $_POST['product_currency'];
				$product_quantity = $_POST['product_in_cart_temp_quantity'];
				$product_unit = $_POST['product_unit'];
				$product_price = $_POST['product_price'];
				$product_tax_name = $_POST['product_tax_name'];
				$product_tax_percentage = $_POST['product_tax_percentage'];
				$product_tax_value = $product_price * $product_tax_percentage / 100;
				$product_total_price = $product_price * $product_quantity;
				$product_total_tax = $product_tax_value * $product_quantity;
				$product_to_pay = $product_total_price + $product_total_tax;
				$product_to_pay = round($product_to_pay * 2, 1) / 2;
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');
				$product_quantity_in_db = $_POST['product_quantity'];
				if ($product_quantity_in_db > 0) {
					$product_in_stock = 1;
					if ($product_quantity > $product_quantity_in_db) {
						$product_in_stock = 2;
					}
				} else {
					$product_in_stock = 0;
				}
				if (isset($logged_client_id) and $logged_client_id != 0) {
					$client_id = $logged_client_id;
				} else {
					$client_id = $_COOKIE['idk_session_front_client'];
				}
				switch ($page) {
					case 'search':
						$page = "search?search=${search_parameter_cart}";
						break;

					case 'subcategories':
						$page = "subcategories?main_category_id=${main_category_id}";
						break;

					case 'products':
						$page = "products?id=${product_id}";
						break;

					default:
						$page = "product?id=${product_id}";
						break;
				}

				if (isset($logged_employee_id) and $logged_employee_id != 0) {
					//Check if temp order exists
					$check_query = $db->prepare("
						SELECT order_id
						FROM idk_order_temp
						WHERE client_id = :client_id AND employee_id = :employee_id");

					$check_query->execute(array(
						':client_id' => $client_id,
						':employee_id' => $logged_employee_id
					));
				} else {
					//Check if temp order exists
					$check_query = $db->prepare("
						SELECT order_id
						FROM idk_order_temp
						WHERE client_id = :client_id AND employee_id IS NULL");

					$check_query->execute(array(
						':client_id' => $client_id
					));
				}

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {
					//If it doesnt't exist, create a new one, otherwise just add products to idk_product_order_temp
					$query = $db->prepare("
						INSERT INTO idk_order_temp
							(client_id, employee_id, order_total_price, order_total_tax, order_to_pay, created_at, updated_at)
						VALUES
							(:client_id, :employee_id, :order_total_price, :order_total_tax, :order_to_pay, :created_at, :updated_at)");

					$query->execute(array(
						':client_id' => $client_id,
						':employee_id' => $employee_id,
						':order_total_price' => 0,
						':order_total_tax' => 0,
						':order_to_pay' => 0,
						':created_at' => $created_at,
						':updated_at' => $updated_at
					));

					$order_id = $db->lastInsertId();
				} else {
					$order_temp = $check_query->fetch();
					$order_id = $order_temp['order_id'];
				}

				//Check if temp product order exists
				$check_query = $db->prepare("
					SELECT product_id
					FROM idk_product_order_temp
					WHERE product_id = :product_id AND order_id = :order_id");

				$check_query->execute(array(
					':product_id' => $product_id,
					':order_id' => $order_id
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {
					//Insert new row in product order temp
					$query = $db->prepare("
						INSERT INTO idk_product_order_temp
							(order_id, product_id, product_name, product_currency, product_quantity, product_unit, product_price, product_tax_name, product_tax_percentage, product_tax_value, product_in_stock, product_quantity_in_db)
						VALUES
							(:order_id, :product_id, :product_name, :product_currency, :product_quantity, :product_unit, :product_price, :product_tax_name, :product_tax_percentage, :product_tax_value, :product_in_stock, :product_quantity_in_db)");

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
						':product_in_stock' => $product_in_stock,
						':product_quantity_in_db' => $product_quantity_in_db
					));
				}

				//Update total price and tax of order temp
				$update_total_price_tax_query = $db->prepare("
					UPDATE idk_order_temp
					SET	order_total_price = order_total_price + :product_total_price, order_total_tax = order_total_tax + :product_total_tax, order_to_pay = order_to_pay + :product_to_pay
					WHERE order_id = :order_id");

				$update_total_price_tax_query->execute(array(
					':order_id' => $order_id,
					':product_total_price' => $product_total_price,
					':product_total_tax' => $product_total_tax,
					':product_to_pay' => $product_to_pay
				));

				header("Location: ${page}&mess=3");
			} else {
				header("Location: ${page}&mess=2");
			}

			break;



			/************************************************************
			 * 					ADD LIST TO CART
			 * *********************************************************/
		case "add_list_to_cart":

			$list_id = $_GET['list_id'];

			if (isset($_GET['list_id'])) {

				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');
				$employee_id = $logged_employee_id ? $logged_employee_id : NULL;
				if (isset($logged_client_id) and $logged_client_id != 0) {
					$client_id = $logged_client_id;
				} else {
					$client_id = $_COOKIE['idk_session_front_client'];
				}

				//Check if temp order exists
				$check_query = $db->prepare("
					SELECT order_id
					FROM idk_order_temp
					WHERE client_id = :client_id");

				$check_query->execute(array(
					':client_id' => $client_id
				));

				$number_of_rows = $check_query->rowCount();

				if ($number_of_rows == 0) {
					//If it doesnt't exist, create a new one, otherwise delete the old one, make the new one and add products to idk_product_order_temp
					$query = $db->prepare("
						INSERT INTO idk_order_temp
							(client_id, employee_id, order_total_price, order_total_tax, order_to_pay, created_at, updated_at)
						VALUES
							(:client_id, :employee_id, :order_total_price, :order_total_tax, :order_to_pay, :created_at, :updated_at)");

					$query->execute(array(
						':client_id' => $client_id,
						':employee_id' => $employee_id,
						':order_total_price' => 0,
						':order_total_tax' => 0,
						':order_to_pay' => 0,
						':created_at' => $created_at,
						':updated_at' => $updated_at
					));

					$order_id = $db->lastInsertId();
				} else {
					$order_temp = $check_query->fetch();
					$order_id = $order_temp['order_id'];

					//Delete everything in product order temp
					$query = $db->prepare("
						DELETE FROM idk_product_order_temp
						WHERE order_id = :order_id");

					$query->execute(array(
						':order_id' => $order_id
					));

					//Delete order temp
					$query = $db->prepare("
						DELETE FROM idk_order_temp
						WHERE client_id = :client_id");

					$query->execute(array(
						':client_id' => $client_id
					));

					//Make new order in order temp
					$query = $db->prepare("
						INSERT INTO idk_order_temp
							(client_id, employee_id, order_total_price, order_total_tax, order_to_pay, created_at, updated_at)
						VALUES
							(:client_id, :employee_id, :order_total_price, :order_total_tax, :order_to_pay, :created_at, :updated_at)");

					$query->execute(array(
						':client_id' => $client_id,
						':employee_id' => $employee_id,
						':order_total_price' => 0,
						':order_total_tax' => 0,
						':order_to_pay' => 0,
						':created_at' => $created_at,
						':updated_at' => $updated_at
					));

					$order_id = $db->lastInsertId();
				}

				//Get all products ids in list
				$products_ids_query = $db->prepare("
					SELECT product_id, product_on_list_quantity
					FROM idk_product_list
					WHERE list_id = :list_id");

				$products_ids_query->execute(array(
					':list_id' => $list_id
				));

				while ($products_ids = $products_ids_query->fetch()) {

					$product_id = $products_ids['product_id'];
					$product_on_list_quantity = $products_ids['product_on_list_quantity'];

					//Get all products
					$products_query = $db->prepare("
						SELECT product_id, product_name, product_currency, product_quantity, product_unit, product_price, product_tax_name, product_tax_percentage
						FROM idk_product
						WHERE product_id = :product_id");

					$products_query->execute(array(
						':product_id' => $product_id
					));

					while ($product = $products_query->fetch()) {

						$product_id = $product['product_id'];
						$product_name = $product['product_name'];
						$product_currency = $product['product_currency'];
						$product_quantity = $product['product_quantity'];
						$product_unit = $product['product_unit'];
						$product_price = $product['product_price'];
						$product_tax_name = $product['product_tax_name'];
						$product_tax_percentage = $product['product_tax_percentage'];
						$product_tax_value = $product_price * $product_tax_percentage / 100;
						$product_total_price = $product_price * $product_on_list_quantity;
						$product_total_tax = $product_tax_value * $product_on_list_quantity;
						$product_to_pay = $product_total_price + $product_total_tax;
						$product_to_pay = round($product_to_pay * 2, 1) / 2;
						if ($product_quantity > 0) {
							$product_in_stock = 1;
							if ($product_on_list_quantity > $product_quantity) {
								$product_in_stock = 2;
							}
						} else {
							$product_in_stock = 0;
						}

						//Check if temp product order exists
						$check_query = $db->prepare("
							SELECT product_id
							FROM idk_product_order_temp
							WHERE product_id = :product_id AND order_id = :order_id");

						$check_query->execute(array(
							':product_id' => $product_id,
							':order_id' => $order_id
						));

						$number_of_rows = $check_query->rowCount();

						if ($number_of_rows == 0) {
							//Insert new row in product order temp
							$query = $db->prepare("
								INSERT INTO idk_product_order_temp
									(order_id, product_id, product_name, product_currency, product_quantity, product_unit, product_price, product_tax_name, product_tax_percentage, product_tax_value, product_in_stock, product_quantity_in_db)
								VALUES
									(:order_id, :product_id, :product_name, :product_currency, :product_quantity, :product_unit, :product_price, :product_tax_name, :product_tax_percentage, :product_tax_value, :product_in_stock, :product_quantity_in_db)");

							$query->execute(array(
								':order_id' => $order_id,
								':product_id' => $product_id,
								':product_name' => $product_name,
								':product_currency' => $product_currency,
								':product_quantity' => $product_on_list_quantity,
								':product_unit' => $product_unit,
								':product_price' => $product_price,
								':product_tax_name' => $product_tax_name,
								':product_tax_percentage' => $product_tax_percentage,
								':product_tax_value' => $product_tax_value,
								':product_in_stock' => $product_in_stock,
								':product_quantity_in_db' => $product_quantity_in_db
							));
						}

						//Update total price and tax of order temp
						$update_total_price_tax_query = $db->prepare("
								UPDATE idk_order_temp
								SET	order_total_price = order_total_price + :product_total_price, order_total_tax = order_total_tax + :product_total_tax, order_to_pay = order_to_pay + :product_to_pay
								WHERE order_id = :order_id");

						$update_total_price_tax_query->execute(array(
							':order_id' => $order_id,
							':product_total_price' => $product_total_price,
							':product_total_tax' => $product_total_tax,
							':product_to_pay' => $product_to_pay
						));
					}
				}

				header("Location: cart");
			} else {
				header("Location: index?mess=2");
			}

			break;



			/************************************************************
			 * 					DELETE ONE ITEM FROM CART TEMP
			 * *********************************************************/
		case "delete_cart_item_temp":

			$order_id = $_POST['order_id'];
			$product_id = $_POST['product_id'];
			$order_total_price = 0.000;
			$order_total_tax = 0.000;
			$order_total_rabat = 0.000;
			$order_to_pay = 0.000;

			//Delete product from idk_product_order_temp
			$query = $db->prepare("
				DELETE FROM idk_product_order_temp
				WHERE product_id = :product_id AND order_id = :order_id");

			$query->execute(array(
				':product_id' => $product_id,
				':order_id' => $order_id
			));

			//Get other products idk_product_order_temp
			$query = $db->prepare("
				SELECT product_quantity, product_price, product_rabat_percentage, product_tax_percentage
				FROM idk_product_order_temp
				WHERE order_id = :order_id");

			$query->execute(array(
				':order_id' => $order_id
			));

			$number_of_rows = $query->rowCount();

			if ($number_of_rows > 0) {

				while ($product = $query->fetch()) {
					$product_quantity = $product['product_quantity'];
					$product_price = $product['product_price'];
					$product_rabat_percentage = $product['product_rabat_percentage'];
					$product_tax_percentage = $product['product_tax_percentage'];
					$product_rabat_value = $product_price * $product_rabat_percentage / 100;
					$product_tax_value = ($product_price - $product_rabat_value) * $product_tax_percentage / 100;

					//Calculate product to pay and order to pay
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
					UPDATE idk_order_temp
					SET	order_total_price = :order_total_price, order_total_tax = :order_total_tax, order_total_rabat = :order_total_rabat, order_to_pay = :order_to_pay
					WHERE order_id = :order_id");

				$update_total_price_tax_query->execute(array(
					':order_id' => $order_id,
					':order_total_price' => $order_total_price,
					':order_total_tax' => $order_total_tax,
					':order_total_rabat' => $order_total_rabat,
					':order_to_pay' => $order_to_pay
				));

				header("Location: cart?mess=1");
			} else {

				//Delete order from idk_order_temp
				$query = $db->prepare("
					DELETE FROM idk_order_temp
					WHERE order_id = :order_id");

				$query->execute(array(
					':order_id' => $order_id
				));
				header("Location: cart?mess=2");
			}

			break;



			/************************************************************
			 * 					DELETE ALL ITEMS FROM CART TEMP
			 * *********************************************************/
		case "delete_all_cart_item_temp":

			$order_id = $_GET['order_id'];

			//Delete all products from product order temp
			$query = $db->prepare("
				DELETE FROM idk_product_order_temp
				WHERE order_id = :order_id");

			$query->execute(array(
				':order_id' => $order_id
			));

			//Delete order from order temp
			$query = $db->prepare("
				DELETE FROM idk_order_temp
				WHERE order_id = :order_id");

			$query->execute(array(
				':order_id' => $order_id
			));

			header("Location: cart?mess=2");

			break;



			/************************************************************
			 * 					UPDATE ORDER
			 * *********************************************************/
		case "update_order":

			if (isset($logged_client_id) and $logged_client_id != 0) {
				$client_id = $logged_client_id;
			} elseif (isset($logged_employee_id) and $logged_employee_id != 0) {
				$client_id = $_COOKIE['idk_session_front_client'];
			} else {
				header("Location: login");
			}

			//Get max rabat for client
			$check_query = $db->prepare("
				SELECT client_max_rabat
				FROM idk_client
				WHERE client_id = :client_id");

			$check_query->execute(array(
				':client_id' => $client_id
			));

			$number_of_rows = $check_query->rowCount();

			if ($number_of_rows == 1) {
				$client_max_rabat_row = $check_query->fetch();
				$client_max_rabat = isset($client_max_rabat_row['client_max_rabat']) ? floatval($client_max_rabat_row['client_max_rabat']) : NULL;
			} else {
				$client_max_rabat = NULL;
			}

			$employee_id = $logged_employee_id ? $logged_employee_id : NULL;
			$products_rabats_array_strings = $_POST['products_rabats_array'];
			$products_quantities_array_strings = $_POST['products_quantities_array'];
			$products_prices_array_strings = $_POST['products_prices_array'];
			$products_tax_percentages_array_strings = $_POST['products_tax_percentages_array'];
			$products_ids_array_strings = $_POST['products_ids_array'];
			$order_id = $_POST['order_id'];

			$products_rabats_array_strings = explode(",", $products_rabats_array_strings[0]);
			$products_quantities_array_strings = explode(",", $products_quantities_array_strings[0]);
			$products_prices_array_strings = explode(",", $products_prices_array_strings[0]);
			$products_tax_percentages_array_strings = explode(",", $products_tax_percentages_array_strings[0]);
			$products_ids_array_strings = explode(",", $products_ids_array_strings[0]);

			$order_total_price = 0.000;
			$order_total_tax = 0.000;
			$order_total_rabat = 0.000;
			$order_to_pay = 0.000;

			for ($i = 0; $i < count($products_ids_array_strings); $i++) {

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

				//Update product order
				$product_order_query = $db->prepare("
					UPDATE idk_product_order_temp
					SET	product_rabat_percentage = :product_rabat_percentage, product_quantity = :product_quantity, product_rabat_value = :product_rabat_value, product_in_stock = :product_in_stock, product_quantity_in_db = :product_quantity_in_db
					WHERE product_id = :product_id");

				$product_order_query->execute(array(
					':product_id' => $product_id,
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
				UPDATE idk_order_temp
				SET	order_total_price = :order_total_price, order_total_tax = :order_total_tax, order_total_rabat = :order_total_rabat, order_to_pay = :order_to_pay
				WHERE order_id = :order_id");

			$update_total_price_tax_query->execute(array(
				':order_id' => $order_id,
				':order_total_price' => $order_total_price,
				':order_total_tax' => $order_total_tax,
				':order_total_rabat' => $order_total_rabat,
				':order_to_pay' => $order_to_pay
			));

			header("Location: cart?mess=4");

			break;



			/************************************************************
			 * 					SUBMIT ORDER
			 * *********************************************************/
		case "submit_order":

			if (isset($logged_client_id) and $logged_client_id != 0) {
				$client_id = $logged_client_id;
			} elseif (isset($logged_employee_id) and $logged_employee_id != 0) {
				$client_id = $_COOKIE['idk_session_front_client'];
			} else {
				header("Location: login");
			}
			$order_note = !empty($_POST['order_note']) ? $_POST['order_note'] : NULL;
			$order_type = !empty($_POST['order_type']) ? $_POST['order_type'] : NULL;
			$order_pay_method = !empty($_POST['order_pay_method']) ? $_POST['order_pay_method'] : NULL;
			$employee_id = $logged_employee_id ? $logged_employee_id : NULL;
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');

			if (isset($client_id) and isset($employee_id)) {

				//Get client_name
				$select_client_query = $db->prepare("
				SELECT client_name
				FROM idk_client
				WHERE client_id = :client_id");

				$select_client_query->execute(array(
					':client_id' => $client_id
				));

				$select_client = $select_client_query->fetch();
				$client_name = $select_client['client_name'];

				//Get data from order temp
				$temp_order_query = $db->prepare("
				SELECT order_id, employee_id, order_total_price, order_total_tax, order_total_rabat, order_to_pay
				FROM idk_order_temp
				WHERE client_id = :client_id");

				$temp_order_query->execute(array(
					':client_id' => $client_id
				));

				$temp_order = $temp_order_query->fetch();

				$temp_order_id = $temp_order['order_id'];
				$temp_employee_id = $temp_order['employee_id'];
				$temp_order_total_price = $temp_order['order_total_price'];
				$temp_order_total_tax = $temp_order['order_total_tax'];
				$order_total_rabat = $temp_order['order_total_rabat'];
				$temp_order_to_pay = $temp_order['order_to_pay'];
				$order_key = MD5(rand());
				$order_status = 1;

				//Get data from product order temp
				$temp_product_order_check_products_query = $db->prepare("
				SELECT product_in_stock
				FROM idk_product_order_temp
				WHERE order_id = :order_id");

				$temp_product_order_check_products_query->execute(array(
					':order_id' => $temp_order_id
				));

				$product_counter = 0;
				$product_get_out_of_stock = 0;

				while ($product_get = $temp_product_order_check_products_query->fetch()) {
					$product_counter++;
					if ($product_get['product_in_stock'] == 0 or $product_get['product_in_stock'] == 2) {
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
					':client_id' => $client_id,
					':order_note' => $order_note,
					':employee_id' => $temp_employee_id,
					':order_total_price' => $temp_order_total_price,
					':order_total_tax' => $temp_order_total_tax,
					':order_total_rabat' => $order_total_rabat,
					':order_to_pay' => $temp_order_to_pay,
					':order_key' => $order_key,
					':order_type' => $order_type,
					':order_status' => $order_status,
					':order_pay_method' => $order_pay_method,
					':created_at' => $created_at,
					':updated_at' => $updated_at
				));

				$order_id = $db->lastInsertId();

				//Get data from product order temp
				$temp_product_order_query = $db->prepare("
					SELECT *
					FROM idk_product_order_temp
					WHERE order_id = :order_id");

				$temp_product_order_query->execute(array(
					':order_id' => $temp_order_id
				));

				//Reset total price, tax, rabat, pay
				$order_total_price = 0.000;
				$order_total_tax = 0.000;
				$order_total_rabat = 0.000;
				$order_to_pay = 0.000;

				while ($temp_product_order = $temp_product_order_query->fetch()) {

					$temp_product_id = $temp_product_order['product_id'];
					$temp_product_api_id = $temp_product_order['product_api_id'];
					$temp_product_name = $temp_product_order['product_name'];
					$temp_product_currency = $temp_product_order['product_currency'];
					$temp_product_quantity = $temp_product_order['product_quantity'];
					$temp_product_unit = $temp_product_order['product_unit'];
					$temp_product_price = $temp_product_order['product_price'];
					$temp_product_tax_name = $temp_product_order['product_tax_name'];
					$temp_product_tax_percentage = $temp_product_order['product_tax_percentage'];
					$temp_product_tax_value = $temp_product_order['product_tax_value'];
					$temp_product_rabat_percentage = $temp_product_order['product_rabat_percentage'];
					$temp_product_rabat_value = $temp_product_order['product_rabat_value'];
					$temp_product_in_stock = $temp_product_order['product_in_stock'];
					$temp_product_quantity_in_db = $temp_product_order['product_quantity_in_db'];

					//Insert new row in product order
					$product_order_query = $db->prepare("
						INSERT INTO idk_product_order
							(order_id, product_id, product_name, product_currency, product_quantity, product_unit, product_price, product_tax_name, product_tax_percentage, product_tax_value, product_rabat_percentage, product_rabat_value, product_in_stock, product_quantity_in_db)
						VALUES
							(:order_id, :product_id, :product_name, :product_currency, :product_quantity, :product_unit, :product_price, :product_tax_name, :product_tax_percentage, :product_tax_value, :product_rabat_percentage, :product_rabat_value, :product_in_stock, :product_quantity_in_db)");

					$product_order_query->execute(array(
						':order_id' => $order_id,
						':product_id' => $temp_product_id,
						':product_name' => $temp_product_name,
						':product_currency' => $temp_product_currency,
						':product_quantity' => $temp_product_quantity,
						':product_unit' => $temp_product_unit,
						':product_price' => $temp_product_price,
						':product_tax_name' => $temp_product_tax_name,
						':product_tax_percentage' => $temp_product_tax_percentage,
						':product_tax_value' => $temp_product_tax_value,
						':product_rabat_percentage' => $temp_product_rabat_percentage,
						':product_rabat_value' => $temp_product_rabat_value,
						':product_in_stock' => $temp_product_in_stock,
						':product_quantity_in_db' => $temp_product_quantity_in_db
					));

					//Update quantity in idk_product
					$update_product_quantity_query = $db->prepare("
						UPDATE idk_product
						SET	product_quantity = product_quantity - :temp_product_quantity
						WHERE product_id = :temp_product_id");

					$update_product_quantity_query->execute(array(
						':temp_product_id' => $temp_product_id,
						':temp_product_quantity' => $temp_product_quantity
					));

					//Calculate product to pay
					$temp_product_tax_value = ($temp_product_price - $temp_product_rabat_value) * $temp_product_tax_percentage / 100;
					$product_total_price = $temp_product_price * $temp_product_quantity; //Price without rabat
					$product_total_tax = $temp_product_tax_value * $temp_product_quantity;
					$product_total_rabat = $temp_product_rabat_value * $temp_product_quantity; //Calculate total rabat value
					$product_to_pay = $product_total_price + $product_total_tax - $product_total_rabat;

					//Calculate total price, tax, rabat, pay
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
					$mail_body = "Poštovani,<br><br>Obavještavamo Vas da je Vaša narudžba uspješno zaprimljena.<br>Narudžbu možete isprintati na sljedećem <a href='" . getSiteUrlr() . "idkadmin/print_order?id=" . $order_id . "&order=" . $order_key . "'>linku</a>.<br><br>Hvala na povjerenju,<br>Vaša Unaviva d.o.o.";
					$mail_altbody = "Poštovani,<br><br>Obavještavamo Vas da je Vaša narudžba uspješno zaprimljena.<br>Narudžbu možete isprintati na sljedećem <a href='" . getSiteUrlr() . "idkadmin/print_order?id=" . $order_id . "&order=" . $order_key . "'>linku</a>.<br><br>Hvala na povjerenju,<br>Vaša Unaviva d.o.o.";

					sendEmail(
						$mail_email,
						$mail_name,
						$mail_subject,
						$mail_body,
						$mail_altbody
					);
				}

				//Get employee name
				$select_employee_query = $db->prepare("
					SELECT employee_first_name, employee_last_name
					FROM idk_employee
					WHERE employee_id = :employee_id");

				$select_employee_query->execute(array(
					':employee_id' => $temp_employee_id
				));

				$number_of_rows_employee = $select_employee_query->rowCount();

				if ($number_of_rows_employee !== 0) {
					$employee = $select_employee_query->fetch();

					$employee_first_name = $employee['employee_first_name'];
					$employee_last_name = $employee['employee_last_name'];
					$employee_first_name_initial = substr($employee_first_name, 0, 1);
					$employee_last_name_initial = substr($employee_last_name, 0, 1);
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
					if (isset($employee_first_name_initial)) {
						$notification_title = "Nova narudžba<br>Klijent: ${client_name}<br>Komercijalista: ${employee_first_name_initial}. ${employee_last_name_initial}.";
					} else {
						$notification_title = "Nova narudžba<br>Klijent: ${client_name}";
					}
					$notification_icon = "shopping-cart";
					$notification_link = "" . getSiteUrlr() . "idkadmin/orders?page=open&order_id=${order_id}";
					$notification_type = 3;

					addNotification($notification_title, $notification_icon, $notification_link, $notification_employeeid, $notification_type);
				}

				if (isset($logged_employee_id) and $logged_employee_id != 0) {
					//Update report
					$report_start_time = NULL;
					$report_end_time = date('Y-m-d H:i:s');
					addReport(
						$logged_employee_id,
						$client_id,
						$order_id,
						$report_start_time,
						$report_end_time
					);
				}

				//Update orders statistics
				updateOrdersStats();

				//Create list for client after first order
				$select_client_order_query = $db->prepare("
					SELECT order_id
					FROM idk_order
					WHERE client_id = :client_id");

				$select_client_order_query->execute(array(
					':client_id' => $client_id
				));

				$number_of_rows_client_order = $select_client_order_query->rowCount();

				if ($number_of_rows_client_order == 1) {

					//Create list
					$list_name = "Automatska lista";

					$query_list = $db->prepare("
						INSERT INTO idk_list
							(list_name, client_id, employee_id, created_at, updated_at)
						VALUES
							(:list_name, :client_id, :employee_id, :created_at, :updated_at)");

					$query_list->execute(array(
						':list_name' => $list_name,
						':client_id' => $client_id,
						':employee_id' => $employee_id,
						':created_at' => $created_at,
						':updated_at' => $updated_at
					));

					$list_id = $db->lastInsertId();

					//Get data from product order temp
					$temp_product_order_list_query = $db->prepare("
						SELECT product_id, product_quantity, product_unit
						FROM idk_product_order_temp
						WHERE order_id = :order_id");

					$temp_product_order_list_query->execute(array(
						':order_id' => $temp_order_id
					));

					while ($temp_product_order_list_row = $temp_product_order_list_query->fetch()) {

						$product_list_id = $temp_product_order_list_row['product_id'];
						$product_list_quantity = $temp_product_order_list_row['product_quantity'];
						$product_list_unit = $temp_product_order_list_row['product_unit'];

						//Add product to list
						$query_product_list = $db->prepare("
							INSERT INTO idk_product_list
								(product_id, list_id, product_on_list_quantity, product_unit)
							VALUES
								(:product_id, :list_id, :product_on_list_quantity, :product_unit)");

						$query_product_list->execute(array(
							':product_id' => $product_list_id,
							':list_id' => $list_id,
							':product_on_list_quantity' => $product_list_quantity,
							':product_unit' => $product_list_unit
						));
					}
				}

				// //Update quantity in idk_product where product_quantity < 0
				// $update_product_quantity_other_products__where_0_query = $db->prepare("
				// 	UPDATE idk_product
				// 	SET	product_quantity = 0
				// 	WHERE product_ 0");

				// $update_product_quantity_other_products__where_0_query->execute();

				//Delete all products from product order temp
				$query = $db->prepare("
					DELETE FROM idk_product_order_temp
					WHERE order_id = :order_id");

				$query->execute(array(
					':order_id' => $temp_order_id
				));

				//Delete order from order temp
				$query = $db->prepare("
					DELETE FROM idk_order_temp
					WHERE order_id = :order_id");

				$query->execute(array(
					':order_id' => $temp_order_id
				));

				unset($_COOKIE['idk_session_front_client']);
				setcookie('idk_session_front_client', '', time() + 2 * 60 * 60 - 60 * 60 * 24 * 30, '/crm');

				if (isset($logged_client_id) and $logged_client_id != 0) {
					header("Location: index?mess=4");
				} else {
					header("Location: selectClient?mess=4");
				}
			} else {
				header("Location: index");
			}

			break;



			/************************************************************
			 * 					CREATE OFFER
			 * *********************************************************/
		case "create_offer":

			if (isset($logged_client_id) and $logged_client_id != 0) {
				$client_id = $logged_client_id;
			} elseif (isset($logged_employee_id) and $logged_employee_id != 0) {
				$client_id = $_COOKIE['idk_session_front_client'];
			} else {
				header("Location: login");
			}
			$offer_note = !empty($_POST['offer_note']) ? $_POST['offer_note'] : NULL;
			$offer_type = !empty($_POST['offer_type']) ? $_POST['offer_type'] : NULL;
			$offer_images = !empty($_POST['offer_images']) ? intval($_POST['offer_images']) : NULL;
			$employee_id = $logged_employee_id ? $logged_employee_id : NULL;
			$created_at = date('Y-m-d H:i:s');
			$updated_at = date('Y-m-d H:i:s');

			//Get data from order temp
			$temp_order_query = $db->prepare("
				SELECT order_id, employee_id, order_total_price, order_total_tax, order_total_rabat, order_to_pay
				FROM idk_order_temp
				WHERE client_id = :client_id");

			$temp_order_query->execute(array(
				':client_id' => $client_id
			));

			$temp_order = $temp_order_query->fetch();

			$temp_order_id = $temp_order['order_id'];
			$temp_employee_id = $temp_order['employee_id'];
			$temp_order_total_price = $temp_order['order_total_price'];
			$temp_order_total_tax = $temp_order['order_total_tax'];
			$temp_order_total_rabat = $temp_order['order_total_rabat'];
			$temp_order_to_pay = $temp_order['order_to_pay'];
			$offer_key = MD5(rand());
			$offer_status = 1;

			//Insert new row in offer
			$offer_query = $db->prepare("
				INSERT INTO idk_offer
					(client_id, offer_note, employee_id, offer_status, offer_total_price, offer_total_tax, offer_total_rabat, offer_to_pay, offer_key, offer_type, created_at, updated_at)
				VALUES
					(:client_id, :offer_note, :employee_id, :offer_status, :offer_total_price, :offer_total_tax, :offer_total_rabat, :offer_to_pay, :offer_key, :offer_type, :created_at, :updated_at)");

			$offer_query->execute(array(
				':client_id' => $client_id,
				':offer_note' => $offer_note,
				':employee_id' => $temp_employee_id,
				':offer_total_price' => $temp_order_total_price,
				':offer_total_tax' => $temp_order_total_tax,
				':offer_total_rabat' => $temp_order_total_rabat,
				':offer_to_pay' => $temp_order_to_pay,
				':offer_key' => $offer_key,
				':offer_type' => $offer_type,
				':offer_status' => $offer_status,
				':created_at' => $created_at,
				':updated_at' => $updated_at
			));

			$offer_id = $db->lastInsertId();

			//Get data from product order temp
			$temp_product_order_query = $db->prepare("
				SELECT product_id, product_name, product_currency, product_quantity, product_unit, product_price, product_tax_name, product_tax_percentage, product_tax_value, product_rabat_percentage, product_rabat_value
				FROM idk_product_order_temp
				WHERE order_id = :order_id");

			$temp_product_order_query->execute(array(
				':order_id' => $temp_order_id
			));

			while ($temp_product_order = $temp_product_order_query->fetch()) {

				$temp_product_id = $temp_product_order['product_id'];
				$temp_product_name = $temp_product_order['product_name'];
				$temp_product_currency = $temp_product_order['product_currency'];
				$temp_product_quantity = $temp_product_order['product_quantity'];
				$temp_product_unit = $temp_product_order['product_unit'];
				$temp_product_price = $temp_product_order['product_price'];
				$temp_product_tax_name = $temp_product_order['product_tax_name'];
				$temp_product_tax_percentage = $temp_product_order['product_tax_percentage'];
				$temp_product_tax_value = $temp_product_order['product_tax_value'];
				$temp_product_rabat_percentage = $temp_product_order['product_rabat_percentage'];
				$temp_product_rabat_value = $temp_product_order['product_rabat_value'];

				//Insert new row in product offer
				$product_offer_query = $db->prepare("
					INSERT INTO idk_product_offer
						(offer_id, product_id, product_name, product_currency, product_quantity, product_unit, product_price, product_tax_name, product_tax_percentage, product_tax_value, product_rabat_percentage, product_rabat_value)
					VALUES
						(:offer_id, :product_id, :product_name, :product_currency, :product_quantity, :product_unit, :product_price, :product_tax_name, :product_tax_percentage, :product_tax_value, :product_rabat_percentage, :product_rabat_value)");

				$product_offer_query->execute(array(
					':offer_id' => $offer_id,
					':product_id' => $temp_product_id,
					':product_name' => $temp_product_name,
					':product_currency' => $temp_product_currency,
					':product_quantity' => $temp_product_quantity,
					':product_unit' => $temp_product_unit,
					':product_price' => $temp_product_price,
					':product_tax_name' => $temp_product_tax_name,
					':product_tax_percentage' => $temp_product_tax_percentage,
					':product_tax_value' => $temp_product_tax_value,
					':product_rabat_percentage' => $temp_product_rabat_percentage,
					':product_rabat_value' => $temp_product_rabat_value
				));
			}

			//Add notification to all administrators

			//Get client_name
			$select_client_query = $db->prepare("
				SELECT t1.client_name, t1.client_id
				FROM idk_client t1
				INNER JOIN idk_offer t2
				ON t1.client_id = t2.client_id
				WHERE t2.offer_id = :offer_id");

			$select_client_query->execute(array(
				':offer_id' => $offer_id
			));

			$select_client = $select_client_query->fetch();
			$client_name = $select_client['client_name'];
			$client_id = $select_client['client_id'];

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

				//Send confirmation e-mail with offer
				$mail_email = $client_email;
				$mail_name = $client_name;
				$mail_subject = "Unaviva d.o.o. B2B - Ponuda/Predračun";
				$mail_body = "Poštovani,<br><br>Dostavljamo Vam ponudu/predračun.<br>Ponudu/Predračun možete isprintati na sljedećem <a href='" . getSiteUrlr() . "idkadmin/print_offer?id=" . $offer_id . "&offer=" . $offer_key . "'>linku</a>.<br><br>Hvala na povjerenju,<br>Vaša Unaviva d.o.o.";
				$mail_altbody = "Poštovani,<br><br>Dostavljamo Vam ponudu/predračun.<br>Ponudu/Predračun možete isprintati na sljedećem <a href='" . getSiteUrlr() . "idkadmin/print_offer?id=" . $offer_id . "&offer=" . $offer_key . "'>linku</a>.<br><br>Hvala na povjerenju,<br>Vaša Unaviva d.o.o.";

				sendEmail(
					$mail_email,
					$mail_name,
					$mail_subject,
					$mail_body,
					$mail_altbody
				);
			}

			//Get employee name
			$select_employee_query = $db->prepare("
				SELECT employee_first_name, employee_last_name
				FROM idk_employee
				WHERE employee_id = :employee_id");

			$select_employee_query->execute(array(
				':employee_id' => $temp_employee_id
			));

			$number_of_rows_employee = $select_employee_query->rowCount();

			if ($number_of_rows_employee !== 0) {
				$employee = $select_employee_query->fetch();

				$employee_first_name = $employee['employee_first_name'];
				$employee_last_name = $employee['employee_last_name'];
				$employee_first_name_initial = substr($employee_first_name, 0, 1);
				$employee_last_name_initial = substr($employee_last_name, 0, 1);
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
				if (isset($employee_first_name_initial)) {
					$notification_title = "Nova ponuda<br>Klijent: ${client_name}<br>Komercijalista: ${employee_first_name_initial}. ${employee_last_name_initial}.";
				} else {
					$notification_title = "Nova ponuda<br>Klijent: ${client_name}";
				}
				$notification_icon = "file";
				$notification_link = "" . getSiteUrlr() . "idkadmin/offers?page=open&offer_id=${offer_id}";
				$notification_type = 4;

				addNotification($notification_title, $notification_icon, $notification_link, $notification_employeeid, $notification_type);
			}

			if (isset($logged_employee_id) and $logged_employee_id != 0) {
				//Update report
				$order_id = NULL;
				$report_start_time = NULL;
				$report_end_time = date('Y-m-d H:i:s');
				addReport(
					$logged_employee_id,
					$client_id,
					$order_id,
					$report_start_time,
					$report_end_time
				);
			}

			//Delete all products from product order temp
			$query = $db->prepare("
				DELETE FROM idk_product_order_temp
				WHERE order_id = :order_id");

			$query->execute(array(
				':order_id' => $temp_order_id
			));

			//Delete order from order temp
			$query = $db->prepare("
				DELETE FROM idk_order_temp
				WHERE order_id = :order_id");

			$query->execute(array(
				':order_id' => $temp_order_id
			));

			unset($_COOKIE['idk_session_front_client']);
			setcookie('idk_session_front_client', '', time() + 2 * 60 * 60 - 60 * 60 * 24 * 30, '/crm');

			if (isset($logged_client_id) and $logged_client_id != 0) {
				header("Location: index?mess=5");
			} else {
				header("Location: selectClient?mess=5");
			}
			break;

			/*-----------------------------------------------------------------------------------------
									CART END
			-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
							ROUTES START
			-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					SET ROUTE STATE
			 * *********************************************************/
		case "set_route_state":

			$route_id = !empty($_POST['routeId']) ? $_POST['routeId'] : NULL;
			$client_id = !empty($_POST['clientId']) ? $_POST['clientId'] : NULL;
			$status = !empty($_POST['status']) ? $_POST['status'] : NULL;
			$comment = !empty($_POST['comment']) ? $_POST['comment'] : NULL;
			$latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : NULL;
			$longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : NULL;
			$datetime = date('Y-m-d H:i:s');
			$datetime_date = date('Y-m-d', strtotime($datetime));

			//Get route day
			$select_route_day_query = $db->prepare("
				SELECT route_day
				FROM idk_route
				WHERE route_id = :route_id");

			$select_route_day_query->execute(array(
				':route_id' => $route_id
			));

			$select_route_day_row = $select_route_day_query->fetch();
			$route_day = $select_route_day_row['route_day'];

			switch ($route_day) {
				case 1:
					$route_day = "Ponedjeljak";
					break;

				case 2:
					$route_day = "Utorak";
					break;

				case 3:
					$route_day = "Srijeda";
					break;

				case 4:
					$route_day = "Četvrtak";
					break;

				case 5:
					$route_day = "Petak";
					break;

				case 6:
					$route_day = "Subota";
					break;

				default:
					$route_day = "Nedjelja";
			}

			//Get number of rows from idk_route_report for notifications (if number of rows > 0, send notifications to administrators)
			$check_route_report_for_notification_query = $db->prepare("
				SELECT rr_id
				FROM idk_route_report
				WHERE rr_route_id = :rr_route_id AND (rr_datetime BETWEEN :rr_datetime_start AND :rr_datetime_end)");

			$check_route_report_for_notification_query->execute(array(
				':rr_route_id' => $route_id,
				':rr_datetime_start' => date('Y-m-d 00:00:00', strtotime($datetime)),
				':rr_datetime_end' => $datetime
			));

			$number_of_rows_route_report_for_notification = $check_route_report_for_notification_query->rowCount();

			if (isset($route_id) and isset($client_id) and isset($status) and isset($latitude) and isset($longitude)) {

				$check_route_report_query = $db->prepare("
					SELECT rr_id
					FROM idk_route_report
					WHERE rr_route_id = :rr_route_id AND rr_client_id = :rr_client_id AND (rr_datetime BETWEEN :rr_datetime_start AND :rr_datetime_end)");

				$check_route_report_query->execute(array(
					':rr_route_id' => $route_id,
					':rr_client_id' => $client_id,
					':rr_datetime_start' => date('Y-m-d 00:00:00', strtotime($datetime)),
					':rr_datetime_end' => $datetime
				));

				$number_of_rows_route_report = $check_route_report_query->rowCount();

				if ($number_of_rows_route_report == 0) {

					$query = $db->prepare("
						INSERT INTO idk_route_report
							(rr_route_id, rr_client_id, rr_status, rr_comment, rr_latitude, rr_longitude, rr_datetime)
						VALUES
							(:rr_route_id, :rr_client_id, :rr_status, :rr_comment, :rr_latitude, :rr_longitude, :rr_datetime)");

					$query->execute(array(
						':rr_route_id' => $route_id,
						':rr_client_id' => $client_id,
						':rr_status' => $status,
						':rr_comment' => $comment,
						':rr_latitude' => $latitude,
						':rr_longitude' => $longitude,
						':rr_datetime' => $datetime
					));

					$check_route_client_query = $db->prepare("
						SELECT client_name
						FROM idk_client
						WHERE client_id = :client_id");

					$check_route_client_query->execute(array(
						':client_id' => $client_id
					));

					$client_row = $check_route_client_query->fetch();
					$client_name = $client_row['client_name'];

					$check_route_employee_query = $db->prepare("
						SELECT employee_first_name, employee_last_name
						FROM idk_employee
						WHERE employee_id = :employee_id");

					$check_route_employee_query->execute(array(
						':employee_id' => $logged_employee_id
					));

					$employee_row = $check_route_employee_query->fetch();
					$employee_first_name = $employee_row['employee_first_name'];
					$employee_last_name = $employee_row['employee_last_name'];
					$employee_first_name_initial = substr($employee_first_name, 0, 1);
					$employee_last_name_initial = substr($employee_last_name, 0, 1);

					//Add to log
					$log_desc = "Dodao izvještaj o ruti: ${route_day} za klijenta: ${client_name}";
					$datetime = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $datetime);

					//Send notifications if this is the first row in idk_route_report for selected client for today
					if ($number_of_rows_route_report_for_notification == 0) {
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
							$notification_title = "Novi izvještaj o ruti!<br>Ruta: ${route_day}<br>Komercijalista: ${employee_first_name_initial}. ${employee_last_name_initial}.";
							$notification_icon = "map-marker";
							$notification_link = "" . getSiteUrlr() . "idkadmin/routes?page=open_report&id=${route_id}&date=${datetime_date}";
							$notification_type = 4;

							addNotification($notification_title, $notification_icon, $notification_link, $notification_employeeid, $notification_type);
						}
					}

					$output_array = array();
					if ($status == 1) {
						array_push($output_array, "Klijent posjećen.<br><small>" . date('d.m.Y. H:i', strtotime($datetime)) . "</small>");
						array_push($output_array, "idk_text_brand");
						array_push($output_array, "idk_assortment_in_stock_card");
						echo json_encode($output_array);
					} else {
						array_push($output_array, "Klijent nije posjećen.<br><small>Razlog: " . $comment . "</small><br><small>" . date('d.m.Y. H:i', strtotime($datetime)) . "</small>");
						array_push($output_array, "idk_text_red");
						array_push($output_array, "idk_assortment_not_in_stock_card");
						echo json_encode($output_array);
					}
				} else {

					$check_route_report_row = $check_route_report_query->fetch();
					$rr_id = $check_route_report_row['rr_id'];

					$query_rr = $db->prepare("
						UPDATE idk_route_report
						SET rr_status = :rr_status, rr_comment = :rr_comment, rr_latitude = :rr_latitude, rr_longitude = :rr_longitude, rr_datetime = :rr_datetime
						WHERE rr_id = :rr_id");

					$query_rr->execute(array(
						':rr_id' => $rr_id,
						':rr_status' => $status,
						':rr_comment' => $comment,
						':rr_latitude' => $latitude,
						':rr_longitude' => $longitude,
						':rr_datetime' => $datetime
					));

					$check_route_client_query = $db->prepare("
						SELECT client_name
						FROM idk_client
						WHERE client_id = :client_id");

					$check_route_client_query->execute(array(
						':client_id' => $client_id
					));

					$client_row = $check_route_client_query->fetch();
					$client_name = $client_row['client_name'];

					$check_route_employee_query = $db->prepare("
						SELECT employee_first_name, employee_last_name
						FROM idk_employee
						WHERE employee_id = :employee_id");

					$check_route_employee_query->execute(array(
						':employee_id' => $logged_employee_id
					));

					$employee_row = $check_route_employee_query->fetch();
					$employee_first_name = $employee_row['employee_first_name'];
					$employee_last_name = $employee_row['employee_last_name'];

					//Add to log
					$log_desc = "Uredio izvještaj za rutu: ${route_day} za klijenta: ${client_name}";
					$datetime = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $datetime);

					$output_array = array();
					if ($status == 1) {
						array_push($output_array, "Klijent posjećen.<br><small>" . date('d.m.Y. H:i', strtotime($datetime)) . "</small>");
						array_push($output_array, "idk_text_brand");
						array_push($output_array, "idk_assortment_in_stock_card");
						echo json_encode($output_array);
					} else {
						array_push($output_array, "Klijent nije posjećen.<br><small>Razlog: " . $comment . "</small><br><small>" . date('d.m.Y. H:i', strtotime($datetime)) . "</small>");
						array_push($output_array, "idk_text_red");
						array_push($output_array, "idk_assortment_not_in_stock_card");
						echo json_encode($output_array);
					}
				}
			} else {

				header("Location: routes");
			}

			break;



			/************************************************************
			 * 					ADD CLIENT TO ROUTE
			 * *********************************************************/
		case "add_client_to_route":

			$route_id = !empty($_POST['routeId']) ? $_POST['routeId'] : NULL;
			$client_id = !empty($_POST['clientId']) ? $_POST['clientId'] : NULL;
			$status = !empty($_POST['status']) ? $_POST['status'] : NULL;
			$comment = !empty($_POST['comment']) ? $_POST['comment'] : NULL;
			$latitude = !empty($_POST['latitude']) ? $_POST['latitude'] : NULL;
			$longitude = !empty($_POST['longitude']) ? $_POST['longitude'] : NULL;
			$datetime = date('Y-m-d H:i:s');
			$datetime_date = date('Y-m-d', strtotime($datetime));

			//Get route day
			$select_route_day_query = $db->prepare("
				SELECT route_day
				FROM idk_route
				WHERE route_id = :route_id");

			$select_route_day_query->execute(array(
				':route_id' => $route_id
			));

			$select_route_day_row = $select_route_day_query->fetch();
			$route_day = $select_route_day_row['route_day'];

			switch ($route_day) {
				case 1:
					$route_day = "Ponedjeljak";
					break;

				case 2:
					$route_day = "Utorak";
					break;

				case 3:
					$route_day = "Srijeda";
					break;

				case 4:
					$route_day = "Četvrtak";
					break;

				case 5:
					$route_day = "Petak";
					break;

				case 6:
					$route_day = "Subota";
					break;

				default:
					$route_day = "Nedjelja";
			}

			//Get number of rows from idk_route_report for notifications (if number of rows > 0, send notifications to administrators)
			$check_route_report_for_notification_query = $db->prepare("
				SELECT rr_id
				FROM idk_route_report
				WHERE rr_route_id = :rr_route_id AND (rr_datetime BETWEEN :rr_datetime_start AND :rr_datetime_end)");

			$check_route_report_for_notification_query->execute(array(
				':rr_route_id' => $route_id,
				':rr_datetime_start' => date('Y-m-d 00:00:00', strtotime($datetime)),
				':rr_datetime_end' => $datetime
			));

			$number_of_rows_route_report_for_notification = $check_route_report_for_notification_query->rowCount();

			if (isset($route_id) and isset($client_id) and isset($status) and isset($latitude) and isset($longitude)) {

				$check_route_client_query = $db->prepare("
					SELECT rc_id
					FROM idk_route_client
					WHERE rc_route_id = :rc_route_id AND rc_client_id = :rc_client_id");

				$check_route_client_query->execute(array(
					':rc_route_id' => $route_id,
					':rc_client_id' => $client_id
				));

				$number_of_rows_route_client = $check_route_client_query->rowCount();

				if ($number_of_rows_route_client == 0) {

					$check_route_report_query = $db->prepare("
					SELECT rr_id
					FROM idk_route_report
					WHERE rr_route_id = :rr_route_id AND rr_client_id = :rr_client_id AND (rr_datetime BETWEEN :rr_datetime_start AND :rr_datetime_end)");

					$check_route_report_query->execute(array(
						':rr_route_id' => $route_id,
						':rr_client_id' => $client_id,
						':rr_datetime_start' => date('Y-m-d 00:00:00', strtotime($datetime)),
						':rr_datetime_end' => $datetime
					));

					$number_of_rows_route_report = $check_route_report_query->rowCount();

					if ($number_of_rows_route_report == 0) {

						$query = $db->prepare("
						INSERT INTO idk_route_report
							(rr_route_id, rr_client_id, rr_status, rr_comment, rr_latitude, rr_longitude, rr_datetime)
						VALUES
							(:rr_route_id, :rr_client_id, :rr_status, :rr_comment, :rr_latitude, :rr_longitude, :rr_datetime)");

						$query->execute(array(
							':rr_route_id' => $route_id,
							':rr_client_id' => $client_id,
							':rr_status' => $status,
							':rr_comment' => $comment,
							':rr_latitude' => $latitude,
							':rr_longitude' => $longitude,
							':rr_datetime' => $datetime
						));

						$check_route_client_query = $db->prepare("
						SELECT client_name, client_image
						FROM idk_client
						WHERE client_id = :client_id");

						$check_route_client_query->execute(array(
							':client_id' => $client_id
						));

						$client_row = $check_route_client_query->fetch();
						$client_name = $client_row['client_name'];
						$client_image = $client_row['client_image'];

						$check_route_employee_query = $db->prepare("
						SELECT employee_first_name, employee_last_name
						FROM idk_employee
						WHERE employee_id = :employee_id");

						$check_route_employee_query->execute(array(
							':employee_id' => $logged_employee_id
						));

						$employee_row = $check_route_employee_query->fetch();
						$employee_first_name = $employee_row['employee_first_name'];
						$employee_last_name = $employee_row['employee_last_name'];
						$employee_first_name_initial = substr($employee_first_name, 0, 1);
						$employee_last_name_initial = substr($employee_last_name, 0, 1);

						//Add to log
						$log_desc = "Dodao izvještaj o ruti: ${route_day} za klijenta: ${client_name}";
						$datetime = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $datetime);

						//Send notifications if this is the first row in idk_route_report for selected client for today
						if ($number_of_rows_route_report_for_notification == 0) {
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
								$notification_title = "Novi izvještaj o ruti!<br>Ruta: ${route_day}<br>Komercijalista: ${employee_first_name_initial}. ${employee_last_name_initial}.";
								$notification_icon = "map-marker";
								$notification_link = "" . getSiteUrlr() . "idkadmin/routes?page=open_report&id=${route_id}&date=${datetime_date}";
								$notification_type = 4;

								addNotification($notification_title, $notification_icon, $notification_link, $notification_employeeid, $notification_type);
							}
						}

						$output_array = array();
						array_push($output_array, "Uspješno ste dodali posjetu novom klijentu: ${client_name}");
						array_push($output_array, $client_name);
						array_push($output_array, $client_image);
						array_push($output_array, date('d.m.Y. H:i', strtotime($datetime)));
						echo json_encode($output_array);
					} else {

						$output_array = array();
						array_push($output_array, "Izvještaj za odabranog klijenta već postoji u bazi.");
						echo json_encode($output_array);
					}
				} else {

					$output_array = array();
					array_push($output_array, "Odabrani klijent već postoji na ruti.");
					echo json_encode($output_array);
				}
			} else {

				$output_array = array();
				array_push($output_array, "Greška! Forma nije pravilno popunjena.");
				echo json_encode($output_array);
				header("Location: routes");
			}

			break;

			/*-----------------------------------------------------------------------------------------
								ROUTES END
	-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
							ASSORTMENT START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					SET ASSORTMENT STATE
			 * *********************************************************/
		case "set_assortment_state":

			$product_id = !empty($_POST['productId']) ? $_POST['productId'] : NULL;
			$client_id = !empty($_POST['clientId']) ? $_POST['clientId'] : NULL;
			$status = !empty($_POST['status']) ? $_POST['status'] : NULL;
			$ar_datetime = date('Y-m-d H:i:s');

			if (isset($product_id) and isset($client_id) and isset($status)) {

				$check_assortment_report_query = $db->prepare("
					SELECT ar_id
					FROM idk_assortment_report
					WHERE ar_employee_id = :ar_employee_id AND ar_client_id = :ar_client_id AND (ar_datetime BETWEEN :ar_datetime_start AND :ar_datetime_end)");

				$check_assortment_report_query->execute(array(
					':ar_employee_id' => $logged_employee_id,
					':ar_client_id' => $client_id,
					':ar_datetime_start' => date('Y-m-d 00:00:00', strtotime($ar_datetime)),
					':ar_datetime_end' => $ar_datetime
				));

				$number_of_rows_assortment_report = $check_assortment_report_query->rowCount();

				if ($number_of_rows_assortment_report == 0) {

					$query = $db->prepare("
						INSERT INTO idk_assortment_report
							(ar_employee_id, ar_client_id, ar_datetime)
						VALUES
							(:ar_employee_id, :ar_client_id, :ar_datetime)");

					$query->execute(array(
						':ar_employee_id' => $logged_employee_id,
						':ar_client_id' => $client_id,
						':ar_datetime' => $ar_datetime
					));

					$ap_assortment_id = $db->lastInsertId();

					$query_ap = $db->prepare("
						INSERT INTO idk_assortment_product
							(ap_assortment_id, ap_product_id, ap_status)
						VALUES
							(:ap_assortment_id, :ap_product_id, :ap_status)");

					$query_ap->execute(array(
						':ap_assortment_id' => $ap_assortment_id,
						':ap_product_id' => $product_id,
						':ap_status' => $status
					));

					$check_assortment_client_query = $db->prepare("
						SELECT client_name
						FROM idk_client
						WHERE client_id = :client_id");

					$check_assortment_client_query->execute(array(
						':client_id' => $client_id
					));

					$client_row = $check_assortment_client_query->fetch();
					$client_name = $client_row['client_name'];

					$check_assortment_employee_query = $db->prepare("
						SELECT employee_first_name, employee_last_name
						FROM idk_employee
						WHERE employee_id = :employee_id");

					$check_assortment_employee_query->execute(array(
						':employee_id' => $logged_employee_id
					));

					$employee_row = $check_assortment_employee_query->fetch();
					$employee_first_name = $employee_row['employee_first_name'];
					$employee_last_name = $employee_row['employee_last_name'];
					$employee_first_name_initial = substr($employee_first_name, 0, 1);
					$employee_last_name_initial = substr($employee_last_name, 0, 1);

					//Add to log
					$log_desc = "Dodao izvještaj za stanje asortimana za klijenta: ${client_name} za proizvod s ID brojem: ${product_id}";
					$datetime = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $datetime);

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
						$notification_title = "Novi izvještaj o stanju asortimana!<br>Klijent: ${client_name}<br>Komercijalista: ${employee_first_name_initial}. ${employee_last_name_initial}.";
						$notification_icon = "bar-chart";
						$notification_link = "" . getSiteUrlr() . "idkadmin/assortment?page=open_report&id=${ap_assortment_id}";
						$notification_type = 4;

						addNotification($notification_title, $notification_icon, $notification_link, $notification_employeeid, $notification_type);
					}

					$output_array = array();
					if ($status == 1) {
						array_push($output_array, "IMA NA STANJU");
						array_push($output_array, "idk_text_brand");
						array_push($output_array, "idk_assortment_in_stock_card");
						echo json_encode($output_array);
					} else {
						array_push($output_array, "NEMA NA STANJU");
						array_push($output_array, "idk_text_red");
						array_push($output_array, "idk_assortment_not_in_stock_card");
						echo json_encode($output_array);
					}
				} else {

					$check_assortment_report_row = $check_assortment_report_query->fetch();
					$ar_id = $check_assortment_report_row['ar_id'];

					$check_assortment_product_query = $db->prepare("
						SELECT ap_id
						FROM idk_assortment_product
						WHERE ap_assortment_id = :ap_assortment_id AND ap_product_id = :ap_product_id");

					$check_assortment_product_query->execute(array(
						':ap_assortment_id' => $ar_id,
						':ap_product_id' => $product_id
					));

					$number_of_rows_assortment_product = $check_assortment_product_query->rowCount();

					if ($number_of_rows_assortment_product == 0) {

						$query_ap = $db->prepare("
							INSERT INTO idk_assortment_product
								(ap_assortment_id, ap_product_id, ap_status)
							VALUES
								(:ap_assortment_id, :ap_product_id, :ap_status)");

						$query_ap->execute(array(
							':ap_status' => $status,
							':ap_assortment_id' => $ar_id,
							':ap_product_id' => $product_id
						));
					} else {

						$query_ap = $db->prepare("
							UPDATE idk_assortment_product
							SET ap_status = :ap_status
							WHERE ap_assortment_id = :ap_assortment_id AND ap_product_id = :ap_product_id");

						$query_ap->execute(array(
							':ap_status' => $status,
							':ap_assortment_id' => $ar_id,
							':ap_product_id' => $product_id
						));
					}

					$query_ap = $db->prepare("
						UPDATE idk_assortment_report
						SET ar_datetime = :ar_datetime
						WHERE ar_id = :ar_id");

					$query_ap->execute(array(
						':ar_datetime' => $ar_datetime,
						':ar_id' => $ar_id
					));

					$check_assortment_client_query = $db->prepare("
						SELECT client_name
						FROM idk_client
						WHERE client_id = :client_id");

					$check_assortment_client_query->execute(array(
						':client_id' => $client_id
					));

					$client_row = $check_assortment_client_query->fetch();
					$client_name = $client_row['client_name'];

					$check_assortment_employee_query = $db->prepare("
						SELECT employee_first_name, employee_last_name
						FROM idk_employee
						WHERE employee_id = :employee_id");

					$check_assortment_employee_query->execute(array(
						':employee_id' => $logged_employee_id
					));

					$employee_row = $check_assortment_employee_query->fetch();
					$employee_first_name = $employee_row['employee_first_name'];
					$employee_last_name = $employee_row['employee_last_name'];

					//Add to log
					$log_desc = "Uredio izvještaj za stanje asortimana za klijenta s ID brojem: ${client_name} za proizvod s ID brojem: ${product_id}";
					$datetime = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $datetime);

					$output_array = array();
					if ($status == 1) {
						array_push($output_array, "IMA NA STANJU");
						array_push($output_array, "idk_text_brand");
						array_push($output_array, "idk_assortment_in_stock_card");
						echo json_encode($output_array);
					} else {
						array_push($output_array, "NEMA NA STANJU");
						array_push($output_array, "idk_text_red");
						array_push($output_array, "idk_assortment_not_in_stock_card");
						echo json_encode($output_array);
					}
				}
			} else {

				header("Location: assortment");
			}

			break;

			/*-----------------------------------------------------------------------------------------
							ASSORTMENT END
-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
									ORDERS START - ZA SKLADISTARA
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					FINISH ORDER
			 * *********************************************************/
		case "finish_order":

			$order_id = $_GET['order_id'];

			//Update order status
			$query = $db->prepare("
				UPDATE idk_order
				SET	order_status = :order_status
				WHERE order_id = :order_id");

			$query->execute(array(
				':order_status' => 6,
				':order_id' => $order_id
			));

			//Get client_name
			$select_client_query = $db->prepare("
				SELECT t1.client_name
				FROM idk_client t1
				INNER JOIN idk_order t2
				ON t1.client_id = t2.client_id
				WHERE t2.order_id = :order_id");

			$select_client_query->execute(array(
				':order_id' => $order_id
			));

			$select_client = $select_client_query->fetch();
			$client_name = $select_client['client_name'];

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
				$notification_title = "Završena narudžba - ${client_name}!";
				$notification_icon = "shopping-cart";
				$notification_link = "" . getSiteUrlr() . "idkadmin/orders?page=open&order_id=${order_id}";
				$notification_type = 3;

				addNotification($notification_title, $notification_icon, $notification_link, $notification_employeeid, $notification_type);
			}

			header("Location: orders?page=list&mess=1");

			break;

			/*-----------------------------------------------------------------------------------------
									ORDERS END
-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
									PRODUCT START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					ADD REVIEW
			 * *********************************************************/
		case "add_product_review":

			$product_id = $_POST['product_id'];

			if (!empty($_POST['review_stars'])) {

				$client_id = $logged_client_id ? $logged_client_id : $logged_employee_id;
				$review_stars = $_POST['review_stars'];
				$review_comment = $_POST['review_comment'];
				$created_at = date('Y-m-d H:i:s');
				$updated_at = date('Y-m-d H:i:s');

				$query = $db->prepare("
					INSERT INTO idk_product_review
						(product_id, client_id, review_stars, review_comment, created_at, updated_at)
					VALUES
						(:product_id, :client_id, :review_stars, :review_comment, :created_at, :updated_at)");

				$query->execute(array(
					':product_id' => $product_id,
					':client_id' => $client_id,
					':review_stars' => $review_stars,
					':review_comment' => $review_comment,
					':created_at' => $created_at,
					':updated_at' => $updated_at
				));

				header("Location: product?id=" . $product_id . "&mess=4");
			} else {
				header("Location: product?id=" . $product_id . "&mess=2");
			}

			break;

			/*-----------------------------------------------------------------------------------------
									PRODUCT END
-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
										NOTIFICATIONS START
-----------------------------------------------------------------------------------------*/

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
				// 		$mail_url = "" . getSiteUrlr() . "/messages?page=open&id=" . $mu_messageid . "";
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
				// 		$mail_url = "" . getSiteUrlr() . "/messages?page=open&id=" . $mu_messageid . "";
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




			/*-----------------------------------------------------------------------------------------
							CONTACT US START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					SEND CONTACT US MESSAGE
			 * *********************************************************/
		case "send_contact_us_message":

			$mail_email = "info@unaviva.ba";
			$mail_name = "Unaviva B2B";
			$mail_subject = "Unaviva B2B - nova poruka sa kontakt forme";
			$mail_body = "<strong>Podaci o pošiljatelju:</strong><br>Ime: " . $_POST['contact_name'] . "<br>Telefon: <a href='tel:" . $_POST['contact_tel'] . "'>" . $_POST['contact_tel'] . "</a><br>E-mail: <a href='mailto:" . $_POST['contact_email'] . "'>" . $_POST['contact_email'] . "</a><br><br><strong>Tekst poruke:</strong><br><br>&nbsp;&nbsp;&nbsp;&nbsp;<em>" . $_POST['contact_message'] . "</em>";
			$mail_altbody = "<strong>Podaci o pošiljatelju:</strong><br>Ime: " . $_POST['contact_name'] . "<br>Telefon: <a href='tel:" . $_POST['contact_tel'] . "'>" . $_POST['contact_tel'] . "</a><br>E-mail: <a href='mailto:" . $_POST['contact_email'] . "'>" . $_POST['contact_email'] . "</a><br><br><strong>Tekst poruke:</strong><br><br>&nbsp;&nbsp;&nbsp;&nbsp;<em>" . $_POST['contact_message'] . "</em>";

			sendEmail($mail_email, $mail_name, $mail_subject, $mail_body, $mail_altbody);

			header("Location: contact_us?mess=1");

			break;


			/*-----------------------------------------------------------------------------------------
							CONTACT US END
-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
							DATACOLLECTIONS START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					ADD DATACOLLECTION
			 * *********************************************************/
		case "add_datacollection":

			$datac_type = $_POST['datac_type'];
			if (isset($logged_client_id) and $logged_client_id != 0) {
				$datac_clientid = $logged_client_id;
			} elseif (isset($logged_employee_id) and $logged_employee_id != 0) {
				$datac_clientid = $_COOKIE['idk_session_front_client'];
			} else {
				$datac_clientid = NULL;
			}
			$datac_datetime = date('Y-m-d H:i:s');
			$datac_desc = $_POST['datac_desc'];

			//Upload and save datacollection_image
			$datacollection_image_final = uploadImage('datacollection', 0, $datac_clientid, 1);

			$query = $db->prepare("
				INSERT INTO idk_datacollection
					(datac_type, datac_employeeid, datac_clientid, datac_datetime, datac_desc, datac_img)
				VALUES
					(:datac_type, :datac_employeeid, :datac_clientid, :datac_datetime, :datac_desc, :datac_img)");

			$query->execute(array(
				':datac_type' => $datac_type,
				':datac_employeeid' => $logged_employee_id,
				':datac_clientid' => $datac_clientid,
				':datac_datetime' => $datac_datetime,
				':datac_desc' => $datac_desc,
				':datac_img' => $datacollection_image_final
			));

			$datac_id = $db->lastInsertId();

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
				$notification_title = "Nova informacija sa terena";
				$notification_icon = "briefcase";
				$notification_link = "" . getSiteUrlr() . "idkadmin/datacollection?page=open&id=${datac_id}";
				$notification_type = 4;

				addNotification($notification_title, $notification_icon, $notification_link, $notification_employeeid, $notification_type);
			}

			header("Location: datacollection?mess=1");

			break;

			/*-----------------------------------------------------------------------------------------
							DATACOLLECTIONS END
-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
							DEBTS START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					ADD DEBT
			 * *********************************************************/
		case "add_debt":

			if (isset($logged_client_id) and $logged_client_id != 0) {
				$debt_clientid = $logged_client_id;
			} elseif (isset($logged_employee_id) and $logged_employee_id != 0) {
				$debt_clientid = $_COOKIE['idk_session_front_client'];
			} else {
				$debt_clientid = NULL;
			}
			$debt_datetime = date('Y-m-d H:i:s');
			$debt_desc = !empty($_POST['debt_desc']) ? $_POST['debt_desc'] : NULL;
			$debt_equipment = !empty($_POST['debt_equipment']) ? $_POST['debt_equipment'] : NULL;
			$debt_quantity = !empty($_POST['debt_quantity']) ? $_POST['debt_quantity'] : NULL;

			//Upload and save debt_image
			$debt_image_final = uploadImage('debt', 0, $debt_clientid, 1);

			$query = $db->prepare("
				INSERT INTO idk_debt
					(debt_employeeid, debt_clientid, debt_datetime, debt_desc, debt_equipment, debt_quantity, debt_img)
				VALUES
					(:debt_employeeid, :debt_clientid, :debt_datetime, :debt_desc, :debt_equipment, :debt_quantity, :debt_img)");

			$query->execute(array(
				':debt_employeeid' => $logged_employee_id,
				':debt_clientid' => $debt_clientid,
				':debt_datetime' => $debt_datetime,
				':debt_desc' => $debt_desc,
				':debt_equipment' => $debt_equipment,
				':debt_quantity' => $debt_quantity,
				':debt_img' => $debt_image_final
			));

			$debt_id = $db->lastInsertId();

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
				$notification_title = "Novo zaduženje";
				$notification_icon = "briefcase";
				$notification_link = "" . getSiteUrlr() . "idkadmin/debts?page=open&id=${debt_id}";
				$notification_type = 4;

				addNotification($notification_title, $notification_icon, $notification_link, $notification_employeeid, $notification_type);
			}

			header("Location: debts?mess=1");

			break;

			/*-----------------------------------------------------------------------------------------
							DEBTS END
-----------------------------------------------------------------------------------------*/




			/*-----------------------------------------------------------------------------------------
							MILEAGE START
-----------------------------------------------------------------------------------------*/

			/************************************************************
			 * 					ADD MILEAGE FROM NEW MILEAGE
			 * *********************************************************/
		case "add_mileage_from_new_mileage":

			$mileage_employee_id = !empty($_POST['employee_id']) ? $_POST['employee_id'] : NULL;
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
					header("Location: newMileage?mess=6&mileage=${mileage_amount_start_from_db}");
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
				':mileage_amount_start' => $mileage_amount_start,
				':mileage_start_time' => $mileage_start_time
			));

			$mileage_id = $db->lastInsertId();

			//Add to log
			$log_desc = "Dodao novu kilometražu";
			$datetime = date('Y-m-d H:i:s');
			addLog($logged_employee_id, $log_desc, $datetime);

			header("Location: selectClient?mess=1");

			break;



			/************************************************************
			 * 					EDIT MILEAGE
			 * *********************************************************/
		case "edit_mileage":

			$mileage_id = !empty($_POST['mileage_id']) ? $_POST['mileage_id'] : NULL;
			$mileage_employee_id = !empty($_POST['employee_id']) ? $_POST['employee_id'] : NULL;
			$mileage_amount_start = !empty($_POST['mileage_amount_start']) ? $_POST['mileage_amount_start'] : NULL;
			$mileage_start_time = date('Y-m-d H:i:s');

			if (isset($mileage_id)) {

				$check_mileage_query = $db->prepare("
					SELECT mileage_amount_start
					FROM idk_mileage
					WHERE mileage_employee_id = :mileage_employee_id AND mileage_end_time IS NOT NULL
					ORDER BY mileage_id DESC");

				$check_mileage_query->execute(array(
					':mileage_employee_id' => $mileage_employee_id
				));

				$number_of_rows = $check_mileage_query->rowCount();

				if ($number_of_rows != 0) {

					$mileage = $check_mileage_query->fetch();
					$mileage_amount_start_from_db = $mileage['mileage_amount_start'];

					if ($mileage_amount_start >= $mileage_amount_start_from_db) {
						$query = $db->prepare("
							UPDATE idk_mileage
							SET mileage_amount_start = :mileage_amount_start, mileage_start_time = :mileage_start_time
							WHERE mileage_id = :mileage_id");

						$query->execute(array(
							':mileage_id' => $mileage_id,
							':mileage_start_time' => $mileage_start_time,
							':mileage_amount_start' => $mileage_amount_start
						));
					} else {
						header("Location: mileage?mess=3&mileage=${mileage_amount_start_from_db}");
						exit;
					}

					//Add to log
					$log_desc = "Uredio kilometražu s ID brojem: ${mileage_id}";
					$datetime = date('Y-m-d H:i:s');
					addLog($logged_employee_id, $log_desc, $datetime);

					header("Location: mileage?mess=1");
				} else {
					header("Location: mileage?mess=2");
				}
			} else {
				header("Location: mileage?mess=4");
			}

			break;

			/*-----------------------------------------------------------------------------------------
							MILEAGE END
-----------------------------------------------------------------------------------------*/
	}
}
