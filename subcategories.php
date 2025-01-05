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

        <!-- Get main cat id from url -->
        <?php
        if (isset($_GET['main_category_id'])) {
            $main_category_id = $_GET['main_category_id'];
        } else {
            $main_category_id = 0;
        }

        $main_category_query = $db->prepare("
            SELECT category_id, category_name
            FROM idk_category
            WHERE category_id = :main_category_id");

        $main_category_query->execute(array(
            ':main_category_id' => $main_category_id
        ));

        $main_category = $main_category_query->fetch();
        $main_category_name = $main_category['category_name'];

        ?>

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
                            }
                            ?>

                            <div class="col-12">
                                <h1 class="idk_page_title"><?php echo $main_category_name; ?></h1>
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
                        WHERE category_sub = :category_sub
					    ORDER BY category_name");

                    $categories_query->execute(array(
                        ':category_sub' => $main_category_id
                    ));

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

        <!-- Category products section -->
        <section class="idk_products_cards_section">
            <div class="container">
                <div class="row align-items-center justify-content-center justify-content-sm-start">

                    <!-- Get data for products -->
                    <?php
                    $query = $db->prepare("
                        SELECT t1.product_id, t1.product_name, t1.product_sku, t1.product_price, t1.product_image, t1.product_currency, t1.product_quantity, t1.product_unit, t1.product_tax_name, t1.product_tax_percentage
                        FROM idk_product t1
						INNER JOIN idk_product_category t2 ON t1.product_id = t2.product_id
                        WHERE t1.product_active = 1 AND t2.category_id = :main_category_id
                        ORDER BY t1.product_name");

                    $query->execute(array(
                        ':main_category_id' => $main_category_id
                    ));

                    while ($product = $query->fetch()) {

                        $product_id = $product['product_id'];
                        $product_name = $product['product_name'];
                        $product_sku = $product['product_sku'];
                        $product_price = $product['product_price'];
                        $product_image = $product['product_image'];
                        $product_currency = $product['product_currency'];
                        $product_quantity = $product['product_quantity'];
                        $product_unit = $product['product_unit'];
                        $product_tax_name = $product['product_tax_name'];
                        $product_tax_percentage = $product['product_tax_percentage'];

                    ?>

                        <div class="col-10 col-sm-6 col-md-4">
                            <div class="card">
                                <a href="<?php getSiteUrl(); ?>product?id=<?php echo $product_id; ?>">
                                    <img class="card-img-top" src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>" alt="<?php echo $product_name; ?> slika">
                                </a>

                                <!-- Button trigger modal -->
                                <button type="button" class="btn idk_add_to_list_btn" data-toggle="modal" data-target="#addToListModal_<?php echo $product_id; ?>">
                                    <span class="lnr lnr-heart"></span>
                                </button>

                                <!-- Modal - add to list -->
                                <div class="modal fade" id="addToListModal_<?php echo $product_id; ?>" tabindex="-1" role="dialog" aria-labelledby="addToListModal_<?php echo $product_id; ?>Label" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addToListModal_<?php echo $product_id; ?>Label">Dodaj u listu</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">

                                                <!-- Form - add item to list from index -->
                                                <form action="<?php getSiteUrl(); ?>do.php?form=add_item_to_list" method="POST">

                                                    <input type="hidden" name="page" id="page" value="subcategories">
                                                    <input type="hidden" name="product_id" id="product_id" value="<?php echo $product_id; ?>">
                                                    <input type="hidden" name="product_unit" id="product_unit" value="<?php echo $product_unit; ?>">
                                                    <input type="hidden" name="main_category_id" id="main_category_id" value="<?php echo $main_category_id; ?>">

                                                    <div class="form-group">
                                                        <label class="sr-only" for="selectList_<?php echo $product_id; ?>">Izaberi listu*</label>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-prepend">
                                                                <label class="input-group-text" for="selectList_<?php echo $product_id; ?>"><span class="lnr lnr-heart"></span></label>
                                                            </div>
                                                            <select class="custom-select" name="list_id" id="selectList_<?php echo $product_id; ?>" required>

                                                                <option value="">Izaberi listu ...</option>

                                                                <!-- Get client's lists from db -->
                                                                <?php
                                                                if (isset($logged_client_id) and $logged_client_id != 0) {

                                                                    $list_query = $db->prepare("
                                                                        SELECT list_id, list_name
                                                                        FROM idk_list
                                                                        WHERE client_id = :client_id AND employee_id IS NULL");

                                                                    $list_query->execute(array(
                                                                        ':client_id' => $logged_client_id
                                                                    ));
                                                                } elseif (isset($_COOKIE['idk_session_front_client'])) {

                                                                    $list_query = $db->prepare("
                                                                        SELECT list_id, list_name
                                                                        FROM idk_list
                                                                        WHERE client_id = :client_id AND employee_id IS NOT NULL");

                                                                    $list_query->execute(array(
                                                                        ':client_id' => $_COOKIE['idk_session_front_client']
                                                                    ));
                                                                }

                                                                while ($list = $list_query->fetch()) {

                                                                    $list_id = $list['list_id'];
                                                                    $list_name = $list['list_name'];

                                                                ?>

                                                                    <option value="<?php echo $list_id; ?>">
                                                                        <?php echo $list_name; ?>
                                                                    </option>
                                                                <?php } ?>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="sr-only" for="product_on_list_quantity_<?php echo $product_id; ?>">Količina*</label>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text"><span class="lnr lnr-layers"></span></div>
                                                            </div>
                                                            <input type="number" class="form-control" name="product_on_list_quantity" id="product_on_list_quantity_<?php echo $product_id; ?>" min="0" placeholder="Količina (<?php echo $product_unit; ?>)*" required>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn idk_btn btn-block">DODAJ</button>
                                                </form><!-- End form - add item to list from index -->

                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- End modal - add to list -->

                                <div class="card-body">
                                    <a href="<?php getSiteUrl(); ?>product?id=<?php echo $product_id; ?>">
                                        <h5 class="card-title"><?php echo $product_name; ?></h5>
                                        <h5 class="card-title"><small>Šifra: <?php echo $product_sku; ?></small></h5>
                                        <?php if (isset($logged_client_show_quantity) and $logged_client_show_quantity == 0) {
                                        } else { ?>
                                            <h5 class="card-title"><small>Stanje: <?php echo $product_quantity . " " . $product_unit; ?></small></h5>
                                        <?php } ?>
                                    </a>
                                    <div class="idk_product_card_bottom_row">
                                        <div class="idk_product_card_price">
                                            <?php if (isset($logged_client_show_price) and $logged_client_show_price == 0) {
                                            } else { ?>
                                                <p class="card-text">VPC: <?php echo number_format($product_price, 3, ',', '.') . " " . $product_currency; ?></p>
                                            <?php } ?>
                                        </div>
                                        <div class="idk_product_card_add_to_cart">

                                            <!-- Button trigger modal - add to cart -->
                                            <button type="button" class="btn idk_add_to_cart_btn" data-toggle="modal" data-target="#addToCartModal_<?php echo $product_id; ?>">
                                                <span class="lnr lnr-cart"></span>
                                            </button>

                                            <!-- Modal - add to cart -->
                                            <div class="modal fade" id="addToCartModal_<?php echo $product_id; ?>" tabindex="-1" role="dialog" aria-labelledby="addToCartModal_<?php echo $product_id; ?>Label" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="addToCartModal_<?php echo $product_id; ?>Label">Dodaj u košaricu</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body">

                                                            <?php
                                                            if (isset($logged_client_id) and $logged_client_id != 0) {
                                                                $client_id = $logged_client_id;
                                                            } else {
                                                                $client_id = $_COOKIE['idk_session_front_client'];
                                                            }

                                                            if (isset($logged_employee_id) and $logged_employee_id != 0) {
                                                                //Check if product temp order exists
                                                                $check_query = $db->prepare("
                                                                  SELECT product_id
                                                                  FROM idk_product_order_temp
                                                                  WHERE order_id IN (SELECT order_id FROM idk_order_temp WHERE client_id = :client_id AND employee_id = :employee_id) AND product_id = :product_id");

                                                                $check_query->execute(array(
                                                                    ':client_id' => $client_id,
                                                                    ':product_id' => $product_id,
                                                                    ':employee_id' => $logged_employee_id
                                                                ));
                                                            } else {
                                                                //Check if product temp order exists
                                                                $check_query = $db->prepare("
                                                                  SELECT product_id
                                                                  FROM idk_product_order_temp
                                                                  WHERE order_id IN (SELECT order_id FROM idk_order_temp WHERE client_id = :client_id AND employee_id IS NULL) AND product_id = :product_id");

                                                                $check_query->execute(array(
                                                                    ':client_id' => $client_id,
                                                                    ':product_id' => $product_id
                                                                ));
                                                            }

                                                            $number_of_rows = $check_query->rowCount();

                                                            if ($number_of_rows == 0) {

                                                            ?>

                                                                <!-- Form - add item to cart temp from subcategories -->
                                                                <form action="<?php getSiteUrl(); ?>do.php?form=add_item_to_cart_temp" method="post">

                                                                    <input type="hidden" name="page" id="page" value="subcategories">
                                                                    <input type="hidden" name="product_id" id="product_id" value="<?php echo $product_id; ?>">
                                                                    <input type="hidden" name="product_name" id="product_name" value="<?php echo $product_name; ?>">
                                                                    <input type="hidden" name="product_quantity" id="product_quantity" value="<?php echo $product_quantity; ?>">
                                                                    <input type="hidden" name="product_currency" id="product_currency" value="<?php echo $product_currency; ?>">
                                                                    <input type="hidden" name="product_unit" id="product_unit" value="<?php echo $product_unit; ?>">
                                                                    <input type="hidden" name="product_price" id="product_price" value="<?php echo $product_price; ?>">
                                                                    <input type="hidden" name="product_tax_name" id="product_tax_name" value="<?php echo $product_tax_name; ?>">
                                                                    <input type="hidden" name="product_tax_percentage" id="product_tax_percentage" value="<?php echo $product_tax_percentage; ?>">
                                                                    <input type="hidden" name="main_category_id" id="main_category_id" value="<?php echo $main_category_id; ?>">

                                                                    <div class="form-group">
                                                                        <label class="sr-only" for="product_in_cart_temp_quantity">Količina*</label>
                                                                        <div class="input-group mb-2">
                                                                            <div class="input-group-prepend">
                                                                                <div class="input-group-text"><span class="lnr lnr-layers"></span></div>
                                                                            </div>
                                                                            <input type="number" class="form-control" name="product_in_cart_temp_quantity" id="product_in_cart_temp_quantity" min="0" placeholder="Količina (<?php echo $product_unit; ?>)*" required>
                                                                        </div>
                                                                    </div>

                                                                    <button type="submit" class="btn idk_btn btn-block">DODAJ</button>
                                                                </form><!-- End form - add item to cart temp from subcategories -->

                                                            <?php } else { ?>
                                                                <h4 class="my-3">Proizvod je već dodan u košaricu!</h4>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> <!-- End add to cart modal -->

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </section> <!-- End category products section -->

    </main> <!-- End main -->

    <!-- Foot bar -->
    <?php include('includes/foot_bar.php'); ?>

    <!-- foot.php -->
    <?php include('includes/foot.php'); ?>

</body>

</html>