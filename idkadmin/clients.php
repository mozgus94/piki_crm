<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
} else {
	$page = "list";
	header("Location: clients?page=list");
}

if (isset($_GET["table_page"])) {
	$table_page = $_GET["table_page"];
} else {
	$table_page = 0;
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
				 * 							LIST ALL CLIENTS
				 * *********************************************************/
				case "list":
			?>

					<div class="row">
						<div class="col-xs-8">
							<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Klijenti</h1>
						</div>
						<div class="col-xs-4 text-right idk_margin_top10">
							<a href="<?php getSiteUrl(); ?>idkadmin/clients?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
						</div>
						<div class="col-xs-12">
							<hr>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">

								<?php if (isset($logged_employee_id) and $logged_employee_id = 1) { ?>
									<div class="row">
										<div class="col-xs-12">
											<a id="idk_import_json_url_clients" href="<?php getSiteUrl(); ?>idkadmin/do.php?form=import_json_url_clients"><button class="btn material-btn material-btn_success">SYNC KUPACA</button></a>
											<br>
											<br>
										</div>
									</div>

									<script>
										$(document).ready(function() {
											$('#idk_import_json_url_clients').on('click', function() {
												$('#idk_clients_table_div').css({
													'text-align': 'center',
													'position': 'absolute',
													'top': '50%',
													'transform': 'translateY(-50%)'
												});
												$('#idk_clients_table_div').html('<img alt="Učitavanje..." src="<?php getSiteUrl(); ?>idkadmin/images/ajax-loader.gif" style="margin: 0 auto;"><p>Import klijenata u toku ...</p>');
											});
										});
									</script>
								<?php } ?>

								<div class="row">
									<div class="col-xs-12" id="idk_clients_table_div">

										<!-- Store site url for use in datatables -->
										<input type="hidden" name="site_url" id="site_url" value="<?php getSiteUrl(); ?>">
										<input type="hidden" name="table_page" id="table_page" value="<?php echo $table_page; ?>">

										<!-- Success and error handling -->
										<?php
										if (isset($_GET['mess'])) {
											$mess = $_GET['mess'];
										} else {
											$mess = 0;
										}

										if ($mess == 1) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog klijenta.</div>';
										} elseif ($mess == 2) {
											echo '<div class="alert material-alert material-alert_danger">Greška: Klijent s tim nazivom i/ili korisničkim imenom već postoji u bazi podataka.</div>';
										} elseif ($mess == 3) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil klijenta.</div>';
										} elseif ($mess == 4) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil klijenta.</div>';
										} elseif ($mess == 5) {
											echo '<div class="alert material-alert material-alert_danger">Greška: Klijent kojeg pokušavate urediti ne postoji u bazi podataka.</div>';
										} elseif ($mess == 6) {
											echo '<div class="alert material-alert material-alert_danger">Greška: Klijent ne postoji u bazi podataka.</div>';
										} elseif ($mess == 7) {
											echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
										} elseif ($mess == 8) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste importovali JSON datoteku.</div>';
										} elseif ($mess == 9) {
											echo '<div class="alert material-alert material-alert_danger">Greška: JSON datoteka koju ste pokušali importovati nije validna.</div>';
										} elseif ($mess == 10) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste importovali CSV datoteku.</div>';
										} elseif ($mess == 11) {
											echo '<div class="alert material-alert material-alert_danger">Greška: CSV datoteka koju ste pokušali importovati nije validna.</div>';
										} elseif ($mess == 12) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste importovali klijente.</div>';
										}
										?>

										<!-- Clients table -->
										<table id="idk_clients_table" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th></th>
													<th>Naziv</th>
													<!-- <th>Šifra klijenta</th> -->
													<th>Adresa</th>
													<th>ID broj</th>
													<th>PDV broj</th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
													<th></th>
												</tr>
											</thead>

											<tbody>
											</tbody>
										</table>
										<!-- End clients table -->

										<!-- Modal -->
										<div class="modal material-modal material-modal_danger fade" id="archiveModal">
											<div class="modal-dialog">
												<div class="modal-content material-modal__content">
													<div class="modal-header material-modal__header">
														<button class="close material-modal__close" data-dismiss="modal">&times;</button>
														<h4 class="modal-title material-modal__title">Arhiviranje</h4>
													</div>
													<div class="modal-body material-modal__body">
														<p>Jeste li sigurni da želite arhivirati klijenta?</p>
													</div>
													<div class="modal-footer material-modal__footer">
														<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
														<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
													</div>
												</div>
											</div>
										</div>

									</div>
								</div>
							</div>
						</div>
					</div>
					<?php

					break;



					/************************************************************
					 * 							ADD NEW CLIENT
					 * *********************************************************/
				case "add":

					if ($getEmployeeStatus == 1 or $getEmployeeStatus == 2) {
					?>

						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Dodaj novog klijenta</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/clients?page=list&table_page=<?php echo $table_page; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-md-offset-1 col-md-8">

											<!-- Form - add client -->
											<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_client" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

												<input type="hidden" name="table_page" id="table_page" value="<?php echo $table_page; ?>">

												<div class="form-group">
													<label for="client_name" class="col-sm-3 control-label"><span class="text-danger">*</span>
														Naziv:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="client_name" id="client_name" placeholder="Naziv" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<!-- <div class="form-group">
													<label for="client_code" class="col-sm-3 control-label">Šifra klijenta:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="client_code" id="client_code" placeholder="Šifra klijenta">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div> -->

												<div class="form-group">
													<label class="col-sm-3 control-label">Kreiraj B2B račun:</label>
													<div class="col-sm-9">
														<div class="main-container__column materail-switch materail-switch_success">
															<input class="materail-switch__element" type="checkbox" id="b2b_account">
															<label class="materail-switch__label" for="b2b_account"></label>
														</div>
													</div>
												</div>
												<script>
													jQuery(document).ready(function($) {
														//reset
														$("#b2b_account").prop("checked", false);
														$("#b2b_account").click(function() {

															if ($("#b2b_account").is(":checked")) {
																//checked
																$("#b2b_account_info").removeClass("hidden").fadeOut(0).fadeIn(1000);
															} else {
																//unchecked
																$("#b2b_account_info").addClass("hidden");
															}

														});
													});
												</script>
												<div class="hidden" id="b2b_account_info">
													<div class="form-group">
														<label for="client_username" class="col-sm-3 control-label">Korisničko
															ime:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="client_username" id="client_username" placeholder="Korisničko ime">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>
													<div class="form-group">
														<label for="client_password" class="col-sm-3 control-label">Lozinka:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="password" name="client_password" id="client_password" placeholder="Lozinka">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="client_business_type" class="col-sm-3 control-label"><span class="text-danger">*</span> Vrsta poslovanja:</label>
													<div class="col-sm-9">

														<select class="selectpicker" id="client_business_type" name="client_business_type" data-live-search="true" required>
															<option value=""></option>
															<?php
															$select_query = $db->prepare("
																SELECT od_data, od_value
																FROM idk_client_otherdata
																WHERE od_group = :od_group
																ORDER BY od_value");

															$select_query->execute(array(
																':od_group' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['od_value'] . "' data-tokens='" . $select_row['od_data'] . "'>" . $select_row['od_data'] . "</option>";
															}
															?>
														</select>

													</div>
												</div>

												<div class="form-group">
													<label for="client_id_number" class="col-sm-3 control-label">ID broj:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="client_id_number" id="client_id_number" placeholder="ID broj">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="client_pdv_number" class="col-sm-3 control-label">PDV broj:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="client_pdv_number" id="client_pdv_number" placeholder="PDV broj">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="client_bank_account" class="col-sm-3 control-label">Žiro račun:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="client_bank_account" id="client_bank_account" placeholder="Žiro račun">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="client_responsible_person" class="col-sm-3 control-label">Odgovorna osoba:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="client_responsible_person" id="client_responsible_person" placeholder="Odgovorna osoba">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="client_phone" class="col-sm-3 control-label">Primarni
														telefon:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="client_phone" id="client_phone" placeholder="Primarni telefon">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="client_email" class="col-sm-3 control-label">
														Primarni email:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="email" name="client_email" id="client_email" placeholder="Primarni email">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="client_address" class="col-sm-3 control-label">Adresa: </label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="client_address" id="client_address" placeholder="Adresa">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="client_postal_code" class="col-sm-3 control-label">Poštanski broj:
													</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="client_postal_code" id="client_postal_code" placeholder="Poštanski broj">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="client_city" class="col-sm-3 control-label">Općina:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="client_city" name="client_city" data-live-search="true">
															<option value="">Odaberi općinu</option>
															<?php
															$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
																GROUP BY location_name
																ORDER BY location_name");

															$select_query->execute(array(
																':location_type' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['location_name'] . "' data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="client_region" class="col-sm-3 control-label">Regija:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="client_region" name="client_region" data-live-search="true">
															<option value="">Odaberi regiju</option>
															<?php
															$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
																GROUP BY location_name
																ORDER BY location_name");

															$select_query->execute(array(
																':location_type' => 2
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['location_name'] . "' data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="client_country" class="col-sm-3 control-label">Država:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="client_country" name="client_country" data-live-search="true">
															<option value="">Odaberi državu</option>
															<?php
															$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
																GROUP BY location_name
																ORDER BY location_name");

															$select_query->execute(array(
																':location_type' => 3
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['location_name'] . "' data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="client_other_info" class="col-sm-3 control-label">Ostale
														informacije:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<textarea class="form-control materail-input material-textarea" name="client_other_info" placeholder="Ostale informacije" rows="6" id="client_other_info"></textarea>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="client_max_rabat" class="col-sm-3 control-label">Maksimalni rabat %:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="number" min="0" max="100" step="0.01" name="client_max_rabat" id="client_max_rabat" placeholder="10.00">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label class="col-sm-3 control-label">Prikaži cijene na shopu za klijenta:</label>
													<div class="col-sm-9">
														<div class="main-container__column materail-switch materail-switch_success">
															<input class="materail-switch__element" type="checkbox" id="client_show_price" name="client_show_price" value="1">
															<label class="materail-switch__label" for="client_show_price"></label>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label class="col-sm-3 control-label">Prikaži količine na shopu za klijenta:</label>
													<div class="col-sm-9">
														<div class="main-container__column materail-switch materail-switch_success">
															<input class="materail-switch__element" type="checkbox" id="client_show_quantity" name="client_show_quantity" value="1">
															<label class="materail-switch__label" for="client_show_quantity"></label>
														</div>
													</div>
												</div>

												<!-- <div class="form-group">
													<label for="client_type" class="col-sm-3 control-label">Vrsta klijenta:</label>
													<div class="col-sm-9">

														<select class="selectpicker" id="client_type" name="client_type" data-live-search="true">
															<option value=""></option>
															<?php
															// $select_query = $db->prepare("
															// 	SELECT od_data, od_value
															// 	FROM idk_client_otherdata
															// 	WHERE od_group = :od_group
															// 	ORDER BY od_value");

															// $select_query->execute(array(
															// 	':od_group' => 2
															// ));

															// while ($select_row = $select_query->fetch()) {
															// 	echo "<option value='" . $select_row['od_value'] . "' data-tokens='" . $select_row['od_data'] . "'>" . $select_row['od_data'] . "</option>";
															// }
															?>
														</select>

													</div>
												</div> -->

												<!-- Add image -->
												<div class="form-group">
													<label for="client_image" class="col-sm-3 control-label">Logo:</label>
													<div class="col-sm-9">
														<div class="fileinput fileinput-new" data-provides="fileinput">
															<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
															<div>
																<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi
																		logo</span><span class="fileinput-exists">Promijeni</span><input type="file" name="client_image" id="client_image"></span>
																<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
																<script>
																	$(function() {
																		$('#client_image').change(function() {

																			var ext = $('#client_image').val().split('.').pop().toLowerCase();

																			if ($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
																				$('#idk_alert_ext').removeClass('hidden');
																				this.value = null;
																			} else {
																				$('#idk_alert_ext').addClass('hidden');
																			}

																			var f = this.files[0];

																			if (f.size > 20388608 || f.fileSize > 20388608) {
																				$('#idk_alert_size').removeClass('hidden');
																				this.value = null;
																			} else {
																				$('#idk_alert_size').addClass('hidden');
																			}

																		});
																	});
																</script>
															</div>
														</div>
													</div>
												</div>

												<!-- Alerts for image -->
												<div class="form-group">
													<label class="col-sm-3"></label>
													<div class="col-sm-9">
														<div id="idk_alert_size" class="hidden">
															<div class="alert material-alert material-alert_danger">Greška:
																Fotografija koju pokušavate
																dodati je veća od dozvoljene veličine.</div>
														</div>
														<div id="idk_alert_ext" class="hidden">
															<div class="alert material-alert material-alert_danger">Greška: Format
																fotografije koju
																pokušavate dodati nije dozvoljen.</div>
														</div>
													</div>
												</div>
												<br>

												<!-- Submit -->
												<div class="form-group">
													<div class="col-sm-offset-2 col-sm-10 text-right">
														<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
															<i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span>
														</button>
														<br>
														<small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
													</div>
												</div>
											</form>
											<!-- End form - add client -->

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
					 * 							EDIT CLIENT
					 * *********************************************************/
				case "edit":

					$client_id = $_GET['id'];

					//Check if client exists
					$check_query = $db->prepare("
						SELECT client_id
						FROM idk_client
						WHERE client_id = :client_id");

					$check_query->execute(array(
						':client_id' => $client_id
					));

					$number_of_rows = $check_query->rowCount();

					if ($number_of_rows == 1) {

						if ($getEmployeeStatus == 1) {

							$query = $db->prepare("
								SELECT client_name, client_code, client_username, client_id_number, client_business_type, client_pdv_number, client_bank_account, client_responsible_person, client_postal_code, client_image, client_address, client_city, client_country, client_other_info, client_region, client_type, client_max_rabat, client_show_price, client_show_quantity
								FROM idk_client
								WHERE client_id = :client_id");

							$query->execute(array(
								':client_id' => $client_id
							));

							$client = $query->fetch();

							$client_name = $client['client_name'];
							$client_code = $client['client_code'];
							$client_username = $client['client_username'];
							$client_id_number = $client['client_id_number'];
							$client_business_type = $client['client_business_type'];
							$client_pdv_number = $client['client_pdv_number'];
							$client_postal_code = $client['client_postal_code'];
							$client_image = $client['client_image'];
							$client_address = $client['client_address'];
							$client_city = $client['client_city'];
							$client_country = $client['client_country'];
							$client_other_info = $client['client_other_info'];
							$client_region = $client['client_region'];
							$client_type = $client['client_type'];
							$client_max_rabat = $client['client_max_rabat'];
							$client_show_price = $client['client_show_price'];
							$client_show_quantity = $client['client_show_quantity'];
							$client_image = $client['client_image'];
							$client_bank_account = $client['client_bank_account'];
							$client_responsible_person = $client['client_responsible_person'];

							//Get primary phone and email from idk_client_info
							$query_info = $db->prepare("
								SELECT ci_data
								FROM idk_client_info
								WHERE ci_group = :ci_group AND ci_primary = :ci_primary AND client_id = :client_id");

							//Get phone
							$query_info->execute(array(
								':ci_group' => 1,
								':ci_primary' => 1,
								':client_id' => $client_id
							));

							$number_of_rows_info = $query_info->rowCount();

							if ($number_of_rows_info > 0) {
								$client_info = $query_info->fetch();
								$client_phone = $client_info['ci_data'];
							} else {
								$client_phone = NULL;
							}

							//Get email
							$query_info->execute(array(
								':ci_group' => 2,
								':ci_primary' => 1,
								':client_id' => $client_id
							));

							$number_of_rows_info = $query_info->rowCount();

							if ($number_of_rows_info > 0) {
								$client_info = $query_info->fetch();
								$client_email = $client_info['ci_data'];
							} else {
								$client_email = NULL;
							}
						?>

							<div class="row">
								<div class="col-xs-8">
									<h1><i class="fa fa-briefcase idk_color_green" aria-hidden="true"></i> Uredi profil klijenta</h1>
								</div>
								<div class="col-xs-4 text-right idk_margin_top10">
									<a href="<?php getSiteUrl(); ?>idkadmin/clients?page=list&table_page=<?php echo $table_page; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
								</div>
								<div class="col-xs-12">
									<hr>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="content_box">
										<div class="row">
											<div class="col-md-offset-1 col-md-8">

												<!-- Form - edit client -->
												<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=edit_client" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

													<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />
													<input type="hidden" name="table_page" id="table_page" value="<?php echo $table_page; ?>">

													<div class="form-group">
														<label for="client_name" class="col-sm-3 control-label"><span class="text-danger">*</span>
															Naziv:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="client_name" id="client_name" value="<?php echo $client_name; ?>" placeholder="Naziv" required>
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<!-- <div class="form-group">
														<label for="client_code" class="col-sm-3 control-label">Šifra klijenta:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="client_code" id="client_code" value="<?php //echo $client_code; 
																																																																	?>" placeholder="Šifra klijenta">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div> -->

													<?php
													if ($client_username == NULL) {
													?>

														<div class="form-group">
															<label class="col-sm-3 control-label">Kreiraj B2B račun:</label>
															<div class="col-sm-9">
																<div class="main-container__column materail-switch materail-switch_success">
																	<input class="materail-switch__element" type="checkbox" id="b2b_account">
																	<label class="materail-switch__label" for="b2b_account"></label>
																</div>
															</div>
														</div>
														<script>
															jQuery(document).ready(function($) {
																//reset
																$("#b2b_account").prop("checked", false);
																$("#b2b_account").click(function() {

																	if ($("#b2b_account").is(":checked")) {
																		//checked
																		$("#b2b_account_info").removeClass("hidden").fadeOut(0).fadeIn(1000);
																	} else {
																		//unchecked
																		$("#b2b_account_info").addClass("hidden");
																	}

																});
															});
														</script>
														<div class="hidden" id="b2b_account_info">
															<div class="form-group">
																<label for="client_username" class="col-sm-3 control-label">Korisničko
																	ime:</label>
																<div class="col-sm-9">
																	<div class="materail-input-block materail-input-block_success">
																		<input class="form-control materail-input" type="text" name="client_username" id="client_username" placeholder="Korisničko ime">
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
															<div class="form-group">
																<label for="client_password" class="col-sm-3 control-label">Lozinka:</label>
																<div class="col-sm-9">
																	<div class="materail-input-block materail-input-block_success">
																		<input class="form-control materail-input" type="password" name="client_password" id="client_password" placeholder="Lozinka">
																		<span class="materail-input-block__line"></span>
																	</div>
																</div>
															</div>
														</div>

													<?php } else { ?>

														<div class="form-group">
															<label for="client_username" class="col-sm-3 control-label">Korisničko
																ime:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="text" name="client_username" id="client_username" placeholder="Korisničko ime" value="<?php echo $client_username; ?>">
																	<span class="materail-input-block__line"></span>
																</div>
															</div>
														</div>

														<div class="form-group">
															<label for="client_password" class="col-sm-3 control-label">Lozinka:</label>
															<div class="col-sm-9">
																<div class="materail-input-block materail-input-block_success">
																	<input class="form-control materail-input" type="password" name="client_password" id="client_password" placeholder="Lozinka">
																	<span class="materail-input-block__line"></span>
																</div>
																<small>Ukoliko želite promijeniti lozinku, unesite novu.</small>
															</div>
														</div>

													<?php } ?>

													<div class="form-group">
														<label for="client_business_type" class="col-sm-3 control-label"><span class="text-danger">*</span> Vrsta poslovanja:</label>
														<div class="col-sm-9">

															<select class="selectpicker" id="client_business_type" name="client_business_type" data-live-search="true" required>
																<option value=""></option>
																<?php
																$select_query = $db->prepare("
																	SELECT od_data, od_value
																	FROM idk_client_otherdata
																	WHERE od_group = :od_group
																	ORDER BY od_value");

																$select_query->execute(array(
																	':od_group' => 1
																));

																while ($select_row = $select_query->fetch()) {
																	echo "<option value='" . $select_row['od_value'] . "'";
																	if ($client_business_type == $select_row['od_value']) echo " selected";
																	else echo "";
																	echo " data-tokens='" . $select_row['od_data'] . "'>" . $select_row['od_data'] . "</option>";
																}
																?>
															</select>

														</div>
													</div>

													<div class="form-group">
														<label for="client_id_number" class="col-sm-3 control-label">ID broj:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="client_id_number" id="client_id_number" value="<?php echo $client_id_number; ?>" placeholder="ID broj">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="client_pdv_number" class="col-sm-3 control-label">PDV broj:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="client_pdv_number" id="client_pdv_number" value="<?php echo $client_pdv_number; ?>" placeholder="PDV broj">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="client_bank_account" class="col-sm-3 control-label">Žiro račun:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="client_bank_account" id="client_bank_account" value="<?php echo $client_bank_account; ?>" placeholder="Žiro račun">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="client_responsible_person" class="col-sm-3 control-label">Odgovorna osoba:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="client_responsible_person" id="client_responsible_person" value="<?php echo $client_responsible_person; ?>" placeholder="Odgovorna osoba">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="client_phone" class="col-sm-3 control-label">Primarni
															telefon:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="client_phone" id="client_phone" value="<?php echo $client_phone; ?>" placeholder="Primarni telefon">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="client_email" class="col-sm-3 control-label">
															Primarni email:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="email" name="client_email" id="client_email" value="<?php echo $client_email; ?>" placeholder="Primarni email">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="client_address" class="col-sm-3 control-label">Adresa: </label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="client_address" id="client_address" value="<?php echo $client_address; ?>" placeholder="Adresa">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="client_postal_code" class="col-sm-3 control-label">Poštanski broj:
														</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="client_postal_code" id="client_postal_code" value="<?php echo $client_postal_code; ?>" placeholder="Poštanski broj">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="client_city" class="col-sm-3 control-label">Općina:</label>
														<div class="col-sm-9">
															<select class="selectpicker" id="client_city" name="client_city" data-live-search="true">
																<option value="">Odaberi općinu</option>
																<?php
																$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
																GROUP BY location_name
																ORDER BY location_name");

																$select_query->execute(array(
																	':location_type' => 1
																));

																while ($select_row = $select_query->fetch()) {
																	echo "<option value='" . $select_row['location_name'] . "'";
																	if ($select_row['location_name'] == $client_city) {
																		echo " selected";
																	}
																	echo " data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
																}
																?>
															</select>
														</div>
													</div>

													<div class="form-group">
														<label for="client_region" class="col-sm-3 control-label">Regija:</label>
														<div class="col-sm-9">
															<select class="selectpicker" id="client_region" name="client_region" data-live-search="true">
																<option value="">Odaberi regiju</option>
																<?php
																$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
																GROUP BY location_name
																ORDER BY location_name");

																$select_query->execute(array(
																	':location_type' => 2
																));

																while ($select_row = $select_query->fetch()) {
																	echo "<option value='" . $select_row['location_name'] . "'";
																	if ($select_row['location_name'] == $client_region) {
																		echo " selected";
																	}
																	echo " data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
																}
																?>
															</select>
														</div>
													</div>

													<div class="form-group">
														<label for="client_country" class="col-sm-3 control-label">Država:</label>
														<div class="col-sm-9">
															<select class="selectpicker" id="client_country" name="client_country" data-live-search="true">
																<option value="">Odaberi državu</option>
																<?php
																$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
																GROUP BY location_name
																ORDER BY location_name");

																$select_query->execute(array(
																	':location_type' => 3
																));

																while ($select_row = $select_query->fetch()) {
																	echo "<option value='" . $select_row['location_name'] . "'";
																	if ($select_row['location_name'] == $client_country) {
																		echo " selected";
																	}
																	echo " data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
																}
																?>
															</select>
														</div>
													</div>

													<div class="form-group">
														<label for="client_other_info" class="col-sm-3 control-label">Ostale
															informacije:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<textarea class="form-control materail-input material-textarea" name="client_other_info" placeholder="Ostale informacije" rows="6" id="client_other_info"><?php echo $client_other_info; ?></textarea>
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="client_max_rabat" class="col-sm-3 control-label">Maksimalni rabat %:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="number" min="0" max="100" step="0.01" name="client_max_rabat" id="client_max_rabat" placeholder="10.00" value="<?php echo $client_max_rabat; ?>">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label class="col-sm-3 control-label">Prikaži cijene na shopu za klijenta:</label>
														<div class="col-sm-9">
															<div class="main-container__column materail-switch materail-switch_success">
																<input class="materail-switch__element" type="checkbox" id="client_show_price" name="client_show_price" value="1" <?php if ($client_show_price and $client_show_price == 1) {
																																																																										echo "checked='checked'";
																																																																									} ?>>
																<label class="materail-switch__label" for="client_show_price"></label>
															</div>
														</div>
													</div>
													<script>
														jQuery(document).ready(function($) {
															//reset
															$("#client_show_price").click(function() {

																$('#client_show_price').attr('checked', function(index, attr) {
																	return attr == 'checked' ? false : 'checked';
																});

															});
														});
													</script>

													<div class="form-group">
														<label class="col-sm-3 control-label">Prikaži količine na shopu za klijenta:</label>
														<div class="col-sm-9">
															<div class="main-container__column materail-switch materail-switch_success">
																<input class="materail-switch__element" type="checkbox" id="client_show_quantity" name="client_show_quantity" value="1" <?php if ($client_show_quantity and $client_show_quantity == 1) {
																																																																													echo "checked='checked'";
																																																																												}
																																																																												?>>
																<label class="materail-switch__label" for="client_show_quantity"></label>
															</div>
														</div>
													</div>
													<script>
														jQuery(document).ready(function($) {
															//reset
															$("#client_show_quantity").click(function() {

																$('#client_show_quantity').attr('checked', function(index, attr) {
																	return attr == 'checked' ? false : 'checked';
																});

															});
														});
													</script>

													<!-- <div class="form-group">
														<label for="client_type" class="col-sm-3 control-label">Vrsta klijenta:</label>
														<div class="col-sm-9">

															<select class="selectpicker" id="client_type" name="client_type" data-live-search="true">
																<option value=""></option>
																<?php
																// $select_query = $db->prepare("
																// 	SELECT od_data, od_value
																// 	FROM idk_client_otherdata
																// 	WHERE od_group = :od_group
																// 	ORDER BY od_value");

																// $select_query->execute(array(
																// 	':od_group' => 2
																// ));

																// while ($select_row = $select_query->fetch()) {
																// 	echo "<option value='" . $select_row['od_value'] . "'";
																// 	if ($client_type == $select_row['od_value']) echo " selected";
																// 	else echo "";
																// 	echo " data-tokens='" . $select_row['od_data'] . "'>" . $select_row['od_data'] . "</option>";
																// }
																?>
															</select>

														</div>
													</div> -->

													<!-- Add image -->
													<div class="form-group">
														<label for="client_image" class="col-sm-3 control-label">Logo:</label>
														<div class="col-sm-9">
															<div class="fileinput fileinput-new" data-provides="fileinput">
																<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
																	<img src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>">
																</div>
																<input type="hidden" name="client_image_url" value="<?php echo $client_image; ?>" />
																<div>
																	<span class="btn btn-default btn-file">
																		<span class="fileinput-new">Izaberi logo</span>
																		<span class="fileinput-exists">Promijeni</span>
																		<input type="file" name="client_image" id="client_image">
																	</span>
																	<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
																	<script>
																		$(function() {
																			$('#client_image').change(function() {

																				var ext = $('#client_image').val().split('.').pop().toLowerCase();

																				if ($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
																					$('#idk_alert_ext').removeClass('hidden');
																					this.value = null;
																				} else {
																					$('#idk_alert_ext').addClass('hidden');
																				}

																				var f = this.files[0];

																				if (f.size > 20388608 || f.fileSize > 20388608) {
																					$('#idk_alert_size').removeClass('hidden');
																					this.value = null;
																				} else {
																					$('#idk_alert_size').addClass('hidden');
																				}

																			});
																		});
																	</script>
																</div>
															</div>
														</div>
													</div>

													<!-- Alerts for image -->
													<div class="form-group">
														<label class="col-sm-3"></label>
														<div class="col-sm-9">
															<div id="idk_alert_size" class="hidden">
																<div class="alert material-alert material-alert_danger">Greška:
																	Fotografija koju pokušavate
																	dodati je veća od dozvoljene veličine.</div>
															</div>
															<div id="idk_alert_ext" class="hidden">
																<div class="alert material-alert material-alert_danger">Greška: Format
																	fotografije koju
																	pokušavate dodati nije dozvoljen.</div>
															</div>
														</div>
													</div>
													<br>

													<!-- Submit -->
													<div class="form-group">
														<div class="col-sm-offset-2 col-sm-10 text-right">
															<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
																<i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span>
															</button>
															<br>
															<small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
														</div>
													</div>
												</form>
												<!-- End form - edit client -->

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
					} else {
						header("Location: clients?page=list&mess=5");
					}

					break;



					/************************************************************
					 * 							OPEN CLIENT PROFILE 
					 * *********************************************************/
				case "open":

					$client_id = $_GET['id'];

					//Mark notification as read
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

					//Check if client exists
					$check_query = $db->prepare("
						SELECT client_id
						FROM idk_client
						WHERE client_id = :client_id");

					$check_query->execute(array(
						':client_id' => $client_id
					));

					$number_of_rows = $check_query->rowCount();

					if ($number_of_rows == 1) {

						$query = $db->prepare("
							SELECT client_name, client_code, client_parent, client_id_number, client_business_type, client_pdv_number, client_bank_account, client_responsible_person, client_postal_code, client_image, client_address, client_city, client_country, client_other_info, client_region, client_type, client_max_rabat, client_important_note 
							FROM idk_client
							WHERE client_id = :client_id");

						$query->execute(array(
							':client_id' => $client_id
						));

						$client = $query->fetch();

						$client_name = $client['client_name'];
						$client_code = $client['client_code'];
						$client_parent = $client['client_parent'];
						$client_id_number = $client['client_id_number'];
						$client_business_type = $client['client_business_type'];
						$client_pdv_number = $client['client_pdv_number'];
						$client_postal_code = $client['client_postal_code'];
						$client_image = $client['client_image'];
						$client_address = $client['client_address'];
						$client_city = $client['client_city'];
						$client_country = $client['client_country'];
						$client_other_info = $client['client_other_info'];
						$client_region = $client['client_region'];
						$client_type = $client['client_type'];
						$client_max_rabat = $client['client_max_rabat'];
						$client_important_note  = $client['client_important_note'];
						$client_image = $client['client_image'];
						$client_bank_account = $client['client_bank_account'];
						$client_responsible_person = $client['client_responsible_person'];

						?>

						<div class="row idk_display_none_for_print">
							<div class="col-xs-8">
								<h1><a class="fancybox" rel="group" href="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>"><img class="idk_profile_img" src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>"></a>
									<?php echo $client_name; ?></h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/clients?page=list&table_page=<?php echo $table_page; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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

											<?php
											if (isset($_GET['mess'])) {
												$mess = $_GET['mess'];
											} else {
												$mess = 0;
											}

											if ($mess == 1) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste dodali novu bilješku.</div>
												<script>
													$(function() {
														$('[href="#notes"]').tab('show');
													});
												</script>
											<?php
											} elseif ($mess == 2) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste dodali novi dokument.</div>
												<script>
													$(function() {
														$('[href="#documents"]').tab('show');
													});
												</script>
											<?php
											} elseif ($mess == 3) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste obrisali dokument.</div>
												<script>
													$(function() {
														$('[href="#documents"]').tab('show');
													});
												</script>
											<?php
											} elseif ($mess == 4) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste obrisali bilješku.</div>
												<script>
													$(function() {
														$('[href="#notes"]').tab('show');
													});
												</script>
											<?php
											} elseif ($mess == 5) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste snimili važne napomene.</div>
												<script>
													$(function() {
														$('[href="#important"]').tab('show');
													});
												</script>
											<?php
											} elseif ($mess == 14) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste dodali novi telefonski broj.</div>
												<script>
													$(function() {
														$('[href="#info"]').tab('show');
													});
												</script>
											<?php
											} elseif ($mess == 15) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste postavili novi primarni telefonski broj.</div>
												<script>
													$(function() {
														$('[href="#info"]').tab('show');
													});
												</script>
											<?php
											} elseif ($mess == 16) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste obrisali kontakt informaciju.</div>
												<script>
													$(function() {
														$('[href="#info"]').tab('show');
													});
												</script>
											<?php
											} elseif ($mess == 17) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste dodali novu email adresu.</div>
												<script>
													$(function() {
														$('[href="#info"]').tab('show');
													});
												</script>
											<?php
											} elseif ($mess == 18) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste postavili novu primarnu email adresu.</div>
												<script>
													$(function() {
														$('[href="#info"]').tab('show');
													});
												</script>
											<?php
											} elseif ($mess == 19) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste dodali novu kontakt informaciju.
												</div>
												<script>
													$(function() {
														$('[href="#info"]').tab('show');
													});
												</script>
											<?php
											} elseif ($mess == 20) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste postavili novu primarnu kontakt informaciju.</div>
												<script>
													$(function() {
														$('[href="#info"]').tab('show');
													});
												</script>
											<?php
											} elseif ($mess == 21) { ?>
												<div class="alert material-alert material-alert_success">Uspješno ste uredili kontakt profil.</div>
												<script>
													$(function() {
														$('[href="#info"]').tab('show');
													});
												</script>
											<?php
											}
											?>

										</div>
									</div>
									<div id="myTabs" class="panel-group material-tabs-group">
										<ul class="nav nav-tabs material-tabs material-tabs_primary">
											<li class="active">
												<a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a>
											</li>
											<li>
												<a href="#notes" class="material-tabs__tab-link" data-toggle="tab">Bilješke</a>
											</li>
											<li>
												<a href="#documents" class="material-tabs__tab-link" data-toggle="tab">Dokumenti</a>
											</li>
											<li>
												<a href="#important" class="material-tabs__tab-link" data-toggle="tab">Ostalo</a>
											</li>
											<li>
												<a href="#offices" class="material-tabs__tab-link" data-toggle="tab">Poslovnice</a>
											</li>
										</ul>
										<div class="tab-content materail-tabs-content">
											<div class="tab-pane fade active in" id="info">
												<div class="row idk_client_info">

													<div class="col-md-6">
														<div class="row">
															<div class="col-sm-8 col-md-6">
																<h5>Osnovne informacije</h5>
															</div>
															<div class="col-sm-4 col-md-6 text-right">
																<a href="clients?page=edit&id=<?php echo $client_id; ?>&table_page=<?php echo $table_page; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
																	<i class="fa fa-pencil" aria-hidden="true"></i> <span></span>
																</a>
																&nbsp;
																<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" id="idk_print_client_btn">
																	<i class="fa fa-print" aria-hidden="true"></i> <span></span>
																</a>
															</div>
														</div>

														<input type="hidden" name="table_page" id="table_page" value="<?php echo $table_page; ?>">

														<!-- Get basic information -->
														<div class="row">
															<strong class="col-sm-4 text-right">Naziv:</strong>
															<div class="col-sm-8">
																<?php
																$select_query = $db->prepare("
																	SELECT od_data
																	FROM idk_client_otherdata
																	WHERE od_group = :od_group AND od_value = :od_value");

																$select_query->execute(array(
																	':od_group' => 1,
																	':od_value' => $client_business_type
																));

																$select_row = $select_query->fetch();

																$client_business_type_echo = $select_row['od_data'];

																// echo $client_name . " " . $client_business_type_echo;
																echo $client_name;
																?>
															</div>
														</div>
														<!-- <div class="row">
															<strong class="col-sm-4 text-right">Šifra klijenta:</strong>
															<div class="col-sm-8"><?php //echo $client_code; 
																										?></div>
														</div> -->
														<?php if (isset($client_parent)) { ?>
															<div class="row">
																<strong class="col-sm-4 text-right">Poslovnica - pripada klijentu:</strong>
																<div class="col-sm-8">
																	<?php
																	if (isset($client_parent)) {
																		$select_query = $db->prepare("
																			SELECT t1.client_name, t2.od_data
																			FROM idk_client t1
																			INNER JOIN idk_client_otherdata t2
																			ON t1.client_business_type = t2.od_value
																			WHERE t1.client_id = :client_id AND t2.od_group = :od_group");

																		$select_query->execute(array(
																			':od_group' => 1,
																			':client_id' => $client_parent
																		));

																		$select_row = $select_query->fetch();

																		$client_parent_name_echo = $select_row['client_name'];
																		$client_parent_business_type_echo = $select_row['od_data'];

																		echo $client_parent_name_echo . " " . $client_parent_business_type_echo;
																	}
																	?>
																</div>
															</div>
														<?php } ?>
														<div class="row">
															<strong class="col-sm-4 text-right">ID broj:</strong>
															<div class="col-sm-8"><?php echo $client_id_number; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">PDV broj:</strong>
															<div class="col-sm-8"><?php echo $client_pdv_number; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Žiro račun:</strong>
															<div class="col-sm-8"><?php echo $client_bank_account; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Odgovorna osoba:</strong>
															<div class="col-sm-8"><?php echo $client_responsible_person; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Adresa:</strong>
															<div class="col-sm-8"><?php echo $client_address; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Poštanski broj:</strong>
															<div class="col-sm-8"><?php echo $client_postal_code; ?>
															</div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Općina:</strong>
															<div class="col-sm-8"><?php echo $client_city; ?>
															</div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Regija:</strong>
															<div class="col-sm-8"><?php echo $client_region; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Država:</strong>
															<div class="col-sm-8"><?php echo $client_country; ?></div>
														</div>
														<!-- <div class="row">
															<strong class="col-sm-4 text-right">Vrsta klijenta:</strong>
															<div class="col-sm-8">
																<?php
																// $select_query = $db->prepare("
																// 	SELECT od_data
																// 	FROM idk_client_otherdata
																// 	WHERE od_group = :od_group AND od_value = :od_value");

																// $select_query->execute(array(
																// 	':od_group' => 2,
																// 	':od_value' => $client_type
																// ));

																// $number_of_rows_client_type = $select_query->rowCount();

																// if ($number_of_rows_client_type !== 0) {
																// 	$select_row = $select_query->fetch();

																// 	echo $select_row['od_data'];
																// }
																?>
															</div>
														</div> -->
														<div class="row">
															<strong class="col-sm-4 text-right">Maksimalni rabat:</strong>
															<div class="col-sm-8"><?php if (isset($client_max_rabat)) {
																											echo $client_max_rabat . "%";
																										} ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Ostale informacije:</strong>
															<div class="col-sm-8"><?php echo $client_other_info; ?></div>
														</div>
													</div>

													<div class="col-md-6">
														<div class="row">
															<div class="col-sm-9">
																<h5>Telefon</h5>
															</div>
															<div class="col-sm-3 text-right">
																<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#phoneModal">
																	<i class="fa fa-plus" aria-hidden="true"></i> <span></span>
																</a>
																<!-- Modal add phone -->
																<div class="modal material-modal material-modal_primary fade text-left" id="phoneModal">
																	<div class="modal-dialog ">
																		<div class="modal-content material-modal__content">
																			<div class="modal-header material-modal__header">
																				<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																				<h4 class="modal-title material-modal__title">Dodaj telefonski broj</h4>
																			</div>
																			<div class="modal-body material-modal__body">

																				<!-- Form - add client phone -->
																				<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_client_phone" method="post" role="form" class="form-horizontal">

																					<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />

																					<div class="form-group">
																						<label for="ci_title_phone" class="col-sm-3 control-label"><span class="text-danger">*</span>
																							Naziv:</label>
																						<div class="col-sm-9">
																							<div class="materail-input-block materail-input-block_success">
																								<input class="form-control materail-input" type="text" name="ci_title" id="ci_title_phone" placeholder="Telefon" required>
																								<span class="materail-input-block__line"></span>
																							</div>
																						</div>
																					</div>

																					<div class="form-group">
																						<label for="ci_data_phone" class="col-sm-3 control-label"><span class="text-danger">*</span> Broj
																							telefona:</label>
																						<div class="col-sm-9">
																							<div class="materail-input-block materail-input-block_success">
																								<input class="form-control materail-input" type="text" name="ci_data" id="ci_data_phone" placeholder="003876XXXXXXXX" required>
																								<span class="materail-input-block__line"></span>
																							</div>
																						</div>
																					</div>
																			</div>

																			<div class="modal-footer material-modal__footer">
																				<ul class="list-inline">
																					<li class="hidden">
																						<i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i>
																					</li>
																					<li>
																						<button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
																					</li>
																				</ul>
																				</form>
																				<!-- End form - add client phone -->

																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

														<!-- Get phone from idk_client_info -->
														<div class="table-responsive">
															<table class="table table-striped">
																<tbody>
																	<?php
																	$query_phones = $db->prepare("
																		SELECT ci_id, ci_title, ci_data, ci_primary
																		FROM idk_client_info
																		WHERE ci_group = :ci_group AND client_id = :client_id
																		ORDER BY ci_primary DESC");

																	$query_phones->execute(array(
																		':ci_group' => 1,
																		':client_id' => $client_id
																	));

																	while ($client_phones = $query_phones->fetch()) {

																		$ci_id = $client_phones['ci_id'];
																		$ci_title = $client_phones['ci_title'];
																		$ci_data = $client_phones['ci_data'];
																		$ci_primary = $client_phones['ci_primary'];
																	?>

																		<tr>
																			<td>
																				<?php echo $ci_title; ?>:
																			</td>
																			<td>
																				<a href="tel:<?php echo $ci_data; ?>"><?php echo $ci_data; ?></a>
																			</td>
																			<td class="text-right">
																				<ul class="list-inline">
																					<?php if ($ci_primary == 1) {
																						echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>';
																					} else {
																						echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteUrlr() . 'idkadmin/do.php?form=set_primary_phone_client&ci_id=' . $ci_id . '&client_id=' . $client_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>';
																					} ?>
																					<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteUrl(); ?>idkadmin/clients?page=del_client_info&id=<?php echo $ci_id; ?>" data-toggle="modal" data-target="#delPhoneModal" class="delPhone"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
																				</ul>
																			</td>

																			<script>
																				$(".delPhone").click(function() {
																					var addressValue = $(this).attr("data");
																					document.getElementById("delPhone_link").href = addressValue;
																				});
																			</script>
																			<!-- DelPhone Modal -->
																			<div class="modal material-modal material-modal_danger fade" id="delPhoneModal">
																				<div class="modal-dialog">
																					<div class="modal-content material-modal__content">
																						<div class="modal-header material-modal__header">
																							<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																							<h4 class="modal-title material-modal__title">Brisanje</h4>
																						</div>
																						<div class="modal-body material-modal__body">
																							<p>Jeste li sigurni da želite obrisati broj telefona?</p>
																						</div>
																						<div class="modal-footer material-modal__footer">
																							<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																							<a id="delPhone_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																						</div>
																					</div>
																				</div>
																			</div>
																		</tr>
																	<?php } ?>

																</tbody>
															</table>
														</div>

														<div class="row">
															<div class="col-sm-9">
																<h5>Email</h5>
															</div>
															<div class="col-sm-3 text-right">
																<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#emailModal">
																	<i class="fa fa-plus" aria-hidden="true"></i> <span></span>
																</a>
																<!-- Modal add email -->
																<div class="modal material-modal material-modal_primary fade text-left" id="emailModal">
																	<div class="modal-dialog ">
																		<div class="modal-content material-modal__content">
																			<div class="modal-header material-modal__header">
																				<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																				<h4 class="modal-title material-modal__title">Dodaj email adresu</h4>
																			</div>
																			<div class="modal-body material-modal__body">

																				<!-- Form - add client email -->
																				<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_client_email" method="post" role="form" class="form-horizontal">

																					<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />

																					<div class="form-group">
																						<label for="ci_title_email" class="col-sm-3 control-label"><span class="text-danger">*</span>
																							Naziv:</label>
																						<div class="col-sm-9">
																							<div class="materail-input-block materail-input-block_success">
																								<input class="form-control materail-input" type="text" name="ci_title" id="ci_title_email" placeholder="Privatni" required>
																								<span class="materail-input-block__line"></span>
																							</div>
																						</div>
																					</div>

																					<div class="form-group">
																						<label for="ci_data_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Email
																							adresa:</label>
																						<div class="col-sm-9">
																							<div class="materail-input-block materail-input-block_success">
																								<input class="form-control materail-input" type="email" name="ci_data" id="ci_data_email" placeholder="info@primjer.com" required>
																								<span class="materail-input-block__line"></span>
																							</div>
																						</div>
																					</div>
																			</div>

																			<div class="modal-footer material-modal__footer">
																				<ul class="list-inline">
																					<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i>
																					</li>
																					<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
																					</li>
																				</ul>
																				</form>
																				<!-- End form - add client email -->

																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

														<!-- Get email from idk_client_info -->
														<div class="table-responsive">
															<table class="table table-striped">
																<tbody>
																	<?php
																	$query_emails = $db->prepare("
																		SELECT ci_id, ci_title, ci_data, ci_primary
																		FROM idk_client_info
																		WHERE ci_group = :ci_group AND client_id = :client_id
																		ORDER BY ci_primary DESC");

																	$query_emails->execute(array(
																		':ci_group' => 2,
																		':client_id' => $client_id
																	));

																	while ($client_emails = $query_emails->fetch()) {

																		$ci_id = $client_emails['ci_id'];
																		$ci_title = $client_emails['ci_title'];
																		$ci_data = $client_emails['ci_data'];
																		$ci_primary = $client_emails['ci_primary'];
																	?>

																		<tr>
																			<td>
																				<?php echo $ci_title; ?>:
																			</td>
																			<td>
																				<a href="mailto:<?php echo $ci_data; ?>"><?php echo $ci_data; ?></a>
																			</td>
																			<td class="text-right">
																				<ul class="list-inline">
																					<?php if ($ci_primary == 1) {
																						echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>';
																					} else {
																						echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteUrlr() . 'idkadmin/do.php?form=set_primary_email_client&ci_id=' . $ci_id . '&client_id=' . $client_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>';
																					} ?>
																					<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteUrl(); ?>idkadmin/clients?page=del_client_info&id=<?php echo $ci_id; ?>" data-toggle="modal" data-target="#delEmailModal" class="delEmail"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
																				</ul>
																			</td>

																			<script>
																				$(".delEmail").click(function() {
																					var addressValue = $(this).attr("data");
																					document.getElementById("delEmail_link").href = addressValue;
																				});
																			</script>
																			<!-- delEmail Modal -->
																			<div class="modal material-modal material-modal_danger fade" id="delEmailModal">
																				<div class="modal-dialog">
																					<div class="modal-content material-modal__content">
																						<div class="modal-header material-modal__header">
																							<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																							<h4 class="modal-title material-modal__title">Brisanje</h4>
																						</div>
																						<div class="modal-body material-modal__body">
																							<p>Jeste li sigurni da želite obrisati email adresu?</p>
																						</div>
																						<div class="modal-footer material-modal__footer">
																							<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																							<a id="delEmail_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																						</div>
																					</div>
																				</div>
																			</div>
																		</tr>
																	<?php } ?>

																</tbody>
															</table>
														</div>

														<div class="row">
															<div class="col-sm-9">
																<h5>Ostale kontakt informacije</h5>
															</div>
															<div class="col-sm-3 text-right">
																<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#otherModal"><i class="fa fa-plus" aria-hidden="true"></i>
																	<span></span></a>
																<!-- Modal add other -->
																<div class="modal material-modal material-modal_primary fade text-left" id="otherModal">
																	<div class="modal-dialog ">
																		<div class="modal-content material-modal__content">
																			<div class="modal-header material-modal__header">
																				<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																				<h4 class="modal-title material-modal__title">Dodaj kontakt informaciju</h4>
																			</div>
																			<div class="modal-body material-modal__body">

																				<!-- Form - add client other information -->
																				<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_client_other" method="post" role="form" class="form-horizontal">

																					<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />

																					<div class="form-group">
																						<label for="ci_title_otherinfo" class="col-sm-3 control-label"><span class="text-danger">*</span>
																							Naziv:</label>
																						<div class="col-sm-9">
																							<div class="materail-input-block materail-input-block_success">
																								<input class="form-control materail-input" type="text" name="ci_title" id="ci_title_otherinfo" placeholder="Facebook" required>
																								<span class="materail-input-block__line"></span>
																							</div>
																						</div>
																					</div>

																					<div class="form-group">
																						<label for="ci_data_otherinfo" class="col-sm-3 control-label"><span class="text-danger">*</span>
																							Link:</label>
																						<div class="col-sm-9">
																							<div class="materail-input-block materail-input-block_success">
																								<input class="form-control materail-input" type="text" name="ci_data" id="ci_data_otherinfo" placeholder="www.facebook.com/profil" required>
																								<span class="materail-input-block__line"></span>
																							</div>
																						</div>
																					</div>
																			</div>

																			<div class="modal-footer material-modal__footer">
																				<ul class="list-inline">
																					<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i>
																					</li>
																					<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
																					</li>
																				</ul>
																				</form>
																				<!-- End form - add client other information -->

																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

														<!-- Get other information from idk_client_info -->
														<div class="table-responsive">
															<table class="table table-striped">
																<tbody>
																	<?php
																	$query_otherinfo = $db->prepare("
																		SELECT ci_id, ci_title, ci_data, ci_primary
																		FROM idk_client_info
																		WHERE ci_group = :ci_group AND client_id = :client_id
																		ORDER BY ci_primary DESC");

																	$query_otherinfo->execute(array(
																		':ci_group' => 3,
																		':client_id' => $client_id
																	));

																	while ($client_otherinfo = $query_otherinfo->fetch()) {

																		$ci_id = $client_otherinfo['ci_id'];
																		$ci_title = $client_otherinfo['ci_title'];
																		$ci_data = $client_otherinfo['ci_data'];
																		$ci_primary = $client_otherinfo['ci_primary'];
																	?>

																		<tr>
																			<td>
																				<?php echo $ci_title; ?>:
																			</td>
																			<td>
																				<a href="<?php echo $ci_data; ?>" target="_blank"><?php echo $ci_data; ?></a>
																			</td>
																			<td class="text-right">
																				<ul class="list-inline">
																					<?php if ($ci_primary == 1) {
																						echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>';
																					} else {
																						echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteUrlr() . 'idkadmin/do.php?form=set_primary_other_client&ci_id=' . $ci_id . '&client_id=' . $client_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>';
																					} ?>
																					<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteUrl(); ?>idkadmin/clients?page=del_client_info&id=<?php echo $ci_id; ?>" data-toggle="modal" data-target="#delOtherModal" class="delOther"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
																				</ul>
																			</td>

																			<script>
																				$(".delOther").click(function() {
																					var addressValue = $(this).attr("data");
																					document.getElementById("delOther_link").href = addressValue;
																				});
																			</script>
																			<!-- DelPhone Modal -->
																			<div class="modal material-modal material-modal_danger fade" id="delOtherModal">
																				<div class="modal-dialog">
																					<div class="modal-content material-modal__content">
																						<div class="modal-header material-modal__header">
																							<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																							<h4 class="modal-title material-modal__title">Brisanje</h4>
																						</div>
																						<div class="modal-body material-modal__body">
																							<p>Jeste li sigurni da želite obrisati kontakt informaciju?</p>
																						</div>
																						<div class="modal-footer material-modal__footer">
																							<button class="btn material-btn material-btn" data-dismiss="modal">Odustani</button>
																							<a id="delOther_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																						</div>
																					</div>
																				</div>
																			</div>
																		</tr>
																	<?php } ?>

																</tbody>
															</table>
														</div>
													</div>
												</div>
											</div>

											<!-- Notes -->
											<div class="tab-pane fade" id="notes">
												<ul class="list-inline text-right">
													<li>
														<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#noteModal">
															<i class="fa fa-sticky-note-o" aria-hidden="true"></i> <span>Dodaj bilješku</span>
														</a>
													</li>
													<!-- Modal add note -->
													<div class="modal material-modal material-modal_primary fade text-left" id="noteModal">
														<div class="modal-dialog ">
															<div class="modal-content material-modal__content">
																<div class="modal-header material-modal__header">
																	<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																	<h4 class="modal-title material-modal__title">Dodaj bilješku</h4>
																</div>
																<div class="modal-body material-modal__body">

																	<!-- Form - add client note -->
																	<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_client_note" method="post" role="form" class="form-horizontal">

																		<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />

																		<div class="form-group">
																			<div class="col-md-offset-2 col-sm-8">
																				<div class="form-group materail-input-block materail-input-block_success">
																					<textarea class="form-control materail-input material-textarea" name="note_txt" placeholder="Bilješka" rows="6" required></textarea>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>
																</div>

																<div class="modal-footer material-modal__footer">
																	<ul class="list-inline">
																		<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i>
																		</li>
																		<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
																		</li>
																	</ul>
																	</form>
																	<!-- End form - add client note -->

																</div>
															</div>
														</div>
													</div>
													<!-- Modal add note end -->
												</ul>
												<hr>
												<div class="panel-group material-accordion material-accordion_primary" id="accordion1">
													<?php

													$year_query = $db->prepare("
														SELECT YEAR (note_datetime) AS note_datetime_year
														FROM idk_note
														WHERE client_id = :client_id AND note_group = :note_group
														GROUP BY YEAR (note_datetime)
														ORDER BY YEAR (note_datetime) DESC");

													$year_query->execute(array(
														':client_id' => $client_id,
														':note_group' => 1
													));

													while ($year_row = $year_query->fetch()) {

														$note_datetime_year = $year_row['note_datetime_year'];

														if ($note_datetime_year == date('Y')) {
															$idk_notes_in = "in";
														} else {
															$idk_notes_in = "";
														}

													?>

														<div class="panel panel-default material-accordion__panel material-accordion__panel">
															<div class="panel-heading material-accordion__heading">
																<h4 class="panel-title">
																	<a class="material-accordion__title" data-toggle="collapse" data-parent="#accordion1" href="#<?php echo $note_datetime_year; ?>"><?php echo $note_datetime_year; ?></a>
																</h4>
															</div>
															<div id="<?php echo $note_datetime_year; ?>" class="panel-collapse <?php echo $idk_notes_in; ?> collapse material-accordion__collapse">
																<div class="panel-body">
																	<?php
																	$notes_query = $db->prepare("
																		SELECT t1.note_id, t1.note_datetime, t1.note_txt, t2.client_name
																		FROM idk_note t1
																		INNER JOIN idk_client t2 USING(client_id)
																		WHERE YEAR (t1.note_datetime) = :note_datetime_year AND t1.client_id = :client_id AND t1.note_group = :note_group
																		ORDER BY t1.note_datetime DESC");

																	$notes_query->execute(array(
																		':note_datetime_year' => $note_datetime_year,
																		':note_group' => 1,
																		':client_id' => $client_id
																	));

																	while ($notes_row = $notes_query->fetch()) {

																		$note_date = date('d.m.Y.', strtotime($notes_row['note_datetime']));
																		$note_time = date('H:i', strtotime($notes_row['note_datetime']));
																		$note_id = $notes_row['note_id'];
																		$note_txt = $notes_row['note_txt'];
																		$client_name = $notes_row['client_name'];

																	?>

																		<div class="row">
																			<div class="col-sm-3">
																				<p>
																					<i class="fa fa-calendar text-primary" aria-hidden="true"></i>
																					<?php echo $note_date; ?> | <i class="fa fa-clock-o text-primary" aria-hidden="true"></i>
																					<?php echo $note_time; ?>
																				</p>
																				<p>
																					<i class="fa fa-user text-primary" aria-hidden="true"></i>
																					<?php echo $client_name; ?>
																				</p>
																			</div>
																			<div class="col-sm-7">
																				<p><?php echo $note_txt; ?></p>
																			</div>
																			<div class="col-sm-2 text-right">
																				<a href="#" data="<?php getSiteUrl(); ?>idkadmin/clients?page=del_note&note_id=<?php echo $note_id; ?>" data-toggle="modal" data-target="#deleteNoteModal" class="delete_note btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>

																				<script>
																					$(".delete_note").click(function() {
																						var addressValue = $(this).attr("data");
																						document.getElementById("delete_note_link").href = addressValue;
																					});
																				</script>
																				<!-- Modal -->
																				<div class="modal material-modal material-modal_danger fade text-left" id="deleteNoteModal">
																					<div class="modal-dialog">
																						<div class="modal-content material-modal__content">
																							<div class="modal-header material-modal__header">
																								<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																								<h4 class="modal-title material-modal__title">Brisanje</h4>
																							</div>
																							<div class="modal-body material-modal__body">
																								<p>Jeste li sigurni da želite obrisati bilješku?</p>
																							</div>
																							<div class="modal-footer material-modal__footer">
																								<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																								<a id="delete_note_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																							</div>
																						</div>
																					</div>
																				</div>
																			</div>
																		</div>
																		<hr>
																	<?php } ?>

																</div>
															</div>
														</div>
													<?php } ?>
												</div>
											</div>

											<!-- Documents -->
											<div class="tab-pane fade" id="documents">
												<ul class="list-inline text-right">
													<li>
														<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#docModal">
															<i class="fa fa-file-text-o" aria-hidden="true"></i> <span>Dodaj dokument</span>
														</a>
													</li>
													<!-- Modal add document -->
													<div class="modal material-modal material-modal_primary fade text-left" id="docModal">
														<div class="modal-dialog ">
															<div class="modal-content material-modal__content">
																<div class="modal-header material-modal__header">
																	<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																	<h4 class="modal-title material-modal__title">Dodaj dokument
																	</h4>
																</div>
																<div class="modal-body material-modal__body">

																	<!-- Form - add client doc -->
																	<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_client_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
																		<div id="idk_alert_size" class="row hidden">
																			<div class="col-sm-12">
																				<div class="alert material-alert material-alert_danger">
																					Greška: Dokument koji
																					pokušavate dodati je veći od dozvoljene
																					veličine.</div>
																			</div>
																		</div>
																		<div id="idk_alert_ext" class="row hidden">
																			<div class="col-sm-12">
																				<div class="alert material-alert material-alert_danger">
																					Greška: Format dokumenta kojeg
																					pokušavate dodati nije dozvoljen.</div>
																			</div>
																		</div>

																		<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />

																		<div class="form-group">
																			<div class="col-md-offset-2 col-sm-8">
																				<div class="form-group materail-input-block materail-input-block_success">
																					<input type="text" class="form-control materail-input" name="doc_name" id="doc_name" placeholder="Naziv dokumenta" required>
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>

																		<div class="form-group">
																			<div class="col-md-offset-2 col-sm-8">
																				<div class="form-group materail-input-block materail-input-block_success">
																					<input type="text" class="form-control materail-input" name="doc_desc" id="doc_desc" placeholder="Opis dokumenta">
																					<span class="materail-input-block__line"></span>
																				</div>
																			</div>
																		</div>

																		<div class="form-group">
																			<div class="col-md-offset-2 col-sm-8">
																				<div class="fileinput fileinput-new" data-provides="fileinput">
																					<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi
																							dokument</span><span class="fileinput-exists">Promijeni</span><input type="file" name="doc_file" id="doc_file" required required></span>
																					<i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni formati: jpg, jpeg, pdf, doc, docx, xls, xlsx, txt, ppt, pptx, png" aria-hidden="true"></i>
																					<span class="fileinput-filename"></span>
																					<a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">×</a>
																					<script>
																						$(function() {
																							$('#doc_file').change(function() {

																								var ext = $('#doc_file').val().split('.').pop().toLowerCase();

																								if ($.inArray(ext, ['jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt', 'ppt', 'pptx', 'png']) == -1) {
																									$('#idk_alert_ext').removeClass('hidden');
																									this.value = null;
																								} else {
																									$('#idk_alert_ext').addClass('hidden');
																								}

																								var f = this.files[0];

																								if (f.size > 20388608 || f.fileSize > 20388608) {
																									$('#idk_alert_size').removeClass('hidden');
																									this.value = null;
																								} else {
																									$('#idk_alert_size').addClass('hidden');
																								}

																							});
																						});
																					</script>
																				</div>
																			</div>
																		</div>
																</div>
																<div class="modal-footer material-modal__footer">
																	<ul class="list-inline">
																		<li class="hidden">
																			<i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i>
																		</li>
																		<li>
																			<button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
																		</li>
																	</ul>
																	</form>
																	<!-- End form - add client doc -->

																</div>
															</div>
														</div>
													</div>
													<!-- Modal add document end -->
												</ul>
												<hr>

												<script type="text/javascript">
													$(document).ready(function() {
														$('#idk_table_documents').DataTable({

															"responsive": true,

															"order": [
																[0, "desc"]
															],

															"bAutoWidth": false,

															"aoColumns": [{
																	"width": "10%"
																},
																{
																	"width": "40%"
																},
																{
																	"width": "40%"
																},
																{
																	"width": "5%",
																	"bSortable": false
																},
																{
																	"width": "5%",
																	"bSortable": false
																}
															]
														});
													});
												</script>

												<!-- Documents table -->
												<table id="idk_table_documents" class="display" cellspacing="0" width="100%">
													<thead>
														<tr>
															<th>Datum</th>
															<th>Naziv</th>
															<th>Opis</th>
															<th>Preuzimanje</th>
															<th></th>
														</tr>
													</thead>

													<tbody>
														<?php
														$query_doc = $db->prepare("
															SELECT t1.doc_id, t1.doc_name, t1.doc_desc, t1.doc_file, t1.doc_icon, t1.doc_datetime
															FROM idk_document t1, idk_client_document t2
															WHERE t1.doc_group = :doc_group AND t2.client_id = :client_id AND t2.doc_id = t1.doc_id");

														$query_doc->execute(array(
															':doc_group' => 1,
															':client_id' => $client_id
														));

														while ($client_doc = $query_doc->fetch()) {

															$doc_id = $client_doc['doc_id'];
															$doc_name = $client_doc['doc_name'];
															$doc_desc = $client_doc['doc_desc'];
															$doc_file = $client_doc['doc_file'];
															$doc_datetime = date('d.m.Y.', strtotime($client_doc['doc_datetime']));

															if ($client_doc['doc_icon'] == "jpg") {
																$doc_icon = '<i class="fa fa-file-image-o fa-lg" aria-hidden="true"></i>';
															} elseif ($client_doc['doc_icon'] == "pdf") {
																$doc_icon = '<i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i>';
															} elseif ($client_doc['doc_icon'] == "doc" or $client_doc['doc_icon'] == "docx") {
																$doc_icon = '<i class="fa fa-file-word-o fa-lg" aria-hidden="true"></i>';
															} elseif ($client_doc['doc_icon'] == "xls" or $client_doc['doc_icon'] == "xlsx") {
																$doc_icon = '<i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i>';
															} elseif ($client_doc['doc_icon'] == "txt") {
																$doc_icon = '<i class="fa fa-file-text-o fa-lg" aria-hidden="true"></i>';
															} elseif ($client_doc['doc_icon'] == "ppt" or $client_doc['doc_icon'] == "pptx") {
																$doc_icon = '<i class="fa fa-file-powerpoint-o fa-lg" aria-hidden="true"></i>';
															} else {
																$doc_icon = '<i class="fa fa-file-o fa-lg" aria-hidden="true"></i>';
															}
														?>

															<tr>
																<td class="text-center">
																	<?php echo $doc_datetime; ?>
																</td>
																<td>
																	<?php echo $doc_name; ?>
																</td>
																<td>
																	<?php echo $doc_desc; ?>
																</td>
																<td class="text-center">
																	<a href="files/clients/documents/<?php echo $doc_file; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK">
																		<?php echo $doc_icon; ?>
																	</a>
																</td>
																<td class="text-center">
																	<a href="#" data="<?php getSiteUrl(); ?>idkadmin/clients?page=del_doc&doc_id=<?php echo $doc_id; ?>" data-toggle="modal" data-target="#deleteDocModal" class="delete_doc btn material-btn material-btn_danger main-container__column">
																		<i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
																	</a>
																</td>
															</tr>
														<?php } ?>

														<script>
															$(".delete_doc").click(function() {
																var addressValue = $(this).attr("data");
																document.getElementById("delete_doc_link").href = addressValue;
															});
														</script>
														<!-- Modal -->
														<div class="modal material-modal material-modal_danger fade" id="deleteDocModal">
															<div class="modal-dialog">
																<div class="modal-content material-modal__content">
																	<div class="modal-header material-modal__header">
																		<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																		<h4 class="modal-title material-modal__title">Brisanje</h4>
																	</div>
																	<div class="modal-body material-modal__body">
																		<p>Jeste li sigurni da želite obrisati dokument?</p>
																	</div>
																	<div class="modal-footer material-modal__footer">
																		<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																		<a id="delete_doc_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																	</div>
																</div>
															</div>
														</div>

													</tbody>
												</table>
											</div>

											<!-- Important note -->
											<div class="tab-pane fade" id="important">

												<!-- Form - save employee important note -->
												<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=save_client_important_note" method="post" role="form" class="form-horizontal">

													<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />

													<div class="form-group">
														<div class="col-md-offset-1 col-sm-10">
															<div class="form-group materail-input-block materail-input-block_success">
																<textarea id="inote" class="form-control materail-input material-textarea" name="client_important_note" placeholder="Važne bilješke" rows="8"><?php echo base64_decode($client_important_note); ?></textarea>
																<span class="materail-input-block__line"></span>
															</div>
															<ul class="list-inline pull-right">
																<li class="hidden">
																	<i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i>
																</li>
																<li>
																	<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
																		<i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span>
																	</button>
																</li>
															</ul>
														</div>
													</div>
												</form>
												<!-- End form - save employee important note -->

												<script>
													$('#inote').trumbowyg({
														lang: 'hr',
														btns: [
															['undo', 'redo'],
															['formatting'],
															['strong', 'em', 'del'],
															['link'],
															['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
															['unorderedList', 'orderedList'],
															['horizontalRule'],
															['fullscreen']
														]
													});
												</script>
											</div>

											<!-- Poslovnice -->
											<div class="tab-pane fade" id="offices">

												<div class="row">
													<div class="col-md-12">
														<div class="content_box">
															<h5>Dodaj novu poslovnicu</h5>
															<div class="row">
																<div class="col-md-8 idk_setting_form_wrapper">

																	<!-- Form - add client office -->
																	<form id="idk_form" action="<?php getSiteURL(); ?>idkadmin/do.php?form=add_client_office" method="post" class="form-horizontal" role="form">

																		<input type="hidden" name="client_id" value="<?php echo $client_id; ?>" />

																		<div class="form-group">
																			<label for="client_office_id" class="col-sm-3 control-label"><span class="text-danger">*</span> Poslovnica:</label>
																			<div class="col-sm-9">
																				<select class="selectpicker" id="client_office_id" name="client_office_id" data-live-search="true" required>
																					<option value=""></option>
																					<?php
																					$select_query = $db->prepare("
																						SELECT client_id, client_name, client_code
																						FROM idk_client
																						WHERE client_id != :client_id AND client_parent IS NULL
																						ORDER BY client_name");

																					$select_query->execute(array(
																						':client_id' => $client_id
																					));

																					while ($select_row = $select_query->fetch()) {
																						echo "<option value='" . $select_row['client_id'] . "' data-tokens='" . $select_row['client_name'] . " " . $select_row['client_code'] . "'>" . $select_row['client_name'] . "</option>";
																					}
																					?>
																				</select>
																			</div>
																		</div>

																		<br>

																		<!-- Submit -->
																		<div class="form-group">
																			<div class="col-sm-offset-2 col-sm-10 text-right">
																				<button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
																					<i class="fa fa-plus" aria-hidden="true"></i>
																					<span>Dodaj</span>
																				</button>
																				<br>
																				<small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
																			</div>
																		</div>
																	</form>
																	<!-- End form - add client office -->

																</div>
															</div>
															<hr>

															<div class="row">
																<div class="col-xs-12">
																	<h5>Trenutne poslovnice</h5>

																	<script type="text/javascript">
																		$(document).ready(function() {
																			$('#idk_table_offices').DataTable({

																				"order": [
																					[0, "asc"]
																				],

																				"bAutoWidth": false,

																				"aoColumns": [{
																						"width": "10%",
																						"bSortable": false
																					},
																					{
																						"width": "40%"
																					},
																					{
																						"width": "40%"
																					},
																					{
																						"width": "10%",
																						"bSortable": false
																					}
																				]
																			});
																		});
																	</script>

																	<!-- Currencies table -->
																	<table id="idk_table_offices" class="display" cellspacing="0" width="100%">
																		<thead>
																			<tr>
																				<th>ID</th>
																				<th>Naziv</th>
																				<th>Adresa</th>
																				<th>Obriši poslovnicu</th>
																			</tr>
																		</thead>

																		<tbody>
																			<?php
																			$query = $db->prepare("
																				SELECT client_id, client_address, client_postal_code, client_city, client_region, client_country, client_name
																				FROM idk_client
																				WHERE client_parent = :client_parent");

																			$query->execute(array(
																				':client_parent' => $client_id
																			));

																			while ($row = $query->fetch()) {


																				$client_office_id = $row['client_id'];
																				$client_office_address = $row['client_address'];
																				$client_office_postal_code = $row['client_postal_code'];
																				$client_office_city = $row['client_city'];
																				$client_office_region = $row['client_region'];
																				$client_office_country = $row['client_country'];
																				$client_office_name = $row['client_name'];
																				$client_office_address_string = '';

																				if (isset($client_office_address)) {
																					$client_office_address_string .= $client_office_address . '<br>';
																				}
																				if (isset($client_office_postal_code)) {
																					$client_office_address_string .= $client_office_postal_code;
																				}
																				if (isset($client_office_city)) {
																					$client_office_address_string .= ' ' . $client_office_city;
																				}
																				$client_office_address_string .= '<br>';
																				if (isset($client_office_region)) {
																					$client_office_address_string .= $client_office_region . '<br>';
																				}
																				if (isset($client_office_country)) {
																					$client_office_address_string .= $client_office_country;
																				}

																			?>

																				<tr>
																					<td class="text-center">
																						<?php echo $client_office_id; ?>
																					</td>
																					<td>
																						<?php echo '<a href="' . getSiteURLr() . 'clients?page=open&id=' . $client_office_id . '">' . $client_office_name . '</a>'; ?>
																					</td>
																					<td>
																						<?php echo $client_office_address_string; ?>
																					</td>
																					<td class="text-center">
																						<a href="#" data="<?php getSiteURL(); ?>idkadmin/do.php?form=delete_client_office&client_id=<?php echo $client_id; ?>&client_office_id=<?php echo $client_office_id; ?>" data-toggle="modal" data-target="#modalDelete" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger">
																							<i class="fa fa-trash-o" aria-hidden="true"></i>
																						</a>
																					</td>
																				</tr>
																			<?php } ?>

																			<script>
																				$(".delete").click(function() {
																					var addressValue = $(this).attr("data");
																					document.getElementById("obrisi_link").href = addressValue;
																				});
																			</script>
																			<!-- Modal delete-->
																			<div class="modal material-modal material-modal_danger fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
																				<div class="modal-dialog">
																					<div class="modal-content material-modal__content">
																						<div class="modal-header material-modal__header">
																							<button class="close material-modal__close" data-dismiss="modal">&times;</span><span class="sr-only">Zatvori</span></button>
																							<h4 class="modal-title material-modal__title" id="modalDeleteLabel">Brisanje</h4>
																						</div>
																						<div class="modal-body material-modal__body">
																							<p>Jeste li sigurni da želite obrisati poslovnicu?</p>
																						</div>
																						<div class="modal-footer material-modal__footer">
																							<button type="button" class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																							<a id="obrisi_link" href=""><button type="button" class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																						</div>
																					</div>
																				</div>
																			</div>

																		</tbody>
																	</table>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
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

						<!-- Print client wrapper -->
						<div id="idk_print_client_wrapper" style="display: none;">
							<div id="print_header">
								<div class="container-fluid">
									<div class="row">
										<div class="col-xs-6">
											<h3>Podaci o klijentu</h3>
											<p class="idk_margin_top30">
												<strong>Klijent</strong> <br>
												<!-- <?php //echo $client_name . " " . $client_business_type_echo; 
															?> -->
												<?php echo $client_name; ?>
											</p>
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
											<h4>Osnovne informacije</h4>

											<?php if (isset($client_name) and $client_name != "") { ?>
												<div class="row">
													<div class="col-xs-6 text-right">
														<p><strong>Naziv:</strong></p>
													</div>
													<div class="col-xs-6">
														<!-- <p><?php //echo $client_name . " " . $client_business_type_echo; 
																		?></p> -->
														<p><?php echo $client_name; ?></p>
													</div>
												</div>
											<?php } ?>
											<!-- <?php //if (isset($client_code) and $client_code != "") { 
														?>
												<div class="row">
													<div class="col-xs-6 text-right">
														<p><strong>Šifra klijenta:</strong></p>
													</div>
													<div class="col-xs-6">
														<p><?php
																//echo $client_code;
																?></p>
													</div>
												</div>
											<?php //} 
											?> -->
											<?php if (isset($client_id_number) and $client_id_number != "") { ?>
												<div class="row">
													<div class="col-xs-6 text-right">
														<p><strong>ID broj:</strong></p>
													</div>
													<div class="col-xs-6">
														<p><?php
																echo $client_id_number;
																?></p>
													</div>
												</div>
											<?php } ?>
											<?php if (isset($client_pdv_number) and $client_pdv_number != "") { ?>
												<div class="row">
													<div class="col-xs-6 text-right">
														<p><strong>PDV broj:</strong></p>
													</div>
													<div class="col-xs-6">
														<p><?php
																echo $client_pdv_number;
																?></p>
													</div>
												</div>
											<?php } ?>
											<?php if (isset($client_bank_account) and $client_bank_account != "") { ?>
												<div class="row">
													<div class="col-xs-6 text-right">
														<p><strong>Žiro račun:</strong></p>
													</div>
													<div class="col-xs-6">
														<p><?php
																echo $client_bank_account;
																?></p>
													</div>
												</div>
											<?php } ?>
											<?php if (isset($client_responsible_person) and $client_responsible_person != "") { ?>
												<div class="row">
													<div class="col-xs-6 text-right">
														<p><strong>Odgovorna osoba:</strong></p>
													</div>
													<div class="col-xs-6">
														<p><?php
																echo $client_responsible_person;
																?></p>
													</div>
												</div>
											<?php } ?>
											<?php if (isset($client_address) and $client_address != "") { ?>
												<div class="row">
													<div class="col-xs-6 text-right">
														<p><strong>Adresa:</strong></p>
													</div>
													<div class="col-xs-6">
														<p><?php
																echo $client_address;
																?></p>
													</div>
												</div>
											<?php } ?>
											<?php if (isset($client_postal_code) and $client_postal_code != "") { ?>
												<div class="row">
													<div class="col-xs-6 text-right">
														<p><strong>Poštanski broj:</strong></p>
													</div>
													<div class="col-xs-6">
														<p><?php
																echo $client_postal_code;
																?></p>
													</div>
												</div>
											<?php } ?>
											<?php if (isset($client_city) and $client_city != "") { ?>
												<div class="row">
													<div class="col-xs-6 text-right">
														<p><strong>Općina:</strong></p>
													</div>
													<div class="col-xs-6">
														<p><?php
																echo $client_city;
																?></p>
													</div>
												</div>
											<?php } ?>
											<?php if (isset($client_region) and $client_region != "") { ?>
												<div class="row">
													<div class="col-xs-6 text-right">
														<p><strong>Regija:</strong></p>
													</div>
													<div class="col-xs-6">
														<p><?php
																echo $client_region;
																?></p>
													</div>
												</div>
											<?php } ?>
											<?php if (isset($client_country) and $client_country != "") { ?>
												<div class="row">
													<div class="col-xs-6 text-right">
														<p><strong>Država:</strong></p>
													</div>
													<div class="col-xs-6">
														<p><?php
																echo $client_country;
																?></p>
													</div>
												</div>
											<?php } ?>
											<?php if (isset($client_other_info) and $client_other_info != "") { ?>
												<div class="row">
													<div class="col-xs-6 text-right">
														<p><strong>Ostale informacije:</strong></p>
													</div>
													<div class="col-xs-6">
														<p><?php
																echo $client_other_info;
																?></p>
													</div>
												</div>
											<?php } ?>
										</div>

										<div class="col-xs-6">
											<h4>Kontakt informacije</h4>

											<?php
											$query_phones = $db->prepare("
											SELECT ci_title, ci_data
											FROM idk_client_info
											WHERE ci_group = :ci_group AND client_id = :client_id
											ORDER BY ci_primary DESC");

											$query_phones->execute(array(
												':ci_group' => 1,
												':client_id' => $client_id
											));

											while ($client_phones = $query_phones->fetch()) {

												$ci_title = $client_phones['ci_title'];
												$ci_data = $client_phones['ci_data'];

												if (isset($ci_data) and $ci_data != "") {
											?>
													<div class="row">
														<div class="col-xs-6 text-right">
															<p><strong><?php echo $ci_title; ?>:</strong></p>
														</div>
														<div class="col-xs-6">
															<p><?php echo $ci_data; ?></p>
														</div>
													</div>
											<?php }
											} ?>

											<?php
											$query_emails = $db->prepare("
											SELECT ci_title, ci_data
											FROM idk_client_info
											WHERE ci_group = :ci_group AND client_id = :client_id
											ORDER BY ci_primary DESC");

											$query_emails->execute(array(
												':ci_group' => 2,
												':client_id' => $client_id
											));

											while ($client_emails = $query_emails->fetch()) {

												$ci_title = $client_emails['ci_title'];
												$ci_data = $client_emails['ci_data'];

												if (isset($ci_data) and $ci_data != "") {
											?>
													<div class="row">
														<div class="col-xs-6 text-right">
															<p><strong><?php echo $ci_title; ?>:</strong></p>
														</div>
														<div class="col-xs-6">
															<p><?php echo $ci_data; ?></p>
														</div>
													</div>
											<?php }
											} ?>


											<?php
											$query_other = $db->prepare("
											SELECT ci_title, ci_data
											FROM idk_client_info
											WHERE ci_group = :ci_group AND client_id = :client_id
											ORDER BY ci_primary DESC");

											$query_other->execute(array(
												':ci_group' => 3,
												':client_id' => $client_id
											));

											while ($client_other = $query_other->fetch()) {

												$ci_title = $client_other['ci_title'];
												$ci_data = $client_other['ci_data'];

												if (isset($ci_data) and $ci_data != "") {
											?>
													<div class="row">
														<div class="col-xs-6 text-right">
															<p><strong><?php echo $ci_title; ?>:</strong></p>
														</div>
														<div class="col-xs-6">
															<p><?php echo $ci_data; ?></p>
														</div>
													</div>
											<?php }
											} ?>

										</div>
									</div>

								</div>
							</div>
						</div>

						<script type="text/javascript">
							$(document).ready(function() {
								$('#idk_print_client_btn').click(function() {
									$('.idk_display_none_for_print').css('display', 'none');
									$('#content').css('background-color', '#fff');
									$('#content').css('margin', '0');
									$('#content').css('padding', '0');
									$('#idk_print_client_wrapper').css('display', 'block');
									window.print();
									$('#idk_print_client_wrapper').css('display', 'none');
									$('#content').css('background-color', '#eee');
									$('#content').css('margin-left', '220px');
									$('#content').css('padding-top', '65px');
									$('.idk_display_none_for_print').css('display', 'block');
								});
							});
						</script>

			<?php
					} else {
						header("Location: clients?page=list&mess=6");
					}
					break;



					/************************************************************
					 * 							DELETE DOCUMENT
					 * *********************************************************/
				case "del_doc":

					if ($getEmployeeStatus == 1) {

						$doc_id = $_GET['doc_id'];

						//Get document name, dataid and delete document
						$doc_open_query = $db->prepare("
							SELECT t1.doc_name, t1.doc_file, t2.client_id, t3.client_name
							FROM idk_document t1, idk_client_document t2, idk_client t3
							WHERE t1.doc_id = :doc_id AND t2.doc_id = :doc_id AND t3.client_id = t2.client_id");

						$doc_open_query->execute(array(
							':doc_id' => $doc_id
						));

						$doc_open = $doc_open_query->fetch();

						$doc_name = $doc_open['doc_name'];
						$doc_file = $doc_open['doc_file'];
						$client_id = $doc_open['client_id'];
						$client_name = $doc_open['client_name'];

						unlink("files/clients/documents/${doc_file}");

						//Add to log
						$log_desc = "Obrisao dokument za klijenta: ${client_name} - ${doc_name}";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						//Delete document from db - idk_document
						$doc_del_query = $db->prepare("
							DELETE FROM idk_document
							WHERE doc_id = :doc_id");

						$doc_del_query->execute(array(
							':doc_id' => $doc_id
						));

						//Delete document from idk_client_document
						$doc_del_query = $db->prepare("
							DELETE FROM idk_client_document
							WHERE doc_id = :doc_id");

						$doc_del_query->execute(array(
							':doc_id' => $doc_id
						));

						header("Location: " . getSiteUrlr() . "idkadmin/clients?page=open&id=$client_id&mess=3");
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
					 * 							DELETE NOTE
					 * *********************************************************/
				case "del_note":

					if ($getEmployeeStatus == 1) {

						$note_id = $_GET['note_id'];

						//Get note_txt and client_id
						$note_open_query = $db->prepare("
							SELECT t1.note_txt, t1.client_id, t2.client_name
							FROM idk_note t1, idk_client t2
							WHERE note_id = :note_id AND t2.client_id = t1.client_id");

						$note_open_query->execute(array(
							':note_id' => $note_id
						));

						$note_open = $note_open_query->fetch();

						$note_txt = $note_open['note_txt'];
						$client_id = $note_open['client_id'];
						$client_name = $note_open['client_name'];

						//Add to log
						$log_desc = "Obrisao bilješku za klijenta: ${client_name} - ${note_txt}";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						//Delete note from db
						$note_del_query = $db->prepare("
							DELETE FROM idk_note
							WHERE note_id = :note_id");

						$note_del_query->execute(array(
							':note_id' => $note_id
						));

						header("Location: " . getSiteUrlr() . "idkadmin/clients?page=open&id=${client_id}&mess=4");
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
					 * 							DELETE CLIENT INFO
					 * *********************************************************/
				case "del_client_info":

					if ($getEmployeeStatus == 1) {

						$ci_id = $_GET['id'];

						//Get ci_title, ci_data and client_id
						$ci_open_query = $db->prepare("
							SELECT t1.ci_title, t1.ci_data, t1.client_id, t2.client_name
							FROM idk_client_info t1, idk_client t2
							WHERE t1.ci_id = :ci_id AND t2.client_id = t1.client_id");

						$ci_open_query->execute(array(
							':ci_id' => $ci_id
						));

						$ci_open = $ci_open_query->fetch();

						$ci_title = $ci_open['ci_title'];
						$ci_data = $ci_open['ci_data'];
						$client_id = $ci_open['client_id'];
						$client_name = $ci_open['client_name'];

						//Add to log
						$log_desc = "Obrisao info: ${ci_title} - ${ci_data} za klijenta: ${client_name}";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						//Delete employee info from db
						$ci_del_query = $db->prepare("
							DELETE FROM idk_client_info
							WHERE ci_id = :ci_id");

						$ci_del_query->execute(array(
							':ci_id' => $ci_id
						));

						header("Location: " . getSiteUrlr() . "idkadmin/clients?page=open&id=${client_id}&mess=16");
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
					 * 							ARCHIVE
					 * *********************************************************/
				case "archive":

					if ($getEmployeeStatus == 1) {

						$client_id = $_GET['id'];

						//Get client name
						$query_select = $db->prepare("
							SELECT client_name
							FROM idk_client
							WHERE client_id = :client_id");

						$query_select->execute(array(
							':client_id' => $client_id
						));

						$client_select = $query_select->fetch();

						$client_name = $client_select['client_name'];

						//Save
						$query = $db->prepare("
							UPDATE idk_client
							SET client_active = :client_active
							WHERE client_id = :client_id");

						$query->execute(array(
							':client_active' => 0,
							':client_id' => $client_id
						));

						//Save changes to b2b clients stats in db
						$query_client = $db->prepare("
							SELECT created_at
							FROM idk_client
							WHERE client_id = :client_id");

						$query_client->execute(array(
							':client_id' => $client_id
						));

						$row_client = $query_client->fetch();
						$created_at = $row_client['created_at'];

						$query_clients_stats = $db->prepare("
							UPDATE idk_stat
							SET stat_b2b_clients = stat_b2b_clients - 1
							WHERE stat_month = :stat_month");

						$query_clients_stats->execute(array(
							':stat_month' => date('Y-m-01', strtotime($created_at))
						));

						//Add to log
						$log_desc = "Arhivirao klijenta: ${client_name}";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: " . getSiteUrlr() . "idkadmin/clients?page=list&mess=4");
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