<div id="idk_loader"></div>

<div id="idk_logo">
	<a href="<?php getSiteUrl(); ?>idkadmin/"><img class="idk_logo1 img-responsive" src="<?php getSiteUrl(); ?>img/logo.png" /></a>
	<a href="<?php getSiteUrl(); ?>idkadmin/"><img class="idk_logo2 img-responsive" src="<?php getSiteUrl(); ?>img/logo_small.png" /></a>
</div>

<div id="idk_add_button">
	<div class="dropdown">
		<a href="#" class="dropdown-toggle" id="idk_add_dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
			<i class="fa fa-plus fa-lg" aria-hidden="true"></i>
		</a>
		<ul class="dropdown-menu" aria-labelledby="idk_add_dropdown">
			<li data-toggle="tooltip" data-placement="right" title="Klijent"><a href="clients?page=add"><i class="fa fa-briefcase fa-lg" aria-hidden="true"></i></a></li>
			<li data-toggle="tooltip" data-placement="right" title="Proizvod"><a href="products?page=add"><i class="fa fa-tasks fa-lg" aria-hidden="true"></i></a></li>
			<li data-toggle="tooltip" data-placement="right" title="Zaposlenik"><a href="employees?page=add"><i class="fa fa-user-plus fa-lg" aria-hidden="true"></i></a></li>
			<li data-toggle="tooltip" data-placement="right" title="Poruka"><a href="messages?page=new"><i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i></a></li>
		</ul>
	</div>
</div>

<!-- <div id="idk_search_header">
	<form>
		<input type="search" placeholder="&#xf002; Traži proizvode, narudžbe, kupce ..." required>
	</form>
</div> -->

<div id="idk_topbar">
	<ul>

		<!-- Messages -->
		<li class="dropdown idk_dropdown_static">
			<a href="#" class="dropdown-toggle" id="idk_mail_notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				<i class="fa fa-envelope-o fa-lg" aria-hidden="true"></i>
				<div class="getNumberOfMessages"></div>
			</a>
			<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="idk_mail_notifications">
				<li class="idk_dropdown_header">Posljednje primljene poruke</li>
				<div id="idk_mail_notifications_scroll">
					<li>
						<ul class="idk_dropdown_menu getMessages_10"></ul>
					</li>
				</div>
				<li class="idk_dropdown_footer"><a href="<?php getSiteUrl(); ?>idkadmin/messages?page=list">Vidi sve poruke</a></li>
			</ul>
		</li>

		<!-- Orders notifications -->
		<li class="dropdown idk_dropdown_static">
			<a href="#" class="dropdown-toggle" id="idk_orders_notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				<i class="fa fa-shopping-cart fa-lg" aria-hidden="true"></i>
				<div class="getNumberOfOrdersNotifications"></div>
			</a>
			<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="idk_orders_notifications">
				<li class="idk_dropdown_header pull-left">Notifikacije</li>
				<li class="idk_dropdown_header pull-right">
					<span id="orders_notifications_mark_read" data-id="<?php echo $logged_employee_id; ?>">Označi sve kao pročitano</span>
				</li>

				<script>
					$('#orders_notifications_mark_read').on('click', function() {
						var data_id = $(this).data('id');
						$.ajax({
							url: "do.php?form=orders_notifications_mark_read",
							type: "POST",
							data: {
								id: data_id
							},
							dataType: "html",
							success: orders_notifications_mark_read
						});
					});
				</script>

				<div class="clearfix"></div>
				<div id="idk_orders_notifications_scroll">
					<li>
						<ul class="idk_dropdown_menu getOrdersNotifications_20"></ul>
					</li>
				</div>
				<li class="idk_dropdown_footer">
					<a href="<?php getSiteUrl(); ?>idkadmin/notifications">Vidi sve notifikacije</a>
				</li>
			</ul>
		</li>

		<!-- Other notifications -->
		<li class="dropdown idk_dropdown_static">
			<a href="#" class="dropdown-toggle" id="idk_other_notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				<i class="fa fa-bell-o fa-lg" aria-hidden="true"></i>
				<div class="getNumberOfOtherNotifications"></div>
			</a>
			<ul class="dropdown-menu dropdown-menu-right" aria-labelledby="idk_other_notifications">
				<li class="idk_dropdown_header pull-left">Notifikacije</li>
				<li class="idk_dropdown_header pull-right">
					<span id="other_notifications_mark_read" data-id="<?php echo $logged_employee_id; ?>">Označi sve kao pročitano</span>
				</li>

				<script>
					$('#other_notifications_mark_read').on('click', function() {
						var data_id = $(this).data('id');
						$.ajax({
							url: "do.php?form=other_notifications_mark_read",
							type: "POST",
							data: {
								id: data_id
							},
							dataType: "html",
							success: other_notifications_mark_read
						});
					});
				</script>

				<div class="clearfix"></div>
				<div id="idk_other_notifications_scroll">
					<li>
						<ul class="idk_dropdown_menu getOtherNotifications_20"></ul>
					</li>
				</div>
				<li class="idk_dropdown_footer">
					<a href="<?php getSiteUrl(); ?>idkadmin/notifications">Vidi sve notifikacije</a>
				</li>
			</ul>
		</li>

		<!-- Profile info -->
		<li class="dropdown idk_dropdown_static">
			<a href="#" class="idk_header_user_link dropdown-toggle" id="idk_user_notifications" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
				<ul class="list-inline">
					<li><img src="./files/employees/images/<?php getEmployeeImage(); ?>" class="idk_header_user_image" alt="<?php getEmployeeFullname(); ?> Image" /></li>
					<li class="idk_user_text_hidden"><?php getEmployeeFullname(); ?></li>
				</ul>
			</a>
			<ul class="dropdown-menu dropdown-menu-right idk_dropdown_menu" aria-labelledby="idk_user_notifications">
				<li class="idk_user_header">
					<img src="./files/employees/images/<?php getEmployeeImage(); ?>" class="img-circle" alt="<?php getEmployeeFullname(); ?> Image">
					<p>
						<?php getEmployeeFullname(); ?>
						<br>
						<small><?php getEmployeePosition(); ?></small>
					</p>
				</li>
				<li><a href="<?php getSiteUrl(); ?>idkadmin/employees?page=open&id=<?php echo $logged_employee_id; ?>"><i class="fa fa-user" aria-hidden="true"></i> Moj profil</a></li>
				<li><a href="<?php getSiteUrl(); ?>idkadmin/employees?page=edit_profile"><i class="fa fa-edit" aria-hidden="true"></i> Uredi profil</a></li>
				<li><a href="<?php getSiteUrl(); ?>idkadmin/logs?page=list"><i class="fa fa-file-text-o" aria-hidden="true"></i> Pregledaj LOG</a></li>
				<li><a href="<?php getSiteUrl(); ?>idkadmin/do.php?form=logout"><i class="fa fa-lock" aria-hidden="true"></i> Odjava</a></li>
			</ul>
		</li>
	</ul>
</div>

<script>
	$('.dropdown-menu').click(function(e) {
		e.stopPropagation();
	});
</script>