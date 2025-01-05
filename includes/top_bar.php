<div class="container-fluid idk_top_bar">
	<div class="row">
		<div class="col-12">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-2">
						<?php if (strpos($_SERVER['REQUEST_URI'], '/cart') !== false or strpos($_SERVER['REQUEST_URI'], '/categories') !== false or strpos($_SERVER['REQUEST_URI'], '/datacollection') !== false or strpos($_SERVER['REQUEST_URI'], '/contact_us') !== false or strpos($_SERVER['REQUEST_URI'], '/list') !== false or strpos($_SERVER['REQUEST_URI'], '/messages') !== false or strpos($_SERVER['REQUEST_URI'], '/orders') !== false or strpos($_SERVER['REQUEST_URI'], '/product') !== false or strpos($_SERVER['REQUEST_URI'], '/search') !== false or strpos($_SERVER['REQUEST_URI'], '/settings') !== false or strpos($_SERVER['REQUEST_URI'], '/settings_for_skladistar') !== false or strpos($_SERVER['REQUEST_URI'], '/subcategories') !== false) { ?>
							<p><a href="javascript: history.go(-1)"><span class="lnr lnr-arrow-left"></span></a></p>
						<?php } ?>
					</div>
					<div class="col-8 text-center">

						<!-- Get owner name from db -->
						<?php
						$owner_query = $db->prepare("
              SELECT owner_id, owner_name, owner_image
              FROM idk_owner");

						$owner_query->execute();

						$owner = $owner_query->fetch();

						$owner_id = $owner['owner_id'];
						$owner_name = $owner['owner_name'];
						$owner_image = $owner['owner_image'];

						?>
						<a href="<?php getSiteUrl(); ?>">
							<!-- <p class="idk_top_bar_logo_typography"><?php //echo $owner_name; 
																													?></p> -->
							<a href="<?php getSiteUrl(); ?>"><img class="idk_top_bar_logo_img" src="<?php getSiteUrl() ?>idkadmin/files/owners/images/<?php echo $owner_image; ?>" alt="<?php echo $owner_name; ?> logo"></a>
						</a>
					</div>
					<div class="col-2 text-right">
						<!-- Notifications icon -->
						<!-- <p><span class="lnr lnr-alarm"></span><span class="badge badge-danger">1</span></p> -->

						<!-- Display menu icon on pages that don't have foot bar -->
						<?php if (strpos($_SERVER['REQUEST_URI'], '/product') !== false or strpos($_SERVER['REQUEST_URI'], '/orders') !== false or strpos($_SERVER['REQUEST_URI'], '/settings_for_skladistar') !== false or (strpos($_SERVER['REQUEST_URI'], '/messages') !== false and isset($_COOKIE['idk_session_front_employee_skladistar']))) { ?>
							<p>
								<a href="#" class="idk_menu_toggler idk_static_background">
									<span class="lnr lnr-menu"></span>
									<?php if ($getUnreadMessages > 0) { ?>
										<span class="badge badge-danger">1</span>
									<?php } ?>
								</a>
							</p>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>