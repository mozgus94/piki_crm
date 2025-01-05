<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
  $page = $_REQUEST["page"];
} else {
  header("Location: orders?page=list");
}

?>

<!DOCTYPE html>
<html>

<head>
  <?php include('includes/head.php'); ?>

</head>

<body>
  <header>
    <?php include('header.php'); ?>
  </header>
  <div id="sidebar">
    <?php include('menu.php'); ?>
  </div>
  <div id="content">
    <div class="container-fluid">
      <?php
      switch ($page) {



          /************************************************************
         * 							LIST ALL ORDERS
         * *********************************************************/
        case "list":
      ?>

          <div class="row">
            <div class="col-xs-8">
              <h1><i class="fa fa-shopping-cart idk_color_green" aria-hidden="true"></i> Narudžbe</h1>
            </div>
            <div class="col-xs-12">
              <hr>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="content_box">

                <div class="row">
                  <div class="col-xs-12 text-center legend">
                    <h1>Filtriraj:</h1>

                    <?php
                    $query = $db->prepare("
                      SELECT od_id, od_data, od_value
                      FROM idk_order_otherdata AS t1");

                    $query->execute();

                    while ($order_otherdata = $query->fetch()) {

                      $od_id = $order_otherdata['od_id'];
                      $od_data = $order_otherdata['od_data'];
                      $od_value = $order_otherdata['od_value'];

                    ?>

                      <a href="<?php getSiteUrl(); ?>idkadmin/orders?page=list&order_status=<?php echo $od_id; ?>"><button class="btn material-btn" style="background: <?php echo $od_value; ?>; color: #fff; text-shadow: 2px 2px rgba(0, 0, 0, 0.15);"><?php echo $od_data; ?></button></a>

                    <?php } ?>

                    <a href="<?php getSiteUrl(); ?>idkadmin/orders?page=list"><button class="btn btn-secondary material-btn">Sve</button></a>

                  </div>
                </div>

                <div class="row">
                  <div class="col-xs-12">

                    <!-- Success and error handling -->
                    <?php
                    if (isset($_GET['mess'])) {
                      $mess = $_GET['mess'];
                    } else {
                      $mess = 0;
                    }

                    if ($mess == 1) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste promijenili status narudžbe.</div>';
                    } elseif ($mess == 2) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
                    } elseif ($mess == 4) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali narudžbu.</div>';
                    } elseif ($mess == 5) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali narudžbe.</div>';
                    } elseif ($mess == 6) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Niste odabrali narudžbe za arhiviranje.</div>';
                    } elseif ($mess == 7) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste duplicirali narudžbu.</div>';
                    }
                    ?>

                    <!-- Filling the table with data -->
                    <script type="text/javascript">
                      $(document).ready(function() {
                        $('#idk_table').DataTable({

                          responsive: true,

                          "order": [
                            [1, "desc"]
                          ],

                          "bAutoWidth": false,

                          "aoColumns": [{
                              "width": "10%"
                            },
                            {
                              "width": "10%"
                            },
                            {
                              "width": "20%"
                            },
                            {
                              "width": "20%"
                            },
                            {
                              "width": "10%"
                            },
                            {
                              "width": "10%"
                            },
                            {
                              "width": "10%"
                            },
                            {
                              "width": "10%",
                              "bSortable": false
                            }
                          ]
                        });
                      });
                    </script>

                    <!-- Orders table -->
                    <table id="idk_table" class="display" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th></th>
                          <th>ID</th>
                          <th>Klijent</th>
                          <th>Komercijalista</th>
                          <th>Za platiti</th>
                          <th>Datum</th>
                          <th>Status</th>
                          <th></th>
                        </tr>
                      </thead>

                      <tbody>

                        <!-- Get data for order -->
                        <?php
                        if (isset($_GET['order_status'])) {

                          $order_status = $_GET['order_status'];

                          $query = $db->prepare("
                            SELECT t1.order_id, t1.client_id, t1.employee_id, t1.order_status, t1.order_total_price, t1.order_total_rabat, t1.order_total_tax, t1.order_to_pay, t1.created_at, t2.client_name, t3.od_value, t3.od_data
                            FROM idk_order t1, idk_client t2, idk_order_otherdata t3
                            WHERE t1.order_status = :order_status AND t1.order_active = :order_active AND t2.client_id = t1.client_id AND t1.order_status = t3.od_id");

                          $query->execute(array(
                            ':order_status' => $order_status,
                            ':order_active' => 1
                          ));
                        } else {

                          $query = $db->prepare("
                            SELECT t1.order_id, t1.client_id, t1.employee_id, t1.order_status, t1.order_total_price, t1.order_total_rabat, t1.order_total_tax, t1.order_to_pay, t1.created_at, t2.client_name, t3.od_value, t3.od_data
                            FROM idk_order t1, idk_client t2, idk_order_otherdata t3
                            WHERE t1.order_status != :order_status AND t1.order_active = :order_active AND t2.client_id = t1.client_id AND t1.order_status = t3.od_id");

                          $query->execute(array(
                            ':order_status' => 0,
                            ':order_active' => 1
                          ));
                        }

                        while ($order = $query->fetch()) {

                          $client_id = $order['client_id'];
                          $client_name = $order['client_name'];
                          $order_employee_id = $order['employee_id'];
                          $order_id = $order['order_id'];
                          $order_status = $order['order_status'];
                          $order_total_price = $order['order_total_price'];
                          $order_total_rabat = $order['order_total_rabat'];
                          $order_total_tax = $order['order_total_tax'];
                          $order_to_pay = $order['order_to_pay'];
                          $order_created_at = $order['created_at'];
                          $order_created_at_new_format = date('d.m.Y.', strtotime($order['created_at']));
                          $order_color = $order['od_value'];
                          $od_data = $order['od_data'];

                        ?>
                          <tr>
                            <td class="text-center">
                              <div class="main-container__column material-checkbox-group material-checkbox-group_success">
                                <input type="checkbox" id="checkbox_<?php echo $order_id; ?>" class="material-checkbox">
                                <label class="material-checkbox-group__label" for="checkbox_<?php echo $order_id; ?>"></label>
                              </div>
                            </td>
                            <td>
                              <?php echo $order_id; ?>
                            </td>
                            <td>
                              <a href="<?php getSiteUrl(); ?>idkadmin/orders?page=open&order_id=<?php echo $order_id; ?>"><?php echo $client_name; ?></a>
                            </td>
                            <td>
                              <?php
                              if (isset($order_employee_id)) {
                                $query_employee = $db->prepare("
                                SELECT employee_first_name, employee_last_name
                                FROM idk_employee
                                WHERE employee_id = :employee_id");

                                $query_employee->execute(array(
                                  ':employee_id' => $order_employee_id
                                ));

                                $row_employee = $query_employee->fetch();

                                echo $row_employee['employee_first_name'] . ' ' . $row_employee['employee_last_name'];
                              }
                              ?>
                            </td>
                            <td>
                              <?php echo number_format($order_to_pay, 2, ',', '.'); ?> KM
                            </td>
                            <td data-sort="<?php echo $order_created_at; ?>">
                              <?php echo $order_created_at_new_format; ?>
                            </td>
                            <td>
                              <button class="btn material-btn" style="width: 100%; height: 20px; background: <?php echo $order_color; ?>; cursor: auto;" data-toggle="tooltip" data-placement="top" title="<?php echo $od_data; ?>">
                              </button>
                            </td>
                            <td class="text-center">
                              <div class="btn-group material-btn-group">
                                <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
                                <ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
                                  <li>
                                    <a href="<?php getSiteUrl(); ?>idkadmin/orders?page=open&order_id=<?php echo $order_id; ?>" class="material-dropdown-menu__link">
                                      <i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori
                                    </a>
                                  </li>

                                  <li>
                                    <a href="<?php getSiteUrl(); ?>idkadmin/orders?page=edit&order_id=<?php echo $order_id; ?>" class="material-dropdown-menu__link">
                                      <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi
                                    </a>
                                  </li>

                                  <li class="idk_dropdown_danger">
                                    <a href="#" data="<?php getSiteUrl(); ?>idkadmin/orders?page=archive&id=<?php echo $order_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link">
                                      <i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj
                                    </a>
                                  </li>
                                </ul>
                              </div>
                            </td>
                          </tr>

                        <?php } ?>

                        <script>
                          // Archiving
                          $(".archive").click(function() {
                            var addressValue = $(this).attr("data");
                            document.getElementById("archive_link").href = addressValue;
                          });
                        </script>
                        <!-- Modal -->
                        <div class="modal material-modal material-modal_danger fade" id="archiveModal">
                          <div class="modal-dialog">
                            <div class="modal-content material-modal__content">
                              <div class="modal-header material-modal__header">
                                <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title material-modal__title">Arhiviranje</h4>
                              </div>
                              <div class="modal-body material-modal__body">
                                <p>Jeste li sigurni da želite arhivirati narudžbu?</p>
                              </div>
                              <div class="modal-footer material-modal__footer">
                                <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                <a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
                              </div>
                            </div>
                          </div>
                        </div> <!-- End modal - archive -->

                      </tbody>
                    </table>
                    <!-- End orders table -->


                    <!-- Order IDS for multiple archiving -->
                    <input type="hidden" name="order_ids[]" id="order_ids">

                    <br>
                    <a href="#" data="<?php getSiteUrl(); ?>idkadmin/orders?page=archive_multiple&ids=" data-toggle="modal" data-target="#archiveMultipleModal" class="archive-multiple btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive">
                      <i class="fa fa-trash-o" aria-hidden="true"></i> <span>&nbsp;&nbsp;Arhiviraj označene narudžbe</span>
                    </a>
                    <!-- Modal -->
                    <div class="modal material-modal material-modal_danger fade" id="archiveMultipleModal">
                      <div class="modal-dialog">
                        <div class="modal-content material-modal__content">
                          <div class="modal-header material-modal__header">
                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title material-modal__title">Arhiviranje</h4>
                          </div>
                          <div class="modal-body material-modal__body">
                            <p>Jeste li sigurni da želite arhivirati označene narudžbe?</p>
                          </div>
                          <div class="modal-footer material-modal__footer">
                            <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                            <a id="archive_multiple_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
                          </div>
                        </div>
                      </div>
                    </div> <!-- End modal - archive multiple -->

                    <script>
                      // Archiving multiple
                      var orderIds = [];
                      $(".material-checkbox").click(function() {
                        var orderId = $(this).attr("id").split("checkbox_")[1];
                        if (!orderIds.includes(orderId)) {
                          orderIds.push(orderId);
                        } else {
                          var index = orderIds.indexOf(orderId);
                          orderIds.splice(index, 1);
                        }
                        $("#order_ids").val(orderIds);
                      });
                      $(".archive-multiple").click(function() {
                        var addressValueMultiple = $(this).attr("data");
                        document.getElementById("archive_multiple_link").href = addressValueMultiple + orderIds;
                      });
                    </script>

                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php
          break;



          /************************************************************
           * 							EDIT ORDER
           * *********************************************************/
        case "edit":
          $order_id = $_GET['order_id'];

          //Mark notification as read
          if (isset($_GET['nid'])) {
            $notification_id = $_GET['nid'];

            $query_update = $db->prepare("
              UPDATE idk_notifications
              SET	notification_status = :notification_status
              WHERE notification_id = :notification_id AND notification_datetime <= NOW()");

            $query_update->execute(array(
              ':notification_status' => 2,
              ':notification_id' => $notification_id
            ));
          }

          $query = $db->prepare("
            SELECT t1.client_id, t1.client_name, t1.client_max_rabat, t2.order_status, t2.order_key, t2.order_fiscalized
            FROM idk_client t1
            INNER JOIN idk_order t2
            ON t1.client_id = t2.client_id
            WHERE t2.order_id = :order_id");

          $query->execute(array(
            ':order_id' => $order_id
          ));

          $client = $query->fetch();

          $client_id = $client['client_id'];
          $client_name = $client['client_name'];
          $client_max_rabat = $client['client_max_rabat'];
          $order_status = $client['order_status'];
          $order_key = $client['order_key'];
          $order_fiscalized = $client['order_fiscalized'];

        ?>

          <div class="row">
            <div class="col-xs-8">
              <h1><i class="fa fa-shopping-cart idk_color_green" aria-hidden="true"></i> Narudžba #<?php echo "${order_id} - Klijent: ${client_name}"; ?></h1>
            </div>
            <div class="col-xs-4 text-right idk_margin_top10">
              <a href="<?php getSiteUrl(); ?>idkadmin/orders?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
            </div>
            <div class="col-xs-12">
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="content_box">
                <div class="row">
                  <div class="col-md-10">

                   <!-- Change order client -->
                   <button type="button" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#duplicateOrder"><i class="fa fa-clipboard" aria-hidden="true"></i> <span>Dupliciraj</span></button>

                  <!-- Modal change client -->
                  <div class="modal material-modal material-modal_primary fade" id="duplicateOrder">
                    <div class="modal-dialog">
                      <div class="modal-content material-modal__content">
                        <div class="modal-header material-modal__header">
                          <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                          <h4 class="modal-title material-modal__title">Dupliciraj narudžbu</h4>
                        </div>
                        <!-- Form - change client -->
                        <form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=duplicate_order" method="post" role="form" class="form-horizontal">
                          <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id; ?>">
                          <div class="modal-body material-modal__body">
                            <div class="form-group">
                             <p> Da li želite duplicirati narudžbu? </p>
                            </div>
                          </div>
                          <div class="modal-footer material-modal__footer">
                            <button type="button" class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                            <button type="submit" class="btn btn-primary material-btn material-btn_primary">POTVRDI</button>
                          </div>
                        </form>
                      </div>
                    </div>
                  </div> <!-- End modal - change client -->

                    <!-- Change order client -->
                    <button type="button" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#changeClientModal"><i class="fa fa-pencil-square" aria-hidden="true"></i> <span>Promijeni klijenta</span></button>

                    <!-- Modal change client -->
                    <div class="modal material-modal material-modal_primary fade" id="changeClientModal">
                      <div class="modal-dialog">
                        <div class="modal-content material-modal__content">
                          <div class="modal-header material-modal__header">
                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title material-modal__title">Promijeni klijenta</h4>
                          </div>
                          <!-- Form - change client -->
                          <form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=order_change_client" method="post" role="form" class="form-horizontal">
                            <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id; ?>">
                            <div class="modal-body material-modal__body">
                              <div class="form-group">
                                <label for="client_id" class="col-sm-3 control-label"><span class="text-danger">*</span> Klijent:</label>
                                <div class="col-sm-9">
                                  <select class="selectpicker" id="client_id" name="client_id" data-live-search="true" required>
                                    <?php
                                    $select_query = $db->prepare("
                                    SELECT client_id, client_name
                                    FROM idk_client
                                    ORDER BY client_name");

                                    $select_query->execute();

                                    while ($select_row = $select_query->fetch()) {
                                      echo "<option value='" . $select_row['client_id'] . "' data-tokens='" . $select_row['client_name'] . "'";
                                      if ($client_name == $select_row['client_name']) {
                                        echo " selected='selected'";
                                      }
                                      echo ">" . $select_row['client_name'] . "</option>";
                                    }
                                    ?>
                                  </select>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer material-modal__footer">
                              <button type="button" class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                              <button type="submit" class="btn btn-primary material-btn material-btn_primary">POTVRDI</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div> <!-- End modal - change client -->

                    <!-- Add new product to order -->
                    <button type="button" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#addProductToOrderModal"><i class="fa fa-plus-square" aria-hidden="true"></i> <span>Dodaj</span></button>

                    <!-- Modal change client -->
                    <div class="modal material-modal material-modal_primary fade" id="addProductToOrderModal">
                      <div class="modal-dialog">
                        <div class="modal-content material-modal__content">
                          <div class="modal-header material-modal__header">
                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title material-modal__title">Dodaj proizvod</h4>
                          </div>
                          <!-- Form - change client -->
                          <form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=order_add_product" method="post" role="form" class="form-horizontal">
                            <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id; ?>">
                            <input type="hidden" name="client_id" id="client_id" value="<?php echo $client_id; ?>">
                            <div class="modal-body material-modal__body">
                              <div class="form-group">
                                <label for="product_id" class="col-sm-3 control-label"><span class="text-danger">*</span> Proizvod:</label>
                                <div class="col-sm-9">
                                  <select class="selectpicker" id="product_id" name="product_id" data-live-search="true" required>
                                    <option value=""></option>
                                    <?php
                                    $select_query = $db->prepare("
                                      SELECT product_id, product_name, product_price, product_currency
                                      FROM idk_product
                                      WHERE product_active = 1
                                      ORDER BY product_name");

                                    $select_query->execute();

                                    while ($select_row = $select_query->fetch()) {
                                      echo "<option value='" . $select_row['product_id'] . "' data-tokens='" . $select_row['product_name'] . "'>" . $select_row['product_name'] . " - " . number_format($select_row['product_price'], 3, ',', '.') . " " . $select_row['product_currency'] . "</option>";
                                    }
                                    ?>
                                  </select>
                                </div>
                              </div>

                              <div class="form-group">
                                <label for="product_quantity" class="col-sm-3 control-label"><span class="text-danger">*</span> Količina:</label>
                                <div class="col-sm-9">
                                  <input type="number" class="form-control materail-input" name="product_quantity" id="product_quantity" min="1" value="1" required>
                                </div>
                              </div>

                              <div class="form-group">
                                <label for="product_rabat_percentage" class="col-sm-3 control-label"><span class="text-danger">*</span> Rabat (%):</label>
                                <div class="col-sm-9">
                                  <input type="number" class="form-control materail-input" name="product_rabat_percentage" id="product_rabat_percentage" min="0" max="<?php if (isset($client_max_rabat)) {
                                                                                                                                                                        echo $client_max_rabat;
                                                                                                                                                                      } else {
                                                                                                                                                                        echo "100";
                                                                                                                                                                      } ?>" value="0.00" required>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer material-modal__footer">
                              <button type="button" class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                              <button type="submit" class="btn btn-primary material-btn material-btn_primary">POTVRDI</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div> <!-- End modal - change client -->

                    <!-- Product IDS for multiple deleting -->
                    <input type="hidden" name="product_ids[]" id="product_ids">

                    <a href="#" data="<?php getSiteUrl(); ?>idkadmin/orders?page=delete_multiple_products&order_id=<?php echo $order_id; ?>&ids=" data-toggle="modal" data-target="#deleteMultipleProductsModal" class="delete-multiple btn material-btn material-btn-icon-danger material-btn_danger main-container__column">
                      <i class="fa fa-minus-circle" aria-hidden="true"></i> <span>Obriši</span>
                    </a>
                    <!-- Modal -->
                    <div class="modal material-modal material-modal_danger fade" id="deleteMultipleProductsModal">
                      <div class="modal-dialog">
                        <div class="modal-content material-modal__content">
                          <div class="modal-header material-modal__header">
                            <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title material-modal__title">Brisanje</h4>
                          </div>
                          <div class="modal-body material-modal__body">
                            <p>Jeste li sigurni da želite obrisati označene proizvode iz narudžbe?</p>
                          </div>
                          <div class="modal-footer material-modal__footer">
                            <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                            <a id="delete_multiple_products_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
                          </div>
                        </div>
                      </div>
                    </div> <!-- End modal - delete multiple products -->

                  </div>
                  <div class="col-md-2 text-right">
                    <!-- Form - update order -->
                    <form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=edit_order" method="post">

                      <!-- Storing all products rabats in array and using it in do.php -->
                      <input type="hidden" name="products_rabats_array[]" id="idk_products_rabats_array" multiple="multiple">

                      <!-- Storing all old products quantites in array and using it in do.php -->
                      <input type="hidden" name="products_quantities_array_old[]" id="idk_products_quantities_array_old" multiple="multiple">

                      <!-- Storing all products quantites in array and using it in do.php -->
                      <input type="hidden" name="products_quantities_array[]" id="idk_products_quantities_array" multiple="multiple">

                      <!-- Storing all products ids in array and using it in do.php -->
                      <input type="hidden" name="products_ids_array[]" id="idk_products_ids_array" multiple="multiple">

                      <!-- Storing all products prices in array and using it in do.php -->
                      <input type="hidden" name="products_prices_array[]" id="idk_products_prices_array" multiple="multiple">

                      <!-- Storing all products tax values in array and using it in do.php -->
                      <input type="hidden" name="products_tax_percentages_array[]" id="idk_products_tax_percentages_array" multiple="multiple">

                      <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                      <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">

                      <button type="submit" id="idk_btn_edit_order" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-check-square" aria-hidden="true"></i> <span>Ažuriraj</span></button>

                    </form><!-- End form - update order -->
                  </div>
                </div>
                <div class="row idk_margin_top20">
                  <div class="col-xs-12">

                    <!-- Success and error handling -->
                    <?php
                    if (isset($_GET['mess'])) {
                      $mess = $_GET['mess'];
                    } else {
                      $mess = 0;
                    }

                    if ($mess == 1) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste promijenili klijenta.</div>';
                    } elseif ($mess == 2) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
                    } elseif ($mess == 3) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali proizvod u narudžbu.</div>';
                    } elseif ($mess == 4) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Proizvod je već dodan u narudžbu.</div>';
                    } elseif ($mess == 5) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali proizvode iz narudžbe.</div>';
                    } elseif ($mess == 6) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Prvo morate označiti proizvode koje želite obrisati.</div>';
                    } elseif ($mess == 7) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste ažurirali narudžbu.</div>';
                    }
                    ?>

                    <!-- Filling the table with data -->
                    <script type="text/javascript">
                      $(document).ready(function() {
                        $('#idk_table').DataTable({

                          responsive: true,
                          "paging": false,
                          "searching": false,
                          "info": false,

                          "order": [
                            [1, "asc"]
                          ],

                          "bAutoWidth": false,

                          "aoColumns": [{
                              "width": "5%"
                            },
                            {
                              "width": "25%"
                            },
                            {
                              "width": "10%"
                            },
                            {
                              "width": "15%"
                            },
                            {
                              "width": "15%"
                            },
                            {
                              "width": "15%"
                            },
                            {
                              "width": "15%"
                            }
                          ]
                        });
                      });
                    </script>

                    <!-- Products table -->
                    <table id="idk_table" class="display idk_order_products_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th></th>
                          <th>Proizvod</th>
                          <th>Cijena</th>
                          <th>Količina</th>
                          <th>Iznos</th>
                          <th>Rabat (%)</th>
                          <th>Iznos rabata</th>
                        </tr>
                      </thead>

                      <tbody>

                        <!-- Get data for product -->
                        <?php

                        $query = $db->prepare("
                          SELECT *
                          FROM idk_product_order
                          WHERE order_id = :order_id");

                        $query->execute(array(
                          ':order_id' => $order_id
                        ));

                        while ($product = $query->fetch()) {

                          $product_id = $product['product_id'];
                          $product_name = $product['product_name'];
                          $product_quantity = $product['product_quantity'];
                          $product_unit = $product['product_unit'];
                          $product_price = $product['product_price'];
                          $product_currency = $product['product_currency'];
                          $product_tax_name = $product['product_tax_name'];
                          $product_tax_percentage = $product['product_tax_percentage'];
                          $product_tax_value = $product['product_tax_value'];
                          $product_rabat_percentage = $product['product_rabat_percentage'];
                          $product_rabat_value = $product['product_rabat_value'];
                          $product_total_price = $product_price * $product_quantity;
                          $product_in_stock = $product['product_in_stock'];
                          $product_quantity_in_db = $product['product_quantity_in_db'];

                        ?>

                          <tr>
                            <td class="text-center">
                              <div class="main-container__column material-checkbox-group material-checkbox-group_success">
                                <input type="checkbox" id="checkbox_<?php echo $product_id; ?>" class="material-checkbox">
                                <label class="material-checkbox-group__label" for="checkbox_<?php echo $product_id; ?>"></label>
                              </div>
                            </td>
                            <td>
                              <input type="hidden" class="idk_product_id" id="idk_product_id_<?php echo $product_id; ?>" value="<?php echo $product_id; ?>">
                              <a href="<?php getSiteUrl(); ?>idkadmin/products?page=open&id=<?php echo $product_id; ?>">
                                <?php echo $product_name; ?>
                                <?php if ($product_in_stock == 0) {
                                  echo "<br><span class='text-danger' style='margin-top: 10px;'>PROIZVODA NIJE BILO NA STANJU U TRENUTKU NARUDŽBE</span>";
                                } elseif ($product_in_stock == 2) {
                                  echo "<br><span class='text-danger' style='margin-top: 10px;'>NEDOVOLJNA KOLIČINA PROIZVODA NA STANJU U TRENUTKU NARUDŽBE: " . $product_quantity_in_db . " " . $product_unit . "<br>(NEDOSTAJE: " . ($product_quantity - $product_quantity_in_db) . " " . $product_unit . ")</span>";
                                } ?>
                              </a>
                            </td>
                            <td>
                              <input type="hidden" class="idk_product_price" id="idk_product_price_<?php echo $product_price; ?>" value="<?php echo $product_price; ?>">
                              <input type="hidden" class="idk_product_tax_percentage" id="idk_product_tax_percentage_<?php echo $product_tax_percentage; ?>" value="<?php echo $product_tax_percentage; ?>">
                              <?php echo number_format($product_price, 3, ',', '.') . " " . $product_currency; ?>
                            </td>
                            <td>
                              <div class="col-6 text-right align-self-center">
                                <input type="hidden" class="idk_product_quantity_old" value="<?php echo $product_quantity; ?>">
                                <input type="number" min="1" class="form-control idk_product_quantity" value="<?php echo $product_quantity; ?>">
                              </div>
                            </td>
                            <td>
                              <?php echo number_format($product_total_price, 3, ',', '.') . " " . $product_currency; ?>
                            </td>
                            <td>
                              <div class="input-group mb-2">
                                <input type="number" min="0" class="form-control idk_product_rabat_percentage" name="product_rabat_percentage" id="product_rabat_percentage_<?php echo $product_id; ?>" placeholder="0.00" value="<?php echo $product_rabat_percentage; ?>">
                              </div>
                            </td>
                            <td>
                              <?php if ($product_rabat_value) {
                                $product_total_rabat = ($product_price * $product_quantity) * $product_rabat_percentage / 100;
                                echo number_format($product_total_rabat, 3, ',', '.') . " " . $product_currency;
                              } else {
                                echo "0,000 KM";
                              }
                              ?>
                            </td>
                          </tr>

                        <?php } ?>

                      </tbody>
                    </table>
                    <!-- End products table -->

                    <script>
                      // Deleting multiple products
                      var productIds = [];
                      $(".material-checkbox").click(function() {
                        var productId = $(this).attr("id").split("checkbox_")[1];
                        if (!productIds.includes(productId)) {
                          productIds.push(productId);
                        } else {
                          var index = productIds.indexOf(productId);
                          productIds.splice(index, 1);
                        }
                        $("#product_ids").val(productIds);
                      });
                      $(".delete-multiple").click(function() {
                        var addressValueMultiple = $(this).attr("data");
                        document.getElementById("delete_multiple_products_link").href = addressValueMultiple + productIds;
                      });
                    </script>

                  </div>
                </div>

                <?php

                $query = $db->prepare("
                  SELECT order_status, order_total_price, order_total_tax, order_total_rabat, order_to_pay, order_note
                  FROM idk_order
                  WHERE order_id = :order_id");

                $query->execute(array(
                  ':order_id' => $order_id
                ));

                $order = $query->fetch();

                $order_note = $order['order_note'];
                $order_total_price = $order['order_total_price'];
                $order_total_tax = $order['order_total_tax'];
                $order_total_rabat = $order['order_total_rabat'];
                $order_to_pay = $order['order_to_pay'];

                ?>

                <!-- Display order_note -->
                <?php if (isset($order_note)) { ?>
                  <div class="row idk_order_note_row">
                    <strong class="col-sm-3 text-right">Bilješka:</strong>
                    <div class="col-sm-9"><?php echo $order_note; ?></div>
                  </div>
                <?php } ?>

                <!-- Get order information -->
                <div class="row idk_order_open_total">
                  <strong class="col-sm-3 text-right">Ukupno:</strong>
                  <div class="col-sm-9"><?php echo number_format($order_total_price, 3, ',', '.') . " KM"; ?></div>
                </div>
                <div class="row idk_order_open_total">
                  <strong class="col-sm-3 text-right">Rabat:</strong>
                  <div class="col-sm-9"><?php if ($order_total_rabat) {
                                          echo number_format($order_total_rabat, 3, ',', '.') . " KM";
                                        } else {
                                          echo "0.00 KM";
                                        } ?></div>
                </div>
                <div class="row idk_order_open_total">
                  <strong class="col-sm-3 text-right">PDV:</strong>
                  <div class="col-sm-9"><?php echo number_format($order_total_tax, 3, ',', '.') . " KM"; ?></div>
                </div>
                <div class="row idk_order_open_total">
                  <strong class="col-sm-3 text-right">ZA PLATITI:</strong>
                  <div class="col-sm-9"><?php echo number_format($order_to_pay, 2, ',', '.') . " KM"; ?></div>
                </div>



              </div>
            </div>
          </div>
        <?php


          break;



        case "open":

          $order_id = $_GET['order_id'];

          //Mark notification as read
          if (isset($_GET['nid'])) {
            $notification_id = $_GET['nid'];

            $query_update = $db->prepare("
                UPDATE idk_notifications
                SET	notification_status = :notification_status
                WHERE notification_id = :notification_id AND notification_datetime <= NOW()");

            $query_update->execute(array(
              ':notification_status' => 2,
              ':notification_id' => $notification_id
            ));
          }

          $query = $db->prepare("
              SELECT t1.client_name, t2.order_status, t2.order_key, t2.order_fiscalized
              FROM idk_client t1
              INNER JOIN idk_order t2
              ON t1.client_id = t2.client_id
              WHERE t2.order_id = :order_id");

          $query->execute(array(
            ':order_id' => $order_id
          ));

          $client = $query->fetch();

          $client_name = $client['client_name'];
          $order_status = $client['order_status'];
          $order_key = $client['order_key'];
          $order_fiscalized = $client['order_fiscalized'];

        ?>

          <div class="row">
            <div class="col-xs-8">
              <h1><i class="fa fa-shopping-cart idk_color_green" aria-hidden="true"></i> Narudžba #<?php echo "${order_id} - Klijent: ${client_name}"; ?></h1>
            </div>
            <div class="col-xs-4 text-right idk_margin_top10">
              <a href="<?php getSiteUrl(); ?>idkadmin/orders?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
            </div>
            <div class="col-xs-12">
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="content_box">
                <div class="row">
                  <div class="col-md-10">
                    <a href="<?php getSiteUrl(); ?>idkadmin/print_order?id=<?php echo $order_id; ?>&order=<?php echo $order_key; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" id="idk_print_report_btn" target="_blank">
                      <i class="fa fa-print" aria-hidden="true"></i> <span>Print</span>
                    </a>
                    <?php if ($order_status != 4 and $order_status != 5 and $order_fiscalized == 0) { ?>
                      <button type="button" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" data-toggle="modal" data-target="#fiscalizeModal"><i class="fa fa-bank" aria-hidden="true"></i> <span>Fiskalizacija</span></button>

                      <!-- Modal fiscalize -->
                      <div class="modal material-modal material-modal_primary fade" id="fiscalizeModal">
                        <div class="modal-dialog">
                          <div class="modal-content material-modal__content">
                            <div class="modal-header material-modal__header">
                              <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                              <h4 class="modal-title material-modal__title">Fiskalizacija</h4>
                            </div>
                            <div class="modal-body material-modal__body">
                              <p>Jeste li sigurni da želite fiskalizirati narudžbu?</p>
                            </div>
                            <div class="modal-footer material-modal__footer">
                              <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                              <a id="idk_fiscalization_btn" href="<?php getSiteUrl(); ?>idkadmin/do.php?form=order_fiscalization&id=<?php echo $order_id; ?>"><button class="btn btn-primary material-btn material-btn_primary">FISKALIZIRAJ</button></a>
                            </div>
                          </div>
                        </div>
                      </div> <!-- End modal - fiscalize -->
                    <?php } elseif ($order_fiscalized == 1) { ?>
                      <strong>&nbsp;&nbsp;Fiskalizirano</strong>
                    <?php } ?>
                  </div>
                  <div class="col-md-2 text-right">
                    <?php if ($order_fiscalized == 0) { ?>
                      <a href="<?php getSiteUrl(); ?>idkadmin/orders?page=edit&order_id=<?php echo $order_id; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" id="idk_print_report_btn">
                        <i class="fa fa-pencil" aria-hidden="true"></i> <span>Uredi</span>
                      </a>
                    <?php } ?>
                  </div>
                </div>
                <div class="row idk_margin_top20">
                  <div class="col-xs-12">

                    <!-- Success and error handling -->
                    <?php
                    if (isset($_GET['mess'])) {
                      $mess = $_GET['mess'];
                    } else {
                      $mess = 0;
                    }

                    if ($mess == 1) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste promijenili status narudžbe.</div>';
                    } elseif ($mess == 2) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
                    } elseif ($mess == 3) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste fiskalizirali narudžbu.</div>';
                    } elseif ($mess == 4) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Došlo je do greške prilikom fiskalizacije narudžbe.</div>';
                    }
                    ?>

                    <!-- Filling the table with data -->
                    <script type="text/javascript">
                      $(document).ready(function() {
                        $('#idk_table').DataTable({

                          responsive: true,
                          "paging": false,
                          "searching": false,
                          "info": false,

                          "order": [
                            [0, "desc"]
                          ],

                          "bAutoWidth": false,

                          "aoColumns": [{
                              "width": "25%"
                            },
                            {
                              "width": "15%"
                            },
                            {
                              "width": "15%"
                            },
                            {
                              "width": "15%"
                            },
                            {
                              "width": "15%"
                            },
                            {
                              "width": "15%"
                            }
                          ]
                        });
                      });
                    </script>

                    <!-- Products table -->
                    <table id="idk_table" class="display idk_order_products_table" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th>Proizvod</th>
                          <th>Jed. VPC</th>
                          <th>Količina</th>
                          <th>Ukupna cijena</th>
                          <th>Jed. rabat</th>
                          <th>Jed. porez</th>
                        </tr>
                      </thead>

                      <tbody>

                        <!-- Get data for product -->
                        <?php

                        $query = $db->prepare("
                            SELECT *
                            FROM idk_product_order
                            WHERE order_id = :order_id");

                        $query->execute(array(
                          ':order_id' => $order_id
                        ));

                        while ($product = $query->fetch()) {

                          $product_id = $product['product_id'];
                          $product_name = $product['product_name'];
                          $product_quantity = $product['product_quantity'];
                          $product_unit = $product['product_unit'];
                          $product_price = $product['product_price'];
                          $product_currency = $product['product_currency'];
                          $product_tax_name = $product['product_tax_name'];
                          $product_tax_percentage = $product['product_tax_percentage'];
                          $product_tax_value = $product['product_tax_value'];
                          $product_rabat_percentage = $product['product_rabat_percentage'];
                          $product_rabat_value = $product['product_rabat_value'];
                          $product_total_price = $product_price * $product_quantity;
                          $product_in_stock = $product['product_in_stock'];
                          $product_quantity_in_db = $product['product_quantity_in_db'];

                        ?>

                          <tr>
                            <td>
                              <a href="<?php getSiteUrl(); ?>idkadmin/products?page=open&id=<?php echo $product_id; ?>">
                                <?php echo $product_name; ?>
                                <?php if ($product_in_stock == 0) {
                                  echo "<br><span class='text-danger' style='margin-top: 10px;'>PROIZVODA NIJE BILO NA STANJU U TRENUTKU NARUDŽBE</span>";
                                } elseif ($product_in_stock == 2) {
                                  echo "<br><span class='text-danger' style='margin-top: 10px;'>NEDOVOLJNA KOLIČINA PROIZVODA NA STANJU U TRENUTKU NARUDŽBE: " . $product_quantity_in_db . " " . $product_unit . "<br>(NEDOSTAJE: " . ($product_quantity - $product_quantity_in_db) . " " . $product_unit . ")</span>";
                                } ?>
                              </a>
                            </td>
                            <td>
                              <?php echo number_format($product_price, 3, ',', '.') . " " . $product_currency; ?>
                            </td>
                            <td>
                              <?php echo intval($product_quantity) . " " . $product_unit; ?>
                            </td>
                            <td>
                              <?php echo number_format($product_total_price, 3, ',', '.') . " " . $product_currency; ?>
                            </td>
                            <td>
                              <?php if ($product_rabat_percentage and $product_rabat_value) {
                                echo number_format($product_rabat_value, 3, ',', '.') . " KM (" . number_format($product_rabat_percentage, 2, ',', '.') . "%)";
                              } else {
                                echo "0,00 KM (0,00%)";
                              } ?>
                            </td>
                            <td>
                              <?php if ($product_tax_value) {
                                echo number_format($product_tax_value, 3, ',', '.') . " KM ";
                              }
                              echo "(" . $product_tax_name . " - " . intval($product_tax_percentage) . "%)"; ?>
                            </td>
                          </tr>

                        <?php } ?>

                      </tbody>
                    </table>
                    <!-- End products table -->

                  </div>
                </div>

                <?php

                $query = $db->prepare("
                    SELECT order_status, order_total_price, order_total_tax, order_total_rabat, order_to_pay, order_note
                    FROM idk_order
                    WHERE order_id = :order_id");

                $query->execute(array(
                  ':order_id' => $order_id
                ));

                $order = $query->fetch();

                $order_note = $order['order_note'];
                $order_total_price = $order['order_total_price'];
                $order_total_tax = $order['order_total_tax'];
                $order_total_rabat = $order['order_total_rabat'];
                $order_to_pay = $order['order_to_pay'];

                ?>

                <!-- Display order_note -->
                <?php if (isset($order_note)) { ?>
                  <div class="row idk_order_note_row">
                    <strong class="col-sm-3 text-right">Bilješka:</strong>
                    <div class="col-sm-9"><?php echo $order_note; ?></div>
                  </div>
                <?php } ?>

                <!-- Get order information -->
                <div class="row idk_order_open_total">
                  <strong class="col-sm-3 text-right">Ukupno:</strong>
                  <div class="col-sm-9"><?php echo number_format($order_total_price, 3, ',', '.') . " KM"; ?></div>
                </div>
                <div class="row idk_order_open_total">
                  <strong class="col-sm-3 text-right">Rabat:</strong>
                  <div class="col-sm-9"><?php if ($order_total_rabat) {
                                          echo number_format($order_total_rabat, 3, ',', '.') . " KM";
                                        } else {
                                          echo "0.00 KM";
                                        } ?></div>
                </div>
                <div class="row idk_order_open_total">
                  <strong class="col-sm-3 text-right">PDV:</strong>
                  <div class="col-sm-9"><?php echo number_format($order_total_tax, 3, ',', '.') . " KM"; ?></div>
                </div>
                <div class="row idk_order_open_total">
                  <strong class="col-sm-3 text-right">ZA PLATITI:</strong>
                  <div class="col-sm-9"><?php echo number_format($order_to_pay, 2, ',', '.') . " KM"; ?></div>
                </div>

                <div class="row">
                  <?php
                  if ($getEmployeeStatus == 1) {
                  ?>

                    <br>
                    <hr>
                    <br>

                    <form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=change_order_status" method="post" role="form" class="form-horizontal">

                      <input type="hidden" name="order_id" id="order_id" value="<?php echo $order_id; ?>">

                      <div class="form-group">
                        <label for="order_status" class="col-sm-3 control-label">Status narudžbe:</label>

                        <div class="col-sm-6">
                          <select class="selectpicker" id="order_status" name="order_status" data-live-search="true" required>
                            <option value=""></option>
                            <?php
                            $select_query = $db->prepare("
                                SELECT od_id, od_data
                                FROM idk_order_otherdata
                                WHERE od_group = :od_group
                                ORDER BY od_data");

                            $select_query->execute(array(
                              ':od_group' => 1
                            ));

                            while ($select_row = $select_query->fetch()) {
                              echo "<option value='" . $select_row['od_id'] . "'";
                              if ($order_status == $select_row['od_id']) {
                                echo "selected='selected'";
                              } else echo "";
                              echo " data-tokens='" . $select_row['od_data'] . "'>" . $select_row['od_data'] . "</option>";
                            }
                            ?>
                          </select>
                        </div>
                        <div class="col-sm-3">
                          <button type="submit" class="btn btn-primary material-btn material-btn_primary">Promijeni</button>
                        </div>

                      </div>
                    </form>

                    <br>
                    <br>
                  <?php
                  }
                  ?>
                </div>

              </div>
            </div>
          </div>
      <?php
          break;



          /************************************************************
           * 							ARCHIVE
           * *********************************************************/
        case "archive":

          if ($getEmployeeStatus == 1) {

            $order_id = $_GET['id'];

            //Get client name
            $query_select = $db->prepare("
							SELECT t1.client_name
              FROM idk_client t1
              INNER JOIN idk_order t2
              ON t1.client_id = t2.client_id
							WHERE t2.order_id = :order_id");

            $query_select->execute(array(
              ':order_id' => $order_id
            ));

            $client_select = $query_select->fetch();

            $client_name = $client_select['client_name'];

            //Save changes to order in db
            $query = $db->prepare("
							UPDATE idk_order
							SET order_status = :order_status
							WHERE order_id = :order_id");

            $query->execute(array(
              ':order_status' => 0,
              ':order_id' => $order_id
            ));

            //Save changes to b2b orders stats in db
            $query_order = $db->prepare("
							SELECT created_at
							FROM idk_order
							WHERE order_id = :order_id");

            $query_order->execute(array(
              ':order_id' => $order_id
            ));

            $row_order = $query_order->fetch();
            $created_at = $row_order['created_at'];

            $query_orders_stats = $db->prepare("
							UPDATE idk_stat
							SET stat_b2b_orders = stat_b2b_orders - 1
							WHERE stat_month = :stat_month");

            $query_orders_stats->execute(array(
              ':stat_month' => date('Y-m-01', strtotime($created_at))
            ));

            //Add to log
            $log_desc = "Arhivirao narudžbu #${order_id} za klijenta: ${client_name}";
            $log_date = date('Y-m-d H:i:s');
            addLog($logged_employee_id, $log_desc, $log_date);

            header("Location: " . getSiteUrlr() . "idkadmin/orders?page=list&mess=4");
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
           * 							ARCHIVE MULTIPLE
           * *********************************************************/
        case "archive_multiple":

          if ($getEmployeeStatus == 1) {

            $order_ids = $_GET['ids'] != "" ? explode(',', $_GET['ids']) : NULL;

            if (isset($order_ids) and count($order_ids) > 0) {
              foreach ($order_ids as $order_id) {

                //Get client name
                $query_select = $db->prepare("
                  SELECT t1.client_name
                  FROM idk_client t1
                  INNER JOIN idk_order t2
                  ON t1.client_id = t2.client_id
                  WHERE t2.order_id = :order_id");

                $query_select->execute(array(
                  ':order_id' => $order_id
                ));

                $client_select = $query_select->fetch();

                $client_name = $client_select['client_name'];

                //Save changes to order in db
                $query = $db->prepare("
                  UPDATE idk_order
                  SET order_status = :order_status
                  WHERE order_id = :order_id");

                $query->execute(array(
                  ':order_status' => 0,
                  ':order_id' => $order_id
                ));

                //Save changes to b2b orders stats in db
                $query_order = $db->prepare("
                  SELECT created_at
                  FROM idk_order
                  WHERE order_id = :order_id");

                $query_order->execute(array(
                  ':order_id' => $order_id
                ));

                $row_order = $query_order->fetch();
                $created_at = $row_order['created_at'];

                $query_orders_stats = $db->prepare("
                  UPDATE idk_stat
                  SET stat_b2b_orders = stat_b2b_orders - 1
                  WHERE stat_month = :stat_month");

                $query_orders_stats->execute(array(
                  ':stat_month' => date('Y-m-01', strtotime($created_at))
                ));

                //Add to log
                $log_desc = "Arhivirao narudžbu #${order_id} za klijenta: ${client_name}";
                $log_date = date('Y-m-d H:i:s');
                addLog($logged_employee_id, $log_desc, $log_date);
              }
              header("Location: " . getSiteUrlr() . "idkadmin/orders?page=list&mess=5");
            } else {
              header("Location: " . getSiteUrlr() . "idkadmin/orders?page=list&mess=6");
            }
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
           * 							DELETE MULTIPLE
           * *********************************************************/
        case "delete_multiple_products":

          if ($getEmployeeStatus == 1) {

            $order_id = isset($_GET['order_id']) ? $_GET['order_id'] : NULL;
            $product_ids = $_GET['ids'] != "" ? explode(',', $_GET['ids']) : NULL;

            //Get order info
            $order_query = $db->prepare("
              SELECT order_status
              FROM idk_order
              WHERE order_id = :order_id");

            $order_query->execute(array(
              ':order_id' => $order_id
            ));

            $order = $order_query->fetch();

            $order_status_old = $order['order_status'];
            $order_status = NULL;

            if (isset($order_id) and isset($product_ids) and count($product_ids) > 0) {
              foreach ($product_ids as $product_id) {
                //Get product order info
                $product_order_query = $db->prepare("
                  SELECT product_quantity
                  FROM idk_product_order
                  WHERE order_id = :order_id AND product_id = :product_id");

                $product_order_query->execute(array(
                  ':order_id' => $order_id,
                  ':product_id' => $product_id
                ));

                $product_order = $product_order_query->fetch();
                $product_quantity = $product_order['product_quantity'];

                //Update quantity in idk_product
                $update_product_quantity_query = $db->prepare("
                  UPDATE idk_product
                  SET	product_quantity = product_quantity + :product_quantity
                  WHERE product_id = :product_id");

                $update_product_quantity_query->execute(array(
                  ':product_id' => $product_id,
                  ':product_quantity' => $product_quantity
                ));

                //Delete from product order
                $query_delete = $db->prepare("
                  DELETE
                  FROM idk_product_order
                  WHERE order_id = :order_id AND product_id = :product_id");

                $query_delete->execute(array(
                  ':order_id' => $order_id,
                  ':product_id' => $product_id
                ));
              }

              //Get product order info
              $product_order_query = $db->prepare("
                SELECT *
                FROM idk_product_order
                WHERE order_id = :order_id");

              $product_order_query->execute(array(
                ':order_id' => $order_id
              ));

              $order_total_price = 0.000;
              $order_total_tax = 0.000;
              $order_total_rabat = 0.000;
              $order_to_pay = 0.000;

              while ($product_order = $product_order_query->fetch()) {
                if ($product_order['product_in_stock'] != 1) {
                  $order_status = 5;
                }

                $product_price = $product_order['product_price'];
                $product_quantity = $product_order['product_quantity'];
                $product_tax_percentage = $product_order['product_tax_percentage'];
                $product_rabat_percentage = $product_order['product_rabat_percentage'];
                $product_rabat_value = $product_price * $product_rabat_percentage / 100;
                $product_tax_value = ($product_price - $product_rabat_value) * $product_tax_percentage / 100;

                //Calculate product to pay again
                // $product_price = $product_price - ($product_price * $product_rabat_percentage / 100); //Calculate price with rabat
                $product_total_price = $product_price * $product_quantity; //Price without rabat
                $product_total_tax = $product_tax_value * $product_quantity;
                $product_total_rabat = $product_rabat_value * $product_quantity; //Calculate total rabat value
                $product_to_pay = $product_total_price + $product_total_tax - $product_total_rabat;

                $order_total_price += $product_total_price;
                $order_total_tax += $product_total_tax;
                $order_total_rabat += $product_total_rabat;
                $order_to_pay += $product_to_pay;
                $order_to_pay = round($order_to_pay * 2, 1) / 2;
              }

              //Update total price and tax of order
              $update_total_price_tax_query = $db->prepare("
                UPDATE idk_order
                SET	order_total_price = :order_total_price, order_total_tax = :order_total_tax, order_total_rabat = :order_total_rabat, order_to_pay = :order_to_pay
                WHERE order_id = :order_id");

              $update_total_price_tax_query->execute(array(
                ':order_id' => $order_id,
                ':order_total_price' => $order_total_price,
                ':order_total_tax' => $order_total_tax,
                ':order_total_rabat' => $order_total_rabat,
                ':order_to_pay' => $order_to_pay
              ));

              //Update order status
              if (isset($order_status) and $order_status == 5) {
                $update_order_query = $db->prepare("
                  UPDATE idk_order
                  SET	order_status = 5
                  WHERE order_id = :order_id");

                $update_order_query->execute(array(
                  ':order_id' => $order_id
                ));
              } elseif ($order_status_old == 5 and !isset($order_status)) {
                $update_order_query = $db->prepare("
                  UPDATE idk_order
                  SET	order_status = 1
                  WHERE order_id = :order_id");

                $update_order_query->execute(array(
                  ':order_id' => $order_id
                ));
              }

              //Add to log
              $log_desc = "Uredio narudžbu #${order_id}";
              $log_date = date('Y-m-d H:i:s');
              addLog($logged_employee_id, $log_desc, $log_date);

              header("Location: " . getSiteUrlr() . "idkadmin/orders?page=edit&order_id=${order_id}&mess=5");
            } else {
              header("Location: " . getSiteUrlr() . "idkadmin/orders?page=edit&order_id=${order_id}&mess=6");
            }
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
          
      }
      ?>


      <!--/************************************************************
 * 							FOOTER
 * *********************************************************/-->
      <footer><?php getCopyright(); ?></footer>
    </div>
  </div>
</body>

</html>