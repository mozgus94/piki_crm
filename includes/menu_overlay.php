 <div id="idk_menu_overlay">
 	<div class="container-fluid">
 		<div class="row">
 			<div class="col-12">
 				<div class="container idk_page_title_container">
 					<div class="row align-items-center">
 						<div class="col-12">
 							<?php if (strpos($_SERVER['REQUEST_URI'], '/product') !== false) { ?>
 								<div class="row align-items-center">
 									<div class="col-8">
 										<h1 class="idk_page_title">Navigacija</h1>
 									</div>
 									<div class="col-4 text-right">
 										<p><a href="#" class="idk_menu_toggler idk_static_background"><span class="lnr lnr-cross"></span></a></p>
 									</div>
 									<div class="col-12">
 										<?php if (isset($_COOKIE['idk_session_front_client'])) { ?>
 											<p><em>Odabran klijent: <?php echo getSelectedClientName(); ?></em></p>
 										<?php } ?>
 									</div>
 								</div>
 							<?php } else { ?>
 								<h1 class="idk_page_title">Navigacija</h1>
 								<?php if (isset($_COOKIE['idk_session_front_client'])) { ?>
 									<p><em>Odabran klijent: <?php echo getSelectedClientName(); ?></em></p>
 								<?php } ?>
 							<?php } ?>
 							<ul>
 								<li><a href="<?php getSiteUrl(); ?>"><span class="lnr lnr-home"></span>Naslovnica</a></li>
 								<?php if (isset($logged_employee_id) and $logged_employee_id != 0) { ?>
 									<li><a href="<?php getSiteUrl(); ?>selectClient"><span class="lnr lnr-briefcase"></span>Odabir klijenta</a></li>
 								<?php } ?>
 								<li><a href="<?php getSiteUrl(); ?>categories"><span class="lnr lnr-list"></span>Kategorije</a></li>
 								<li><a href="<?php getSiteUrl(); ?>search"><span class="lnr lnr-magnifier"></span>Pretraga</a></li>
 								<li>
 									<a href="<?php getSiteUrl(); ?>cart"><span class="lnr lnr-cart"></span>Košarica
 										<?php if ($getTempOrder > 0) { ?>
 											<span class="badge badge-danger">1</span>
 										<?php } ?>
 									</a>
 								</li>
 								<li><a href="<?php getSiteUrl(); ?>list"><span class="lnr lnr-heart"></span>Moje liste</a></li>
 								<?php if (isset($logged_employee_id) and $logged_employee_id != 0) { ?>
 									<?php if (getEmployeeStatus() != 1) { ?>
 										<!-- <li><a href="<?php //getSiteUrl(); ?>mileage"><span class="lnr lnr-car"></span>Kilometraža</a></li> -->
 									<?php } ?>
 									<li><a href="<?php getSiteUrl(); ?>routes"><span class="lnr lnr-map-marker"></span>Rute</a></li>
 									<!-- <li><a href="<?php //getSiteUrl(); ?>assortment"><span class="lnr lnr-chart-bars"></span>Stanje asortimana</a></li> -->
 									<li><a href="<?php getSiteUrl(); ?>datacollection"><span class="lnr lnr-frame-expand"></span>Informacije sa terena</a></li>
 									<!-- <li><a href="<?php //getSiteUrl(); ?>debts"><span class="lnr lnr-frame-contract"></span>Zaduženja</a></li> -->
 									<li><a href="<?php getSiteUrl(); ?>products"><span class="lnr lnr-layers"></span>Proizvodi</a></li>
 									<li><a href="<?php getSiteUrl(); ?>offers_view"><span class="lnr lnr-file-empty"></span>Ponude</a></li>
 									<li><a href="<?php getSiteUrl(); ?>orders_view"><span class="lnr lnr-store"></span>Narudžbe</a></li>
 									<li>
 										<a href="<?php getSiteUrl(); ?>messages"><span class="lnr lnr-envelope"></span>Poruke
 											<?php if ($getUnreadMessages > 0) { ?>
 												<span class="badge badge-danger">1</span>
 											<?php } ?>
 										</a>
 									</li>
 									<li><a href="<?php getSiteUrl(); ?>settings"><span class="lnr lnr-cog"></span>Postavke</a></li>
 								<?php } ?>
 								<li><a href="<?php getSiteUrl(); ?>do.php?form=logout"><span class="lnr lnr-exit"></span>Odjava</a></li>
 							</ul>
 						</div>
 					</div>
 				</div>
 			</div>
 		</div>
 	</div>
 </div>