<?php
include("includes/functions.php");
include("includes/common_for_new_mileage.php");

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
                            if (isset($_GET['mileage'])) {
                                $mileage_amount_start_from_db = $_GET['mileage'];
                            } else {
                                $mileage_amount_start_from_db = 0;
                            }

                            if ($mess == 1) {
                                echo '<div class="col-12 alert material-alert material-alert_success mb-5">Uspješno ste spremili početnu kilometražu.</div>';
                            } elseif ($mess == 2) {
                                echo '<div class="col-12 alert material-alert material-alert_success mb-5">Uspješno ste spremili završnu kilometražu.</div>';
                            } elseif ($mess == 4) {
                                echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success mb-5">Hvala! Vaša narudžba je zaprimljena.</div></div>';
                            } elseif ($mess == 5) {
                                echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success mb-5">Hvala! Uspješno ste kreirali ponudu.</div></div>';
                            } elseif ($mess == 6) {
                                echo '<div class="alert material-alert material-alert_danger mb-5">Greška! Završna kilometraža ne može biti manja od početne. Početna kilometraža: ' . $mileage_amount_start_from_db . ' km</div>';
                            }
                            ?>
                            <div class="col-12">
                                <h1 class="idk_page_title">Kilometraža</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($logged_employee_id) and $logged_employee_id != 0) { ?>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Form - Add mileage -->
                        <form id="idk_form" action="<?php getSiteUrl(); ?>do.php?form=add_mileage_from_new_mileage" method="post" class="form-horizontal" role="form">

                            <input type="hidden" name="employee_id" value="<?php echo $logged_employee_id; ?>" />

                            <?php if (date('Y-m-d', strtotime(getMileageStartTime($logged_employee_id))) <= date('Y-m-d', strtotime('-1 day'))) { ?>
                                <div class="form-group">
                                    <label for="mileage_amount_start">Početna kilometraža</label>
                                    <div class="row">
                                        <div class="col-9">
                                            <div class="input-group mb-2 idk_box_shadow_light">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text"><span class="lnr lnr-car"></span></div>
                                                </div>
                                                <input type="number" class="form-control" name="mileage_amount_start" id="mileage_amount_start" placeholder="Početna kilometraža" required>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <button type="submit" class="btn idk_btn btn-block m-0">SNIMI</button>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <h2>Početna kilometraža za ovaj dan je već postavljena</h2>
                            <?php } ?>

                        </form> <!-- End form - Add mileage -->
                    </div>
                </div>
            </div>
        <?php } ?>

    </header> <!-- End header -->

    <!-- Foot bar -->
    <?php include('includes/foot_bar.php'); ?>

    <!-- foot.php -->
    <?php include('includes/foot.php'); ?>

</body>

</html>