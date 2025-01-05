<?php

include("includes/functions.php");
include("includes/common.php");

$output = '';
$product_supplier = !empty($_POST['product_supplier']) ? $_POST['product_supplier'] : NULL;
$report_date_from = !empty($_POST['report_date_from']) ? $_POST['report_date_from'] : NULL;
$report_date_to = !empty($_POST['report_date_to']) ? $_POST['report_date_to'] : NULL;
$type = !empty($_POST['type']) ? $_POST['type'] : NULL;

$owner_query = $db->prepare("
  SELECT owner_name, owner_image
  FROM idk_owner");

$owner_query->execute();

$owner = $owner_query->fetch();

$owner_name = $owner['owner_name'];
$owner_image = $owner['owner_image'];

if (isset($type) and $type == "print") {
  $output .= '
  <div id="print_header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-6">
          <h3>Izvještaj po dobavljaču</h3>
          <p class="idk_margin_top30">
            <strong>Unaviva d.o.o.</strong> <br>
            Dr. Irfana Ljubijankića 87 <br>
            77000 Bihać <br>
            Tel: 00 387 37 961 131 <br>
            E-Mail: info@unaviva.ba <br>
            Web: www.unaviva.ba <br>
          </p>
        </div>
        <div class="col-xs-6 text-right">
          <img src="' . getSiteUrlr() . 'idkadmin/files/owners/images/' . $owner_image . '" class="idk_print_logo" alt="' . $owner_name . '">
          <p class="idk_margin_top30" id="idk_print_header_right_col">
            <strong>Datum</strong><br>
            ' . date('d.m.Y.') . '
          </p>
        </div>
      </div>
    </div>
  </div>

  <div id="print_main">
    <div class="container-fluid">
      <div class="row idk_margin_top50">
        <div class="col-xs-12">
          <h5>Parametri</h5>
          <div class="row idk_margin_top30">
            <div class="col-xs-4">
              <p><strong>Dobavljač:</strong></p>
            </div>
            <div class="col-xs-8">
              <p>' . $product_supplier . '</p>
            </div>
          </div>';

  if (isset($report_date_from)) {
    $output .= '
          <div class="row">
            <div class="col-xs-4">
              <p><strong>Datum od:</strong></p>
            </div>
            <div class="col-xs-8">
              <p>' . date('d.m.Y.', strtotime($report_date_from)) . '</p>
            </div>
          </div>';
  }

  if (isset($report_date_to)) {
    $output .= '
          <div class="row">
            <div class="col-xs-4">
              <p><strong>Datum do:</strong></p>
            </div>
            <div class="col-xs-8">
              <p>' . date('d.m.Y.', strtotime($report_date_to)) . '</p>
            </div>
          </div>';
  }

  $output .= '
        </div>
      </div>

      <div class="row idk_margin_top50">
        <div class="col-xs-12">
          <h5>Izvještaj</h5>
        </div>
        <div class="col-xs-12 text-center idk_margin_top30">
          <table width="100%" class="tg" id="idk_print_table">
            <thead>
              <tr>
                <th width="10%" class="tg-kj9p">ID proizvoda</th>
                <th width="35%" class="tg-kj9p">Naziv proizvoda</th>
                <th width="15%" class="tg-kj9p">Proizvod naručen puta</th>
                <th width="15%" class="tg-kj9p">Ukupno naručeno proizvoda</th>
                <th width="25%" class="tg-nj7c">Ukupan iznos</th>
              </tr>
            </thead>
            <tbody>';
}

if (isset($product_supplier)) {
  $query = $db->prepare("
    SELECT t1.product_id, t1.product_name
    FROM idk_product_order t1
    INNER JOIN idk_order t2
    ON t1.order_id = t2.order_id
    INNER JOIN idk_product t3
    ON t1.product_id = t3.product_id
    WHERE (t2.created_at BETWEEN :report_date_from AND :report_date_to) AND t2.order_status != 0 AND t3.product_supplier = :product_supplier
    GROUP BY product_id");

  if (isset($report_date_from) and isset($report_date_to)) {
    $query->execute(array(
      ':product_supplier' => $product_supplier,
      ':report_date_from' => date('Y-m-d', strtotime($report_date_from)),
      ':report_date_to' => date('Y-m-d', strtotime($report_date_to))
    ));
  } elseif (isset($report_date_from)) {

    $query->execute(array(
      ':product_supplier' => $product_supplier,
      ':report_date_from' => date('Y-m-d', strtotime($report_date_from)),
      ':report_date_to' => date('Y-m-d')
    ));
  } elseif (isset($report_date_to)) {

    $query->execute(array(
      ':product_supplier' => $product_supplier,
      ':report_date_from' => date('Y-m-d', strtotime('1970-01-01')),
      ':report_date_to' => date('Y-m-d', strtotime($report_date_to))
    ));
  } else {
    $query = $db->prepare("
      SELECT t1.product_id, t1.product_name
      FROM idk_product_order t1
      INNER JOIN idk_order t2
      ON t1.order_id = t2.order_id
      INNER JOIN idk_product t3
      ON t1.product_id = t3.product_id
      WHERE t2.order_status != 0 AND t3.product_supplier = :product_supplier
      GROUP BY product_id");

    $query->execute(array(
      ':product_supplier' => $product_supplier
    ));
  }

  $number_of_rows = $query->rowCount();

  if ($number_of_rows > 0) {
    while ($row = $query->fetch()) {

      $product_id = $row['product_id'];
      $product_name = $row['product_name'];
      $product_total_quantity = 0;
      $product_total_price = 0;

      if (isset($type) and $type == "print") {

        $output .= '
          <tr>
            <td width="10%" class="tg-2qw4">' . $product_id . '</td>
            <td width="35%" class="tg-2qw4">' . $product_name . '</td>';
      } else {

        $output .= '
        <tr>
          <td>
            <a href="' . getSiteUrlr() . 'idkadmin/products?page=open&id=' . $product_id . '">' . $product_id . '</a>
          </td>
          <td>
            <a href="' . getSiteUrlr() . 'idkadmin/products?page=open&id=' . $product_id . '">' . $product_name . '</a>
          </td>';
      }

      $query_products = $db->prepare("
        SELECT product_price, product_rabat_percentage, product_quantity, product_unit, product_tax_percentage
        FROM idk_product_order
        WHERE product_id = :product_id");

      $query_products->execute(array(
        ':product_id' => $product_id
      ));

      $number_of_rows_products = $query_products->rowCount();

      if (isset($type) and $type == "print") {

        $output .= '
          <td width="15%" class="tg-2qw4">' . $number_of_rows_products . '</td>';
      } else {

        $output .= '
          <td>' . $number_of_rows_products . '</td>';
      }

      if ($number_of_rows_products > 0) {
        while ($row_product = $query_products->fetch()) {

          $product_price = $row_product['product_price'];
          $product_rabat_percentage = $row_product['product_rabat_percentage'];
          $product_quantity = $row_product['product_quantity'];
          $product_unit = $row_product['product_unit'];
          $product_tax_percentage = $row_product['product_tax_percentage'];
          $product_total_quantity += $product_quantity;
          $product_total_price += ($product_price * $product_quantity) - (($product_price * $product_quantity) * $product_rabat_percentage / 100) + (($product_price * $product_quantity) * $product_tax_percentage / 100);
        }
      }

      if (isset($type) and $type == "print") {

        $output .= '
            <td width="15%" class="tg-2qw4">' . $product_total_quantity . ' ' . $product_unit . '</td>
            <td width="25%" class="tg-sfdd">' . number_format($product_total_price, 2, ',', '.') . ' KM</td>
          </tr>';
      } else {
        $output .= '
        <td data-sort="' . $product_total_quantity . '">
          ' . $product_total_quantity . ' ' . $product_unit . '
        </td>
        <td data-sort="' . $product_total_price . '">
          ' . number_format($product_total_price, 2, ',', '.') . ' KM
        </td>
        </tr>';
      }
    }
  }
}

if (isset($type) and $type == "print") {
  $output .= '</tbody>
            </table>
          </div>
        </div>
      </div>
    </div>';
}
echo $output;
