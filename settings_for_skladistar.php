<?php
include("includes/functions.php");
include("includes/common_for_orders.php");

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
  <div id="idk_menu_overlay">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="container idk_page_title_container">
            <div class="row align-items-center">
              <div class="col-12">
                <div class="row align-items-center">
                  <div class="col-8">
                    <h1 class="idk_page_title">Navigacija</h1>
                  </div>
                  <div class="col-4 text-right">
                    <p><a href="#" class="idk_menu_toggler idk_static_background"><span class="lnr lnr-cross"></span></a></p>
                  </div>
                </div>
                <ul>
                  <li><a href="<?php getSiteUrl(); ?>orders"><span class="lnr lnr-list"></span>Nove narudžbe</a></li>
                  <li><a href="<?php getSiteUrl(); ?>orders?page=finished_orders"><span class="lnr lnr-checkmark-circle"></span>Završene narudžbe</a></li>
                  <li>
                    <a href="<?php getSiteUrl(); ?>messages"><span class="lnr lnr-envelope"></span>Poruke
                      <?php if ($getUnreadMessages > 0) { ?>
                        <span class="badge badge-danger">1</span>
                      <?php } ?>
                    </a>
                  </li>
                  <li><a href="<?php getSiteUrl(); ?>settings_for_skladistar"><span class="lnr lnr-cog"></span>Postavke</a></li>
                  <li><a href="<?php getSiteUrl(); ?>do.php?form=logout"><span class="lnr lnr-exit"></span>Odjava</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

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
                    echo '<div class="alert material-alert material-alert_success mb-5">Uspješno ste uredili profil.</div>';
                  }
                  ?>
                  <h1 class="idk_page_title">Postavke</h1>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="row">
          <div class="col-12">

            <?php
            if (isset($logged_employee_id) and $logged_employee_id != 0) {

              $query = $db->prepare("
                SELECT employee_first_name, employee_last_name, employee_login_email, employee_image, employee_jmbg, employee_address, employee_city, employee_country
                FROM idk_employee
                WHERE employee_id = :employee_id");

              $query->execute(array(
                ':employee_id' => $logged_employee_id
              ));

              $employee = $query->fetch();

              $employee_first_name = $employee['employee_first_name'];
              $employee_last_name = $employee['employee_last_name'];
              $employee_jmbg = $employee['employee_jmbg'];
              $employee_login_email = $employee['employee_login_email'];
              $employee_address = $employee['employee_address'];
              $employee_city = $employee['employee_city'];
              $employee_country = $employee['employee_country'];
              $employee_image = $employee['employee_image'];

              //Get primary phone from idk_employee_info
              $query_phone = $db->prepare("
                SELECT ei_data
                FROM idk_employee_info
                WHERE ei_group = :ei_group AND ei_primary = :ei_primary AND employee_id = :employee_id");

              $query_phone->execute(array(
                ':ei_group' => 1,
                ':ei_primary' => 1,
                ':employee_id' => $logged_employee_id
              ));

              $employee_phone = $query_phone->fetch();

              if ($employee_phone) {
                $employee_phone = $employee_phone['ei_data'];
              }

            ?>

              <!-- Form - Edit employee profile -->
              <form id="idk_form" action="<?php getSiteUrl(); ?>do.php?form=edit_employee_profile" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

                <input type="hidden" name="employee_id" value="<?php echo $logged_employee_id; ?>" />

                <!-- Add image -->
                <div class="form-group">
                  <label for="employee_image" class="sr-only">Fotografija:</label>
                  <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
                      <img src="<?php getSiteUrl(); ?>idkadmin/files/employees/images/<?php echo $employee_image; ?>">
                    </div>
                    <input type="hidden" name="employee_image_url" value="<?php echo $employee_image; ?>" />
                    <div>
                      <span class="btn btn-default btn-file">
                        <span class="fileinput-new">Promijeni fotografiju</span>
                        <span class="fileinput-exists">Promijeni</span>
                        <input type="file" name="employee_image" id="employee_image">
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

                <div class="form-group">
                  <label for="employee_first_name">Ime<span class="text-danger">*</span></label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-user"></span></div>
                    </div>
                    <input type="text" class="form-control" name="employee_first_name" id="employee_first_name" placeholder="Ime" value="<?php echo $employee_first_name; ?>" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="employee_last_name">Prezime<span class="text-danger">*</span></label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-user"></span></div>
                    </div>
                    <input type="text" class="form-control" name="employee_last_name" id="employee_last_name" placeholder="Prezime" value="<?php echo $employee_last_name; ?>" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="employee_login_email">Login email<span class="text-danger">*</span></label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-envelope"></span></div>
                    </div>
                    <input type="email" class="form-control" name="employee_login_email" id="employee_login_email" placeholder="Login email" value="<?php echo $employee_login_email; ?>" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="employee_password">Lozinka</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-lock"></span></div>
                    </div>
                    <input type="password" class="form-control" name="employee_password" id="employee_password" placeholder="Lozinka">
                  </div>
                  <p><em><small>Ukoliko želite promijeniti lozinku, unesite novu.</small></em></p>
                </div>

                <div class="form-group">
                  <label for="employee_jmbg">JMBG</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-license"></span></div>
                    </div>
                    <input type="number" class="form-control" name="employee_jmbg" id="employee_jmbg" placeholder="JMBG" value="<?php echo $employee_jmbg; ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label for="employee_phone">Primarni telefon</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-phone-handset"></span></div>
                    </div>
                    <input type="text" class="form-control" name="employee_phone" id="employee_phone" placeholder="Primarni telefon" value="<?php echo $employee_phone; ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label for="employee_address">Adresa</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-home"></span></div>
                    </div>
                    <input type="text" class="form-control" name="employee_address" id="employee_address" placeholder="Adresa" value="<?php echo $employee_address; ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label for="employee_city">Općina</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-home"></span></div>
                    </div>
                    <select class="custom-select bg-white" id="employee_city" name="employee_city">
                      <option value="">Odaberi općinu</option>
                      <?php
                      $select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
                                WHERE location_type = :location_type
                                GROUP BY location_name");

                      $select_query->execute(array(
                        ':location_type' => 1
                      ));

                      while ($select_row = $select_query->fetch()) {
                        echo "<option value='" . $select_row['location_name'] . "'";
                        if ($select_row['location_name'] == $employee_city) {
                          echo " selected";
                        }
                        echo ">" . $select_row['location_name'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="employee_country">Država</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-home"></span></div>
                    </div>
                    <select class="custom-select bg-white" id="employee_country" name="employee_country">
                      <option value="">Odaberi državu</option>
                      <?php
                      $select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
                                WHERE location_type = :location_type
                                GROUP BY location_name");

                      $select_query->execute(array(
                        ':location_type' => 3
                      ));

                      while ($select_row = $select_query->fetch()) {
                        echo "<option value='" . $select_row['location_name'] . "'";
                        if ($select_row['location_name'] == $employee_country) {
                          echo " selected";
                        }
                        echo ">" . $select_row['location_name'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <button type="submit" class="btn idk_btn btn-block">SNIMI</button>
              </form> <!-- End form - edit employee profile -->

            <?php } elseif (isset($logged_client_id) and $logged_client_id != 0) {

              $query = $db->prepare("
                SELECT client_name, client_username, client_id_number, client_business_type, client_pdv_number, client_postal_code, client_image, client_address, client_city, client_country, client_region, client_type
                FROM idk_client
                WHERE client_id = :client_id");

              $query->execute(array(
                ':client_id' => $logged_client_id
              ));

              $client = $query->fetch();

              $client_name = $client['client_name'];
              $client_username = $client['client_username'];
              $client_id_number = $client['client_id_number'];
              $client_business_type = $client['client_business_type'];
              $client_pdv_number = $client['client_pdv_number'];
              $client_postal_code = $client['client_postal_code'];
              $client_image = $client['client_image'];
              $client_address = $client['client_address'];
              $client_city = $client['client_city'];
              $client_country = $client['client_country'];
              $client_region = $client['client_region'];
              $client_type = $client['client_type'];
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
                ':client_id' => $logged_client_id
              ));

              $client_info = $query_info->fetch();
              $client_phone = $client_info['ci_data'];

              //Get email
              $query_info->execute(array(
                ':ci_group' => 2,
                ':ci_primary' => 1,
                ':client_id' => $logged_client_id
              ));

              $client_info = $query_info->fetch();
              $client_email = $client_info['ci_data'];

            ?>

              <!-- Form - Edit client profile -->
              <form id="idk_form" action="<?php getSiteUrl(); ?>do.php?form=edit_client_profile" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

                <input type="hidden" name="client_id" value="<?php echo $logged_client_id; ?>" />

                <!-- Add image -->
                <div class="form-group">
                  <label for="client_image" class="sr-only">Fotografija:</label>
                  <div class="fileinput fileinput-new" data-provides="fileinput">
                    <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
                      <img src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>">
                    </div>
                    <input type="hidden" name="client_image_url" value="<?php echo $client_image; ?>" />
                    <div>
                      <span class="btn btn-default btn-file">
                        <span class="fileinput-new">Promijeni fotografiju</span>
                        <span class="fileinput-exists">Promijeni</span>
                        <input type="file" name="client_image" id="client_image">
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

                <div class="form-group">
                  <label for="client_name">Naziv<span class="text-danger">*</span></label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-user"></span></div>
                    </div>
                    <input type="text" class="form-control" name="client_name" id="client_name" placeholder="Naziv" value="<?php echo $client_name; ?>" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="client_username">Korisničko ime<span class="text-danger">*</span></label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-envelope"></span></div>
                    </div>
                    <input type="text" class="form-control" name="client_username" id="client_username" placeholder="Korisničko ime" value="<?php echo $client_username; ?>" required>
                  </div>
                </div>

                <div class="form-group">
                  <label for="client_password">Lozinka</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-lock"></span></div>
                    </div>
                    <input type="password" class="form-control" name="client_password" id="client_password" placeholder="Lozinka">
                  </div>
                  <p><em><small>Ukoliko želite promijeniti lozinku, unesite novu.</small></em></p>
                </div>

                <div class="form-group">
                  <label for="client_id_number">ID broj<span class="text-danger">*</span></label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-license"></span></div>
                    </div>
                    <input type="text" class="form-control" name="client_id_number" id="client_id_number" placeholder="ID broj" value="<?php echo $client_id_number; ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label for="client_pdv_number">PDV broj</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-license"></span></div>
                    </div>
                    <input type="text" class="form-control" name="client_pdv_number" id="client_pdv_number" placeholder="PDV broj" value="<?php echo $client_pdv_number; ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label for="client_phone">Primarni telefon</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-phone-handset"></span></div>
                    </div>
                    <input type="text" class="form-control" name="client_phone" id="client_phone" placeholder="Primarni telefon" value="<?php echo $client_phone; ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label for="client_email">Primarni email</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-envelope"></span></div>
                    </div>
                    <input type="text" class="form-control" name="client_email" id="client_email" placeholder="Primarni email" value="<?php echo $client_email; ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label for="client_address">Adresa</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-apartment"></span></div>
                    </div>
                    <input type="text" class="form-control" name="client_address" id="client_address" placeholder="Adresa" value="<?php echo $client_address; ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label for="client_postal_code">Poštanski broj</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-apartment"></span></div>
                    </div>
                    <input type="text" class="form-control" name="client_postal_code" id="client_postal_code" placeholder="Poštanski broj" value="<?php echo $client_postal_code; ?>">
                  </div>
                </div>

                <div class="form-group">
                  <label for="client_city">Općina</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-apartment"></span></div>
                    </div>
                    <select class="custom-select bg-white" id="client_city" name="client_city">
                      <option value="">Odaberi općinu</option>
                      <?php
                      $select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
                                WHERE location_type = :location_type
                                GROUP BY location_name");

                      $select_query->execute(array(
                        ':location_type' => 1
                      ));

                      while ($select_row = $select_query->fetch()) {
                        echo "<option value='" . $select_row['location_name'] . "'";
                        if ($select_row['location_name'] == $client_city) {
                          echo " selected";
                        }
                        echo ">" . $select_row['location_name'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="client_region">Regija</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-apartment"></span></div>
                    </div>
                    <select class="custom-select bg-white" id="client_region" name="client_region">
                      <option value="">Odaberi regiju</option>
                      <?php
                      $select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
                                WHERE location_type = :location_type
                                GROUP BY location_name");

                      $select_query->execute(array(
                        ':location_type' => 2
                      ));

                      while ($select_row = $select_query->fetch()) {
                        echo "<option value='" . $select_row['location_name'] . "'";
                        if ($select_row['location_name'] == $client_region) {
                          echo " selected";
                        }
                        echo ">" . $select_row['location_name'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="form-group">
                  <label for="client_country">Država</label>
                  <div class="input-group mb-2">
                    <div class="input-group-prepend">
                      <div class="input-group-text"><span class="lnr lnr-apartment"></span></div>
                    </div>
                    <select class="custom-select bg-white" id="client_country" name="client_country">
                      <option value="">Odaberi državu</option>
                      <?php
                      $select_query = $db->prepare("
																SELECT location_name
																FROM idk_location
                                WHERE location_type = :location_type
                                GROUP BY location_name");

                      $select_query->execute(array(
                        ':location_type' => 3
                      ));

                      while ($select_row = $select_query->fetch()) {
                        echo "<option value='" . $select_row['location_name'] . "'";
                        if ($select_row['location_name'] == $client_country) {
                          echo " selected";
                        }
                        echo ">" . $select_row['location_name'] . "</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <button type="submit" class="btn idk_btn btn-block">SNIMI</button>
              </form> <!-- End form - edit client profile -->
            <?php } ?>

          </div>
        </div>
      </div>
    </section> <!-- End settings inputs section -->

  </main> <!-- End main -->

  <!-- foot.php -->
  <?php include('includes/foot.php'); ?>

</body>

</html>