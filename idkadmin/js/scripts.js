$(document).ready(function () {
	// === Sidebar navigation === //

	$('.submenu > a').click(function (e) {
		e.preventDefault();
		var submenu = $(this).siblings('ul');
		var li = $(this).parents('li');
		var submenus = $('#sidebar li.submenu ul');
		var submenus_parents = $('#sidebar li.submenu');
		if (li.hasClass('open')) {
			if ($(window).width() > 768 || $(window).width() < 479) {
				submenu.slideUp();
			} else {
				submenu.fadeOut(250);
			}
			li.removeClass('open');
		} else {
			if ($(window).width() > 768 || $(window).width() < 479) {
				submenus.slideUp();
				submenu.slideDown();
			} else {
				submenus.fadeOut(250);
				submenu.fadeIn(250);
			}
			submenus_parents.removeClass('open');
			li.addClass('open');
		}
	});

	var ul = $('#sidebar > ul');

	$('#sidebar > a').click(function (e) {
		e.preventDefault();
		var sidebar = $('#sidebar');
		if (sidebar.hasClass('open')) {
			sidebar.removeClass('open');
			ul.slideUp(250);
		} else {
			sidebar.addClass('open');
			ul.slideDown(250);
		}
	});

	// === Resize window related === //
	$(window).resize(function () {
		if ($(window).width() > 479) {
			ul.css({ display: 'block' });
			$('#content-header .btn-group').css({ width: 'auto' });
		}
		if ($(window).width() < 479) {
			// fix_position();
		}
		if ($(window).width() > 768) {
			$('#user-nav > ul').css({ width: 'auto', margin: '0' });
			$('#content-header .btn-group').css({ width: 'auto' });
		}
	});

	if ($(window).width() > 479) {
		$('#content-header .btn-group').css({ width: 'auto' });
		ul.css({ display: 'block' });
	}
});

//Mini scrolls
$(function () {
	$('#idk_orders_notifications_scroll').slimScroll({
		height: '200px'
	});
});
$(function () {
	$('#idk_other_notifications_scroll').slimScroll({
		height: '200px'
	});
});
$(function () {
	$('#idk_mail_notifications_scroll').slimScroll({
		height: '200px'
	});
});
$(function () {
	$('#idk_tasks_notifications_scroll').slimScroll({
		height: '200px'
	});
});
$(function () {
	$('.idk_events_box_top').slimScroll({
		height: '130px'
	});
});
$(function () {
	$('.idk_tasks').slimScroll({
		height: '300px'
	});
});
$(function () {
	$('.idk_project_list').slimScroll({
		height: '400px',
		alwaysVisible: true
	});
});

//Tooltip
$(function () {
	$('[data-toggle="tooltip"]').tooltip();
});

$(function () {
	$('.matchHeight').matchHeight();
});

//Bootstrap select
(function (root, factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module unless amdModuleId is set
		define(['jquery'], function (a0) {
			return factory(a0);
		});
	} else if (typeof module === 'object' && module.exports) {
		// Node. Does not work with strict CommonJS, but
		// only CommonJS-like environments that support module.exports,
		// like Node.
		module.exports = factory(require('jquery'));
	} else {
		factory(root['jQuery']);
	}
})(this, function (jQuery) {
	(function ($) {
		$.fn.selectpicker.defaults = {
			noneSelectedText: 'Ništa izabrano',
			noneResultsText: 'No results match {0}',
			countSelectedText: function (numSelected, numTotal) {
				return numSelected == 1 ? '{0} item selected' : '{0} items selected';
			},
			maxOptionsText: function (numAll, numGroup) {
				return [numAll == 1 ? 'Limit reached ({n} item max)' : 'Limit reached ({n} items max)', numGroup == 1 ? 'Group limit reached ({n} item max)' : 'Group limit reached ({n} items max)'];
			},
			selectAllText: 'Select All',
			deselectAllText: 'Deselect All',
			multipleSeparator: ', '
		};
	})(jQuery);
});

//Input date
$(document).ready(function () {
	var msg = '';
	var elements = document.getElementsByTagName('INPUT');

	for (var i = 0; i < elements.length; i++) {
		elements[i].oninvalid = function (e) {
			if (!e.target.validity.valid) {
				switch (e.target.id) {
					case 'password':
						e.target.setCustomValidity('Bad password');
						break;
					case 'login_email':
						e.target.setCustomValidity('Username cannot be blank');
						break;
					default:
						e.target.setCustomValidity('');
						break;
				}
			}
		};
		elements[i].oninput = function (e) {
			e.target.setCustomValidity(msg);
		};
	}
});

$(document).ready(function () {
	//fancybox
	$('.fancybox').fancybox();

	//Submit disabled
	$('#idk_form').submit(function (e) {
		$('li').removeClass('hidden');
		$('button').prop('disabled', true);
	});

	//Form on leve error
	$('form').on('change keyup keydown', 'input, textarea, select', function () {
		$(this).addClass('changed-input');
	});

	$(window).on('beforeunload', function () {
		if ($('.changed-input').length) {
			return true;
		}
	});

	$('form').on('submit', function (e) {
		$(window).off('beforeunload');
		return true;
	});

	//DataTable width fix
	$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		$.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
	});

	//timeago
	timeago().render($('.timeago'));
});

$(window).load(function () {
	$('#idk_loader').fadeOut('slow');
});

/*********************************************
 * 			DATATABLES - PRODUCTS LIST START
 *********************************************/
$(document).ready(function () {
	//Filling the datatable
	let siteUrl = $('#site_url').val();
	let tablePage = $('#table_page').val();

	let table = $('#idk_products_table').DataTable({
		responsive: true,
		processing: true,
		serverSide: true,
		ajax: 'server.php',
		drawCallback: function (settings) {
			tablePage = table.page.info().page;

			table.rows().every(function (rowIdx, tableLoop, rowLoop) {
				let data = this.data();
				let productImage = data[0];
				let productName = data[1];
				let productId = data[6];
				let productCurrency = data[7];
				let productUnit = data[8];
				data[0] = '<a href="' + siteUrl + 'idkadmin/products?page=open&id=' + productId + '"><img class="idk_profile_img" src="' + siteUrl + 'idkadmin/files/products/images/' + productImage + '"></img></a>';
				data[1] = '<a href="' + siteUrl + 'idkadmin/products?page=open&id=' + productId + '&table_page=' + tablePage + '">' + productName + '</a>';
				data[4] += ' ' + productCurrency;
				data[5] += ' ' + productUnit;
				data[6] =
					'<div class="btn-group material-btn-group">' +
					'<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown">' +
					'<i class="fa fa-cogs fa-lg" aria-hidden="true"></i>' +
					'<span class="caret material-btn__caret"></span>' +
					'</button>' +
					'<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">' +
					'<li>' +
					'<a href="' +
					siteUrl +
					'idkadmin/products?page=open&id=' +
					productId +
					'&table_page=' +
					tablePage +
					'" class="material-dropdown-menu__link">' +
					'<i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori' +
					'</a>' +
					'</li>' +
					'<li>' +
					'<a href="' +
					siteUrl +
					'idkadmin/products?page=edit&id=' +
					productId +
					'&table_page=' +
					tablePage +
					'" class="material-dropdown-menu__link">' +
					'<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi' +
					'</a>' +
					'</li>' +
					'<li class="idk_dropdown_danger">' +
					'<a href="#" data ="' +
					siteUrl +
					'idkadmin/products?page=archive&id=' +
					productId +
					'&table_page=' +
					tablePage +
					'" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link">' +
					'<i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj' +
					'</a>' +
					'</li>' +
					'</ul>' +
					'</div>';
				this.data(data);
			});

			$('.archive').click(function () {
				let addressValue = $(this).attr('data');
				document.getElementById('archive_link').href = addressValue;
			});
		},
		order: [[1, 'asc']],
		columnDefs: [
			{
				targets: [7, 8, 9],
				visible: false
			}
		],
		aoColumns: [
			{
				width: '5%',
				bSortable: false
			},
			{
				width: '25%'
			},
			{
				width: '15%'
			},
			{
				width: '15%'
			},
			{
				width: '15%'
			},
			{
				width: '15%'
			},
			{
				width: '10%',
				bSortable: false
			}
		],
		displayStart: tablePage * 10,
		language: {
			processing: 'Učitavanje...'
		}
	});
	/*********************************************
	 * 			DATATABLES - PRODUCTS LIST END
	 *********************************************/

	/*********************************************
	 * 			DATATABLES - CLIENTS LIST START
	 *********************************************/
	//Filling the datatable
	siteUrl = $('#site_url').val();
	tablePage = $('#table_page').val();

	let tableClients = $('#idk_clients_table').DataTable({
		responsive: true,
		processing: true,
		serverSide: true,
		ajax: 'server_clients.php',
		drawCallback: function (settings) {
			tablePage = tableClients.page.info().page;

			tableClients.rows().every(function (rowIdx, tableLoop, rowLoop) {
				let data = this.data();
				let clientImage = data[0];
				let clientName = data[1];
				let clientId = data[5];
				let clientAddress = data[6] ? data[6] + ', ' : '';
				let clientPostalCode = data[7] ? data[7] : '';
				let clientCity = data[2] ? data[2] + ', ' : '';
				let clientRegion = data[8] ? data[8] + ', ' : '';
				let clientCountry = data[9] ? data[9] : '';
				data[0] = '<a href="' + siteUrl + 'idkadmin/clients?page=open&id=' + clientId + '"><img class="idk_profile_img" src="' + siteUrl + 'idkadmin/files/clients/images/' + clientImage + '"></img></a>';
				data[1] = '<a href="' + siteUrl + 'idkadmin/clients?page=open&id=' + clientId + '&table_page=' + tablePage + '">' + clientName + '</a>';
				data[2] = clientAddress + '<br>' + clientPostalCode + ' ' + clientCity + '<br>' + clientRegion + '<br>' + clientCountry;
				data[5] =
					'<div class="btn-group material-btn-group">' +
					'<button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown">' +
					'<i class="fa fa-cogs fa-lg" aria-hidden="true"></i>' +
					'<span class="caret material-btn__caret"></span>' +
					'</button>' +
					'<ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">' +
					'<li>' +
					'<a href="' +
					siteUrl +
					'idkadmin/clients?page=open&id=' +
					clientId +
					'&table_page=' +
					tablePage +
					'" class="material-dropdown-menu__link">' +
					'<i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori' +
					'</a>' +
					'</li>' +
					'<li>' +
					'<a href="' +
					siteUrl +
					'idkadmin/clients?page=edit&id=' +
					clientId +
					'&table_page=' +
					tablePage +
					'" class="material-dropdown-menu__link">' +
					'<i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi' +
					'</a>' +
					'</li>' +
					'<li class="idk_dropdown_danger">' +
					'<a href="#" data ="' +
					siteUrl +
					'idkadmin/clients?page=archive&id=' +
					clientId +
					'&table_page=' +
					tablePage +
					'" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link">' +
					'<i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj' +
					'</a>' +
					'</li>' +
					'</ul>' +
					'</div>';
				this.data(data);
			});

			$('.archive').click(function () {
				let addressValue = $(this).attr('data');
				document.getElementById('archive_link').href = addressValue;
			});
		},
		order: [[1, 'asc']],
		columnDefs: [
			{
				targets: [6, 7, 8, 9],
				visible: false
			}
		],
		aoColumns: [
			{
				width: '5%',
				bSortable: false
			},
			{
				width: '25%'
			},
			{
				width: '20%'
			},
			{
				width: '20%'
			},
			{
				width: '20%'
			},
			{
				width: '10%',
				bSortable: false
			}
		],
		displayStart: tablePage * 10,
		language: {
			processing: 'Učitavanje...'
		}
	});
	/*********************************************
	 * 			DATATABLES - CLIENTS LIST END
	 *********************************************/
	//Azuriranje kosarice
	$('#idk_btn_edit_order').on('click', function () {
		//Spremanje vrijednosti rabata svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productRabat = document.getElementsByClassName('idk_product_rabat_percentage');
		let productsRabatsArray = document.getElementById('idk_products_rabats_array');
		let productRabatArray = new Array();

		for (const rabat of productRabat) {
			productRabatArray.push(rabat.value);
			if (productRabatArray.length > 0) productsRabatsArray.value = productRabatArray;
		}

		//Spremanje vrijednosti stare kolicine svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productQuantityOld = document.getElementsByClassName('idk_product_quantity_old');
		let productsQuantitiesArrayOld = document.getElementById('idk_products_quantities_array_old');
		let productQuantityArrayOld = new Array();

		for (const quantity of productQuantityOld) {
			productQuantityArrayOld.push(quantity.value);
			if (productQuantityArrayOld.length > 0) productsQuantitiesArrayOld.value = productQuantityArrayOld;
		}

		//Spremanje vrijednosti kolicine svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productQuantity = document.getElementsByClassName('idk_product_quantity');
		let productsQuantitiesArray = document.getElementById('idk_products_quantities_array');
		let productQuantityArray = new Array();

		for (const quantity of productQuantity) {
			productQuantityArray.push(quantity.value);
			if (productQuantityArray.length > 0) productsQuantitiesArray.value = productQuantityArray;
		}

		//Spremanje vrijednosti id-a svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productId = document.getElementsByClassName('idk_product_id');
		let productsIdsArray = document.getElementById('idk_products_ids_array');
		let productIdArray = new Array();

		for (const id of productId) {
			productIdArray.push(id.value);
			if (productIdArray.length > 0) productsIdsArray.value = productIdArray;
		}

		//Spremanje vrijednosti cijene svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productPrice = document.getElementsByClassName('idk_product_price');
		let productsPricesArray = document.getElementById('idk_products_prices_array');
		let productPriceArray = new Array();

		for (const price of productPrice) {
			productPriceArray.push(price.value);
			if (productPriceArray.length > 0) productsPricesArray.value = productPriceArray;
		}

		//Spremanje vrijednosti tax value-a svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productTaxValue = document.getElementsByClassName('idk_product_tax_percentage');
		let productsTaxValuesArray = document.getElementById('idk_products_tax_percentages_array');
		let productTaxValueArray = new Array();

		for (const taxValue of productTaxValue) {
			productTaxValueArray.push(taxValue.value);
			if (productTaxValueArray.length > 0) productsTaxValuesArray.value = productTaxValueArray;
		}
	});

	//Azuriranje kosarice
	$('#idk_btn_edit_offer').on('click', function () {
		//Spremanje vrijednosti rabata svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productRabat = document.getElementsByClassName('idk_product_rabat_percentage');
		let productsRabatsArray = document.getElementById('idk_products_rabats_array');
		let productRabatArray = new Array();

		for (const rabat of productRabat) {
			productRabatArray.push(rabat.value);
			if (productRabatArray.length > 0) productsRabatsArray.value = productRabatArray;
		}

		//Spremanje vrijednosti stare kolicine svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productQuantityOld = document.getElementsByClassName('idk_product_quantity_old');
		let productsQuantitiesArrayOld = document.getElementById('idk_products_quantities_array_old');
		let productQuantityArrayOld = new Array();

		for (const quantity of productQuantityOld) {
			productQuantityArrayOld.push(quantity.value);
			if (productQuantityArrayOld.length > 0) productsQuantitiesArrayOld.value = productQuantityArrayOld;
		}

		//Spremanje vrijednosti kolicine svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productQuantity = document.getElementsByClassName('idk_product_quantity');
		let productsQuantitiesArray = document.getElementById('idk_products_quantities_array');
		let productQuantityArray = new Array();

		for (const quantity of productQuantity) {
			productQuantityArray.push(quantity.value);
			if (productQuantityArray.length > 0) productsQuantitiesArray.value = productQuantityArray;
		}

		//Spremanje vrijednosti id-a svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productId = document.getElementsByClassName('idk_product_id');
		let productsIdsArray = document.getElementById('idk_products_ids_array');
		let productIdArray = new Array();

		for (const id of productId) {
			productIdArray.push(id.value);
			if (productIdArray.length > 0) productsIdsArray.value = productIdArray;
		}

		//Spremanje vrijednosti cijene svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productPrice = document.getElementsByClassName('idk_product_price');
		let productsPricesArray = document.getElementById('idk_products_prices_array');
		let productPriceArray = new Array();

		for (const price of productPrice) {
			productPriceArray.push(price.value);
			if (productPriceArray.length > 0) productsPricesArray.value = productPriceArray;
		}

		//Spremanje vrijednosti tax value-a svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productTaxValue = document.getElementsByClassName('idk_product_tax_percentage');
		let productsTaxValuesArray = document.getElementById('idk_products_tax_percentages_array');
		let productTaxValueArray = new Array();

		for (const taxValue of productTaxValue) {
			productTaxValueArray.push(taxValue.value);
			if (productTaxValueArray.length > 0) productsTaxValuesArray.value = productTaxValueArray;
		}
	});
});
