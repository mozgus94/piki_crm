<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
  $page = $_REQUEST["page"];
} else {
  header("Location: assortment?page=open_report");
}

?>

<!DOCTYPE html>
<html>

<head>

  <?php include('includes/head.php'); ?>

</head>

<body>
  <header class="idk_display_none_for_print">
    <?php include('header.php'); ?>
  </header>
  <div id="sidebar" class="idk_display_none_for_print">
    <?php include('menu.php'); ?>
  </div>
  <div id="content">
    <div class="container-fluid">
      <?php
      switch ($page) {



          /************************************************************
           * 							OPEN REPORT
           * *********************************************************/
        case "open_report":

          $ar_id = $_GET['id'];

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

          //Check if assortment report exists
          $check_query = $db->prepare("
            SELECT ar_client_id, ar_employee_id, ar_datetime
            FROM idk_assortment_report
            WHERE ar_id = :ar_id");

          $check_query->execute(array(
            ':ar_id' => $ar_id
          ));

          $number_of_rows = $check_query->rowCount();

          if ($number_of_rows != 0) {

            $check_row = $check_query->fetch();
            $ar_client_id = $check_row['ar_client_id'];
            $ar_employee_id = $check_row['ar_employee_id'];
            $ar_datetime = $check_row['ar_datetime'];

            //Get client name and business type
            $assortment_client_query = $db->prepare("
              SELECT client_name, client_business_type
              FROM idk_client
              WHERE client_id = :client_id");

            $assortment_client_query->execute(array(
              ':client_id' => $ar_client_id
            ));

            $assortment_client_row = $assortment_client_query->fetch();
            $client_name = $assortment_client_row['client_name'];
            $client_business_type = $assortment_client_row['client_business_type'];

            $select_client_business_type_query = $db->prepare("
              SELECT od_data
              FROM idk_client_otherdata
              WHERE od_group = :od_group AND od_value = :od_value");

            $select_client_business_type_query->execute(array(
              ':od_group' => 1,
              ':od_value' => $client_business_type
            ));

            $select_client_business_type_row = $select_client_business_type_query->fetch();
            $client_business_type_echo = $select_client_business_type_row['od_data'];

            //Get employee name
            $assortment_employee_query = $db->prepare("
              SELECT employee_first_name, employee_last_name
              FROM idk_employee
              WHERE employee_id = :employee_id");

            $assortment_employee_query->execute(array(
              ':employee_id' => $ar_employee_id
            ));

            $assortment_employee_row = $assortment_employee_query->fetch();
            $employee_first_name = $assortment_employee_row['employee_first_name'];
            $employee_last_name = $assortment_employee_row['employee_last_name'];

      ?>

            <div class="row idk_display_none_for_print">
              <div class="col-xs-8">
                <h1><i class="fa fa-bar-chart idk_color_green" aria-hidden="true"></i> Stanje asortimana za klijenta: <?php echo $client_name; ?><small> - <?php echo date('d.m.Y.', strtotime($ar_datetime)); ?></small></h1>
              </div>
              <div class="col-xs-4 text-right idk_margin_top10">
                <a href="<?php getSiteUrl(); ?>idkadmin/reports?page=assortment" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
              </div>
              <div class="col-xs-12">
                <hr>
              </div>
            </div>

            <div class="row idk_display_none_for_print">
              <div class="col-md-12">
                <div class="content_box">

                  <div id="myTabs" class="panel-group material-tabs-group">
                    <ul class="nav nav-tabs material-tabs material-tabs_primary">
                      <li class="active">
                        <a href="#info" class="material-tabs__tab-link" data-toggle="tab">Informacije</a>
                      </li>
                    </ul>
                    <div class="tab-content materail-tabs-content">
                      <div class="tab-pane fade active in" id="info">
                        <div class="row idk_product_info">

                          <div class="col-md-6">
                            <?php
                            //Get products status for assortment report
                            $assortment_product_query = $db->prepare("
                              SELECT ap_product_id, ap_status
                              FROM idk_assortment_product
                              WHERE ap_assortment_id = :ap_assortment_id");

                            $assortment_product_query->execute(array(
                              ':ap_assortment_id' => $ar_id
                            ));

                            $products_in_stock = array();
                            $products_not_in_stock = array();

                            while ($assortment_product = $assortment_product_query->fetch()) {
                              $product_id = $assortment_product['ap_product_id'];
                              $product_status = $assortment_product['ap_status'];

                              if ($product_status == 1) {
                                array_push($products_in_stock, $product_id);
                              } elseif ($product_status == 2) {
                                array_push($products_not_in_stock, $product_id);
                              }
                            }
                            ?>

                            <!-- Proizvodi na stanju -->
                            <div class="row">
                              <div class="col-sm-9">
                                <h5>Proizvodi na stanju</h5>
                              </div>
                              <div class="col-sm-3 text-right">
                                <a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" id="idk_print_report_btn">
                                  <i class="fa fa-print" aria-hidden="true"></i> <span>Print</span>
                                </a>
                              </div>
                            </div>

                            <?php foreach ($products_in_stock as $product_in_stock_id) {
                              //Get product info
                              $product_query = $db->prepare("
                                SELECT product_name, product_image
                                FROM idk_product
                                WHERE product_id = :product_id");

                              $product_query->execute(array(
                                ':product_id' => $product_in_stock_id
                              ));

                              $product_row = $product_query->fetch();
                              $product_name = $product_row['product_name'];
                              $product_image = $product_row['product_image'];
                            ?>
                              <div class="row">
                                <div class="col-sm-1 text-right">
                                  <img src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>" class="idk_profile_img" alt="<?php echo $product_name; ?> slika">
                                </div>
                                <div class="col-sm-11 idk_color_dark_green idk_padding_top10">
                                  <?php echo $product_name; ?>
                                </div>
                              </div>
                            <?php } ?>
                            <br>
                            <br>

                            <!-- Proizvodi kojih nema na stanju -->
                            <div class="row">
                              <div class="col-sm-12">
                                <h5 class="idk_border_red">Proizvodi kojih nema na stanju</h5>
                              </div>
                            </div>

                            <?php foreach ($products_not_in_stock as $product_not_in_stock_id) {
                              //Get product info
                              $product_query = $db->prepare("
                                SELECT product_name, product_image
                                FROM idk_product
                                WHERE product_id = :product_id");

                              $product_query->execute(array(
                                ':product_id' => $product_not_in_stock_id
                              ));

                              $product_row = $product_query->fetch();
                              $product_name = $product_row['product_name'];
                              $product_image = $product_row['product_image'];
                            ?>
                              <div class="row">
                                <div class="col-sm-1 text-right">
                                  <img src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>" class="idk_profile_img" alt="<?php echo $product_name; ?> slika">
                                </div>
                                <div class="col-sm-11 idk_color_red idk_padding_top10">
                                  <?php echo $product_name; ?>
                                </div>
                              </div>
                            <?php } ?>
                            <br>
                            <br>

                            <!-- Ostali proizvodi -->
                            <?php
                            //Get product info
                            $product_query = $db->prepare("
                              SELECT product_id, product_name, product_image
                              FROM idk_product
                              WHERE product_active = :product_active");

                            $product_query->execute(array(
                              ':product_active' => 1
                            ));

                            $number_of_rows = $product_query->rowCount();

                            if ($number_of_rows > (count($products_in_stock) + count($products_not_in_stock))) {
                            ?>

                              <div class="row">
                                <div class="col-sm-12">
                                  <h5 class="idk_border_gray">Ostali proizvodi</h5>
                                </div>
                              </div>

                              <?php
                              while ($product_row = $product_query->fetch()) {
                                $product_id = $product_row['product_id'];
                                $product_name = $product_row['product_name'];
                                $product_image = $product_row['product_image'];
                                if (!in_array($product_id, $products_in_stock) and !in_array($product_id, $products_not_in_stock)) {
                              ?>
                                  <div class="row">
                                    <div class="col-sm-1 text-right">
                                      <img src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>" class="idk_profile_img" alt="<?php echo $product_name; ?> slika">
                                    </div>
                                    <div class="col-sm-11 idk_padding_top10">
                                      <?php echo $product_name; ?>
                                    </div>
                                  </div>
                              <?php }
                              } ?>
                              <br>
                              <br>
                            <?php } ?>
                          </div>

                          <div class="col-md-6">
                            <div class="row">
                              <div class="col-sm-12">
                                <h5>Informacije o klijentu i komercijalisti</h5>
                              </div>
                            </div>
                            <!-- Get client and employee info -->
                            <div class="row">
                              <strong class="col-sm-4 text-right">Klijent:</strong>
                              <!-- <div class="col-sm-8"><?php //echo $client_name . " " . $client_business_type_echo; ?></div> -->
                              <div class="col-sm-8"><?php echo $client_name; ?></div>
                            </div>
                            <br>
                            <div class="row">
                              <strong class="col-sm-4 text-right">Komercijalista:</strong>
                              <div class="col-sm-8"><?php echo $employee_first_name . " " . $employee_last_name; ?></div>
                            </div>
                            <br>
                            <div class="row">
                              <strong class="col-sm-4 text-right">Datum:</strong>
                              <div class="col-sm-8"><?php echo date('d.m.Y.', strtotime($ar_datetime)); ?></div>
                            </div>
                          </div>

                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <?php
            $owner_query = $db->prepare("
              SELECT owner_name, owner_image
              FROM idk_owner");

            $owner_query->execute();

            $owner = $owner_query->fetch();

            $owner_name = $owner['owner_name'];
            $owner_image = $owner['owner_image'];
            ?>

            <!-- Print report wrapper -->
            <div id="idk_print_report_wrapper" style="display: none;">
              <div id="print_header">
                <div class="container-fluid">
                  <div class="row">
                    <div class="col-xs-6">
                      <h3>Stanje asortimana</h3>
                      <p class="idk_margin_top30">
                        <strong>Klijent</strong> <br>
                        <!-- <?php //echo $client_name . " " . $client_business_type_echo; ?> <br> -->
                        <?php echo $client_name; ?> <br>
                        <strong>Komercijalista</strong> <br>
                        <?php echo $employee_first_name . " " . $employee_last_name; ?> <br>
                        <strong>Datum</strong> <br>
                        <?php echo date('d.m.Y.', strtotime($ar_datetime)); ?> <br>
                      </p>
                    </div>
                    <div class="col-xs-6 text-right">
                      <img src="<?php getSiteUrl(); ?>idkadmin/files/owners/images/<?php echo $owner_image; ?>" class="idk_print_logo" alt="<?php echo $owner_name; ?> logo">
                      <p class="idk_margin_top30">
                        <strong>Unaviva d.o.o.</strong> <br>
                        Dr. Irfana Ljubijankića 87 <br>
                        77000 Bihać <br>
                        Tel: 00 387 37 961 131 <br>
                        E-Mail: info@unaviva.ba <br>
                        Web: www.unaviva.ba <br>
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div id="print_main">
                <div class="container-fluid">
                  <div class="row idk_margin_top50">
                    <div class="col-xs-6">
                      <h4>Proizvodi na stanju</h4>

                      <?php foreach ($products_in_stock as $product_in_stock_id) {
                        //Get product info
                        $product_query = $db->prepare("
                          SELECT product_name
                          FROM idk_product
                          WHERE product_id = :product_id");

                        $product_query->execute(array(
                          ':product_id' => $product_in_stock_id
                        ));

                        $product_row = $product_query->fetch();
                        $product_name = $product_row['product_name'];
                      ?>
                        <div class="row">
                          <div class="col-sm-12 idk_color_dark_green">
                            <?php echo $product_name; ?>
                          </div>
                        </div>
                      <?php } ?>
                    </div>

                    <div class="col-xs-6">
                      <h4>Proizvodi kojih nema na stanju</h4>

                      <?php foreach ($products_not_in_stock as $product_not_in_stock_id) {
                        //Get product info
                        $product_query = $db->prepare("
                          SELECT product_name
                          FROM idk_product
                          WHERE product_id = :product_id");

                        $product_query->execute(array(
                          ':product_id' => $product_not_in_stock_id
                        ));

                        $product_row = $product_query->fetch();
                        $product_name = $product_row['product_name'];
                      ?>
                        <div class="row">
                          <div class="col-sm-12 idk_color_red">
                            <?php echo $product_name; ?>
                          </div>
                        </div>
                      <?php } ?>
                    </div>
                  </div>

                  <?php
                  //Get product info
                  $product_query = $db->prepare("
                    SELECT product_id, product_name
                    FROM idk_product
                    WHERE product_active = :product_active");

                  $product_query->execute(array(
                    ':product_active' => 1
                  ));

                  $number_of_rows = $product_query->rowCount();

                  if ($number_of_rows > (count($products_in_stock) + count($products_not_in_stock))) {
                  ?>

                    <div class="row idk_margin_top50">
                      <div class="col-xs-12">
                        <h4>Ostali proizvodi</h4>

                        <?php
                        while ($product_row = $product_query->fetch()) {
                          $product_id = $product_row['product_id'];
                          $product_name = $product_row['product_name'];
                          if (!in_array($product_id, $products_in_stock) and !in_array($product_id, $products_not_in_stock)) {
                        ?>
                            <div class="row">
                              <div class="col-sm-12">
                                <?php echo $product_name; ?>
                              </div>
                            </div>
                        <?php }
                        } ?>
                      </div>
                    </div>
                  <?php } ?>

                </div>
              </div>
            </div>

            <script type="text/javascript">
              $(document).ready(function() {
                $('#idk_print_report_btn').click(function() {
                  $('.idk_display_none_for_print').css('display', 'none');
                  $('#content').css('background-color', '#fff');
                  $('#content').css('margin', '0');
                  $('#content').css('padding', '0');
                  $('#idk_print_report_wrapper').css('display', 'block');
                  window.print();
                  $('#idk_print_report_wrapper').css('display', 'none');
                  $('#content').css('background-color', '#eee');
                  $('#content').css('margin-left', '220px');
                  $('#content').css('padding-top', '65px');
                  $('.idk_display_none_for_print').css('display', 'block');
                });
              });
            </script>
      <?php
          } else {
            header("Location: reports?page=assortment&mess=2");
          }
          break;
      }
      ?>



      <!--/************************************************************
 * 							FOOTER
 * *********************************************************/-->
      <footer class="idk_display_none_for_print"><?php getCopyright(); ?></footer>
    </div>
  </div>
</body>

</html>