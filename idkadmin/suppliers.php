<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
  $page = $_REQUEST["page"];
} else {
  header("Location: suppliers?page=list");
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
 * 							LIST ALL SUPPLIERS
 * *********************************************************/
        case "list":

          if ($getEmployeeStatus == 1) {

            //Mark as read
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
      ?>

            <div class="row">
              <div class="col-xs-12">
                <?php
                if (isset($_GET['mess'])) {
                  $mess = $_GET['mess'];
                } else {
                  $mess = 0;
                }

                if ($mess == 1) {
                  echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
                } elseif ($mess == 2) {
                  echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novog dobavljača.</div>';
                } elseif ($mess == 3) {
                  echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali dobavljača.</div>';
                } elseif ($mess == 4) {
                  echo '<div class="alert material-alert material-alert_danger">Greška! Dobavljač koji pokušavate dodati već postoji u bazi podataka.</div>';
                }
                ?>
              </div>
            </div>

            <div class="row">
              <div class="col-xs-12">
                <h1><i class="fa fa-building idk_color_green" aria-hidden="true"></i> Dobavljači</h1>
              </div>
              <div class="col-xs-12">
                <hr>
              </div>
            </div>

            <div class="row">
              <div class="col-md-12">
                <div class="content_box">
                  <h5>Dodaj novog dobavljača</h5>
                  <div class="row">
                    <div class="col-md-8 idk_setting_form_wrapper">

                      <!-- Form - add supplier -->
                      <form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_supplier" method="post" class="form-horizontal" role="form">

                        <div class="form-group">
                          <label for="supplier_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv dobavljača:</label>
                          <div class="col-sm-9">
                            <div class="materail-input-block materail-input-block_success">
                              <input class="form-control materail-input" type="text" name="supplier_name" id="supplier_name" placeholder="Naziv dobavljača ..." required>
                              <span class="materail-input-block__line"></span>
                            </div>
                          </div>
                        </div>

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
                      <!-- End form - add supplier -->

                    </div>
                  </div>
                  <hr>

                  <div class="row">
                    <div class="col-xs-12">
                      <h5>Trenutni dobavljači</h5>

                      <script type="text/javascript">
                        $(document).ready(function() {
                          $('#idk_table').DataTable({

                            "order": [
                              [0, "asc"]
                            ],

                            "bAutoWidth": false,

                            "aoColumns": [{
                                "width": "10%"
                              },
                              {
                                "width": "80%"
                              },
                              {
                                "width": "10%",
                                "bSortable": false
                              }
                            ]
                          });
                        });
                      </script>

                      <!-- Suppliers table -->
                      <table id="idk_table" class="display" cellspacing="0" width="100%">
                        <thead>
                          <tr>
                            <th>ID</th>
                            <th>Naziv</th>
                            <th>Obriši</th>
                          </tr>
                        </thead>

                        <tbody>
                          <?php
                          $query = $db->prepare("
														SELECT supplier_id, supplier_name
														FROM idk_supplier");

                          $query->execute();

                          while ($row = $query->fetch()) {

                            $supplier_id = $row['supplier_id'];
                            $supplier_name = $row['supplier_name'];

                          ?>
                            <tr>
                              <td class="text-center">
                                <?php echo $supplier_id; ?>
                              </td>
                              <td>
                                <?php echo $supplier_name; ?>
                              </td>
                              <td class="text-center">
                                <a href="#" data="<?php getSiteUrl(); ?>idkadmin/do.php?form=delete_supplier&supplier_id=<?php echo $supplier_id; ?>" data-toggle="modal" data-target="#modalDelete" class="delete dropdown-toggle material-dropdown-btn material-btn material-btn_danger">
                                  <i class="fa fa-trash-o" aria-hidden="true"></i>
                                </a>
                              </td>
                            </tr>
                          <?php } ?>

                          <script>
                            $(".delete").click(function() {
                              var addressValue = $(this).attr("data");
                              document.getElementById("obrisi_link").href = addressValue;
                            });
                          </script>
                          <!-- Modal delete-->
                          <div class="modal material-modal material-modal_danger fade" id="modalDelete" tabindex="-1" role="dialog" aria-labelledby="modalDeleteLabel" aria-hidden="true">
                            <div class="modal-dialog">
                              <div class="modal-content material-modal__content">
                                <div class="modal-header material-modal__header">
                                  <button class="close material-modal__close" data-dismiss="modal">&times;</span><span class="sr-only">Zatvori</span></button>
                                  <h4 class="modal-title material-modal__title" id="modalDeleteLabel">Brisanje</h4>
                                </div>
                                <div class="modal-body material-modal__body">
                                  <p>Jeste li sigurni da želite obrisati dobavljača?</p>
                                </div>
                                <div class="modal-footer material-modal__footer">
                                  <button type="button" class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                                  <a id="obrisi_link" href=""><button type="button" class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
                                </div>
                              </div>
                            </div>
                          </div>

                        </tbody>
                      </table>
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