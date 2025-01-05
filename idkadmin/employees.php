<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
} else {
	$page = "list";
	header("Location: employees?page=list");
}

?>

<!DOCTYPE html>
<html>

<head>

	<?php include('includes/head.php'); ?>

</head>

<body>
	<header>
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('menu.php'); ?>
	</div>
	<div id="content">
		<div class="container-fluid">
			<?php
			switch ($page) {



					/************************************************************
 * 							LIST ALL EMPLOYEES
 * *********************************************************/
				case "list":
			?>

					<div class="row">
						<div class="col-xs-8">
							<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Zaposlenici</h1>
						</div>
						<div class="col-xs-4 text-right idk_margin_top10">
							<a href="<?php getSiteUrl(); ?>idkadmin/employees?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
						</div>
						<div class="col-xs-12">
							<hr>
						</div>
					</div>
					<div class="row">
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

										if ($mess == 1) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog zaposlenika.</div>';
										} elseif ($mess == 2) {
											echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik kojeg pokušavate dodati već postoji u bazi podataka.</div>';
										} elseif ($mess == 3) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili profil zaposlenika.</div>';
										} elseif ($mess == 4) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali profil zaposlenika.</div>';
										} elseif ($mess == 5) {
											echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik kojeg pokušavate urediti ne postoji u bazi podataka.</div>';
										} elseif ($mess == 6) {
											echo '<div class="alert material-alert material-alert_danger">Greška: Korisnik ne postoji u bazi podataka.</div>';
										}
										?>

										<!-- Filling the table with data -->
										<script type="text/javascript">
											$(document).ready(function() {
												$('#idk_table').DataTable({

													responsive: true,

													"order": [
														[1, "asc"]
													],

													"bAutoWidth": false,

													"aoColumns": [{
															"width": "5%",
															"bSortable": false
														},
														{
															"width": "25%"
														},
														{
															"width": "20%"
														},
														{
															"width": "25%"
														},
														{
															"width": "15%"
														},
														{
															"width": "10%",
															"bSortable": false
														}
													]
												});
											});
										</script>

										<!-- Employees table -->
										<table id="idk_table" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th></th>
													<th>Ime i prezime</th>
													<th>Telefon</th>
													<th>Email</th>
													<th>Status</th>
													<th></th>
												</tr>
											</thead>

											<tbody>

												<!-- Get data for employee -->
												<?php
												$query = $db->prepare("
													SELECT employee_id, employee_first_name, employee_last_name, employee_login_email, employee_status, employee_image, employee_commercialist_type
													FROM idk_employee
													WHERE employee_active = 1");

												$query->execute();

												while ($employee = $query->fetch()) {

													$employee_id = $employee['employee_id'];
													$employee_first_name = $employee['employee_first_name'];
													$employee_last_name = $employee['employee_last_name'];
													$employee_login_email = $employee['employee_login_email'];
													$employee_image = $employee['employee_image'];
													$employee_commercialist_type = $employee['employee_commercialist_type'];

													if ($employee['employee_status'] == 1) {
														$employee_status = "Administrator";
													} elseif ($employee['employee_status'] == 2) {
														$employee_status = "Komercijalista";
													} elseif ($employee['employee_status'] == 3) {
														$employee_status = "Skladištar";
													}

													//Get primary phone from idk_employee_info
													$query_phone = $db->prepare("
														SELECT ei_data
														FROM idk_employee_info
														WHERE ei_group = :ei_group AND ei_primary = :ei_primary AND employee_id = :employee_id");

													$query_phone->execute(array(
														':ei_group' => 1,
														':ei_primary' => 1,
														':employee_id' => $employee_id
													));

													$number_of_rows = $query_phone->rowCount();

													if ($number_of_rows > 0) {
														$employee_info = $query_phone->fetch();
														$employee_phone = $employee_info['ei_data'];
													} else {
														$employee_phone = "";
													}

												?>

													<tr>
														<td class="text-center">
															<a href="<?php getSiteUrl(); ?>idkadmin/employees?page=open&id=<?php echo $employee_id; ?>">
																<img class="idk_profile_img" src="<?php getSiteUrl(); ?>idkadmin/files/employees/images/<?php echo $employee_image; ?>">
															</a>
														</td>
														<td>
															<a href="<?php getSiteUrl(); ?>idkadmin/employees?page=open&id=<?php echo $employee_id; ?>">
																<?php echo "${employee_first_name} ${employee_last_name}"; ?>
															</a>
															<?php
															if ($employee_status == "Komercijalista" and isset($employee_commercialist_type)) {
																if ($employee_commercialist_type == 1) {
																	echo " (HoReCa)";
																} elseif ($employee_commercialist_type == 2) {
																	echo " (Retail)";
																}
															}
															?>
														</td>
														<td>
															<a href="tel:<?php echo $employee_phone; ?>">
																<?php echo $employee_phone; ?>
															</a>
														</td>
														<td>
															<a href="mailto:<?php echo $employee_login_email; ?>">
																<?php echo $employee_login_email; ?>
															</a>
														</td>
														<td>
															<?php echo $employee_status; ?>
														</td>
														<td class="text-center">
															<div class="btn-group material-btn-group">
																<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown">
																	<i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span>
																</button>
																<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
																	<li>
																		<a href="<?php getSiteUrl(); ?>idkadmin/employees?page=open&id=<?php echo $employee_id; ?>" class="material-dropdown-menu__link">
																			<i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori
																		</a>
																	</li>
																	<li>
																		<a href="<?php getSiteUrl(); ?>idkadmin/employees?page=edit&id=<?php echo $employee_id; ?>" class="material-dropdown-menu__link">
																			<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi
																		</a>
																	</li>
																	<li class="idk_dropdown_danger">
																		<a href="#" data="<?php getSiteUrl(); ?>idkadmin/employees?page=archive&id=<?php echo $employee_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link">
																			<i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj
																		</a>
																	</li>
																</ul>
															</div>
														</td>
													</tr>

												<?php } ?>

												<!-- Archiving -->
												<script>
													$(".archive").click(function() {
														var addressValue = $(this).attr("data");
														document.getElementById("archive_link").href = addressValue;
													});
												</script>
												<!-- Modal -->
												<div class="modal material-modal material-modal_danger fade" id="archiveModal">
													<div class="modal-dialog">
														<div class="modal-content material-modal__content">
															<div class="modal-header material-modal__header">
																<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																<h4 class="modal-title material-modal__title">Arhiviranje</h4>
															</div>
															<div class="modal-body material-modal__body">
																<p>Jeste li sigurni da želite arhivirati zaposlenika?</p>
															</div>
															<div class="modal-footer material-modal__footer">
																<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
																<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
															</div>
														</div>
													</div>
												</div>
											</tbody>
										</table>
										<!-- End employees table -->

									</div>
								</div>
							</div>
						</div>
					</div>
					<?php

					break;



					/************************************************************
					 * 							ADD NEW EMPLOYEE
					 * *********************************************************/
				case "add":

					if ($getEmployeeStatus == 1) {
					?>

						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Dodaj novog zaposlenika</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/employees?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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

											<!-- Form - add employee -->
											<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_employee" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

												<div class="form-group">
													<label for="employee_first_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="employee_first_name" id="employee_first_name" placeholder="Ime" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="employee_last_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="employee_last_name" id="employee_last_name" placeholder="Prezime" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="employee_login_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Login email:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="email" name="employee_login_email" id="employee_login_email" placeholder="Login email" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="employee_password" class="col-sm-3 control-label"><span class="text-danger">*</span> Lozinka:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="password" name="employee_password" id="employee_password" placeholder="Lozinka" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="employee_status" class="col-sm-3 control-label"><span class="text-danger">*</span> Status:</label>
													<div class="col-sm-9">

														<select class="selectpicker" id="employee_status" name="employee_status" data-live-search="true" required>
															<option value=""></option>
															<?php
															$select_query = $db->prepare("
																SELECT od_data, od_id
																FROM idk_employee_otherdata
																WHERE od_group = :od_group
																ORDER BY od_data");

															$select_query->execute(array(
																':od_group' => 2
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['od_id'] . "' data-tokens='" . $select_row['od_data'] . "'>" . $select_row['od_data'] . "</option>";
															}
															?>
														</select>

													</div>
												</div>

												<div class="form-group">
													<label for="employee_position" class="col-sm-3 control-label"><span class="text-danger">*</span> Pozicija:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="employee_position" name="employee_position" data-live-search="true" required>
															<option value=""></option>
															<?php
															$select_query = $db->prepare("
																SELECT od_data, od_id
																FROM idk_employee_otherdata
																WHERE od_group = :od_group
																ORDER BY od_data");

															$select_query->execute(array(
																':od_group' => 1
															));

															while ($select_row = $select_query->fetch()) {
																echo "<option value='" . $select_row['od_id'] . "' data-tokens='" . $select_row['od_data'] . "'>" . $select_row['od_data'] . "</option>";
															}
															?>
														</select>
													</div>
												</div>

												<div class="form-group">
													<label for="employee_jmbg" class="col-sm-3 control-label">JMBG:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="number" name="employee_jmbg" id="employee_jmbg" placeholder="JMBG">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="employee_color" class="col-sm-3 control-label">Boja:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="color" name="employee_color" id="employee_color" value="#4092d9" />
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="employee_rfid" class="col-sm-3 control-label">RFID:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="employee_rfid" id="employee_rfid" placeholder="RFID">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="employee_dob" class="col-sm-3 control-label">Datum rođenja:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="employee_dob" id="employee_dob" placeholder="Datum rođenja">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$("#employee_dob").flatpickr({
															dateFormat: "d.m.Y.",
															"locale": "bs"
														});
													</script>
												</div>

												<div class="form-group">
													<label for="employee_doe" class="col-sm-3 control-label">Datum zaposlenja:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="employee_doe" id="employee_doe" placeholder="Datum zaposlenja">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
													<script>
														$("#employee_doe").flatpickr({
															dateFormat: "d.m.Y.",
															"locale": "bs"
														});
													</script>
												</div>

												<div class="form-group">
													<label for="employee_address" class="col-sm-3 control-label">Adresa: </label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="employee_address" id="employee_address" placeholder="Adresa">
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="employee_city" class="col-sm-3 control-label">Općina:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="employee_city" name="employee_city" data-live-search="true">
															<option value="">Odaberi općinu</option>
															<?php
															$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
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
													<label for="employee_region" class="col-sm-3 control-label">Regija:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="employee_region" name="employee_region" data-live-search="true">
															<option value="">Odaberi regiju</option>
															<?php
															$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
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
													<label for="employee_country" class="col-sm-3 control-label">Država:</label>
													<div class="col-sm-9">
														<select class="selectpicker" id="employee_country" name="employee_country" data-live-search="true">
															<option value="">Odaberi državu</option>
															<?php
															$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
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
													<label for="employee_other_info" class="col-sm-3 control-label">Ostale informacije:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<textarea class="form-control materail-input material-textarea" name="employee_other_info" placeholder="Ostale informacije" rows="6" id="employee_other_info"></textarea>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<!-- Add image -->
												<div class="form-group">
													<label for="employee_image" class="col-sm-3 control-label">Fotografija:</label>
													<div class="col-sm-9">
														<div class="fileinput fileinput-new" data-provides="fileinput">
															<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
															<div>
																<span class="btn btn-default btn-file">
																	<span class="fileinput-new">Izaberi fotografiju</span>
																	<span class="fileinput-exists">Promijeni</span>
																	<input type="file" name="employee_image" id="employee_image">
																</span>
																<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
																<script>
																	$(function() {
																		$('#employee_image').change(function() {

																			var ext = $('#employee_image').val().split('.').pop().toLowerCase();

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
											<!-- End form - add employee -->

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
					 * 							EDIT EMPLOYEE
					 * *********************************************************/
				case "edit":

					$employee_id = $_GET['id'];

					//Check if employee exists
					$check_query = $db->prepare("
						SELECT employee_id
						FROM idk_employee
						WHERE employee_id = :employee_id");

					$check_query->execute(array(
						':employee_id' => $employee_id
					));

					$number_of_rows = $check_query->rowCount();

					if ($number_of_rows == 1) {

						if ($getEmployeeStatus == 1) {

							$query = $db->prepare("
								SELECT employee_first_name, employee_last_name, employee_login_email, employee_position, employee_status, employee_image, employee_jmbg, employee_rfid, employee_color, employee_address, employee_city, employee_region, employee_country, employee_other_info, employee_dob, employee_doe, employee_commercialist_type
								FROM idk_employee
								WHERE employee_id = :employee_id");

							$query->execute(array(
								':employee_id' => $employee_id
							));

							$employee = $query->fetch();

							$employee_first_name = $employee['employee_first_name'];
							$employee_last_name = $employee['employee_last_name'];
							$employee_jmbg = $employee['employee_jmbg'];
							$employee_position = $employee['employee_position'];
							$employee_dob = $employee['employee_dob'];
							$employee_doe = $employee['employee_doe'];
							$employee_login_email = $employee['employee_login_email'];
							$employee_color = $employee['employee_color'];
							$employee_rfid = $employee['employee_rfid'];
							$employee_address = $employee['employee_address'];
							$employee_city = $employee['employee_city'];
							$employee_region = $employee['employee_region'];
							$employee_country = $employee['employee_country'];
							$employee_other_info = $employee['employee_other_info'];
							$employee_status = $employee['employee_status'];
							$employee_image = $employee['employee_image'];
							$employee_commercialist_type = $employee['employee_commercialist_type'];

						?>

							<div class="row">
								<div class="col-xs-8">
									<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Uredi profil zaposlenika</h1>
								</div>
								<div class="col-xs-4 text-right idk_margin_top10">
									<a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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

												<!-- Form - edit employee -->
												<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=edit_employee" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

													<input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>" />

													<div class="form-group">
														<label for="employee_first_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="employee_first_name" id="employee_first_name" value="<?php echo $employee_first_name; ?>" placeholder="Ime" required>
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="employee_last_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="employee_last_name" id="employee_last_name" value="<?php echo $employee_last_name; ?>" placeholder="Prezime" required>
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="employee_login_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Login email:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="email" name="employee_login_email" id="employee_login_email" value="<?php echo $employee_login_email; ?>" placeholder="Login email" required>
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="employee_password" class="col-sm-3 control-label">Lozinka:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="password" name="employee_password" id="employee_password" placeholder="Lozinka">
																<span class="materail-input-block__line"></span>
															</div>
															<small>Ukoliko želite promijeniti lozinku, unesite novu.</small>
														</div>
													</div>

													<div class="form-group">
														<label for="employee_status" class="col-sm-3 control-label"><span class="text-danger">*</span> Status:</label>
														<div class="col-sm-9">

															<select class="selectpicker" id="employee_status" name="employee_status" data-live-search="true" required>
																<option value=""></option>
																<?php
																$select_query = $db->prepare("
																	SELECT od_data, od_id
																	FROM idk_employee_otherdata
																	WHERE od_group = :od_group
																	ORDER BY od_data");

																$select_query->execute(array(
																	':od_group' => 2
																));

																while ($select_row = $select_query->fetch()) {
																	echo "<option value='" . $select_row['od_id'] . "'";
																	if ($employee_status == $select_row['od_id']) echo " selected";
																	else echo "";
																	echo " data-tokens='" . $select_row['od_data'] . "'>" . $select_row['od_data'] . "</option>";
																}
																?>
															</select>

														</div>
													</div>

													<div class="form-group">
														<label for="employee_position" class="col-sm-3 control-label"><span class="text-danger">*</span> Pozicija:</label>
														<div class="col-sm-9">
															<select class="selectpicker" id="employee_position" name="employee_position" data-live-search="true" required>
																<option value=""></option>
																<?php
																$select_query = $db->prepare("
																	SELECT od_data, od_id
																	FROM idk_employee_otherdata
																	WHERE od_group = :od_group
																	ORDER BY od_data");

																$select_query->execute(array(
																	':od_group' => 1
																));

																while ($select_row = $select_query->fetch()) {
																	if ($select_row['od_id'] == $employee_position) {
																		$is_selected = " selected";
																	} else {
																		$is_selected = "";
																	}
																	echo "<option value='" . $select_row['od_id'] . "'" . $is_selected . " data-tokens='" . $select_row['od_data'] . "'>" . $select_row['od_data'] . "</option>";
																}
																?>
															</select>
														</div>
													</div>

													<div class="form-group">
														<label for="employee_jmbg" class="col-sm-3 control-label">JMBG:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="number" name="employee_jmbg" id="employee_jmbg" value="<?php echo $employee_jmbg; ?>" placeholder="JMBG">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="employee_color" class="col-sm-3 control-label">Boja:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="color" name="employee_color" id="employee_color" value="<?php echo $employee_color; ?>" />
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="employee_rfid" class="col-sm-3 control-label">RFID:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="employee_rfid" id="employee_rfid" value="<?php echo $employee_rfid; ?>" placeholder="RFID">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="employee_dob" class="col-sm-3 control-label">Datum rođenja:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="employee_dob" id="employee_dob" value="<?php if (isset($employee_dob)) {
																																																																																																																										echo date('d.m.Y.', strtotime($employee_dob));
																																																																																																																									} ?>" placeholder="Datum rođenja">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
														<script>
															$("#employee_dob").flatpickr({
																dateFormat: "d.m.Y.",
																"locale": "bs"
															});
														</script>
													</div>

													<div class="form-group">
														<label for="employee_doe" class="col-sm-3 control-label">Datum zaposlenja:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="employee_doe" id="employee_doe" value="<?php if (isset($employee_doe)) {
																																																																																																																										echo date('d.m.Y.', strtotime($employee_doe));
																																																																																																																									} ?>" placeholder="Datum zaposlenja">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
														<script>
															$("#employee_doe").flatpickr({
																dateFormat: "d.m.Y.",
																"locale": "bs"
															});
														</script>
													</div>

													<div class="form-group">
														<label for="employee_address" class="col-sm-3 control-label">Adresa:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<input class="form-control materail-input" type="text" name="employee_address" id="employee_address" value="<?php echo $employee_address; ?>" placeholder="Adresa">
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<div class="form-group">
														<label for="employee_city" class="col-sm-3 control-label">Općina:</label>
														<div class="col-sm-9">
															<select class="selectpicker" id="employee_city" name="employee_city" data-live-search="true">
																<option value="">Odaberi općinu</option>
																<?php
																$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
																ORDER BY location_name");

																$select_query->execute(array(
																	':location_type' => 1
																));

																while ($select_row = $select_query->fetch()) {
																	echo "<option value='" . $select_row['location_name'] . "'";
																	if ($select_row['location_name'] == $employee_city) {
																		echo " selected";
																	}
																	echo " data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
																}
																?>
															</select>
														</div>
													</div>

													<div class="form-group">
														<label for="employee_region" class="col-sm-3 control-label">Regija:</label>
														<div class="col-sm-9">
															<select class="selectpicker" id="employee_region" name="employee_region" data-live-search="true">
																<option value="">Odaberi regiju</option>
																<?php
																$select_query = $db->prepare("
																	SELECT location_name
																	FROM idk_location
																	WHERE location_type = :location_type
																	ORDER BY location_name");

																$select_query->execute(array(
																	':location_type' => 2
																));

																while ($select_row = $select_query->fetch()) {
																	echo "<option value='" . $select_row['location_name'] . "'";
																	if ($select_row['location_name'] == $employee_region) {
																		echo " selected";
																	}
																	echo " data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
																}
																?>
															</select>
														</div>
													</div>

													<div class="form-group">
														<label for="employee_country" class="col-sm-3 control-label">Država:</label>
														<div class="col-sm-9">
															<select class="selectpicker" id="employee_country" name="employee_country" data-live-search="true">
																<option value="">Odaberi državu</option>
																<?php
																$select_query = $db->prepare("
																	SELECT location_name
																	FROM idk_location
																	WHERE location_type = :location_type
																	ORDER BY location_name");

																$select_query->execute(array(
																	':location_type' => 3
																));

																while ($select_row = $select_query->fetch()) {
																	echo "<option value='" . $select_row['location_name'] . "'";
																	if ($select_row['location_name'] == $employee_country) {
																		echo " selected";
																	}
																	echo " data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
																}
																?>
															</select>
														</div>
													</div>

													<div class="form-group">
														<label for="employee_other_info" class="col-sm-3 control-label">Ostale informacije:</label>
														<div class="col-sm-9">
															<div class="materail-input-block materail-input-block_success">
																<textarea class="form-control materail-input material-textarea" name="employee_other_info" placeholder="Ostale informacije" rows="6" id="employee_other_info"><?php echo $employee_other_info; ?></textarea>
																<span class="materail-input-block__line"></span>
															</div>
														</div>
													</div>

													<!-- Add image -->
													<div class="form-group">
														<label for="employee_image" class="col-sm-3 control-label">Fotografija:</label>
														<div class="col-sm-9">
															<div class="fileinput fileinput-new" data-provides="fileinput">
																<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
																	<img src="<?php getSiteUrl(); ?>idkadmin/files/employees/images/<?php echo $employee_image; ?>">
																</div>
																<input type="hidden" name="employee_image_url" value="<?php echo $employee_image; ?>" />
																<div>
																	<span class="btn btn-default btn-file">
																		<span class="fileinput-new">Izaberi fotografiju</span>
																		<span class="fileinput-exists">Promijeni</span>
																		<input type="file" name="employee_image" id="employee_image">
																	</span>
																	<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
																	<script>
																		$(function() {
																			$('#employee_image').change(function() {

																				var ext = $('#employee_image').val().split('.').pop().toLowerCase();

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
												<!-- End form - edit employee -->

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
						header("Location: employees?page=list&mess=5");
					}

					break;



					/************************************************************
					 * 							EDIT PROFILE 
					 * *********************************************************/
				case "edit_profile":

					$query = $db->prepare("
						SELECT employee_first_name, employee_last_name, employee_login_email, employee_image, employee_jmbg, employee_rfid, employee_color, employee_address, employee_city, employee_region, employee_country, employee_dob, employee_doe
						FROM idk_employee
						WHERE employee_id = :employee_id");

					$query->execute(array(
						':employee_id' => $logged_employee_id
					));

					$employee = $query->fetch();

					$employee_first_name = $employee['employee_first_name'];
					$employee_last_name = $employee['employee_last_name'];
					$employee_jmbg = $employee['employee_jmbg'];
					$employee_dob = $employee['employee_dob'];
					$employee_doe = $employee['employee_doe'];
					$employee_login_email = $employee['employee_login_email'];
					$employee_color = $employee['employee_color'];
					$employee_rfid = $employee['employee_rfid'];
					$employee_address = $employee['employee_address'];
					$employee_city = $employee['employee_city'];
					$employee_region = $employee['employee_region'];
					$employee_country = $employee['employee_country'];
					$employee_image = $employee['employee_image'];

					?>

					<div class="row">
						<div class="col-xs-12">
							<h1><i class="fa fa-user idk_color_green" aria-hidden="true"></i> Uredi lični profil</h1>
						</div>
						<div class="col-xs-12">
							<hr>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="content_box">
								<div class="row">
									<?php
									if (isset($_GET['mess'])) {
										$mess = $_GET['mess'];
									} else {
										$mess = 0;
									}

									if ($mess == 1) {
										echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili vaš profil.</div>';
									}
									?>
									<div class="col-md-offset-1 col-md-8">

										<!-- Form - edit profile -->
										<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=edit_profile" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

											<input type="hidden" name="employee_id" value="<?php echo $logged_employee_id; ?>" />

											<div class="form-group">
												<label for="employee_first_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Ime:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="employee_first_name" id="employee_first_name" value="<?php echo $employee_first_name; ?>" placeholder="Ime" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>

											<div class="form-group">
												<label for="employee_last_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Prezime:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="employee_last_name" id="employee_last_name" value="<?php echo $employee_last_name; ?>" placeholder="Prezime" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>

											<div class="form-group">
												<label for="employee_login_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Login email:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="email" name="employee_login_email" id="employee_login_email" value="<?php echo $employee_login_email; ?>" placeholder="Login email" required>
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>

											<div class="form-group">
												<label for="employee_password" class="col-sm-3 control-label"> Lozinka:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="password" name="employee_password" id="employee_password" placeholder="Lozinka">
														<span class="materail-input-block__line"></span>
													</div>
													<small>Ukoliko želite promijeniti lozinku, unesite novu.</small>
												</div>
											</div>

											<div class="form-group">
												<label for="employee_jmbg" class="col-sm-3 control-label">JMBG:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="number" name="employee_jmbg" id="employee_jmbg" value="<?php echo $employee_jmbg; ?>" placeholder="JMBG">
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>

											<div class="form-group">
												<label for="employee_color" class="col-sm-3 control-label">Boja:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="color" name="employee_color" id="employee_color" value="<?php echo $employee_color; ?>" />
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>

											<div class="form-group">
												<label for="employee_rfid" class="col-sm-3 control-label">RFID:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="employee_rfid" id="employee_rfid" value="<?php echo $employee_rfid; ?>" placeholder="RFID">
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>

											<div class="form-group">
												<label for="employee_dob" class="col-sm-3 control-label">Datum rođenja:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="employee_dob" id="employee_dob" value="<?php if (isset($employee_dob)) {
																																																																																																																								echo date('d.m.Y.', strtotime($employee_dob));
																																																																																																																							} ?>" placeholder="Datum rođenja">
														<span class="materail-input-block__line"></span>
													</div>
												</div>
												<script>
													$("#employee_dob").flatpickr({
														dateFormat: "d.m.Y.",
														"locale": "bs"
													});
												</script>
											</div>

											<div class="form-group">
												<label for="employee_doe" class="col-sm-3 control-label">Datum zaposlenja:</label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" data-init-set="false" data-large-default="true" data-format="d.m.Y." data-large-mode="true" data-modal="true" type="text" name="employee_doe" id="employee_doe" value="<?php if (isset($employee_doe)) {
																																																																																																																								echo date('d.m.Y.', strtotime($employee_doe));
																																																																																																																							} ?>" placeholder="Datum zaposlenja">
														<span class="materail-input-block__line"></span>
													</div>
												</div>
												<script>
													$("#employee_doe").flatpickr({
														dateFormat: "d.m.Y.",
														"locale": "bs"
													});
												</script>
											</div>

											<div class="form-group">
												<label for="employee_address" class="col-sm-3 control-label">Adresa: </label>
												<div class="col-sm-9">
													<div class="materail-input-block materail-input-block_success">
														<input class="form-control materail-input" type="text" name="employee_address" id="employee_address" value="<?php echo $employee_address; ?>" placeholder="Adresa">
														<span class="materail-input-block__line"></span>
													</div>
												</div>
											</div>

											<div class="form-group">
												<label for="employee_city" class="col-sm-3 control-label">Općina:</label>
												<div class="col-sm-9">
													<select class="selectpicker" id="employee_city" name="employee_city" data-live-search="true">
														<option value="">Odaberi općinu</option>
														<?php
														$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
																ORDER BY location_name");

														$select_query->execute(array(
															':location_type' => 1
														));

														while ($select_row = $select_query->fetch()) {
															echo "<option value='" . $select_row['location_name'] . "'";
															if ($select_row['location_name'] == $employee_city) {
																echo " selected";
															}
															echo " data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
														}
														?>
													</select>
												</div>
											</div>

											<div class="form-group">
												<label for="employee_region" class="col-sm-3 control-label">Regija:</label>
												<div class="col-sm-9">
													<select class="selectpicker" id="employee_region" name="employee_region" data-live-search="true">
														<option value="">Odaberi regiju</option>
														<?php
														$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
																ORDER BY location_name");

														$select_query->execute(array(
															':location_type' => 2
														));

														while ($select_row = $select_query->fetch()) {
															echo "<option value='" . $select_row['location_name'] . "'";
															if ($select_row['location_name'] == $employee_region) {
																echo " selected";
															}
															echo " data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
														}
														?>
													</select>
												</div>
											</div>

											<div class="form-group">
												<label for="employee_country" class="col-sm-3 control-label">Država:</label>
												<div class="col-sm-9">
													<select class="selectpicker" id="employee_country" name="employee_country" data-live-search="true">
														<option value="">Odaberi državu</option>
														<?php
														$select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
																WHERE location_type = :location_type
																ORDER BY location_name");

														$select_query->execute(array(
															':location_type' => 3
														));

														while ($select_row = $select_query->fetch()) {
															echo "<option value='" . $select_row['location_name'] . "'";
															if ($select_row['location_name'] == $employee_country) {
																echo " selected";
															}
															echo " data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
														}
														?>
													</select>
												</div>
											</div>

											<!-- Add image -->
											<div class="form-group">
												<label for="employee_image" class="col-sm-3 control-label">Fotografija:</label>
												<div class="col-sm-9">
													<div class="fileinput fileinput-new" data-provides="fileinput">
														<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
															<img src="<?php getSiteUrl(); ?>idkadmin/files/employees/images/<?php echo $employee_image; ?>">
														</div>
														<input type="hidden" name="employee_image_url" value="<?php echo $employee_image; ?>" />
														<div>
															<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi
																	fotografiju</span><span class="fileinput-exists">Promijeni</span><input type="file" name="employee_image" id="employee_image"></span>
															<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
															<script>
																$(function() {
																	$('#employee_image').change(function() {

																		var ext = $('#employee_image').val().split('.').pop().toLowerCase();

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
										<!-- End form - edit profile -->
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php

					break;



					/************************************************************
					 * 							OPEN PROFILE 
					 * *********************************************************/
				case "open":

					$employee_id = $_GET['id'];

					//Check if employee exists
					$check_query = $db->prepare("
						SELECT employee_id
						FROM idk_employee
						WHERE employee_id = :employee_id");

					$check_query->execute(array(
						':employee_id' => $employee_id
					));

					$number_of_rows = $check_query->rowCount();

					if ($number_of_rows == 1) {

						$query = $db->prepare("
							SELECT employee_first_name, employee_last_name, employee_login_email, employee_position, employee_dob, employee_doe, employee_address, employee_city, employee_region, employee_country, employee_status, employee_image, employee_other_info, employee_important_note, employee_commercialist_type
							FROM idk_employee
							WHERE employee_id = :employee_id");

						$query->execute(array(
							':employee_id' => $employee_id
						));

						$employee = $query->fetch();

						$employee_first_name = $employee['employee_first_name'];
						$employee_last_name = $employee['employee_last_name'];
						$employee_position = $employee['employee_position'];
						$employee_dob = date('d.m.Y.', strtotime($employee['employee_dob']));
						$employee_doe = date('d.m.Y.', strtotime($employee['employee_doe']));
						$employee_login_email = $employee['employee_login_email'];
						$employee_address = $employee['employee_address'];
						$employee_city = $employee['employee_city'];
						$employee_region = $employee['employee_region'];
						$employee_country = $employee['employee_country'];
						$employee_other_info = $employee['employee_other_info'];
						$employee_status = $employee['employee_status'];
						$employee_important_note = $employee['employee_important_note'];
						$employee_image = $employee['employee_image'];
						$employee_commercialist_type = $employee['employee_commercialist_type'];

					?>
						<div class="row">
							<div class="col-xs-8">
								<h1>
									<a class="fancybox" rel="group" href="<?php getSiteUrl(); ?>idkadmin/files/employees/images/<?php echo $employee_image; ?>">
										<img class="idk_profile_img" src="<?php getSiteUrl(); ?>idkadmin/files/employees/images/<?php echo $employee_image; ?>">
									</a>
									<?php echo "${employee_first_name} ${employee_last_name}"; ?>
								</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/employees?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>
						<div class="row">
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
												<div class="alert material-alert material-alert_success">Uspješno ste obrisali kontakt informaciju.
												</div>
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
										</ul>
										<div class="tab-content materail-tabs-content">
											<div class="tab-pane fade active in" id="info">
												<div class="row idk_employee_info">

													<div class="col-md-6">
														<div class="row">
															<div class="col-sm-9">
																<h5>Osnovne informacije</h5>
															</div>
															<div class="col-sm-3 text-right">
																<a href="employees?page=edit&id=<?php echo $employee_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
																	<i class="fa fa-pencil" aria-hidden="true"></i> <span></span>
																</a>
															</div>
														</div>

														<!-- Get basic information -->
														<div class="row">
															<strong class="col-sm-4 text-right">Ime:</strong>
															<div class="col-sm-8"><?php echo $employee_first_name; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Prezime:</strong>
															<div class="col-sm-8"><?php echo $employee_last_name; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Pozicija:</strong>
															<div class="col-sm-8">
																<?php
																$select_query = $db->prepare("
																	SELECT od_data, od_id
																	FROM idk_employee_otherdata
																	WHERE od_group = :od_group");

																$select_query->execute(array(
																	':od_group' => 1
																));

																while ($select_row = $select_query->fetch()) {
																	if ($select_row['od_id'] == $employee_position) {
																		echo $select_row['od_data'];
																	}
																}
																?>
															</div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Login Email:</strong>
															<div class="col-sm-8"><?php echo $employee_login_email; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Datum rođenja:</strong>
															<div class="col-sm-8">
																<?php if (!is_null($employee['employee_dob'])) {
																	echo $employee_dob;
																} ?>
															</div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Datum zaposlenja:</strong>
															<div class="col-sm-8">
																<?php if (!is_null($employee['employee_doe'])) {
																	echo $employee_doe;
																} ?>
															</div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Adresa:</strong>
															<div class="col-sm-8"><?php echo $employee_address; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Općina:</strong>
															<div class="col-sm-8"><?php echo $employee_city; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Regija:</strong>
															<div class="col-sm-8"><?php echo $employee_region; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Država:</strong>
															<div class="col-sm-8"><?php echo $employee_country; ?></div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Status:</strong>
															<div class="col-sm-8">
																<?php
																$select_query = $db->prepare("
																SELECT od_data, od_id
																FROM idk_employee_otherdata
																	WHERE od_group = :od_group");

																$select_query->execute(array(
																	':od_group' => 2
																));

																while ($select_row = $select_query->fetch()) {
																	if ($select_row['od_id'] == $employee_status) {
																		echo $select_row['od_data'];
																	}
																}
																?>
															</div>
														</div>
														<div class="row">
															<strong class="col-sm-4 text-right">Ostale informacije:</strong>
															<div class="col-sm-8"><?php echo $employee_other_info; ?></div>
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

																				<!-- Form - add employee phone -->
																				<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_employee_phone" method="post" role="form" class="form-horizontal">

																					<input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>" />

																					<div class="form-group">
																						<label for="ei_title_phone" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																						<div class="col-sm-9">
																							<div class="materail-input-block materail-input-block_success">
																								<input class="form-control materail-input" type="text" name="ei_title" id="ei_title_phone" placeholder="Telefon" required>
																								<span class="materail-input-block__line"></span>
																							</div>
																						</div>
																					</div>

																					<div class="form-group">
																						<label for="ei_data_phone" class="col-sm-3 control-label"><span class="text-danger">*</span> Broj telefona:</label>
																						<div class="col-sm-9">
																							<div class="materail-input-block materail-input-block_success">
																								<input class="form-control materail-input" type="text" name="ei_data" id="ei_data_phone" placeholder="003876XXXXXXXX" required>
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
																				<!-- End form - add employee phone -->

																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

														<!-- Get phone from idk_employee_info -->
														<div class="table-responsive">
															<table class="table table-striped">
																<tbody>
																	<?php
																	$query_phones = $db->prepare("
																		SELECT ei_id, ei_title, ei_data, ei_primary
																		FROM idk_employee_info
																		WHERE ei_group = :ei_group AND employee_id = :employee_id
																		ORDER BY ei_primary DESC");

																	$query_phones->execute(array(
																		':ei_group' => 1,
																		':employee_id' => $employee_id
																	));

																	while ($employee_phones = $query_phones->fetch()) {

																		$ei_id = $employee_phones['ei_id'];
																		$ei_title = $employee_phones['ei_title'];
																		$ei_data = $employee_phones['ei_data'];
																		$ei_primary = $employee_phones['ei_primary'];
																	?>

																		<tr>
																			<td>
																				<?php echo $ei_title; ?>:
																			</td>
																			<td>
																				<a href="tel:<?php echo $ei_data; ?>">
																					<?php echo $ei_data; ?>
																				</a>
																			</td>
																			<td class="text-right">
																				<ul class="list-inline">
																					<?php if ($ei_primary == 1) {
																						echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>';
																					} else {
																						echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteUrlr() . 'idkadmin/do.php?form=set_primary_phone_employee&ei_id=' . $ei_id . '&employee_id=' . $employee_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>';
																					} ?>
																					<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteUrl(); ?>idkadmin/employees?page=del_employee_info&id=<?php echo $ei_id; ?>" data-toggle="modal" data-target="#delPhoneModal" class="delPhone"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
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

																				<!-- Form - add email to idk_employee_info -->
																				<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_employee_email" method="post" role="form" class="form-horizontal">

																					<input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>" />

																					<div class="form-group">
																						<label for="ei_title_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																						<div class="col-sm-9">
																							<div class="materail-input-block materail-input-block_success">
																								<input class="form-control materail-input" type="text" name="ei_title" id="ei_title_email" placeholder="Privatni" required>
																								<span class="materail-input-block__line"></span>
																							</div>
																						</div>
																					</div>

																					<div class="form-group">
																						<label for="ei_data_email" class="col-sm-3 control-label"><span class="text-danger">*</span> Email adresa:</label>
																						<div class="col-sm-9">
																							<div class="materail-input-block materail-input-block_success">
																								<input class="form-control materail-input" type="email" name="ei_data" id="ei_data_email" placeholder="info@primjer.com" required>
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
																				<!-- End form - add email to idk_employee_info -->

																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

														<!-- Get email from idk_employee_info -->
														<div class="table-responsive">
															<table class="table table-striped">
																<tbody>
																	<?php
																	$query_emails = $db->prepare("
																		SELECT ei_id, ei_title, ei_data, ei_primary
																		FROM idk_employee_info
																		WHERE ei_group = :ei_group AND employee_id = :employee_id
																		ORDER BY ei_primary DESC");

																	$query_emails->execute(array(
																		':ei_group' => 2,
																		':employee_id' => $employee_id
																	));

																	while ($employee_emails = $query_emails->fetch()) {

																		$ei_id = $employee_emails['ei_id'];
																		$ei_title = $employee_emails['ei_title'];
																		$ei_data = $employee_emails['ei_data'];
																		$ei_primary = $employee_emails['ei_primary'];
																	?>

																		<tr>
																			<td>
																				<?php echo $ei_title; ?>:
																			</td>
																			<td>
																				<a href="mailto:<?php echo $ei_data; ?>">
																					<?php echo $ei_data; ?>
																				</a>
																			</td>
																			<td class="text-right">
																				<ul class="list-inline">
																					<?php if ($ei_primary == 1) {
																						echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>';
																					} else {
																						echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteUrlr() . 'idkadmin/do.php?form=set_primary_email_employee&ei_id=' . $ei_id . '&employee_id=' . $employee_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>';
																					} ?>
																					<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteUrl(); ?>idkadmin/employees?page=del_employee_info&id=<?php echo $ei_id; ?>" data-toggle="modal" data-target="#delEmailModal" class="delEmail"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
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
																<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#otherModal">
																	<i class="fa fa-plus" aria-hidden="true"></i> <span></span>
																</a>
																<!-- Modal add other -->
																<div class="modal material-modal material-modal_primary fade text-left" id="otherModal">
																	<div class="modal-dialog ">
																		<div class="modal-content material-modal__content">
																			<div class="modal-header material-modal__header">
																				<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																				<h4 class="modal-title material-modal__title">Dodaj kontakt informaciju</h4>
																			</div>
																			<div class="modal-body material-modal__body">

																				<!-- Form - add employee other information -->
																				<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_employee_other" method="post" role="form" class="form-horizontal">

																					<input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>" />

																					<div class="form-group">
																						<label for="ei_title_otherinfo" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																						<div class="col-sm-9">
																							<div class="materail-input-block materail-input-block_success">
																								<input class="form-control materail-input" type="text" name="ei_title" id="ei_title_otherinfo" placeholder="Facebook" required>
																								<span class="materail-input-block__line"></span>
																							</div>
																						</div>
																					</div>

																					<div class="form-group">
																						<label for="ei_data_otherinfo" class="col-sm-3 control-label"><span class="text-danger">*</span> Link:</label>
																						<div class="col-sm-9">
																							<div class="materail-input-block materail-input-block_success">
																								<input class="form-control materail-input" type="text" name="ei_data" id="ei_data_otherinfo" placeholder="www.facebook.com/profil" required>
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
																				<!-- End form - add employee other information -->

																			</div>
																		</div>
																	</div>
																</div>
															</div>
														</div>

														<!-- Get other information from idk_employee_info -->
														<div class="table-responsive">
															<table class="table table-striped">
																<tbody>
																	<?php
																	$query_otherinfo = $db->prepare("
																		SELECT ei_id, ei_title, ei_data, ei_primary
																		FROM idk_employee_info
																		WHERE ei_group = :ei_group AND employee_id = :employee_id
																		ORDER BY ei_primary DESC");

																	$query_otherinfo->execute(array(
																		':ei_group' => 3,
																		':employee_id' => $employee_id
																	));

																	while ($employee_otherinfo = $query_otherinfo->fetch()) {

																		$ei_id = $employee_otherinfo['ei_id'];
																		$ei_title = $employee_otherinfo['ei_title'];
																		$ei_data = $employee_otherinfo['ei_data'];
																		$ei_primary = $employee_otherinfo['ei_primary'];
																	?>

																		<tr>
																			<td>
																				<?php echo $ei_title; ?>:
																			</td>
																			<td>
																				<a href="<?php echo $ei_data; ?>" target="_blank">
																					<?php echo $ei_data; ?>
																				</a>
																			</td>
																			<td class="text-right">
																				<ul class="list-inline">
																					<?php if ($ei_primary == 1) {
																						echo '<li data-toggle="tooltip" data-placement="top" title="Primarni"><i class="fa fa-star fa-lg text-success" aria-hidden="true"></i></li>';
																					} else {
																						echo '<li data-toggle="tooltip" data-placement="top" title="Postavi kao primarni"><a href="' . getSiteUrlr() . 'idkadmin/do.php?form=set_primary_other_employee&ei_id=' . $ei_id . '&employee_id=' . $employee_id  . '"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a></li>';
																					} ?>
																					<li data-toggle="tooltip" data-placement="top" title="Obriši"><a href="#" data="<?php getSiteUrl(); ?>idkadmin/employees?page=del_employee_info&id=<?php echo $ei_id; ?>" data-toggle="modal" data-target="#delOtherModal" class="delOther"><i class="fa fa-trash-o fa-lg text-danger" aria-hidden="true"></i></a></li>
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
													<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#noteModal">
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

																	<!-- Form - add employee note -->
																	<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_employee_note" method="post" role="form" class="form-horizontal">

																		<input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>" />

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
																	<!-- End form - add employee note -->

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
														WHERE employee_id = :employee_id AND note_group = :note_group
														GROUP BY YEAR (note_datetime)
														ORDER BY YEAR (note_datetime) DESC");

													$year_query->execute(array(
														':employee_id' => $employee_id,
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
																		SELECT t1.note_id, t1.note_datetime, t1.note_txt, t2.employee_first_name, t2.employee_last_name
																		FROM idk_note t1
																		INNER JOIN idk_employee t2 ON t1.employee_id = t2.employee_id
																		WHERE YEAR (t1.note_datetime) = :note_datetime_year AND t1.employee_id = :employee_id AND t1.note_group = :note_group
																		ORDER BY t1.note_datetime DESC");

																	$notes_query->execute(array(
																		':note_datetime_year' => $note_datetime_year,
																		':note_group' => 1,
																		':employee_id' => $employee_id
																	));

																	while ($notes_row = $notes_query->fetch()) {

																		$note_date = date('d.m.Y.', strtotime($notes_row['note_datetime']));
																		$note_time = date('H:i', strtotime($notes_row['note_datetime']));
																		$note_id = $notes_row['note_id'];
																		$note_txt = $notes_row['note_txt'];
																		$employee_first_name = $notes_row['employee_first_name'];
																		$employee_last_name = $notes_row['employee_last_name'];

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
																					<?php echo $employee_first_name; ?>
																					<?php echo $employee_last_name; ?>
																				</p>
																			</div>
																			<div class="col-sm-7">
																				<p><?php echo $note_txt; ?></p>
																			</div>
																			<div class="col-sm-2 text-right">
																				<a href="#" data="<?php getSiteUrl(); ?>idkadmin/employees?page=del_note&note_id=<?php echo $note_id; ?>" data-toggle="modal" data-target="#deleteNoteModal" class="delete_note btn material-btn material-btn_danger main-container__column"><i class="fa fa-trash-o fa-lg" aria-hidden="true"></i></a>

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
													<li><a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#docModal">
															<i class="fa fa-file-text-o" aria-hidden="true"></i> <span>Dodaj dokument</span>
														</a>
													</li>
													<!-- Modal add document -->
													<div class="modal material-modal material-modal_primary fade text-left" id="docModal">
														<div class="modal-dialog ">
															<div class="modal-content material-modal__content">
																<div class="modal-header material-modal__header">
																	<button class="close material-modal__close" data-dismiss="modal">&times;</button>
																	<h4 class="modal-title material-modal__title">Dodaj dokument</h4>
																</div>
																<div class="modal-body material-modal__body">

																	<!-- Form - add employee doc -->
																	<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_employee_doc" enctype="multipart/form-data" method="post" role="form" class="form-horizontal">
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

																		<input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>" />

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
																					<span class="btn btn-default btn-file">
																						<span class="fileinput-new">Izaberi dokument</span>
																						<span class="fileinput-exists">Promijeni</span>
																						<input type="file" name="doc_file" id="doc_file" required>
																					</span>
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
																		<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i>
																		</li>
																		<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
																		</li>
																	</ul>
																	</form>
																	<!-- End form - add employee doc -->

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
															FROM idk_document t1, idk_employee_document t2
															WHERE t1.doc_group = :doc_group AND t2.employee_id = :employee_id AND t2.doc_id = t1.doc_id");

														$query_doc->execute(array(
															':doc_group' => 1,
															':employee_id' => $employee_id
														));

														while ($employee_doc = $query_doc->fetch()) {

															$doc_id = $employee_doc['doc_id'];
															$doc_name = $employee_doc['doc_name'];
															$doc_desc = $employee_doc['doc_desc'];
															$doc_file = $employee_doc['doc_file'];
															$doc_datetime = date('d.m.Y.', strtotime($employee_doc['doc_datetime']));

															if ($employee_doc['doc_icon'] == "jpg") {
																$doc_icon = '<i class="fa fa-file-image-o fa-lg" aria-hidden="true"></i>';
															} elseif ($employee_doc['doc_icon'] == "pdf") {
																$doc_icon = '<i class="fa fa-file-pdf-o fa-lg" aria-hidden="true"></i>';
															} elseif ($employee_doc['doc_icon'] == "doc" or $employee_doc['doc_icon'] == "docx") {
																$doc_icon = '<i class="fa fa-file-word-o fa-lg" aria-hidden="true"></i>';
															} elseif ($employee_doc['doc_icon'] == "xls" or $employee_doc['doc_icon'] == "xlsx") {
																$doc_icon = '<i class="fa fa-file-excel-o fa-lg" aria-hidden="true"></i>';
															} elseif ($employee_doc['doc_icon'] == "txt") {
																$doc_icon = '<i class="fa fa-file-text-o fa-lg" aria-hidden="true"></i>';
															} elseif ($employee_doc['doc_icon'] == "ppt" or $employee_doc['doc_icon'] == "pptx") {
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
																	<a href="files/employees/documents/<?php echo $doc_file; ?>" class="btn material-btn material-btn_success main-container__column" target="_BLANK">
																		<?php echo $doc_icon; ?>
																	</a>
																</td>
																<td class="text-center">
																	<a href="#" data="<?php getSiteUrl(); ?>idkadmin/employees?page=del_doc&doc_id=<?php echo $doc_id; ?>" data-toggle="modal" data-target="#deleteDocModal" class="delete_doc btn material-btn material-btn_danger main-container__column">
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
												<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=save_employee_important_note" method="post" role="form" class="form-horizontal">

													<input type="hidden" name="employee_id" value="<?php echo $employee_id; ?>" />

													<div class="form-group">
														<div class="col-md-offset-1 col-sm-10">
															<div class="form-group materail-input-block materail-input-block_success">
																<textarea id="inote" class="form-control materail-input material-textarea" name="employee_important_note" placeholder="Važne bilješke" rows="8"><?php echo base64_decode($employee_important_note); ?></textarea>
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
										</div>
									</div>
								</div>
							</div>
						</div>
			<?php
					} else {
						header("Location: employees?page=list&mess=6");
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
							SELECT t1.doc_name, t1.doc_file, t2.employee_id
							FROM idk_document t1, idk_employee_document t2
							WHERE t1.doc_id = :doc_id AND t2.doc_id = :doc_id");

						$doc_open_query->execute(array(
							':doc_id' => $doc_id
						));

						$doc_open = $doc_open_query->fetch();

						$doc_name = $doc_open['doc_name'];
						$doc_file = $doc_open['doc_file'];
						$employee_id = $doc_open['employee_id'];

						unlink("files/employees/documents/${doc_file}");

						//Add to log
						$log_desc = "Obrisao dokument: ${doc_name}";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						//Delete document from db - idk_document
						$doc_del_query = $db->prepare("
							DELETE FROM idk_document
							WHERE doc_id = :doc_id");

						$doc_del_query->execute(array(
							':doc_id' => $doc_id
						));

						//Delete document from idk_employee_document
						$doc_del_query = $db->prepare("
							DELETE FROM idk_employee_document
							WHERE doc_id = :doc_id");

						$doc_del_query->execute(array(
							':doc_id' => $doc_id
						));

						header("Location: " . getSiteUrlr() . "idkadmin/employees?page=open&id=${employee_id}&mess=3");
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

						//Get note_txt and employee_id
						$note_open_query = $db->prepare("
							SELECT note_txt, employee_id
							FROM idk_note
							WHERE note_id = :note_id");

						$note_open_query->execute(array(
							':note_id' => $note_id
						));

						$note_open = $note_open_query->fetch();

						$note_txt = $note_open['note_txt'];
						$employee_id = $note_open['employee_id'];

						//Add to log
						$log_desc = "Obrisao bilješku: ${note_txt}";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						//Delete note from db
						$note_del_query = $db->prepare("
							DELETE FROM idk_note
							WHERE note_id = :note_id");

						$note_del_query->execute(array(
							':note_id' => $note_id
						));

						header("Location: " . getSiteUrlr() . "idkadmin/employees?page=open&id=${employee_id}&mess=4");
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

						$employee_id = $_GET['id'];

						//Get
						$query_select = $db->prepare("
							SELECT employee_first_name, employee_last_name
							FROM idk_employee
							WHERE employee_id = :employee_id");

						$query_select->execute(array(
							':employee_id' => $employee_id
						));

						$employee_select = $query_select->fetch();

						$employee_first_name = $employee_select['employee_first_name'];
						$employee_last_name = $employee_select['employee_last_name'];

						//Save
						$query = $db->prepare("
							UPDATE idk_employee
							SET employee_active = :employee_active
							WHERE employee_id = :employee_id");

						$query->execute(array(
							':employee_active' => 0,
							':employee_id' => $employee_id
						));

						//Add to log
						$log_desc = "Arhivirao zaposlenika: ${employee_first_name} ${employee_last_name}";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						header("Location: " . getSiteUrlr() . "idkadmin/employees?page=list&mess=4");
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
					 * 							DELETE EMPLOYEE INFO
					 * *********************************************************/
				case "del_employee_info":

					if ($getEmployeeStatus == 1) {

						$ei_id = $_GET['id'];

						//Get ei_title, ei_data and employee_id
						$ei_open_query = $db->prepare("
							SELECT ei_title, ei_data, employee_id
							FROM idk_employee_info
							WHERE ei_id = :ei_id");

						$ei_open_query->execute(array(
							':ei_id' => $ei_id
						));

						$ei_open = $ei_open_query->fetch();

						$ei_title = $ei_open['ei_title'];
						$ei_data = $ei_open['ei_data'];
						$employee_id = $ei_open['employee_id'];

						//Add to log
						$log_desc = "Obrisao info: ${ei_title} - ${ei_data}";
						$log_date = date('Y-m-d H:i:s');
						addLog($logged_employee_id, $log_desc, $log_date);

						//Delete employee info from db
						$ei_del_query = $db->prepare("
							DELETE FROM idk_employee_info
							WHERE ei_id = :ei_id");

						$ei_del_query->execute(array(
							':ei_id' => $ei_id
						));

						header("Location: " . getSiteUrlr() . "idkadmin/employees?page=open&id=${employee_id}&mess=16");
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
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>

</html>