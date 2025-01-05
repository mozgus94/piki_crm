<?php
include("includes/functions.php");
include("includes/common_for_select_client.php");

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
                                echo '<div class="col-12 alert material-alert material-alert_success mb-5">Uspješno ste spremili početnu kilometražu.</div>';
                            } elseif ($mess == 2) {
                                echo '<div class="col-12 alert material-alert material-alert_success mb-5">Uspješno ste spremili završnu kilometražu.</div>';
                            } elseif ($mess == 4) {
                                echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success mb-5">Hvala! Vaša narudžba je zaprimljena.</div></div>';
                            } elseif ($mess == 5) {
                                echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_success mb-5">Hvala! Uspješno ste kreirali ponudu.</div></div>';
                            } elseif ($mess == 6) {
                                echo '<div class="col-12 mb-5"><div class="alert material-alert material-alert_danger mb-5">Greška! Završna kilometraža ne može biti manja od početne.</div></div>';
                            }
                            ?>
                            <div class="col-12">
                                <h1 class="idk_page_title">Izaberi klijenta</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </header> <!-- End header -->

    <!-- Main -->
    <main>

        <!-- Select client section -->
        <section class="idk_select_client_section">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="container">
                            <div class="row" id="idk_select_client">
                                <div class="col-12">

                                    <!-- Form - select client -->
                                    <form action="<?php getSiteUrl(); ?>do.php?form=select_client" method="post" class="idk_select_client_form">
                                        <div class="form-group">
                                            <select class="selectpicker" name="client_id" id="selectClient" data-live-search="true" required>

                                                <option selected>Izaberi klijenta ...</option>

                                                <!-- Get clients from db -->
                                                <?php
                                                $client_query = $db->prepare("
                                                    SELECT client_id, client_name, client_code
                                                    FROM idk_client
                                                    WHERE client_active = 1
                                                    ORDER BY client_name");

                                                $client_query->execute();

                                                while ($client = $client_query->fetch()) {

                                                    $client_id = $client['client_id'];
                                                    $client_name = $client['client_name'];
                                                    $client_code = $client['client_code'];

                                                ?>

                                                    <option value="<?php echo $client_id; ?>" data-tokens="<?php echo $client_name . ' ' . $client_code; ?>" onclick="submit();">
                                                        <?php echo $client_name; ?>
                                                    </option>

                                                <?php } ?>

                                            </select>
                                        </div>

                                        <!-- <button type="submit" class="btn idk_btn btn-block">ODABERI</button> -->
                                    </form><!-- End form - select client -->

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> <!-- End select client section -->

    </main> <!-- End main -->

    <!-- Foot bar -->
    <?php include('includes/foot_bar.php'); ?>

    <!-- foot.php -->
    <?php include('includes/foot.php'); ?>

</body>

</html>