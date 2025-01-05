<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
} else {
	header("Location: settings?page=list");
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
 * 					LIST ALL SETTINGS OPTIONS
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
										<div class="col-xs-12">
											<!-- Success and error handling -->
											<?php
											if (isset($_GET['mess'])) {
												$mess = $_GET['mess'];
											} else {
												$mess = 0;
											}

											if ($mess == 1) {
												echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
											} elseif ($mess == 2) {
												echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu vrstu poreza.</div>';
											} elseif ($mess == 3) {
												echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili proizvod.</div>';
											} elseif ($mess == 4) {
												echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali proizvod.</div>';
											} elseif ($mess == 5) {
												echo '<div class="alert material-alert material-alert_danger">Greška: Proizvod koji pokušavate urediti ne postoji u bazi podataka.</div>';
											} elseif ($mess == 6) {
												echo '<div class="alert material-alert material-alert_danger">Greška: Proizvod ne postoji u bazi podataka.</div>';
											} elseif ($mess == 7) {
												echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
											}
											?>
										</div>
									</div>

									<div class="row">
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=identity" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Identitet</h6>
												</div>
											</a>
										</div>
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=tax" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Porez</h6>
												</div>
											</a>
										</div>
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=currency" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Valuta</h6>
												</div>
											</a>
										</div>
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=units" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Mjerna jedinica</h6>
												</div>
											</a>
										</div>
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=order_status" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Status narudžbe</h6>
												</div>
											</a>
										</div>
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=employee_positions" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Pozicija zaposlenika</h6>
												</div>
											</a>
										</div>
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=datacollection_type" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Vrsta informacije sa terena</h6>
												</div>
											</a>
										</div>
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=city" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Općina</h6>
												</div>
											</a>
										</div>
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=region" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Regija</h6>
												</div>
											</a>
										</div>
										<div class="col-sm-6 col-md-4 col-lg-3">
											<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=country" class="idk_setting_name_link">
												<div class="idk_setting_name_wrapper">
													<h6>Država</h6>
												</div>
											</a>
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
					 * 					TAX SETTINGS
					 * *********************************************************/
				case "tax":

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
							<div class="col-xs-12">
								<?php
								if (isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								} else {
									$mess = 0;
								}

								if ($mess == 1) {
									echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
								} elseif ($mess == 2) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novi porez.</div>';
								} elseif ($mess == 3) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali porez.</div>';
								} elseif ($mess == 4) {
									echo '<div class="alert material-alert material-alert_danger">Greška! Porez koji pokušavate dodati već postoji u bazi podataka.</div>';
								} elseif ($mess == 5) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste postavili zadani porez.</div>';
								}
								?>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-percent idk_color_green" aria-hidden="true"></i> Porez</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<h5>Dodaj novu vrstu poreza</h5>
									<div class="row">
										<div class="col-md-8 idk_setting_form_wrapper">

											<!-- Form - add tax -->
											<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_tax" method="post" class="form-horizontal" role="form">

												<div class="form-group">
													<label for="tax_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv poreza:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="tax_name" id="tax_name" placeholder="Naziv ..." required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="tax_value" class="col-sm-3 control-label"><span class="text-danger">*</span> Vrijednost poreza %:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="number" min="0" max="100" step="0.01" name="tax_value" id="tax_value" placeholder="10.00" required>
															<span class="materail-input-block__line"></span>
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
											<!-- End form - add tax -->

										</div>
									</div>
									<hr>

									<div class="row">
										<div class="col-xs-12">
											<h5>Trenutne vrste poreza</h5>

											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table').DataTable({

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

											<!-- Taxes table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th></th>
														<th>Naziv</th>
														<th>Vrijednost</th>
														<th>Obriši</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$query = $db->prepare("
														SELECT od_id, od_data, od_value, od_primary
														FROM idk_product_otherdata WHERE od_group = :od_group");

													$query->execute(array(
														':od_group' => 1
													));

													while ($row = $query->fetch()) {

														$od_id = $row['od_id'];
														$od_data = $row['od_data'];
														$od_value = $row['od_value'];
														$od_primary = $row['od_primary'];

													?>
														<tr>
															<td class="text-center">
																<?php if ($od_primary == 1) {
																	echo '<i class="fa fa-star fa-lg text-success" title="Zadano" aria-hidden="true"></i>';
																} else {
																	echo '<a href="' . getSiteUrlr() . 'idkadmin/do.php?form=set_primary_tax&tax_id=' . $od_id . '" title="Postavi kao zadano"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a>';
																} ?>
															</td>
															<td>
																<?php echo $od_data; ?>
															</td>
															<td>
																<?php echo $od_value; ?> %
															</td>
															<td class="text-center">
																<a href="#" data="<?php getSiteUrl(); ?>idkadmin/do.php?form=delete_tax&tax_id=<?php echo $od_id; ?>" data-toggle="modal" data-target="#modalDelete" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger">
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
																	<p>Jeste li sigurni da želite obrisati porez?</p>
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
					 * 					CURRENCY SETTINGS
					 * *********************************************************/
				case "currency":

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
							<div class="col-xs-12">
								<?php
								if (isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								} else {
									$mess = 0;
								}

								if ($mess == 1) {
									echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
								} elseif ($mess == 2) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu valutu.</div>';
								} elseif ($mess == 3) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali valutu.</div>';
								} elseif ($mess == 4) {
									echo '<div class="alert material-alert material-alert_danger">Greška! Valuta koju pokušavate dodati već postoji u bazi podataka.</div>';
								} elseif ($mess == 5) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste postavili zadanu valutu.</div>';
								}
								?>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-usd idk_color_green" aria-hidden="true"></i> Valuta</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<h5>Dodaj novu valutu</h5>
									<div class="row">
										<div class="col-md-8 idk_setting_form_wrapper">

											<!-- Form - add currency -->
											<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_currency" method="post" class="form-horizontal" role="form">

												<div class="form-group">
													<label for="currency_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv valute:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="currency_name" id="currency_name" placeholder="Konvertibilna marka" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="currency_code" class="col-sm-3 control-label"><span class="text-danger">*</span> Oznaka valute:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="currency_code" id="currency_code" placeholder="KM" required>
															<span class="materail-input-block__line"></span>
														</div>
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
											<!-- End form - add currency -->

										</div>
									</div>
									<hr>

									<div class="row">
										<div class="col-xs-12">
											<h5>Trenutne valute</h5>

											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table').DataTable({

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
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th></th>
														<th>Naziv</th>
														<th>Oznaka</th>
														<th>Obriši</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$query = $db->prepare("
														SELECT od_id, od_data, od_other_info, od_primary
														FROM idk_product_otherdata WHERE od_group = :od_group");

													$query->execute(array(
														':od_group' => 2
													));

													while ($row = $query->fetch()) {

														$od_id = $row['od_id'];
														$od_data = $row['od_data'];
														$od_other_info = $row['od_other_info'];
														$od_primary = $row['od_primary'];

													?>

														<tr>
															<td class="text-center">
																<?php if ($od_primary == 1) {
																	echo '<i class="fa fa-star fa-lg text-success" title="Zadano" aria-hidden="true"></i>';
																} else {
																	echo '<a href="' . getSiteUrlr() . 'idkadmin/do.php?form=set_primary_currency&curr_id=' . $od_id . '" title="Postavi kao zadano"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a>';
																} ?>
															</td>
															<td>
																<?php echo $od_data; ?>
															</td>
															<td>
																<?php echo $od_other_info; ?>
															</td>
															<td class="text-center">
																<a href="#" data="<?php getSiteUrl(); ?>idkadmin/do.php?form=delete_currency&curr_id=<?php echo $od_id; ?>" data-toggle="modal" data-target="#modalDelete" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger">
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
																	<p>Jeste li sigurni da želite obrisati valutu?</p>
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
					 * 					UNITS SETTINGS
					 * *********************************************************/
				case "units":

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
							<div class="col-xs-12">
								<?php
								if (isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								} else {
									$mess = 0;
								}

								if ($mess == 1) {
									echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
								} elseif ($mess == 2) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu mjernu jedinicu.</div>';
								} elseif ($mess == 3) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali mjernu jedinicu.</div>';
								} elseif ($mess == 4) {
									echo '<div class="alert material-alert material-alert_danger">Greška! Mjerna jedinica koju pokušavate dodati već postoji u bazi podataka.</div>';
								} elseif ($mess == 5) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste postavili zadanu mjernu jedinicu.</div>';
								}
								?>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-balance-scale idk_color_green" aria-hidden="true"></i> Mjerna jedinica</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<h5>Dodaj novu mjernu jedinicu</h5>
									<div class="row">
										<div class="col-md-8 idk_setting_form_wrapper">

											<!-- Form - add unit -->
											<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_unit" method="post" class="form-horizontal" role="form">

												<div class="form-group">
													<label for="unit_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv jedinice:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="unit_name" id="unit_name" placeholder="Komad" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="unit_code" class="col-sm-3 control-label"><span class="text-danger">*</span> Oznaka jedinice:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="unit_code" id="unit_code" placeholder="kom" required>
															<span class="materail-input-block__line"></span>
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
											<!-- End form - add unit -->

										</div>
									</div>
									<hr>

									<div class="row">
										<div class="col-xs-12">
											<h5>Trenutne mjerne jedinice</h5>

											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table').DataTable({

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

											<!-- Units table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th></th>
														<th>Naziv</th>
														<th>Oznaka</th>
														<th>Obriši</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$query = $db->prepare("
														SELECT od_id, od_data, od_other_info, od_primary
														FROM idk_product_otherdata WHERE od_group = :od_group");

													$query->execute(array(
														':od_group' => 5
													));

													while ($row = $query->fetch()) {

														$od_id = $row['od_id'];
														$od_data = $row['od_data'];
														$od_other_info = $row['od_other_info'];
														$od_primary = $row['od_primary'];

													?>
														<tr>
															<td class="text-center">
																<?php if ($od_primary == 1) {
																	echo '<i class="fa fa-star fa-lg text-success" title="Zadano" aria-hidden="true"></i>';
																} else {
																	echo '<a href="' . getSiteUrlr() . 'idkadmin/do.php?form=set_primary_unit&unit_id=' . $od_id . '" title="Postavi kao zadano"><i class="fa fa-star-o fa-lg text-success" aria-hidden="true"></i></a>';
																} ?>
															</td>
															<td>
																<?php echo $od_data; ?>
															</td>
															<td>
																<?php echo $od_other_info; ?>
															</td>
															<td class="text-center">
																<a href="#" data="<?php getSiteUrl(); ?>idkadmin/do.php?form=delete_unit&unit_id=<?php echo $od_id; ?>" data-toggle="modal" data-target="#modalDelete" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger">
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
																	<p>Jeste li sigurni da želite obrisati mjernu jedinicu?</p>
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
					 * 					EMPLOYEE POSITIONS SETTINGS
					 * *********************************************************/
				case "employee_positions":

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
							<div class="col-xs-12">
								<?php
								if (isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								} else {
									$mess = 0;
								}

								if ($mess == 1) {
									echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
								} elseif ($mess == 2) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu poziciju zaposlenika.</div>';
								} elseif ($mess == 3) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali poziciju zaposlenika.</div>';
								} elseif ($mess == 4) {
									echo '<div class="alert material-alert material-alert_danger">Greška! Pozicija zaposlenika koju pokušavate dodati već postoji u bazi podataka.</div>';
								} elseif ($mess == 5) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste postavili zadanu poziciju zaposlenika.</div>';
								}
								?>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-users idk_color_green" aria-hidden="true"></i> Pozicija zaposlenika</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<h5>Dodaj novu poziciju zaposlenika</h5>
									<div class="row">
										<div class="col-md-8 idk_setting_form_wrapper">

											<!-- Form - add employee position -->
											<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_employee_position" method="post" class="form-horizontal" role="form">

												<div class="form-group">
													<label for="employee_position_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv pozicije:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="employee_position_name" id="employee_position_name" placeholder="Pozicija" required>
															<span class="materail-input-block__line"></span>
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
											<!-- End form - add employee position -->

										</div>
									</div>
									<hr>

									<div class="row">
										<div class="col-xs-12">
											<h5>Trenutne pozicije zaposlenika</h5>

											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table').DataTable({

														"order": [
															[0, "asc"]
														],

														"bAutoWidth": false,

														"aoColumns": [{
																"width": "90%"
															},
															{
																"width": "10%",
																"bSortable": false
															}
														]
													});
												});
											</script>

											<!-- Employee positions table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>Naziv</th>
														<th>Obriši</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$query = $db->prepare("
														SELECT od_id, od_data
														FROM idk_employee_otherdata WHERE od_group = :od_group");

													$query->execute(array(
														':od_group' => 1
													));

													while ($row = $query->fetch()) {

														$od_id = $row['od_id'];
														$od_data = $row['od_data'];

													?>
														<tr>
															<td>
																<?php echo $od_data; ?>
															</td>
															<td class="text-center">
																<a href="#" data="<?php getSiteUrl(); ?>idkadmin/do.php?form=delete_employee_position&employee_position_id=<?php echo $od_id; ?>" data-toggle="modal" data-target="#modalDelete" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger">
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
																	<p>Jeste li sigurni da želite obrisati poziciju zaposlenika?</p>
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
					 * 					ORDERS SETTINGS
					 * *********************************************************/
				case "order_status":

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
							<div class="col-xs-12">
								<?php
								if (isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								} else {
									$mess = 0;
								}

								if ($mess == 1) {
									echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
								} elseif ($mess == 2) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novi status narudžbe.</div>';
								} elseif ($mess == 3) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali status narudžbe.</div>';
								} elseif ($mess == 4) {
									echo '<div class="alert material-alert material-alert_danger">Greška! Status narudžbe koji pokušavate dodati već postoji u bazi podataka.</div>';
								}
								?>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-shopping-cart idk_color_green" aria-hidden="true"></i> Status narudžbe</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<h5>Dodaj novi status</h5>
									<div class="row">
										<div class="col-md-8 idk_setting_form_wrapper">

											<!-- Form - add order status -->
											<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_order_status" method="post" class="form-horizontal" role="form">

												<div class="form-group">
													<label for="order_status_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv statusa:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="order_status_name" id="order_status_name" placeholder="Završeno" required>
															<span class="materail-input-block__line"></span>
														</div>
													</div>
												</div>

												<div class="form-group">
													<label for="order_status_color" class="col-sm-3 control-label"><span class="text-danger">*</span> Boja statusa:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="color" name="order_status_color" id="order_status_color" value="#4092d9" required />
															<span class="materail-input-block__line"></span>
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
											<!-- End form - add order status -->

										</div>
									</div>
									<hr>

									<div class="row">
										<div class="col-xs-12">
											<h5>Trenutni statusi</h5>

											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table').DataTable({

														"order": [
															[0, "asc"]
														],

														"bAutoWidth": false,

														"aoColumns": [{
																"width": "50%"
															},
															{
																"width": "50%"
															}
														]
													});
												});
											</script>

											<!-- Orders status table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>Naziv</th>
														<th>Boja</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$query = $db->prepare("
														SELECT od_id, od_data, od_value
														FROM idk_order_otherdata WHERE od_group = :od_group");

													$query->execute(array(
														':od_group' => 1
													));

													while ($row = $query->fetch()) {

														$od_id = $row['od_id'];
														$od_data = $row['od_data'];
														$od_value = $row['od_value'];

													?>

														<tr>
															<td>
																<?php echo $od_data; ?>
															</td>
															<td>
																<button class="btn material-btn" style="width: 100%; height: 20px; background: <?php echo $od_value; ?>; cursor: auto;"></button>
															</td>
															<!-- <td class="text-center">
																		<a href="#"
																			data="<?php getSiteUrl(); ?>idkadmin/do.php?form=delete_order_status&order_status_id=<?php echo $od_id; ?>"
																			data-toggle="modal" data-target="#modalDelete"
																			class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger"><i
																				class="fa fa-trash-o" aria-hidden="true"></i></a>
																	</td> -->
														</tr>
													<?php } ?>

													<!-- <script>
																$(".delete").click(function () {
																	var addressValue = $(this).attr("data");
																	document.getElementById("obrisi_link").href = addressValue;
																});
															</script> -->
													<!-- Modal delete-->
													<!-- <div class="modal material-modal material-modal_danger fade" id="modalDelete"
																	tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel"
																	aria-hidden="true">
																	<div class="modal-dialog">
																		<div class="modal-content material-modal__content">
																			<div class="modal-header material-modal__header">
																				<button class="close material-modal__close"
																					data-dismiss="modal">&times;</span><span
																						class="sr-only">Zatvori</span></button>
																				<h4 class="modal-title material-modal__title"
																					id="modalDeleteLabel">Brisanje</h4>
																			</div>
																			<div class="modal-body material-modal__body">
																				<p>Jeste li sigurni da želite obrisati status narudžbe?</p>
																			</div>
																			<div class="modal-footer material-modal__footer">
																				<button type="button" class="btn material-btn material-btn"
																					data-dismiss="modal">Zatvori</button>
																				<a id="obrisi_link" href=""><button type="button"
																						class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
																			</div>
																		</div>
																	</div>
																</div> -->

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
					 * 					IDENTITY SETTINGS
					 * *********************************************************/
				case "identity":

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
							<div class="col-xs-12">
								<?php
								if (isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								} else {
									$mess = 0;
								}

								if ($mess == 1) {
									echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
								} elseif ($mess == 2) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali identitet vlasnika.</div>';
								} elseif ($mess == 3) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili identitet.</div>';
								}
								?>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-id-card idk_color_green" aria-hidden="true"></i> Identitet vlasnika</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<div class="row">
										<div class="col-md-12">
											<div class="content_box">
												<div class="row">

													<?php

													//Check if owner exists
													$check_query = $db->prepare("
														SELECT owner_id
														FROM idk_owner
														WHERE owner_id = 1");

													$check_query->execute();
													$check_owner = $check_query->fetch();

													$number_of_rows = $check_query->rowCount();

													if ($number_of_rows == 1) {

														$query = $db->prepare("
															SELECT owner_id, owner_name, owner_business_type, owner_id_number, owner_pdv_number, owner_postal_code, owner_image, owner_color, owner_address, owner_city, owner_country, owner_other_info, owner_region
															FROM idk_owner
															LIMIT 1");

														$query->execute();

														$owner = $query->fetch();

														$owner_id = $owner['owner_id'];
														$owner_name = $owner['owner_name'];
														$owner_business_type = $owner['owner_business_type'];
														$owner_id_number = $owner['owner_id_number'];
														$owner_pdv_number = $owner['owner_pdv_number'];
														$owner_postal_code = $owner['owner_postal_code'];
														$owner_image = $owner['owner_image'];
														$owner_color = $owner['owner_color'];
														$owner_address = $owner['owner_address'];
														$owner_city = $owner['owner_city'];
														$owner_country = $owner['owner_country'];
														$owner_other_info = $owner['owner_other_info'];
														$owner_region = $owner['owner_region'];
														$owner_image = $owner['owner_image'];

														//Get primary phone and email from idk_owner_info
														$query_info = $db->prepare("
															SELECT ci_data
															FROM idk_owner_info
															WHERE ci_group = :ci_group AND ci_primary = :ci_primary AND owner_id = :owner_id");

													?>

														<div class="col-md-offset-1 col-md-8">

															<h5>Uredi identitet</h5>

															<!-- Form - edit identity -->
															<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=edit_owner" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

																<input type="hidden" name="owner_id" value="<?php echo $owner_id; ?>" />

																<div class="form-group">
																	<label for="owner_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="owner_name" id="owner_name" value="<?php echo $owner_name; ?>" placeholder="Naziv" required>
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_business_type" class="col-sm-3 control-label"> Vrsta poslovanja:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="owner_business_type" id="owner_business_type" value="<?php echo $owner_business_type; ?>" placeholder="d.o.o.">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_id_number" class="col-sm-3 control-label">ID broj:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="owner_id_number" id="owner_id_number" value="<?php echo $owner_id_number; ?>" placeholder="ID broj">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_pdv_number" class="col-sm-3 control-label">PDV broj:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="owner_pdv_number" id="owner_pdv_number" value="<?php echo $owner_pdv_number; ?>" placeholder="PDV broj">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_color" class="col-sm-3 control-label">Boja:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="color" name="owner_color" id="owner_color" value="<?php echo $owner_color; ?>" />
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_address" class="col-sm-3 control-label">Adresa:
																	</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="owner_address" id="owner_address" value="<?php echo $owner_address; ?>" placeholder="Adresa">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_postal_code" class="col-sm-3 control-label">Poštanski broj:
																	</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="owner_postal_code" id="owner_postal_code" value="<?php echo $owner_postal_code; ?>" placeholder="Poštanski broj">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_city" class="col-sm-3 control-label">Općina:</label>
																	<div class="col-sm-9">
																		<select class="selectpicker" id="owner_city" name="owner_city" data-live-search="true">
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
																				if ($select_row['location_name'] == $owner_city) {
																					echo " selected";
																				}
																				echo " data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
																			}
																			?>
																		</select>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_region" class="col-sm-3 control-label">Regija:</label>
																	<div class="col-sm-9">
																		<select class="selectpicker" id="owner_region" name="owner_region" data-live-search="true">
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
																				if ($select_row['location_name'] == $owner_region) {
																					echo " selected";
																				}
																				echo " data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
																			}
																			?>
																		</select>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_country" class="col-sm-3 control-label">Država:</label>
																	<div class="col-sm-9">
																		<select class="selectpicker" id="owner_country" name="owner_country" data-live-search="true">
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
																				if ($select_row['location_name'] == $owner_country) {
																					echo " selected";
																				}
																				echo " data-tokens='" . $select_row['location_name'] . "'>" . $select_row['location_name'] . "</option>";
																			}
																			?>
																		</select>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_other_info" class="col-sm-3 control-label">Ostale informacije:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<textarea class="form-control materail-input material-textarea" name="owner_other_info" placeholder="Ostale informacije" rows="6" id="owner_other_info"><?php echo $owner_other_info; ?></textarea>
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<!-- Add image -->
																<div class="form-group">
																	<label for="owner_image" class="col-sm-3 control-label">Logo:</label>
																	<div class="col-sm-9">
																		<div class="fileinput fileinput-new" data-provides="fileinput">
																			<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
																				<img src="<?php getSiteUrl(); ?>idkadmin/files/owners/images/<?php echo $owner_image; ?>">
																			</div>
																			<input type="hidden" name="owner_image_url" value="<?php echo $owner_image; ?>" />
																			<div>
																				<span class="btn btn-default btn-file">
																					<span class="fileinput-new">Izaberi logo</span>
																					<span class="fileinput-exists">Promijeni</span>
																					<input type="file" name="owner_image" id="owner_image">
																				</span>
																				<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
																				<script>
																					$(function() {
																						$('#owner_image').change(function() {

																							var ext = $('#owner_image').val().split('.').pop().toLowerCase();

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
																			<div class="alert material-alert material-alert_danger">
																				Greška:
																				Fotografija koju pokušavate
																				dodati je veća od dozvoljene veličine.</div>
																		</div>
																		<div id="idk_alert_ext" class="hidden">
																			<div class="alert material-alert material-alert_danger">
																				Greška: Format
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
															<!-- End form - edit identity -->

														</div>

													<?php } else { ?>

														<div class="col-md-offset-1 col-md-8">

															<h5>Dodaj novi identitet</h5>

															<!-- Form - add identity -->
															<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_owner" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

																<div class="form-group">
																	<label for="owner_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="owner_name" id="owner_name" placeholder="Naziv" required>
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_business_type" class="col-sm-3 control-label">Vrsta poslovanja:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="owner_business_type" id="owner_business_type" placeholder="d.o.o.">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_id_number" class="col-sm-3 control-label">ID broj:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="owner_id_number" id="owner_id_number" placeholder="ID broj">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_pdv_number" class="col-sm-3 control-label">PDV broj:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="owner_pdv_number" id="owner_pdv_number" placeholder="PDV broj">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_color" class="col-sm-3 control-label">Boja:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="color" name="owner_color" id="owner_color" value="#4092d9" />
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_address" class="col-sm-3 control-label">Adresa:
																	</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="owner_address" id="owner_address" placeholder="Adresa">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_postal_code" class="col-sm-3 control-label">Poštanski broj:
																	</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<input class="form-control materail-input" type="text" name="owner_postal_code" id="owner_postal_code" placeholder="Poštanski broj">
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<div class="form-group">
																	<label for="owner_city" class="col-sm-3 control-label">Općina:</label>
																	<div class="col-sm-9">
																		<select class="selectpicker" id="owner_city" name="owner_city" data-live-search="true">
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
																	<label for="owner_region" class="col-sm-3 control-label">Regija:</label>
																	<div class="col-sm-9">
																		<select class="selectpicker" id="owner_region" name="owner_region" data-live-search="true">
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
																	<label for="owner_country" class="col-sm-3 control-label">Država:</label>
																	<div class="col-sm-9">
																		<select class="selectpicker" id="owner_country" name="owner_country" data-live-search="true">
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
																	<label for="owner_other_info" class="col-sm-3 control-label">Ostale informacije:</label>
																	<div class="col-sm-9">
																		<div class="materail-input-block materail-input-block_success">
																			<textarea class="form-control materail-input material-textarea" name="owner_other_info" placeholder="Ostale informacije" rows="6" id="owner_other_info"></textarea>
																			<span class="materail-input-block__line"></span>
																		</div>
																	</div>
																</div>

																<!-- Add image -->
																<div class="form-group">
																	<label for="owner_image" class="col-sm-3 control-label">Logo:</label>
																	<div class="col-sm-9">
																		<div class="fileinput fileinput-new" data-provides="fileinput">
																			<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
																			</div>
																			<div>
																				<span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi
																						logo</span><span class="fileinput-exists">Promijeni</span><input type="file" name="owner_image" id="owner_image"></span>
																				<a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
																				<script>
																					$(function() {
																						$('#owner_image').change(function() {

																							var ext = $('#owner_image').val().split('.').pop().toLowerCase();

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
																			<div class="alert material-alert material-alert_danger">
																				Greška:
																				Fotografija koju pokušavate
																				dodati je veća od dozvoljene veličine.</div>
																		</div>
																		<div id="idk_alert_ext" class="hidden">
																			<div class="alert material-alert material-alert_danger">
																				Greška: Format
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
															<!-- End form - add identity -->

														</div>

													<?php } ?>

												</div>
											</div>
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



				case "datacollection_type":

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
							<div class="col-xs-12">
								<?php
								if (isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								} else {
									$mess = 0;
								}

								if ($mess == 1) {
									echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
								} elseif ($mess == 2) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu vrstu informacije.</div>';
								} elseif ($mess == 3) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali vrstu informacije.</div>';
								} elseif ($mess == 4) {
									echo '<div class="alert material-alert material-alert_danger">Greška! Vrsta informacije koju pokušavate dodati već postoji u bazi podataka.</div>';
								}
								?>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-info idk_color_green" aria-hidden="true"></i> Vrsta informacije sa terena</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<h5>Dodaj novu vrstu informacije</h5>
									<div class="row">
										<div class="col-md-8 idk_setting_form_wrapper">

											<!-- Form - add employee position -->
											<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_datacollection_type" method="post" class="form-horizontal" role="form">

												<div class="form-group">
													<label for="dc_type_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv informacije:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="dc_type_name" id="dc_type_name" placeholder="Naziv" required>
															<span class="materail-input-block__line"></span>
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
											<!-- End form - add employee position -->

										</div>
									</div>
									<hr>

									<div class="row">
										<div class="col-xs-12">
											<h5>Trenutne vrste informacija sa terena</h5>

											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table').DataTable({

														"order": [
															[0, "asc"]
														],

														"bAutoWidth": false,

														"aoColumns": [{
																"width": "90%"
															},
															{
																"width": "10%",
																"bSortable": false
															}
														]
													});
												});
											</script>

											<!-- Employee positions table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>Naziv</th>
														<th>Obriši</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$query = $db->prepare("
															SELECT dc_type_id, dc_type_name
															FROM idk_datacollection_type");

													$query->execute();

													while ($row = $query->fetch()) {

														$dc_type_id = $row['dc_type_id'];
														$dc_type_name = $row['dc_type_name'];

													?>
														<tr>
															<td>
																<?php echo $dc_type_name; ?>
															</td>
															<td class="text-center">
																<a href="#" data="<?php getSiteUrl(); ?>idkadmin/do.php?form=delete_datacollection_type&dc_type_id=<?php echo $dc_type_id; ?>" data-toggle="modal" data-target="#modalDelete" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger">
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
																	<p>Jeste li sigurni da želite obrisati vrstu informacije sa terena?</p>
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


				case "city":

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
							<div class="col-xs-12">
								<?php
								if (isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								} else {
									$mess = 0;
								}

								if ($mess == 1) {
									echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
								} elseif ($mess == 2) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu općinu.</div>';
								} elseif ($mess == 3) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali općinu.</div>';
								} elseif ($mess == 4) {
									echo '<div class="alert material-alert material-alert_danger">Greška! Općina koju pokušavate dodati već postoji u bazi podataka.</div>';
								}
								?>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-map-marker idk_color_green" aria-hidden="true"></i> Općina</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<h5>Dodaj novu općinu</h5>
									<div class="row">
										<div class="col-md-8 idk_setting_form_wrapper">

											<!-- Form - add employee position -->
											<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_city" method="post" class="form-horizontal" role="form">

												<div class="form-group">
													<label for="city_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv općine:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="city_name" id="city_name" placeholder="Naziv općine" required>
															<span class="materail-input-block__line"></span>
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
											<!-- End form - add employee position -->

										</div>
									</div>
									<hr>

									<div class="row">
										<div class="col-xs-12">
											<h5>Trenutne općine</h5>

											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table').DataTable({

														"order": [
															[0, "asc"]
														],

														"bAutoWidth": false,

														"aoColumns": [{
																"width": "90%"
															},
															{
																"width": "10%",
																"bSortable": false
															}
														]
													});
												});
											</script>

											<!-- Employee positions table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>Naziv</th>
														<th>Obriši</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$query = $db->prepare("
															SELECT location_id, location_name
															FROM idk_location
															WHERE location_type = 1");

													$query->execute();

													while ($row = $query->fetch()) {

														$location_id = $row['location_id'];
														$location_name = $row['location_name'];

													?>
														<tr>
															<td>
																<?php echo $location_name; ?>
															</td>
															<td class="text-center">
																<a href="#" data="<?php getSiteUrl(); ?>idkadmin/do.php?form=delete_city&location_id=<?php echo $location_id; ?>" data-toggle="modal" data-target="#modalDelete" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger">
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
																	<p>Jeste li sigurni da želite obrisati općinu?</p>
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



				case "region":

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
							<div class="col-xs-12">
								<?php
								if (isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								} else {
									$mess = 0;
								}

								if ($mess == 1) {
									echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
								} elseif ($mess == 2) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu regiju.</div>';
								} elseif ($mess == 3) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali regiju.</div>';
								} elseif ($mess == 4) {
									echo '<div class="alert material-alert material-alert_danger">Greška! Regija koju pokušavate dodati već postoji u bazi podataka.</div>';
								}
								?>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-map-marker idk_color_green" aria-hidden="true"></i> Regija</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<h5>Dodaj novu regiju</h5>
									<div class="row">
										<div class="col-md-8 idk_setting_form_wrapper">

											<!-- Form - add employee position -->
											<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_region" method="post" class="form-horizontal" role="form">

												<div class="form-group">
													<label for="region_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv regije:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="region_name" id="region_name" placeholder="Naziv regije" required>
															<span class="materail-input-block__line"></span>
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
											<!-- End form - add employee position -->

										</div>
									</div>
									<hr>

									<div class="row">
										<div class="col-xs-12">
											<h5>Trenutne regije</h5>

											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table').DataTable({

														"order": [
															[0, "asc"]
														],

														"bAutoWidth": false,

														"aoColumns": [{
																"width": "90%"
															},
															{
																"width": "10%",
																"bSortable": false
															}
														]
													});
												});
											</script>

											<!-- Employee positions table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>Naziv</th>
														<th>Obriši</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$query = $db->prepare("
															SELECT location_id, location_name
															FROM idk_location
															WHERE location_type = 2");

													$query->execute();

													while ($row = $query->fetch()) {

														$location_id = $row['location_id'];
														$location_name = $row['location_name'];

													?>
														<tr>
															<td>
																<?php echo $location_name; ?>
															</td>
															<td class="text-center">
																<a href="#" data="<?php getSiteUrl(); ?>idkadmin/do.php?form=delete_region&location_id=<?php echo $location_id; ?>" data-toggle="modal" data-target="#modalDelete" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger">
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
																	<p>Jeste li sigurni da želite obrisati regiju?</p>
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



				case "country":

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
							<div class="col-xs-12">
								<?php
								if (isset($_GET['mess'])) {
									$mess = $_GET['mess'];
								} else {
									$mess = 0;
								}

								if ($mess == 1) {
									echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
								} elseif ($mess == 2) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu državu.</div>';
								} elseif ($mess == 3) {
									echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali državu.</div>';
								} elseif ($mess == 4) {
									echo '<div class="alert material-alert material-alert_danger">Greška! Država koju pokušavate dodati već postoji u bazi podataka.</div>';
								}
								?>
							</div>
						</div>

						<div class="row">
							<div class="col-xs-8">
								<h1><i class="fa fa-map-marker idk_color_green" aria-hidden="true"></i> Država</h1>
							</div>
							<div class="col-xs-4 text-right idk_margin_top10">
								<a href="<?php getSiteUrl(); ?>idkadmin/settings?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
							</div>
							<div class="col-xs-12">
								<hr>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="content_box">
									<h5>Dodaj novu državu</h5>
									<div class="row">
										<div class="col-md-8 idk_setting_form_wrapper">

											<!-- Form - add employee position -->
											<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_country" method="post" class="form-horizontal" role="form">

												<div class="form-group">
													<label for="country_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv države:</label>
													<div class="col-sm-9">
														<div class="materail-input-block materail-input-block_success">
															<input class="form-control materail-input" type="text" name="country_name" id="country_name" placeholder="Naziv države" required>
															<span class="materail-input-block__line"></span>
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
											<!-- End form - add employee position -->

										</div>
									</div>
									<hr>

									<div class="row">
										<div class="col-xs-12">
											<h5>Trenutne države</h5>

											<script type="text/javascript">
												$(document).ready(function() {
													$('#idk_table').DataTable({

														"order": [
															[0, "asc"]
														],

														"bAutoWidth": false,

														"aoColumns": [{
																"width": "90%"
															},
															{
																"width": "10%",
																"bSortable": false
															}
														]
													});
												});
											</script>

											<!-- Employee positions table -->
											<table id="idk_table" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th>Naziv</th>
														<th>Obriši</th>
													</tr>
												</thead>

												<tbody>
													<?php
													$query = $db->prepare("
															SELECT location_id, location_name
															FROM idk_location
															WHERE location_type = 3");

													$query->execute();

													while ($row = $query->fetch()) {

														$location_id = $row['location_id'];
														$location_name = $row['location_name'];

													?>
														<tr>
															<td>
																<?php echo $location_name; ?>
															</td>
															<td class="text-center">
																<a href="#" data="<?php getSiteUrl(); ?>idkadmin/do.php?form=delete_country&location_id=<?php echo $location_id; ?>" data-toggle="modal" data-target="#modalDelete" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger">
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
																	<p>Jeste li sigurni da želite obrisati državu?</p>
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
			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>

</html>