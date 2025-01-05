<?php
include("includes/functions.php");
include("includes/common_for_messages.php");

$getTempOrder = getTempOrder();
$getUnreadMessages = getUnreadMessages();

if (isset($_REQUEST["page"])) {
	$page = $_REQUEST["page"];
} else {
	$page = "list";
	header("Location: messages?page=list");
}

?>

<!DOCTYPE html>
<html lang="bs">

<head>

	<?php include('includes/head.php'); ?>

</head>

<body class="idk_body_background">

	<!-- Overlay menu -->
	<?php
	if (isset($_COOKIE['idk_session_front_employee'])) {
		include('includes/menu_overlay.php');
	} else {
	?>
		<div id="idk_menu_overlay">
			<div class="container-fluid">
				<div class="row">
					<div class="col-12">
						<div class="container idk_page_title_container">
							<div class="row align-items-center">
								<div class="col-12">
									<div class="row align-items-center">
										<div class="col-8">
											<h1 class="idk_page_title">Navigacija</h1>
										</div>
										<div class="col-4 text-right">
											<p><a href="#" class="idk_menu_toggler idk_static_background"><span class="lnr lnr-cross"></span></a></p>
										</div>
									</div>
									<ul>
										<li><a href="<?php getSiteUrl(); ?>orders"><span class="lnr lnr-list"></span>Nove narudžbe</a></li>
										<li><a href="<?php getSiteUrl(); ?>orders?page=finished_orders"><span class="lnr lnr-checkmark-circle"></span>Završene narudžbe</a></li>
										<li>
											<a href="<?php getSiteUrl(); ?>messages"><span class="lnr lnr-envelope"></span>Poruke
												<?php if ($getUnreadMessages > 0) { ?>
													<span class="badge badge-danger">1</span>
												<?php } ?>
											</a>
										</li>
										<li><a href="<?php getSiteUrl(); ?>settings_for_skladistar"><span class="lnr lnr-cog"></span>Postavke</a></li>
										<li><a href="<?php getSiteUrl(); ?>do.php?form=logout"><span class="lnr lnr-exit"></span>Odjava</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>

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
								echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success">Uspješno ste poslali poruku.</div></div>';
							} elseif ($mess == 2) {
								echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_danger">Greška: Forma nije pravilno popunjena!</div></div>';
							}
							?>
							<div class="col-10">
								<h1 class="idk_page_title">Moje poruke</h1>
							</div>
							<div class="col-2 text-right">
								<!-- Button trigger modal -->
								<button type="button" class="btn" data-toggle="modal" data-target="#newMessageModal">
									<span class="lnr lnr-plus-circle"></span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!-- Modal -->
		<div class="modal fade" id="newMessageModal" tabindex="-1" role="dialog" aria-labelledby="newMessageModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="newMessageModalLabel">Nova poruka</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form action="<?php getSiteUrl(); ?>do.php?form=send_message" class="idk_send_message_form" method="post">
							<input type="hidden" name="message_sentid" value="<?php echo $logged_employee_id; ?>">
							<div class="form-group">
								<label class="sr-only" for="mu_employee_id">Prima*</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><span class="lnr lnr-user"></span></div>
									</div>
									<select class="selectpicker form-control" title="Prima*" multiple="multiple" id="mu_employeeid" name="mu_employeeid[]" data-live-search="true" required>
										<?php
										$users_query = $db->prepare("
					  					SELECT employee_id, employee_first_name, employee_last_name
					  					FROM idk_employee
											WHERE employee_status != :employee_status AND employee_id != :employee_id
					  					ORDER BY employee_first_name ASC");

										$users_query->execute(array(
											':employee_status' => 0,
											':employee_id' => $logged_employee_id
										));

										while ($users = $users_query->fetch()) {

											echo "<option value='" . $users['employee_id'] . "' data-tokens='" . $users['employee_first_name'] . " " . $users['employee_last_name'] . "'>" . $users['employee_first_name'] . " " . $users['employee_last_name'] . "</option>";
										}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="sr-only" for="message_subject">Predmet*</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><span class="lnr lnr-tag"></span></div>
									</div>
									<input type="text" class="form-control" name="message_subject" id="message_subject" placeholder="Predmet*" required>
								</div>
							</div>
							<div class="form-group idk_textarea_form_group">
								<label class="sr-only" for="message_text">Vaša poruka*</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><span class="lnr lnr-pencil"></span></div>
									</div>
									<textarea class="form-control" id="message_text" class="form-control" name="message_text" rows="3" placeholder="Vaša poruka*"></textarea>
								</div>
							</div>
							<button type="submit" class="btn idk_btn btn-block">POŠALJI</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</header> <!-- End header -->

	<!-- Main -->
	<main>

		<?php
		switch ($page) {



				/************************************************************
 * 					LIST ALL MESSAGES
 * *********************************************************/
			case "list":

				if (isset($logged_employee_id) and $logged_employee_id != 0) {
		?>

					<!-- List messages section -->
					<section class="idk_list_items_section">
						<div class="container-fluid">
							<div class="row">
								<div class="col-12">
									<div class="container">
										<ul class="nav nav-pills mb-5 col-12" id="pills-tab" role="tablist">
											<li class="nav-item col-6 p-0" role="presentation">
												<a class="nav-link active" id="idk_received_messages_tab" data-toggle="pill" href="#idk_received_messages" role="tab" aria-controls="idk_received_messages" aria-selected="true">
													<div class="row align-items-center">
														<div class="col-3 text-right">
															<span class="lnr lnr-enter"></span>
														</div>
														<div class="col-9">
															Primljene poruke
														</div>
													</div>
												</a>
											</li>
											<li class="nav-item col-6 p-0" role="presentation">
												<a class="nav-link" id="idk_sent_messages-tab" data-toggle="pill" href="#idk_sent_messages" role="tab" aria-controls="idk_sent_messages" aria-selected="false">
													<div class="row align-items-center">
														<div class="col-3 text-right">
															<span class="lnr lnr-exit"></span>
														</div>
														<div class="col-9">
															Poslane poruke
														</div>
													</div>
												</a>
											</li>
										</ul>
										<div class="tab-content" id="pills-tabContent">

											<div class="tab-pane fade show active" id="idk_received_messages" role="tabpanel" aria-labelledby="idk_received_messages_tab">
												<?php
												$inbox_query = $db->prepare("
												SELECT message_id, message_subject, message_datetime, employee_first_name, employee_last_name, employee_image, mu_status
												FROM idk_messages
												INNER JOIN idk_messages_users ON idk_messages.message_id = idk_messages_users.mu_messageid
												INNER JOIN idk_employee ON idk_messages.message_sentid = idk_employee.employee_id
												WHERE mu_employeeid = :mu_employeeid AND mu_status != :mu_status
												ORDER BY mu_status, message_datetime DESC");

												$inbox_query->execute(array(
													':mu_employeeid' => $logged_employee_id,
													':mu_status' => 2
												));

												while ($inbox_row = $inbox_query->fetch()) {

													$message_id = $inbox_row['message_id'];
													$employee_fullname = $inbox_row['employee_first_name'] . ' ' . $inbox_row['employee_last_name'];
													$employee_image = $inbox_row['employee_image'];
													$message_subject = $inbox_row['message_subject'];
													$message_datetime = $inbox_row['message_datetime'];
													$message_datetime_f = date('d.m.Y H:i', strtotime($inbox_row['message_datetime']));
													$mu_status = $inbox_row['mu_status'];

													if ($mu_status == 0) {
														$mess_status_style = "font-weight: 700;";
														$mess_status_card_style = "border: 2px solid var(--brand-color); box-shadow: 0px 3px 24px rgba(1,114,175, 0.16);";
													} else {
														$mess_status_style = "";
														$mess_status_card_style = "";
													}
												?>

													<a href="<?php getSiteUrl(); ?>messages?page=open&id=<?php echo $message_id; ?>">
														<div class="card mb-3 idk_order_card" <?php echo 'style="' . $mess_status_card_style . '"'; ?>>
															<div class="card-body">
																<div class="row align-items-center">
																	<div class="col-3 p-0 text-center">
																		<img class="idk_order_client_image" src="<?php getSiteUrl(); ?>idkadmin/files/employees/images/<?php echo $employee_image; ?>" alt="<?php echo $employee_fullname; ?> image">
																	</div>
																	<div class="col-9">
																		<h5 class="card-title idk_order_client_name" <?php echo 'style="' . $mess_status_style . '"' ?>><?php echo $message_subject; ?></h5>
																		<p class="card-text idk_order_number" <?php echo 'style="' . $mess_status_style . '"' ?>><?php echo $employee_fullname; ?></p>
																	</div>
																</div>
																<p class="card-text text-right idk_order_date"><small><em <?php echo 'style="' . $mess_status_style . '"' ?>><?php echo $message_datetime_f; ?></em></small></p>
															</div>
														</div>
													</a>

												<?php } ?>
											</div>

											<div class="tab-pane fade" id="idk_sent_messages" role="tabpanel" aria-labelledby="idk_sent_messages_tab">
												<?php
												$inbox_query = $db->prepare("
												SELECT message_id, message_subject, message_datetime, employee_first_name, employee_last_name, employee_image
												FROM idk_messages
												INNER JOIN idk_messages_users ON idk_messages.message_id = idk_messages_users.mu_messageid
												INNER JOIN idk_employee ON idk_messages_users.mu_employeeid = idk_employee.employee_id
												WHERE message_sentid = :message_sentid AND message_status = :message_status
												ORDER BY message_datetime DESC");

												$inbox_query->execute(array(
													':message_sentid' => $logged_employee_id,
													':message_status' => 1
												));

												while ($inbox_row = $inbox_query->fetch()) {

													$message_id = $inbox_row['message_id'];
													$employee_fullname = $inbox_row['employee_first_name'] . ' ' . $inbox_row['employee_last_name'];
													$employee_image = $inbox_row['employee_image'];
													$message_subject = $inbox_row['message_subject'];
													$message_datetime = $inbox_row['message_datetime'];
													$message_datetime_f = date('d.m.Y H:i', strtotime($inbox_row['message_datetime']));
												?>

													<a href="<?php getSiteUrl(); ?>messages?page=open&id=<?php echo $message_id; ?>">
														<div class="card mb-3 idk_order_card">
															<div class="card-body">
																<div class="row align-items-center">
																	<div class="col-3 p-0 text-center">
																		<img class="idk_order_client_image" src="<?php getSiteUrl(); ?>idkadmin/files/employees/images/<?php echo $employee_image; ?>" alt="<?php echo $employee_fullname; ?> image">
																	</div>
																	<div class="col-9">
																		<h5 class="card-title idk_order_client_name"><?php echo $message_subject; ?></h5>
																		<p class="card-text idk_order_number"><?php echo $employee_fullname; ?></p>
																	</div>
																</div>
																<p class="card-text text-right idk_order_date"><small><em><?php echo $message_datetime_f; ?></em></small></p>
															</div>
														</div>
													</a>

												<?php } ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section> <!-- End list messages section -->

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
				 * 					OPEN MESSAGE
				 * *********************************************************/
			case "open":

				$message_id = $_GET['id'];

				//Check if message exists for logged employee
				$query_check = $db->prepare("
						SELECT mu_id
						FROM idk_messages_users
						WHERE mu_messageid = :mu_messageid AND mu_employeeid = :mu_employeeid");

				$query_check->execute(array(
					':mu_messageid' => $message_id,
					':mu_employeeid' => $logged_employee_id
				));

				$number_of_messages = $query_check->rowCount();

				//Check if message exists for logged employee sentmail
				$query_check_sent = $db->prepare("
						SELECT message_id
						FROM idk_messages
						WHERE message_id = :message_id AND message_sentid = :message_sentid");

				$query_check_sent->execute(array(
					':message_id' => $message_id,
					':message_sentid' => $logged_employee_id
				));

				$number_of_sent_messages = $query_check_sent->rowCount();

				if ($number_of_messages > 0 or $number_of_sent_messages > 0) {

					//Mark message as ready for logged employee
					$query_update = $db->prepare("
							UPDATE idk_messages_users
							SET mu_status = :mu_status
							WHERE mu_messageid = :mu_messageid AND mu_employeeid = :mu_employeeid");

					$query_update->execute(array(
						':mu_status' => 1,
						':mu_messageid' => $message_id,
						':mu_employeeid' => $logged_employee_id
					));

					//Get message data
					$query = $db->prepare("
							SELECT message_id, message_subject, message_text, message_datetime, message_sentid, employee_first_name, employee_last_name, employee_image
							FROM idk_messages
							INNER JOIN idk_messages_users ON idk_messages.message_id = idk_messages_users.mu_messageid
							INNER JOIN idk_employee ON idk_messages.message_sentid = idk_employee.employee_id
							WHERE message_id = :message_id");

					$query->execute(array(
						':message_id' => $message_id
					));

					$row = $query->fetch();

					$message_subject = $row['message_subject'];
					$message_sentid = $row['message_sentid'];
					$message_text = $row['message_text'];
					$employee_fullname = $row['employee_first_name'] . ' ' . $row['employee_last_name'];
					$employee_image = $row['employee_image'];
					$message_date = date('d.m.Y.', strtotime($row['message_datetime']));
					$message_time = date('H:i', strtotime($row['message_datetime']));

				?>
					<!-- Open message section -->
					<section class="idk_list_items_section">
						<div class="container-fluid">
							<div class="row">
								<div class="col-12">
									<div class="container">
										<div class="card mb-3 idk_order_card">
											<div class="card-header">
												<div class="row">
													<div class="col-9">
														<h1 class="idk_message_subject"><span class="lnr lnr-envelope"></span> <?php echo $message_subject; ?></h1>
													</div>
													<div class="col-3 text-right">
														<!-- Button trigger modal -->
														<button type="button" class="btn idk_reply_btn" data-toggle="modal" data-target="#replyMessageModal" title="Odgovori">
															<span class="lnr lnr-undo mr-1"></span>
														</button>
													</div>

													<!-- Modal -->
													<div class="modal fade" id="replyMessageModal" tabindex="-1" role="dialog" aria-labelledby="replyMessageModalLabel" aria-hidden="true">
														<div class="modal-dialog modal-dialog-centered" role="document">
															<div class="modal-content">
																<div class="modal-header">
																	<h5 class="modal-title" id="replyMessageModalLabel">Odgovori</h5>
																	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
																		<span aria-hidden="true">&times;</span>
																	</button>
																</div>
																<div class="modal-body">
																	<form action="<?php getSiteUrl(); ?>do.php?form=reply_message" class="idk_send_message_form" method="post">
																		<input type="hidden" name="message_sentid" value="<?php echo $logged_employee_id; ?>">
																		<!-- message_text must be echoed inside single quotes in case it contains double quotes or html code -->
																		<input type="hidden" name="message_text" value='<?php echo $message_text; ?>'>
																		<div class="form-group">
																			<label class="sr-only" for="mu_employee_id">Prima*</label>
																			<div class="input-group mb-2">
																				<div class="input-group-prepend">
																					<div class="input-group-text"><span class="lnr lnr-user"></span></div>
																				</div>
																				<select class="selectpicker form-control" title="Prima*" multiple="multiple" id="mu_employeeid" name="mu_employeeid[]" data-live-search="true" required>
																					<?php
																					$users_query = $db->prepare("
																							SELECT employee_id, employee_first_name, employee_last_name
																							FROM idk_employee
																							WHERE employee_status != :employee_status AND employee_id != :employee_id
																							ORDER BY employee_first_name ASC");

																					$users_query->execute(array(
																						':employee_status' => 0,
																						':employee_id' => $logged_employee_id
																					));

																					while ($users = $users_query->fetch()) {

																						if ($users['employee_id'] == $message_sentid) {
																							$user_select = "selected";
																						} else {
																							$user_select = "";
																						}

																						echo "<option value='" . $users['employee_id'] . "' data-tokens='" . $users['employee_first_name'] . " " . $users['employee_last_name'] . "'" . $user_select . ">" . $users['employee_first_name'] . " " . $users['employee_last_name'] . "</option>";
																					}
																					?>
																				</select>
																			</div>
																		</div>
																		<div class="form-group">
																			<label class="sr-only" for="message_subject">Predmet*</label>
																			<div class="input-group mb-2">
																				<div class="input-group-prepend">
																					<div class="input-group-text"><span class="lnr lnr-tag"></span></div>
																				</div>
																				<input type="text" class="form-control" name="message_subject" id="message_subject" placeholder="Predmet*" value="Re: <?php echo $message_subject; ?>" required>
																			</div>
																		</div>
																		<div class="form-group idk_textarea_form_group">
																			<label class="sr-only" for="reply_text">Vaša poruka*</label>
																			<div class="input-group mb-2">
																				<div class="input-group-prepend">
																					<div class="input-group-text"><span class="lnr lnr-pencil"></span></div>
																				</div>
																				<textarea class="form-control" id="reply_text" class="form-control" name="reply_text" rows="3" placeholder="Vaša poruka*"></textarea>
																			</div>
																		</div>
																		<button type="submit" class="btn idk_btn btn-block">POŠALJI</button>
																	</form>
																</div>
															</div>
														</div>
													</div>

												</div>
												<ul class="list-group list-group-horizontal align-items-center">
													<li class="list-group-item border-0">
														<img class="idk_message_open_employee_image mr-1" src="<?php getSiteUrl(); ?>idkadmin/files/employees/images/<?php echo $employee_image; ?>"> <?php echo $employee_fullname; ?> | <span class="lnr lnr-calendar-full mx-1"></span> <?php echo $message_date; ?> | <span class="lnr lnr-clock mx-1"></span> <?php echo $message_time; ?>
													</li>
												</ul>
											</div>
											<div class="card-body">
												<?php
												$message_text_array = explode('|', $message_text);
												echo '<p>' . $message_text_array[0] . '</p>';
												if (isset($message_text_array[1])) {
													echo '<br><br><blockquote class="blockquote">';
													echo '<p class="mb-4">' . $message_text_array[1] . '</p>';
													if (isset($message_text_array[2])) {
														for ($i = 2; $i < count($message_text_array); $i++) {
															echo '<blockquote class="blockquote"><p class="mb-4">' . $message_text_array[$i];
														}
														echo '</blockquote>';
													}
													echo '</blockquote>';
												}
												?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					</section> <!-- End open message section -->
		<?php
				} else {
					echo '
						<div class="alert material-alert material-alert_danger">
							<h4>PORUKA NIJE DOSTUPNA!</h4>
							<p>Poruku koju pokušavate otvoriti nije dostupna. Kontaktirajte administratora za pomoć.</p>
							<br />
							<a href="javascript: history.go(-1)"><button class="btn material-btn main-container__column"><i class="fa fa-chevron-left"></i> Povratak</button></a>
						</div>
					';
				}


				break;
		} ?>

	</main> <!-- End main -->

	<!-- Foot bar -->
	<?php
	if (isset($_COOKIE['idk_session_front_employee'])) {
		include('includes/foot_bar.php');
	} ?>

	<!-- foot.php -->
	<?php include('includes/foot.php'); ?>

</body>

</html>