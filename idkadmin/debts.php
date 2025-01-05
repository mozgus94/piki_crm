<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
  $page = $_REQUEST["page"];
} else {
  header("Location: debts?page=list");
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

        case "list":
      ?>

          <div class="row">
            <div class="col-xs-8">
              <h1><i class="fa fa-square idk_color_green"></i> Zaduženja</h1>
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
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste obirsali zaduženje.</div>';
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
                              "width": "5%"
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
                          <th>ID</th>
                          <th>Klijent</th>
                          <th>Komercijalista</th>
                          <th>Zadužio</th>
                          <th>Količina</th>
                          <th>Opis</th>
                          <th>Datum</th>
                          <th></th>
                        </tr>
                      </thead>

                      <tbody>

                        <!-- Get data for debt -->
                        <?php
                        if (isset($_GET['type'])) {

                          $type = $_GET['type'];

                          $query = $db->prepare("
                            SELECT debt_id, client_name, employee_first_name, employee_last_name, debt_type, debt_datetime, debt_desc, debt_equipment, debt_quantity
                            FROM idk_debt
                            INNER JOIN idk_client ON idk_debt.debt_clientid = idk_client.client_id
                            INNER JOIN idk_employee ON idk_debt.debt_employeeid = idk_employee.employee_id
                            WHERE debt_type = :debt_type");

                          $query->execute(array(
                            ':debt_type' => $type
                          ));
                        } else {

                          $query = $db->prepare("
                            SELECT debt_id, client_name, employee_first_name, employee_last_name, debt_type, debt_datetime, debt_desc, debt_equipment, debt_quantity
                            FROM idk_debt
                            INNER JOIN idk_client ON idk_debt.debt_clientid = idk_client.client_id
                            INNER JOIN idk_employee ON idk_debt.debt_employeeid = idk_employee.employee_id");

                          $query->execute();
                        }

                        while ($row = $query->fetch()) {

                          $debt_id = $row['debt_id'];
                          $client_name = $row['client_name'];
                          $employee_first_name = $row['employee_first_name'];
                          $employee_last_name = $row['employee_last_name'];
                          $debt_type = $row['debt_type'];
                          $debt_equipment = $row['debt_equipment'];
                          $debt_quantity = $row['debt_quantity'];
                          $debt_desc = $row['debt_desc'];
                          $debt_desc = strlen($debt_desc) > 100 ? substr($debt_desc, 0, 100) . "..." : $debt_desc;
                          $debt_datetime = $row['debt_datetime'];
                          $debt_datetime_format = date('d.m.Y.', strtotime($row['debt_datetime']));

                        ?>
                          <tr>
                            <td class="text-center"><?php echo $debt_id; ?></td>
                            <td><a href="<?php getSiteUrl(); ?>idkadmin/debts?page=open&id=<?php echo $debt_id; ?>"><?php echo $client_name; ?></a></td>
                            <td><?php echo $employee_first_name; ?> <?php echo $employee_last_name; ?></td>
                            <td><?php echo $debt_equipment; ?></td>
                            <td><?php echo $debt_quantity; ?> kom</td>
                            <td><?php echo $debt_desc; ?></td>
                            <td data-sort="<?php echo $debt_datetime; ?>">
                              <?php echo $debt_datetime_format; ?>
                            </td>
                            <td class="text-center">
                              <div class="btn-group material-btn-group">
                                <button class="dropdown-toggle material-dropdown-btn material-btn material-btn_primary idk_btn_table" data-toggle="dropdown"><i class="fa fa-cogs fa-lg" aria-hidden="true"></i> <span class="caret material-btn__caret"></span></button>
                                <ul class="dropdown-menu material-dropdown-menu material-dropdown-menu_primary idk_dropdown_table" role="menu">
                                  <li>
                                    <a href="<?php getSiteUrl(); ?>idkadmin/debts?page=open&id=<?php echo $debt_id; ?>" class="material-dropdown-menu__link">
                                      <i class="fa fa-folder-open-o" aria-hidden="true"></i> Otvori
                                    </a>
                                  </li>

                                  <li class="idk_dropdown_danger">
                                    <a href="#" data="<?php getSiteUrl(); ?>idkadmin/debts?page=del&id=<?php echo $debt_id; ?>" data-toggle="modal" data-target="#archiveModal" class="archive material-dropdown-menu__link">
                                      <i class="fa fa-trash-o" aria-hidden="true"></i> Obriši
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
                                <h4 class="modal-title material-modal__title">Brisanje</h4>
                              </div>
                              <div class="modal-body material-modal__body">
                                <p>Jeste li sigurni da želite obrisati zaduženje?</p>
                              </div>
                              <div class="modal-footer material-modal__footer">
                                <button class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                <a id="archive_link" href=""><button class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
                              </div>
                            </div>
                          </div>
                        </div> <!-- End modal - archive -->

                      </tbody>
                    </table>
                    <!-- End orders table -->

                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php
          break;

        case "open":

          $debt_id = $_GET['id'];

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
            SELECT debt_id, client_name, employee_first_name, employee_last_name, debt_type, debt_datetime, debt_desc, debt_img, debt_equipment, debt_quantity
            FROM idk_debt
            INNER JOIN idk_client ON idk_debt.debt_clientid = idk_client.client_id
            INNER JOIN idk_employee ON idk_debt.debt_employeeid = idk_employee.employee_id
            WHERE debt_id = :debt_id");

          $query->execute(array(
            ':debt_id' => $debt_id
          ));

          $row = $query->fetch();

          $debt_id = $row['debt_id'];
          $client_name = $row['client_name'];
          $employee_first_name = $row['employee_first_name'];
          $employee_last_name = $row['employee_last_name'];
          $debt_type = $row['debt_type'];
          $debt_equipment = $row['debt_equipment'];
          $debt_quantity = $row['debt_quantity'];
          $debt_datetime = date('d.m.Y. - H:i', strtotime($row['debt_datetime']));
          $debt_desc = $row['debt_desc'];

        ?>

          <div class="row">
            <div class="col-xs-8">
              <h1><i class="fa fa-square idk_color_green"></i> Zaduženje #<?php echo "${debt_id} - Klijent: ${client_name}"; ?></h1>
            </div>
            <div class="col-xs-4 text-right idk_margin_top10">
              <a href="<?php getSiteUrl(); ?>idkadmin/debts?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
            </div>
            <div class="col-xs-12">
              <hr>
            </div>
          </div>

          <div class="row">
            <div class="col-md-12">
              <div class="content_box">
                <div class="row">
                  <div class="col-md-6">
                    <ul class="list-unstyled">
                      <li><b>Datum i vrijeme:</b> <?php echo $debt_datetime; ?></li><br>
                      <li><b>Klijent:</b> <?php echo $client_name; ?></li><br>
                      <li><b>Komercijalista:</b> <?php echo $employee_first_name; ?> <?php echo $employee_last_name; ?></li><br>
                      <li><b>Zadužio:</b> <?php echo $debt_equipment; ?></li><br>
                      <li><b>Količina:</b> <?php echo $debt_quantity; ?> kom</li><br>
                      <li><b>Opis:</b> <?php echo $debt_desc; ?></li>
                    </ul>
                  </div>
                  <div class="col-md-6">
                    <?php if ($row['debt_img'] != NULL) { ?>
                      <a class="fancybox" rel="group" href="<?php getSiteUrl(); ?>idkadmin/files/debts/images/<?php echo $row['debt_img']; ?>">
                        <img class="img-responsive" src="<?php getSiteUrl(); ?>idkadmin/files/debts/images/<?php echo $row['debt_img']; ?>">
                      </a>
                    <?php } ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
      <?php
          break;

        case "del":

          if ($getEmployeeStatus == 1) {

            $debt_id = $_GET['id'];

            //Get client name
            $query = $db->prepare("
              SELECT client_name, debt_img
              FROM idk_debt
              INNER JOIN idk_client ON idk_debt.debt_clientid = idk_client.client_id
              WHERE debt_id = :debt_id");

            $query->execute(array(
              ':debt_id' => $debt_id
            ));

            $row = $query->fetch();

            $client_name = $row['client_name'];
            $debt_img = $row['debt_img'];

            if ($debt_img == "" or $debt_img == NULL) {
            } else {
              unlink(getSiteUrlr() . "idkadmin/files/debts/images/" . $debt_img);
              unlink(getSiteUrlr() . "idkadmin/files/debts/thumbs/" . $debt_img);
            }

            $del_query = $db->prepare("
              DELETE FROM idk_debt
              WHERE debt_id = :debt_id");

            $del_query->execute(array(
              ':debt_id' => $debt_id
            ));

            //Add to log
            $log_desc = "Obrisao zaduženje #${debt_id} za klijenta: ${client_name}";
            $log_date = date('Y-m-d H:i:s');
            addLog($logged_employee_id, $log_desc, $log_date);

            header("Location: " . getSiteUrlr() . "idkadmin/debts?page=list&mess=1");
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

      <footer><?php getCopyright(); ?></footer>
    </div>
  </div>
</body>

</html>