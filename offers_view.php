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
                  <h1 class="idk_page_title">Ponude</h1>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php if (isset($logged_employee_id) and $logged_employee_id != 0) { ?>
        <!-- List all offers -->
        <div class="container mt-5">
          <div class="row align-items-center justify-content-center text-center">
            <div class="col-12">
              <table id="idk_table_offers" class="display" width="100%">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Klijent</th>
                    <th>Za platiti</th>
                    <th>Datum</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $query = $db->prepare("
                    SELECT t1.offer_id, t1.client_id, t1.offer_key, t1.offer_to_pay, t1.created_at, t2.client_name
                    FROM idk_offer t1, idk_client t2
                    WHERE t1.offer_status != 0 AND t2.client_id = t1.client_id AND t1.employee_id = :employee_id");

                  $query->execute(array(
                    ':employee_id' => $logged_employee_id
                  ));

                  $number_of_rows = $query->rowCount();

                  if ($number_of_rows !== 0) {

                    while ($offer = $query->fetch()) {

                      $offer_id = $offer['offer_id'];
                      $client_name = $offer['client_name'];
                      $offer_to_pay = $offer['offer_to_pay'];
                      $offer_key = $offer['offer_key'];
                      $created_at = $offer['created_at'];
                  ?>
                      <tr>
                        <td>
                          <?php echo '<a href="' . getSiteUrlr() . 'idkadmin/print_offer?id=' . $offer_id . '&offer=' . $offer_key . '" target="_blank">' . $offer_id . '</a>'; ?>
                        </td>
                        <td>
                          <?php if (isset($client_name)) {
                            echo '<a href="' . getSiteUrlr() . 'idkadmin/print_offer?id=' . $offer_id . '&offer=' . $offer_key . '" target="_blank">' . $client_name . '</a>';
                          } ?>
                        </td>
                        <td>
                          <?php if (isset($offer_to_pay)) {
                            echo number_format($offer_to_pay, 2, ',', '.') . ' KM';
                          } ?>
                        </td>
                        <td data-sort="<?php echo $created_at; ?>">
                          <?php if (isset($created_at)) {
                            echo date('d.m.Y.', strtotime($created_at));
                          } ?>
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