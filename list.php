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
                                echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success">Uspješno ste dodali novu listu.</div></div>';
                            } elseif ($mess == 2) {
                                echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_danger">Greška: Forma nije pravilno popunjena!</div></div>';
                            } elseif ($mess == 3) {
                                echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success">Uspješno ste obrisali listu.</div></div>';
                            } elseif ($mess == 4) {
                                echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_danger">Greška: Lista nije pronađena u bazi!</div></div>';
                            }
                            ?>
                            <div class="col-10">
                                <h1 class="idk_page_title">Moje liste</h1>
                            </div>
                            <div class="col-2 text-right">
                                <!-- Button trigger modal -->
                                <button type="button" class="btn" data-toggle="modal" data-target="#newListModal">
                                    <span class="lnr lnr-plus-circle"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="newListModal" tabindex="-1" role="dialog" aria-labelledby="newListModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="newListModalLabel">Nova lista</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="<?php getSiteUrl(); ?>do.php?form=add_list" method="post">
                            <div class="form-group">
                                <label class="sr-only" for="list_name">Naziv liste*</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><span class="lnr lnr-heart"></span></div>
                                    </div>
                                    <input type="text" class="form-control" name="list_name" id="list_name" placeholder="Naziv liste*" required>
                                </div>
                            </div>
                            <button type="submit" class="btn idk_btn btn-block">DODAJ</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header> <!-- End header -->

    <!-- Main -->
    <main>

        <!-- List items section -->
        <section class="idk_list_items_section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="container">
                            <div class="accordion" id="list_accordion">

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
                                } elseif (isset($logged_employee_id) and $logged_employee_id != 0) {

                                    if(isset($_COOKIE['idk_session_front_client'])){
                                        $client_id = $_COOKIE['idk_session_front_client'];
                                    } else{
                                        $client_id = NULL;
                                    }

                                    $list_query = $db->prepare("
                                        SELECT list_id, list_name
                                        FROM idk_list
                                        WHERE client_id = :client_id AND employee_id IS NOT NULL");

                                    $list_query->execute(array(
                                        ':client_id' => $client_id
                                    ));
                                }


                                while ($list = $list_query->fetch()) {
                                    $list_items_counter = 0;
                                    $list_id = $list['list_id'];
                                    $list_name = $list['list_name'];

                                ?>

                                    <div class="card">
                                        <div class="card-header" id="heading<?php echo $list_id; ?>">
                                            <div class="row align-items-center">
                                                <div class="col-8">
                                                    <h2 class="mb-0 d-inline">
                                                        <button class="btn btn-link text-left idk_list_name" type="button" data-toggle="collapse" data-target="#collapse<?php echo $list_id; ?>" aria-expanded="true" aria-controls="collapse<?php echo $list_id; ?>">
                                                            <?php echo $list_name; ?>
                                                        </button>
                                                    </h2>
                                                    <!-- Button trigger delete list -->
                                                    <a href="#" class="idk_delete_list" data="<?php getSiteUrl(); ?>do.php?form=delete_list&list_id=<?php echo $list_id; ?>" data-toggle="modal" data-target="#deleteListModal">
                                                        <span class="lnr lnr-trash"></span>
                                                    </a>
                                                </div>
                                                <div class="col-4 text-right">
                                                    <a href="<?php getSiteUrl(); ?>do.php?form=add_list_to_cart&list_id=<?php echo $list_id; ?>">
                                                        <div class="row align-items-center p-0">
                                                            <div class="col-6 text-center text-sm-right p-0 pr-sm-1">
                                                                <span class="lnr lnr-cart idk_add_list_to_cart_icon"></span>
                                                            </div>
                                                            <div class="col-6 text-center text-sm-left p-0 pl-sm-1">
                                                                <span class="idk_add_list_to_cart_span">Dodaj listu u košaricu</span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <div id="collapse<?php echo $list_id; ?>" class="collapse" aria-labelledby="heading<?php echo $list_id; ?>" data-parent="#list_accordion">
                                            <div class="card-body">

                                                <div class="row idk_list_items_row">

                                                    <!-- Get data for list items -->
                                                    <?php
                                                    $query = $db->prepare("
                                                        SELECT t1.product_id, t1.product_name, t2.product_on_list_quantity, t2.product_unit
                                                        FROM idk_product t1
                                                        INNER JOIN idk_product_list t2 ON t1.product_id = t2.product_id
                                                        WHERE t2.list_id = :list_id
                                                        ORDER BY t1.product_name");

                                                    $query->execute(array(
                                                        ':list_id' => $list_id
                                                    ));

                                                    while ($product = $query->fetch()) {
                                                        $list_items_counter++;
                                                        $product_id = $product['product_id'];
                                                        $product_name = $product['product_name'];
                                                        $product_on_list_quantity = $product['product_on_list_quantity'];
                                                        $product_unit = $product['product_unit'];
                                                    ?>

                                                        <div class="col-6 mb-2">
                                                            <a href="<?php getSiteUrl(); ?>product?id=<?php echo $product_id; ?>">
                                                                <p><strong><?php echo $list_items_counter . ". " . $product_name; ?></strong></p>
                                                            </a>
                                                        </div>
                                                        <div class="col-6 mb-2">
                                                            <p><strong><?php echo $product_on_list_quantity . " " . $product_unit; ?></strong></p>
                                                        </div>

                                                    <?php } ?>

                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                <?php } ?>

                                <!-- Modal - delete list -->
                                <div class="modal fade" id="deleteListModal" tabindex="-1" role="dialog" aria-labelledby="deleteListModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteListModalLabel">Obriši listu</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <h4 class="my-3">Jeste li sigurni da želite obrisati listu?</h4>
                                            </div>
                                            <div class="modal-footer">
                                                <a href="#" data-dismiss="modal" class="btn btn-secondary">Zatvori</a>
                                                <a id="idk_delete_list_link" href="" class="btn btn-danger">Obriši</a>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- End delete list modal -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> <!-- End list items section -->

    </main> <!-- End main -->

    <!-- Foot bar -->
    <?php include('includes/foot_bar.php'); ?>

    <!-- foot.php -->
    <?php include('includes/foot.php'); ?>

</body>

</html>