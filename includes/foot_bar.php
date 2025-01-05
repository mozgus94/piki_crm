  <!-- Footer -->
  <footer class="idk_foot_bar fixed-bottom">
  	<!-- SVG Path -->
  	<div class="idk_svg_div">
  		<svg viewBox="0 0 500 150" preserveAspectRatio="none" style="height: 100%; width: 100%;">
  			<path d="M-3.67,-2.45 C175.22,158.38 309.53,148.52 506.49,-0.48 L500.00,150.00 L0.00,150.00 Z" style="stroke: none; fill: #FFF;"></path>
  		</svg>
  	</div>
  	<div class="container-fluid">
  		<div class="row justify-content-center align-items-center">
  			<div class="col-12">
  				<div class="container">
  					<div class="row justify-content-space-between align-items-center">
  						<div class="col-3">
  							<a href="#" class="idk_menu_toggler">
  								<span class="lnr lnr-menu"></span>
  								<?php if ($getUnreadMessages > 0) { ?>
  									<span class="badge badge-danger">1</span>
  								<?php } ?>
  							</a>
  						</div>
  						<div class="col-3">
  							<a href="search" <?php if (strpos($_SERVER['REQUEST_URI'], '/search') !== false) {
																		echo 'class="idk_active"';
																	} ?>>
  								<span class="lnr lnr-magnifier"></span>
  							</a>
  						</div>
  						<div class="col-3">
  							<a href="cart" <?php if (strpos($_SERVER['REQUEST_URI'], '/cart') !== false) {
																	echo 'class="idk_active"';
																} ?>>
  								<span class="lnr lnr-cart"></span>
  								<?php if ($getTempOrder > 0) { ?>
  									<span class="badge badge-danger">1</span>
  								<?php } ?>
  							</a>
  						</div>
  						<div class="col-3">
  							<a href="list" <?php if (strpos($_SERVER['REQUEST_URI'], '/list') !== false) {
																	echo 'class="idk_active"';
																} ?>>
  								<span class="lnr lnr-heart"></span>
  							</a>
  						</div>
  					</div>
  				</div>
  			</div>
  		</div>
  	</div>
  </footer> <!-- End footer -->