<?php
include("includes/functions.php");
include("includes/common.php");
?>

<!DOCTYPE html>
<html>

<head>

	<?php include('includes/head.php'); ?>

</head>

<body onload="startTime();">
	<header>
		<?php include('header.php'); ?>
	</header>
	<div id="sidebar">
		<?php include('menu.php'); ?>
	</div>
	<div id="content" class="idk_index_content">
		<div class="container-fluid">

			<div class="row">
				<div class="col-sm-4">
					<h1>
						<i class="fa fa-angle-double-right idk_color_green" aria-hidden="true"></i> Dobrodošli <?php getEmployeeFullname(); ?>
					</h1>
				</div>
				<div class="col-sm-8 text-right idk_margin_top10">

				</div>
				<div class="col-xs-12">
					<?php
					if (isset($_GET['mess'])) {
						$mess = $_GET['mess'];
					} else {
						$mess = 0;
					}

					if ($mess == 1) {
						// echo '<br><div class="alert material-alert material-alert_success">Hvala! Uspješno ste poslali poruku IDK CRM agentima za podršku.</div>';
					}
					?>
					<hr>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6 col-lg-3">
					<div class="idk_time_box idk_box_shadow">
						<h2 id="idk_clock"></h2>

						<script>
							function startTime() {
								var today = new Date();
								var h = today.getHours();
								var m = today.getMinutes();
								m = checkTime(m);
								document.getElementById('idk_clock').innerHTML = h + ":" + m;
								var t = setTimeout(startTime, 500);
							}

							function checkTime(i) {
								if (i < 10) {
									i = "0" + i
								};
								return i;
							}
						</script>
						<p><?php getAdminDate(); ?></p>
						<ul class="list-inline">
							<li><img src="images/<?php getWeatherIcon(); ?>" /> <?php echo getCrmCityr(); ?></li>
							<li><img src="images/w_temperature.png" /> <?php getTemperature(); ?> °C</li>
						</ul>
					</div>
				</div>
				<div class="col-md-6 col-lg-9">
					<div class="idk_events_box idk_box_shadow">
						<div class="row idk_stats_box">
							<div class="col-sm-12">
								<div class="idk_info_box">
									<h5>Statistika - <?php getCurrentMonthAndYear(); ?></h5>
								</div>
								<div class="col-sm-4">
									<div class="text-center">
										<h2><?php getVisitsStats(); ?></h2>
									</div>
									<div class="text-center">
										<p>POSJETA</p>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="text-center">
										<h2><?php getClientsStats(); ?></h2>
									</div>
									<div class="text-center">
										<p>KLIJENATA</p>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="text-center">
										<h2><?php getOrdersStats(); ?></h2>
									</div>
									<div class="text-center">
										<p>NARUDŽBI</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-6 col-lg-4">
					<div class="idk_box idk_box_shadow">
						<h5>Novi klijenti</h5>
						<ul class="idk_tasks">
							<!-- Get data for client -->
							<?php
							$query = $db->prepare("
								SELECT t1.client_id, t1.client_name, t1.created_at, t2.employee_first_name, t2.employee_last_name
								FROM idk_client t1, idk_employee t2
								WHERE client_active != 0 AND t2.employee_id = t1.client_added_by_id
								ORDER BY t1.created_at DESC
								LIMIT 10");

							$query->execute();

							while ($client = $query->fetch()) {

								$client_id = $client['client_id'];
								$client_name = $client['client_name'];
								$client_created_at = $client['created_at'];
								$client_created_by_employee_first_name = $client['employee_first_name'];
								$client_created_by_employee_last_name = $client['employee_last_name'];

							?>

								<a href="<?php getSiteUrl(); ?>idkadmin/clients?page=open&id=<?php echo $client_id; ?>"">
								<li>
									<h3>
									<span	class=" label label-success material-label material-label_success material-label_xs main-container__column" data-toggle="tooltip" data-placement="right" title="<?php echo $client_name; ?>"> <i class="fa fa-briefcase" aria-hidden="true"></i></span>
									<span class="label label-success material-label material-label_success material-label_xs main-container__column" data-toggle="tooltip" data-placement="right" title="<?php echo "${client_created_by_employee_first_name} ${client_created_by_employee_last_name}"; ?>"> <i class="fa fa-user" aria-hidden="true"></i></span> | <?php echo $client_name; ?> <span class="pull-right"><?php echo date('d.m.Y.', strtotime($client_created_at)); ?></span>
									</h3>
									</li>
								</a>
							<?php } ?>
						</ul>
					</div>
				</div>
				<div class="col-md-6 col-lg-4">
					<div class="idk_box idk_box_shadow">
						<h5>Nove narudžbe</h5>
						<ul class="idk_tasks">
							<!-- Get data for order -->
							<?php

							$query = $db->prepare("
                SELECT t1.order_id, t1.employee_id, t1.created_at, t2.client_name, t3.employee_first_name, t3.employee_last_name
								FROM idk_order t1
								INNER JOIN idk_client t2
								ON t1.client_id = t2.client_id
								LEFT JOIN idk_employee t3
								ON t1.employee_id = t3.employee_id
								WHERE t1.order_active = :order_active
								ORDER BY t1.created_at DESC
								LIMIT 10");

							$query->execute(array(
								':order_active' => 1
							));

							while ($order = $query->fetch()) {

								$order_id = $order['order_id'];
								$client_name = $order['client_name'];
								$order_employee_first_name = $order['employee_first_name'];
								$order_employee_last_name = $order['employee_last_name'];
								$order_created_at = $order['created_at'];
								$order_created_at_new_format = date('d.m.Y.', strtotime($order['created_at']));

							?>
								<a href="<?php getSiteUrl(); ?>idkadmin/orders?page=open&order_id=<?php echo $order_id; ?>">
									<li>
										<h3><span class=" label label-success material-label material-label_success material-label_xs main-container__column" data-toggle="tooltip" data-placement="right" title="Narudžba #<?php echo $order_id; ?>"><i class="fa fa-shopping-cart" aria-hidden="true"></i></span>
											<?php if (isset($order_employee_first_name)) { ?>
												<span class="label label-success material-label material-label_success material-label_xs main-container__column" data-toggle="tooltip" data-placement="right" title="<?php echo $order_employee_first_name . ' ' . $order_employee_last_name; ?>"><i class="fa fa-user" aria-hidden="true"></i></span>
											<?php } else { ?>
												<span class="label label-success material-label material-label_success material-label_xs main-container__column" data-toggle="tooltip" data-placement="right" title="<?php echo $client_name; ?>"><i class="fa fa-briefcase" aria-hidden="true"></i></span>
											<?php } ?> | <?php echo $client_name; ?> <span class="pull-right"><?php echo $order_created_at_new_format; ?></span>
										</h3>
									</li>
								</a>
							<?php } ?>
						</ul>
					</div>
				</div>
				<div class="col-md-12 col-lg-4">
					<div class="idk_box idk_box_shadow">
						<section class="main">
							<div class="custom-calendar-wrap">
								<div id="custom-inner" class="custom-inner">
									<div class="custom-header clearfix">
										<nav>
											<span id="custom-prev" class="custom-prev"></span>
											<span id="custom-next" class="custom-next"></span>
										</nav>
										<h2 id="custom-month" class="custom-month"></h2>
										<h3 id="custom-year" class="custom-year"></h3>
									</div>
									<div id="calendar" class="fc-calendar-container"></div>
								</div>
							</div>
						</section>
						<script type="text/javascript">
							$(function() {

								var transEndEventNames = {
										'WebkitTransition': 'webkitTransitionEnd',
										'MozTransition': 'transitionend',
										'OTransition': 'oTransitionEnd',
										'msTransition': 'MSTransitionEnd',
										'transition': 'transitionend'
									},
									transEndEventName = transEndEventNames[Modernizr.prefixed('transition')],
									$wrapper = $('#custom-inner'),
									$calendar = $('#calendar'),
									cal = $calendar.calendario({
										displayWeekAbbr: true
									}),
									$month = $('#custom-month').html(cal.getMonthName()),
									$year = $('#custom-year').html(cal.getYear());

								$('#custom-next').on('click', function() {
									cal.gotoNextMonth(updateMonthYear);
								});
								$('#custom-prev').on('click', function() {
									cal.gotoPreviousMonth(updateMonthYear);
								});

								function updateMonthYear() {
									$month.html(cal.getMonthName());
									$year.html(cal.getYear());
								}
							});
						</script>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-12">
					<div class="idk_box idk_box_shadow">
						<canvas id="indexChart"></canvas>
					</div>
				</div>
			</div>
			<script>
				let ctx = $('#indexChart');
				let labels = [];
				let visits = [];
				let clients = [];
				let orders = [];

				<?php
				$query = $db->prepare("
					SELECT *
					FROM idk_stat");

				$query->execute();

				while ($stat = $query->fetch()) {
					$date = $stat['stat_month'];
					$visit = $stat['stat_b2b_visits'];
					$client = $stat['stat_b2b_clients'];
					$order = $stat['stat_b2b_orders'];
					$label = date('m-Y', strtotime($date));
				?>

					labels.push('<?php echo $label; ?>');
					visits.push(<?php echo $visit; ?>);
					clients.push(<?php echo $client; ?>);
					orders.push(<?php echo $order; ?>);

				<?php } ?>

				let indexChart = new Chart(ctx, {
					type: 'line',
					data: {
						labels: labels,
						datasets: [{
							label: 'Posjete',
							data: visits,
							backgroundColor: [
								'rgba(255, 99, 132, 0.2)',
								'rgba(54, 162, 235, 0.2)',
								'rgba(255, 206, 86, 0.2)',
								'rgba(75, 192, 192, 0.2)',
								'rgba(153, 102, 255, 0.2)',
								'rgba(255, 159, 64, 0.2)'
							],
							borderColor: [
								'rgba(255, 99, 132, 1)',
								'rgba(54, 162, 235, 1)',
								'rgba(255, 206, 86, 1)',
								'rgba(75, 192, 192, 1)',
								'rgba(153, 102, 255, 1)',
								'rgba(255, 159, 64, 1)'
							],
							borderWidth: 1.5
						},
						{
							label: 'Novi klijenti',
							data: clients,
							backgroundColor: [
								'rgba(255, 99, 132, 0.2)',
								'rgba(54, 162, 235, 0.2)',
								'rgba(255, 206, 86, 0.2)',
								'rgba(75, 192, 192, 0.2)',
								'rgba(153, 102, 255, 0.2)',
								'rgba(255, 159, 64, 0.2)'
							],
							borderColor: [
								'rgba(255, 99, 132, 1)',
								'rgba(54, 162, 235, 1)',
								'rgba(255, 206, 86, 1)',
								'rgba(75, 192, 192, 1)',
								'rgba(153, 102, 255, 1)',
								'rgba(255, 159, 64, 1)'
							],
							borderWidth: 1.5
						},
						{
							label: 'Narudžbe',
							data: orders,
							backgroundColor: [
								'rgba(255, 99, 132, 0.2)',
								'rgba(54, 162, 235, 0.2)',
								'rgba(255, 206, 86, 0.2)',
								'rgba(75, 192, 192, 0.2)',
								'rgba(153, 102, 255, 0.2)',
								'rgba(255, 159, 64, 0.2)'
							],
							borderColor: [
								'rgba(255, 99, 132, 1)',
								'rgba(54, 162, 235, 1)',
								'rgba(255, 206, 86, 1)',
								'rgba(75, 192, 192, 1)',
								'rgba(153, 102, 255, 1)',
								'rgba(255, 159, 64, 1)'
							],
							borderWidth: 1.5
						}]
					},
					options: {
						scales: {
							y: {
								beginAtZero: true
							}
						}
					}
				});

				indexChart.options.elements.line.tension = 0.2;
				indexChart.update();
			</script>


			<!--/************************************************************
 * 							FOOTER
 * *********************************************************/-->
			<footer>
				<?php getCopyright(); ?>
			</footer>
		</div>
	</div>
</body>

</html>