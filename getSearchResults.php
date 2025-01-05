<?php

include("includes/functions.php");
include("includes/common.php");

$output = '';

$query = $db->prepare("
  SELECT product_id, product_name, product_sku, product_price, product_image, product_currency, product_quantity, product_unit, product_tax_name, product_tax_percentage
  FROM idk_product
  WHERE (product_name LIKE :search OR product_sku LIKE :search OR product_supplier LIKE :search) AND product_active = :product_active
  ORDER BY product_name");

$query->execute(array(
  ':search' => "%" . $_POST['search'] . "%",
  ':product_active' => 1
));

$number_of_rows = $query->rowCount();

if ($number_of_rows > 0) {
  while ($product = $query->fetch()) {

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

    $output .= '
      <div class="col-10 col-sm-6 col-md-4">
        <div class="card">
              <a href="' . getSiteUrlr() . 'product?id=' . $product_id . '">
                <img class="card-img-top"
                src="' . getSiteUrlr() . 'idkadmin/files/products/images/' . $product_image . '" alt="' . $product_name . ' slika">
              </a>
              
              <button type="button" class="btn idk_add_to_list_btn" data-toggle="modal" data-target="#addToListModal_' . $product_id . '">
                <span class="lnr lnr-heart"></span>
                </button>

                <div class="modal fade" id="addToListModal_' . $product_id . '" tabindex="-1" role="dialog"
                  aria-labelledby="addToListModal_' . $product_id . 'Label" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="addToListModal_' . $product_id . 'Label">
                          Dodaj u listu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">

                        <form action="' . getSiteUrlr() . 'do.php?form=add_item_to_list" method="POST">

                          <input type="hidden" name="page" id="page" value="search">
                          <input type="hidden" name="product_id" id="product_id" value="' . $product_id . '">
                          <input type="hidden" name="product_unit" id="product_unit" value="' . $product_unit . '">
                          <input type="hidden" name="search_parameter" id="search_parameter" value="' . $_POST['search'] . '">

                          <div class="form-group">
                            <label class="sr-only" for="selectList_' . $product_id . '">Izaberi
                              listu*</label>
                            <div class="input-group mb-2">
                              <div class="input-group-prepend">
                                <label class="input-group-text" for="selectList_' . $product_id . '"><span
                                    class="lnr lnr-heart"></span></label>
                              </div>
                              <select class="custom-select" name="list_id" id="selectList_' . $product_id . '" required>

                                <option value="">Izaberi listu ...</option>';

    if (isset($logged_client_id) and $logged_client_id != 0) {

      $list_query = $db->prepare("
        SELECT list_id, list_name
        FROM idk_list
        WHERE client_id = :client_id AND employee_id IS NULL");

      $list_query->execute(array(
        ':client_id' => $logged_client_id
      ));
    } elseif (isset($_COOKIE['idk_session_front_client'])) {

      $list_query = $db->prepare("
        SELECT list_id, list_name
        FROM idk_list
        WHERE client_id = :client_id AND employee_id IS NOT NULL");

      $list_query->execute(array(
        ':client_id' => $_COOKIE['idk_session_front_client']
      ));
    }

    while ($list = $list_query->fetch()) {

      $list_id = $list['list_id'];
      $list_name = $list['list_name'];

      $output .= '<option value="' . $list_id . '">' . $list_name . '</option>';
    }

    $output .= '
                 </select>
            </div>
          </div>

          <div class="form-group">
            <label class="sr-only" for="product_on_list_quantity_' . $product_id . '">Količina*</label>
            <div class="input-group mb-2">
              <div class="input-group-prepend">
                <div class="input-group-text"><span class="lnr lnr-layers"></span></div>
              </div>
              <input type="number" class="form-control" name="product_on_list_quantity"
                id="product_on_list_quantity_' . $product_id . '" min="0"
                placeholder="Količina (' . $product_unit . ')*" required>
            </div>
          </div>
          <button type="submit" class="btn idk_btn btn-block">DODAJ</button>

        </form>

      </div>
    </div>
  </div>
</div>

<div class="card-body">
  <a href="' . getSiteUrlr() . 'product?id=' . $product_id . '">
    <h5 class="card-title">' . $product_name . '</h5>
    <h5 class="card-title"><small>Šifra: ' . $product_sku . '</small></h5>';

    if (isset($logged_client_show_quantity) and $logged_client_show_quantity == 0) {
    } else {

      $output .=  '<h5 class="card-title"><small>Stanje: ' . $product_quantity . ' ' . $product_unit . '</small></h5>';
    }

    $output .= '</a>
  <div class="idk_product_card_bottom_row">
    <div class="idk_product_card_price">';

    if (isset($logged_client_show_price) and $logged_client_show_price == 0) {
    } else {

      $output .=  '<p class="card-text">VPC: ' . number_format($product_price, 3, ',', '.') . ' ' . $product_currency . '</p>';
    }

    $output .= '</div>
    <div class="idk_product_card_add_to_cart">

    <button type="button" class="btn idk_add_to_cart_btn" data-toggle="modal" data-target="#addToCartModal_' . $product_id . '">
      <span class="lnr lnr-cart"></span>
    </button>

    <div class="modal fade" id="addToCartModal_' . $product_id . '"
      tabindex="-1" role="dialog"
      aria-labelledby="addToCartModal_' . $product_id . 'Label"
      aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title"
                      id="addToCartModal_' . $product_id . 'Label">Dodaj u
                      košaricu</h5>
                  <button type="button" class="close" data-dismiss="modal"
                      aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                  </button>
              </div>
              <div class="modal-body">';

    if (isset($logged_client_id) and $logged_client_id != 0) {
      $client_id = $logged_client_id;
    } else {
      $client_id = $_COOKIE['idk_session_front_client'];
    }

    if (isset($logged_employee_id) and $logged_employee_id != 0) {
      //Check if product temp order exists
      $check_query = $db->prepare("
        SELECT product_id
        FROM idk_product_order_temp
        WHERE order_id IN (SELECT order_id FROM idk_order_temp WHERE client_id = :client_id AND employee_id = :employee_id) AND product_id = :product_id");

      $check_query->execute(array(
        ':client_id' => $client_id,
        ':product_id' => $product_id,
        ':employee_id' => $logged_employee_id
      ));
    } else {
      //Check if product temp order exists
      $check_query = $db->prepare("
        SELECT product_id
        FROM idk_product_order_temp
        WHERE order_id IN (SELECT order_id FROM idk_order_temp WHERE client_id = :client_id AND employee_id IS NULL) AND product_id = :product_id");

      $check_query->execute(array(
        ':client_id' => $client_id,
        ':product_id' => $product_id
      ));
    }

    $number_of_rows = $check_query->rowCount();

    if ($number_of_rows == 0) {

      $output .= '<form
                        action="' . getSiteUrlr() . 'do.php?form=add_item_to_cart_temp"
                        method="post">

                        <input type="hidden" name="page" id="page"
                            value="search">
                        <input type="hidden" name="search_parameter_cart" id="search_parameter_cart"
                            value="' . $_POST['search'] . '">
                        <input type="hidden" name="product_id" id="product_id"
                            value="' . $product_id . '">
                        <input type="hidden" name="product_name" id="product_name"
                            value="' . $product_name . '">
                        <input type="hidden" name="product_quantity" id="product_quantity"
                            value="' . $product_quantity . '">
                        <input type="hidden" name="product_currency"
                            id="product_currency"
                            value="' . $product_currency . '">
                        <input type="hidden" name="product_unit" id="product_unit"
                            value="' . $product_unit . '">
                        <input type="hidden" name="product_price" id="product_price"
                            value="' . $product_price . '">
                        <input type="hidden" name="product_tax_name"
                            id="product_tax_name"
                            value="' . $product_tax_name . '">
                        <input type="hidden" name="product_tax_percentage"
                            id="product_tax_percentage"
                            value="' . $product_tax_percentage . '">

                        <div class="form-group">
                            <label class="sr-only"
                                for="product_in_cart_temp_quantity">Količina*</label>
                            <div class="input-group mb-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><span
                                            class="lnr lnr-layers"></span></div>
                                </div>
                                <input type="number" class="form-control"
                                    name="product_in_cart_temp_quantity"
                                    id="product_in_cart_temp_quantity" min="0"
                                    placeholder="Količina (' . $product_unit . ')*"
                                    required>
                            </div>
                        </div>

                        <button type="submit"
                            class="btn idk_btn btn-block">DODAJ</button>

                  </form>';
    } else {
      $output .= '<h4 class="my-3">Proizvod je već dodan u košaricu!</h4>';
    }
    $output .= '
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                ';
  }

  echo $output;
} else {
  echo '<div class="col-12">
          <p>Nema pronađenih rezultata</p>
        </div>';
}
