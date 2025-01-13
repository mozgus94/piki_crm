<?php
include("includes/functions.php");
include("includes/common.php");

$getEmployeeStatus = getEmployeeStatus();

if (isset($_REQUEST["page"])) {
  $page = $_REQUEST["page"];
} else {
  $page = "list";
  header("Location: products?page=list");
}

if (isset($_GET["table_page"])) {
  $table_page = $_GET["table_page"];
} else {
  $table_page = 0;
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
         * 							LIST ALL PRODUCTS
         * *********************************************************/
        case "list":
      ?>

        <div class="row">
          <div class="col-xs-8">
            <h1><i class="fa fa-tasks idk_color_green" aria-hidden="true"></i> Proizvodi</h1>
          </div>
          <div class="col-xs-4 text-right idk_margin_top10">
            <a href="<?php getSiteUrl(); ?>idkadmin/products?page=add" class="btn material-btn material-btn-icon-success material-btn_success main-container__column material-btn-icon-responsive"><i class="fa fa-plus" aria-hidden="true"></i> <span>Dodaj</span></a>
          </div>
          <div class="col-xs-12">
            <hr>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <div class="content_box">
              <div id="idkProductsTableContainer">
                <table id="idkProductsTable" class="display" cellspacing="0" width="100%">
                  <thead>
                    <tr>
                      <th>Slika</th>
                      <th>Šifra</th>
                      <th>Naziv</th>
                      <th>MPC</th>
                      <th>VPC</th>
                      <th>Akcije</th>
                    </tr>
                  </thead>
                  <tbody></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <script type="module" src="modules/products/productsData.js"></script>
        <script>
          document.addEventListener("DOMContentLoaded", function () {
            if (window.fetchAndRenderProducts) {
              window.fetchAndRenderProducts();
            }
          });
        </script>

          <?php

          break;



          /************************************************************
           * 							ADD NEW PRODUCT
           * *********************************************************/
        case "add":

          if ($getEmployeeStatus == 1) {
          ?>

            <div class="row">
              <div class="col-xs-8">
                <h1><i class="fa fa-tasks idk_color_green" aria-hidden="true"></i> Dodaj novi proizvod</h1>
              </div>
              <div class="col-xs-4 text-right idk_margin_top10">
                <a href="<?php getSiteUrl(); ?>idkadmin/products?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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

                      <!-- Form - add product -->
                      <form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=add_product" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

                        <input type="hidden" name="table_page" id="table_page" value="<?php echo $table_page; ?>">

                        <div class="form-group">
                          <label for="product_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
                          <div class="col-sm-9">
                            <div class="materail-input-block materail-input-block_success">
                              <input class="form-control materail-input" type="text" name="product_name" id="product_name" placeholder="Naziv" required>
                              <span class="materail-input-block__line"></span>
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="product_api_id" class="col-sm-3 control-label"><span class="text-danger">*</span> API ID:</label>
                          <div class="col-sm-9">
                            <div class="materail-input-block materail-input-block_success">
                              <input class="form-control materail-input" type="text" name="product_api_id" id="product_api_id" placeholder="API ID proizvoda" required>
                              <span class="materail-input-block__line"></span>
                            </div>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="product_supplier" class="col-sm-3 control-label">Dobavljač:</label>
                          <div class="col-sm-9">
                            <select class="selectpicker" id="product_supplier" name="product_supplier" data-live-search="true">
                              <option value=""></option>
                              <?php
                              $select_query = $db->prepare("
																		SELECT supplier_name
																		FROM idk_supplier
                                    ORDER BY supplier_name");

                              $select_query->execute();

                              while ($select_row = $select_query->fetch()) {
                                echo "<option value='" . $select_row['supplier_name'] . "' data-tokens='" . $select_row['supplier_name'] . "'>" . $select_row['supplier_name'] . "</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="product_price" class="col-sm-3 control-label">Cijena:</label>
                          <div class="col-sm-6">
                            <div class="materail-input-block materail-input-block_success">
                              <input class="form-control materail-input" type="number" name="product_price" id="product_price" placeholder="10.000" min="0" step="0.001">
                              <span class="materail-input-block__line"></span>
                            </div>
                          </div>
                          <div class="col-sm-3">
                            <select class="selectpicker" id="product_currency" name="product_currency" data-live-search="true">
                              <?php
                              $select_query = $db->prepare("
																		SELECT od_other_info, od_primary
																		FROM idk_product_otherdata
                                    WHERE od_group = :od_group
                                    ORDER BY od_other_info");

                              $select_query->execute(array(
                                ':od_group' => 2
                              ));

                              while ($select_row = $select_query->fetch()) {
                                echo "<option value='" . $select_row['od_other_info'] . "'";
                                if ($select_row['od_primary'] == 1) echo " selected";
                                else echo "";
                                echo " data-tokens='" . $select_row['od_other_info'] . "'>" . $select_row['od_other_info'] . "</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="product_tax" class="col-sm-3 control-label">Porez:</label>
                          <div class="col-sm-9">
                            <select class="selectpicker" id="product_tax" name="product_tax" data-live-search="true">
                              <?php
                              $select_query = $db->prepare("
																		SELECT od_data, od_value, od_primary
																		FROM idk_product_otherdata
                                    WHERE od_group = :od_group
                                    ORDER BY od_data");

                              $select_query->execute(array(
                                ':od_group' => 1
                              ));

                              while ($select_row = $select_query->fetch()) {
                                echo "<option value='" . $select_row['od_data'] . "," . $select_row['od_value'] . "'";
                                if ($select_row['od_primary'] == 1) echo " selected";
                                else echo "";
                                echo " data-tokens='" . $select_row['od_data'] . " - " . $select_row['od_value'] . "'>" . $select_row['od_data'] . " - " . $select_row['od_value'] . "%</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="product_unit" class="col-sm-3 control-label">Mjerna jedinica:</label>
                          <div class="col-sm-9">
                            <select class="selectpicker" id="product_unit" name="product_unit" data-live-search="true">
                              <?php
                              $select_query = $db->prepare("
																SELECT od_other_info, od_primary
																FROM idk_product_otherdata
                                WHERE od_group = :od_group
                                ORDER BY od_other_info");

                              $select_query->execute(array(
                                ':od_group' => 5
                              ));

                              while ($select_row = $select_query->fetch()) {
                                echo "<option value='" . $select_row['od_other_info'] . "'";
                                if ($select_row['od_primary'] == 1) echo " selected";
                                else echo "";
                                echo " data-tokens='" . $select_row['od_other_info'] . "'>" . $select_row['od_other_info'] . "</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>

                        <div class="form-group">
                          <label for="product_quantity" class="col-sm-3 control-label">Količina:</label>
                          <div class="col-sm-6">
                            <div class="materail-input-block materail-input-block_success">
                              <input class="form-control materail-input" type="number" name="product_quantity" id="product_quantity" placeholder="100">
                              <span class="materail-input-block__line"></span>
                            </div>
                          </div>
                          <div class="col-sm-3">
                            <select class="selectpicker" id="product_unit" name="product_unit">
                              <?php
                              $select_query = $db->prepare("
																SELECT od_other_info, od_primary
																FROM idk_product_otherdata
                                WHERE od_group = :od_group
                                ORDER BY od_other_info");

                              $select_query->execute(array(
                                ':od_group' => 5
                              ));

                              while ($select_row = $select_query->fetch()) {
                                echo "<option value='" . $select_row['od_other_info'] . "'";
                                if ($select_row['od_primary'] == 1) echo " selected";
                                else echo "";
                                echo ">" . $select_row['od_other_info'] . "</option>";
                              }
                              ?>
                            </select>
                          </div>
                        </div>

                        <div class="form-group idk_sku">
                          <label class="col-sm-3 control-label">SKU:</label>
                          <div class="col-sm-9" style="display: flex; align-items: center; justify-content: center;">
                            <div class="col-sm-5">
                              <p class="idk_sku_automatic idk_border_bottom_green">Automatski broj</p>
                            </div>
                            <div class="col-sm-2">
                              <div class="main-container__column materail-switch materail-switch_success">
                                <input class="materail-switch__element" type="checkbox" id="product_sku_checkbox" name="product_sku_checkbox" value="1">
                                <label class="materail-switch__label" for="product_sku_checkbox"></label>
                              </div>
                            </div>
                            <div class="col-sm-5"><input class="form-control materail-input" type="text" name="product_sku" id="product_sku" placeholder="Unesi SKU broj" disabled="disabled"></div>
                          </div>
                        </div>
                        <script>
                          jQuery(document).ready(function($) {
                            //reset
                            $("#product_sku_checkbox").click(function() {

                              $('#product_sku_checkbox').attr('checked', function(index, attr) {
                                return attr == 'checked' ? false : 'checked';
                              });

                              $('#product_sku').attr('disabled', function(index, attr) {
                                return attr == 'disabled' ? false : 'disabled';
                              });

                              $('#product_sku').val(null);

                              $('.idk_sku_automatic').toggleClass('idk_border_bottom_green').toggleClass('idk_border_bottom_muted').toggleClass('idk_text_muted');
                              $('#product_sku').toggleClass('idk_border_bottom_green');

                            });
                          });
                        </script>

                        <div class="form-group">
                          <label for="product_categories" class="col-sm-3 control-label"><span class="text-danger">*</span> Pripada kategorijama:</label>
                          <div class="col-sm-9">
                            <select class="selectpicker" id="product_categories" name="product_categories[]" multiple="multiple" data-live-search="true" required>
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

                        <div class="form-group">
                          <label class="col-sm-3 control-label">Izdvojeni proizvod:</label>
                          <div class="col-sm-9">
                            <div class="main-container__column materail-switch materail-switch_success">
                              <input class="materail-switch__element" type="checkbox" id="product_featured" name="product_featured" value="1">
                              <label class="materail-switch__label" for="product_featured"></label>
                            </div>
                          </div>
                        </div>
                        <br>

                        <!-- Add image -->
                        <div class="form-group">
                          <label for="product_image" class="col-sm-3 control-label">Primarna slika:</label>
                          <div class="col-sm-9">
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                              <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;"></div>
                              <div>
                                <span class="btn btn-default btn-file">
                                  <span class="fileinput-new">Izaberi sliku</span>
                                  <span class="fileinput-exists">Promijeni</span>
                                  <input type="file" name="product_image" id="product_image">
                                </span>
                                <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
                                <script>
                                  $(function() {
                                    $('#product_image').change(function() {

                                      var ext = $('#product_image').val().split('.').pop().toLowerCase();

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
                          <label class="col-sm-3"></label>
                          <div class="col-sm-9">
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

                        <div class="form-group">
                          <label for="product_desc" class="col-sm-3 control-label">Detaljan opis:</label>
                          <div class="col-sm-9">
                            <div class="form-group materail-input-block materail-input-block_success">
                              <textarea id="product_desc" class="form-control materail-input material-textarea" name="product_desc" placeholder="Opis proizvoda" rows="8"></textarea>
                              <span class="materail-input-block__line"></span>
                            </div>
                          </div>
                        </div>
                        <script>
                          $('#product_desc').trumbowyg({
                            lang: 'hr',
                            btns: [
                              ['undo', 'redo'],
                              ['formatting'],
                              ['strong', 'em', 'del'],
                              ['link'],
                              ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                              ['unorderedList', 'orderedList'],
                              ['horizontalRule'],
                              ['fullscreen']
                            ]
                          });
                        </script>
                        <br>

                        <!--<div class="form-group">
                              <label for="news_img" class="col-sm-2 control-label"><span class="text-danger">*</span> Slike:</label>

                              <div class="col-sm-10">
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                  <span class="btn btn-default btn-file"><span class="fileinput-new">Izaberite slike</span><span class="fileinput-exists">Promijenite</span><input name="photo_img[]" id="photo_img" multiple="multiple" type="file" /></span>
                                  <span class="fileinput-filenumber"></span>
                                  <ul class="list-inline" id="result" />
                                  
                                  <script>
                                    window.onload = function(){
                                      if(window.File && window.FileList && window.FileReader){
                                        var filesInput = document.getElementById("photo_img");
                                        filesInput.addEventListener("change", function(event){
                                          var files = event.target.files;
                                          var output = document.getElementById("result");
                                          var ul = document.getElementById("result");
                                          while (ul.hasChildNodes()) {
                                            ul.removeChild(ul.firstChild);
                                          }
                                          for(var i = 0; i< files.length; i++)
                                          {
                                            var file = files[i];
                                            if(!file.type.match('image'))
                                              continue;
                                            var picReader = new FileReader();
                                            picReader.addEventListener("load",function(event){
                                              var picFile = event.target;
                                              var li = document.createElement("li");
                                              li.innerHTML = "<img class='idk_thumbnail thumbnail' src='" + picFile.result + "' />";
                                              output.insertBefore(li,null);
                                            });
                                            picReader.readAsDataURL(file);
                                          }
                                        });
                                      }else{
                                        console.log("Your browser does not support File API");
                                      }
                                    }
                                  </script>
                                </div>
                              </div>
                            </div>
                            <hr> -->

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
                      <!-- End form - add product -->

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
           * 							EDIT PRODUCT
           * *********************************************************/
        case "edit":

          $product_id = $_GET['id'];

          //Check if product exists
          $check_query = $db->prepare("
						SELECT product_id
						FROM idk_product
						WHERE product_id = :product_id");

          $check_query->execute(array(
            ':product_id' => $product_id
          ));

          $number_of_rows = $check_query->rowCount();

          if ($number_of_rows == 1) {

            if ($getEmployeeStatus == 1) {

              // Get product data
              $query = $db->prepare("
								SELECT product_name, product_api_id, product_price, product_quantity, product_image, product_desc, product_currency, product_tax_name, product_tax_percentage, product_featured, product_sku, product_supplier
								FROM idk_product
								WHERE product_id = :product_id");

              $query->execute(array(
                ':product_id' => $product_id
              ));

              $product = $query->fetch();

              $product_api_id = $product['product_api_id'];
              $product_name = $product['product_name'];
              $product_price = $product['product_price'];
              $product_quantity = $product['product_quantity'];
              $product_desc = $product['product_desc'];
              $product_image = $product['product_image'];
              $product_currency = $product['product_currency'];
              $product_tax_name = $product['product_tax_name'];
              $product_tax_percentage = $product['product_tax_percentage'];
              $product_featured = $product['product_featured'];
              $product_sku = $product['product_sku'];
              $product_supplier = $product['product_supplier'];

              // Get all categories for product
              $query_cat = $db->prepare("
								SELECT category_id
								FROM idk_product_category
								WHERE product_id = :product_id");

              $query_cat->execute(array(
                ':product_id' => $product_id
              ));

              // define an empty array of categories
              $product_categories = array();

              // push category_ids from idk_product_category into product_categories
              while ($product_cat = $query_cat->fetch()) {
                $product_category = $product_cat['category_id'];
                array_push($product_categories, $product_category);
              }

            ?>

              <div class="row">
                <div class="col-xs-8">
                  <h1><i class="fa fa-tasks idk_color_green" aria-hidden="true"></i> Uredi proizvod</h1>
                </div>
                <div class="col-xs-4 text-right idk_margin_top10">
                  <a href="<?php getSiteUrl(); ?>idkadmin/products?page=list" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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

                        <!-- Form - edit product -->
                        <form id="idk_form" action="<?php getSiteUrl(); ?>idkadmin/do.php?form=edit_product" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">

                          <input type="hidden" name="product_id" value="<?php echo $product_id; ?>" />
                          <input type="hidden" name="table_page" id="table_page" value="<?php echo $table_page; ?>">

                          <div class="form-group">
                            <label for="product_name" class="col-sm-3 control-label"><span class="text-danger">*</span> Naziv:</label>
                            <div class="col-sm-9">
                              <div class="materail-input-block materail-input-block_success">
                                <input class="form-control materail-input" type="text" name="product_name" id="product_name" placeholder="Naziv" value="<?php echo $product_name; ?>" required>
                                <span class="materail-input-block__line"></span>
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="product_api_id" class="col-sm-3 control-label"><span class="text-danger">*</span> API ID:</label>
                            <div class="col-sm-9">
                              <div class="materail-input-block materail-input-block_success">
                                <input class="form-control materail-input" type="text" name="product_api_id" id="product_api_id" placeholder="API ID proizvoda" value="<?php echo $product_api_id; ?>" required>
                                <span class="materail-input-block__line"></span>
                              </div>
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="product_supplier" class="col-sm-3 control-label">Dobavljač:</label>
                            <div class="col-sm-9">
                              <select class="selectpicker" id="product_supplier" name="product_supplier" data-live-search="true">
                                <option value=""></option>
                                <?php
                                $select_query = $db->prepare("
																		SELECT supplier_name
																		FROM idk_supplier
                                    ORDER BY supplier_name");

                                $select_query->execute(array(
                                  ':od_group' => 1
                                ));

                                while ($select_row = $select_query->fetch()) {
                                  echo "<option value='" . $select_row['supplier_name'] . "' data-tokens='" . $select_row['supplier_name'] . "'";
                                  if ($select_row['supplier_name'] == $product_supplier) echo " selected";
                                  else echo "";
                                  echo ">" . $select_row['supplier_name'] . "</option>";
                                }
                                ?>
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="product_price" class="col-sm-3 control-label">Cijena:</label>
                            <div class="col-sm-6">
                              <div class="materail-input-block materail-input-block_success">
                                <input class="form-control materail-input" type="number" name="product_price" id="product_price" placeholder="10.000" min="0" step="0.001" value="<?php echo $product_price; ?>">
                                <span class="materail-input-block__line"></span>
                              </div>
                            </div>
                            <div class="col-sm-3">
                              <select class="selectpicker" id="product_currency" name="product_currency" data-live-search="true">
                                <?php
                                $select_query = $db->prepare("
																	SELECT od_other_info
																	FROM idk_product_otherdata
                                  WHERE od_group = :od_group
                                  ORDER BY od_other_info");

                                $select_query->execute(array(
                                  ':od_group' => 2
                                ));

                                while ($select_row = $select_query->fetch()) {
                                  echo "<option value='" . $select_row['od_other_info'] . "'";
                                  if ($select_row['od_other_info'] == $product_currency) echo " selected";
                                  else echo "";
                                  echo " data-tokens='" . $select_row['od_other_info'] . "'>" . $select_row['od_other_info'] . "</option>";
                                }
                                ?>
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="product_tax" class="col-sm-3 control-label">Porez:</label>
                            <div class="col-sm-9">
                              <select class="selectpicker" id="product_tax" name="product_tax" data-live-search="true">
                                <?php
                                $select_query = $db->prepare("
																	SELECT od_data, od_value
																	FROM idk_product_otherdata
                                  WHERE od_group = :od_group
                                  ORDER BY od_data");

                                $select_query->execute(array(
                                  ':od_group' => 1
                                ));

                                while ($select_row = $select_query->fetch()) {
                                  echo "<option value='" . $select_row['od_data'] . "," . $select_row['od_value'] . "'";
                                  if ($select_row['od_data'] == $product_tax_name) echo " selected";
                                  else echo "";
                                  echo " data-tokens='" . $select_row['od_data'] . " - " . $select_row['od_value'] . "'>" . $select_row['od_data'] . " - " . $select_row['od_value'] . "%</option>";
                                }
                                ?>
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label for="product_quantity" class="col-sm-3 control-label">Količina:</label>
                            <div class="col-sm-6">
                              <div class="materail-input-block materail-input-block_success">
                                <input class="form-control materail-input" type="number" name="product_quantity" id="product_quantity" placeholder="100" value="<?php echo $product_quantity; ?>">
                                <span class="materail-input-block__line"></span>
                              </div>
                            </div>
                            <div class="col-sm-3">
                              <select class="selectpicker" id="product_unit" name="product_unit">
                                <?php
                                $select_query = $db->prepare("
																	SELECT od_other_info
																	FROM idk_product_otherdata
                                  WHERE od_group = :od_group
                                  ORDER BY od_other_info");

                                $select_query->execute(array(
                                  ':od_group' => 5
                                ));

                                while ($select_row = $select_query->fetch()) {
                                  echo "<option value='" . $select_row['od_other_info'] . "'";
                                  if ($select_row['od_other_info'] == $product_currency) echo " selected";
                                  else echo "";
                                  echo ">" . $select_row['od_other_info'] . "</option>";
                                }
                                ?>
                              </select>
                            </div>
                          </div>

                          <div class="form-group idk_sku">
                            <label class="col-sm-3 control-label">SKU:</label>
                            <div class="col-sm-9" style="display: flex; align-items: center; justify-content: center;">
                              <div class="col-sm-5">
                                <p class="idk_sku_automatic <?php if ($product_sku) {
                                                              echo "idk_border_bottom_muted idk_text_muted";
                                                            } ?>">
                                  Automatski broj</p>
                              </div>
                              <div class="col-sm-2">
                                <div class="main-container__column materail-switch materail-switch_success">
                                  <input class="materail-switch__element" type="checkbox" id="product_sku_checkbox" name="product_sku_checkbox" value="1" <?php if ($product_sku) {
                                                                                                                                                            echo "checked='checked'";
                                                                                                                                                          } ?>>
                                  <label class="materail-switch__label" for="product_sku_checkbox"></label>
                                </div>
                              </div>
                              <div class="col-sm-5"><input class="form-control materail-input <?php if ($product_sku) {
                                                                                                echo "idk_border_bottom_green";
                                                                                              } ?>" type="text" name="product_sku" id="product_sku" placeholder="Unesi SKU broj" <?php if (!$product_sku) {
                                                                                                                                                                                    echo "disabled='disabled'";
                                                                                                                                                                                  } ?> value="<?php echo $product_sku; ?>">
                              </div>
                            </div>
                          </div>
                          <script>
                            jQuery(document).ready(function($) {
                              //reset
                              $("#product_sku_checkbox").click(function() {

                                $('#product_sku_checkbox').attr('checked', function(index, attr) {
                                  return attr == 'checked' ? false : 'checked';
                                });

                                $('#product_sku').attr('disabled', function(index, attr) {
                                  return attr == 'disabled' ? false : 'disabled';
                                });

                                $('#product_sku').val(null);

                                $('.idk_sku_automatic').toggleClass('idk_border_bottom_green').toggleClass('idk_border_bottom_muted').toggleClass('idk_text_muted');
                                $('#product_sku').toggleClass('idk_border_bottom_green');

                              });
                            });
                          </script>

                          <div class="form-group">
                            <label for="product_categories" class="col-sm-3 control-label"><span class="text-danger">*</span> Pripada kategorijama:</label>
                            <div class="col-sm-9">
                              <select class="selectpicker" id="product_categories" name="product_categories[]" multiple="multiple" data-live-search="true" required>
                                <?php
                                $select_query = $db->prepare("
                                  SELECT category_id, category_name
                                  FROM idk_category
                                  ORDER BY category_name");

                                $select_query->execute();

                                while ($select_row = $select_query->fetch()) {
                                  echo "<option value='" . $select_row['category_id'] . "'";
                                  if (in_array($select_row['category_id'], $product_categories)) echo " selected";
                                  else echo "";
                                  echo " data-tokens='" . $select_row['category_name'] . "'>" . $select_row['category_name'] . "</option>";
                                }
                                ?>
                              </select>
                            </div>
                          </div>

                          <div class="form-group">
                            <label class="col-sm-3 control-label">Izdvojeni proizvod:</label>
                            <div class="col-sm-9">
                              <div class="main-container__column materail-switch materail-switch_success">
                                <input class="materail-switch__element" type="checkbox" id="product_featured" name="product_featured" value="1" <?php if ($product_featured == 1) {
                                                                                                                                                  echo "checked='checked'";
                                                                                                                                                } ?>>
                                <label class="materail-switch__label" for="product_featured"></label>
                              </div>
                            </div>
                          </div>
                          <script>
                            jQuery(document).ready(function($) {
                              //reset
                              $("#product_featured").click(function() {

                                $('#product_featured').attr('checked', function(index, attr) {
                                  return attr == 'checked' ? false : 'checked';
                                });

                              });
                            });
                          </script>
                          <br>

                          <!-- Add image -->
                          <div class="form-group">
                            <label for="product_image" class="col-sm-3 control-label">Primarna slika:</label>
                            <div class="col-sm-9">
                              <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-preview thumbnail" data-trigger="fileinput" style="width: 160px; height: 160px;">
                                  <?php if ($product_image) { ?>
                                    <img src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>">
                                  <?php } ?>
                                </div>
                                <input type="hidden" name="product_image_url" value="<?php echo $product_image; ?>" />
                                <div>
                                  <span class="btn btn-default btn-file"><span class="fileinput-new">Izaberi
                                      sliku</span><span class="fileinput-exists">Promijeni</span><input type="file" name="product_image" id="product_image"></span>
                                  <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Ukloni</a>
                                  <script>
                                    $(function() {
                                      $('#product_image').change(function() {

                                        var ext = $('#product_image').val().split('.').pop().toLowerCase();

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
                            <label class="col-sm-3"></label>
                            <div class="col-sm-9">
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

                          <div class="form-group">
                            <label for="product_desc" class="col-sm-3 control-label">Opis proizvoda:</label>
                            <div class="col-sm-9">
                              <div class="form-group materail-input-block materail-input-block_success">
                                <textarea id="product_desc" class="form-control materail-input material-textarea" name="product_desc" placeholder="Opis proizvoda" rows="8"><?php echo $product_desc; ?></textarea>
                                <span class="materail-input-block__line"></span>
                              </div>
                            </div>
                          </div>
                          <script>
                            $('#product_desc').trumbowyg({
                              lang: 'hr',
                              btns: [
                                ['undo', 'redo'],
                                ['formatting'],
                                ['strong', 'em', 'del'],
                                ['link'],
                                ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                                ['unorderedList', 'orderedList'],
                                ['horizontalRule'],
                                ['fullscreen']
                              ]
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
                        <!-- End form - edit product -->

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
            header("Location: products?page=list&mess=5");
          }

          break;



          /************************************************************
           * 							OPEN SINGLE PRODUCT 
           * *********************************************************/
        case "open":

          $product_id = $_GET['id'];

          //Check if product exists
          $check_query = $db->prepare("
            SELECT product_id
            FROM idk_product
            WHERE product_id = :product_id");

          $check_query->execute(array(
            ':product_id' => $product_id
          ));

          $number_of_rows = $check_query->rowCount();

          if ($number_of_rows == 1) {

            $query = $db->prepare("
              SELECT product_name, product_api_id, product_price, product_quantity, product_image, product_desc, product_currency, product_tax_name, product_tax_percentage, product_unit, product_sku, product_supplier
              FROM idk_product
              WHERE product_id = :product_id");

            $query->execute(array(
              ':product_id' => $product_id
            ));

            $product = $query->fetch();

            $product_api_id = $product['product_api_id'];
            $product_name = $product['product_name'];
            $product_price = $product['product_price'];
            $product_quantity = $product['product_quantity'];
            $product_desc = $product['product_desc'];
            $product_image = $product['product_image'];
            $product_currency = $product['product_currency'];
            $product_tax_name = $product['product_tax_name'];
            $product_tax_percentage = $product['product_tax_percentage'];
            $product_unit = $product['product_unit'];
            $product_sku = $product['product_sku'];
            $product_supplier = $product['product_supplier'];

            // Get all categories for product
            $query_cat = $db->prepare("
              SELECT category_id
              FROM idk_product_category
              WHERE product_id = :product_id");

            $query_cat->execute(array(
              ':product_id' => $product_id
            ));

            // define an empty array of categories
            $product_categories = array();

            // push category_ids from idk_product_category into product_categories
            while ($product_cat = $query_cat->fetch()) {
              $product_category = $product_cat['category_id'];
              array_push($product_categories, $product_category);
            }

            ?>

            <div class="row">
              <div class="col-xs-8">
                <h1>
                  <?php if ($product_image) { ?>
                    <a class="fancybox" rel="group" href="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>">
                      <img class="idk_profile_img" src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>">
                    </a>
                  <?php } ?>
                  <?php echo $product_name; ?>
                </h1>
              </div>
              <div class="col-xs-4 text-right idk_margin_top10">
                <a href="<?php getSiteUrl(); ?>idkadmin/products?page=list&table_page=<?php echo $table_page; ?>" class="btn material-btn material-btn-icon-primary material-btn_primary main-container__column material-btn-icon-responsive"><i class="fa fa-chevron-left" aria-hidden="true"></i> <span>Povratak</span></a>
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
                      if (isset($_GET['mess'])) {
                        $mess = $_GET['mess'];
                      } else {
                        $mess = 0;
                      }

                      if ($mess == 1) {
                        echo '<div class="alert material-alert material-alert_success">Uspješno ste uredili proizvod.</div>';
                      } ?>
                    </div>
                  </div>

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
                            <div class="row">
                              <div class="col-sm-9">
                                <h5>Osnovne informacije</h5>
                              </div>
                              <div class="col-sm-3 text-right">
                                <a href="products?page=edit&id=<?php echo $product_id; ?>&table_page=<?php echo $table_page; ?>" class="btn material-btn material-btn-icon-success material-btn_success main-container__column">
                                  <i class="fa fa-pencil" aria-hidden="true"></i> <span></span>
                                </a>
                              </div>
                            </div>

                            <input type="hidden" name="table_page" id="table_page" value="<?php echo $table_page; ?>">

                            <!-- Get basic information -->
                            <div class="row">
                              <strong class="col-sm-4 text-right">Naziv:</strong>
                              <div class="col-sm-8"><?php echo $product_name; ?></div>
                            </div>
                            <div class="row">
                              <strong class="col-sm-4 text-right">API ID:</strong>
                              <div class="col-sm-8"><?php echo $product_api_id; ?></div>
                            </div>
                            <div class="row">
                              <strong class="col-sm-4 text-right">SKU broj:</strong>
                              <div class="col-sm-8"><?php echo $product_sku; ?></div>
                            </div>
                            <div class="row">
                              <strong class="col-sm-4 text-right">Dobavljač:</strong>
                              <div class="col-sm-8"><?php echo $product_supplier; ?></div>
                            </div>
                            <div class="row">
                              <strong class="col-sm-4 text-right">Cijena:</strong>
                              <div class="col-sm-8"><?php echo number_format($product_price, 3, ',', '.') . " " . $product_currency; ?></div>
                            </div>
                            <div class="row">
                              <strong class="col-sm-4 text-right">Porez:</strong>
                              <div class="col-sm-8"><?php echo "${product_tax_name} - ${product_tax_percentage}%"; ?></div>
                            </div>
                            <div class="row">
                              <strong class="col-sm-4 text-right">Opis proizvoda:</strong>
                              <div class="col-sm-8"><?php echo $product_desc; ?>
                              </div>
                            </div>
                            <div class="row">
                              <strong class="col-sm-4 text-right">Kategorije:</strong>
                              <div class="col-sm-8">
                                <?php
                                $select_query = $db->prepare("
                                  SELECT category_id, category_name
                                  FROM idk_category
                                  ORDER BY category_name");

                                $select_query->execute();

                                while ($select_row = $select_query->fetch()) {
                                  if (in_array($select_row['category_id'], $product_categories)) {
                                    echo $select_row['category_name'] . "<br>";
                                  }
                                } ?>
                              </div>
                            </div>
                          </div>

                          <div class="col-md-6">
                            <div class="row">
                              <div class="col-sm-12">
                                <?php if ($product_image) { ?>
                                  <img class="img-responsive" src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>">
                                <?php } ?>
                              </div>
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
          } else {
            header("Location: products?page=list&mess=6");
          }
          break;



          /************************************************************
           * 							ARCHIVE
           * *********************************************************/
        case "archive":

          if ($getEmployeeStatus == 1) {

            $product_id = $_GET['id'];

            //Get product name
            $query_select = $db->prepare("
							SELECT product_name
							FROM idk_product
							WHERE product_id = :product_id");

            $query_select->execute(array(
              ':product_id' => $product_id
            ));

            $product_select = $query_select->fetch();

            $product_name = $product_select['product_name'];

            //Save
            $query = $db->prepare("
              UPDATE idk_product
              SET product_active = :product_active
              WHERE product_id = :product_id");

            $query->execute(array(
              ':product_active' => 0,
              ':product_id' => $product_id
            ));

            //Delete product from all lists
            $query = $db->prepare("
              DELETE FROM idk_product_list
              WHERE product_id = :product_id");

            $query->execute(array(
              ':product_id' => $product_id
            ));

            //Add to log
            $log_desc = "Arhivirao proizvod: ${product_name}";
            $log_date = date('Y-m-d H:i:s');
            addLog($logged_employee_id, $log_desc, $log_date);

            header("Location: " . getSiteUrlr() . "idkadmin/products?page=list&mess=4&table_page=${table_page}");
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