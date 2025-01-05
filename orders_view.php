<?php
include("includes/functions.php");
include("includes/common_for_messages.php");

$getTempOrder = getTempOrder();
$getUnreadMessages = getUnreadMessages();

?>

<!DOCTYPE html>
<html lang="bs">

<head>

  <?php include('includes/head.php'); ?>

</head>

<body class="idk_body_background">

  <!-- Overlay menu -->
  <?php include('includes/menu_overlay.php'); ?>

  <!-- Header -->
  <header class="header">

    <!-- Top bar -->
    <?php include('includes/top_bar.php'); ?>

  </header> <!-- End header -->

  <!-- Main -->
  <main>

    <!-- Settings inputs section -->
    <section class="idk_settings_section">

      <div class="container-fluid">
        <div class="row">
          <div class="col-12">
            <div class="container idk_page_title_container">
              <div class="row align-items-center">
                <div class="col-12">
                  <?php
                  if (isset($_GET['mess'])) {
                    $mess = $_GET['mess'];
                  } else {
                    $mess = 0;
                  }
                  ?>
                  <h1 class="idk_page_title">Narudžbe</h1>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php if (isset($logged_employee_id) and $logged_employee_id != 0) { ?>
        <!-- List all orders -->
        <div class="container mt-5">
          <div class="row align-items-center justify-content-center text-center">
            <div class="col-12">
              <table id="idk_table_orders" class="display" width="100%">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Klijent</th>
                    <th>Za platiti</th>
                    <th>Datum</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $query = $db->prepare("
                    SELECT t1.order_id, t1.client_id, t1.employee_id, t1.order_status, t1.order_total_price, t1.order_total_rabat, t1.order_total_tax, t1.order_to_pay, t1.order_key, t1.created_at, t2.client_name, t3.od_value, t3.od_data
                    FROM idk_order t1, idk_client t2, idk_order_otherdata t3
                    WHERE t1.order_status != 0 AND t2.client_id = t1.client_id AND t1.order_status = t3.od_id AND t1.employee_id = :employee_id");

                  $query->execute(array(
                    ':employee_id' => $logged_employee_id
                  ));

                  $number_of_rows = $query->rowCount();

                  if ($number_of_rows !== 0) {

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
                      $order_key = $order['order_key'];
                      $order_created_at = $order['created_at'];
                      $order_color = $order['od_value'];
                      $od_data = $order['od_data'];
                  ?>
                      <tr>
                        <td>
                          <?php echo $order_id; ?>
                        </td>
                        <td>
                          <?php if (isset($client_name)) {
                            echo '<a href="' . getSiteUrlr() . 'idkadmin/print_order?id=' . $order_id . '&order=' . $order_key . '" target="_blank">' . $client_name . '</a>';
                          } ?>
                        </td>
                        <td>
                          <?php if (isset($order_to_pay)) {
                            echo number_format($order_to_pay, 2, ',', '.') . ' KM';
                          } ?>
                        </td>
                        <td data-sort="<?php echo $order_created_at; ?>">
                          <?php if (isset($order_created_at)) {
                            echo date('d.m.Y.', strtotime($order_created_at));
                          } ?>
                        </td>
                        <td>
                          <button class="btn material-btn" style="width: 100%; background: <?php echo $order_color; ?>; cursor: auto; color: #fff; text-shadow: 2px 2px rgba(0, 0, 0, 0.15);" data-toggle="tooltip" data-placement="top" title="<?php echo $od_data; ?>"><?php echo $od_data; ?></button>
                        </td>
                      </tr>
                    <?php } ?>
                  <?php } ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      <?php } ?>
    </section> <!-- End settings inputs section -->

  </main> <!-- End main -->

  <!-- Foot bar -->
  <?php include('includes/foot_bar.php'); ?>

  <!-- foot.php -->
  <?php include('includes/foot.php'); ?>

</body>

</html>