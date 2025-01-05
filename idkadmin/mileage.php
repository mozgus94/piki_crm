<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
} else {
	$page = "list";
	header("Location: mileage?page=list");
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
 * 							LIST ALL MILEAGES
 * *********************************************************/
				case "list":
			?>

					<div class="row">
						<div class="col-xs-8">
							<h1><i class="fa fa-car idk_color_green" aria-hidden="true"></i> Kilometraža</h1>
						</div>
						<div class="col-xs-4 text-right idk_margin_top10">
							<button type="button" data-toggle="modal" data-target="#restartMileage" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-car" aria-hidden="true"></i> <span>Restartuj kilometražu</span></button>
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

										if (isset($_GET['mileage'])) {
											$mileage_amount_start_from_db = $_GET['mileage'];
										} else {
											$mileage_amount_start_from_db = 0;
										}

										if (isset($_GET['employee_id'])) {
											$mileage_employee_id = $_GET['employee_id'];

											$query_mileage_employee = $db->prepare("
												SELECT employee_first_name, employee_last_name
												FROM idk_employee
												WHERE employee_id = :employee_id");

											$query_mileage_employee->execute(array(
												':employee_id' => $mileage_employee_id
											));

											$number_of_rows = $query_mileage_employee->rowCount();

											if ($number_of_rows != 0) {

												$row_mileage_employee = $query_mileage_employee->fetch();
												$mileage_employee_first_name = $row_mileage_employee['employee_first_name'];
												$mileage_employee_last_name = $row_mileage_employee['employee_last_name'];
											}
										} else {
											$mileage_employee_first_name = NULL;
											$mileage_employee_last_name = NULL;
										}

										if ($mess == 1) {
											echo '<div class="alert material-alert material-alert_success">Uspješno ste restartovali kilometražu za komercijalistu: ' . $mileage_employee_first_name . ' ' . $mileage_employee_last_name . '.</div>';
										} elseif ($mess == 2) {
											echo '<div class="alert material-alert material-alert_danger">Greška! Završna kilometraža ne može biti manja od početne. Početna kilometraža: ' . $mileage_amount_start_from_db . ' km</div>';
										} elseif ($mess == 3) {
											echo '<div class="alert material-alert material-alert_danger">Greška: Forma nije pravilno popunjena!</div>';
										}
										?>

										<!-- Filling the table with data -->
										<script type="text/javascript">
											$(document).ready(function() {
												$('#idk_table').DataTable({

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
											});
										</script>

										<!-- Mileage table -->
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

												<!-- Get data for mileage -->
												<?php
												$query = $db->prepare("
													SELECT mileage_id, mileage_employee_id, mileage_start_time, mileage_end_time, mileage_amount_start, mileage_amount_end
													FROM idk_mileage
													ORDER BY mileage_id DESC");

												$query->execute();

												$number_of_rows = $query->rowCount();

												if ($number_of_rows > 0) {
													while ($row = $query->fetch()) {

														$mileage_id = $row['mileage_id'];
														$mileage_employee_id = $row['mileage_employee_id'];
														$mileage_start_time = $row['mileage_start_time'];
														$mileage_end_time = $row['mileage_end_time'];
														$mileage_amount_start = $row['mileage_amount_start'];
														$mileage_amount_end = $row['mileage_amount_end'];

														if (isset($mileage_employee_id)) {
															$query_employee = $db->prepare("
																SELECT employee_first_name, employee_last_name
																FROM idk_employee
																WHERE employee_id = :employee_id");

															$query_employee->execute(array(
																':employee_id' => $mileage_employee_id
															));

															$row_employee = $query_employee->fetch();

															$employee_first_name = $row_employee['employee_first_name'];
															$employee_last_name = $row_employee['employee_last_name'];
														}

												?>

														<tr>
															<td>
																<?php echo $mileage_id; ?>
															</td>
															<td>
																<a href="<?php getSiteUrl(); ?>idkadmin/employees?page=open&id=<?php echo $mileage_employee_id; ?>">
																	<?php echo $employee_first_name . ' ' . $employee_last_name; ?>
																</a>
															</td>
															<td data-sort="<?php echo $mileage_start_time; ?>">
																<?php echo date('d.m.Y. H:i', strtotime($mileage_start_time)); ?>
															</td>
															<td data-sort="<?php echo $mileage_amount_start; ?>">
																<?php echo $mileage_amount_start . ' km'; ?>
															</td>
															<td data-sort="<?php echo $mileage_end_time; ?>">
																<?php if (isset($mileage_end_time)) { ?>
																	<?php echo date('d.m.Y. H:i', strtotime($mileage_end_time)); ?>
																<?php } ?>
															</td>
															<td data-sort="<?php echo $mileage_amount_end; ?>">
																<?php if (isset($mileage_amount_end)) { ?>
																	<?php echo $mileage_amount_end . ' km'; ?>
																<?php } ?>
															</td>
															<td data-sort="<?php echo ($mileage_amount_end - $mileage_amount_start); ?>">
																<?php if (isset($mileage_amount_end)) { ?>
																	<?php echo ($mileage_amount_end - $mileage_amount_start) . ' km'; ?>
																<?php } ?>
															</td>
														</tr>

												<?php }
												} ?>
											</tbody>
										</table>
										<!-- End employees table -->

									</div>
								</div>
							</div>
						</div>
					</div>

					<!-- Modal - restart mileage -->
					<div class="modal material-modal material-modal_primary fade text-left" id="restartMileage">
						<div class="modal-dialog ">
							<div class="modal-content material-modal__content">
								<div class="modal-header material-modal__header">
									<button class="close material-modal__close" data-dismiss="modal">&times;</button>
									<h4 class="modal-title material-modal__title">Restartuj kilometražu</h4>
								</div>
								<div class="modal-body material-modal__body">
									<!-- Form - restart mileage -->
									<form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=restart_mileage" method="post" class="form-horizontal" role="form">

										<p class="mt-5">Jeste li sigurni da želite restartovati kilometražu?</p>
										<p class="">Kilometraža za odabranog komercijalistu će biti restartovana i kilometraža za današnji dan će biti postavljena na 0. Prije toga, morate odabrati komercijalistu i upisati završnu kilometražu za prethodni dan:</p>
										<br>

										<div class="form-group">
											<label for="employee_id" class="col-sm-4 control-label"><span class="text-danger">*</span> Komercijalista:</label>
											<div class="col-sm-8">
												<select class="selectpicker" id="employee_id" name="employee_id" data-live-search="true" required>
													<option value=""></option>
													<?php
													$query_mileage_employee = $db->prepare("
														SELECT employee_id, employee_first_name, employee_last_name
														FROM idk_employee
														WHERE employee_active = :employee_active AND employee_status = :employee_status");

													$query_mileage_employee->execute(array(
														':employee_active' => 1,
														':employee_status' => 2
													));

													while ($row_mileage_employee = $query_mileage_employee->fetch()) {

														$mileage_employee_id = $row_mileage_employee['employee_id'];
														$mileage_employee_first_name = $row_mileage_employee['employee_first_name'];
														$mileage_employee_last_name = $row_mileage_employee['employee_last_name'];

														echo "<option value='" . $mileage_employee_id . "' data-tokens='" . $mileage_employee_first_name . ' ' . $mileage_employee_last_name . "'>" . $mileage_employee_first_name . ' ' . $mileage_employee_last_name . "</option>";
													}
													?>
												</select>
											</div>
										</div>

										<div class="form-group">
											<label for="mileage_amount_start_restart_mileage" class="col-sm-4 control-label"><span class="text-danger">*</span>
												Završna kilometraža:</label>
											<div class="col-sm-8">
												<div class="materail-input-block materail-input-block_success">
													<input class="form-control materail-input" type="number" name="mileage_amount_start" id="mileage_amount_start_restart_mileage" placeholder="Završna kilometraža" required>
													<span class="materail-input-block__line"></span>
												</div>
											</div>
										</div>
								</div>
								<div class="modal-footer material-modal__footer">
									<ul class="list-inline">
										<li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
										<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Restartuj</button></li>
									</ul>
								</div>
								</form>
							</div>
						</div>
					</div>
					<!-- End modal - restart mileage -->
			<?php

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