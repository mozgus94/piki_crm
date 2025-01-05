<?php
include("includes/functions.php");
include("includes/common.php");

$getTempOrder = getTempOrder();
$getUnreadMessages = getUnreadMessages();

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
								echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success">Uspješno ste ažurirali stanje asortimana.</div></div>';
							} elseif ($mess == 2) {
								echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_danger">Greška: Forma nije pravilno popunjena!</div></div>';
							}
							?>
							<div class="col-12">
								<h1 class="idk_page_title">Stanje asortimana za klijenta: <?php echo getSelectedClientName(); ?></h1>
								<h1 class="idk_page_title"><small><?php echo date('d.m.Y.'); ?></small></h1>
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

			<!-- List assortment section -->
			<section class="idk_list_items_section">
				<div class="container-fluid">
					<div class="row">
						<div class="col-12">
							<div class="container">

								<?php

								$products_query = $db->prepare("
									SELECT *
									FROM idk_product t1
									WHERE product_active = :product_active
									ORDER BY product_name");

								$products_query->execute(array(
									':product_active' => 1
								));

								while ($product = $products_query->fetch()) {

									$product_id = $product['product_id'];
									$product_name = $product['product_name'];
									$product_sku = $product['product_sku'];
									$product_price = $product['product_price'];
									$product_image = $product['product_image'];
									$product_currency = $product['product_currency'];
									$product_quantity = $product['product_quantity'];
									$product_unit = $product['product_unit'];
									$product_tax_name = $product['product_tax_name'];
									$product_tax_percentage = $product['product_tax_percentage'];

									$product_status_query = $db->prepare("
										SELECT t1.ap_status
										FROM idk_assortment_product t1
										INNER JOIN idk_assortment_report t2
										ON t1.ap_assortment_id = t2.ar_id
										WHERE t1.ap_product_id = :ap_product_id AND t2.ar_employee_id = :ar_employee_id AND t2.ar_client_id = :ar_client_id AND (t2.ar_datetime BETWEEN :ar_datetime_start AND :ar_datetime_end)");

									$product_status_query->execute(array(
										':ap_product_id' => $product_id,
										':ar_employee_id' => $logged_employee_id,
										':ar_client_id' => $_COOKIE['idk_session_front_client'],
										':ar_datetime_start' => date('Y-m-d 00:00:00'),
										':ar_datetime_end' => date('Y-m-d H:i:s')
									));

									$num_of_rows = $product_status_query->rowCount();
									$product_status = 0;

									if ($num_of_rows != 0) {
										$product_status_row = $product_status_query->fetch();
										$product_status = $product_status_row['ap_status'];
									}

								?>

									<div class="card mb-3 idk_order_card <?php if ($product_status == 1) {
																													echo 'idk_assortment_in_stock_card';
																												} elseif ($product_status == 2) {
																													echo 'idk_assortment_not_in_stock_card';
																												} ?>">
										<div class="card-body">
											<div class="row align-items-center">
												<div class="col-12">
													<p class="idk_assortment_text mb-4 <?php if ($product_status == 1) {
																																echo 'idk_text_brand';
																															} elseif ($product_status == 2) {
																																echo 'idk_text_red';
																															} ?>">
														<?php if ($product_status == 1) {
															echo 'IMA NA STANJU';
														} elseif ($product_status == 2) {
															echo 'NEMA NA STANJU';
														} ?>
													</p>
												</div>
												<div class="col-3 p-0 text-center">
													<img class="idk_order_client_image" src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>" alt="<?php echo $product_name; ?> slika">
												</div>
												<div class="col-9 col-md-6">
													<h5 class="card-title idk_order_client_name <?php if ($product_status == 1) {
																																				echo 'idk_text_brand idk_text_bold';
																																			} elseif ($product_status == 2) {
																																				echo 'idk_text_red idk_text_bold';
																																			} ?>"><?php echo $product_name; ?></h5>
												</div>
												<div class="col-12 col-md-3 mt-3 mt-md-0">
													<div class="row">
														<div class="col-12 text-right">
															<button type="button" class="btn idk_set_assortment_state_btn idk_text_brand <?php if ($product_status == 1) {
																																																							echo 'd-none';
																																																						} ?>" id="idk_assortment_in_stock_<?php echo $product_id; ?>" data="1" title="Ima na stanju">
																<span class="lnr lnr-checkmark-circle"></span>
															</button>
															<button type="button" class="btn idk_set_assortment_state_btn idk_text_red <?php if ($product_status == 2) {
																																																						echo 'd-none';
																																																					} ?>" id="idk_assortment_not_in_stock_<?php echo $product_id; ?>" data="2" title="Nema na stanju">
																<span class="lnr lnr-cross-circle">
															</button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								<?php } ?>

								<h1 class="idk_page_title mt-5"><small>Ostali proizvodi</small></h1>

								<?php
								$products_query = $db->prepare("
									SELECT *
									FROM idk_product t1
									WHERE product_active = :product_active
									ORDER BY product_name");

								$products_query->execute(array(
									':product_active' => 1
								));

								while ($product = $products_query->fetch()) {

									$product_id = $product['product_id'];
									$product_name = $product['product_name'];
									$product_sku = $product['product_sku'];
									$product_price = $product['product_price'];
									$product_image = $product['product_image'];
									$product_currency = $product['product_currency'];
									$product_quantity = $product['product_quantity'];
									$product_unit = $product['product_unit'];
									$product_tax_name = $product['product_tax_name'];
									$product_tax_percentage = $product['product_tax_percentage'];

									$product_status_query = $db->prepare("
										SELECT t1.ap_status
										FROM idk_assortment_product t1
										INNER JOIN idk_assortment_report t2
										ON t1.ap_assortment_id = t2.ar_id
										WHERE t1.ap_product_id = :ap_product_id AND t2.ar_employee_id = :ar_employee_id AND t2.ar_client_id = :ar_client_id AND (t2.ar_datetime BETWEEN :ar_datetime_start AND :ar_datetime_end)");

									$product_status_query->execute(array(
										':ap_product_id' => $product_id,
										':ar_employee_id' => $logged_employee_id,
										':ar_client_id' => $_COOKIE['idk_session_front_client'],
										':ar_datetime_start' => date('Y-m-d 00:00:00'),
										':ar_datetime_end' => date('Y-m-d H:i:s')
									));

									$num_of_rows = $product_status_query->rowCount();
									$product_status = 0;

									if ($num_of_rows != 0) {
										$product_status_row = $product_status_query->fetch();
										$product_status = $product_status_row['ap_status'];
									}

								?>

									<div class="card mb-3 idk_order_card <?php if ($product_status == 1) {
																													echo 'idk_assortment_in_stock_card';
																												} elseif ($product_status == 2) {
																													echo 'idk_assortment_not_in_stock_card';
																												} ?>">
										<div class="card-body">
											<div class="row align-items-center">
												<div class="col-12">
													<p class="idk_assortment_text mb-4 <?php if ($product_status == 1) {
																																echo 'idk_text_brand';
																															} elseif ($product_status == 2) {
																																echo 'idk_text_red';
																															} ?>">
														<?php if ($product_status == 1) {
															echo 'IMA NA STANJU';
														} elseif ($product_status == 2) {
															echo 'NEMA NA STANJU';
														} ?>
													</p>
												</div>
												<div class="col-3 p-0 text-center">
													<img class="idk_order_client_image" src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>" alt="<?php echo $product_name; ?> slika">
												</div>
												<div class="col-9 col-md-6">
													<h5 class="card-title idk_order_client_name <?php if ($product_status == 1) {
																																				echo 'idk_text_brand idk_text_bold';
																																			} elseif ($product_status == 2) {
																																				echo 'idk_text_red idk_text_bold';
																																			} ?>"><?php echo $product_name; ?></h5>
												</div>
												<div class="col-12 col-md-3 mt-3 mt-md-0">
													<div class="row">
														<div class="col-12 text-right">
															<button type="button" class="btn idk_set_assortment_state_btn idk_text_brand <?php if ($product_status == 1) {
																																																							echo 'd-none';
																																																						} ?>" id="idk_assortment_in_stock_<?php echo $product_id; ?>" data="1" title="Ima na stanju">
																<span class="lnr lnr-checkmark-circle"></span>
															</button>
															<button type="button" class="btn idk_set_assortment_state_btn idk_text_red <?php if ($product_status == 2) {
																																																						echo 'd-none';
																																																					} ?>" id="idk_assortment_not_in_stock_<?php echo $product_id; ?>" data="2" title="Nema na stanju">
																<span class="lnr lnr-cross-circle">
															</button>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								<?php } ?>

							</div>
						</div>
					</div>
				</div>
			</section> <!-- End list assortment section -->

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
		$(document).ready(function() {
			$('.idk_set_assortment_state_btn').on('click', function() {
				let self = $(this);
				let productId = $(this).attr('id').split('in_stock_')[1];
				let status = $(this).attr('data');
				let clientId = <?php echo isset($_COOKIE['idk_session_front_client']) ? $_COOKIE['idk_session_front_client'] : NULL; ?>;

				$.ajax({
					url: '<?php getSiteUrl(); ?>do.php?form=set_assortment_state',
					method: 'post',
					dataType: 'json',
					data: {
						productId,
						clientId,
						status
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
			});
		});
	</script>

</body>

</html>