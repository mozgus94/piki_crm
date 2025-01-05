<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
  $page = $_REQUEST["page"];
} else {
  $page = "list";
  header("Location: categories?page=list");
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
 * 							LIST ALL CATEGORIES
 * *********************************************************/
        case "list":

          if (isset($_GET['mess'])) {
            $mess = $_GET['mess'];
          } else {
            $mess = 0;
          }
      ?>
          <div class="row">
            <div class="col-xs-8">
              <h1><i class="fa fa-th-large idk_color_green" aria-hidden="true"></i> Kategorije</h1>
            </div>

            <div class="col-xs-4 text-right idk_margin_top10">
              <a href="#" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive" data-toggle="modal" data-target="#categoryModal"><i class="fa fa-plus" aria-hidden="true"></i>
                <span>Dodaj</span></a>

              <!-- Modal add category -->
              <div class="modal material-modal material-modal_primary fade text-left" id="categoryModal">
                <div class="modal-dialog ">
                  <div class="modal-content material-modal__content">
                    <div class="modal-header material-modal__header">
                      <button class="close material-modal__close" data-dismiss="modal">&times;</button>
                      <h4 class="modal-title material-modal__title">Dodaj kategoriju</h4>
                    </div>
                    <div class="modal-body material-modal__body">

                      <?php
                      if ($getEmployeeStatus == 1) {
                      ?>

                        <!-- Form - add category -->
                        <form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_category" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">

                          <div class="form-group">
                            <label for="category_name" class="col-sm-4 control-label"><span class="text-danger">*</span>
                              Naziv:</label>
                            <div class="col-sm-8">
                              <div class="materail-input-block materail-input-block_success">
                                <input class="form-control materail-input" type="text" name="category_name" id="category_name" placeholder="Naziv" required>
                                <span class="materail-input-block__line"></span>
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="category_sub" class="col-sm-4 control-label">Pripada kategoriji:</label>
                            <div class="col-sm-8">

                              <select class="selectpicker" id="category_sub" name="category_sub" data-live-search="true">
                                <option value="0" selected>Samostalna kategorija</option>
                                <?php
                                $select_query = $db->prepare("
																	SELECT category_id, category_name
                                  FROM idk_category
																	ORDER BY category_name");

                                $select_query->execute();

                                while ($select_row = $select_query->fetch()) {
                                  echo "<option value='" . $select_row['category_id'] . "' data-tokens='" . $select_row['category_name'] . "'>" . $select_row['category_name'] . "</option>";
                                }
                                ?>
                              </select>

                            </div>
                          </div>

                          <!-- Add image -->
                          <div class="form-group">
                            <label for="category_image" class="col-sm-4 control-label">Slika:</label>
                            <div class="col-sm-8">
                              <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
                                <div>
                                  <span class="btn btn-default btn-file">
                                    <span class="fileinput-new">Izaberi sliku</span>
                                    <span class="fileinput-exists">Promijeni</span>
                                    <input type="file" name="category_image" id="category_image">
                                  </span>
                                  <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
                                  <script>
                                    $(function() {
                                      $('#category_image').change(function() {

                                        var ext = $('#category_image').val().split('.').pop().toLowerCase();

                                        if ($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
                                          $('#idk_alert_ext').removeClass('hidden');
                                          this.value = null;
                                        } else {
                                          $('#idk_alert_ext').addClass('hidden');
                                        }

                                        var f = this.files[0];

                                        if (f.size > 20388608 || f.fileSize > 20388608) {
                                          $('#idk_alert_size').removeClass('hidden');
                                          this.value = null;
                                        } else {
                                          $('#idk_alert_size').addClass('hidden');
                                        }

                                      });
                                    });
                                  </script>
                                </div>
                              </div>
                            </div>
                          </div>

                          <!-- Alerts for image -->
                          <div class="form-group">
                            <label class="col-sm-4"></label>
                            <div class="col-sm-8">
                              <div id="idk_alert_size" class="hidden">
                                <div class="alert material-alert material-alert_danger">Greška: Fotografija koju pokušavate
                                  dodati je veća od dozvoljene veličine.</div>
                              </div>
                              <div id="idk_alert_ext" class="hidden">
                                <div class="alert material-alert material-alert_danger">Greška: Format fotografije koju
                                  pokušavate dodati nije dozvoljen.</div>
                              </div>
                            </div>
                          </div>

                    </div>
                    <div class="modal-footer material-modal__footer">
                      <ul class="list-inline">
                        <li class="hidden"><i class="fa fa-circle-o-notch fa-spin fa-lg fa-fw text-success"></i></li>
                        <li><button type="submit" class="btn btn-primary material-btn material-btn_primary">Dodaj</button>
                        </li>
                      </ul>
                      </form>
                      <!-- End form - add category -->

                    <?php
                      } else {
                        echo '
                          <div class="alert material-alert material-alert_danger">
                            <h4>NEMATE PRIVILEGIJE!</h4>
                            <p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
                          </div>
                        ';
                      }
                    ?>
                    </div>

                  </div>
                </div>
              </div> <!-- End modal - add category -->

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
                    <?php
                    if ($mess == 1) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste dodali novu kategoriju.</div>';
                    } elseif ($mess == 2) {
                      echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena sa * su obavezna.</div>';
                    } elseif ($mess == 3) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste obrisali kategoriju.</div>';
                    } elseif ($mess == 4) {
                      echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili kategoriju.</div>';
                    }

                    getCategories();
                    ?>

                    <script>
                      $(".obrisi").click(function() {
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
                            <?php
                            if ($getEmployeeStatus == 1) {
                            ?>
                              <p>Jeste li sigurni da želite obrisati kategoriju?</p>
                              <p><strong>Sve potkategorije će biti obrisane!</strong></p>
                          </div>
                          <div class="modal-footer material-modal__footer">
                            <button type="button" class="btn material-btn material-btn" data-dismiss="modal">Zatvori</button>
                            <a id="obrisi_link" href=""><button type="button" class="btn btn-primary material-btn material-btn_danger">OBRIŠI</button></a>
                          </div>
                        <?php
                            } else {
                              echo '
                                <div class="alert material-alert material-alert_danger">
                                  <h4>NEMATE PRIVILEGIJE!</h4>
                                  <p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
                                </div>
                                </div>
                              ';
                            }
                        ?>
                        </div>
                      </div>
                    </div>
                    <hr>

                  </div>
                </div>
              </div>
            </div>
          </div>
          <?php

          break;



          /************************************************************
           * 							DELETE CATEGORY
           * *********************************************************/
        case "del":

          if ($getEmployeeStatus == 1) {

            $category_id = $_GET['category_id'];

            //get cat name
            $cat_open_query = $db->prepare("
              SELECT category_name
              FROM idk_category
              WHERE category_id = :category_id");

            $cat_open_query->execute(array(
              ':category_id' => $category_id
            ));

            $cat_open = $cat_open_query->fetch();

            $category_name = $cat_open['category_name'];

            //Add to log
            $log_desc = "Obrisao kategoriju: ${category_name}";
            $log_date = date('Y-m-d H:i:s');
            addLog($logged_employee_id, $log_desc, $log_date);

            //Delete subcategories
            $category_sub_del_query = $db->prepare("
              DELETE FROM idk_category
              WHERE category_sub = :category_id");

            $category_sub_del_query->execute(array(
              ':category_id' => $category_id
            ));

            //Delete category
            $cat_del_query = $db->prepare("
              DELETE FROM idk_category
              WHERE category_id = :category_id");

            $cat_del_query->execute(array(
              ':category_id' => $category_id
            ));

            header("Location: categories?page=list&mess=3");
          } else {
            echo ' 
              <div class="alert alert-danger" role="alert">
                <h4>NEMATE PRIVILEGIJE!</h4>
                <p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
                <br>
                <a href="javascript: history.go(-1)"><button type="button" class="btn btn-default"><i class="fa fa-chevron-left"></i> Povratak</button></a>
              </div>';
          }

          break;



          /************************************************************
           * 							EDIT CATEGORY
           * *********************************************************/
        case "edit":

          if ($getEmployeeStatus == 1) {

            $category_id = $_GET['category_id'];

            $cat_query = $db->prepare("
              SELECT category_id, category_name, category_image, category_sub
              FROM idk_category
              WHERE category_id = :category_id");

            $cat_query->execute(array(
              ':category_id' => $category_id
            ));

            $cat = $cat_query->fetch();

            $category_id = $cat['category_id'];
            $category_name = $cat['category_name'];
            $category_sub = $cat['category_sub'];
            $category_image = $cat['category_image'];
            if (!isset($category_image)) {
              $category_image = "none.jpg";
            }
          ?>

            <div class="row">
              <div class="col-xs-8">
                <h1><i class="fa fa-th-large idk_color_green" aria-hidden="true"></i> Uredi kategoriju</h1>
              </div>
              <div class="col-xs-4 text-right idk_margin_top10">
                <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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

                      <!-- Form - edit category -->
                      <form action="<?php getSiteUrl(); ?>idkadmin/do.php?form=edit_category" method="post" role="form" class="form-horizontal" enctype="multipart/form-data">

                        <input type="hidden" name="category_id" value="<?php echo $category_id; ?>" />

                        <div class="form-group">
                          <label for="category_name" class="col-sm-4 control-label"><span class="text-danger">*</span>
                            Naziv:</label>
                          <div class="col-sm-8">
                            <div class="materail-input-block materail-input-block_success">
                              <input class="form-control materail-input" type="text" name="category_name" id="category_name" value="<?php echo $category_name; ?>" placeholder="Naziv" required>
                              <span class="materail-input-block__line"></span>
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="category_sub" class="col-sm-4 control-label">Pripada kategoriji:</label>
                          <div class="col-sm-8">

                            <select class="selectpicker" id="category_sub" name="category_sub" data-live-search="true">
                              <option value="0" selected>Samostalna kategorija</option>
                              <?php
                              $select_query = $db->prepare("
																SELECT category_id, category_name
                                FROM idk_category
                                WHERE category_id != :category_id
																ORDER BY category_name");

                              $select_query->execute(array(
                                ':category_id' => $category_id
                              ));

                              while ($select_row = $select_query->fetch()) {
                                echo "<option value='" . $select_row['category_id'] . "'";
                                if ($category_sub == $select_row['category_id']) echo " selected";
                                else echo "";
                                echo " data-tokens='" . $select_row['category_name'] . "'>" . $select_row['category_name'] . "</option>";
                              }
                              ?>
                            </select>

                          </div>
                        </div>

                        <!-- Add image -->
                        <div class="form-group">
                          <label for="category_image" class="col-sm-4 control-label">Slika:</label>
                          <div class="col-sm-8">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                              <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
                                <?php if ($category_image) { ?>
                                  <img src="<?php getSiteUrl(); ?>idkadmin/files/categorys/images/<?php echo $category_image; ?>">
                                <?php } ?>
                              </div>
                              <input type="hidden" name="category_image_url" value="<?php echo $category_image; ?>" />
                              <div>
                                <span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi
                                    sliku</span><span class="fileinput-exists">Promijeni</span><input type="file" name="category_image" id="category_image"></span>
                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
                                <script>
                                  $(function() {
                                    $('#category_image').change(function() {

                                      var ext = $('#category_image').val().split('.').pop().toLowerCase();

                                      if ($.inArray(ext, ['jpg', 'jpeg', 'png']) == -1) {
                                        $('#idk_alert_ext').removeClass('hidden');
                                        this.value = null;
                                      } else {
                                        $('#idk_alert_ext').addClass('hidden');
                                      }

                                      var f = this.files[0];

                                      if (f.size > 20388608 || f.fileSize > 20388608) {
                                        $('#idk_alert_size').removeClass('hidden');
                                        this.value = null;
                                      } else {
                                        $('#idk_alert_size').addClass('hidden');
                                      }

                                    });
                                  });
                                </script>
                              </div>
                            </div>
                          </div>
                        </div>

                        <!-- Alerts for image -->
                        <div class="form-group">
                          <label class="col-sm-4"></label>
                          <div class="col-sm-8">
                            <div id="idk_alert_size" class="hidden">
                              <div class="alert material-alert material-alert_danger">Greška: Fotografija koju pokušavate
                                dodati je veća od dozvoljene veličine.</div>
                            </div>
                            <div id="idk_alert_ext" class="hidden">
                              <div class="alert material-alert material-alert_danger">Greška: Format fotografije koju
                                pokušavate dodati nije dozvoljen.</div>
                            </div>
                          </div>
                        </div>

                        <!-- Submit -->
                        <div class="form-group">
                          <div class="col-sm-offset-2 col-sm-10 text-right">
                            <button type="submit" class="btn material-btn material-btn-icon-success material-btn_success main-container__column"><i class="fa fa-save" aria-hidden="true"></i> <span>Snimi</span></button>
                            <br><small>Sva polja označena sa <span class="text-danger">*</span> su obavezna!</small>
                          </div>
                        </div>
                      </form>
                      <!-- End form - edit category -->

                    </div>
                  </div>
                </div>
              </div>
            </div>
      <?php
          } else {
            echo '
            <div class="alert alert-danger" role="alert">
                <h4>NEMATE PRIVILEGIJE!</h4>
                <p>Nemate privilegije za ovaj dio stranice. Kontaktirajte administratora za pomoć.</p>
                <br>
                <a href="javascript: history.go(-1)"><button type="button" class="btn btn-default"><i class="fa fa-chevron-left"></i> Povratak</button></a>
              </div>';
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