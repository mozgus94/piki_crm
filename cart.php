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
                            <?php
                            if (isset($_GET['mess'])) {
                                $mess = $_GET['mess'];
                            } else {
                                $mess = 0;
                            }

                            if ($mess == 1) {
                                echo '<div class="col-12"><div class="alert material-alert material-alert_success mb-5">Uspješno ste obrisali proizvod iz košarice.</div></div>';
                            } elseif ($mess == 2) {
                                echo '<div class="col-12"><div class="alert material-alert material-alert_success mb-5">Uspješno ste ispraznili košaricu.</div></div>';
                            } elseif ($mess == 3) {
                                echo '<div class="col-12"><div class="alert material-alert material-alert_success mb-5">Hvala! Uspješno ste izvršili narudžbu.</div></div>';
                            } elseif ($mess == 4) {
                                echo '<div class="col-12"><div class="alert material-alert material-alert_success mb-5">Uspješno ste ažurirali košaricu.</div></div>';
                            }
                            ?>
                            <div class="col-10">
                                <h1 class="idk_page_title">Košarica</h1>
                            </div>
                            <div class="col-2 text-right">
                                <!-- Button trigger delete all items from cart -->
                                <a href="#" data-toggle="modal" data-target="#deleteAllFromCartModal">
                                    <span class="lnr lnr-trash"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header> <!-- End header -->

    <!-- Main -->
    <main>

        <!-- Cart items section -->
        <section class="idk_cart_items_section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="container">

                            <!-- Get products from product_order_temp -->
                            <?php
                            if (isset($logged_client_id) and $logged_client_id != 0) {
                                $client_id = $logged_client_id;
                            } else if (isset($_COOKIE['idk_session_front_client'])) {
                                $client_id = $_COOKIE['idk_session_front_client'];
                            } else {
                                $client_id = NULL;
                            }

                            //Get max rabat for client
                            $check_query = $db->prepare("
                                SELECT client_max_rabat
                                FROM idk_client
                                WHERE client_id = :client_id");

                            $check_query->execute(array(
                                ':client_id' => $client_id
                            ));

                            $number_of_rows = $check_query->rowCount();

                            if ($number_of_rows == 1) {
                                $client_max_rabat_row = $check_query->fetch();
                                $client_max_rabat = isset($client_max_rabat_row['client_max_rabat']) ? floatval($client_max_rabat_row['client_max_rabat']) : NULL;
                            } else {
                                $client_max_rabat = NULL;
                            }

                            //If employee is logged
                            if (isset($_COOKIE['idk_session_front_client'])) {
                                // Get product order data
                                $product_order_temp_query = $db->prepare("
                                    SELECT t1.order_id
                                    FROM idk_product_order_temp t1, idk_order_temp t2
                                    WHERE t2.client_id = :client_id AND t1.order_id = t2.order_id AND t2.employee_id = :employee_id");

                                $product_order_temp_query->execute(array(
                                    ':client_id' => $client_id,
                                    ':employee_id' => $logged_employee_id
                                ));
                            } else {
                                // Get product order data
                                $product_order_temp_query = $db->prepare("
                                    SELECT t1.order_id
                                    FROM idk_product_order_temp t1, idk_order_temp t2
                                    WHERE t2.client_id = :client_id AND t1.order_id = t2.order_id AND t2.employee_id IS NULL");

                                $product_order_temp_query->execute(array(
                                    ':client_id' => $client_id
                                ));
                            }

                            $number_of_rows = $product_order_temp_query->rowCount();

                            if ($number_of_rows > 0) {
                                $product_order_temp = $product_order_temp_query->fetch();
                                $order_id = $product_order_temp['order_id'];
                            } else {
                                $order_id = NULL;
                            }


                            if (isset($logged_employee_id) and $logged_employee_id != 0 and isset($order_id)) { ?>
                                <div class="row">
                                    <div class="col-12">
                                        <p class="idk_order_rabat_percentage_paragraph">
                                        <div class="form-group">
                                            <label class="sr-only" for="order_rabat_percentage">Rabat na cijelu narudžbu %</label>
                                            <div class="input-group mb-2">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">Rabat na cijelu narudžbu %</div>
                                                </div>
                                                <input type="number" min="0" <?php if (isset($client_max_rabat)) {
                                                                                    echo 'max="' . $client_max_rabat . '"';
                                                                                } else {
                                                                                    echo 'max=100';
                                                                                } ?> class="form-control idk_order_rabat_percentage" name="order_rabat_percentage" id="order_rabat_percentage" placeholder="0.00">
                                            </div>
                                        </div>
                                        </p>
                                    </div>
                                </div>
                                <?php }
                            if (isset($order_id)) {
                                if (isset($logged_employee_id) and $logged_employee_id != 0) {
                                    // Get product order data
                                    $product_order_temp_query = $db->prepare("
                                        SELECT t1.product_id, t1.order_id, t1.product_name, t1.product_quantity, t1.product_unit, t1.product_price, t1.product_currency, t1.product_tax_name, t1.product_tax_percentage, t1.product_rabat_percentage, t2.order_total_price, t2.order_total_tax, t2.order_total_rabat, t2.order_to_pay
                                        FROM idk_product_order_temp t1, idk_order_temp t2
                                        WHERE t2.client_id = :client_id AND t1.order_id = t2.order_id AND t2.employee_id = :employee_id");

                                    $product_order_temp_query->execute(array(
                                        ':client_id' => $client_id,
                                        ':employee_id' => $logged_employee_id
                                    ));
                                } else {
                                    // Get product order data
                                    $product_order_temp_query = $db->prepare("
                                        SELECT t1.product_id, t1.order_id, t1.product_name, t1.product_quantity, t1.product_unit, t1.product_price, t1.product_currency, t1.product_tax_name, t1.product_tax_percentage, t1.product_rabat_percentage, t2.order_total_price, t2.order_total_tax, t2.order_total_rabat, t2.order_to_pay
                                        FROM idk_product_order_temp t1, idk_order_temp t2
                                        WHERE t2.client_id = :client_id AND t1.order_id = t2.order_id AND t2.employee_id IS NULL");

                                    $product_order_temp_query->execute(array(
                                        ':client_id' => $client_id
                                    ));
                                }

                                while ($product_order_temp = $product_order_temp_query->fetch()) {

                                    $product_id = $product_order_temp['product_id'];
                                    $order_id = $product_order_temp['order_id'];
                                    $product_name = $product_order_temp['product_name'];
                                    $product_quantity = $product_order_temp['product_quantity'];
                                    $product_unit = $product_order_temp['product_unit'];
                                    $product_price = $product_order_temp['product_price'];
                                    $product_currency = $product_order_temp['product_currency'];
                                    $product_tax_name = $product_order_temp['product_tax_name'];
                                    $product_tax_percentage = $product_order_temp['product_tax_percentage'];
                                    $product_rabat_percentage = $product_order_temp['product_rabat_percentage'];
                                    $order_total_price = $product_order_temp['order_total_price'];
                                    $order_total_tax = $product_order_temp['order_total_tax'];
                                    $order_total_rabat = $product_order_temp['order_total_rabat'];
                                    $order_to_pay = $product_order_temp['order_to_pay'];

                                ?>

                                    <div class="row">
                                        <div class="col-2 pr-0 align-self-center">
                                            <p class="idk_cart_remove_item">
                                                <!-- Button trigger delete one item from cart -->
                                                <a href="#" data-toggle="modal" data-target="#deleteFromCartModal_<?php echo $product_id; ?>">
                                                    <span class="lnr lnr-cross-circle"></span>
                                                </a>
                                            </p>
                                        </div>

                                        <!-- Modal - delete one item from cart -->
                                        <div class="modal fade" id="deleteFromCartModal_<?php echo $product_id; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteFromCartModal_<?php echo $product_id; ?>Label" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteFromCartModal_<?php echo $product_id; ?>Label">Ukloni iz košarice</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <form action="<?php getSiteUrl(); ?>do.php?form=delete_cart_item_temp" method="post">

                                                        <input type="hidden" name="order_id" id="order_id_<?php echo $product_id; ?>" value="<?php echo $order_id; ?>">
                                                        <input type="hidden" name="product_id" id="product_id_<?php echo $product_id; ?>" value="<?php echo $product_id; ?>" class="idk_product_id">
                                                        <input type="hidden" name="product_name" id="product_name_<?php echo $product_id; ?>" value="<?php echo $product_name; ?>">
                                                        <input type="hidden" name="product_currency" id="product_currency_<?php echo $product_id; ?>" value="<?php echo $product_currency; ?>">
                                                        <input type="hidden" name="product_quantity" id="product_quantity_<?php echo $product_id; ?>" value="<?php echo $product_quantity; ?>">
                                                        <input type="hidden" name="product_unit" id="product_unit_<?php echo $product_id; ?>" value="<?php echo $product_unit; ?>">
                                                        <input type="hidden" name="product_price" id="product_price_<?php echo $product_id; ?>" value="<?php echo $product_price; ?>" class="idk_product_price">
                                                        <input type="hidden" name="product_tax_name" id="product_tax_name_<?php echo $product_id; ?>" value="<?php echo $product_tax_name; ?>">
                                                        <input type="hidden" name="product_tax_percentage" id="product_tax_percentage_<?php echo $product_id; ?>" value="<?php echo $product_tax_percentage; ?>" class="idk_product_tax_percentage">

                                                        <div class="modal-body">
                                                            <h4 class="my-3">Jeste li sigurni da želite ukloniti proizvod iz košarice?</h4>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" data-dismiss="modal" class="btn btn-secondary">Zatvori</button>
                                                            <button type="submit" class="btn btn-danger">Ukloni</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div> <!-- End delete one item from cart modal -->

                                        <!-- Modal - delete all items from cart -->
                                        <div class="modal fade" id="deleteAllFromCartModal" tabindex="-1" role="dialog" aria-labelledby="deleteAllFromCartModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="deleteAllFromCartModalLabel">Isprazni košaricu</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h4 class="my-3">Jeste li sigurni da želite isprazniti košaricu?</h4>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a href="#" data-dismiss="modal" class="btn btn-secondary">Zatvori</a>
                                                        <a href="<?php getSiteUrl(); ?>do.php?form=delete_all_cart_item_temp&order_id=<?php echo $order_id; ?>" class="btn btn-danger">Ukloni sve</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!-- End delete all items from cart modal -->

                                        <?php if (isset($logged_client_show_price) and $logged_client_show_price == 0) { ?>
                                            <div class="col-4 pl-0 align-self-center">
                                                <p class="idk_cart_item_title">
                                                    <?php echo $product_name; ?>
                                                </p>
                                            </div>
                                            <div class="col-6 text-right align-self-center">
                                                <p class="idk_cart_item_quantity">
                                                    <button type="button" class="btn border-0 p-0" style="vertical-align: unset;"><span class="lnr lnr-circle-minus"></span></button>
                                                    <input type="number" min="0" class="idk_cart_item_quantity_number" value="<?php echo $product_quantity; ?>">
                                                    <button type="button" class="btn border-0 p-0" style="vertical-align: unset;"><span class="lnr lnr-plus-circle"></span></button>
                                                </p>
                                            </div>
                                        <?php } else { ?>

                                            <div class="col-10 pl-0">
                                                <div>
                                                    <p class="idk_cart_item_title mb-3">
                                                        <?php echo $product_name; ?>
                                                    </p>
                                                </div>
                                                <div class="container-fluid p-0">
                                                    <div class="row align-items-center idk_cart_item_price_quantity_row">
                                                        <div class="col-6">
                                                            <p class="idk_cart_item_price">
                                                                VPC: <?php echo isset($product_price) && is_numeric($product_price) 
                                                                        ? number_format($product_price, 3, ',', '.') . " " . $product_currency 
                                                                        : "N/A"; ?>
                                                            </p>

                                                            <?php if (isset($logged_employee_id) and $logged_employee_id != 0) { ?>
                                                                <p class="mt-3">
                                                                <div class="form-group">
                                                                    <label class="sr-only" for="product_rabat_percentage_<?php echo $product_id; ?>">Rabat %</label>
                                                                    <div class="input-group mb-2">
                                                                        <div class="input-group-prepend">
                                                                            <div class="input-group-text">Rabat %</div>
                                                                        </div>
                                                                        <input type="number" min="0" <?php if (isset($client_max_rabat)) {
                                                                                                            echo 'max="' . $client_max_rabat . '"';
                                                                                                        } else {
                                                                                                            echo 'max=100';
                                                                                                        } ?> class="form-control idk_product_rabat_percentage" name="product_rabat_percentage" id="product_rabat_percentage_<?php echo $product_id; ?>" placeholder="0.00" value="<?php echo $product_rabat_percentage; ?>">
                                                                    </div>
                                                                </div>
                                                                </p>
                                                            <?php } ?>

                                                        </div>
                                                        <div class="col-6 text-right">
                                                            <p class="idk_cart_item_quantity">
                                                                <button type="button" class="btn border-0 p-0" style="vertical-align: unset;"><span class="lnr lnr-circle-minus"></span></button>
                                                                <input type="number" min="0" class="idk_cart_item_quantity_number" value="<?php echo $product_quantity; ?>">
                                                                <button type="button" class="btn border-0 p-0" style="vertical-align: unset;"><span class="lnr lnr-plus-circle"></span></button>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>

                                    </div>

                            <?php
                                }
                            }
                            ?>

                            <?php if (!isset($order_id)) { ?>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="h2">Vaša košarica je prazna</div>
                                    </div>
                                </div>
                                <?php } else {

                                if (isset($logged_client_show_price) and $logged_client_show_price == 0) {
                                } else { ?>

                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <p class="idk_total_paragraph">Ukupno</p>
                                            <p class="idk_total_rabat_paragraph">Rabat</p>
                                            <p class="idk_tax_paragraph">PDV 17%</p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <p class="idk_total_value"> <?php echo isset($order_total_price) && is_numeric($order_total_price) 
                                                                            ? number_format($order_total_price, 3, ',', '.') . " KM" 
                                                                            : "0.000 KM"; ?> KM</p>
                                            <p class="idk_total_rabat_value"> <?php echo isset($order_total_rabat) && is_numeric($order_total_rabat) 
                                                                            ? number_format($order_total_rabat, 3, ',', '.') . " KM" 
                                                                            : "0.000 KM"; ?> KM</p>
                                            <p class="idk_tax_value">  <?php echo isset($order_total_tax) && is_numeric($order_total_tax) 
                                                                            ? number_format($order_total_tax, 3, ',', '.') . " KM" 
                                                                            : "0.000 KM"; ?> KM</p>
                                        </div>
                                    </div>

                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <p class="idk_to_pay_paragraph">Za platiti</p>
                                        </div>
                                        <div class="col-6 text-right">
                                            <p class="idk_to_pay_value"><?php echo isset($order_to_pay) && is_numeric($order_to_pay) 
                                                                        ? number_format($order_to_pay, 2, ',', '.') . " KM" 
                                                                        : "0.00 KM"; ?> KM</p>
                                        </div>
                                    </div>

                                <?php } ?>

                                <!-- Form - update order -->
                                <form action="<?php getSiteUrl(); ?>do.php?form=update_order" method="post">

                                    <!-- Storing all products rabats in array and using it in do.php -->
                                    <input type="hidden" name="products_rabats_array[]" id="idk_products_rabats_array" multiple="multiple">

                                    <!-- Storing all products quantites in array and using it in do.php -->
                                    <input type="hidden" name="products_quantities_array[]" id="idk_products_quantities_array" multiple="multiple">

                                    <!-- Storing all products ids in array and using it in do.php -->
                                    <input type="hidden" name="products_ids_array[]" id="idk_products_ids_array" multiple="multiple">

                                    <!-- Storing all products prices in array and using it in do.php -->
                                    <input type="hidden" name="products_prices_array[]" id="idk_products_prices_array" multiple="multiple">

                                    <!-- Storing all products tax values in array and using it in do.php -->
                                    <input type="hidden" name="products_tax_percentages_array[]" id="idk_products_tax_percentages_array" multiple="multiple">

                                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

                                    <button type="submit" class="btn idk_btn btn-block idk_btn_update_order d-flex justify-content-center align-items-center"><span class="lnr lnr-sync mr-2"></span> Ažuriraj košaricu</button>

                                </form><!-- End form - update order -->

                                <?php if (isset($logged_employee_id) and $logged_employee_id != 0) { ?>
                                    <!-- Form - create offer -->
                                    <form action="<?php getSiteUrl(); ?>do.php?form=create_offer" method="post">
                                        <button type="button" data-toggle="modal" data-target="#confirmOffer" class="btn idk_btn btn-block idk_btn_update_order d-flex justify-content-center align-items-center" id="idk_btn_ponuda"><span class="lnr lnr-file-empty mr-2"></span> Kreiraj ponudu</button>

                                        <!-- Modal - confirm offer -->
                                        <div class="modal fade" id="confirmOffer" tabindex="-1" role="dialog" aria-labelledby="confirmOfferLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="confirmOfferLabel">Potvrdi ponudu</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h4 class="mt-3 mb-5">Jeste li sigurni da želite kreirati ponudu?</h4>
                                                        <h5>Tip ponude</h5>
                                                        <div class="form-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="offer_type" id="offer_type_1" value="1" checked>
                                                                <label class="form-check-label" for="offer_type_1">
                                                                    Na licu mjesta
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="offer_type" id="offer_type_2" value="2">
                                                                <label class="form-check-label" for="offer_type_2">
                                                                    Telefonska ponuda
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <h5 class="mt-5">Prikaži slike na ponudi</h5>
                                                        <div class="form-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="offer_images" id="offer_images_1" value="1" checked>
                                                                <label class="form-check-label" for="offer_images_1">
                                                                    Da
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="offer_images" id="offer_images_2" value="2">
                                                                <label class="form-check-label" for="offer_images_2">
                                                                    Ne
                                                                </label>
                                                            </div>
                                                        </div>

                                                        <h5 class="mt-5">Bilješka</h5>
                                                        <div class="form-group">
                                                            <label class="sr-only" for="offer_note">Bilješka</label>
                                                            <textarea class="form-control idk_body_background p-3" id="offer_note" class="form-control" name="offer_note" rows="3" placeholder="Bilješka..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a href="#" data-dismiss="modal" class="btn btn-secondary">Zatvori</a>
                                                        <button type="submit" class="btn btn-success">Potvrdi</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!-- End modal - confirm offer -->
                                    </form> <!-- End form - create offer -->
                                <?php } ?>

                                <!-- Form - submit order -->
                                <form action="<?php getSiteUrl(); ?>do.php?form=submit_order" method="post">
                                    <button type="button" data-toggle="modal" data-target="#confirmOrder" class="btn idk_btn btn-block idk_btn_submit_order d-flex justify-content-center align-items-center"><span class="lnr lnr-cart mr-2"></span> NARUČI</button>

                                    <!-- Modal - confirm order -->
                                    <div class="modal fade" id="confirmOrder" tabindex="-1" role="dialog" aria-labelledby="confirmOrderLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="confirmOrderLabel">Potvrdi narudžbu</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <h4 class="mt-3 mb-5">Jeste li sigurni da želite izvršiti narudžbu?</h4>
                                                    <?php if (isset($logged_employee_id) and $logged_employee_id != 0) { ?>
                                                        <h5>Tip narudžbe</h5>
                                                        <div class="form-group">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="order_type" id="order_type_1" value="1" checked>
                                                                <label class="form-check-label" for="order_type_1">
                                                                    Na licu mjesta
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="radio" name="order_type" id="order_type_2" value="2">
                                                                <label class="form-check-label" for="order_type_2">
                                                                    Telefonska narudžba
                                                                </label>
                                                            </div>
                                                        </div>
                                                    <?php } else { ?>
                                                        <input type="hidden" name="order_type" value="3">
                                                    <?php } ?>
                                                    <h5 class="mt-5">Bilješka</h5>
                                                    <div class="form-group">
                                                        <label class="sr-only" for="order_note">Bilješka</label>
                                                        <textarea class="form-control idk_body_background p-3" id="order_note" class="form-control" name="order_note" rows="3" placeholder="Bilješka..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <a href="#" data-dismiss="modal" class="btn btn-secondary">Zatvori</a>
                                                    <button type="submit" class="btn btn-success">Potvrdi</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- End modal - confirm order -->
                                </form><!-- End form - submit order -->

                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>
        </section> <!-- End cart items section -->

    </main> <!-- End main -->

    <!-- Foot bar -->
    <?php include('includes/foot_bar.php'); ?>

    <!-- foot.php -->
    <?php include('includes/foot.php'); ?>

</body>

</html>