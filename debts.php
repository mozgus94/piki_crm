<?php
include("includes/functions.php");
include("includes/common.php");

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

                  if ($mess == 1) {
                    echo '<div class="alert material-alert material-alert_success mb-5">Uspješno ste dodali novo zaduženje.</div>';
                  }
                  ?>
                  <h1 class="idk_page_title">Zaduženja</h1>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="row">
          <div class="col-12">

            <!-- Form - Add new debt -->
            <form id="idk_form" action="<?php getSiteUrl(); ?>do.php?form=add_debt" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

              <!-- <div class="form-group">
                <label for="debt_type">Odaberi vrstu informacije <span class="text-danger">*</span></label>
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text"><span class="lnr lnr-list"></span></div>
                  </div>
                  <select class="custom-select bg-white" id="debt_type" name="debt_type" required>
                    <option value="">Odaberi vrstu informacije</option>
                    <?php

                    // $type_query = $db->prepare("
                    //   SELECT dc_type_name
                    //   FROM idk_datacollection_type
                    //   GROUP BY dc_type_name ASC");

                    // $type_query->execute();

                    // while ($type = $type_query->fetch()) {
                    //   echo "<option value='" . $type['dc_type_name'] . "'>" . $type['dc_type_name'] . "</option>";
                    // }
                    ?>
                  </select>
                </div>
              </div> -->

              <div class="form-group">
                <label for="debt_equipment">Zadužio <span class="text-danger">*</span></label>
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text"><span class="lnr lnr-pencil"></span></div>
                  </div>
                  <input type="text" class="form-control" name="debt_equipment" id="debt_equipment" placeholder="Primjer: Frižider" required>
                </div>
              </div>

              <div class="form-group">
                <label for="debt_quantity">Količina <span class="text-danger">*</span></label>
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text"><span class="lnr lnr-pencil"></span></div>
                  </div>
                  <input type="number" class="form-control" name="debt_quantity" id="debt_quantity" value="1" min="0" placeholder="Količina" required>
                </div>
              </div>

              <div class="form-group">
                <label for="debt_desc">Opis</label>
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text"><span class="lnr lnr-pencil"></span></div>
                  </div>
                  <textarea class="form-control" name="debt_desc" id="debt_desc" rows="8" cols="80"></textarea>
                </div>
              </div>

              <!-- Add image -->
              <div class="form-group">
                <label for="debt_image">Fotografija:</label><br>
                <div class="fileinput fileinput-new" data-provides="fileinput">
                  <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
                  </div>
                  <div>
                    <span class="btn btn-default btn-file">
                      <span class="fileinput-new">Dodaj fotografiju</span>
                      <span class="fileinput-exists">Promijeni</span>
                      <input type="file" name="debt_image" id="debt_image">
                    </span>
                    <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
                  </div>
                </div>
              </div>

              <!-- Alerts for image -->
              <div class="form-group">
                <label class="col-sm-3"></label>
                <div class="col-sm-9">
                  <div id="idk_alert_size" class="d-none">
                    <div class="alert material-alert material-alert_danger mb-5">Greška:
                      Fotografija koju pokušavate
                      dodati je veća od dozvoljene veličine.</div>
                  </div>
                  <div id="idk_alert_ext" class="d-none">
                    <div class="alert material-alert material-alert_danger mb-5">Greška: Format
                      fotografije koju
                      pokušavate dodati nije dozvoljen.</div>
                  </div>
                </div>
              </div>

              <button type="submit" class="btn idk_btn btn-block">DODAJ</button>
            </form> <!-- End form - add new debt -->

          </div>
        </div>
      </div>
    </section> <!-- End settings inputs section -->

  </main> <!-- End main -->

  <!-- Foot bar -->
  <?php include('includes/foot_bar.php'); ?>

  <!-- foot.php -->
  <?php include('includes/foot.php'); ?>

</body>

</html>