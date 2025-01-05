<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
  $page = $_REQUEST["page"];
} else {
  header("Location: routes?page=list");
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
         * 							LIST ALL ROUTES
         * *********************************************************/
        case "list":
      ?>

          <div class="row">
            <div class="col-xs-8">
              <h1><i class="fa fa-map-marker idk_color_green" aria-hidden="true"></i> Rute</h1>
            </div>
            <div class="col-xs-4 text-right idk_margin_top10">
              <a href="<?php getSiteUrl(); ?>idkadmin/routes?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
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
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu rutu.</div>';
                    } elseif ($mess == 2) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Ruta već postoji u bazi podataka.</div>';
                    } elseif ($mess == 3) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili rutu.</div>';
                    } elseif ($mess == 4) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste arhivirali rutu.</div>';
                    } elseif ($mess == 5) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Ruta koji pokušavate urediti ne postoji u bazi podataka ili je arhivirana.</div>';
                    } elseif ($mess == 6) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Ruta ne postoji u bazi podataka ili je arhivirana.</div>';
                    } elseif ($mess == 7) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
                    } elseif ($mess == 8) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Ruta već postoji u bazi podataka, ali je arhivirana. Aktivirajte rutu iz arhive.</div>';
                    } elseif ($mess == 9) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Ruta za taj dan i komercijalistu već postoji za sedmicu u mjesecu = 0. Ako želite dodati još jednu rutu za taj dan i komercijalistu, promijenite sedmicu postojeće rute.</div>';
                    }
                    ?>

                    <!-- Filling the table with data -->
                    <script type="text/javascript">
                      $(document).ready(function() {
                        $('#idk_table').DataTable({

                          "responsive": true,

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
                              "width": "25%"
                            },
                            {
                              "width": "20%"
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
                          <th>Sedmica u mjesecu</th>
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
                          ':route_active' => 1
                        ));

                        while ($route = $query->fetch()) {

                          $route_id = $route['route_id'];
                          $route_day = $route['route_day'];
                          $route_week = $route['route_week'];
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

                          if ($route_week == 0) {
                            $route_week = "0 - ruta se ponavlja svake sedmice";
                          } elseif ($route_week == 1) {
                            $route_week = "1. sedmica";
                          } elseif ($route_week == 2) {
                            $route_week = "2. sedmica";
                          } else {
                            $route_week = NULL;
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
                              <?php echo $route_week; ?>
                            </td>
                            <td data-sort="<?php echo $created_at; ?>">
                              <?php echo $created_at_new_format; ?>
                            </td>
                            <td class="text-center">
                              <div class="btn-group material-btn-group">
                                <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown">
                                  <i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span>
                                </button>
                                <ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
                                  <!-- <li>
                                <a href="<?php //getSiteUrl(); 
                                          ?>idkadmin/routes?page=open&id=<?php //echo $route_id; 
                                                                          ?>" class="material-dropdown-menu__link">
                                  <i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori
                                </a>
                              </li> -->
                                  <li>
                                    <a href="<?php getSiteUrl(); ?>idkadmin/routes?page=edit&id=<?php echo $route_id; ?>" class="material-dropdown-menu__link">
                                      <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Uredi
                                    </a>
                                  </li>
                                  <li class="idk_dropdown_danger">
                                    <a href="#" data="<?php getSiteUrl(); ?>idkadmin/routes?page=archive&id=<?php echo $route_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link">
                                      <i class="fa fa-trash-o" aria-hidden="true"></i> Arhiviraj
                                    </a>
                                  </li>
                                </ul>
                              </div>
                            </td>
                          </tr>

                        <?php } ?>

                        <!-- Archiving -->
                        <script>
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
                                <p>Jeste li sigurni da želite arhivirati rutu?</p>
                              </div>
                              <div class="modal-footer material-modal__footer">
                                <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                <a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">ARHIVIRAJ</button></a>
                              </div>
                            </div>
                          </div>
                        </div>
                      </tbody>
                    </table>
                    <!-- End routes table -->

                  </div>
                </div>
              </div>
            </div>
          </div>

          <?php

          break;



          /************************************************************
           * 							ADD NEW ROUTE
           * *********************************************************/
        case "add":

          if ($getEmployeeStatus == 1) {
          ?>

            <div class="row">
              <div class="col-xs-8">
                <h1><i class="fa fa-map-marker idk_color_green" aria-hidden="true"></i> Dodaj novu rutu</h1>
              </div>
              <div class="col-xs-4 text-right idk_margin_top10">
                <a href="<?php getSiteUrl(); ?>idkadmin/routes?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
              </div>
              <div class="col-xs-12">
                <hr>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="content_box">
                  <div class="row">
                    <div class="col-md-offset-1 col-md-8">

                      <!-- Form - add route -->
                      <form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_route" method="post" class="form-horizontal" role="form">

                        <div class="form-group">
                          <label for="route_day" class="col-sm-3 control-label"><span class="text-danger">*</span> Dan:</label>
                          <div class="col-sm-9">
                            <select class="selectpicker" id="route_day" name="route_day" data-live-search="true" required>
                              <option value=""></option>
                              <option value="1" data-tokens="Ponedjeljak">Ponedjeljak</option>
                              <option value="2" data-tokens="Utorak">Utorak</option>
                              <option value="3" data-tokens="Srijeda">Srijeda</option>
                              <option value="4" data-tokens="Četvrtak">Četvrtak</option>
                              <option value="5" data-tokens="Petak">Petak</option>
                              <option value="6" data-tokens="Subota">Subota</option>
                              <option value="7" data-tokens="Nedjelja">Nedjelja</option>
                            </select>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="route_week" class="col-sm-3 control-label">Sedmica u mjesecu:</label>
                          <div class="col-sm-9">
                            <select class="selectpicker" id="route_week" name="route_week" data-live-search="true">
                              <option value=""></option>
                              <option value="1" data-tokens="1">1.</option>
                              <option value="2" data-tokens="2">2.</option>
                            </select>
                            <small>Ako komercijalista ima samo jednu rutu za odabrani dan, ostavite prazno</small>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="route_employee_id" class="col-sm-3 control-label"><span class="text-danger">*</span> Komercijalista:</label>
                          <div class="col-sm-9">
                            <select class="selectpicker" id="route_employee_id" name="route_employee_id" data-live-search="true" required>
                              <option value=""></option>
                              <?php
                              $select_query = $db->prepare("
                                SELECT employee_id, employee_first_name, employee_last_name
                                FROM idk_employee
                                WHERE employee_status = :employee_status AND employee_active = :employee_active");

                              $select_query->execute(array(
                                ':employee_status' => 2,
                                ':employee_active' => 1
                              ));

                              while ($select_row = $select_query->fetch()) {
                                echo "<option value='" . $select_row['employee_id'] . "' data-tokens='" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "'>" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="route_clients_select" class="col-sm-3 control-label"><span class="text-danger">*</span> Klijenti:</label>
                          <div class="col-sm-9">
                            <select class="selectpicker" id="route_clients_select" name="route_clients_select[]" multiple data-live-search="true" required>
                              <option value=""></option>
                              <?php
                              $select_query = $db->prepare("
                                SELECT client_id, client_name
                                FROM idk_client
                                WHERE client_active = :client_active");

                              $select_query->execute(array(
                                ':client_active' => 1
                              ));

                              while ($select_row = $select_query->fetch()) {
                                echo "<option value='" . $select_row['client_id'] . "' data-tokens='" . $select_row['client_name'] . "'>" . $select_row['client_name'] . "</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>
                        <br>

                        <div class="list-group" id="route_clients"></div>

                        <script>
                          let routeClientsDiv = document.getElementById("route_clients");
                          new Sortable(routeClientsDiv, {
                            handle: '.handle',
                            animation: 200
                          });

                          let routeClients = [];
                          $('#route_clients_select').on('change', function() {
                            routeClients = $(this).val();
                            $('#route_clients').html('');
                            $.ajax({
                              url: 'getRouteClients.php',
                              method: 'post',
                              data: {
                                routeClients
                              },
                              dataType: 'text',
                              success: function(data) {
                                $('#route_clients').html(data);
                              }
                            });
                          });
                        </script>

                        <br>

                        <!-- Submit -->
                        <div class="form-group">
                          <div class="col-sm-offset-2 col-sm-10 text-right">
                            <button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
                              <i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span>
                            </button>
                            <br>
                            <small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
                          </div>
                        </div>
                      </form>
                      <!-- End form - add route -->

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
           * 							EDIT ROUTE
           * *********************************************************/
        case "edit":

          $route_id = $_GET['id'];

          //Check if route exists
          $check_query = $db->prepare("
            SELECT route_id
            FROM idk_route
            WHERE route_id = :route_id AND route_active = :route_active");

          $check_query->execute(array(
            ':route_id' => $route_id,
            ':route_active' => 1
          ));

          $number_of_rows = $check_query->rowCount();

          if ($number_of_rows == 1) {

            if ($getEmployeeStatus == 1) {

              // Get route data
              $query = $db->prepare("
                SELECT route_day, route_week, route_employee_id
                FROM idk_route
                WHERE route_id = :route_id");

              $query->execute(array(
                ':route_id' => $route_id
              ));

              $route = $query->fetch();

              $route_day = $route['route_day'];
              $route_week = $route['route_week'];
              $route_employee_id = $route['route_employee_id'];

              // Get all clients and positions for route
              $query_clients_and_positions = $db->prepare("
                SELECT rc_client_id, rc_client_position
                FROM idk_route_client
                WHERE rc_route_id = :rc_route_id");

              $query_clients_and_positions->execute(array(
                ':rc_route_id' => $route_id
              ));

              // define an empty array of clients and positions
              $route_clients = array();
              $route_positions = array();

              // push client_ids and client_positions from idk_route_client into route_clients and route_positions
              while ($route_client_row = $query_clients_and_positions->fetch()) {
                $route_client = $route_client_row['rc_client_id'];
                $route_position = $route_client_row['rc_client_position'];
                array_push($route_clients, $route_client);
                array_push($route_positions, $route_position);
              }

            ?>

              <div class="row">
                <div class="col-xs-8">
                  <h1><i class="fa fa-map-marker idk_color_green" aria-hidden="true"></i> Uredi rutu</h1>
                </div>
                <div class="col-xs-4 text-right idk_margin_top10">
                  <a href="<?php getSiteUrl(); ?>idkadmin/routes?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
                </div>
                <div class="col-xs-12">
                  <hr>
                </div>
              </div>

              <div class="row">
                <div class="col-md-12">
                  <div class="content_box">
                    <div class="row">
                      <div class="col-md-offset-1 col-md-8">

                        <!-- Form - edit route -->
                        <form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=edit_route" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

                          <input type="hidden" name="route_id" value="<?php echo $route_id; ?>" />

                          <div class="form-group">
                            <label for="route_day" class="col-sm-3 control-label"><span class="text-danger">*</span> Dan:</label>
                            <div class="col-sm-9">
                              <select class="selectpicker" id="route_day" name="route_day" data-live-search="true" required>
                                <option value=""></option>
                                <option value="1" <?php if ($route_day == 1) {
                                                    echo " selected";
                                                  } ?> data-tokens="Ponedjeljak">Ponedjeljak</option>
                                <option value="2" <?php if ($route_day == 2) {
                                                    echo " selected";
                                                  } ?> data-tokens="Utorak">Utorak</option>
                                <option value="3" <?php if ($route_day == 3) {
                                                    echo " selected";
                                                  } ?> data-tokens="Srijeda">Srijeda</option>
                                <option value="4" <?php if ($route_day == 4) {
                                                    echo " selected";
                                                  } ?> data-tokens="Četvrtak">Četvrtak</option>
                                <option value="5" <?php if ($route_day == 5) {
                                                    echo " selected";
                                                  } ?> data-tokens="Petak">Petak</option>
                                <option value="6" <?php if ($route_day == 6) {
                                                    echo " selected";
                                                  } ?> data-tokens="Subota">Subota</option>
                                <option value="7" <?php if ($route_day == 7) {
                                                    echo " selected";
                                                  } ?> data-tokens="Nedjelja">Nedjelja</option>
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="route_week" class="col-sm-3 control-label">Sedmica u mjesecu:</label>
                            <div class="col-sm-9">
                              <select class="selectpicker" id="route_week" name="route_week" data-live-search="true">
                                <option value=""></option>
                                <option value="1" <?php if ($route_week == 1) {
                                                    echo " selected";
                                                  } ?> data-tokens="1">1.</option>
                                <option value="2" <?php if ($route_week == 2) {
                                                    echo " selected";
                                                  } ?> data-tokens="2">2.</option>
                              </select>
                              <small>Ako komercijalista ima samo jednu rutu za odabrani dan, ostavite prazno</small>
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="route_employee_id" class="col-sm-3 control-label"><span class="text-danger">*</span> Komercijalista:</label>
                            <div class="col-sm-9">
                              <select class="selectpicker" id="route_employee_id" name="route_employee_id" data-live-search="true" required>
                                <option value=""></option>
                                <?php
                                $select_query = $db->prepare("
                                  SELECT employee_id, employee_first_name, employee_last_name
                                  FROM idk_employee
                                  WHERE employee_status = :employee_status AND employee_active = :employee_active");

                                $select_query->execute(array(
                                  ':employee_status' => 2,
                                  ':employee_active' => 1
                                ));

                                while ($select_row = $select_query->fetch()) {
                                  echo "<option value='" . $select_row['employee_id'] . "'";
                                  if ($select_row['employee_id'] == $route_employee_id) {
                                    echo " selected ";
                                  }
                                  echo " data-tokens='" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "'>" . $select_row['employee_first_name'] . " " . $select_row['employee_last_name'] . "</option>";
                                }
                                ?>
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="route_clients_select" class="col-sm-3 control-label"><span class="text-danger">*</span> Klijenti:</label>
                            <div class="col-sm-9">
                              <select class="selectpicker" id="route_clients_select" name="route_clients_select[]" multiple data-live-search="true" required>
                                <option value=""></option>
                                <?php
                                $select_query = $db->prepare("
                                  SELECT client_id, client_name
                                  FROM idk_client
                                  WHERE client_active = :client_active");

                                $select_query->execute(array(
                                  ':client_active' => 1
                                ));

                                while ($select_row = $select_query->fetch()) {
                                  echo "<option value='" . $select_row['client_id'] . "'";
                                  if (in_array($select_row['client_id'], $route_clients)) {
                                    echo " selected ";
                                  }
                                  echo "data-tokens='" . $select_row['client_name'] . "'>" . $select_row['client_name'] . "</option>";
                                }
                                ?>
                              </select>
                            </div>
                          </div>
                          <br>

                          <div class="list-group" id="route_clients"></div>

                          <script>
                            let routeClientsDiv = document.getElementById("route_clients");
                            new Sortable(routeClientsDiv, {
                              handle: '.handle',
                              animation: 200
                            });

                            let routeClients = [];

                            <?php foreach ($route_clients as $route_client) { ?>
                              routeClients.push(<?php echo $route_client; ?>);
                            <?php } ?>

                            $.ajax({
                              url: 'getRouteClients.php',
                              method: 'post',
                              data: {
                                routeClients
                              },
                              dataType: 'text',
                              success: function(data) {
                                $('#route_clients').html(data);
                              }
                            });

                            $('#route_clients_select').on('change', function() {
                              routeClients = $(this).val();
                              $('#route_clients').html('');
                              $.ajax({
                                url: 'getRouteClients.php',
                                method: 'post',
                                data: {
                                  routeClients
                                },
                                dataType: 'text',
                                success: function(data) {
                                  $('#route_clients').html(data);
                                }
                              });
                            });
                          </script>

                          <br>

                          <!-- Submit -->
                          <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10 text-right">
                              <button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
                                <i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span>
                              </button>
                              <br>
                              <small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
                            </div>
                          </div>
                        </form>
                        <!-- End form - edit route -->

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
          } else {
            header("Location: routes?page=list&mess=5");
          }

          break;



          /************************************************************
           * 							OPEN REPORT
           * *********************************************************/
        case "open_report":

          $route_id = isset($_GET['id']) ? $_GET['id'] : NULL;
          $rr_datetime = isset($_GET['date']) ? $_GET['date'] : NULL;

          if (isset($route_id) and isset($rr_datetime)) {

            $rr_datetime_array = explode('-', $rr_datetime);
            $rr_datetime = $rr_datetime_array[0] . '-' . $rr_datetime_array[1] . '-' . $rr_datetime_array[2];

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

            //Check if route report exists
            $check_query = $db->prepare("
              SELECT t1.*, t2.route_employee_id, t2.route_day
              FROM idk_route_report t1
              INNER JOIN idk_route t2
              ON t1.rr_route_id = t2.route_id
              WHERE t1.rr_route_id = :rr_route_id AND (t1.rr_datetime BETWEEN :rr_datetime_start AND :rr_datetime_end)
              GROUP BY t1.rr_route_id
              ORDER BY t1.rr_datetime DESC");

            $check_query->execute(array(
              ':rr_route_id' => $route_id,
              ':rr_datetime_start' => date('Y-m-d 00:00:00', strtotime($rr_datetime)),
              ':rr_datetime_end' => date('Y-m-d 23:59:59', strtotime($rr_datetime))
            ));

            $number_of_rows = $check_query->rowCount();

            if ($number_of_rows != 0) {

              $check_row = $check_query->fetch();
              $route_employee_id = $check_row['route_employee_id'];
              $route_day = $check_row['route_day'];

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

              //Get employee name
              $route_employee_query = $db->prepare("
                SELECT employee_first_name, employee_last_name
                FROM idk_employee
                WHERE employee_id = :employee_id");

              $route_employee_query->execute(array(
                ':employee_id' => $route_employee_id
              ));

              $route_employee_row = $route_employee_query->fetch();
              $employee_first_name = $route_employee_row['employee_first_name'];
              $employee_last_name = $route_employee_row['employee_last_name'];

            ?>

              <div class="row idk_display_none_for_print">
                <div class="col-xs-8">
                  <h1><i class="fa fa-map-marker idk_color_green" aria-hidden="true"></i> Izvještaj o ruti: <?php echo $route_day; ?>, za komercijalistu: <?php echo $employee_first_name . ' ' . $employee_last_name; ?><small> - <?php echo date('d.m.Y.', strtotime($rr_datetime)); ?></small></h1>
                </div>
                <div class="col-xs-4 text-right idk_margin_top10">
                  <a href="<?php getSiteUrl(); ?>idkadmin/reports?page=routes" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
                              //Get clients status for route report
                              $route_client_query = $db->prepare("
                                SELECT rr_client_id, rr_status
                                FROM idk_route_report
                                WHERE rr_route_id = :rr_route_id AND (rr_datetime BETWEEN :rr_datetime_start AND :rr_datetime_end)");

                              $route_client_query->execute(array(
                                ':rr_route_id' => $route_id,
                                ':rr_datetime_start' => date('Y-m-d 00:00:00', strtotime($rr_datetime)),
                                ':rr_datetime_end' => date('Y-m-d 23:59:59', strtotime($rr_datetime))
                              ));

                              $visited_clients = array();
                              $unvisited_clients = array();
                              $other_clients = array();
                              $additional_clients = array();

                              while ($route_client = $route_client_query->fetch()) {
                                $client_id = $route_client['rr_client_id'];
                                $client_status = $route_client['rr_status'];

                                //Check if client is added additionally by a commercialist or not
                                $route_client_check_additional_query = $db->prepare("
                                  SELECT rc_id
                                  FROM idk_route_client
                                  WHERE rc_route_id = :rc_route_id AND rc_client_id = :rc_client_id");

                                $route_client_check_additional_query->execute(array(
                                  ':rc_route_id' => $route_id,
                                  ':rc_client_id' => $client_id
                                ));

                                $number_of_rows_route_clients_additional = $route_client_check_additional_query->rowCount();

                                if ($number_of_rows_route_clients_additional == 0) {
                                  array_push($additional_clients, $client_id);
                                } else {
                                  if ($client_status == 1) {
                                    array_push($visited_clients, $client_id);
                                  } elseif ($client_status == 2) {
                                    array_push($unvisited_clients, $client_id);
                                  }
                                }
                              }
                              ?>

                              <!-- Posjećeni klijenti -->
                              <div class="row">
                                <div class="col-sm-9">
                                  <h5>Posjećeni klijenti</h5>
                                </div>
                                <div class="col-sm-3 text-right">
                                  <a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column" id="idk_print_report_btn">
                                    <i class="fa fa-print" aria-hidden="true"></i> <span>Print</span>
                                  </a>
                                </div>
                              </div>

                              <?php foreach ($visited_clients as $visited_client_id) {
                                //Get client info
                                $client_query = $db->prepare("
                                  SELECT client_name, client_image, client_business_type
                                  FROM idk_client
                                  WHERE client_id = :client_id");

                                $client_query->execute(array(
                                  ':client_id' => $visited_client_id
                                ));

                                $client_row = $client_query->fetch();
                                $client_name = $client_row['client_name'];
                                $client_image = $client_row['client_image'];
                                $client_business_type = $client_row['client_business_type'];

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

                                $select_client_datetime_query = $db->prepare("
                                  SELECT rr_datetime
                                  FROM idk_route_report
                                  WHERE rr_route_id = :rr_route_id AND rr_client_id = :rr_client_id AND (rr_datetime BETWEEN :rr_datetime_start AND :rr_datetime_end)");

                                $select_client_datetime_query->execute(array(
                                  ':rr_route_id' => $route_id,
                                  ':rr_client_id' => $visited_client_id,
                                  ':rr_datetime_start' => date('Y-m-d 00:00:00', strtotime($rr_datetime)),
                                  ':rr_datetime_end' => date('Y-m-d 23:59:59', strtotime($rr_datetime))
                                ));

                                $select_client_datetime_row = $select_client_datetime_query->fetch();
                                $rr_datetime_client = $select_client_datetime_row['rr_datetime'];
                              ?>
                                <div class="row">
                                  <div class="col-sm-1 text-right">
                                    <!-- <img src="<?php //getSiteUrl(); 
                                                    ?>idkadmin/files/clients/images/<?php //echo $client_image; 
                                                                                                          ?>" class="idk_profile_img" alt="<?php //echo $client_name . ' ' . $client_business_type_echo; 
                                                                                                                                                                      ?> slika"> -->
                                    <img src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>" class="idk_profile_img" alt="<?php echo $client_name; ?> slika">
                                  </div>
                                  <div class="col-sm-11 idk_color_dark_green idk_padding_left20">
                                    <!-- <?php //echo $client_name . ' ' . $client_business_type_echo; 
                                          ?><br> -->
                                    <?php echo $client_name; ?><br>
                                    <?php echo date('d.m.Y. H:i', strtotime($rr_datetime_client)); ?>
                                  </div>
                                </div>
                                <br>
                              <?php } ?>
                              <br>
                              <br>

                              <!-- Neposjećeni klijenti -->
                              <div class="row">
                                <div class="col-sm-12">
                                  <h5 class="idk_border_red">Neposjećeni klijenti</h5>
                                </div>
                              </div>

                              <?php foreach ($unvisited_clients as $unvisited_client_id) {
                                //Get client info
                                $client_query = $db->prepare("
                                  SELECT client_name, client_image, client_business_type
                                  FROM idk_client
                                  WHERE client_id = :client_id");

                                $client_query->execute(array(
                                  ':client_id' => $unvisited_client_id
                                ));

                                $client_row = $client_query->fetch();
                                $client_name = $client_row['client_name'];
                                $client_image = $client_row['client_image'];
                                $client_business_type = $client_row['client_business_type'];

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

                                $select_client_datetime_query = $db->prepare("
                                  SELECT rr_comment, rr_datetime
                                  FROM idk_route_report
                                  WHERE rr_route_id = :rr_route_id AND rr_client_id = :rr_client_id AND (rr_datetime BETWEEN :rr_datetime_start AND :rr_datetime_end)");

                                $select_client_datetime_query->execute(array(
                                  ':rr_route_id' => $route_id,
                                  ':rr_client_id' => $unvisited_client_id,
                                  ':rr_datetime_start' => date('Y-m-d 00:00:00', strtotime($rr_datetime)),
                                  ':rr_datetime_end' => date('Y-m-d 23:59:59', strtotime($rr_datetime))
                                ));

                                $select_client_datetime_row = $select_client_datetime_query->fetch();
                                $rr_comment_client = $select_client_datetime_row['rr_comment'];
                                $rr_datetime_client = $select_client_datetime_row['rr_datetime'];
                              ?>
                                <div class="row">
                                  <div class="col-sm-1 text-right">
                                    <!-- <img src="<?php //getSiteUrl(); 
                                                    ?>idkadmin/files/clients/images/<?php //echo $client_image; 
                                                                                                          ?>" class="idk_profile_img" alt="<?php //echo $client_name . ' ' . $client_business_type_echo; 
                                                                                                                                                                      ?> slika"> -->
                                    <img src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>" class="idk_profile_img" alt="<?php echo $client_name; ?> slika">
                                  </div>
                                  <div class="col-sm-11 idk_color_red idk_padding_left20">
                                    <!-- <?php //echo $client_name . ' ' . $client_business_type_echo; 
                                          ?><br> -->
                                    <?php echo $client_name; ?><br>
                                    <?php echo 'Razlog: ' . $rr_comment_client; ?><br>
                                    <?php echo date('d.m.Y. H:i', strtotime($rr_datetime_client)); ?>
                                  </div>
                                </div>
                                <br>
                              <?php } ?>
                              <br>
                              <br>

                              <!-- Ostali klijenti na ruti -->
                              <?php
                              //Get client info
                              $client_query = $db->prepare("
                                SELECT t1.client_id, t1.client_name, t1.client_image, t1.client_business_type
                                FROM idk_client t1
                                INNER JOIN idk_route_client t2
                                ON t1.client_id = t2.rc_client_id
                                WHERE t2.rc_route_id = :rc_route_id");

                              $client_query->execute(array(
                                ':rc_route_id' => $route_id
                              ));

                              $number_of_rows = $client_query->rowCount();

                              if ($number_of_rows > (count($visited_clients) + count($unvisited_clients))) {
                              ?>

                                <div class="row">
                                  <div class="col-sm-12">
                                    <h5 class="idk_border_gray">Ostali klijenti na ruti</h5>
                                  </div>
                                </div>

                                <?php
                                while ($client_row = $client_query->fetch()) {
                                  $client_id = $client_row['client_id'];
                                  $client_name = $client_row['client_name'];
                                  $client_image = $client_row['client_image'];
                                  $client_business_type = $client_row['client_business_type'];

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

                                  if (!in_array($client_id, $visited_clients) and !in_array($client_id, $unvisited_clients)) {
                                ?>
                                    <div class="row">
                                      <div class="col-sm-1 text-right">
                                        <!-- <img src="<?php //getSiteUrl(); 
                                                        ?>idkadmin/files/clients/images/<?php //echo $client_image; 
                                                                                                              ?>" class="idk_profile_img" alt="<?php //echo $client_name . ' ' . $client_business_type_echo; 
                                                                                                                                                                          ?> slika"> -->
                                        <img src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>" class="idk_profile_img" alt="<?php echo $client_name; ?> slika">
                                      </div>
                                      <div class="col-sm-11 idk_padding_top10 idk_padding_left20">
                                        <!-- <?php //echo $client_name . ' ' . $client_business_type_echo; 
                                              ?> -->
                                        <?php echo $client_name; ?>
                                      </div>
                                    </div>
                                <?php }
                                } ?>
                                <br>
                                <br>
                              <?php } ?>

                              <?php if (count($additional_clients) > 0) { ?>
                                <!-- Posjećeni klijenti mimo rute -->
                                <div class="row">
                                  <div class="col-sm-12">
                                    <h5 class="idk_border_blue">Posjećeni klijenti mimo rute</h5>
                                  </div>
                                </div>

                                <?php foreach ($additional_clients as $additional_client_id) {
                                  //Get client info
                                  $client_query = $db->prepare("
                                    SELECT client_name, client_image, client_business_type
                                    FROM idk_client
                                    WHERE client_id = :client_id");

                                  $client_query->execute(array(
                                    ':client_id' => $additional_client_id
                                  ));

                                  $client_row = $client_query->fetch();
                                  $client_name = $client_row['client_name'];
                                  $client_image = $client_row['client_image'];
                                  $client_business_type = $client_row['client_business_type'];

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

                                  $select_client_datetime_query = $db->prepare("
                                    SELECT rr_comment, rr_datetime
                                    FROM idk_route_report
                                    WHERE rr_route_id = :rr_route_id AND rr_client_id = :rr_client_id AND (rr_datetime BETWEEN :rr_datetime_start AND :rr_datetime_end)");

                                  $select_client_datetime_query->execute(array(
                                    ':rr_route_id' => $route_id,
                                    ':rr_client_id' => $additional_client_id,
                                    ':rr_datetime_start' => date('Y-m-d 00:00:00', strtotime($rr_datetime)),
                                    ':rr_datetime_end' => date('Y-m-d 23:59:59', strtotime($rr_datetime))
                                  ));

                                  $select_client_datetime_row = $select_client_datetime_query->fetch();
                                  $rr_comment_client = $select_client_datetime_row['rr_comment'];
                                  $rr_datetime_client = $select_client_datetime_row['rr_datetime'];
                                ?>
                                  <div class="row">
                                    <div class="col-sm-1 text-right">
                                      <!-- <img src="<?php //getSiteUrl(); 
                                                      ?>idkadmin/files/clients/images/<?php //echo $client_image; 
                                                                                                            ?>" class="idk_profile_img" alt="<?php //echo $client_name . ' ' . $client_business_type_echo; 
                                                                                                                                                                        ?> slika"> -->
                                      <img src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>" class="idk_profile_img" alt="<?php echo $client_name; ?> slika">
                                    </div>
                                    <div class="col-sm-11 idk_color_dark_blue idk_padding_left20">
                                      <!-- <?php //echo $client_name . ' ' . $client_business_type_echo; 
                                            ?><br> -->
                                      <?php echo $client_name; ?><br>
                                      <?php echo 'Razlog: ' . $rr_comment_client; ?><br>
                                      <?php echo date('d.m.Y. H:i', strtotime($rr_datetime_client)); ?>
                                    </div>
                                  </div>
                                  <br>
                                <?php } ?>
                                <br>
                                <br>
                              <?php } ?>
                            </div>

                            <div class="col-md-6">
                              <div class="row">
                                <div class="col-sm-12">
                                  <h5>Informacije o komercijalisti i ruti</h5>
                                </div>
                              </div>
                              <!-- Get route and employee info -->
                              <div class="row">
                                <strong class="col-sm-4 text-right">Komercijalista:</strong>
                                <div class="col-sm-8"><?php echo $employee_first_name . " " . $employee_last_name; ?></div>
                              </div>
                              <br>
                              <div class="row">
                                <strong class="col-sm-4 text-right">Ruta:</strong>
                                <div class="col-sm-8"><?php echo $route_day; ?></div>
                              </div>
                              <br>
                              <div class="row">
                                <strong class="col-sm-4 text-right">Datum:</strong>
                                <div class="col-sm-8"><?php echo date('d.m.Y.', strtotime($rr_datetime)); ?></div>
                              </div>
                              <br>
                              <br>

                              <!-- Mapa -->
                              <div class="row">
                                <div class="col-sm-12">
                                  <div id="map"></div>
                                </div>
                              </div>
                            </div>
                            <br>
                            <br>

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
                        <h3>Izvještaj o ruti</h3>
                        <p class="idk_margin_top30">
                          <strong>Ruta za dan</strong> <br>
                          <?php echo $route_day; ?> <br>
                          <strong>Komercijalista</strong> <br>
                          <?php echo $employee_first_name . " " . $employee_last_name; ?> <br>
                          <strong>Datum</strong> <br>
                          <?php echo date('d.m.Y.', strtotime($rr_datetime)); ?> <br>
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
                        <h4>Posjećeni klijenti</h4>

                        <?php foreach ($visited_clients as $visited_client_id) {
                          //Get product info
                          $client_query = $db->prepare("
                            SELECT client_name, client_business_type
                            FROM idk_client
                            WHERE client_id = :client_id");

                          $client_query->execute(array(
                            ':client_id' => $visited_client_id
                          ));

                          $client_row = $client_query->fetch();
                          $client_name = $client_row['client_name'];
                          $client_business_type = $client_row['client_business_type'];

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
                        ?>
                          <div class="row">
                            <div class="col-sm-12 idk_color_dark_green">
                              <!-- <?php //echo $client_name . ' ' . $client_business_type_echo; 
                                    ?> -->
                              <?php echo $client_name; ?>
                            </div>
                          </div>
                        <?php } ?>
                      </div>

                      <div class="col-xs-6">
                        <h4>Neposjećeni klijenti</h4>

                        <?php foreach ($unvisited_clients as $unvisited_client_id) {
                          //Get product info
                          $client_query = $db->prepare("
                            SELECT client_name, client_business_type
                            FROM idk_client
                            WHERE client_id = :client_id");

                          $client_query->execute(array(
                            ':client_id' => $unvisited_client_id
                          ));

                          $client_row = $client_query->fetch();
                          $client_name = $client_row['client_name'];
                          $client_business_type = $client_row['client_business_type'];

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
                        ?>
                          <div class="row">
                            <div class="col-sm-12 idk_color_red">
                              <!-- <?php //echo $client_name . ' ' . $client_business_type_echo; 
                                    ?> -->
                              <?php echo $client_name; ?>
                            </div>
                          </div>
                        <?php } ?>
                      </div>
                    </div>

                    <?php
                    //Get client info
                    $client_query = $db->prepare("
                      SELECT t1.client_id, t1.client_name, t1.client_image, t1.client_business_type
                      FROM idk_client t1
                      INNER JOIN idk_route_client t2
                      ON t1.client_id = t2.rc_client_id
                      WHERE t2.rc_route_id = :rc_route_id");

                    $client_query->execute(array(
                      ':rc_route_id' => $route_id
                    ));

                    $number_of_rows = $client_query->rowCount();

                    if ($number_of_rows > (count($visited_clients) + count($unvisited_clients))) {
                    ?>

                      <div class="row idk_margin_top50">
                        <div class="col-xs-12">
                          <h4>Ostali klijenti na ruti</h4>

                          <?php
                          while ($client_row = $client_query->fetch()) {
                            $client_id = $client_row['client_id'];
                            $client_name = $client_row['client_name'];
                            $client_image = $client_row['client_image'];
                            $client_business_type = $client_row['client_business_type'];

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

                            if (!in_array($client_id, $visited_clients) and !in_array($client_id, $unvisited_clients)) {
                          ?>
                              <div class="row">
                                <div class="col-sm-12">
                                  <!-- <?php //echo $client_name . ' ' . $client_business_type_echo; 
                                        ?> -->
                                  <?php echo $client_name; ?>
                                </div>
                              </div>
                          <?php }
                          } ?>
                        </div>
                      </div>
                    <?php } ?>

                    <?php if (count($additional_clients) > 0) { ?>

                      <div class="row idk_margin_top50">
                        <div class="col-xs-12">
                          <h4>Posjećeni klijenti mimo rute</h4>

                          <?php
                          foreach ($additional_clients as $additional_client_id) {
                            //Get product info
                            $client_query = $db->prepare("
                              SELECT client_name, client_business_type
                              FROM idk_client
                              WHERE client_id = :client_id");

                            $client_query->execute(array(
                              ':client_id' => $additional_client_id
                            ));

                            $client_row = $client_query->fetch();
                            $client_name = $client_row['client_name'];
                            $client_business_type = $client_row['client_business_type'];

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
                          ?>
                            <div class="row">
                              <div class="col-sm-12 idk_color_dark_blue">
                                <!-- <?php //echo $client_name . ' ' . $client_business_type_echo; 
                                      ?> -->
                                <?php echo $client_name; ?>
                              </div>
                            </div>
                          <?php } ?>
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

                  // Leaflet map
                  let map = L.map('map').setView([44.850873, 16.010092], 10);

                  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                  }).addTo(map);

                  map.addControl(new L.Control.Fullscreen());

                  <?php
                  $all_clients = array_merge($unvisited_clients, $visited_clients);
                  foreach ($all_clients as $client_id) {
                    //Get client name
                    $client_query = $db->prepare("
                      SELECT t1.client_name, t1.client_business_type, t2.rr_latitude, t2.rr_longitude, t2.rr_datetime, t2.rr_status, t2.rr_comment
                      FROM idk_client t1
                      INNER JOIN idk_route_report t2
                      ON t1.client_id = t2.rr_client_id
                      WHERE t2.rr_client_id = :rr_client_id AND t2.rr_route_id = :rr_route_id AND (t2.rr_datetime BETWEEN :rr_datetime_start AND :rr_datetime_end)");

                    $client_query->execute(array(
                      ':rr_client_id' => $client_id,
                      ':rr_route_id' => $route_id,
                      ':rr_datetime_start' => date('Y-m-d 00:00:00', strtotime($rr_datetime)),
                      ':rr_datetime_end' => date('Y-m-d 23:59:59', strtotime($rr_datetime))
                    ));

                    $client_row = $client_query->fetch();
                    $client_name = $client_row['client_name'];
                    $client_business_type = $client_row['client_business_type'];
                    $rr_latitude = $client_row['rr_latitude'];
                    $rr_longitude = $client_row['rr_longitude'];
                    $rr_status = $client_row['rr_status'];
                    $rr_comment = $client_row['rr_comment'];
                    $rr_datetime_client = $client_row['rr_datetime'];

                    //Get client business type
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
                    $map_popup = NULL;

                    if ($rr_status == 1) {
                      // $map_popup = $client_name . ' ' . $client_business_type_echo . '<br>Posjećeno: ' . date('d.m.Y. H:i', strtotime($rr_datetime_client));
                      $map_popup = $client_name . '<br>Posjećeno: ' . date('d.m.Y. H:i', strtotime($rr_datetime_client));
                    } elseif ($rr_status == 2) {
                      // $map_popup = $client_name . ' ' . $client_business_type_echo . '<br>Nije posjećeno: ' . $rr_comment . '<br>' . date('d.m.Y. H:i', strtotime($rr_datetime_client));
                      $map_popup = $client_name . '<br>Nije posjećeno: ' . $rr_comment . '<br>' . date('d.m.Y. H:i', strtotime($rr_datetime_client));
                    }
                  ?>

                    L.marker([<?php echo $rr_latitude; ?>, <?php echo $rr_longitude; ?>]).addTo(map)
                      .bindPopup('<?php echo $map_popup; ?>')
                      .openPopup();

                  <?php } ?>

                });
              </script>
      <?php
            } else {
              header("Location: reports?page=routes&mess=2");
            }
          } else {
            header("Location: reports?page=routes&mess=2");
          }
          break;



          /************************************************************
           * 							ARCHIVE
           * *********************************************************/
        case "archive":

          if ($getEmployeeStatus == 1) {

            $route_id = $_GET['id'];

            //Save
            $query = $db->prepare("
              UPDATE idk_route
              SET route_active = :route_active
              WHERE route_id = :route_id");

            $query->execute(array(
              ':route_active' => 0,
              ':route_id' => $route_id
            ));

            //Add to log
            $log_desc = "Arhivirao rutu s ID brojem: ${route_id}";
            $log_date = date('Y-m-d H:i:s');
            addLog($logged_employee_id, $log_desc, $log_date);

            header("Location: " . getSiteUrlr() . "idkadmin/routes?page=list&mess=4");
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
      <footer class="idk_display_none_for_print"><?php getCopyright(); ?></footer>
    </div>
  </div>
</body>

</html>