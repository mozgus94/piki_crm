<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
  $page = $_REQUEST["page"];
} else {
  $page = "list_employees";
  header("Location: archive?page=list_employees");
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
         * 							LIST ALL ARCHIVED EMPLOYEES
         * *********************************************************/
        case "list_employees":

          if ($getEmployeeStatus == 1) {
      ?>

            <div class="row">
              <div class="col-xs-8">
                <h1><i class="fa fa-users" aria-hidden="true"></i> Arhivirani Zaposlenici</h1>
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
                        echo '<div class="alert material-alert material-alert_success">Uspješno ste aktivirali profil zaposlenika.</div>';
                      }
                      ?>

                      <!-- Filling the table with data -->
                      <script type="text/javascript">
                        $(document).ready(function() {
                          $('#idk_table').DataTable({

                            responsive: true,

                            "order": [
                              [1, "asc"]
                            ],

                            "bAutoWidth": false,

                            "aoColumns": [{
                                "width": "5%",
                                "bSortable": false
                              },
                              {
                                "width": "25%"
                              },
                              {
                                "width": "20%"
                              },
                              {
                                "width": "25%"
                              },
                              {
                                "width": "15%"
                              },
                              {
                                "width": "10%",
                                "bSortable": false
                              }
                            ]
                          });
                        });
                      </script>

                      <!-- Employees table -->
                      <table id="idk_table" class="display" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th></th>
                            <th>Ime i prezime</th>
                            <th>Telefon</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th></th>
                          </tr>
                        </thead>

                        <tbody>

                          <!-- Get data for employee -->
                          <?php
                          $query = $db->prepare("
														SELECT employee_id, employee_first_name, employee_last_name, employee_login_email, employee_status, employee_image
														FROM idk_employee
														WHERE employee_active = 0");

                          $query->execute();

                          while ($employee = $query->fetch()) {

                            $employee_id = $employee['employee_id'];
                            $employee_first_name = $employee['employee_first_name'];
                            $employee_last_name = $employee['employee_last_name'];
                            $employee_login_email = $employee['employee_login_email'];
                            $employee_image = $employee['employee_image'];

                            if ($employee['employee_status'] == 0) {
                              $employee_status = "Arhiviran";
                            } elseif ($employee['employee_status'] == 1) {
                              $employee_status = "Administrator";
                            } elseif ($employee['employee_status'] == 2) {
                              $employee_status = "Komercijalista";
                            } elseif ($employee['employee_status'] == 3) {
                              $employee_status = "Skladištar";
                            }

                            //Get primary phone from idk_employee_info
                            $query_phone = $db->prepare("
															SELECT ei_data
															FROM idk_employee_info
															WHERE ei_group = :ei_group AND ei_primary = :ei_primary AND employee_id = :employee_id");

                            $query_phone->execute(array(
                              ':ei_group' => 1,
                              ':ei_primary' => 1,
                              ':employee_id' => $employee_id
                            ));

                            $number_of_rows = $query_phone->rowCount();

                            if ($number_of_rows > 0) {
                              $employee_info = $query_phone->fetch();
                              $employee_phone = $employee_info['ei_data'];
                            } else {
                              $employee_phone = "";
                            }

                          ?>

                            <tr>
                              <td class="text-center">
                                <a href="<?php getSiteUrl(); ?>idkadmin/employees?page=open&id=<?php echo $employee_id; ?>">
                                  <img class="idk_profile_img" src="<?php getSiteUrl(); ?>idkadmin/files/employees/images/<?php echo $employee_image; ?>">
                                </a>
                              </td>
                              <td>
                                <a href="<?php getSiteUrl(); ?>idkadmin/employees?page=open&id=<?php echo $employee_id; ?>">
                                  <?php echo "${employee_first_name} ${employee_last_name}"; ?>
                                </a>
                              </td>
                              <td>
                                <a href="tel:<?php echo $employee_phone; ?>">
                                  <?php echo $employee_phone; ?>
                                </a>
                              </td>
                              <td>
                                <a href="mailto:<?php echo $employee_login_email; ?>">
                                  <?php echo $employee_login_email; ?>
                                </a>
                              </td>
                              <td>
                                <?php echo $employee_status; ?>
                              </td>
                              <td class="text-center">
                                <a href="<?php getSiteUrl(); ?>idkadmin/archive?page=activate_employee&employee_id=<?php echo $employee_id; ?>">
                                  <i class="fa fa-undo" aria-hidden="true"></i> Vrati u aktivne
                                </a>
                              </td>
                            </tr>

                          <?php } ?>

                        </tbody>
                      </table>
                      <!-- End employees table -->

                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php
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
           * 							LIST ALL ARCHIVED CLIENTS
           * *********************************************************/
        case "list_clients":

          if ($getEmployeeStatus == 1) {
          ?>

            <div class="row">
              <div class="col-xs-8">
                <h1><i class="fa fa-briefcase" aria-hidden="true"></i> Arhivirani Klijenti</h1>
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
                        echo '<div class="alert material-alert material-alert_success">Uspješno ste aktivirali profil klijenta.</div>';
                      }
                      ?>

                      <!-- Filling the table with data -->
                      <script type="text/javascript">
                        $(document).ready(function() {
                          $('#idk_table').DataTable({

                            responsive: true,

                            "order": [
                              [1, "asc"]
                            ],

                            "bAutoWidth": false,

                            "aoColumns": [{
                                "width": "5%",
                                "bSortable": false
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
                                "width": "15%"
                              },
                              {
                                "width": "10%",
                                "bSortable": false
                              }
                            ]
                          });
                        });
                      </script>

                      <!-- Clients table -->
                      <table id="idk_table" class="display" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th></th>
                            <th>Naziv</th>
                            <th>ID Broj</th>
                            <th>Općina</th>
                            <th>Telefon</th>
                            <th>Email</th>
                            <th></th>
                          </tr>
                        </thead>

                        <tbody>

                          <!-- Get data for client -->
                          <?php
                          $query = $db->prepare("
														SELECT client_id, client_name, client_id_number, client_city, client_image
														FROM idk_client
														WHERE client_active = 0");

                          $query->execute();

                          while ($client = $query->fetch()) {

                            $client_id = $client['client_id'];
                            $client_name = $client['client_name'];
                            $client_id_number = $client['client_id_number'];
                            $client_city = $client['client_city'];
                            $client_image = $client['client_image'];

                            //Get primary phone and email from idk_client_info
                            $query_info = $db->prepare("
															SELECT ci_data
															FROM idk_client_info
															WHERE ci_group = :ci_group AND ci_primary = :ci_primary AND client_id = :client_id");

                            //Get phone
                            $query_info->execute(array(
                              ':ci_group' => 1,
                              ':ci_primary' => 1,
                              ':client_id' => $client_id
                            ));

                            $number_of_rows = $query_info->rowCount();

                            if ($number_of_rows > 0) {
                              $client_info = $query_info->fetch();
                              $client_phone = $client_info['ci_data'];
                            } else {
                              $client_phone = "";
                            }

                            // Get email
                            $query_info->execute(array(
                              ':ci_group' => 2,
                              ':ci_primary' => 1,
                              ':client_id' => $client_id
                            ));

                            $number_of_rows = $query_info->rowCount();

                            if ($number_of_rows > 0) {
                              $client_info = $query_info->fetch();
                              $client_email = $client_info['ci_data'];
                            } else {
                              $client_email = "";
                            }

                          ?>

                            <tr>
                              <td class="text-center">
                                <a href="<?php getSiteUrl(); ?>idkadmin/clients?page=open&id=<?php echo $client_id; ?>">
                                  <img class="idk_profile_img" src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>">
                                </a>
                              </td>
                              <td>
                                <a href="<?php getSiteUrl(); ?>idkadmin/clients?page=open&id=<?php echo $client_id; ?>">
                                  <?php echo $client_name; ?>
                                </a>
                              </td>
                              <td>
                                <?php echo $client_id_number; ?>
                              </td>
                              <td>
                                <?php echo $client_city; ?>
                              </td>
                              <td>
                                <a href="tel:<?php echo $client_phone; ?>">
                                  <?php echo $client_phone; ?>
                                </a>
                              </td>
                              <td>
                                <a href="mailto:<?php echo $client_email; ?>">
                                  <?php echo $client_email; ?>
                                </a>
                              </td>
                              <td class="text-center">
                                <a href="<?php getSiteUrl(); ?>idkadmin/archive?page=activate_client&client_id=<?php echo $client_id; ?>">
                                  <i class="fa fa-undo" aria-hidden="true"></i> Vrati u aktivne
                                </a>
                              </td>
                            </tr>

                          <?php } ?>

                        </tbody>
                      </table>
                      <!-- End clients table -->

                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php
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
           * 							LIST ALL ARCHIVED PRODUCTS
           * *********************************************************/
        case "list_products":

          if ($getEmployeeStatus == 1) {
          ?>

            <div class="row">
              <div class="col-xs-8">
                <h1><i class="fa fa-tasks" aria-hidden="true"></i> Arhivirani Proizvodi</h1>
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
                        echo '<div class="alert material-alert material-alert_success">Uspješno ste aktivirali proizvod.</div>';
                      }
                      ?>

                      <!-- Filling the table with data -->
                      <script type="text/javascript">
                        $(document).ready(function() {
                          $('#idk_table').DataTable({

                            responsive: true,

                            "order": [
                              [1, "asc"]
                            ],

                            "bAutoWidth": false,

                            "aoColumns": [{
                                "width": "5%",
                                "bSortable": false
                              },
                              {
                                "width": "35%"
                              },
                              {
                                "width": "25%"
                              },
                              {
                                "width": "25%"
                              },
                              {
                                "width": "10%",
                                "bSortable": false
                              }
                            ]
                          });
                        });
                      </script>

                      <!-- Products table -->
                      <table id="idk_table" class="display" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th></th>
                            <th>Naziv</th>
                            <th>Cijena</th>
                            <th>Na stanju</th>
                            <th></th>
                          </tr>
                        </thead>

                        <tbody>

                          <!-- Get data for product -->
                          <?php
                          $query = $db->prepare("
														SELECT product_id, product_name, product_price, product_quantity, product_image, product_currency
														FROM idk_product
														WHERE product_active = 0");

                          $query->execute();

                          while ($product = $query->fetch()) {

                            $product_id = $product['product_id'];
                            $product_name = $product['product_name'];
                            $product_price = $product['product_price'];
                            $product_quantity = $product['product_quantity'];
                            $product_image = $product['product_image'];
                            $product_currency = $product['product_currency'];

                          ?>

                            <tr>
                              <td class="text-center">
                                <?php if ($product_image) { ?>
                                  <a href="<?php getSiteUrl(); ?>idkadmin/products?page=open&id=<?php echo $product_id; ?>">
                                    <img class="idk_profile_img" src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>">
                                  </a>
                                <?php } ?>
                              </td>
                              <td>
                                <a href="<?php getSiteUrl(); ?>idkadmin/products?page=open&id=<?php echo $product_id; ?>">
                                  <?php echo $product_name; ?>
                                </a>
                              </td>
                              <td>
                                <?php echo number_format($product_price, 3, ',', '.') . ' ' . $product_currency; ?>
                              </td>
                              <td>
                                <?php echo $product_quantity; ?>
                              </td>
                              <td class="text-center">
                                <a href="<?php getSiteUrl(); ?>idkadmin/archive?page=activate_product&product_id=<?php echo $product_id; ?>">
                                  <i class="fa fa-undo" aria-hidden="true"></i> Vrati u aktivne
                                </a>
                              </td>
                            </tr>

                          <?php } ?>

                        </tbody>
                      </table>
                      <!-- End products table -->

                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php
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
           * 							LIST ALL ARCHIVED ORDERS
           * *********************************************************/
        case "list_orders":

          if ($getEmployeeStatus == 1) {
          ?>

            <div class="row">
              <div class="col-xs-8">
                <h1><i class="fa fa-shopping-cart" aria-hidden="true"></i> Arhivirane Narudžbe</h1>
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
                        echo '<div class="alert material-alert material-alert_success">Uspješno ste aktivirali narudžbu.</div>';
                      }
                      ?>

                      <!-- Filling the table with data -->
                      <script type="text/javascript">
                        $(document).ready(function() {
                          $('#idk_table').DataTable({

                            responsive: true,

                            "order": [
                              [0, "desc"]
                            ],

                            "bAutoWidth": false,

                            "aoColumns": [{
                                "width": "10%"
                              },
                              {
                                "width": "20%"
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
                                "width": "10%"
                              }
                            ]
                          });
                        });
                      </script>

                      <!-- Products table -->
                      <table id="idk_table" class="display" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Klijent</th>
                            <th>Ukupna cijena</th>
                            <th>Porez</th>
                            <th>Za platiti</th>
                            <th>Datum</th>
                            <th></th>
                          </tr>
                        </thead>

                        <tbody>

                          <!-- Get data for order -->
                          <?php

                          $query = $db->prepare("
                            SELECT t1.order_id, t1.client_id, t1.employee_id, t1.order_total_price, t1.order_total_tax, t1.order_to_pay, t1.created_at, t2.client_name
                            FROM idk_order t1, idk_client t2
                            WHERE t1.order_status = :order_status AND t2.client_id = t1.client_id");

                          $query->execute(array(
                            ':order_status' => 0
                          ));

                          while ($order = $query->fetch()) {

                            $order_id = $order['order_id'];
                            $client_id = $order['client_id'];
                            $client_name = $order['client_name'];
                            $order_employee_id = $order['employee_id'];
                            $order_total_price = $order['order_total_price'];
                            $order_total_tax = $order['order_total_tax'];
                            $order_to_pay = $order['order_to_pay'];
                            $order_created_at = $order['created_at'];
                            $order_created_at_new_format = date('d.m.Y.', strtotime($order['created_at']));

                          ?>

                            <tr>
                              <td class="text-center">
                                <?php echo $order_id; ?>
                              </td>
                              <td>
                                <a href="<?php getSiteUrl(); ?>idkadmin/orders?page=open&order_id=<?php echo $order_id; ?>">
                                  <?php echo $client_name; ?>
                                </a>
                              </td>
                              <td>
                                <?php echo number_format($order_total_price, 3, ',', '.'); ?> KM
                              </td>
                              <td>
                                <?php echo $order_total_tax; ?> KM
                              </td>
                              <td>
                                <?php echo $order_to_pay; ?> KM
                              </td>
                              <td data-sort="<?php echo $order_created_at; ?>">
                                <?php echo $order_created_at_new_format; ?>
                              </td>
                              <td class="text-center">
                                <a href="<?php getSiteUrl(); ?>idkadmin/archive?page=activate_order&order_id=<?php echo $order_id; ?>">
                                  <i class="fa fa-undo" aria-hidden="true"></i> Vrati u aktivne
                                </a>
                              </td>
                            </tr>

                          <?php } ?>

                        </tbody>
                      </table>
                      <!-- End orders table -->

                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php
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
           * 							LIST ALL ARCHIVED OFFERS
           * *********************************************************/
        case "list_offers":

          if ($getEmployeeStatus == 1) {
          ?>

            <div class="row">
              <div class="col-xs-8">
                <h1><i class="fa fa-file" aria-hidden="true"></i> Arhivirane Ponude</h1>
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
                        echo '<div class="alert material-alert material-alert_success">Uspješno ste aktivirali ponudu.</div>';
                      }
                      ?>

                      <!-- Filling the table with data -->
                      <script type="text/javascript">
                        $(document).ready(function() {
                          $('#idk_table').DataTable({

                            responsive: true,

                            "order": [
                              [0, "desc"]
                            ],

                            "bAutoWidth": false,

                            "aoColumns": [{
                                "width": "10%"
                              },
                              {
                                "width": "20%"
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
                                "width": "10%"
                              }
                            ]
                          });
                        });
                      </script>

                      <!-- Products table -->
                      <table id="idk_table" class="display" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Klijent</th>
                            <th>Ukupna cijena</th>
                            <th>Porez</th>
                            <th>Za platiti</th>
                            <th>Datum</th>
                            <th></th>
                          </tr>
                        </thead>

                        <tbody>

                          <!-- Get data for order -->
                          <?php

                          $query = $db->prepare("
                            SELECT t1.offer_id, t1.client_id, t1.employee_id, t1.offer_total_price, t1.offer_total_tax, t1.offer_to_pay, t1.created_at, t2.client_name
                            FROM idk_offer t1, idk_client t2
                            WHERE t1.offer_status = :offer_status AND t2.client_id = t1.client_id");

                          $query->execute(array(
                            ':offer_status' => 0
                          ));

                          while ($offer = $query->fetch()) {

                            $offer_id = $offer['offer_id'];
                            $client_id = $offer['client_id'];
                            $client_name = $offer['client_name'];
                            $offer_employee_id = $offer['employee_id'];
                            $offer_total_price = $offer['offer_total_price'];
                            $offer_total_tax = $offer['offer_total_tax'];
                            $offer_to_pay = $offer['offer_to_pay'];
                            $offer_created_at = $offer['created_at'];
                            $offer_created_at_new_format = date('d.m.Y.', strtotime($offer['created_at']));

                          ?>

                            <tr>
                              <td class="text-center">
                                <?php echo $offer_id; ?>
                              </td>
                              <td>
                                <a href="<?php getSiteUrl(); ?>idkadmin/offers?page=open&offer_id=<?php echo $offer_id; ?>">
                                  <?php echo $client_name; ?>
                                </a>
                              </td>
                              <td>
                                <?php echo number_format($offer_total_price, 3, ',', '.'); ?> KM
                              </td>
                              <td>
                                <?php echo $offer_total_tax; ?> KM
                              </td>
                              <td>
                                <?php echo $offer_to_pay; ?> KM
                              </td>
                              <td data-sort="<?php echo $offer_created_at; ?>">
                                <?php echo $offer_created_at_new_format; ?>
                              </td>
                              <td class="text-center">
                                <a href="<?php getSiteUrl(); ?>idkadmin/archive?page=activate_offer&offer_id=<?php echo $offer_id; ?>">
                                  <i class="fa fa-undo" aria-hidden="true"></i> Vrati u aktivne
                                </a>
                              </td>
                            </tr>

                          <?php } ?>

                        </tbody>
                      </table>
                      <!-- End offers table -->

                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php
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
           * 							LIST ALL ARCHIVED ROUTES
           * *********************************************************/
        case "list_routes":

          if ($getEmployeeStatus == 1) {
          ?>

            <div class="row">
              <div class="col-xs-8">
                <h1><i class="fa fa-map-marker" aria-hidden="true"></i> Arhivirane Rute</h1>
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
                        echo '<div class="alert material-alert material-alert_success">Uspješno ste aktivirali rutu.</div>';
                      }
                      ?>

                      <!-- Filling the table with data -->
                      <script type="text/javascript">
                        $(document).ready(function() {
                          $('#idk_table').DataTable({

                            responsive: true,

                            "order": [
                              [0, "desc"]
                            ],

                            "bAutoWidth": false,

                            "aoColumns": [{
                                "width": "5%",
                                "bSortable": false
                              },
                              {
                                "width": "20%"
                              },
                              {
                                "width": "20%"
                              },
                              {
                                "width": "25%"
                              },
                              {
                                "width": "20%"
                              },
                              {
                                "width": "10%",
                                "bSortable": false
                              }
                            ]
                          });
                        });
                      </script>

                      <!-- Routes table -->
                      <table id="idk_table" class="display" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Dan</th>
                            <th>Komercijalista</th>
                            <th>Lista klijenata</th>
                            <th>Datum kreiranja</th>
                            <th></th>
                          </tr>
                        </thead>

                        <tbody>

                          <!-- Get data for route -->
                          <?php
                          $query = $db->prepare("
                            SELECT *
                            FROM idk_route
                            WHERE route_active = :route_active");

                          $query->execute(array(
                            ':route_active' => 0
                          ));

                          while ($route = $query->fetch()) {

                            $route_id = $route['route_id'];
                            $route_day = $route['route_day'];
                            $route_employee_id = $route['route_employee_id'];
                            $created_at = $route['created_at'];

                            if (isset($created_at)) {
                              $created_at_new_format = date('d.m.Y.', strtotime($created_at));
                            }

                            if ($route_day == 1) {
                              $route_day = "Ponedjeljak";
                            } elseif ($route_day == 2) {
                              $route_day = "Utorak";
                            } elseif ($route_day == 3) {
                              $route_day = "Srijeda";
                            } elseif ($route_day == 4) {
                              $route_day = "Četvrtak";
                            } elseif ($route_day == 5) {
                              $route_day = "Petak";
                            } elseif ($route_day == 6) {
                              $route_day = "Subota";
                            } elseif ($route_day == 7) {
                              $route_day = "Nedjelja";
                            } else {
                              $route_day = NULL;
                            }

                          ?>

                            <tr>
                              <td>
                                <?php echo $route_id; ?>
                              </td>
                              <td>
                                <a href="<?php getSiteUrl(); ?>idkadmin/routes?page=edit&id=<?php echo $route_id; ?>"><?php echo $route_day; ?></a>
                              </td>
                              <td>
                                <?php
                                $select_query = $db->prepare("
                                  SELECT employee_first_name, employee_last_name
                                  FROM idk_employee
                                  WHERE employee_id = :employee_id");

                                $select_query->execute(array(
                                  ':employee_id' => $route_employee_id
                                ));

                                $num_of_rows = $select_query->rowCount();

                                if ($num_of_rows != 0) {

                                  $select_row = $select_query->fetch();
                                  $employee_first_name = $select_row['employee_first_name'];
                                  $employee_last_name = $select_row['employee_last_name'];

                                  echo '<a href="' . getSiteUrlr() . 'idkadmin/employees?page=open&id=' . $route_employee_id . '">' . $employee_first_name .  ' ' . $employee_last_name . '</a>';
                                }
                                ?>
                              </td>
                              <td>
                                <?php
                                $select_rc_query = $db->prepare("
                                  SELECT t1.client_id, t1.client_name
                                  FROM idk_client t1
                                  INNER JOIN idk_route_client t2
                                  ON t1.client_id = t2.rc_client_id
                                  WHERE t2.rc_route_id = :rc_route_id
                                  ORDER BY t2.rc_client_position");

                                $select_rc_query->execute(array(
                                  ':rc_route_id' => $route_id
                                ));

                                $num_of_rows_rc = $select_rc_query->rowCount();
                                $counter = 0;

                                if ($num_of_rows_rc != 0) {

                                  while ($select_row_rc = $select_rc_query->fetch()) {
                                    $client_id = $select_row_rc['client_id'];
                                    $client_name = $select_row_rc['client_name'];
                                    $counter++;

                                    echo $counter . '. <a href="' . getSiteUrlr() . 'idkadmin/clients?page=open&id=' . $client_id . '">' . $client_name . '</a><br>';
                                  }
                                }
                                ?>
                              </td>
                              <td data-sort="<?php echo $created_at; ?>">
                                <?php echo $created_at_new_format; ?>
                              </td>
                              <td class="text-center">
                                <a href="<?php getSiteUrl(); ?>idkadmin/archive?page=activate_route&route_id=<?php echo $route_id; ?>">
                                  <i class="fa fa-undo" aria-hidden="true"></i> Vrati u aktivne
                                </a>
                              </td>
                            </tr>

                          <?php } ?>

                        </tbody>
                      </table>
                      <!-- End routes table -->

                    </div>
                  </div>
                </div>
              </div>
            </div>
      <?php
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
           * 							ACTIVATE EMPLOYEE
           * *********************************************************/
        case "activate_employee":

          $employee_id = $_GET['employee_id'];

          $select_query = $db->prepare("
            UPDATE idk_employee
            SET employee_active = :employee_active
            WHERE employee_id = :employee_id");

          $select_query->execute(array(
            ':employee_active' => 1,
            ':employee_id' => $employee_id
          ));

          header("Location: archive?page=list_employees&mess=1");

          break;



          /************************************************************
           * 							ACTIVATE CLIENT
           * *********************************************************/
        case "activate_client":

          $client_id = $_GET['client_id'];

          $select_query = $db->prepare("
            UPDATE idk_client
            SET client_active = :client_active
            WHERE client_id = :client_id");

          $select_query->execute(array(
            ':client_active' => 1,
            ':client_id' => $client_id
          ));

          //Save changes to b2b clients stats in db
          $query_client = $db->prepare("
            SELECT created_at
            FROM idk_client
            WHERE client_id = :client_id");

          $query_client->execute(array(
            ':client_id' => $client_id
          ));

          $row_client = $query_client->fetch();
          $created_at = $row_client['created_at'];

          $query_clients_stats = $db->prepare("
            UPDATE idk_stat
            SET stat_b2b_clients = stat_b2b_clients + 1
            WHERE stat_month = :stat_month");

          $query_clients_stats->execute(array(
            ':stat_month' => date('Y-m-01', strtotime($created_at))
          ));

          header("Location: archive?page=list_clients&mess=1");

          break;



          /************************************************************
           * 							ACTIVATE PRODUCT
           * *********************************************************/
        case "activate_product":

          $product_id = $_GET['product_id'];

          $select_query = $db->prepare("
            UPDATE idk_product
            SET product_active = :product_active
            WHERE product_id = :product_id");

          $select_query->execute(array(
            ':product_active' => 1,
            ':product_id' => $product_id
          ));

          header("Location: archive?page=list_products&mess=1");

          break;



          /************************************************************
           * 							ACTIVATE ORDER
           * *********************************************************/
        case "activate_order":

          $order_id = $_GET['order_id'];

          $select_query = $db->prepare("
            UPDATE idk_order
            SET order_status = :order_status
            WHERE order_id = :order_id");

          $select_query->execute(array(
            ':order_status' => 1,
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
            SET stat_b2b_orders = stat_b2b_orders + 1
            WHERE stat_month = :stat_month");

          $query_orders_stats->execute(array(
            ':stat_month' => date('Y-m-01', strtotime($created_at))
          ));

          header("Location: archive?page=list_orders&mess=1");

          break;



          /************************************************************
           * 							ACTIVATE OFFER
           * *********************************************************/
        case "activate_offer":

          $offer_id = $_GET['offer_id'];

          $select_query = $db->prepare("
            UPDATE idk_offer
            SET offer_status = :offer_status
            WHERE offer_id = :offer_id");

          $select_query->execute(array(
            ':offer_status' => 1,
            ':offer_id' => $offer_id
          ));

          header("Location: archive?page=list_offers&mess=1");

          break;



          /************************************************************
           * 							ACTIVATE ROUTE
           * *********************************************************/
        case "activate_route":

          $route_id = $_GET['route_id'];

          $select_query = $db->prepare("
            UPDATE idk_route
            SET route_active = :route_active
            WHERE route_id = :route_id");

          $select_query->execute(array(
            ':route_active' => 1,
            ':route_id' => $route_id
          ));

          header("Location: archive?page=list_routes&mess=1");

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