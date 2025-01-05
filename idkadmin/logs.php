<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
} else {
	header("Location: logs?page=list");
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
 * 							LIST ALL LOGS
 * *********************************************************/
				case "list":
			?>

					<div class="row">
						<div class="col-xs-12">
							<h1><i class="fa fa-file-text-o idk_color_green" aria-hidden="true"></i> Pregled LOG: <?php getEmployeeFullname(); ?></h1>
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

										<script type="text/javascript">
											$(document).ready(function() {
												$('#idk_table').DataTable({

													responsive: true,

													"order": [
														[1, "desc"]
													],

													"bAutoWidth": false,

													"aoColumns": [{
															"width": "0%",
															"bVisible": false
														},
														{
															"width": "20%"
														},
														{
															"width": "70%"
														}
													]
												});
											});
										</script>

										<!-- Logs table -->
										<table id="idk_table" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th></th>
													<th class="text-center">Datum i vrijeme</th>
													<th>Log opis</th>
												</tr>
											</thead>

											<tbody>
												<?php
												$query = $db->prepare("
													SELECT log_id, log_desc, log_date
													FROM idk_log
                          WHERE employee_id = :employee_id");

												$query->execute(array(
													':employee_id' => $logged_employee_id
												));

												while ($row = $query->fetch()) {

													$log_id = $row['log_id'];
													$log_date = $row['log_date'];
													$log_date_new_format = date('d.m.Y. H:i', strtotime($row['log_date']));
													$log_desc = $row['log_desc'];
												?>
													<tr>
														<td>
															<?php echo $log_id; ?>
														</td>
														<td class="text-center" data-sort="<?php echo $log_date; ?>">
															<?php echo $log_date_new_format; ?>
														</td>
														<td>
															<?php echo $log_desc; ?>
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