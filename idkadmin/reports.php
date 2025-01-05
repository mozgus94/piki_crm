<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
} else {
	header("Location: reports?page=list");
}

?>

<!DOCTYPE html>
<html>

<head>

	<?php include('includes/head.php'); ?>

</head>

<body>
	<header class="idk_display_none_for_print">
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar" class="idk_display_none_for_print">
		<?php include('menu.php'); ?>
	</div>
	<div id="content">
		<div class="container-fluid">
			<?php
			switch ($page) {



					/************************************************************
				 * 					LIST ALL REPORTS OPTIONS
				 * *********************************************************/
				case "list":

					if ($getEmployeeStatus == 1) {

						//Mark as read
						if (isset($_GET['nid'])) {
							$notification_id = $_GET['nid'];

							$query_update = $db->prepare("
								UPDATE idk_notifications
								SET	notification_status = :notification_status
								WHERE notification_id = :notification_id AND notification_datetime <= NOW()");

							$query_update->execute(array(
								':notification_status' => 2,
								':notification_id' => $notification_id
							));
						}

			?>

						<div class="row">
							<div class="col-md-12">
								<div class="content_box">

									<div class="row">
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/reports?page=reports_per_client" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Izvještaji komercijaliste po prodajnom mjestu</h6>
												</div>
											</a>
										</div>
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/reports?page=reports_per_order" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Prodaja</h6>
												</div>
											</a>
										</div>
										<!-- <div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php //getSiteUrl(); ?>idkadmin/reports?page=mileage" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Kilometraža</h6>
												</div>
											</a>
										</div> -->
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/reports?page=routes" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Rute</h6>
												</div>
											</a>
										</div>
										<!-- <div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/reports?page=assortment" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Stanje asortimana</h6>
												</div>
											</a>
										</div> -->
									</div>
								</div>
							</div>
						</div>
					<?php
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
					 * 					IZVJESTAJI KOMERCIJALISTE PO PRODAJNOM MJESTU
					 * *********************************************************/
				case "reports_per_client":

					if ($getEmployeeStatus == 1) {

						//Mark as read
						if (isset($_GET['nid'])) {
							$notification_id = $_GET['nid'];

							$query_update = $db->prepare("
								UPDATE idk_notifications
								SET	notification_status = :notification_status
								WHERE notification_id = :notification_id AND notification_datetime <= NOW()");

							$query_update->execute(array(
								':notification_status' => 2,
								':notification_id' => $notification_id
							));
						}

					?>

						<div class="row idk_display_none_for_print">
							<div class="col-xs-8">
								<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Izvještaji komercijaliste po prodajnom mjestu</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/reports?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row idk_display_none_for_print">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-md-12">
											<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" id="idk_print_report_btn">
												<i class="fa fa-print" aria-hidden="true"></i> <span>Print</span>
											</a>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6 idk_setting_form_wrapper">
											<h5>Odaberi komercijalistu i prodajno mjesto</h5>

											<!-- <form id="idk_form" action="#" method="post" class="form-horizontal" role="form"> -->
											<div class="form-horizontal">

												<div class="form-group">
													<label for="report_employee" class="col-sm-3 control-label">Komercijalista:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_employee" name="report_employee" data-live-search="true">
															<option value=""></option>
															<?php
															$select_query = $db->prepare("
																		SELECT employee_id, employee_first_name, employee_last_name
																		FROM idk_employee
																		WHERE employee_active = :employee_active AND (employee_status = :employee_status OR employee_status = :employee_status_admin)
                                    ORDER BY employee_last_name, employee_first_name");

															$select_query->execute(array(
																':employee_active' => 1,
																':employee_status' => 2,
																':employee_status_admin' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "' data-tokens='" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "'>" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_city" class="col-sm-3 control-label">Općina:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_city" name="report_city" data-live-search="true">
															<option value="">Odaberi općinu</option>
															<?php
															$select_query = $db->prepare("
																SELECT client_city
																FROM idk_client
																WHERE client_active = :client_active
																GROUP BY client_city
                                ORDER BY client_city");

															$select_query->execute(array(
																':client_active' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['client_city'] . "' data-tokens='" . $select_row['client_city'] . "'>" . $select_row['client_city'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_client" class="col-sm-3 control-label">Klijent:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_client" name="report_client" data-live-search="true">
															<option value=""></option>
															<?php
															$select_query = $db->prepare("
																SELECT client_id, client_name
																FROM idk_client
																WHERE client_active = :client_active
																ORDER BY client_name");

															$select_query->execute(array(
																':client_active' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['client_name'] . "' data-tokens='" . $select_row['client_name'] . "'>" . $select_row['client_name'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_order" class="col-sm-3 control-label">Narudžba:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_order" name="report_order" data-live-search="true">
															<option value=""></option>
															<option value="DA">DA</option>
															<option value="NE">NE</option>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_order_type" class="col-sm-3 control-label">Tip narudžbe:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_order_type" name="report_order_type" data-live-search="true">
															<option value=""></option>
															<option value="Na licu mjesta">Na licu mjesta</option>
															<option value="Telefonska narudžba">Telefonska narudžba</option>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_date_from" class="col-sm-3 control-label">Datum od:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="report_date_from" id="report_date_from" placeholder="Datum od">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$("#report_date_from").flatpickr({
															dateFormat: "d.m.Y.",
															maxDate: "today",
															"locale": "bs"
														});
													</script>
												</div>

												<div class="form-group">
													<label for="report_date_to" class="col-sm-3 control-label">Datum do:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="report_date_to" id="report_date_to" placeholder="Datum do">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$("#report_date_to").flatpickr({
															dateFormat: "d.m.Y.",
															maxDate: "today",
															"locale": "bs"
														});
													</script>
												</div>
											</div>
											<!-- </form> -->

										</div>

										<div class="col-sm-6 idk_setting_form_wrapper">
											<h5>Izvještaj</h5>

											<div class="form-horizontal">

												<div class="form-group">
													<label for="report_number_of_visits" class="col-sm-3 control-label">Broj posjeta:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="report_number_of_visits" id="report_number_of_visits" placeholder="Broj posjeta" readonly>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="report_number_of_orders" class="col-sm-3 control-label">Broj narudžbi:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="report_number_of_orders" id="report_number_of_orders" placeholder="Broj narudžbi" readonly>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="report_number_of_orders_na_licu_mjesta" class="col-sm-3 control-label">Narudžbe na licu mjesta:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="report_number_of_orders_na_licu_mjesta" id="report_number_of_orders_na_licu_mjesta" placeholder="Narudžbe na licu mjesta" readonly>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="report_number_of_orders_telefonski" class="col-sm-3 control-label">Telefonske narudžbe:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="report_number_of_orders_telefonski" id="report_number_of_orders_telefonski" placeholder="Telefonske narudžbe" readonly>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

											</div>
										</div>
									</div>

									<div class="row idk_margin_top20">
										<div class="col-xs-12">

											<script type="text/javascript">
												$(document).ready(function() {

													function getReport(reportTable) {
														var totalVisits = reportTable.rows({
															'search': 'applied'
														}).count();
														var totalOrders = reportTable.column(7, {
															'search': 'applied'
														}).data().filter(function(value, index) {
															return value == 'DA' ? true : false;
														});
														var totalOrdersNaLicuMjesta = reportTable.column(8, {
															'search': 'applied'
														}).data().filter(function(value, index) {
															return value == 'Na licu mjesta' ? true : false;
														});
														var totalOrdersTelefonski = reportTable.column(8, {
															'search': 'applied'
														}).data().filter(function(value, index) {
															return value == 'Telefonska narudžba' ? true : false;
														});

														$('#report_number_of_visits').val(totalVisits);
														$('#report_number_of_orders').val(totalOrders.count());
														$('#report_number_of_orders_na_licu_mjesta').val(totalOrdersNaLicuMjesta.count());
														$('#report_number_of_orders_telefonski').val(totalOrdersTelefonski.count());
													}

													var reportTable = $('#idk_table').DataTable({

														"destroy": true,

														responsive: true,

														"order": [
															[0, "desc"]
														],

														"bAutoWidth": false,

														"aoColumns": [{
																"width": "10%"
															},
															{
																"width": "10%"
															},
															{
																"width": "10%"
															},
															{
																"width": "10%"
															},
															{
																"width": "10%"
															},
															{
																"width": "10%"
															},
															{
																"width": "15%"
															},
															{
																"width": "10%"
															},
															{
																"width": "15%"
															}
														]
													});

													getReport(reportTable);

													$("#report_employee").change(function() {
														var employeeName = $(this).val();
														reportTable.column(1).search(employeeName);
														reportTable.draw();
														getReport(reportTable);
													});

													$("#report_city").change(function() {
														var cityName = $(this).val();
														reportTable = reportTable.column(3).search(cityName);
														reportTable = reportTable.draw();
														getReport(reportTable);
													});

													$("#report_client").change(function() {
														var clientName = $(this).val();
														reportTable = reportTable.column(2).search(clientName);
														reportTable = reportTable.draw();
														getReport(reportTable);
													});

													$("#report_order_type").change(function() {
														var orderType = $(this).val();
														reportTable.column(8).search(orderType);
														reportTable.draw();
														getReport(reportTable);
													});

													$("#report_order").change(function() {
														var order = $(this).val();
														reportTable.column(7).search(order);
														reportTable.draw();
														getReport(reportTable);
													});

													$("#report_date_from").change(function() {
														var dateFrom = $(this).val();
														var str = "";
														if (dateFrom) {
															dateFrom = new Date(dateFrom.split('.').reverse().join('-').substr(1));
															reportTable.column(4).data().each(function(value, index) {
																var date = value;
																var dateNewFormat = value.split(' ')[0];
																dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																var dateNew = new Date(dateNewFormat);
																if (dateNew >= dateFrom) {
																	str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																}
															});
															str = str.substring(0, str.length - 1);
															if (str == "") {
																str = "Nema rezultata!";
															}
															reportTable.column(4).search(str, true, false);
														} else {
															reportTable.column(4).search('');
														}
														reportTable.draw();
														getReport(reportTable);
													});

													$("#report_date_to").change(function() {
														var dateTo = $(this).val();
														var str = "";
														if (dateTo) {
															dateTo = new Date(dateTo.split('.').reverse().join('-').substr(1));
															reportTable.column(5).data().each(function(value, index) {
																var date = value;
																var dateNewFormat = value.split(' ')[0];
																dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																var dateNew = new Date(dateNewFormat);
																if (dateNew <= dateTo) {
																	str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																}
															});
															str = str.substring(0, str.length - 1);
															if (str == "") {
																str = "Nema rezultata!";
															}
															reportTable.column(5).search(str, true, false);
														} else {
															reportTable.column(5).search('');
														}
														reportTable.draw();
														getReport(reportTable);
													});
												});
											</script>

											<!-- Reports table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>ID</th>
														<th>Komercijalista</th>
														<th>Klijent</th>
														<th>Općina</th>
														<th>Vijeme prijave</th>
														<th>Vijeme odjave</th>
														<th>Trajanje</th>
														<th>Narudžba</th>
														<th>Tip narudžbe</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$query = $db->prepare("
															SELECT report_id, employee_id, client_id, order_id, report_start_time, report_end_time
															FROM idk_report
															WHERE report_end_time IS NOT NULL");

													$query->execute();

													while ($row = $query->fetch()) {

														$report_id = $row['report_id'];
														$employee_id = $row['employee_id'];
														$client_id = $row['client_id'];
														$order_id = $row['order_id'];
														$report_start_time = $row['report_start_time'];
														$report_end_time = $row['report_end_time'];

													?>
														<tr>
															<td>
																<?php echo $report_id; ?>
															</td>
															<td>
																<?php
																$query_employee = $db->prepare("
																	SELECT employee_first_name, employee_last_name
																	FROM idk_employee
																	WHERE employee_id = :employee_id");

																$query_employee->execute(array(
																	':employee_id' => $employee_id
																));

																$row_employee = $query_employee->fetch();

																$employee_first_name = $row_employee['employee_first_name'];
																$employee_last_name = $row_employee['employee_last_name'];

																echo '<a href="' . getSiteUrlr() . 'idkadmin/employees?page=open&id=' . $employee_id . '">' . $employee_first_name . ' ' . $employee_last_name . '</a>';
																?>
															</td>
															<td>
																<?php
																$query_client = $db->prepare("
																	SELECT client_name, client_city
																	FROM idk_client
																	WHERE client_id = :client_id");

																$query_client->execute(array(
																	':client_id' => $client_id
																));

																$row_client = $query_client->fetch();

																$client_name = $row_client['client_name'];
																$client_city = $row_client['client_city'];

																echo '<a href="' . getSiteUrlr() . 'idkadmin/clients?page=open&id=' . $client_id . '">' . $client_name . '</a>';
																?>
															</td>
															<td>
																<?php echo $client_city; ?>
															</td>
															<td>
																<?php echo date('d.m.Y. H:i:s', strtotime($report_start_time)); ?>
															</td>
															<td>
																<?php echo date('d.m.Y. H:i:s', strtotime($report_end_time)); ?>
															</td>
															<td>
																<?php

																//Difference between two datetimes
																$first_date = new DateTime($report_start_time);
																$second_date = new DateTime($report_end_time);
																$interval = $first_date->diff($second_date);

																echo $interval->format('%Hh : %Im : %Ss');
																?>
															</td>
															<td>
																<?php
																if (isset($order_id)) {
																	echo "DA";
																} else {
																	echo "NE";
																}
																?>
															</td>
															<td>
																<?php
																if (isset($order_id)) {
																	$query_order = $db->prepare("
																		SELECT order_type
																		FROM idk_order
																		WHERE order_id = :order_id");

																	$query_order->execute(array(
																		':order_id' => $order_id
																	));

																	$number_of_rows = $query_order->rowCount();

																	if ($number_of_rows == 1) {
																		$row_order = $query_order->fetch();

																		$order_type = $row_order['order_type'];

																		if ($order_type == 1) {
																			echo "Na licu mjesta";
																		} elseif ($order_type == 2) {
																			echo "Telefonska narudžba";
																		}
																	}
																}
																?>
															</td>
														</tr>
													<?php } ?>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

						<?php
						$owner_query = $db->prepare("
							SELECT owner_name, owner_image
							FROM idk_owner");

						$owner_query->execute();

						$owner = $owner_query->fetch();

						$owner_name = $owner['owner_name'];
						$owner_image = $owner['owner_image'];
						?>

						<!-- Print report wrapper -->
						<div id="idk_print_report_wrapper" style="display: none;">
							<div id="print_header">
								<div class="container-fluid">
									<div class="row">
										<div class="col-xs-6">
											<h3>Izvještaj komercijaliste</h3>
											<p class="idk_margin_top30" id="idk_print_header_right_col"></p>
										</div>
										<div class="col-xs-6 text-right">
											<img src="<?php getSiteUrl(); ?>idkadmin/files/owners/images/<?php echo $owner_image; ?>" class="idk_print_logo" alt="<?php echo $owner_name; ?> logo">
											<p class="idk_margin_top30">
												<strong>Unaviva d.o.o.</strong> <br>
												Dr. Irfana Ljubijankića 87 <br>
												77000 Bihać <br>
												Tel: 00 387 37 961 131 <br>
												E-Mail: info@unaviva.ba <br>
												Web: www.unaviva.ba <br>
											</p>
										</div>
									</div>
								</div>
							</div>

							<div id="print_main">
								<div class="container-fluid">
									<div class="row idk_margin_top50">
										<div class="col-xs-6">
											<h5>Parametri</h5>
											<div class="row idk_margin_top30" id="idk_print_main_left_col"></div>
										</div>

										<div class="col-xs-6" id="idk_print_main_right_row">
											<h5>Izvještaj</h5>
											<div class="row idk_margin_top30" id="idk_print_main_right_col"></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<script type="text/javascript">
							$(document).ready(function() {
								$('#idk_print_report_btn').click(function() {
									let headerRightCol = '';
									let mainLeftCol = '';
									let mainRightCol = '';
									let report = '';

									if ($('#report_employee').val() && $('#report_employee').val().length > 0) {
										headerRightCol += '<strong>Komercijalista</strong><br>' + $('#report_employee').val();
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Komercijalista:</strong></p></div><div class="col-xs-7"><p>' + $('#report_employee').val() + '</p></div></div>';
									}
									if ($('#report_city').val() && $('#report_city').val().length > 0) {
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Općina:</strong></p></div><div class="col-xs-7"><p>' + $('#report_city').val() + '</p></div></div>';
									}
									if ($('#report_client').val() && $('#report_client').val().length > 0) {
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Klijent:</strong></p></div><div class="col-xs-7"><p>' + $('#report_client').val() + '</p></div></div>';
									}
									if ($('#report_order').val() && $('#report_order').val().length > 0) {
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Narudžba:</strong></p></div><div class="col-xs-7"><p>' + $('#report_order').val() + '</p></div></div>';
									}
									if ($('#report_order_type').val() && $('#report_order_type').val().length > 0) {
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Tip narudžbe:</strong></p></div><div class="col-xs-7"><p>' + $('#report_order_type').val() + '</p></div></div>';
									}
									if ($('#report_date_from').val() && $('#report_date_from').val().length > 0) {
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Datum od:</strong></p></div><div class="col-xs-7"><p>' + $('#report_date_from').val() + '</p></div></div>';
									}
									if ($('#report_date_to').val() && $('#report_date_to').val().length > 0) {
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Datum do:</strong></p></div><div class="col-xs-7"><p>' + $('#report_date_to').val() + '</p></div></div>';
									}

									mainRightCol += '<div class="row"><div class="col-xs-7"><p><strong>Broj posjeta:</strong></p></div><div class="col-xs-5"><p>' + $('#report_number_of_visits').val() + '</p></div></div>';
									mainRightCol += '<div class="row"><div class="col-xs-7"><p><strong>Broj narudžbi:</strong></p></div><div class="col-xs-5"><p>' + $('#report_number_of_orders').val() + '</p></div></div>';
									mainRightCol += '<div class="row"><div class="col-xs-7"><p><strong>Narudžbe na licu mjesta:</strong></p></div><div class="col-xs-5"><p>' + $('#report_number_of_orders_na_licu_mjesta').val() + '</p></div></div>';
									mainRightCol += '<div class="row"><div class="col-xs-7"><p><strong>Telefonske narudžbe:</strong></p></div><div class="col-xs-5"><p>' + $('#report_number_of_orders_telefonski').val() + '</p></div></div>';
									$('#idk_print_header_right_col').html(headerRightCol);
									$('#idk_print_main_left_col').html(mainLeftCol);
									$('#idk_print_main_right_col').html(mainRightCol);
									$('.idk_display_none_for_print').css('display', 'none');
									$('#content').css('background-color', '#fff');
									$('#content').css('margin', '0');
									$('#content').css('padding', '0');
									$('#idk_print_report_wrapper').css('display', 'block');
									window.print();
									$('#idk_print_report_wrapper').css('display', 'none');
									$('#content').css('background-color', '#eee');
									$('#content').css('margin-left', '220px');
									$('#content').css('padding-top', '65px');
									$('.idk_display_none_for_print').css('display', 'block');
								});
							});
						</script>

					<?php
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
					 * 					PRODAJA
					 * *********************************************************/
				case "reports_per_order":

					if ($getEmployeeStatus == 1) {

						//Mark as read
						if (isset($_GET['nid'])) {
							$notification_id = $_GET['nid'];

							$query_update = $db->prepare("
								UPDATE idk_notifications
								SET	notification_status = :notification_status
								WHERE notification_id = :notification_id AND notification_datetime <= NOW()");

							$query_update->execute(array(
								':notification_status' => 2,
								':notification_id' => $notification_id
							));
						}

					?>

						<div class="row idk_display_none_for_print">
							<div class="col-xs-8">
								<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Prodaja</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/reports?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row idk_display_none_for_print">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-md-12">
											<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" id="idk_print_report_btn">
												<i class="fa fa-print" aria-hidden="true"></i> <span>Print</span>
											</a>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6 idk_setting_form_wrapper">
											<h5>Odaberi općinu i period</h5>

											<!-- <form id="idk_form" action="#" method="post" class="form-horizontal" role="form"> -->
											<div class="form-horizontal">

												<div class="form-group">
													<label for="report_city" class="col-sm-3 control-label">Općina:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_city" name="report_city" data-live-search="true">
															<option value="">Odaberi općinu</option>
															<?php
															$select_query = $db->prepare("
																SELECT client_city
																FROM idk_client
																WHERE client_active = :client_active
																GROUP BY client_city
																ORDER BY client_city");

															$select_query->execute(array(
																':client_active' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['client_city'] . "' data-tokens='" . $select_row['client_city'] . "'>" . $select_row['client_city'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_employee" class="col-sm-3 control-label">Komercijalista:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_employee" name="report_employee" data-live-search="true">
															<option value=""></option>
															<?php
															$select_query = $db->prepare("
																SELECT employee_id, employee_first_name, employee_last_name
																FROM idk_employee
																WHERE employee_active = :employee_active AND (employee_status = :employee_status OR employee_status = :employee_status_admin)
																ORDER BY employee_last_name, employee_first_name");

															$select_query->execute(array(
																':employee_active' => 1,
																':employee_status' => 2,
																':employee_status_admin' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "' data-tokens='" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "'>" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_client" class="col-sm-3 control-label">Klijent:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_client" name="report_client" data-live-search="true">
															<option value=""></option>
															<?php
															$select_query = $db->prepare("
																SELECT client_id, client_name
																FROM idk_client
																WHERE client_active = :client_active
																ORDER BY client_name");

															$select_query->execute(array(
																':client_active' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['client_name'] . "' data-tokens='" . $select_row['client_name'] . "'>" . $select_row['client_name'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_order_type" class="col-sm-3 control-label">Tip narudžbe:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_order_type" name="report_order_type" data-live-search="true">
															<option value=""></option>
															<option value="Na licu mjesta">Na licu mjesta</option>
															<option value="Telefonska narudžba">Telefonska narudžba</option>
															<option value="Narudžba od strane klijenta">Narudžba od strane klijenta</option>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_order_status" class="col-sm-3 control-label">Status narudžbe:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_order_status" name="report_order_status" data-live-search="true">
															<option value=""></option>
															<?php
															$query = $db->prepare("
																SELECT od_id, od_data, od_value
																FROM idk_order_otherdata
																ORDER BY od_data");

															$query->execute();

															while ($order_otherdata = $query->fetch()) {
																echo "<option value='" . $order_otherdata['od_data'] . "' data-tokens='" . $order_otherdata['od_data'] . "'>" . $order_otherdata['od_data'] . "</option>";
															} ?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_date_from" class="col-sm-3 control-label">Datum od:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="report_date_from" id="report_date_from" placeholder="Datum od">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$("#report_date_from").flatpickr({
															dateFormat: "d.m.Y.",
															maxDate: "today",
															"locale": "bs"
														});
													</script>
												</div>

												<div class="form-group">
													<label for="report_date_to" class="col-sm-3 control-label">Datum do:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="report_date_to" id="report_date_to" placeholder="Datum do">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$("#report_date_to").flatpickr({
															dateFormat: "d.m.Y.",
															maxDate: "today",
															"locale": "bs"
														});
													</script>
												</div>
											</div>
											<!-- </form> -->

										</div>

										<div class="col-sm-6 idk_setting_form_wrapper">
											<h5>Izvještaj</h5>

											<div class="form-horizontal">

												<div class="form-group">
													<label for="report_number_of_orders" class="col-sm-3 control-label">Broj narudžbi:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="report_number_of_orders" id="report_number_of_orders" placeholder="Broj narudžbi" readonly>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="report_number_of_orders_na_licu_mjesta" class="col-sm-3 control-label">Narudžbe na licu mjesta:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="report_number_of_orders_na_licu_mjesta" id="report_number_of_orders_na_licu_mjesta" placeholder="Narudžbe na licu mjesta" readonly>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="report_number_of_orders_telefonski" class="col-sm-3 control-label">Telefonske narudžbe:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="report_number_of_orders_telefonski" id="report_number_of_orders_telefonski" placeholder="Telefonske narudžbe" readonly>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="report_number_of_orders_client" class="col-sm-3 control-label">Narudžbe od strane klijenta:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="report_number_of_orders_client" id="report_number_of_orders_client" placeholder="Narudžbe od strane klijenta" readonly>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="report_orders_total_amount" class="col-sm-3 control-label">Ukupan iznos narudžbi:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="report_orders_total_amount" id="report_orders_total_amount" placeholder="Ukupan iznos narudžbi" readonly>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="report_orders_average_amount" class="col-sm-3 control-label">Prosječan iznos narudžbe:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="report_orders_average_amount" id="report_orders_average_amount" placeholder="Prosječan iznos narudžbe" readonly>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

											</div>
										</div>
									</div>

									<div class="row idk_margin_top20">
										<div class="col-xs-12">

											<script type="text/javascript">
												$(document).ready(function() {

													function getReport(reportTable) {
														var totalValue = 0;
														var averageValue = 0;
														reportTable.column(4, {
															'search': 'applied'
														}).data().filter(function(value, index) {
															value = value.replace('.', '').replace(',', '.');
															value = parseFloat(value);
															totalValue += value;
														});
														var totalOrders = reportTable.rows({
															'search': 'applied'
														}).count();
														var totalOrdersNaLicuMjesta = reportTable.column(6, {
															'search': 'applied'
														}).data().filter(function(value, index) {
															return value == 'Na licu mjesta' ? true : false;
														});
														var totalOrdersTelefonski = reportTable.column(6, {
															'search': 'applied'
														}).data().filter(function(value, index) {
															return value == 'Telefonska narudžba' ? true : false;
														});
														var totalOrdersClient = reportTable.column(6, {
															'search': 'applied'
														}).data().filter(function(value, index) {
															return value == 'Narudžba od strane klijenta' ? true : false;
														});

														if (totalOrders) {
															averageValue = totalValue / totalOrders;
														}

														$('#report_number_of_orders').val(totalOrders);
														$('#report_number_of_orders_na_licu_mjesta').val(totalOrdersNaLicuMjesta.count());
														$('#report_number_of_orders_telefonski').val(totalOrdersTelefonski.count());
														$('#report_number_of_orders_client').val(totalOrdersClient.count());
														$('#report_orders_total_amount').val(totalValue.toFixed(2).replace('.', ',').replace(/\d(?=(\d{3})+\,)/g, '$&.') + ' KM');
														$('#report_orders_average_amount').val(averageValue.toFixed(2).replace('.', ',').replace(/\d(?=(\d{3})+\,)/g, '$&.') + ' KM');
													}

													var reportTable = $('#idk_table').DataTable({

														"destroy": true,

														responsive: true,

														"order": [
															[0, "desc"]
														],

														"bAutoWidth": false,

														"aoColumns": [{
																"width": "10%"
															},
															{
																"width": "15%"
															},
															{
																"width": "15%"
															},
															{
																"width": "10%"
															},
															{
																"width": "10%"
															},
															{
																"width": "10%"
															},
															{
																"width": "15%"
															},
															{
																"width": "15%"
															}
														]
													});

													getReport(reportTable);

													$("#report_city").change(function() {
														var cityName = $(this).val();
														reportTable.column(3).search(cityName);
														reportTable.draw();
														getReport(reportTable);
													});

													$("#report_employee").change(function() {
														var employeeName = $(this).val();
														reportTable.column(1).search(employeeName);
														reportTable.draw();
														getReport(reportTable);
													});

													$("#report_client").change(function() {
														var clientName = $(this).val();
														reportTable.column(2).search(clientName);
														reportTable.draw();
														getReport(reportTable);
													});

													$("#report_order_type").change(function() {
														var orderType = $(this).val();
														reportTable.column(6).search(orderType);
														reportTable.draw();
														getReport(reportTable);
													});

													$("#report_order_status").change(function() {
														var orderStatus = $(this).val();
														reportTable.column(7).search(orderStatus);
														reportTable.draw();
														getReport(reportTable);
													});

													$("#report_date_from").change(function() {
														var dateFrom = $(this).val();
														var dateTo = $("#report_date_to").val();
														var str = "";
														if (dateFrom) {
															dateFrom = new Date(dateFrom.split('.').reverse().join('-').substr(1));
															if (dateTo) {
																dateTo = new Date(dateTo.split('.').reverse().join('-').substr(1));
																reportTable.column(5).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];
																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew >= dateFrom && dateNew <= dateTo) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															} else {
																reportTable.column(5).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];
																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew >= dateFrom) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															}
															str = str.substring(0, str.length - 1);
															if (str == "") {
																str = "Nema rezultata!";
															}
															reportTable.column(5).search(str, true, false);
														} else {
															reportTable.column(5).search('');
														}
														reportTable.draw();
														getReport(reportTable);
													});

													$("#report_date_to").change(function() {
														var dateTo = $(this).val();
														var dateFrom = $("#report_date_from").val();
														var str = "";
														if (dateTo) {
															dateTo = new Date(dateTo.split('.').reverse().join('-').substr(1));
															if (dateFrom) {
																dateFrom = new Date(dateFrom.split('.').reverse().join('-').substr(1));
																reportTable.column(5).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];
																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew >= dateFrom && dateNew <= dateTo) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															} else {
																reportTable.column(5).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];
																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew <= dateTo) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															}
															str = str.substring(0, str.length - 1);
															if (str == "") {
																str = "Nema rezultata!";
															}
															reportTable.column(5).search(str, true, false);
														} else {
															reportTable.column(5).search('');
														}
														reportTable.draw();
														getReport(reportTable);
													});
												});
											</script>

											<!-- Reports table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>ID</th>
														<th>Komercijalista</th>
														<th>Klijent</th>
														<th>Općina</th>
														<th>Za platiti</th>
														<th>Datum</th>
														<th>Tip narudžbe</th>
														<th>Status</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$query = $db->prepare("
														SELECT t1.order_id, t1.client_id, t1.employee_id, t1.order_status, t1.order_total_price, t1.order_total_rabat, t1.order_total_tax, t1.order_to_pay, t1.created_at, t1.order_type, t2.client_name, t2.client_city, t3.od_value, t3.od_data
                            FROM idk_order t1, idk_client t2, idk_order_otherdata t3
                            WHERE t1.order_status != 0 AND t2.client_id = t1.client_id AND t1.order_status = t3.od_id");

													$query->execute();

													while ($row = $query->fetch()) {

														$client_id = $row['client_id'];
														$client_name = $row['client_name'];
														$client_city = $row['client_city'];
														$order_employee_id = $row['employee_id'];
														$order_id = $row['order_id'];
														$order_type = $row['order_type'];
														$order_status = $row['order_status'];
														$order_total_price = $row['order_total_price'];
														$order_total_rabat = $row['order_total_rabat'];
														$order_total_tax = $row['order_total_tax'];
														$order_to_pay = $row['order_to_pay'];
														$order_created_at = $row['created_at'];
														$order_created_at_new_format = date('d.m.Y.', strtotime($row['created_at']));
														$order_color = $row['od_value'];
														$od_data = $row['od_data'];

													?>
														<tr>
															<td>
																<a href="<?php getSiteUrl(); ?>idkadmin/orders?page=open&order_id=<?php echo $order_id; ?>"><?php echo $order_id; ?></a>
															</td>
															<td>
																<?php
																if (isset($order_employee_id)) {
																	$query_employee = $db->prepare("
																		SELECT employee_first_name, employee_last_name
																		FROM idk_employee
																		WHERE employee_id = :employee_id");

																	$query_employee->execute(array(
																		':employee_id' => $order_employee_id
																	));

																	$row_employee = $query_employee->fetch();

																	echo '<a href="' . getSiteUrlr() . 'idkadmin/employees?page=open&id=' . $order_employee_id . '">' . $row_employee['employee_first_name'] . ' ' . $row_employee['employee_last_name'] . '</a>';
																}
																?>
															</td>
															<td>
																<?php echo '<a href="' . getSiteUrlr() . 'idkadmin/clients?page=open&id=' . $client_id . '">' . $client_name . '</a>'; ?>
															</td>
															<td>
																<?php echo $client_city; ?>
															</td>
															<td>
																<?php echo number_format($order_to_pay, 2, ',', '.'); ?> KM
															</td>
															<td data-sort="<?php echo $order_created_at; ?>">
																<?php echo $order_created_at_new_format; ?>
															</td>
															<td>
																<?php
																if ($order_type == 1) {
																	echo "Na licu mjesta";
																} elseif ($order_type == 2) {
																	echo "Telefonska narudžba";
																} elseif ($order_type == 3) {
																	echo "Narudžba od strane klijenta";
																}
																?>
															</td>
															<td>
																<?php echo $od_data; ?>
															</td>
														</tr>
													<?php } ?>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

						<?php
						$owner_query = $db->prepare("
							SELECT owner_name, owner_image
							FROM idk_owner");

						$owner_query->execute();

						$owner = $owner_query->fetch();

						$owner_name = $owner['owner_name'];
						$owner_image = $owner['owner_image'];
						?>

						<!-- Print report wrapper -->
						<div id="idk_print_report_wrapper" style="display: none;">
							<div id="print_header">
								<div class="container-fluid">
									<div class="row">
										<div class="col-xs-6">
											<h3>Prodaja po općinama</h3>
											<p class="idk_margin_top30" id="idk_print_header_right_col"></p>
										</div>
										<div class="col-xs-6 text-right">
											<img src="<?php getSiteUrl(); ?>idkadmin/files/owners/images/<?php echo $owner_image; ?>" class="idk_print_logo" alt="<?php echo $owner_name; ?> logo">
											<p class="idk_margin_top30">
												<strong>Unaviva d.o.o.</strong> <br>
												Dr. Irfana Ljubijankića 87 <br>
												77000 Bihać <br>
												Tel: 00 387 37 961 131 <br>
												E-Mail: info@unaviva.ba <br>
												Web: www.unaviva.ba <br>
											</p>
										</div>
									</div>
								</div>
							</div>

							<div id="print_main">
								<div class="container-fluid">
									<div class="row idk_margin_top50">
										<div class="col-xs-6">
											<h5>Parametri</h5>
											<div class="row idk_margin_top30" id="idk_print_main_left_col"></div>
										</div>

										<div class="col-xs-6" id="idk_print_main_right_row">
											<h5>Izvještaj</h5>
											<div class="row idk_margin_top30" id="idk_print_main_right_col"></div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<script type="text/javascript">
							$(document).ready(function() {
								$('#idk_print_report_btn').click(function() {
									let headerRightCol = '';
									let mainLeftCol = '';
									let mainRightCol = '';
									let report = '';

									if ($('#report_city').val() && $('#report_city').val().length > 0) {
										headerRightCol += '<strong>Općina</strong><br>' + $('#report_city').val();
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Općina:</strong></p></div><div class="col-xs-7"><p>' + $('#report_city').val() + '</p></div></div>';
									}
									if ($('#report_employee').val() && $('#report_employee').val().length > 0) {
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Komercijalista:</strong></p></div><div class="col-xs-7"><p>' + $('#report_employee').val() + '</p></div></div>';
									}
									if ($('#report_client').val() && $('#report_client').val().length > 0) {
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Klijent:</strong></p></div><div class="col-xs-7"><p>' + $('#report_client').val() + '</p></div></div>';
									}
									if ($('#report_order_type').val() && $('#report_order_type').val().length > 0) {
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Tip narudžbe:</strong></p></div><div class="col-xs-7"><p>' + $('#report_order_type').val() + '</p></div></div>';
									}
									if ($('#report_order_status').val() && $('#report_order_status').val().length > 0) {
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Status narudžbe:</strong></p></div><div class="col-xs-7"><p>' + $('#report_order_status').val() + '</p></div></div>';
									}
									if ($('#report_date_from').val() && $('#report_date_from').val().length > 0) {
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Datum od:</strong></p></div><div class="col-xs-7"><p>' + $('#report_date_from').val() + '</p></div></div>';
									}
									if ($('#report_date_to').val() && $('#report_date_to').val().length > 0) {
										mainLeftCol += '<div class="row"><div class="col-xs-5"><p><strong>Datum do:</strong></p></div><div class="col-xs-7"><p>' + $('#report_date_to').val() + '</p></div></div>';
									}

									mainRightCol += '<div class="row"><div class="col-xs-7"><p><strong>Broj narudžbi:</strong></p></div><div class="col-xs-5"><p>' + $('#report_number_of_orders').val() + '</p></div></div>';
									mainRightCol += '<div class="row"><div class="col-xs-7"><p><strong>Narudžbe na licu mjesta:</strong></p></div><div class="col-xs-5"><p>' + $('#report_number_of_orders_na_licu_mjesta').val() + '</p></div></div>';
									mainRightCol += '<div class="row"><div class="col-xs-7"><p><strong>Telefonske narudžbe:</strong></p></div><div class="col-xs-5"><p>' + $('#report_number_of_orders_telefonski').val() + '</p></div></div>';
									mainRightCol += '<div class="row"><div class="col-xs-7"><p><strong>Narudžbe od strane klijenta:</strong></p></div><div class="col-xs-5"><p>' + $('#report_number_of_orders_client').val() + '</p></div></div>';
									mainRightCol += '<div class="row"><div class="col-xs-7"><p><strong>Ukupan iznos narudžbi:</strong></p></div><div class="col-xs-5"><p>' + $('#report_orders_total_amount').val() + '</p></div></div>';
									mainRightCol += '<div class="row"><div class="col-xs-7"><p><strong>Prosječan iznos narudžbe:</strong></p></div><div class="col-xs-5"><p>' + $('#report_orders_average_amount').val() + '</p></div></div>';
									$('#idk_print_header_right_col').html(headerRightCol);
									$('#idk_print_main_left_col').html(mainLeftCol);
									$('#idk_print_main_right_col').html(mainRightCol);
									$('.idk_display_none_for_print').css('display', 'none');
									$('#content').css('background-color', '#fff');
									$('#content').css('margin', '0');
									$('#content').css('padding', '0');
									$('#idk_print_report_wrapper').css('display', 'block');
									window.print();
									$('#idk_print_report_wrapper').css('display', 'none');
									$('#content').css('background-color', '#eee');
									$('#content').css('margin-left', '220px');
									$('#content').css('padding-top', '65px');
									$('.idk_display_none_for_print').css('display', 'block');
								});
							});
						</script>

					<?php
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
					 * 					KILOMETRAZA
					 * *********************************************************/
				case "mileage":

					if ($getEmployeeStatus == 1) {

						//Mark as read
						if (isset($_GET['nid'])) {
							$notification_id = $_GET['nid'];

							$query_update = $db->prepare("
								UPDATE idk_notifications
								SET	notification_status = :notification_status
								WHERE notification_id = :notification_id AND notification_datetime <= NOW()");

							$query_update->execute(array(
								':notification_status' => 2,
								':notification_id' => $notification_id
							));
						}

					?>

						<div class="row idk_display_none_for_print">
							<div class="col-xs-8">
								<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Kilometraža</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/reports?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row idk_display_none_for_print">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-md-12">
											<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" id="idk_print_report_btn">
												<i class="fa fa-print" aria-hidden="true"></i> <span>Print</span>
											</a>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6 idk_setting_form_wrapper">
											<h5>Odaberi komercijalistu i period</h5>

											<!-- <form id="idk_form" action="#" method="post" class="form-horizontal" role="form"> -->
											<div class="form-horizontal">

												<div class="form-group">
													<label for="report_employee" class="col-sm-3 control-label">Komercijalista:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_employee" name="report_employee" data-live-search="true">
															<option value=""></option>
															<?php
															$select_query = $db->prepare("
																		SELECT employee_id, employee_first_name, employee_last_name
																		FROM idk_employee
																		WHERE employee_active = :employee_active AND (employee_status = :employee_status OR employee_status = :employee_status_admin)
                                    ORDER BY employee_last_name, employee_first_name");

															$select_query->execute(array(
																':employee_active' => 1,
																':employee_status' => 2,
																':employee_status_admin' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . '|' . $select_row['employee_id'] . "' data-tokens='" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "'>" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_date_from" class="col-sm-3 control-label">Datum od:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="report_date_from" id="report_date_from" placeholder="Datum od">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$("#report_date_from").flatpickr({
															dateFormat: "d.m.Y.",
															maxDate: "today",
															"locale": "bs"
														});
													</script>
												</div>

												<div class="form-group">
													<label for="report_date_to" class="col-sm-3 control-label">Datum do:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="report_date_to" id="report_date_to" placeholder="Datum do">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$("#report_date_to").flatpickr({
															dateFormat: "d.m.Y.",
															maxDate: "today",
															"locale": "bs"
														});
													</script>
												</div>
											</div>
											<!-- </form> -->

										</div>

										<div class="col-sm-6 idk_setting_form_wrapper">
											<h5>Izvještaj</h5>

											<div class="form-horizontal">

												<div class="form-group">
													<label for="report_mileage_total_amount" class="col-sm-3 control-label">Ukupno kilometara:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="report_mileage_total_amount" id="report_mileage_total_amount" placeholder="Ukupno kilometara" readonly>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

											</div>
										</div>
									</div>

									<div class="row idk_margin_top20">
										<div class="col-xs-12">

											<script type="text/javascript">
												$(document).ready(function() {

													function getReport(reportTable) {
														var totalValue = 0;
														var startArray = [];
														var endArray = [];
														reportTable.column(3, {
															'search': 'applied'
														}).data().filter(function(value, index) {
															startArray.push(value.split(' ')[0]);
														});
														reportTable.column(5, {
															'search': 'applied'
														}).data().filter(function(value, index) {
															endArray.push(value.split(' ')[0]);
														});
														for (let i = 0; i < startArray.length; i++) {
															if (endArray[i] && endArray[i].length > 0) {
																totalValue += (endArray[i] - startArray[i]);
															}
														}

														$('#report_mileage_total_amount').val(totalValue + ' km');
													}

													var reportTable = $('#idk_table').DataTable({

														"destroy": true,

														responsive: true,

														"order": [
															[0, "desc"]
														],

														"bAutoWidth": false,

														"aoColumns": [{
																"width": "10%"
															},
															{
																"width": "15%"
															},
															{
																"width": "15%"
															},
															{
																"width": "15%"
															},
															{
																"width": "15%"
															},
															{
																"width": "15%"
															},
															{
																"width": "15%"
															}
														]
													});

													getReport(reportTable);

													$("#report_employee").change(function() {
														var employeeName = $(this).val().split('|')[0];
														reportTable.column(1).search(employeeName);
														reportTable.draw();
														getReport(reportTable);
													});

													$("#report_date_from").change(function() {
														var dateFrom = $(this).val();
														var dateTo = $("#report_date_to").val();
														var str = "";
														if (dateFrom) {
															dateFrom = new Date(dateFrom.split('.').reverse().join('-').substr(1));
															if (dateTo) {
																dateTo = new Date(dateTo.split('.').reverse().join('-').substr(1));
																reportTable.column(2).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];
																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew >= dateFrom && dateNew <= dateTo) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															} else {
																reportTable.column(2).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];
																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew >= dateFrom) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															}
															str = str.substring(0, str.length - 1);
															if (str == "") {
																str = "Nema rezultata!";
															}
															reportTable.column(2).search(str, true, false);
														} else {
															reportTable.column(2).search('');
														}
														reportTable.draw();
														getReport(reportTable);
													});

													$("#report_date_to").change(function() {
														var dateTo = $(this).val();
														var dateFrom = $("#report_date_from").val();
														var str = "";
														if (dateTo) {
															dateTo = new Date(dateTo.split('.').reverse().join('-').substr(1));
															if (dateFrom) {
																dateFrom = new Date(dateFrom.split('.').reverse().join('-').substr(1));
																reportTable.column(2).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];
																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew >= dateFrom && dateNew <= dateTo) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															} else {
																reportTable.column(2).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];
																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew <= dateTo) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															}
															str = str.substring(0, str.length - 1);
															if (str == "") {
																str = "Nema rezultata!";
															}
															reportTable.column(2).search(str, true, false);
														} else {
															reportTable.column(2).search('');
														}
														reportTable.draw();
														getReport(reportTable);
													});
												});
											</script>

											<!-- Reports table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>ID</th>
														<th>Komercijalista</th>
														<th>Početno vrijeme</th>
														<th>Početna kilometraža</th>
														<th>Završno vrijeme</th>
														<th>Završna kilometraža</th>
														<th>Razlika</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$query = $db->prepare("
														SELECT mileage_id, mileage_employee_id, mileage_start_time, mileage_end_time, mileage_amount_start, mileage_amount_end
                						FROM idk_mileage");

													$query->execute();

													while ($row = $query->fetch()) {

														$mileage_id = $row['mileage_id'];
														$mileage_employee_id = $row['mileage_employee_id'];
														$mileage_amount_start = $row['mileage_amount_start'];
														$mileage_amount_end = $row['mileage_amount_end'];
														$mileage_start_time = $row['mileage_start_time'];
														$mileage_end_time = $row['mileage_end_time'];

													?>
														<tr>
															<td>
																<?php echo $mileage_id; ?>
															</td>
															<td>
																<?php
																if (isset($mileage_employee_id)) {
																	$query_employee = $db->prepare("
																		SELECT employee_first_name, employee_last_name
																		FROM idk_employee
																		WHERE employee_id = :employee_id");

																	$query_employee->execute(array(
																		':employee_id' => $mileage_employee_id
																	));

																	$row_employee = $query_employee->fetch();

																	echo '<a href="' . getSiteUrlr() . 'idkadmin/employees?page=open&id=' . $mileage_employee_id . '">' . $row_employee['employee_first_name'] . ' ' . $row_employee['employee_last_name'] . '</a>';
																}
																?>
															</td>
															<td data-sort="<?php echo $mileage_start_time; ?>">
																<?php if (isset($mileage_start_time)) {
																	echo date('d.m.Y. H:i', strtotime($mileage_start_time));
																} ?>
															</td>
															<td>
																<?php if (isset($mileage_amount_start)) {
																	echo $mileage_amount_start . ' km';
																} ?>
															</td>
															<td data-sort="<?php echo $mileage_end_time; ?>">
																<?php if (isset($mileage_end_time)) {
																	echo date('d.m.Y. H:i', strtotime($mileage_end_time));
																} ?>
															</td>
															<td>
																<?php if (isset($mileage_amount_end)) {
																	echo $mileage_amount_end . ' km';
																} ?>
															</td>
															<td>
																<?php if (isset($mileage_amount_end) and isset($mileage_amount_start)) {
																	echo ($mileage_amount_end - $mileage_amount_start) . ' km';
																} ?>
															</td>
														</tr>
													<?php } ?>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Print report wrapper -->
						<div id="idk_print_report_wrapper" style="display: none;"></div>

						<script type="text/javascript">
							$(document).ready(function() {
								$('#idk_print_report_btn').click(function() {
									var report_employee = $('#report_employee').val();
									var report_date_from = $('#report_date_from').val();
									var report_date_to = $('#report_date_to').val();
									var type = 'print';

									$('#idk_print_report_wrapper').html('');
									$.ajax({
										url: 'getMileageReports.php',
										method: 'post',
										data: {
											report_employee,
											report_date_from,
											report_date_to,
											type
										},
										dataType: 'text',
										success: function(data) {
											$('#idk_print_report_wrapper').html(data);
											$('.idk_display_none_for_print').css('display', 'none');
											$('#content').css('background-color', '#fff');
											$('#content').css('margin', '0');
											$('#content').css('padding', '0');
											$('#idk_print_report_wrapper').css('display', 'block');
											$('#idk_print_total_mileage').html($('#report_mileage_total_amount').val());

											function windowPrint() {
												window.print();
												$('#idk_print_report_wrapper').css('display', 'none');
												$('#content').css('background-color', '#eee');
												$('#content').css('margin-left', '220px');
												$('#content').css('padding-top', '65px');
												$('.idk_display_none_for_print').css('display', 'block');
											}
											setTimeout(windowPrint, 10);
										}
									});
								});
							});
						</script>

					<?php
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
					 * 					STANJE ASORTIMANA
					 * *********************************************************/
				case "assortment":

					if ($getEmployeeStatus == 1) {

						//Mark as read
						if (isset($_GET['nid'])) {
							$notification_id = $_GET['nid'];

							$query_update = $db->prepare("
									UPDATE idk_notifications
									SET	notification_status = :notification_status
									WHERE notification_id = :notification_id AND notification_datetime <= NOW()");

							$query_update->execute(array(
								':notification_status' => 2,
								':notification_id' => $notification_id
							));
						}

					?>

						<div class="row idk_display_none_for_print">
							<div class="col-xs-8">
								<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Stanje asortimana</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/reports?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row idk_display_none_for_print">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-xs-12">
											<!-- Success and error handling -->
											<?php
											if (isset($_GET['mess'])) {
												$mess = $_GET['mess'];
											} else {
												$mess = 0;
											}

											if ($mess == 2) {
												echo '<div class="alert material-alert material-alert_danger">Greška: Izvještaj o stanju asortimana nije pronađen.</div>';
											}
											?>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6 idk_setting_form_wrapper">
											<h5>Odaberi komercijalistu i period</h5>

											<!-- <form id="idk_form" action="#" method="post" class="form-horizontal" role="form"> -->
											<div class="form-horizontal">

												<div class="form-group">
													<label for="report_employee" class="col-sm-3 control-label">Komercijalista:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_employee" name="report_employee" data-live-search="true">
															<option value=""></option>
															<?php
															$select_query = $db->prepare("
																SELECT employee_id, employee_first_name, employee_last_name
																FROM idk_employee
																WHERE employee_active = :employee_active AND (employee_status = :employee_status OR employee_status = :employee_status_admin)
																ORDER BY employee_last_name, employee_first_name");

															$select_query->execute(array(
																':employee_active' => 1,
																':employee_status' => 2,
																':employee_status_admin' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . '|' . $select_row['employee_id'] . "' data-tokens='" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "'>" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_client" class="col-sm-3 control-label">Klijent:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_client" name="report_client" data-live-search="true">
															<option value=""></option>
															<?php
															$select_query = $db->prepare("
																SELECT client_id, client_name
																FROM idk_client
																WHERE client_active = :client_active
																ORDER BY client_name");

															$select_query->execute(array(
																':client_active' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['client_name'] . "' data-tokens='" . $select_row['client_name'] . "'>" . $select_row['client_name'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_date_from" class="col-sm-3 control-label">Datum od:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="report_date_from" id="report_date_from" placeholder="Datum od">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$("#report_date_from").flatpickr({
															dateFormat: "d.m.Y.",
															maxDate: "today",
															"locale": "bs"
														});
													</script>
												</div>

												<div class="form-group">
													<label for="report_date_to" class="col-sm-3 control-label">Datum do:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="report_date_to" id="report_date_to" placeholder="Datum do">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$("#report_date_to").flatpickr({
															dateFormat: "d.m.Y.",
															maxDate: "today",
															"locale": "bs"
														});
													</script>
												</div>
											</div>
											<!-- </form> -->

										</div>
									</div>

									<div class="row idk_margin_top20">
										<div class="col-xs-12">

											<script type="text/javascript">
												$(document).ready(function() {

													var reportTable = $('#idk_table').DataTable({

														"destroy": true,

														responsive: true,

														"order": [
															[0, "desc"]
														],

														"bAutoWidth": false,

														"aoColumns": [{
																"width": "10%"
															},
															{
																"width": "25%"
															},
															{
																"width": "35%"
															},
															{
																"width": "30%"
															}

														]
													});

													$("#report_employee").change(function() {
														var employeeName = $(this).val().split('|')[0];
														reportTable.column(2).search(employeeName);
														reportTable.draw();
													});

													$("#report_client").change(function() {
														var clientName = $(this).val();
														reportTable.column(1).search(clientName);
														reportTable.draw();
													});

													$("#report_date_from").change(function() {
														var dateFrom = $(this).val();
														var dateTo = $("#report_date_to").val();
														var str = "";
														if (dateFrom) {
															dateFrom = new Date(dateFrom.split('.').reverse().join('-').substr(1));
															if (dateTo) {
																dateTo = new Date(dateTo.split('.').reverse().join('-').substr(1));
																reportTable.column(3).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];

																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew >= dateFrom && dateNew <= dateTo) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															} else {
																reportTable.column(3).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];

																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew >= dateFrom) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															}
															str = str.substring(0, str.length - 1);
															if (str == "") {
																str = "Nema rezultata!";
															}
															reportTable.column(3).search(str, true, false);
														} else {
															reportTable.column(3).search('');
														}
														reportTable.draw();
													});

													$("#report_date_to").change(function() {
														var dateTo = $(this).val();
														var dateFrom = $("#report_date_from").val();
														var str = "";
														if (dateTo) {
															dateTo = new Date(dateTo.split('.').reverse().join('-').substr(1));
															if (dateFrom) {
																dateFrom = new Date(dateFrom.split('.').reverse().join('-').substr(1));
																reportTable.column(3).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];
																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew >= dateFrom && dateNew <= dateTo) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															} else {
																reportTable.column(3).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];
																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew <= dateTo) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															}
															str = str.substring(0, str.length - 1);
															if (str == "") {
																str = "Nema rezultata!";
															}
															reportTable.column(3).search(str, true, false);
														} else {
															reportTable.column(3).search('');
														}
														reportTable.draw();
													});
												});
											</script>

											<!-- Reports table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>ID</th>
														<th>Klijent</th>
														<th>Komercijalista</th>
														<th>Datum</th>
													</tr>
												</thead>

												<tbody>
													<!-- Get data for assortment -->
													<?php
													$query = $db->prepare("
														SELECT *
														FROM idk_assortment_report");

													$query->execute();

													while ($assortment = $query->fetch()) {

														$ar_id = $assortment['ar_id'];
														$ar_employee_id = $assortment['ar_employee_id'];
														$ar_client_id = $assortment['ar_client_id'];
														$ar_datetime = $assortment['ar_datetime'];

														if (isset($ar_datetime)) {
															$ar_datetime_new_format = date('d.m.Y.', strtotime($ar_datetime));
														}

													?>
														<tr>
															<td>
																<?php echo $ar_id; ?>
															</td>
															<td>
																<?php
																$select_query = $db->prepare("
																	SELECT client_name
																	FROM idk_client
																	WHERE client_id = :client_id");

																$select_query->execute(array(
																	':client_id' => $ar_client_id
																));

																$num_of_rows_client = $select_query->rowCount();

																if ($num_of_rows_client != 0) {

																	$select_row = $select_query->fetch();
																	$client_name = $select_row['client_name'];

																	echo "<a href = '" . getSiteUrlr() . "idkadmin/assortment?page=open_report&id=" . $ar_id . "'>" . $client_name . '</a>';
																}
																?>
															</td>
															<td>
																<?php
																$select_query = $db->prepare("
																	SELECT employee_first_name, employee_last_name
																	FROM idk_employee
																	WHERE employee_id = :employee_id");

																$select_query->execute(array(
																	':employee_id' => $ar_employee_id
																));

																$num_of_rows = $select_query->rowCount();

																if ($num_of_rows != 0) {

																	$select_row = $select_query->fetch();
																	$employee_first_name = $select_row['employee_first_name'];
																	$employee_last_name = $select_row['employee_last_name'];

																	echo $employee_first_name .  ' ' . $employee_last_name;
																}
																?>
															</td>
															<td data-sort="<?php echo $ar_datetime; ?>">
																<?php echo $ar_datetime_new_format; ?>
															</td>
														</tr>
													<?php } ?>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

					<?php
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
					 * 					RUTE
					 * *********************************************************/
				case "routes":

					if ($getEmployeeStatus == 1) {

						//Mark as read
						if (isset($_GET['nid'])) {
							$notification_id = $_GET['nid'];

							$query_update = $db->prepare("
								UPDATE idk_notifications
								SET	notification_status = :notification_status
								WHERE notification_id = :notification_id AND notification_datetime <= NOW()");

							$query_update->execute(array(
								':notification_status' => 2,
								':notification_id' => $notification_id
							));
						}

					?>

						<div class="row idk_display_none_for_print">
							<div class="col-xs-8">
								<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Rute</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/reports?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row idk_display_none_for_print">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-xs-12">
											<!-- Success and error handling -->
											<?php
											if (isset($_GET['mess'])) {
												$mess = $_GET['mess'];
											} else {
												$mess = 0;
											}

											if ($mess == 2) {
												echo '<div class="alert material-alert material-alert_danger">Greška: Izvještaj o ruti nije pronađen.</div>';
											}
											?>
										</div>
									</div>
									<div class="row">
										<div class="col-sm-6 idk_setting_form_wrapper">
											<h5>Odaberi komercijalistu i period</h5>

											<!-- <form id="idk_form" action="#" method="post" class="form-horizontal" role="form"> -->
											<div class="form-horizontal">

												<div class="form-group">
													<label for="report_employee" class="col-sm-3 control-label">Komercijalista:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_employee" name="report_employee" data-live-search="true">
															<option value=""></option>
															<?php
															$select_query = $db->prepare("
																SELECT employee_id, employee_first_name, employee_last_name
																FROM idk_employee
																WHERE employee_active = :employee_active AND (employee_status = :employee_status OR employee_status = :employee_status_admin)
																ORDER BY employee_last_name, employee_first_name");

															$select_query->execute(array(
																':employee_active' => 1,
																':employee_status' => 2,
																':employee_status_admin' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . '|' . $select_row['employee_id'] . "' data-tokens='" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "'>" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_day" class="col-sm-3 control-label">Dan:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="report_day" name="report_day" data-live-search="true">
															<option value=""></option>
															<option value="Ponedjeljak" data-tokens="Ponedjeljak">Ponedjeljak</option>
															<option value="Utorak" data-tokens="Utorak">Utorak</option>
															<option value="Srijeda" data-tokens="Srijeda">Srijeda</option>
															<option value="Četvrtak" data-tokens="Četvrtak">Četvrtak</option>
															<option value="Petak" data-tokens="Petak">Petak</option>
															<option value="Subota" data-tokens="Subota">Subota</option>
															<option value="Nedjelja" data-tokens="Nedjelja">Nedjelja</option>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="report_date_from" class="col-sm-3 control-label">Datum od:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="report_date_from" id="report_date_from" placeholder="Datum od">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$("#report_date_from").flatpickr({
															dateFormat: "d.m.Y.",
															maxDate: "today",
															"locale": "bs"
														});
													</script>
												</div>

												<div class="form-group">
													<label for="report_date_to" class="col-sm-3 control-label">Datum do:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="report_date_to" id="report_date_to" placeholder="Datum do">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$("#report_date_to").flatpickr({
															dateFormat: "d.m.Y.",
															maxDate: "today",
															"locale": "bs"
														});
													</script>
												</div>
											</div>
											<!-- </form> -->

										</div>
									</div>

									<div class="row idk_margin_top20">
										<div class="col-xs-12">

											<script type="text/javascript">
												$(document).ready(function() {

													var reportTable = $('#idk_table').DataTable({

														"destroy": true,

														responsive: true,

														"order": [
															[3, "desc"]
														],

														"bAutoWidth": false,

														"aoColumns": [{
																"width": "10%"
															},
															{
																"width": "25%"
															},
															{
																"width": "35%"
															},
															{
																"width": "30%"
															}

														]
													});

													$("#report_employee").change(function() {
														var employeeName = $(this).val().split('|')[0];
														reportTable.column(2).search(employeeName);
														reportTable.draw();
													});

													$("#report_day").change(function() {
														var day = $(this).val();
														reportTable.column(1).search(day);
														reportTable.draw();
													});

													$("#report_date_from").change(function() {
														var dateFrom = $(this).val();
														var dateTo = $("#report_date_to").val();
														var str = "";
														if (dateFrom) {
															dateFrom = new Date(dateFrom.split('.').reverse().join('-').substr(1));
															if (dateTo) {
																dateTo = new Date(dateTo.split('.').reverse().join('-').substr(1));
																reportTable.column(3).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];

																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew >= dateFrom && dateNew <= dateTo) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															} else {
																reportTable.column(3).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];

																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew >= dateFrom) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															}
															str = str.substring(0, str.length - 1);
															if (str == "") {
																str = "Nema rezultata!";
															}
															reportTable.column(3).search(str, true, false);
														} else {
															reportTable.column(3).search('');
														}
														reportTable.draw();
													});

													$("#report_date_to").change(function() {
														var dateTo = $(this).val();
														var dateFrom = $("#report_date_from").val();
														var str = "";
														if (dateTo) {
															dateTo = new Date(dateTo.split('.').reverse().join('-').substr(1));
															if (dateFrom) {
																dateFrom = new Date(dateFrom.split('.').reverse().join('-').substr(1));
																reportTable.column(3).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];
																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew >= dateFrom && dateNew <= dateTo) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															} else {
																reportTable.column(3).data().each(function(value, index) {
																	var date = value;
																	var dateNewFormat = value.split(' ')[0];
																	dateNewFormat = dateNewFormat.split('.').reverse().join('-').substr(1);
																	var dateNew = new Date(dateNewFormat);
																	if (dateNew <= dateTo) {
																		str = str + "" + dateNewFormat.split('-').reverse().join('.') + "|";
																	}
																});
															}
															str = str.substring(0, str.length - 1);
															if (str == "") {
																str = "Nema rezultata!";
															}
															reportTable.column(3).search(str, true, false);
														} else {
															reportTable.column(3).search('');
														}
														reportTable.draw();
													});
												});
											</script>

											<!-- Reports table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>ID rute</th>
														<th>Dan</th>
														<th>Komercijalista</th>
														<th>Datum</th>
													</tr>
												</thead>

												<tbody>
													<!-- Get data for route -->
													<?php
													$query = $db->prepare("
														SELECT t1.*, t2.route_employee_id, t2.route_day
														FROM idk_route_report t1
														INNER JOIN idk_route t2
														ON t1.rr_route_id = t2.route_id
														GROUP BY t1.rr_route_id
														ORDER BY t1.rr_datetime DESC");

													$query->execute();

													while ($route = $query->fetch()) {

														$rr_id = $route['rr_id'];
														$route_id = $route['rr_route_id'];
														$route_day = $route['route_day'];
														$route_employee_id = $route['route_employee_id'];
														$rr_datetime = $route['rr_datetime'];

														if (isset($rr_datetime)) {
															$rr_datetime_date = date('Y-m-d', strtotime($rr_datetime));
															$rr_datetime_new_format = date('d.m.Y.', strtotime($rr_datetime));
														}

														if ($route_day == 1) {
															$route_day = "Ponedjeljak";
														} elseif ($route_day == 2) {
															$route_day = "Utorak";
														} elseif ($route_day == 3) {
															$route_day = "Srijeda";
														} elseif ($route_day == 4) {
															$route_day = "Četvrtak";
														} elseif ($route_day == 5) {
															$route_day = "Petak";
														} elseif ($route_day == 6) {
															$route_day = "Subota";
														} elseif ($route_day == 7) {
															$route_day = "Nedjelja";
														} else {
															$route_day = NULL;
														}

													?>
														<tr>
															<td>
																<?php echo $route_id; ?>
															</td>
															<td>
																<?php echo "<a href = '" . getSiteUrlr() . "idkadmin/routes?page=open_report&id=" . $route_id . "&date=" . $rr_datetime_date . "'>" . $route_day . '</a>'; ?>
															</td>
															<td>
																<?php
																$select_query = $db->prepare("
																	SELECT employee_first_name, employee_last_name
																	FROM idk_employee
																	WHERE employee_id = :employee_id");

																$select_query->execute(array(
																	':employee_id' => $route_employee_id
																));

																$num_of_rows = $select_query->rowCount();

																if ($num_of_rows != 0) {

																	$select_row = $select_query->fetch();
																	$employee_first_name = $select_row['employee_first_name'];
																	$employee_last_name = $select_row['employee_last_name'];

																	echo $employee_first_name .  ' ' . $employee_last_name;
																}
																?>
															</td>
															<td data-sort="<?php echo $rr_datetime; ?>">
																<?php echo $rr_datetime_new_format; ?>
															</td>
														</tr>
													<?php } ?>

												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
						</div>

			<?php
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
			}
			?>



			<!--/************************************************************
 * 							FOOTER
 * *********************************************************/-->
			<footer class="idk_display_none_for_print"><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>

</html>