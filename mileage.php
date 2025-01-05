<?php
include("includes/functions.php");
include("includes/common_for_messages.php");

$getTempOrder = getTempOrder();
$getUnreadMessages = getUnreadMessages();
$getEmployeeStatus = getEmployeeStatus();

if ($getEmployeeStatus == 1) {
  header('Location: index');
}
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
                <?php
                if (isset($_GET['mess'])) {
                  $mess = $_GET['mess'];
                } else {
                  $mess = 0;
                }

                if (isset($_GET['mileage'])) {
                  $mileage_amount_start_from_db = $_GET['mileage'];
                } else {
                  $mileage_amount_start_from_db = 0;
                }

                if ($mess == 1) {
                  echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success mb-5">Uspješno ste uredili početnu kilometražu.</div></div>';
                } elseif ($mess == 2) {
                  echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_danger mb-5">Greška! Kilometraža za današnji dan nije pronađena u bazi.</div></div>';
                } elseif ($mess == 3) {
                  echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_danger mb-5">Greška! Početna kilometraža ne može biti manja od početne kilometraže za jučerašnji dan.<br>Početna kilometraža za jučer: ' . $mileage_amount_start_from_db . ' km</div></div>';
                } elseif ($mess == 4) {
                  echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_danger mb-5">Greška! Forma nije dobro popunjena.</div></div>';
                }
                ?>
                <div class="col-12">
                  <h1 class="idk_page_title">Kilometraža</h1>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <?php if (isset($logged_employee_id) and $logged_employee_id != 0) { ?>
        <!-- <div class="container">
          <div class="row">
            <div class="col-12">
              <h2>Početna kilometraža se unosi na početku dana</h2>
            </div>
          </div>
        </div> -->

        <div class="container">
          <div class="row">
            <div class="col-12">
              <?php
              $query_last_mileage = $db->prepare("
                SELECT mileage_id, mileage_amount_start
                FROM idk_mileage
                WHERE mileage_employee_id = :mileage_employee_id AND (mileage_start_time BETWEEN :mileage_start_time_start AND :mileage_start_time_end)");

              $query_last_mileage->execute(array(
                ':mileage_employee_id' => $logged_employee_id,
                ':mileage_start_time_start' => date('Y-m-d 00:00:00'),
                ':mileage_start_time_end' => date('Y-m-d 23:59:59')
              ));

              $number_of_rows_last_mileage = $query_last_mileage->rowCount();

              if ($number_of_rows_last_mileage !== 0) {

                $mileage = $query_last_mileage->fetch();

                $mileage_id = $mileage['mileage_id'];
                $mileage_amount_start = $mileage['mileage_amount_start'];
              ?>

                <!-- Form - Edit mileage -->
                <form id="idk_form" action="<?php getSiteUrl(); ?>do.php?form=edit_mileage" method="post" class="form-horizontal mt-4" role="form">
                  <input type="hidden" name="employee_id" value="<?php echo $logged_employee_id; ?>" />
                  <input type="hidden" name="mileage_id" value="<?php echo $mileage_id; ?>" />

                  <div class="form-group">
                    <label for="mileage_amount_start">Početna kilometraža za <?php echo date('d.m.Y.'); ?></label>
                    <div class="row">
                      <div class="col-9">
                        <div class="input-group mb-2 idk_box_shadow_light">
                          <div class="input-group-prepend">
                            <div class="input-group-text"><span class="lnr lnr-car"></span></div>
                          </div>
                          <input type="number" class="form-control" name="mileage_amount_start" id="mileage_amount_start" placeholder="Početna kilometraža" value="<?php echo $mileage_amount_start; ?>" required>
                        </div>
                      </div>
                      <div class="col-3">
                        <button type="submit" class="btn idk_btn btn-block m-0">SNIMI</button>
                      </div>
                    </div>
                  </div>
                </form> <!-- End form - Edit mileage -->

              <?php } else { ?>
                <h2>Kilometraža za današnji dan nije unesena</h2>
              <?php } ?>

            </div>
          </div>
        </div>

        <!-- List all mileages -->
        <div class="container mt-5">

          <div class="row align-items-center justify-content-center text-center">
            <div class="col-12">
              <table id="idk_table_mileage" class="display" width="100%">
                <thead>
                  <tr>
                    <th>Početno vrijeme</th>
                    <th>Početna kilometraža</th>
                    <th>Završno vrijeme</th>
                    <th>Završna kilometraža</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $mileage_id = NULL;

                  $query = $db->prepare("
                    SELECT mileage_id, mileage_employee_id, mileage_start_time, mileage_end_time, mileage_amount_start, mileage_amount_end
                    FROM idk_mileage
                    WHERE mileage_employee_id = :mileage_employee_id
                    ORDER BY mileage_id DESC");

                  $query->execute(array(
                    ':mileage_employee_id' => $logged_employee_id
                  ));

                  $number_of_rows = $query->rowCount();

                  if ($number_of_rows !== 0) {

                    while ($mileage = $query->fetch()) {

                      $mileage_id = $mileage['mileage_id'];
                      $mileage_employee_id = $mileage['mileage_employee_id'];
                      $mileage_amount_start = $mileage['mileage_amount_start'];
                      $mileage_amount_end = $mileage['mileage_amount_end'];
                      $mileage_start_time = $mileage['mileage_start_time'];
                      $mileage_end_time = $mileage['mileage_end_time'];
                  ?>
                      <tr>
                        <td data-sort="<?php echo $mileage_start_time; ?>">
                          <?php if (isset($mileage_start_time)) {
                            echo date('d.m.Y. H:i', strtotime($mileage_start_time));
                          } ?>
                        </td>
                        <td>
                          <?php if (isset($mileage_amount_start)) {
                            echo $mileage_amount_start . ' km';
                          } ?>
                        </td>
                        <td data-sort="<?php echo $mileage_end_time; ?>">
                          <?php if (isset($mileage_end_time)) {
                            echo date('d.m.Y. H:i', strtotime($mileage_end_time));
                          } ?>
                        </td>
                        <td>
                          <?php if (isset($mileage_amount_end)) {
                            echo $mileage_amount_end . ' km';
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