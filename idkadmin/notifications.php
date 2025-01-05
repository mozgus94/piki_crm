<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();
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

			<div class="row">
				<div class="col-xs-8">
					<h1><i class="fa fa-bell-o idk_color_green" aria-hidden="true"></i> Notifikacije</h1>
				</div>
				<div class="col-xs-4 text-right idk_margin_top10">
				</div>
				<div class="col-xs-12">
					<hr />
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					<div class="content_box">
						<div id="myTabs" class="panel-group material-tabs-group">
							<ul class="nav nav-tabs material-tabs material-tabs_primary">
								<li class="active"><a href="#idk_notifications_orders" class="material-tabs__tab-link" data-toggle="tab"><i class="fa fa-shopping-cart" aria-hidden="true"></i> Notifikacije za narudžbe</a></li>
								<li><a href="#idk_notifications_other" class="material-tabs__tab-link" data-toggle="tab"><i class="fa fa-bell" aria-hidden="true"></i> Ostale notifikacije</a></li>
							</ul>
							<div class="tab-content materail-tabs-content">
								<div class="tab-pane fade active in" id="idk_notifications_orders">
									<script type="text/javascript">
										$(document).ready(function() {
											$('#idk_notifications_orders_table').DataTable({

												responsive: true,

												"order": [
													[1, "desc"]
												],

												"aoColumns": [{
														"width": "70%"
													},
													{
														"width": "30%"
													}
												]
											});
										});
									</script>
									<table id="idk_notifications_orders_table" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Tekst notifikacije</th>
												<th>Datum</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$query = $db->prepare("
													SELECT notification_id, notification_title, notification_icon, notification_link, notification_status, notification_datetime
													FROM idk_notifications
													WHERE notification_employeeid = :notification_employeeid AND notification_datetime <= NOW() AND notification_type = :notification_type");

											$query->execute(array(
												':notification_employeeid' => $logged_employee_id,
												':notification_type' => 3
											));

											while ($row = $query->fetch()) {

												$notification_id = $row['notification_id'];
												$notification_title = $row['notification_title'];
												$notification_icon = $row['notification_icon'];
												$notification_link = $row['notification_link'];
												$notification_datetime = $row['notification_datetime'];
												if ($row['notification_status'] == 1) {
													$notification_status = 'idk_highlight_bg idk_bold';
												} else {
													$notification_status = '';
												}
											?>
												<tr>
													<td class="<?php echo $notification_status; ?>"><?php echo '<a href="' . $notification_link . '&nid=' . $notification_id . '"><i class="fa fa-' . $notification_icon . '"></i></i> ' . $notification_title . '</a>'; ?></td>
													<td data-sort="<?php echo date('Y-m-d H:i', strtotime($notification_datetime)); ?>"><?php echo date('d.m.Y. H:i', strtotime($notification_datetime)); ?></td>
												</tr>
											<?php	}	?>
										</tbody>
									</table>
								</div>
								<div class="tab-pane fade" id="idk_notifications_other">
									<script type="text/javascript">
										$(document).ready(function() {
											$('#idk_notifications_other_table').DataTable({

												responsive: true,

												"order": [
													[1, "desc"]
												],

												"aoColumns": [{
														"width": "70%"
													},
													{
														"width": "30%"
													}
												]
											});
										});
									</script>
									<table id="idk_notifications_other_table" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th>Tekst notifikacije</th>
												<th>Datum</th>
											</tr>
										</thead>
										<tbody>
											<?php
											$query = $db->prepare("
													SELECT notification_id, notification_title, notification_icon, notification_link, notification_status, notification_datetime
													FROM idk_notifications
													WHERE notification_employeeid = :notification_employeeid AND notification_datetime <= NOW() AND notification_type = :notification_type");

											$query->execute(array(
												':notification_employeeid' => $logged_employee_id,
												':notification_type' => 4
											));

											while ($row = $query->fetch()) {

												$notification_id = $row['notification_id'];
												$notification_title = $row['notification_title'];
												$notification_icon = $row['notification_icon'];
												$notification_link = $row['notification_link'];
												$notification_datetime = $row['notification_datetime'];
												if ($row['notification_status'] == 1) {
													$notification_status = 'idk_highlight_bg idk_bold';
												} else {
													$notification_status = '';
												}
											?>
												<tr>
													<td class="<?php echo $notification_status; ?>"><?php echo '<a href="' . $notification_link . '&nid=' . $notification_id . '"><i class="fa fa-' . $notification_icon . '"></i></i> ' . $notification_title . '</a>'; ?></td>
													<td data-sort="<?php echo date('Y-m-d H:i', strtotime($notification_datetime)); ?>"><?php echo date('d.m.Y. H:i', strtotime($notification_datetime)); ?></td>
												</tr>
											<?php	}	?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
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
							<p>Jeste li sigurni da želite arhivirati poruku?</p>
						</div>
						<div class="modal-footer material-modal__footer">
							<button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
							<a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">Arhiviraj</button></a>
						</div>
					</div>
				</div>
			</div>

			<footer><?php getCopyright(); ?></footer>
		</div>
	</div>
</body>

</html>