<?php
include("includes/functions.php");
include("includes/common_for_routes.php");

$getTempOrder = getTempOrder();
$getUnreadMessages = getUnreadMessages();

if (isset($_GET['date'])) {
	$date = date('Y-m-d', strtotime($_GET['date']));
} else {
	$date = date('Y-m-d');
}

?>

<!DOCTYPE html>
<html lang="bs">

<head>

	<?php include('includes/head.php'); ?>

</head>

<body class="idk_body_background">

	<!-- Overlay menu -->
	<?php include('includes/menu_overlay.php');	?>

	<!-- Header -->
	<header class="header">

		<!-- Top bar -->
		<?php include('includes/top_bar.php'); ?>

		<div class="container-fluid">
			<div class="row">
				<div class="col-12">
					<div class="container idk_page_title_container">
						<div class="row align-items-center">
							<?php
							if (isset($_GET['mess'])) {
								$mess = $_GET['mess'];
							} else {
								$mess = 0;
							}

							if ($mess == 1) {
								echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success">Uspješno ste ažurirali izvještaj o ruti.</div></div>';
							} elseif ($mess == 2) {
								echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_danger">Greška: Forma nije pravilno popunjena!</div></div>';
							}
							?>

							<div class="col-12">
								<div class="alert material-alert material-alert_success d-none mb-5" id="idk_add_client_to_route_success_alert"></div>
							</div>

							<div class="col-12">
								<h1 class="idk_page_title">
									Ruta: <?php switch (date('w', strtotime($date))) {
													case 1:
														echo 'Ponedjeljak';
														break;

													case 2:
														echo 'Utorak';
														break;

													case 3:
														echo 'Srijeda';
														break;

													case 4:
														echo 'Četvrtak';
														break;

													case 5:
														echo 'Petak';
														break;

													case 6:
														echo 'Subota';
														break;

													default:
														echo 'Nedjelja';
														break;
												} ?> - <?php echo date('d.m.Y.', strtotime($date)); ?>
								</h1>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</header>

	<!-- Main -->
	<main>

		<?php if (isset($logged_employee_id) and $logged_employee_id != 0) { ?>

			<!-- List clients on route section -->
			<section class="idk_list_items_section">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<div class="container">

								<?php
								$current_week_of_month = weekOfMonth(time());
								switch ($current_week_of_month) {
									case 3:
										$current_week_of_month = 1;
										break;

									case 4:
										$current_week_of_month = 2;
										break;

									case 5:
										$current_week_of_month = 1;
										break;

									default:
										$current_week_of_month = $current_week_of_month;
										break;
								}

								$route_clients_ids_array = array();

								//Look for routes that have week = 0 first
								$route_clients_query = $db->prepare("
									SELECT t1.rc_route_id, t1.rc_client_id, t3.client_name, t3.client_image
									FROM idk_route_client t1
									INNER JOIN idk_route t2
									ON t1.rc_route_id = t2.route_id
									INNER JOIN idk_client t3
									ON t1.rc_client_id = t3.client_id
									WHERE t2.route_day = :route_day AND t2.route_week = 0 AND t2.route_employee_id = :route_employee_id AND t2.route_active = :route_active
									ORDER BY t1.rc_client_position");

								$route_clients_query->execute(array(
									':route_day' => date('w', strtotime($date)),
									':route_employee_id' => $logged_employee_id,
									':route_active' => 1
								));

								$num_of_rows_route_client = $route_clients_query->rowCount();

								//If not found, look for routes with week = current week of the month
								if ($num_of_rows_route_client == 0) {
									$route_clients_query = $db->prepare("
										SELECT t1.rc_route_id, t1.rc_client_id, t3.client_name, t3.client_image
										FROM idk_route_client t1
										INNER JOIN idk_route t2
										ON t1.rc_route_id = t2.route_id
										INNER JOIN idk_client t3
										ON t1.rc_client_id = t3.client_id
										WHERE t2.route_day = :route_day AND t2.route_week = :route_week AND t2.route_employee_id = :route_employee_id AND t2.route_active = :route_active
										ORDER BY t1.rc_client_position");

									$route_clients_query->execute(array(
										':route_day' => date('w', strtotime($date)),
										':route_week' => $current_week_of_month,
										':route_employee_id' => $logged_employee_id,
										':route_active' => 1
									));

									$num_of_rows_route_client = $route_clients_query->rowCount();
								}

								if ($num_of_rows_route_client != 0) { ?>

									<form action="routes?" method="GET">
										<input class="form-control text-center mb-4 bg-white" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="date" id="date" placeholder="Datum rute" value="<?php echo date('d.m.Y.', strtotime($date)); ?>">
									</form>

									<button type="button" class="btn idk_btn btn-block m-0 mb-5" data-toggle="modal" data-target="#addClientToRouteModal">DODAJ POSJETU KLIJENTU MIMO RUTE</button>

									<!-- Modal add new client to route -->
									<div class="modal fade" id="addClientToRouteModal" tabindex="-1" role="dialog" aria-labelledby="addClientToRouteModalLabel" aria-hidden="true">
										<div class="modal-dialog modal-dialog-centered" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="addClientToRouteModalLabel">Dodaj posjetu klijentu mimo rute</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">
													<div class="mt-4 idk_form_div">
														<input type="hidden" name="client_id" value="">
														<h5>Jeste li sigurni da želite dodati posjetu novom klijentu?</h5>

														<div class="mb-5 idk_add_client_to_route_danger_alert_wrapper">
															<div class="alert material-alert material-alert_danger d-none" id="idk_add_client_to_route_danger_alert"></div>
														</div>

														<div class="idk_select_client_form mt-4" id="idk_select_client_on_route_form">
															<div class="form-group">
																<select class="selectpicker" name="client_id" id="selectClient" data-live-search="true" required>
																	<option value="" selected>Izaberi klijenta ...</option>
																	<!-- Get clients from db -->
																	<?php
																	$client_query = $db->prepare("
																		SELECT client_id, client_name, client_code
																		FROM idk_client
																		WHERE client_active = 1
																		ORDER BY client_name");

																	$client_query->execute();

																	while ($client = $client_query->fetch()) {

																		$client_id = $client['client_id'];
																		$client_name = $client['client_name'];
																		$client_code = $client['client_code'];
																	?>
																		<option value="<?php echo $client_id; ?>" data-tokens="<?php echo $client_name . ' ' . $client_code; ?>">
																			<?php echo $client_name; ?>
																		</option>
																	<?php } ?>
																</select>
															</div>

															<p class="idk_text_red" id="idk_add_client_to_route_client_error"></p>

															<div class="form-group idk_textarea_form_group mt-4">
																<label class="sr-only" for="add_client_to_route_reason">Razlog*</label>
																<div class="input-group mb-2">
																	<div class="input-group-prepend">
																		<div class="input-group-text"><span class="lnr lnr-pencil"></span></div>
																	</div>
																	<input type="text" class="form-control" id="add_client_to_route_reason" name="add_client_to_route_reason" rows="3" placeholder="Razlog*" required>
																</div>
															</div>
														</div>

														<p class="idk_text_red" id="idk_add_client_to_route_reason_error"></p>
														<button type="button" class="btn idk_btn btn-block" id="idk_add_client_to_route" data="1">DODAJ</button>
													</div>
												</div>
											</div>
										</div>
									</div>

									<?php
									while ($route_clients_row = $route_clients_query->fetch()) {

										$rc_route_id = $route_clients_row['rc_route_id'];
										$rc_client_id = $route_clients_row['rc_client_id'];
										$client_name = $route_clients_row['client_name'];
										$client_image = $route_clients_row['client_image'];
										$rr_datetime = date('Y-m-d H:i:s');
										$rr_status = 0;

										array_push($route_clients_ids_array, $rc_client_id);

										$route_report_status_query = $db->prepare("
											SELECT rr_status, rr_comment, rr_datetime
											FROM idk_route_report
											WHERE rr_route_id = :rr_route_id AND rr_client_id = :rr_client_id AND (rr_datetime BETWEEN :rr_datetime_start AND :rr_datetime_end)");

										$route_report_status_query->execute(array(
											':rr_route_id' => $rc_route_id,
											':rr_client_id' => $rc_client_id,
											':rr_datetime_start' => date('Y-m-d 00:00:00', strtotime($rr_datetime)),
											':rr_datetime_end' => $rr_datetime
										));

										$num_of_rows = $route_report_status_query->rowCount();

										if ($num_of_rows != 0) {

											$route_report_status_row = $route_report_status_query->fetch();
											$rr_status  = $route_report_status_row['rr_status'];
											$rr_comment = $route_report_status_row['rr_comment'];
											$rr_datetime = $route_report_status_row['rr_datetime'];
										}
									?>

										<div class="card mb-3 idk_order_card <?php if ($rr_status == 1) {
																														echo 'idk_assortment_in_stock_card';
																													} elseif ($rr_status == 2) {
																														echo 'idk_assortment_not_in_stock_card';
																													} ?>" id="idk_order_card_<?php echo $rc_client_id; ?>">
											<div class="card-body">
												<div class="row align-items-center">
													<div class="col-12">
													</div>
													<div class="col-3 p-0 text-center">
														<img class="idk_order_client_image" src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>" alt="<?php echo $client_name; ?> slika">
													</div>
													<div class="col-9 col-md-6">
														<h5 class="card-title idk_order_client_name <?php if ($rr_status == 1) {
																																					echo 'idk_text_brand idk_text_bold';
																																				} elseif ($rr_status == 2) {
																																					echo 'idk_text_red idk_text_bold';
																																				} ?>">
															<?php echo $client_name; ?>
														</h5>
														<p class="idk_assortment_text <?php if ($rr_status == 1) {
																														echo 'idk_text_brand';
																													} elseif ($rr_status == 2) {
																														echo 'idk_text_red';
																													} ?>">
															<?php 
															if ($rr_status == 1) {
																echo 'Klijent posjećen.<br><small>' . date('d.m.Y. H:i', strtotime($rr_datetime)) . '</small>';
															} elseif ($rr_status == 2) {
																echo 'Klijent nije posjećen.<br><small>Razlog: ' . $rr_comment . '</small><br><small>' . date('d.m.Y. H:i', strtotime($rr_datetime)) . '</small>';
															} ?>
														</p>
													</div>
													<div class="col-12 col-md-3 mt-3 mt-md-0">
														<div class="row">
															<div class="col-12 text-right">
																<button type="button" class="btn idk_set_assortment_state_btn idk_confirm_client_visit idk_text_brand <?php if ($rr_status == 1) {
																																																																				echo 'd-none';
																																																																			} ?>" id="idk_confirm_client_visit_<?php echo $rc_client_id; ?>" data="1" title="Potvrdi posjetu klijentu">
																	<span class="lnr lnr-checkmark-circle"></span>
																</button>
																<button type="button" class="btn idk_set_assortment_state_btn idk_text_red <?php if ($rr_status == 2) {
																																																							echo 'd-none';
																																																						} ?>" title="Otkaži posjetu klijentu" data-toggle="modal" data-target="#routeReportCancelClientVisitModal_<?php echo $rc_client_id; ?>">
																	<span class="lnr lnr-cross-circle">
																</button>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>

										<!-- Modal cancel -->
										<div class="modal fade" id="routeReportCancelClientVisitModal_<?php echo $rc_client_id; ?>" tabindex="-1" role="dialog" aria-labelledby="routeReportCancelClientVisitModalLabel" aria-hidden="true">
											<div class="modal-dialog modal-dialog-centered" role="document">
												<div class="modal-content">
													<div class="modal-header">
														<h5 class="modal-title" id="routeReportCancelClientVisitModalLabel">Otkaži posjetu klijentu</h5>
														<button type="button" class="close" data-dismiss="modal" aria-label="Close">
															<span aria-hidden="true">&times;</span>
														</button>
													</div>
													<div class="modal-body">
														<div class="mt-4 idk_form_div">
															<input type="hidden" name="client_id" value="<?php echo $rc_client_id; ?>">
															<h5>Jeste li sigurni da želite otkazati posjetu klijentu <?php echo $client_name; ?>?</h5>
															<div class="form-group idk_textarea_form_group mt-4">
																<label class="sr-only" for="cancel_client_visit_reason">Razlog*</label>
																<div class="input-group mb-2">
																	<div class="input-group-prepend">
																		<div class="input-group-text"><span class="lnr lnr-pencil"></span></div>
																	</div>
																	<input type="text" class="form-control" id="cancel_client_visit_reason_<?php echo $rc_client_id; ?>" name="cancel_client_visit_reason" rows="3" placeholder="Razlog*" required>
																</div>
															</div>
															<p class="idk_text_red idk_cancel_client_error" id="idk_cancel_client_error_<?php echo $rc_client_id; ?>"></p>
															<button type="button" class="btn idk_btn btn-block idk_cancel_client_visit" id="idk_cancel_client_visit_<?php echo $rc_client_id; ?>" data="2">OTKAŽI</button>
														</div>
													</div>
												</div>
											</div>
										</div>

									<?php } ?>

									<?php
									if (isset($rc_route_id)) {
										//Check if there are clients added to the route by a commercialist additionally
										$route_clients_additional_query = $db->prepare("
											SELECT t1.rr_route_id, t1.rr_client_id, t1.rr_datetime, t3.client_name, t3.client_image
											FROM idk_route_report t1
											INNER JOIN idk_route t2
											ON t1.rr_route_id = t2.route_id
											INNER JOIN idk_client t3
											ON t1.rr_client_id = t3.client_id
											WHERE t1.rr_route_id = :rr_route_id AND (t1.rr_datetime BETWEEN :rr_datetime_start AND :rr_datetime_end)");

										$route_clients_additional_query->execute(array(
											':rr_route_id' => $rc_route_id,
											':rr_datetime_start' => date('Y-m-d 00:00:00'),
											':rr_datetime_end' => date('Y-m-d 23:59:59')
										));

										$number_of_rows_route_clients_additional = $route_clients_additional_query->rowCount();

										if ($number_of_rows_route_clients_additional != 0 and $number_of_rows_route_clients_additional > count($route_clients_ids_array)) { ?>

											<h1 class="idk_page_title my-5"><small>Posjećeni klijenti mimo rute</small></h2>

												<div id="idk_route_clients_additional_div">
													<?php
													while ($route_clients_additional_row = $route_clients_additional_query->fetch()) {

														$rr_route_id = $route_clients_additional_row['rr_route_id'];
														$rr_client_id = $route_clients_additional_row['rr_client_id'];
														$rr_datetime = $route_clients_additional_row['rr_datetime'];
														$client_name = $route_clients_additional_row['client_name'];
														$client_image = $route_clients_additional_row['client_image'];

														if (!in_array($rr_client_id, $route_clients_ids_array)) {
													?>

															<div class="card mb-3 idk_order_card idk_assortment_in_stock_card" id="idk_order_card_<?php echo $rr_client_id; ?>">
																<div class="card-body">
																	<div class="row align-items-center">
																		<div class="col-12">
																		</div>
																		<div class="col-3 p-0 text-center">
																			<img class="idk_order_client_image" src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>" alt="<?php echo $client_name; ?> slika">
																		</div>
																		<div class="col-9 col-md-6">
																			<h5 class="card-title idk_order_client_name idk_text_brand idk_text_bold">
																				<?php echo $client_name; ?>
																			</h5>
																			<p class="idk_assortment_text idk_text_brand">
																				<?php echo 'Klijent posjećen.<br><small>' . date('d.m.Y. H:i', strtotime($rr_datetime)) . '</small>'; ?>
																			</p>
																		</div>
																	</div>
																</div>
															</div>

													<?php }
													} ?>
												</div>

										<?php
										}
									}
										?>

									<?php } else { ?>
										<form action="routes?" method="GET">
										<input class="form-control text-center mb-4 bg-white" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="date" id="date" placeholder="Datum rute" value="<?php echo date('d.m.Y.', strtotime($date)); ?>">
										</form>

										<button type="button" class="btn idk_btn btn-block m-0 mb-5" data-toggle="modal" data-target="#addClientToRouteModal">DODAJ POSJETU KLIJENTU MIMO RUTE</button>

									<!-- Modal add new client to route -->
									<div class="modal fade" id="addClientToRouteModal" tabindex="-1" role="dialog" aria-labelledby="addClientToRouteModalLabel" aria-hidden="true">
										<div class="modal-dialog modal-dialog-centered" role="document">
											<div class="modal-content">
												<div class="modal-header">
													<h5 class="modal-title" id="addClientToRouteModalLabel">Dodaj posjetu klijentu mimo rute</h5>
													<button type="button" class="close" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body">
													<div class="mt-4 idk_form_div">
														<input type="hidden" name="client_id" value="">
														<h5>Jeste li sigurni da želite dodati posjetu novom klijentu?</h5>

														<div class="mb-5 idk_add_client_to_route_danger_alert_wrapper">
															<div class="alert material-alert material-alert_danger d-none" id="idk_add_client_to_route_danger_alert"></div>
														</div>

														<div class="idk_select_client_form mt-4" id="idk_select_client_on_route_form">
															<div class="form-group">
																<select class="selectpicker" name="client_id" id="selectClient" data-live-search="true" required>
																	<option value="" selected>Izaberi klijenta ...</option>
																	<!-- Get clients from db -->
																	<?php
																	$client_query = $db->prepare("
																		SELECT client_id, client_name, client_code
																		FROM idk_client
																		WHERE client_active = 1
																		ORDER BY client_name");

																	$client_query->execute();

																	while ($client = $client_query->fetch()) {

																		$client_id = $client['client_id'];
																		$client_name = $client['client_name'];
																		$client_code = $client['client_code'];
																	?>
																		<option value="<?php echo $client_id; ?>" data-tokens="<?php echo $client_name . ' ' . $client_code; ?>">
																			<?php echo $client_name; ?>
																		</option>
																	<?php } ?>
																</select>
															</div>

															<p class="idk_text_red" id="idk_add_client_to_route_client_error"></p>

															<div class="form-group idk_textarea_form_group mt-4">
																<label class="sr-only" for="add_client_to_route_reason">Razlog*</label>
																<div class="input-group mb-2">
																	<div class="input-group-prepend">
																		<div class="input-group-text"><span class="lnr lnr-pencil"></span></div>
																	</div>
																	<input type="text" class="form-control" id="add_client_to_route_reason" name="add_client_to_route_reason" rows="3" placeholder="Razlog*" required>
																</div>
															</div>
														</div>

														<p class="idk_text_red" id="idk_add_client_to_route_reason_error"></p>
														<button type="button" class="btn idk_btn btn-block" id="idk_add_client_to_route" data="1">DODAJ</button>
													</div>
												</div>
											</div>
										</div>
									</div>

										<h2>Nemate kreirane rute za današnji dan</h2>
									<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</section> <!-- End list clients on route section -->

			<!-- Modal Geolocation Error -->
			<div class="modal fade" id="geolocationErrorModal" tabindex="-1" role="dialog" aria-labelledby="geolocationErrorModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="geolocationErrorModalLabel">Greška geolokacije</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<form class="mt-4">
								<h5 class="mb-4">Nije moguće spremiti izvještaj o ruti.</h5>
								<h5>Razlog:</h5>
								<h5 id="idk_geolocation_error_text"></h5>
								<button type="button" class="btn idk_btn btn-block" data-dismiss="modal" aria-label="Close">ZATVORI</button>
							</form>
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
		} ?>

	</main> <!-- End main -->

	<!-- Foot bar -->
	<?php if (isset($_COOKIE['idk_session_front_employee'])) {
		include('includes/foot_bar.php');
	} ?>

	<!-- foot.php -->
	<?php include('includes/foot.php'); ?>

	<script>
		'use strict'

		$(document).ready(function() {
			// Confirm visit to client
			$('.idk_confirm_client_visit').on('click', function(e) {
				e.preventDefault();
				let self = $(this);
				let status = $(this).attr('data');
				let clientId = $(this).attr('id').split('client_visit_')[1];
				let routeId = <?php echo isset($rc_route_id) ? $rc_route_id : null; ?>;

				let options = {
					enableHighAccuracy: true,
					timeout: 5000,
					maximumAge: 0
				};

				function success(pos) {
					let crd = pos.coords;
					let latitude = crd.latitude;
					let longitude = crd.longitude;

					$.ajax({
						url: '<?php getSiteUrl(); ?>do.php?form=set_route_state',
						method: 'post',
						dataType: 'json',
						data: {
							routeId,
							clientId,
							status,
							latitude,
							longitude
						},
						success: function(data) {
							self.closest('.idk_order_card').find('.idk_set_assortment_state_btn').removeClass('d-none');
							self.addClass('d-none');

							self.closest('.idk_order_card').removeClass('idk_assortment_in_stock_card').removeClass('idk_assortment_not_in_stock_card');
							self.closest('.idk_order_card').addClass(data[2]);

							self.closest('.idk_order_card').find('.idk_assortment_text').removeClass('idk_text_brand').removeClass('idk_text_red');
							self.closest('.idk_order_card').find('.idk_assortment_text').html(data[0]);
							self.closest('.idk_order_card').find('.idk_assortment_text').addClass(data[1]);

							self.closest('.idk_order_card').find('.card-title').removeClass('idk_text_brand').removeClass('idk_text_red');
							self.closest('.idk_order_card').find('.card-title').addClass('idk_text_bold').addClass(data[1]);
						}
					});
				}

				function error(err) {
					switch (err.code) {
						case err.PERMISSION_DENIED:
							$('#idk_geolocation_error_text').html('Zabranili ste pristup Vašoj lokaciji. Molimo Vas da promijenite postavke Vašeg web preglednika i omogućite pristup lokaciji.');
							$('#geolocationErrorModal').modal('show');
							break;
						case err.POSITION_UNAVAILABLE:
							$('#idk_geolocation_error_text').html('Informacija o lokaciji nije dostupna.');
							$('#geolocationErrorModal').modal('show');
							break;
						case err.TIMEOUT:
							$('#idk_geolocation_error_text').html('Došlo je do isteka vremena za preuzimanje Vaše lokacije.');
							$('#geolocationErrorModal').modal('show');
							break;
						case err.UNKNOWN_ERROR:
							$('#idk_geolocation_error_text').html('Došlo je do nepoznate greške.');
							$('#geolocationErrorModal').modal('show');
							break;
						default:
							$('#idk_geolocation_error_text').html('Došlo je do nepoznate greške.');
							$('#geolocationErrorModal').modal('show');
					}
				}

				navigator.geolocation.getCurrentPosition(success, error, options);
			});

			// Cancel visit to client on click of button in modal
			$('.idk_cancel_client_visit').on('click', function(e) {
				e.preventDefault();
				let self = $(this);
				let status = $(this).attr('data');
				let clientId = $(this).attr('id').split('client_visit_')[1];
				let routeId = <?php echo isset($rc_route_id) ? $rc_route_id : null; ?>;
				let comment = $('#cancel_client_visit_reason_' + clientId).val();
				$('.idk_cancel_client_error').html('');

				if (comment && comment.length > 0) {

					let options = {
						enableHighAccuracy: true,
						timeout: 5000,
						maximumAge: 0
					};

					function success(pos) {
						let crd = pos.coords;
						let latitude = crd.latitude;
						let longitude = crd.longitude;

						$.ajax({
							url: '<?php getSiteUrl(); ?>do.php?form=set_route_state',
							method: 'post',
							dataType: 'json',
							data: {
								routeId,
								clientId,
								status,
								comment,
								latitude,
								longitude
							},
							success: function(data) {
								$('#idk_order_card_' + clientId).find('.idk_set_assortment_state_btn').removeClass('d-none');
								$('#idk_order_card_' + clientId).find('.idk_set_assortment_state_btn.idk_text_red').addClass('d-none');

								$('#idk_order_card_' + clientId).removeClass('idk_assortment_in_stock_card').removeClass('idk_assortment_not_in_stock_card');
								$('#idk_order_card_' + clientId).addClass(data[2]);

								$('#idk_order_card_' + clientId).find('.idk_assortment_text').removeClass('idk_text_brand').removeClass('idk_text_red');
								$('#idk_order_card_' + clientId).find('.idk_assortment_text').html(data[0]);
								$('#idk_order_card_' + clientId).find('.idk_assortment_text').addClass(data[1]);

								$('#idk_order_card_' + clientId).find('.card-title').removeClass('idk_text_brand').removeClass('idk_text_red');
								$('#idk_order_card_' + clientId).find('.card-title').addClass('idk_text_bold').addClass(data[1]);
								$('#routeReportCancelClientVisitModal_' + clientId).modal('hide');
							}
						});
					}

					function error(err) {
						switch (err.code) {
							case err.PERMISSION_DENIED:
								$('#idk_geolocation_error_text').html('Zabranili ste pristup Vašoj lokaciji. Molimo Vas da promijenite postavke Vašeg web preglednika i omogućite pristup lokaciji.');
								$('#geolocationErrorModal').modal('show');
								$('#routeReportCancelClientVisitModal_' + clientId).modal('hide');
								break;
							case err.POSITION_UNAVAILABLE:
								$('#idk_geolocation_error_text').html('Informacija o lokaciji nije dostupna.');
								$('#geolocationErrorModal').modal('show');
								$('#routeReportCancelClientVisitModal_' + clientId).modal('hide');
								break;
							case err.TIMEOUT:
								$('#idk_geolocation_error_text').html('Došlo je do isteka vremena za preuzimanje Vaše lokacije.');
								$('#geolocationErrorModal').modal('show');
								$('#routeReportCancelClientVisitModal_' + clientId).modal('hide');
								break;
							case err.UNKNOWN_ERROR:
								$('#idk_geolocation_error_text').html('Došlo je do nepoznate greške.');
								$('#geolocationErrorModal').modal('show');
								$('#routeReportCancelClientVisitModal_' + clientId).modal('hide');
								break;
							default:
								$('#idk_geolocation_error_text').html('Došlo je do nepoznate greške.');
								$('#geolocationErrorModal').modal('show');
								$('#routeReportCancelClientVisitModal_' + clientId).modal('hide');
						}
					}

					navigator.geolocation.getCurrentPosition(success, error, options);

				} else {
					$('#idk_cancel_client_error_' + clientId).html('Morate unijeti razlog otkazivanja posjete klijentu.');
				}
			});

			//Enable canceling visit to client on pressing enter key
			$('input[name="cancel_client_visit_reason"]').on('keyup', function(e) {
				e.preventDefault();
				e.stopPropagation();
				let clientId = $(this).attr('id').split('visit_reason_')[1];
				if (!e) e = window.event;
				let keyCode = e.code || e.key;
				if (keyCode == 'Enter') {
					// Enter pressed
					$('#idk_cancel_client_visit_' + clientId).click();
					return true;
				}
				return false;
			});

			// Add new client to route on click of button in modal
			$('#idk_add_client_to_route').on('click', function(e) {
				e.preventDefault();
				let self = $(this);
				let clientId = $('#selectClient').val();
				let routeId = <?php echo isset($rc_route_id) ? $rc_route_id : null; ?>;
				let comment = $('#add_client_to_route_reason').val();
				let status = $(this).attr('data');
				$('#idk_add_client_to_route_client_error').html('');
				$('#idk_add_client_to_route_reason_error').html('');

				if (clientId && clientId.length > 0 && comment && comment.length > 0) {

					let options = {
						enableHighAccuracy: true,
						timeout: 10000,
						maximumAge: 60000
					};

					function success(pos) {
						let crd = pos.coords;
						let latitude = crd.latitude;
						let longitude = crd.longitude;

						$.ajax({
							url: '<?php getSiteUrl(); ?>do.php?form=add_client_to_route',
							method: 'post',
							dataType: 'json',
							data: {
								routeId,
								clientId,
								status,
								comment,
								latitude,
								longitude
							},
							success: function(data) {
								$('#idk_add_client_to_route_danger_alert').addClass('d-none').html('');
								if (data.length === 1) {
									$('#idk_add_client_to_route_danger_alert').removeClass('d-none').html(data[0]);
								} else {
									$('#idk_add_client_to_route_success_alert').removeClass('d-none').html(data[0]);

									$('#idk_route_clients_additional_div').append(
										'<div class="card mb-3 idk_order_card idk_assortment_in_stock_card" id="idk_order_card_' + clientId + '">' +
										'<div class="card-body">' +
										'<div class="row align-items-center">' +
										'<div class="col-12">' +
										'</div>' +
										'<div class="col-3 p-0 text-center">' +
										'<img class="idk_order_client_image" src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/' + data[2] + '" alt="' + data[1] + ' slika">' +
										'</div>' +
										'<div class="col-9 col-md-6">' +
										'<h5 class="card-title idk_order_client_name idk_text_brand idk_text_bold">' +
										data[1] +
										'</h5>' +
										'<p class="idk_assortment_text idk_text_brand">' +
										'Klijent posjećen.<br><small>' + data[3] + '</small>' +
										'</p>' +
										'</div>' +
										'</div>' +
										'</div>' +
										'</div>');

									$('#selectClient').val('');
									$('#selectClient').selectpicker('refresh');
									$('#add_client_to_route_reason').val('');

									$('#addClientToRouteModal').modal('hide');
								}
							}
						});
					}

					function error(err) {
						switch (err.code) {
							case err.PERMISSION_DENIED:
								$('#idk_geolocation_error_text').html('Zabranili ste pristup Vašoj lokaciji. Molimo Vas da promijenite postavke Vašeg web preglednika i omogućite pristup lokaciji.');
								$('#geolocationErrorModal').modal('show');
								$('#addClientToRouteModal').modal('hide');
								break;
							case err.POSITION_UNAVAILABLE:
								$('#idk_geolocation_error_text').html('Informacija o lokaciji nije dostupna.');
								$('#geolocationErrorModal').modal('show');
								$('#addClientToRouteModal').modal('hide');
								break;
							case err.TIMEOUT:
								$('#idk_geolocation_error_text').html('Došlo je do isteka vremena za preuzimanje Vaše lokacije.');
								$('#geolocationErrorModal').modal('show');
								$('#addClientToRouteModal').modal('hide');
								break;
							case err.UNKNOWN_ERROR:
								$('#idk_geolocation_error_text').html('Došlo je do nepoznate greške.');
								$('#geolocationErrorModal').modal('show');
								$('#addClientToRouteModal').modal('hide');
								break;
							default:
								$('#idk_geolocation_error_text').html('Došlo je do nepoznate greške.');
								$('#geolocationErrorModal').modal('show');
								$('#addClientToRouteModal').modal('hide');
						}
					}

					if (navigator && navigator.geolocation) {
						navigator.geolocation.getCurrentPosition(success, error, options);
					} else {
						$('#idk_geolocation_error_text').html('Geolokacija nije podržana na Vašem uređaju.');
						$('#geolocationErrorModal').modal('show');
						$('#addClientToRouteModal').modal('hide');
					}
				} else {
					if (!clientId || clientId.length <= 0) {
						$('#idk_add_client_to_route_client_error').html('Morate odabrati klijenta.');
					}
					if (!comment || comment.length <= 0) {
						$('#idk_add_client_to_route_reason_error').html('Morate unijeti razlog dodavanja klijenta na rutu.');
					}
				}
			});

			//Enable adding new client to route on pressing enter key
			$('input[name="add_client_to_route_reason"]').on('keyup', function(e) {
				e.preventDefault();
				e.stopPropagation();
				if (!e) e = window.event;
				let keyCode = e.code || e.key;
				if (keyCode == 'Enter') {
					// Enter pressed
					$('#idk_add_client_to_route').click();
					return true;
				}
				return false;
			});
		});
	</script>

</body>

</html>