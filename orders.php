<?php
include("includes/functions.php");
include("includes/common_for_orders.php");

$getEmployeeStatus = getEmployeeStatus();
$getUnreadMessages = getUnreadMessages();

if (isset($_REQUEST["page"])) {
    $page = $_REQUEST["page"];
} else {
    $page = "list";
    header("Location: orders?page=list");
}

?>

<!DOCTYPE html>
<html lang="bs">

<head>

    <?php include('includes/head.php'); ?>

</head>

<body class="idk_body_background">

    <!-- Overlay menu -->
    <div id="idk_menu_overlay">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="container idk_page_title_container">
                        <div class="row align-items-center">
                            <div class="col-12">
                                <div class="row align-items-center">
                                    <div class="col-8">
                                        <h1 class="idk_page_title">Navigacija</h1>
                                    </div>
                                    <div class="col-4 text-right">
                                        <p><a href="#" class="idk_menu_toggler idk_static_background"><span class="lnr lnr-cross"></span></a></p>
                                    </div>
                                </div>
                                <ul>
                                    <li><a href="<?php getSiteUrl(); ?>orders"><span class="lnr lnr-list"></span>Nove narudžbe</a></li>
                                    <li><a href="<?php getSiteUrl(); ?>orders?page=finished_orders"><span class="lnr lnr-checkmark-circle"></span>Završene narudžbe</a></li>
                                    <li>
                                        <a href="<?php getSiteUrl(); ?>messages"><span class="lnr lnr-envelope"></span>Poruke
                                            <?php if ($getUnreadMessages > 0) { ?>
                                                <span class="badge badge-danger">1</span>
                                            <?php } ?>
                                        </a>
                                    </li>
                                    <li><a href="<?php getSiteUrl(); ?>settings_for_skladistar"><span class="lnr lnr-cog"></span>Postavke</a></li>
                                    <li><a href="<?php getSiteUrl(); ?>do.php?form=logout"><span class="lnr lnr-exit"></span>Odjava</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="header">

        <!-- Top bar -->
        <?php include('includes/top_bar.php'); ?>

    </header> <!-- End header -->

    <!-- Main -->
    <main>

        <?php
        switch ($page) {



                /************************************************************
 * 					LIST ALL NEW ORDERS
 * *********************************************************/
            case "list":
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
                                        echo '<div class="col-12 my-5"><div class="alert material-alert material-alert_success" style="margin-top: -50px">Uspješno ste završili narudžbu.</div></div>';
                                    } elseif ($mess == 2) {
                                        echo '<div class="col-12 my-5"><div class="alert material-alert material-alert_danger" style="margin-top: -50px">Greška: Forma nije pravilno popunjena!</div></div>';
                                    }
                                    ?>
                                    <div class="col-12">
                                        <h1 class="idk_page_title">Nove narudžbe</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- List orders section -->
                <section class="idk_list_items_section">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="container">

                                    <!-- Get new orders from db -->
                                    <?php

                                    $orders_query = $db->prepare("
                                        SELECT t1.order_id, t1.updated_at, t2.client_name, t2.client_image
                                        FROM idk_order t1
                                        INNER JOIN idk_client t2 ON t1.client_id = t2.client_id
                                        WHERE t1.order_status = :order_status");

                                    $orders_query->execute(array(
                                        ':order_status' => 2
                                    ));

                                    while ($order = $orders_query->fetch()) {

                                        $order_id = $order['order_id'];
                                        $order_updated_at = $order['updated_at'];
                                        $client_name = $order['client_name'];
                                        $client_image = $order['client_image'];
                                        $client_image = "none.jpg";

                                    ?>

                                        <a href="<?php getSiteUrl(); ?>orders?page=open&order_id=<?php echo $order_id; ?>">
                                            <div class="card mb-3 idk_order_card">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-3 p-0 text-center">
                                                            <img class="idk_order_client_image" src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>" alt="<?php echo $client_name; ?> logo">
                                                        </div>
                                                        <div class="col-9">
                                                            <h5 class="card-title idk_order_client_name"><?php echo $client_name; ?></h5>
                                                            <p class="card-text idk_order_number">Narudžba #<?php echo $order_id; ?></p>
                                                        </div>
                                                    </div>
                                                    <p class="card-text text-right idk_order_date"><small><em><?php echo date('d.m.Y.', strtotime($order_updated_at)); ?></em></small></p>
                                                </div>
                                            </div>
                                        </a>

                                    <?php } ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </section> <!-- End list orders section -->

                <?php
                break;



                /************************************************************
                 * 					OPEN ORDER
                 * *********************************************************/
            case "open":

                $order_id = $_GET['order_id'];

                if (isset($order_id)) {

                    // Get order from db
                    $order_query = $db->prepare("
                        SELECT t1.order_id, t1.updated_at, t2.client_name, t2.client_image
                        FROM idk_order t1
                        INNER JOIN idk_client t2 ON t1.client_id = t2.client_id
                        WHERE t1.order_id = :order_id");

                    $order_query->execute(array(
                        ':order_id' => $order_id
                    ));

                    $order = $order_query->fetch();

                    $order_id = $order['order_id'];
                    $order_updated_at = $order['updated_at'];
                    $client_name = $order['client_name'];
                    $client_image = $order['client_image'];
                    $client_image = "none.jpg";

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
                                            echo '<div class="col-12 my-5"><div class="alert material-alert material-alert_success">Uspješno ste završili narudžbu.</div></div>';
                                        } elseif ($mess == 2) {
                                            echo '<div class="col-12 my-5"><div class="alert material-alert material-alert_danger">Greška: Forma nije pravilno popunjena!</div></div>';
                                        }
                                        ?>
                                        <div class="col-2 p-0 text-center">
                                            <img class="idk_order_open_client_image" src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>" alt="<?php echo $client_name; ?> logo">
                                        </div>
                                        <div class="col-10">
                                            <h1 class="idk_page_title"><?php echo $client_name; ?></h1>
                                        </div>
                                        <div class="col-10 offset-2">
                                            <p class="idk_order_open_number">Narudžba #<?php echo $order_id; ?></p>
                                            <p class="idk_order_open_date"><small><em><?php echo date('d.m.Y.', strtotime($order_updated_at)); ?></em></small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List order products section -->
                    <section class="idk_list_items_section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="container">

                                        <form id="idk_skladiste_barkod_form" class="mt-0">
                                            <div class="form-group bg-white">
                                                <label class="sr-only" for="idk_skladiste_barkod_input">Barkod</label>
                                                <div class="input-group mb-2 bg-white">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text"><span class="lnr lnr-chart-bars"></span></div>
                                                    </div>
                                                    <input type="text" class="form-control bg-white" name="idk_skladiste_barkod_input" id="idk_skladiste_barkod_input" placeholder="Barkod" autofocus required>
                                                </div>
                                            </div>
                                        </form>

                                        <!-- Get order products from db -->
                                        <?php

                                        $order_products_query = $db->prepare("
                                            SELECT t1.product_id, t1.product_name, t1.product_quantity, t1.product_unit, t2.product_sku, t2.product_barcode
                                            FROM idk_product_order t1
                                            INNER JOIN idk_product t2
                                            ON t1.product_id = t2.product_id
                                            WHERE t1.order_id = :order_id");

                                        $order_products_query->execute(array(
                                            ':order_id' => $order_id
                                        ));

                                        while ($order_product = $order_products_query->fetch()) {

                                            $product_id = $order_product['product_id'];
                                            $product_name = $order_product['product_name'];
                                            $product_quantity = $order_product['product_quantity'];
                                            $product_unit = $order_product['product_unit'];
                                            $product_sku = $order_product['product_sku'];
                                            $product_barcode = $order_product['product_barcode'];

                                        ?>

                                            <div class="card mb-3 idk_order_card">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-2 p-0 text-center">
                                                            <div class="main-container__column material-checkbox-group material-checkbox-group_primary">
                                                                <input type="checkbox" id="checkbox_<?php echo $product_id; ?>" name="check_product" value="1" class="material-checkbox">
                                                                <label class="material-checkbox-group__label idk_check_product_label" for="checkbox_<?php echo $product_id; ?>" id="checkboxLabel_<?php echo $product_id; ?>"></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-10">
                                                            <h5 class="card-title idk_order_product_name"><?php echo $product_name; ?></h5>
                                                            <?php if (isset($product_sku)) { ?>
                                                                <p class="card-text idk_order_product_sku">SKU: <?php echo '<span class="idk_order_product_sku_value idk_order_product_sku_value_' . $product_sku . '">' . $product_sku . '</span>'; ?></p>
                                                            <?php } ?>
                                                            <?php if (isset($product_barcode)) { ?>
                                                                <p class="card-text idk_order_product_barcode">Barkod: <?php echo '<span class="idk_order_product_barcode_value idk_order_product_barcode_value_' . $product_barcode . '">' . $product_barcode . '</span>'; ?></p>
                                                            <?php } ?>
                                                            <p class="card-text idk_order_quantity">Količina: <?php echo '<span class="idk_order_quantity_value">' . $product_quantity . '</span> ' . $product_unit; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php } ?>

                                        <a data-toggle="modal" data-target="#finishOrder" id="idk_finish_order" class="btn idk_btn btn-block idk_btn_update_order disabled">Završi narudžbu</a>

                                        <!-- Modal - confirm finishing order -->
                                        <div class="modal fade" id="finishOrder" tabindex="-1" role="dialog" aria-labelledby="finishOrderLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="finishOrderLabel">Završi narudžbu</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <h4 class="my-3">Jeste li sigurni da želite završiti narudžbu?</h4>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a href="#" data-dismiss="modal" class="btn btn-secondary">Zatvori</a>
                                                        <a href="<?php getSiteUrl(); ?>do.php?form=finish_order&order_id=<?php echo $order_id; ?>" class="btn btn-success">Potvrdi</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> <!-- End modal - confirm finishing order -->

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section> <!-- End list order products section -->

                <?php } else {
                    header("Location: orders");
                }
                break;



                /************************************************************
                 * 					LIST ALL FINISHED ORDERS
                 * *********************************************************/
            case "finished_orders":
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
                                        echo '<div class="col-12 my-5"><div class="alert material-alert material-alert_success" style="margin-top: -50px">Uspješno ste završili narudžbu.</div></div>';
                                    } elseif ($mess == 2) {
                                        echo '<div class="col-12 my-5"><div class="alert material-alert material-alert_danger" style="margin-top: -50px">Greška: Forma nije pravilno popunjena!</div></div>';
                                    }
                                    ?>
                                    <div class="col-12">
                                        <h1 class="idk_page_title">Završene narudžbe</h1>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- List orders section -->
                <section class="idk_list_items_section">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12">
                                <div class="container">

                                    <!-- Get new orders from db -->
                                    <?php

                                    $orders_query = $db->prepare("
                                        SELECT t1.order_id, t1.updated_at, t2.client_name, t2.client_image
                                        FROM idk_order t1
                                        INNER JOIN idk_client t2 ON t1.client_id = t2.client_id
                                        WHERE t1.order_status = :order_status
                                        ORDER BY t1.order_id DESC");

                                    $orders_query->execute(array(
                                        ':order_status' => 3
                                    ));

                                    while ($order = $orders_query->fetch()) {

                                        $order_id = $order['order_id'];
                                        $order_updated_at = $order['updated_at'];
                                        $client_name = $order['client_name'];
                                        $client_image = $order['client_image'];
                                        $client_image = "none.jpg";

                                    ?>

                                        <a href="<?php getSiteUrl(); ?>orders?page=open_finished_order&order_id=<?php echo $order_id; ?>">
                                            <div class="card mb-3 idk_order_card">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-3 p-0 text-center">
                                                            <img class="idk_order_client_image" src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>" alt="<?php echo $client_name; ?> logo">
                                                        </div>
                                                        <div class="col-9">
                                                            <h5 class="card-title idk_order_client_name"><?php echo $client_name; ?></h5>
                                                            <p class="card-text idk_order_number">Narudžba #<?php echo $order_id; ?></p>
                                                        </div>
                                                    </div>
                                                    <p class="card-text text-right idk_order_date"><small><em><?php echo date('d.m.Y.', strtotime($order_updated_at)); ?></em></small></p>
                                                </div>
                                            </div>
                                        </a>

                                    <?php } ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </section> <!-- End list orders section -->

                <?php
                break;



                /************************************************************
                 * 					OPEN FINISHED ORDER
                 * *********************************************************/
            case "open_finished_order":

                $order_id = $_GET['order_id'];

                if (isset($order_id)) {

                    // Get order from db
                    $order_query = $db->prepare("
                        SELECT t1.order_id, t1.updated_at, t2.client_name, t2.client_image
                        FROM idk_order t1
                        INNER JOIN idk_client t2 ON t1.client_id = t2.client_id
                        WHERE t1.order_id = :order_id");

                    $order_query->execute(array(
                        ':order_id' => $order_id
                    ));

                    $order = $order_query->fetch();

                    $order_id = $order['order_id'];
                    $order_updated_at = $order['updated_at'];
                    $client_name = $order['client_name'];
                    $client_image = $order['client_image'];

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
                                            echo '<div class="col-12 my-5"><div class="alert material-alert material-alert_success">Uspješno ste završili narudžbu.</div></div>';
                                        } elseif ($mess == 2) {
                                            echo '<div class="col-12 my-5"><div class="alert material-alert material-alert_danger">Greška: Forma nije pravilno popunjena!</div></div>';
                                        }
                                        ?>
                                        <div class="col-2 p-0 text-center">
                                            <img class="idk_order_open_client_image" src="<?php getSiteUrl(); ?>idkadmin/files/clients/images/<?php echo $client_image; ?>" alt="<?php echo $client_name; ?> logo">
                                        </div>
                                        <div class="col-10">
                                            <h1 class="idk_page_title">
                                                <?php echo $client_name; ?></h1>
                                        </div>
                                        <div class="col-10 offset-2">
                                            <p class="idk_order_open_number">Narudžba #<?php echo $order_id; ?></p>
                                            <p class="idk_order_open_date"><small><em><?php echo date('d.m.Y.', strtotime($order_updated_at)); ?></em></small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- List order products section -->
                    <section class="idk_list_items_section">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-12">
                                    <div class="container">

                                        <!-- Get order products from db -->
                                        <?php

                                        $order_products_query = $db->prepare("
                                            SELECT t1.product_id, t1.product_name, t1.product_quantity, t1.product_unit, t2.product_sku, t2.product_barcode
                                            FROM idk_product_order t1
                                            INNER JOIN idk_product t2
                                            ON t1.product_id = t2.product_id
                                            WHERE t1.order_id = :order_id");

                                        $order_products_query->execute(array(
                                            ':order_id' => $order_id
                                        ));

                                        while ($order_product = $order_products_query->fetch()) {

                                            $product_id = $order_product['product_id'];
                                            $product_name = $order_product['product_name'];
                                            $product_quantity = $order_product['product_quantity'];
                                            $product_unit = $order_product['product_unit'];
                                            $product_sku = $order_product['product_sku'];
                                            $product_barcode = $order_product['product_barcode'];

                                        ?>

                                            <div class="card mb-3 idk_order_card">
                                                <div class="card-body">
                                                    <div class="row align-items-center">
                                                        <div class="col-2 p-0 text-center">
                                                            <div class="main-container__column material-checkbox-group material-checkbox-group_primary">
                                                                <input type="checkbox" id="checkbox_<?php echo $product_id; ?>" name="checked_product" value="1" class="material-checkbox" checked="checked">
                                                                <label class="material-checkbox-group__label idk_check_product_label" for="checkbox_<?php echo $product_id; ?>" id="checkboxLabel_<?php echo $product_id; ?>"></label>
                                                            </div>
                                                        </div>
                                                        <div class="col-10">
                                                            <h5 class="card-title idk_order_product_name"><?php echo $product_name; ?></h5>
                                                            <?php if (isset($product_sku)) { ?>
                                                                <p class="card-text idk_order_product_sku">SKU: <?php echo $product_sku; ?></p>
                                                            <?php } ?>
                                                            <?php if (isset($product_barcode)) { ?>
                                                                <p class="card-text idk_order_product_barcode">Barkod: <?php echo $product_barcode; ?></p>
                                                            <?php } ?>
                                                            <p class="card-text idk_order_quantity">Količina: <?php echo $product_quantity . ' ' . $product_unit; ?></p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php } ?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section> <!-- End list order products section -->

        <?php } else {
                    header("Location: orders");
                }
                break;
        } ?>
    </main> <!-- End main -->

    <!-- foot.php -->
    <?php include('includes/foot.php'); ?>

    <script>
        $(document).ready(function() {
            $('input[name="check_product"]').click(function() {

                $(this).attr('checked', function(index, attr) {
                    return attr == 'checked' ? false : 'checked';
                });

                $(this).closest('.row').find('.col-10').toggleClass('idk_strike_through');

                let numberOfCheckboxes = $('input[name="check_product"]').length;

                if ($('input[name="check_product"]:checked').length == numberOfCheckboxes) {
                    $('#idk_finish_order').removeClass('disabled');
                } else {
                    $('#idk_finish_order').addClass('disabled');
                }

            });
        });
    </script>

</body>

</html>