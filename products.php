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
                                <h1 class="idk_page_title">Svi proizvodi</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header> <!-- End header -->

    <!-- Main -->
    <main>

        <!-- Products section -->
        <section class="idk_products_cards_section mt-5">
            <div class="container">
                <div class="row align-items-center justify-content-center justify-content-sm-start">

                    <table id="idk_table_products" class="display" width="100%">
                        <tbody>
                            <tr>
                                <td class="text-center pt-5">
                                    <h5>Učitavanje...</h5>
                                    <img src="./idkadmin/images/ajax-loader.gif" alt="Učitavanje...">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </section> <!-- End products section -->


    </main> <!-- End main -->

    <!-- Foot bar -->
    <?php include('includes/foot_bar.php'); ?>

    <!-- foot.php -->
    <?php include('includes/foot.php'); ?>

</body>

</html>