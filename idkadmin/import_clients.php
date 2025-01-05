<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();
?>
<!DOCTYPE html>
<html lang="en">

<head>

	<?php include('includes/head.php'); ?>
</head>

<body>
	<div class="container">
		<div class="row">

			<?php
			if ($getEmployeeStatus == 1) {
			?>

				<!-- Import json button trigger modal -->
				<div class="col-xs-12 idk_margin_top10 idk_margin_bottom30">
					<a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" data-toggle="modal" data-target="#importJsonModal"><i class="fa fa-file" aria-hidden="true"></i> <span>&nbsp;&nbsp;JSON IMPORT</span></a>
				</div>

				<!-- Modal import json -->
				<div class="modal material-modal material-modal_primary fade text-left" id="importJsonModal">
					<div class="modal-dialog ">
						<div class="modal-content material-modal__content">
							<div class="modal-header material-modal__header">
								<button class="close material-modal__close" data-dismiss="modal">&times;</button>
								<h4 class="modal-title material-modal__title">Import JSON datoteke</h4>
							</div>
							<div class="modal-body material-modal__body">

								<!-- Form - import json -->
								<form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=import_json_clients" method="post" role="form" class="form-horizontal" enctype="multipart/form-data" id="idk_import_json_form">

									<!-- Add json file -->
									<div class="form-group">
										<label for="json_file" class="col-sm-4 control-label"><span class="text-danger">*</span> JSON datoteka:</label>
										<div class="col-sm-8">
											<div class="fileinput fileinput-new" data-provides="fileinput">
												<div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
												<div>
													<span class="btn btn-default btn-file">
														<span class="fileinput-new">Izaberi datoteku</span>
														<span class="fileinput-exists">Promijeni</span>
														<input type="file" name="json_file" id="json_file" required>
													</span>
													<i class="fa fa-info-circle fa-lg idk_margin_left10" data-toggle="tooltip" data-placement="right" title="Napomena: Dokument ne smije biti veći od 20MB! - Dozvoljeni format: json" aria-hidden="true"></i>
													<br>
													<a href="#" class="btn btn-default fileinput-exists idk_margin_top10" data-dismiss="fileinput">Ukloni</a>
													<script>
														$(function() {
															$('#json_file').change(function() {

																var ext = $('#json_file').val().split('.').pop().toLowerCase();

																if ($.inArray(ext, ['json']) == -1) {
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

									<!-- Alerts for json_file -->
									<div class="form-group">
										<label class="col-sm-4"></label>
										<div class="col-sm-8">
											<div id="idk_alert_size" class="hidden">
												<div class="alert material-alert material-alert_danger">Greška: Datoteka koju pokušavate
													dodati je veća od dozvoljene veličine.</div>
											</div>
											<div id="idk_alert_ext" class="hidden">
												<div class="alert material-alert material-alert_danger">Greška: Format datoteke koju
													pokušavate dodati nije dozvoljen.</div>
											</div>
										</div>
									</div>

							</div>
							<div class="modal-footer material-modal__footer">
								<ul class="list-inline">
									<li class="hidden idk_spinner"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
									<li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Import</button></li>
								</ul>
								</form>
								<!-- End form - import json -->

								<script>
									$(function() {
										$('#idk_import_json_form').submit(function() {

											$('.idk_spinner').removeClass('hidden');

										});
									});
								</script>
							</div>

						</div>
					</div>
				</div> <!-- End modal - import json -->

			<?php
			} else {
				echo '
                                  <div class="alert material-alert material-alert_danger">
                                    <h4>NEMATE PRIVILEGIJE!</h4>
                                    <p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
                                  </div>
                                ';
			}
			?>

		</div>
	</div>
</body>

</html>