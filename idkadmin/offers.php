<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
  $page = $_REQUEST["page"];
} else {
  header("Location: offers?page=list");
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
         * 							LIST ALL OFFERS
         * *********************************************************/
        case "list":
      ?>

          <div class="row">
            <div class="col-xs-8">
              <h1><i class="fa fa-file idk_color_green" aria-hidden="true"></i> Ponude</h1> 
            </div>
            <div class="col-xs-12">
              <hr>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="content_box">

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
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste kreirali narudžbu iz ponude i ponuda je arhivirana.</div>';
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
                              "width": "15%"
                            },
                            {
                              "width": "20%"
                            },
                            {
                              "width": "20%"
                            },
                            {
                              "width": "15%"
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

                    <!-- Offers table -->
                    <table id="idk_table" class="display" cellspacing="0" width="100%">
                      <thead>
                        <tr>
                          <th></th>
                          <th>ID</th>
                          <th>Klijent</th>
                          <th>Komercijalista</th>
                          <th>Za platiti</th>
                          <th>Datum</th>
                          <th></th>
                        </tr>
                      </thead>

                      <tbody>

                        <!-- Get data for offer -->
                        <?php

                        $query = $db->prepare("
                            SELECT t1.offer_id, t1.client_id, t1.employee_id, t1.offer_status, t1.offer_total_price, t1.offer_total_rabat, t1.offer_total_tax, t1.offer_to_pay, t1.created_at, t2.client_name
                            FROM idk_offer t1, idk_client t2
                            WHERE t1.offer_status != 0 AND t2.client_id = t1.client_id");

                        $query->execute();

                        while ($offer = $query->fetch()) {

                          $client_id = $offer['client_id'];
                          $client_name = $offer['client_name'];
                          $offer_employee_id = $offer['employee_id'];
                          $offer_id = $offer['offer_id'];
                          $offer_status = $offer['offer_status'];
                          $offer_total_price = $offer['offer_total_price'];
                          $offer_total_rabat = $offer['offer_total_rabat'];
                          $offer_total_tax = $offer['offer_total_tax'];
                          $offer_to_pay = $offer['offer_to_pay'];
                          $offer_created_at = $offer['created_at'];
                          $offer_created_at_new_format = date('d.m.Y.', strtotime($offer['created_at']));

                        ?>
                          <tr>
                            <td class="text-center">
                              <div class="main-container__column material-checkbox-group material-checkbox-group_success">
                                <input type="checkbox" id="checkbox_<?php echo $offer_id; ?>" class="material-checkbox">
                                <label class="material-checkbox-group__label" for="checkbox_<?php echo $offer_id; ?>"></label>
                              </div>
                            </td>
                            <td>
                              <?php echo $offer_id; ?>
                            </td>
                            <td>
                              <a href="<?php getSiteUrl(); ?>idkadmin/offers?page=open&offer_id=<?php echo $offer_id; ?>"><?php echo $client_name; ?></a>
                            </td>
                            <td>
                              <?php
                              if (isset($offer_employee_id)) {
                                $query_employee = $db->prepare("
                                SELECT employee_first_name, employee_last_name
                                FROM idk_employee
                                WHERE employee_id = :employee_id");

                                $query_employee->execute(array(
                                  ':employee_id' => $offer_employee_id
                                ));

                                $row_employee = $query_employee->fetch();

                                echo $row_employee['employee_first_name'] . ' ' . $row_employee['employee_last_name'];
                              }
                              ?>
                            </td>
                            <td>
                              <?php echo number_format($offer_to_pay, 2, ',', '.'); ?> KM
                            </td>
                            <td data-sort="<?php echo $offer_created_at; ?>">
                              <?php echo $offer_created_at_new_format; ?>
                            </td>
                            <td class="text-center">
                              <div class="btn-group material-btn-group">
                                <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
                                <ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
                                  <li>
                                    <a href="<?php getSiteUrl(); ?>idkadmin/offers?page=open&offer_id=<?php echo $offer_id; ?>" class="material-dropdown-menu__link">
                                      <i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori
                                    </a>
                                  </li>

                                  <li>
                                    <a href="<?php getSiteUrl(); ?>idkadmin/offers?page=edit&offer_id=<?php echo $offer_id; ?>" class="material-dropdown-menu__link">
                                      <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi
                                    </a>
                                  </li>

                                  <li>
                                    <a href="#" data-toggle="modal" data-target="#createOrderModal" class="create_order material-dropdown-menu__link">
                                      <i class="fa fa-shopping-cart" aria-hidden="true"></i> Narudžba
                                    </a>
                                  </li>

                                  <li class="idk_dropdown_danger">
                                    <a href="#" data="<?php getSiteUrl(); ?>idkadmin/offers?page=archive&id=<?php echo $offer_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link">
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
                        <!-- Modal - archive -->
                        <div class="modal material-modal material-modal_danger fade" id="archiveModal">
                          <div class="modal-dialog">
                            <div class="modal-content material-modal__content">
                              <div class="modal-header material-modal__header">
                                <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title material-modal__title">Arhiviranje</h4>
                              </div>
                              <div class="modal-body material-modal__body">
                                <p>Jeste li sigurni da želite arhivirati ponudu?</p>
                              </div>
                              <div class="modal-footer material-modal__footer">
                                <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                <a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
                              </div>
                            </div>
                          </div>
                        </div> <!-- End modal - archive -->

                        <!-- Modal - create order -->
                        <div class="modal material-modal material-modal_primary fade" id="createOrderModal">
                          <div class="modal-dialog">
                            <div class="modal-content material-modal__content">
                              <form action="<?php getSiteURL(); ?>idkadmin/do.php?form=create_order_from_offer" method="post">
                                <input type="hidden" name="offer_id" value="<?php echo $offer_id; ?>">
                                <div class="modal-header material-modal__header">
                                  <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                  <h4 class="modal-title material-modal__title">Kreiranje narudžbe iz ponude</h4>
                                </div>
                                <div class="modal-body material-modal__body">
                                  <p>Jeste li sigurni da želite kreirati narudžbu iz ponude?</p>
                                  <br>
                                  <p>Način plaćanja</p>
                                  <div class="form-group">
                                    <div class="form-check">
                                      <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_1" value="1">
                                      <label class="form-check-label" for="offer_pay_method_1">
                                        Gotovina KM
                                      </label>
                                    </div>
                                    <div class="form-check">
                                      <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_2" value="2" checked>
                                      <label class="form-check-label" for="offer_pay_method_2">
                                        Virmansko
                                      </label>
                                    </div>
                                    <div class="form-check">
                                      <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_3" value="3">
                                      <label class="form-check-label" for="offer_pay_method_3">
                                        Visa kreditna kartica
                                      </label>
                                    </div>
                                    <div class="form-check">
                                      <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_4" value="4">
                                      <label class="form-check-label" for="offer_pay_method_4">
                                        MasterCard kreditna kartica
                                      </label>
                                    </div>
                                    <div class="form-check">
                                      <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_5" value="5">
                                      <label class="form-check-label" for="offer_pay_method_5">
                                        BamCard kreditna kartica
                                      </label>
                                    </div>
                                    <div class="form-check">
                                      <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_6" value="6">
                                      <label class="form-check-label" for="offer_pay_method_6">
                                        Ček
                                      </label>
                                    </div>
                                  </div>
                                </div>
                                <div class="modal-footer material-modal__footer">
                                  <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                  <button type="submit" class="btn btn-primary material-btn material-btn_primary">Kreiraj narudžbu</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div> <!-- End modal - create order -->

                      </tbody>
                    </table>
                    <!-- End offers table -->


                    <!-- Orffer IDS for multiple archiving -->
                    <input type="hidden" name="offer_ids[]" id="offer_ids">

                    <br>
                    <a href="#" data="<?php getSiteUrl(); ?>idkadmin/offers?page=archive_multiple&ids=" data-toggle="modal" data-target="#archiveMultipleModal" class="archive-multiple btn material-btn material-btn-icon-danger material-btn_danger main-container__column material-btn-icon-responsive">
                      <i class="fa fa-trash-o" aria-hidden="true"></i> <span>&nbsp;&nbsp;Arhiviraj označene ponude</span>
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
                            <p>Jeste li sigurni da želite arhivirati označene ponude?</p>
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
                      var offerIds = [];
                      $(".material-checkbox").click(function() {
                        var offerId = $(this).attr("id").split("checkbox_")[1];
                        if (!offerIds.includes(offerId)) {
                          offerIds.push(offerId);
                        } else {
                          var index = offerIds.indexOf(offerId);
                          offerIds.splice(index, 1);
                        }
                        $("#offer_ids").val(offerIds);
                      });
                      $(".archive-multiple").click(function() {
                        var addressValueMultiple = $(this).attr("data");
                        document.getElementById("archive_multiple_link").href = addressValueMultiple + offerIds;
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
           * 							OPEN OFFER
           * *********************************************************/
        case "open":

          $offer_id = $_GET['offer_id'];

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
            SELECT t1.client_name, t2.offer_status, t2.offer_key
            FROM idk_client t1
            INNER JOIN idk_offer t2
            ON t1.client_id = t2.client_id
            WHERE t2.offer_id = :offer_id");

          $query->execute(array(
            ':offer_id' => $offer_id
          ));

          $client = $query->fetch();

          $client_name = $client['client_name'];
          $offer_status = $client['offer_status'];
          $offer_key = $client['offer_key'];

        ?>

          <div class="row">
            <div class="col-xs-8">
              <h1><i class="fa fa-file idk_color_green" aria-hidden="true"></i> Ponuda #<?php echo "${offer_id} - Klijent: ${client_name}"; ?></h1>
            </div>
            <div class="col-xs-4 text-right idk_margin_top10">
              <a href="<?php getSiteUrl(); ?>idkadmin/offers?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
            </div>
            <div class="col-xs-12">
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="content_box">
                <div class="row">
                  <div class="col-md-12">
                    <a href="<?php getSiteUrl(); ?>idkadmin/print_offer?id=<?php echo $offer_id; ?>&offer=<?php echo $offer_key; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" id="idk_print_report_btn" target="_blank">
                      <i class="fa fa-print" aria-hidden="true"></i> <span>Print</span>
                    </a>
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
                          FROM idk_product_offer
                          WHERE offer_id = :offer_id");

                        $query->execute(array(
                          ':offer_id' => $offer_id
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

                        ?>

                          <tr>
                            <td>
                              <a href="<?php getSiteUrl(); ?>idkadmin/products?page=open&id=<?php echo $product_id; ?>">
                                <?php echo $product_name; ?>
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
                  SELECT offer_status, offer_total_price, offer_total_tax, offer_total_rabat, offer_to_pay, offer_note
                  FROM idk_offer
                  WHERE offer_id = :offer_id");

                $query->execute(array(
                  ':offer_id' => $offer_id
                ));

                $offer = $query->fetch();

                $offer_note = $offer['offer_note'];
                $offer_total_price = $offer['offer_total_price'];
                $offer_total_tax = $offer['offer_total_tax'];
                $offer_total_rabat = $offer['offer_total_rabat'];
                $offer_to_pay = $offer['offer_to_pay'];

                ?>

                <!-- Display offer_note -->
                <?php if (isset($offer_note)) { ?>
                  <div class="row idk_order_note_row">
                    <strong class="col-sm-3 text-right">Bilješka:</strong>
                    <div class="col-sm-9"><?php echo $offer_note; ?></div>
                  </div>
                <?php } ?>

                <!-- Get offer information -->
                <div class="row idk_order_open_total">
                  <strong class="col-sm-3 text-right">Ukupno:</strong>
                  <div class="col-sm-9"><?php echo number_format($offer_total_price, 3, ',', '.') . " KM"; ?></div>
                </div>
                <div class="row idk_order_open_total">
                  <strong class="col-sm-3 text-right">Rabat:</strong>
                  <div class="col-sm-9"><?php if ($offer_total_rabat) {
                                          echo number_format($offer_total_rabat, 3, ',', '.') . " KM";
                                        } else {
                                          echo "0,00 KM";
                                        } ?></div>
                </div>
                <div class="row idk_order_open_total">
                  <strong class="col-sm-3 text-right">PDV:</strong>
                  <div class="col-sm-9"><?php echo number_format($offer_total_tax, 3, ',', '.') . " KM"; ?></div>
                </div>
                <div class="row idk_order_open_total">
                  <strong class="col-sm-3 text-right">ZA PLATITI:</strong>
                  <div class="col-sm-9"><?php echo number_format($offer_to_pay, 2, ',', '.') . " KM"; ?></div>
                </div>

                <div class="row">
                  <?php
                  if ($getEmployeeStatus == 1) {
                  ?>

                    <br>
                    <hr>
                    <br>

                    <form class="form-horizontal">
                      <div class="form-group">
                        <label for="offer_create_order" class="col-sm-6 control-label">Kreiraj narudžbu iz ponude:</label>
                        <div class="col-sm-6">
                          <a href="#" id="offer_create_order" data-toggle="modal" data-target="#createOrderModal" class="create_order btn btn-primary material-btn material-btn_primary">
                            <i class="fa fa-shopping-cart" aria-hidden="true"></i> Kreiraj narudžbu
                          </a>
                        </div>
                      </div>
                    </form>

                    <!-- Modal - create order -->
                    <div class="modal material-modal material-modal_primary fade" id="createOrderModal">
                      <div class="modal-dialog">
                        <div class="modal-content material-modal__content">
                          <form action="<?php getSiteURL(); ?>idkadmin/do.php?form=create_order_from_offer" method="post">
                            <input type="hidden" name="offer_id" value="<?php echo $offer_id; ?>">
                            <div class="modal-header material-modal__header">
                              <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                              <h4 class="modal-title material-modal__title">Kreiranje narudžbe iz ponude</h4>
                            </div>
                            <div class="modal-body material-modal__body">
                              <p>Jeste li sigurni da želite kreirati narudžbu iz ponude?</p>
                              <br>
                              <p>Način plaćanja</p>
                              <div class="form-group">
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_1" value="1">
                                  <label class="form-check-label" for="offer_pay_method_1">
                                    Gotovina KM
                                  </label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_2" value="2" checked>
                                  <label class="form-check-label" for="offer_pay_method_2">
                                    Virmansko
                                  </label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_3" value="3">
                                  <label class="form-check-label" for="offer_pay_method_3">
                                    Visa kreditna kartica
                                  </label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_4" value="4">
                                  <label class="form-check-label" for="offer_pay_method_4">
                                    MasterCard kreditna kartica
                                  </label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_5" value="5">
                                  <label class="form-check-label" for="offer_pay_method_5">
                                    BamCard kreditna kartica
                                  </label>
                                </div>
                                <div class="form-check">
                                  <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_6" value="6">
                                  <label class="form-check-label" for="offer_pay_method_6">
                                    Ček
                                  </label>
                                </div>
                              </div>
                            </div>
                            <div class="modal-footer material-modal__footer">
                              <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                              <button type="submit" class="btn btn-primary material-btn material-btn_primary">Kreiraj narudžbu</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div> <!-- End modal - create order -->

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

          //EDIT OFFER

          case "edit":

            $offer_id = $_GET['offer_id'];
  
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
              SELECT t1.client_name, t2.offer_status, t2.offer_key
              FROM idk_client t1
              INNER JOIN idk_offer t2
              ON t1.client_id = t2.client_id
              WHERE t2.offer_id = :offer_id");
  
            $query->execute(array(
              ':offer_id' => $offer_id
            ));
  
            $client = $query->fetch();
  
            $client_name = $client['client_name'];
            $offer_status = $client['offer_status'];
            $offer_key = $client['offer_key'];
  
          ?>
  
            <div class="row">
              <div class="col-xs-8">
                <h1><i class="fa fa-file idk_color_green" aria-hidden="true"></i> Ponuda #<?php echo "${offer_id} - Klijent: ${client_name}"; ?></h1>
              </div>
              <div class="col-xs-4 text-right idk_margin_top10">
                <a href="<?php getSiteUrl(); ?>idkadmin/offers?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
                      <a href="<?php getSiteUrl(); ?>idkadmin/print_offer?id=<?php echo $offer_id; ?>&offer=<?php echo $offer_key; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" id="idk_print_report_btn" target="_blank">
                        <i class="fa fa-print" aria-hidden="true"></i> <span>Print</span>
                      </a>

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
                          <form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=offer_change_client" method="post" role="form" class="form-horizontal">
                            <input type="hidden" name="offer_id" id="offer_id" value="<?php echo $offer_id; ?>">
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
                          <form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=offer_add_product" method="post" role="form" class="form-horizontal">
                            <input type="hidden" name="offer_id" id="offer_id" value="<?php echo $offer_id; ?>">
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

                    <a href="#" data="<?php getSiteUrl(); ?>idkadmin/offers?page=delete_multiple_products&offer_id=<?php echo $offer_id; ?>&ids=" data-toggle="modal" data-target="#deleteMultipleProductsModal" class="delete-multiple btn material-btn material-btn-icon-danger material-btn_danger main-container__column">
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
                    <form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=edit_offer" method="post">

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

                      <input type="hidden" name="offer_id" value="<?php echo $offer_id; ?>">
                      <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">

                      <button type="submit" id="idk_btn_edit_offer" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-check-square" aria-hidden="true"></i> <span>Ažuriraj</span></button>

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
                        echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali proizvod u ponudu.</div>';
                      } elseif ($mess == 4) {
                        echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali proizvod iz ponude.</div>';
                      } elseif ($mess == 5) {
                        echo '<div class="alert material-alert material-alert_danger">Greška: Prvo morate označiti proizvode koje želite obrisati.</div>';
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
                                "width": "10%"
                              },
                              {
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
                                "width": "10%"
                              },
                              {
                                "width": "10%"
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
                            <th>Jed. VPC</th>
                            <th>Količina</th>
                            <th>Ukupna cijena</th>
                            <th>Rabat (%)</th>
                            <th>Iznos rabata</th>
                          </tr>
                        </thead>
  
                        <tbody>
  
                          <!-- Get data for product -->
                          <?php
  
                          $query = $db->prepare("
                            SELECT *
                            FROM idk_product_offer
                            WHERE offer_id = :offer_id");
  
                          $query->execute(array(
                            ':offer_id' => $offer_id
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
                                <?php if ($product_rabat_percentage and $product_rabat_value) {
                                  echo number_format($product_rabat_value, 3, ',', '.') . " KM";
                                } else {
                                  echo "0,00 KM";
                                } ?>
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
                    SELECT offer_status, offer_total_price, offer_total_tax, offer_total_rabat, offer_to_pay, offer_note
                    FROM idk_offer
                    WHERE offer_id = :offer_id");
  
                  $query->execute(array(
                    ':offer_id' => $offer_id
                  ));
  
                  $offer = $query->fetch();
  
                  $offer_note = $offer['offer_note'];
                  $offer_total_price = $offer['offer_total_price'];
                  $offer_total_tax = $offer['offer_total_tax'];
                  $offer_total_rabat = $offer['offer_total_rabat'];
                  $offer_to_pay = $offer['offer_to_pay'];
  
                  ?>
  
                  <!-- Display offer_note -->
                  <?php if (isset($offer_note)) { ?>
                    <div class="row idk_order_note_row">
                      <strong class="col-sm-3 text-right">Bilješka:</strong>
                      <div class="col-sm-9"><?php echo $offer_note; ?></div>
                    </div>
                  <?php } ?>
  
                  <!-- Get offer information -->
                  <div class="row idk_order_open_total">
                    <strong class="col-sm-3 text-right">Ukupno:</strong>
                    <div class="col-sm-9"><?php echo number_format($offer_total_price, 3, ',', '.') . " KM"; ?></div>
                  </div>
                  <div class="row idk_order_open_total">
                    <strong class="col-sm-3 text-right">Rabat:</strong>
                    <div class="col-sm-9"><?php if ($offer_total_rabat) {
                                            echo number_format($offer_total_rabat, 3, ',', '.') . " KM";
                                          } else {
                                            echo "0,00 KM";
                                          } ?></div>
                  </div>
                  <div class="row idk_order_open_total">
                    <strong class="col-sm-3 text-right">PDV:</strong>
                    <div class="col-sm-9"><?php echo number_format($offer_total_tax, 3, ',', '.') . " KM"; ?></div>
                  </div>
                  <div class="row idk_order_open_total">
                    <strong class="col-sm-3 text-right">ZA PLATITI:</strong>
                    <div class="col-sm-9"><?php echo number_format($offer_to_pay, 2, ',', '.') . " KM"; ?></div>
                  </div>
  
                  <div class="row">
                    <?php
                    if ($getEmployeeStatus == 1) {
                    ?>
  
                      <br>
                      <hr>
                      <br>
  
                      <form class="form-horizontal">
                        <div class="form-group">
                          <label for="offer_create_order" class="col-sm-6 control-label">Kreiraj narudžbu iz ponude:</label>
                          <div class="col-sm-6">
                            <a href="#" id="offer_create_order" data-toggle="modal" data-target="#createOrderModal" class="create_order btn btn-primary material-btn material-btn_primary">
                              <i class="fa fa-shopping-cart" aria-hidden="true"></i> Kreiraj narudžbu
                            </a>
                          </div>
                        </div>
                      </form>
  
                      <!-- Modal - create order -->
                      <div class="modal material-modal material-modal_primary fade" id="createOrderModal">
                        <div class="modal-dialog">
                          <div class="modal-content material-modal__content">
                            <form action="<?php getSiteURL(); ?>idkadmin/do.php?form=create_order_from_offer" method="post">
                              <input type="hidden" name="offer_id" value="<?php echo $offer_id; ?>">
                              <div class="modal-header material-modal__header">
                                <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title material-modal__title">Kreiranje narudžbe iz ponude</h4>
                              </div>
                              <div class="modal-body material-modal__body">
                                <p>Jeste li sigurni da želite kreirati narudžbu iz ponude?</p>
                                <br>
                                <p>Način plaćanja</p>
                                <div class="form-group">
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_1" value="1">
                                    <label class="form-check-label" for="offer_pay_method_1">
                                      Gotovina KM
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_2" value="2" checked>
                                    <label class="form-check-label" for="offer_pay_method_2">
                                      Virmansko
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_3" value="3">
                                    <label class="form-check-label" for="offer_pay_method_3">
                                      Visa kreditna kartica
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_4" value="4">
                                    <label class="form-check-label" for="offer_pay_method_4">
                                      MasterCard kreditna kartica
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_5" value="5">
                                    <label class="form-check-label" for="offer_pay_method_5">
                                      BamCard kreditna kartica
                                    </label>
                                  </div>
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="offer_pay_method" id="offer_pay_method_6" value="6">
                                    <label class="form-check-label" for="offer_pay_method_6">
                                      Ček
                                    </label>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer material-modal__footer">
                                <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                <button type="submit" class="btn btn-primary material-btn material-btn_primary">Kreiraj narudžbu</button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div> <!-- End modal - create order -->
  
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

            $offer_id = $_GET['id'];

            //Get client name
            $query_select = $db->prepare("
							SELECT t1.client_name
              FROM idk_client t1
              INNER JOIN idk_offer t2
              ON t1.client_id = t2.client_id
							WHERE t2.offer_id = :offer_id");

            $query_select->execute(array(
              ':offer_id' => $offer_id
            ));

            $client_select = $query_select->fetch();

            $client_name = $client_select['client_name'];

            //Save changes to order in db
            $query = $db->prepare("
							UPDATE idk_offer
							SET offer_status = :offer_status
							WHERE offer_id = :offer_id");

            $query->execute(array(
              ':offer_status' => 0,
              ':offer_id' => $offer_id
            ));

            //Add to log
            $log_desc = "Arhivirao ponudu #${offer_id} za klijenta: ${client_name}";
            $log_date = date('Y-m-d H:i:s');
            addLog($logged_employee_id, $log_desc, $log_date);

            header("Location: " . getSiteUrlr() . "idkadmin/offers?page=list&mess=4");
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

            $offer_ids = $_GET['ids'] != "" ? explode(',', $_GET['ids']) : NULL;

            if (isset($offer_ids) and count($offer_ids) > 0) {
              foreach ($offer_ids as $offer_id) {

                //Get client name
                $query_select = $db->prepare("
                SELECT t1.client_name
                FROM idk_client t1
                INNER JOIN idk_offer t2
                ON t1.client_id = t2.client_id
                WHERE t2.offer_id = :offer_id");

                $query_select->execute(array(
                  ':offer_id' => $offer_id
                ));

                $client_select = $query_select->fetch();

                $client_name = $client_select['client_name'];

                //Save changes to order in db
                $query = $db->prepare("
                UPDATE idk_offer
                SET offer_status = :offer_status
                WHERE offer_id = :offer_id");

                $query->execute(array(
                  ':offer_status' => 0,
                  ':offer_id' => $offer_id
                ));

                //Add to log
                $log_desc = "Arhivirao ponudu #${offer_id} za klijenta: ${client_name}";
                $log_date = date('Y-m-d H:i:s');
                addLog($logged_employee_id, $log_desc, $log_date);
              }
              header("Location: " . getSiteUrlr() . "idkadmin/offers?page=list&mess=5");
            } else {
              header("Location: " . getSiteUrlr() . "idkadmin/offers?page=list&mess=6");
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

          case "delete_multiple_products":

            if ($getEmployeeStatus == 1) {
  
              $offer_id = isset($_GET['offer_id']) ? $_GET['offer_id'] : NULL;
              $product_ids = $_GET['ids'] != "" ? explode(',', $_GET['ids']) : NULL;
  
              if (isset($offer_id) and isset($product_ids) and count($product_ids) > 0) {
                foreach ($product_ids as $product_id) {

                  //Delete from product order
                  $query_delete = $db->prepare("
                    DELETE
                    FROM idk_product_offer
                    WHERE offer_id = :offer_id AND product_id = :product_id");
  
                  $query_delete->execute(array(
                    ':offer_id' => $offer_id,
                    ':product_id' => $product_id
                  ));
                }
  
                //Get product order info
                $product_offer_query = $db->prepare("
                  SELECT *
                  FROM idk_product_offer
                  WHERE offer_id = :offer_id");
  
                $product_offer_query->execute(array(
                  ':offer_id' => $offer_id
                ));
  
                $offer_total_price = 0.000;
                $offer_total_tax = 0.000;
                $offer_total_rabat = 0.000;
                $offer_to_pay = 0.000;
  
                while ($product_offer = $product_offer_query->fetch()) {
	
                  $product_price = $product_offer['product_price'];
                  $product_quantity = $product_offer['product_quantity'];
                  $product_tax_percentage = $product_offer['product_tax_percentage'];
                  $product_rabat_percentage = $product_offer['product_rabat_percentage'];
                  $product_rabat_value = $product_price * $product_rabat_percentage / 100;
                  $product_tax_value = ($product_price - $product_rabat_value) * $product_tax_percentage / 100;
      
                  //Calculate product to pay again
                  // $product_price = $product_price - ($product_price * $product_rabat_percentage / 100); //Calculate price with rabat
                  $product_total_price = $product_price * $product_quantity; //Price without rabat
                  $product_total_tax = $product_tax_value * $product_quantity;
                  $product_total_rabat = $product_rabat_value * $product_quantity; //Calculate total rabat value
                  $product_to_pay = $product_total_price + $product_total_tax - $product_total_rabat;
      
                  $offer_total_price += $product_total_price;
                  $offer_total_tax += $product_total_tax;
                  $offer_total_rabat += $product_total_rabat;
                  $offer_to_pay += $product_to_pay;
                  $offer_to_pay = round($offer_to_pay * 2, 1) / 2;
                }
            
      
                //Update total price and tax of order
                $update_total_price_tax_query = $db->prepare("
                  UPDATE idk_offer
                  SET	offer_total_price = :offer_total_price, offer_total_tax = :offer_total_tax, offer_total_rabat = :offer_total_rabat, offer_to_pay = :offer_to_pay
                  WHERE offer_id = :offer_id");
      
                $update_total_price_tax_query->execute(array(
                  ':offer_id' => $offer_id,
                  ':offer_total_price' => $offer_total_price,
                  ':offer_total_tax' => $offer_total_tax,
                  ':offer_total_rabat' => $offer_total_rabat,
                  ':offer_to_pay' => $offer_to_pay
                ));
                
  
                //Add to log
                $log_desc = "Uredio ponudu #${offer_id}";
                $log_date = date('Y-m-d H:i:s');
                addLog($logged_employee_id, $log_desc, $log_date);
  
                header("Location: " . getSiteUrlr() . "idkadmin/offers?page=edit&offer_id=${offer_id}&mess=4");
              } else {
                header("Location: " . getSiteUrlr() . "idkadmin/offers?page=edit&offer_id=${offer_id}&mess=5");
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