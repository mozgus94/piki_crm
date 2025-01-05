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

    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <div class="container idk_page_title_container">
            <div class="row align-items-center">
              <div class="col-12">
                <h1 class="idk_page_title">Kategorije</h1>
              </div>
            </div>
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