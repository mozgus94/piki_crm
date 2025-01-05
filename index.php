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

    <div class="container-fluid p-0">
      <div class="row">
        <div class="col-12">
          <div class="idk_main_slider">

            <!-- Get data for products for main slider -->
            <?php
            $query = $db->prepare("
							SELECT product_id, product_name, product_image
							FROM idk_product
              WHERE product_active = 1 AND product_featured = 1
              ORDER BY updated_at DESC
              LIMIT 4");

            $query->execute();

            while ($product = $query->fetch()) {

              $product_id = $product['product_id'];
              $product_name = $product['product_name'];
              $product_image = $product['product_image'];

            ?>
              <div>
                <img src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>" alt="<?php echo $product_name; ?> slika">
                <div class="idk_main_slider_overlay"></div>
                <div class="idk_main_slider_text">
                  <div class="container">
                    <div class="row">
                      <div class="col-8">
                        <a href="<?php getSiteUrl(); ?>product?id=<?php echo $product_id; ?>">
                          <h2><?php echo $product_name; ?></h2>
                        </a>
                      </div>
                      <div class="col-4">
                        <button class="idk_main_slider_right_arrow next btn">
                          <span class="lnr lnr-chevron-right"></span>
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            <?php } ?>

          </div>
        </div>
      </div>
    </div>

  </header> <!-- End header -->

  <!-- Main -->
  <main>

    <!-- Categories cards section -->
    <section id="idk_categories_cards_section">
      <div class="container">
        <div class="row align-items-center">

          <?php
          if (isset($_GET['mess'])) {
            $mess = $_GET['mess'];
          } else {
            $mess = 0;
          }

          if ($mess == 1) {
            echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success mb-5">Uspješno ste dodali proizvod u listu.</div></div>';
          } elseif ($mess == 2) {
            echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_danger mb-5">Greška: Forma nije pravilno popunjena!</div></div>';
          } elseif ($mess == 3) {
            echo '<div class="col-12 mb-1">
                      <div class="alert material-alert material-alert_success mb-5">Hvala! Uspješno ste dodali proizvod u košaricu.</div>
                  </div>
                  <div class="col-4 mb-5">
                      <a href="' . getSiteUrlr() . 'categories" title="Nastavi kupovinu">
                          <button type="button" class="btn idk_after_adding_to_cart_btn">
                              <span class="lnr lnr-store"></span>
                          </button> 
                      </a>
                  </div>
                  <div class="col-4 mb-5">
                      <a href="' . getSiteUrlr() . '" title="Nazad na početnu">
                          <button type="button" class="btn idk_after_adding_to_cart_btn">
                              <span class="lnr lnr-home"></span>
                          </button> 
                      </a>
                  </div>
                  <div class="col-4 mb-5">
                      <a href="' . getSiteUrlr() . 'cart" title="Pregledaj košaricu">
                          <button type="button" class="btn idk_after_adding_to_cart_btn">
                              <span class="lnr lnr-cart"></span>
                          </button> 
                      </a>
                  </div>';
          } elseif ($mess == 4) {
            echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success mb-5">Hvala! Vaša narudžba je zaprimljena.</div></div>';
          } elseif ($mess == 5) {
            echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success mb-5">Hvala! Uspješno ste kreirali ponudu.</div></div>';
          }
          ?>

          <!-- Get categories from db -->
          <?php
          $categories_query = $db->prepare("
						SELECT category_id, category_name, category_image
            FROM idk_category
            WHERE category_sub = 0
						ORDER BY category_name");

          $categories_query->execute();

          while ($category = $categories_query->fetch()) {

            $category_id = $category['category_id'];
            $category_name = $category['category_name'];
            $category_image = $category['category_image'];
            if (!isset($category_image)) {
              $category_image = "none.jpg";
            }

          ?>

            <div class="col-6 col-md-4">
              <a href="<?php getSiteUrl(); ?>subcategories?main_category_id=<?php echo $category_id; ?>">
                <div class="card">
                  <img class="card-img-top" src="<?php getSiteUrl(); ?>idkadmin/files/categorys/images/<?php echo $category_image; ?>" alt="<?php echo $category_name; ?> slika">
                  <div class="card-body">
                    <h5 class="card-title"><?php echo $category_name; ?></h5>
                  </div>
                </div>
              </a>
            </div>

          <?php } ?>
        </div>
      </div>
    </section> <!-- End categories cards section -->

  </main> <!-- End main -->

  <!-- Foot bar -->
  <?php include('includes/foot_bar.php'); ?>

  <!-- foot.php -->
  <?php include('includes/foot.php'); ?>

</body>

</html>