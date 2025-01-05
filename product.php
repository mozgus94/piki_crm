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



        <!-- /************************************************************
    * 							OPEN SINGLE PRODUCT 
    * *********************************************************/ -->
        <?php
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
                SELECT product_name, product_price, product_image, product_desc, product_currency, product_quantity, product_unit, product_tax_name, product_tax_percentage
                FROM idk_product
                WHERE product_id = :product_id");

            $query->execute(array(
                ':product_id' => $product_id
            ));

            $product = $query->fetch();

            $product_name = $product['product_name'];
            $product_price = $product['product_price'];
            $product_desc = $product['product_desc'];
            $product_image = $product['product_image'];
            $product_currency = $product['product_currency'];
            $product_quantity = $product['product_quantity'];
            $product_unit = $product['product_unit'];
            $product_tax_name = $product['product_tax_name'];
            $product_tax_percentage = $product['product_tax_percentage'];

        ?>

            <!-- Product image section -->
            <section class="idk_product_image_section">
                <div class="container-fluid">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-12 p-0">
                            <a data-fancybox="gallery" href="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>"><img src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>" class="img-fluid" alt="<?php echo $product_name; ?> slika"></a>
                        </div>
                    </div>
                </div>
            </section> <!-- End product image section -->

            <!-- Product title section -->
            <section class="idk_product_title_section">
                <div class="container-fluid">
                    <div class="row justify-content-center align-items-center">
                        <div class="col-12 p-0">
                            <div class="idk_product_cart_list_btns">

                                <!-- Button trigger modal - add to cart -->
                                <button type="button" class="btn idk_add_to_cart_btn" data-toggle="modal" data-target="#addToCartModal_<?php echo $product_id; ?>">
                                    <span class="lnr lnr-cart"></span>
                                </button>

                                <!-- Button trigger modal add to list -->
                                <button type="button" class="btn idk_add_to_list_btn" data-toggle="modal" data-target="#addToListModal">
                                    <span class="lnr lnr-heart"></span>
                                </button>

                                <!-- Modal - add to list -->
                                <div class="modal fade" id="addToListModal" tabindex="-1" role="dialog" aria-labelledby="addToListModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addToListModalLabel">Dodaj u listu</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="<?php getSiteUrl(); ?>do.php?form=add_item_to_list" method="post">

                                                    <input type="hidden" name="page" id="page" value="product">
                                                    <input type="hidden" name="product_id" id="product_id" value="<?php echo $product_id; ?>">
                                                    <input type="hidden" name="product_unit" id="product_unit" value="<?php echo $product_unit; ?>">

                                                    <div class="form-group">
                                                        <label class="sr-only" for="selectList">Izaberi listu*</label>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-prepend">
                                                                <label class="input-group-text" for="selectList"><span class="lnr lnr-heart"></span></label>
                                                            </div>
                                                            <select class="custom-select" name="list_id" id="selectList" required>

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
                                                        <label class="sr-only" for="product_on_list_quantity">Količina*</label>
                                                        <div class="input-group mb-2">
                                                            <div class="input-group-prepend">
                                                                <div class="input-group-text"><span class="lnr lnr-layers"></span></div>
                                                            </div>
                                                            <input type="number" class="form-control" name="product_on_list_quantity" id="product_on_list_quantity" min="0" placeholder="Količina (<?php echo $product_unit; ?>)*" required>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn idk_btn btn-block">DODAJ</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- End add to list modal -->


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

                                                    <!-- Form - add item to cart temp -->
                                                    <form action="<?php getSiteUrl(); ?>do.php?form=add_item_to_cart_temp" method="post">

                                                        <input type="hidden" name="page" id="page_<?php echo $product_id; ?>" value="product">
                                                        <input type="hidden" name="product_id" id="product_id_<?php echo $product_id; ?>" value="<?php echo $product_id; ?>">
                                                        <input type="hidden" name="product_name" id="product_name_<?php echo $product_id; ?>" value="<?php echo $product_name; ?>">
                                                        <input type="hidden" name="product_currency" id="product_currency_<?php echo $product_id; ?>" value="<?php echo $product_currency; ?>">
                                                        <input type="hidden" name="product_quantity" id="product_quantity_<?php echo $product_id; ?>" value="<?php echo $product_quantity; ?>">
                                                        <input type="hidden" name="product_unit" id="product_unit_<?php echo $product_id; ?>" value="<?php echo $product_unit; ?>">
                                                        <input type="hidden" name="product_price" id="product_price_<?php echo $product_id; ?>" value="<?php echo $product_price; ?>">
                                                        <input type="hidden" name="product_tax_name" id="product_tax_name_<?php echo $product_id; ?>" value="<?php echo $product_tax_name; ?>">
                                                        <input type="hidden" name="product_tax_percentage" id="product_tax_percentage_<?php echo $product_id; ?>" value="<?php echo $product_tax_percentage; ?>">

                                                        <div class="form-group">
                                                            <label class="sr-only" for="product_in_cart_temp_quantity_<?php echo $product_id; ?>">Količina*</label>
                                                            <div class="input-group mb-2">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text"><span class="lnr lnr-layers"></span></div>
                                                                </div>
                                                                <input type="number" class="form-control" name="product_in_cart_temp_quantity" id="product_in_cart_temp_quantity_<?php echo $product_id; ?>" min="0" placeholder="Količina (<?php echo $product_unit; ?>)*" required>
                                                            </div>
                                                        </div>

                                                        <button type="submit" class="btn idk_btn btn-block">DODAJ</button>
                                                    </form><!-- End form - add item to cart temp -->

                                                <?php } else { ?>
                                                    <h4 class="my-3">Proizvod je već dodan u košaricu!</h4>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- End add to cart modal -->

                            </div>
                        </div>

                        <div class="col-12">
                            <div class="row">
                                <?php
                                if (isset($_GET['mess'])) {
                                    $mess = $_GET['mess'];
                                } else {
                                    $mess = 0;
                                }

                                if ($mess == 1) {
                                    echo '<div class="col-12 mb-1"><div class="alert material-alert material-alert_success mb-5">Uspješno ste dodali proizvod u listu.</div></div>';
                                } elseif ($mess == 2) {
                                    echo '<div class="col-12 mb-1"><div class="alert material-alert material-alert_danger mb-5">Greška: Forma nije pravilno popunjena!</div></div>';
                                } elseif ($mess == 4) {
                                    echo '<div class="col-12 mb-1"><div class="alert material-alert material-alert_success mb-5">Hvala! Uspješno ste dodali recenziju.</div></div>';
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
                                }
                                ?>
                            </div>
                            <h1 class="idk_product_title"><?php echo $product_name; ?></h1>
                            <div class="row idk_product_price_stars">
                                <div class="col-6">
                                    <?php if (isset($logged_client_show_price) and $logged_client_show_price == 0) {
                                    } else { ?>
                                        <h2 class="idk_product_price mr-5">VPC: <?php echo number_format($product_price, 3, ',', '.') . " " . $product_currency; ?></h2>
                                    <?php } ?>
                                    <?php if (isset($logged_client_show_quantity) and $logged_client_show_quantity == 0) {
                                    } else { ?>
                                        <h2 class="idk_product_price"><small>Stanje: <?php echo $product_quantity . " " . $product_unit; ?></small></h2>
                                    <?php } ?>
                                </div>
                                <div class="col-6 text-right">

                                    <!-- Get average score from idk_product_review -->
                                    <?php
                                    $average_score_query = $db->prepare("
                                                SELECT AVG(review_stars) AS review_avg
                                                FROM idk_product_review
                                                WHERE product_id = :product_id");

                                    $average_score_query->execute(array(
                                        ':product_id' => $product_id
                                    ));

                                    $average_score = $average_score_query->fetch();

                                    $review_avg = $average_score['review_avg'];

                                    ?>

                                    <!-- Button trigger modal - add review -->
                                    <button type="button" class="btn" data-toggle="modal" data-target="#addProductReviewModal" title="<?php echo $review_avg; ?>">
                                        <div class="idk_product_stars">
                                            <span class="lnr lnr-star <?php if ($review_avg >= 1) echo 'checked'; ?>"></span>
                                            <span class="lnr lnr-star <?php if ($review_avg >= 1.5) echo 'checked'; ?>"></span>
                                            <span class="lnr lnr-star <?php if ($review_avg >= 2.5) echo 'checked'; ?>"></span>
                                            <span class="lnr lnr-star <?php if ($review_avg >= 3.5) echo 'checked'; ?>"></span>
                                            <span class="lnr lnr-star <?php if ($review_avg >= 4.5) echo 'checked'; ?>"></span>
                                        </div>
                                    </button>
                                </div>

                                <!-- Modal - add review -->
                                <div class="modal fade" id="addProductReviewModal" tabindex="-1" role="dialog" aria-labelledby="addProductReviewModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="addProductReviewModalLabel">Dodaj recenziju</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <?php if (isset($_COOKIE['idk_session_front'])) { ?>
                                                    <form action="<?php getSiteUrl(); ?>do.php?form=add_product_review" method="post">
                                                        <div class="form-group">
                                                            <label class="sr-only" for="review_stars">Ocjena*</label>
                                                            <div class="input-group mb-2">
                                                                <div class="input-group-prepend">
                                                                    <label class="input-group-text" for="review_stars"><span class="lnr lnr-star"></span></label>
                                                                </div>
                                                                <select class="custom-select" name="review_stars" id="review_stars" required>

                                                                    <option selected>Ocjena ...</option>

                                                                    <option value="1">1</span></option>
                                                                    <option value="2">2</option>
                                                                    <option value="3">3</option>
                                                                    <option value="4">4</option>
                                                                    <option value="5">5</option>

                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="form-group idk_textarea_form_group">
                                                            <label class="sr-only" for="review_comment">Komentar</label>
                                                            <div class="input-group mb-2">
                                                                <div class="input-group-prepend">
                                                                    <div class="input-group-text">
                                                                        <span class="lnr lnr-text-align-left"></span>
                                                                    </div>
                                                                </div>
                                                                <textarea type="text" class="form-control" name="review_comment" id="review_comment" placeholder="Komentar"></textarea>
                                                            </div>
                                                        </div>
                                                        <input type="hidden" name="product_id" id="product_id" value="<?php echo $product_id; ?>">
                                                        <button type="submit" class="btn idk_btn btn-block">DODAJ</button>
                                                    </form>
                                                <?php } else { ?>
                                                    <h5>Samo klijenti mogu ostavljati recenzije!</h5>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- End add review modal -->

                            </div>
                        </div>
                    </div>
                </div>
            </section> <!-- End product title section -->

            <section class="idk_product_desc_section">
                <div class="accordion" id="product_accordion">

                    <div class="card">
                        <div class="card-header" id="opis">
                            <h2 class="mb-0">
                                <a href="#opis">
                                    <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                        Opis <span class="lnr lnr-chevron-up"></span>
                                    </button>
                                </a>
                            </h2>
                        </div>
                        <div id="collapseOne" class="collapse show" aria-labelledby="opis" data-parent="#product_accordion">
                            <div class="card-body">
                                <div class="container">
                                    <div class="row align-items-center">
                                        <div class="col-12">
                                            <p class="idk_product_desc"><?php echo $product_desc; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="recenzije">
                            <h2 class="mb-0">
                                <a href="#recenzije"><button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                        Recenzije <span class="lnr lnr-chevron-down"></span>
                                    </button></a>
                            </h2>
                        </div>
                        <div id="collapseTwo" class="collapse" aria-labelledby="recenzije" data-parent="#product_accordion">
                            <div class="card-body">
                                <div class="container">

                                    <!-- Get product reviews from db -->
                                    <?php
                                    $product_review_query = $db->prepare("
                                        SELECT client_id, review_stars, review_comment, updated_at
                                        FROM idk_product_review
                                        WHERE product_id = :product_id");

                                    $product_review_query->execute(array(
                                        ':product_id' => $product_id
                                    ));

                                    while ($product_review = $product_review_query->fetch()) {

                                        $client_id = $product_review['client_id'];
                                        $review_stars = $product_review['review_stars'];
                                        $review_comment = $product_review['review_comment'];
                                        $review_date = $product_review['updated_at'];

                                        $review_client_query = $db->prepare("
                                            SELECT client_name, client_image
                                            FROM idk_client
                                            WHERE client_id = :client_id");

                                        $review_client_query->execute(array(
                                            ':client_id' => $client_id
                                        ));

                                        $review_client = $review_client_query->fetch();

                                        $client_name = $review_client['client_name'];
                                        $client_image = $review_client['client_image'];

                                    ?>

                                        <div class="row align-items-center">
                                            <div class="col-4 col-sm-2">
                                                <img src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>" class="idk_review_client_image">
                                            </div>
                                            <div class="col-8 col-sm-8">
                                                <h2 class="idk_review_client_name"> <?php echo $client_name; ?></h2>
                                                <div class="idk_product_review_stars">
                                                    <span class="lnr lnr-star <?php if ($review_stars >= 1) echo 'checked'; ?>"></span>
                                                    <span class="lnr lnr-star <?php if ($review_stars >= 2) echo 'checked'; ?>"></span>
                                                    <span class="lnr lnr-star <?php if ($review_stars >= 3) echo 'checked'; ?>"></span>
                                                    <span class="lnr lnr-star <?php if ($review_stars >= 4) echo 'checked'; ?>"></span>
                                                    <span class="lnr lnr-star <?php if ($review_stars >= 5) echo 'checked'; ?>"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row align-items-center mt-3 mb-5">
                                            <div class="col-12">
                                                <p class="idk_review_comment"><?php echo $review_comment; ?></p>
                                                <hr>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if (isset($logged_client_id) and $logged_client_id != 0) { ?>
                                        <div class="row align-items-center mt-3 mb-5">
                                            <div class="col-12">
                                                <button type="button" class="btn idk_btn btn-block" data-toggle="modal" data-target="#addProductReviewModal">DODAJ RECENZIJU</button>
                                            </div>
                                        </div>
                                    <?php } ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        <?php
        } else {
            echo '<div class="alert material-alert material-alert_danger mt-5">Greška: Proizvod ne postoji u bazi podataka.</div>';
        }
        ?>

    </main> <!-- End main -->

    <!-- foot.php -->
    <?php include('includes/foot.php'); ?>

</body>

</html>