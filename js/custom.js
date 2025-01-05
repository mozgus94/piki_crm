'use strict';

// Inicijalizacija glavnog slidera
$(document).ready(function () {
	$('.idk_main_slider').slick({
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		speed: 500,
		dots: true,
		cssEase: 'linear',
		autoplay: true,
		autoplaySpeed: 2500,
		arrows: true,
		nextArrow: $('.next')
	});

	// Promjena kolicine proizvoda u kosarici - minus 1
	$('.idk_cart_item_quantity .lnr-circle-minus').on('click touch', function () {
		let inputValueTemp = $(this).closest('.idk_cart_item_quantity').children('.idk_cart_item_quantity_number').val();
		if (inputValueTemp >= 1) {
			let inputValue = parseFloat($(this).closest('.idk_cart_item_quantity').children('.idk_cart_item_quantity_number').val()) - 1;
			$(this).closest('.idk_cart_item_quantity').children('.idk_cart_item_quantity_number').val(inputValue);
		}
	});

	// Promjena kolicine proizvoda u kosarici - plus 1
	$('.idk_cart_item_quantity .lnr-plus-circle').on('click touch', function () {
		let inputValueTemp = $(this).closest('.idk_cart_item_quantity').children('.idk_cart_item_quantity_number').val();
		if (inputValueTemp >= 0) {
			let inputValue = parseFloat($(this).closest('.idk_cart_item_quantity').children('.idk_cart_item_quantity_number').val()) + 1;
			$(this).closest('.idk_cart_item_quantity').children('.idk_cart_item_quantity_number').val(inputValue);
		} else {
			let inputValue = 0;
			$(this).closest('.idk_cart_item_quantity').children('.idk_cart_item_quantity_number').val(inputValue);
		}
	});

	// Promjena strelice gore-dolje kod opisa na stranici pojedinacnih proizvoda
	$('.idk_product_desc_section .card-header button').on('click touch', function () {
		$('.idk_product_desc_section .card-header button').children().removeClass('lnr-chevron-up');
		$('.idk_product_desc_section .card-header button').children().addClass('lnr-chevron-down');
		$(this).children().removeClass('lnr-chevron-down');
		$(this).children().addClass('lnr-chevron-up');
		if ($(this).closest('.card').children('.collapse').hasClass('show')) {
			$(this).closest('.card').children('.card-header').find('.lnr').removeClass('lnr-chevron-up');
			$(this).closest('.card').children('.card-header').find('.lnr').addClass('lnr-chevron-down');
		}
	});

	//Promjena vidljivosti menija
	$('.idk_menu_toggler').on('click touch', function () {
		let nav = document.getElementById('idk_menu_overlay');
		$(this).toggleClass('idk_active');
		$('header').toggleClass('d-none');
		$('main').toggleClass('d-none');
		if (nav.offsetWidth == 0) {
			nav.style.width = '100%';
			if ($(window).height() < 400) {
				nav.style.minHeight = 'calc(100vh + 630px)';
			} else {
				nav.style.minHeight = 'calc(100vh + 350px)';
			}
		} else {
			nav.style.width = '0';
			nav.style.minHeight = '0';
		}
	});

	//Alert boxovi za input sliku
	$('#employee_image').change(function () {
		let ext = $('#employee_image').val().split('.').pop().toLowerCase();

		if ($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
			$('#idk_alert_ext').removeClass('d-none');
			this.value = null;
		} else {
			$('#idk_alert_ext').addClass('d-none');
		}

		let f = this.files[0];

		if (f.size > 20388608 || f.fileSize > 20388608) {
			$('#idk_alert_size').removeClass('d-none');
			this.value = null;
		} else {
			$('#idk_alert_size').addClass('d-none');
		}
	});

	//Dodavanje klase show na list accordion
	$('.idk_list_items_section #list_accordion .collapse:first').addClass('show');

	//Azuriranje rabata na input rabata u idk_order_rabat_percentage
	$('.idk_order_rabat_percentage').on('input', function () {
		let rabat = $(this).val();
		$('.idk_product_rabat_percentage').val(rabat);
	});

	//Azuriranje kosarice
	$('.idk_btn_update_order').on('click touch', function () {
		//Spremanje vrijednosti rabata svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productRabat = document.getElementsByClassName('idk_product_rabat_percentage');
		let productsRabatsArray = document.getElementById('idk_products_rabats_array');
		let productRabatArray = new Array();

		for (const rabat of productRabat) {
			productRabatArray.push(rabat.value);
			if (productRabatArray.length > 0) productsRabatsArray.value = productRabatArray;
		}

		//Spremanje vrijednosti kolicine svakog proizvoda u novi niz i dodijeljivanje value skrivenom inputu
		let productQuantity = document.getElementsByClassName('idk_cart_item_quantity_number');
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

	//Skrivanje top bara i foot bara ako je search input u fokusu pri ucitavanju stranice
	if ($('input[name="search"]').is(':focus')) {
		// $(this).closest('.idk_page_title_container').css('margin-top', '20px');
		// $('.idk_top_bar').css('margin-top', '-62px');
		$('.idk_foot_bar').css('margin-bottom', '-90px');
		// window.location.href = '#idk_top_bar';
	}

	//Skrivanje top bara i foot bara na focus search inputa
	$('input[name="search"]').on('focusin', function () {
		// $(this).closest('.idk_page_title_container').css('margin-top', '20px');
		// $('.idk_top_bar').css('margin-top', '-62px');
		$('.idk_foot_bar').css('margin-bottom', '-90px');
	});

	//Otkrivanje top bara i foot bara na focus search inputa
	$('input[name="search"]').on('focusout', function () {
		// $(this).closest('.idk_page_title_container').css('margin-top', '90px');
		// $('.idk_top_bar').css('margin-top', '0');
		$('.idk_foot_bar').css('margin-bottom', '0');
	});

	//Postavljanje vrijednosti search_parameter
	let searchParameterStatic = $('input[name="search"]').val();
	$('#search_parameter').attr('value', searchParameterStatic);
	$('#search_parameter_cart').attr('value', searchParameterStatic);

	//Slanje search_parameter u do.php prilikom add_item_to_list_from_search i add_item_to_cart_temp_from_search
	$('input[name="search"]').on('input', function () {
		let searchParameter = $(this).val();
		$('#search_parameter').attr('value', searchParameter);
		$('#search_parameter_cart').attr('value', searchParameter);
	});

	//Submitanje forme za odabir klijenta na klik option elementa u selectu
	$('#selectClient').on('change', function () {
		$('.idk_select_client_form').submit();
	});

	//Delete list on click
	$('.idk_delete_list').click(function () {
		var addressValue = $(this).attr('data');
		document.getElementById('idk_delete_list_link').href = addressValue;
	});

	/*************************************************************************
														SKLADISTE 
	*************************************************************************/

	//Promjena kolicine i checkiranje proizvoda na input barkoda
	$('#idk_skladiste_barkod_form').on('submit', function (e) {
		e.preventDefault();
		e.stopPropagation();
		let barcodeValue = $('#idk_skladiste_barkod_input').val();
		let productBarcodes = document.getElementsByClassName('idk_order_product_barcode_value');

		for (let i = 0; i < productBarcodes.length; i++) {
			let productBarcodeValue = productBarcodes[i].innerHTML;

			if (productBarcodeValue === barcodeValue) {
				let quantity = $('.idk_order_product_barcode_value_' + productBarcodeValue)
					.closest('.col-10')
					.find('.idk_order_quantity_value');
				let quantityValue = quantity.html();

				if (quantityValue == 1) {
					quantityValue -= 1;
					quantity.html(quantityValue);
					quantity
						.closest('.row')
						.find('input[name="check_product"')
						.attr('checked', function (index, attr) {
							return attr == 'checked' ? false : 'checked';
						});

					quantity.closest('.row').find('.col-10').addClass('idk_strike_through');

					let numberOfCheckboxes = $('input[name="check_product"]').length;

					if ($('input[name="check_product"]:checked').length == numberOfCheckboxes) {
						$('#idk_finish_order').removeClass('disabled');
					} else {
						$('#idk_finish_order').addClass('disabled');
					}
				} else if (quantityValue == 0) {
					quantity
						.closest('.row')
						.find('input[name="check_product"')
						.attr('checked', function (index, attr) {
							return attr == 'checked' ? 'checked' : 'checked';
						});
					quantity.closest('.row').find('.col-10').addClass('idk_strike_through');
				} else {
					quantityValue -= 1;
					quantity.html(quantityValue);
				}
			}
		}

		$('#idk_skladiste_barkod_input').val('');
	});

	//Mileage datatable
	$('#idk_table_mileage').DataTable({
		language: {
			decimal: ',',
			emptyTable: 'Nema unosa za prikaz',
			info: 'Prikazujem _START_ do _END_ od _TOTAL_ unosa',
			infoEmpty: 'Prikazujuem 0 do 0 od 0 unosa',
			infoFiltered: '(filtrirano od _MAX_ ukupnih unosa)',
			infoPostFix: '',
			thousands: '.',
			lengthMenu: 'Prikaži _MENU_ unosa',
			loadingRecords: 'Učitavanje...',
			processing: 'Procesiranje...',
			search: 'Traži:',
			zeroRecords: 'Nema pronađenih rezultata',
			paginate: {
				first: 'Prva',
				last: 'Posljednja',
				next: 'Sljedeća',
				previous: 'Prethodna'
			},
			aria: {
				sortAscending: ': rastući redoslijed',
				sortDescending: ': opadajući redoslijed'
			}
		},
		order: [0, 'desc']
	});

	//Offers datatable
	$('#idk_table_offers').DataTable({
		language: {
			decimal: ',',
			emptyTable: 'Nema unosa za prikaz',
			info: 'Prikazujem _START_ do _END_ od _TOTAL_ unosa',
			infoEmpty: 'Prikazujuem 0 do 0 od 0 unosa',
			infoFiltered: '(filtrirano od _MAX_ ukupnih unosa)',
			infoPostFix: '',
			thousands: '.',
			lengthMenu: 'Prikaži _MENU_ unosa',
			loadingRecords: 'Učitavanje...',
			processing: 'Procesiranje...',
			search: 'Traži:',
			zeroRecords: 'Nema pronađenih rezultata',
			paginate: {
				first: 'Prva',
				last: 'Posljednja',
				next: 'Sljedeća',
				previous: 'Prethodna'
			},
			aria: {
				sortAscending: ': rastući redoslijed',
				sortDescending: ': opadajući redoslijed'
			}
		},
		order: [0, 'desc']
	});

	//Orders datatable
	$('#idk_table_orders').DataTable({
		language: {
			decimal: ',',
			emptyTable: 'Nema unosa za prikaz',
			info: 'Prikazujem _START_ do _END_ od _TOTAL_ unosa',
			infoEmpty: 'Prikazujuem 0 do 0 od 0 unosa',
			infoFiltered: '(filtrirano od _MAX_ ukupnih unosa)',
			infoPostFix: '',
			thousands: '.',
			lengthMenu: 'Prikaži _MENU_ unosa',
			loadingRecords: 'Učitavanje...',
			processing: 'Procesiranje...',
			search: 'Traži:',
			zeroRecords: 'Nema pronađenih rezultata',
			paginate: {
				first: 'Prva',
				last: 'Posljednja',
				next: 'Sljedeća',
				previous: 'Prethodna'
			},
			aria: {
				sortAscending: ': rastući redoslijed',
				sortDescending: ': opadajući redoslijed'
			}
		},
		order: [0, 'desc']
	});

	//Products datatable
	let url = window.location.href;
	if (url.includes('/products')) {
		$.ajax({
			url: 'getProducts.php',
			method: 'get',
			dataType: 'text',
			success: function (data) {
				$('#idk_table_products').html('<thead><tr><th>ID</th><th>Naziv proizvoda</th><th>Šifra proizvoda</th><th></th><th></th></tr></thead><tbody></tbody>');
				$('#idk_table_products tbody').html(data);
				$('#idk_table_products').DataTable({
					language: {
						decimal: ',',
						emptyTable: 'Nema unosa za prikaz',
						info: 'Prikazujem _START_ do _END_ od _TOTAL_ unosa',
						infoEmpty: 'Prikazujuem 0 do 0 od 0 unosa',
						infoFiltered: '(filtrirano od _MAX_ ukupnih unosa)',
						infoPostFix: '',
						thousands: '.',
						lengthMenu: 'Prikaži _MENU_ unosa',
						loadingRecords: 'Učitavanje...',
						processing: 'Procesiranje...',
						search: 'Traži:',
						zeroRecords: 'Nema pronađenih rezultata',
						paginate: {
							first: 'Prva',
							last: 'Posljednja',
							next: 'Sljedeća',
							previous: 'Prethodna'
						},
						aria: {
							sortAscending: ': rastući redoslijed',
							sortDescending: ': opadajući redoslijed'
						}
					},
					order: [0, 'asc']
				});
			}
		});
	}

	//Searching
	$('#search').on('input', function (e) {
		e.preventDefault();
		let txt = e.target.value;
		$('#idk_search_results').html('');
		if (txt.length < 3) {
		} else {
			$.ajax({
				url: 'getSearchResults.php',
				method: 'post',
				data: {
					search: txt
				},
				dataType: 'text',
				success: function (data) {
					$('#idk_search_results').html(data);
				}
			});
		}
	});

	//Tagify
	let inputDebtEquipment = document.querySelector('#debt_equipment');

	if (inputDebtEquipment) {
		let tagify = new Tagify(inputDebtEquipment, {
			whitelist: [],
			delimiters: null,
			originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(''),
			maxTags: 1
		});

		$.ajax({
			url: 'getTags.php',
			dataType: 'json',
			method: 'post',
			cache: false,
			success: function (resp) {
				if (resp.length > 0) {
					for (let i = 0; i < resp.length; i++) {
						tagify.settings.whitelist.push(resp[i]);
					}
				}
			},
			error: function (e) {
				console.log('Error: ' + e);
			}
		});
	}

	//Datum rute
	$('#date').flatpickr({
		dateFormat: 'd.m.Y.',
		locale: 'bs'
	});

	$('#date').on('change', function() {
		$(this).parent('form').submit();
});
});


