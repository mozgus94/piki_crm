<?php
include("includes/functions.php");
// Ne mora klijent biti logovan
// include("includes/common.php");

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- SEO -->
    <!-- <meta name=”robots” content="index, follow"> -->
    <meta name=”robots” content="noindex, nofollow">

    <meta name="author" content="IDK Studio d.o.o.">

    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php getSiteUrl(); ?>images/favicon/144.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php getSiteUrl(); ?>images/favicon/114.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php getSiteUrl(); ?>images/favicon/72.png">
    <link rel="apple-touch-icon-precomposed" href="<?php getSiteUrl(); ?>images/favicon/57.png">
    <link rel="shortcut icon" href="<?php getSiteUrl(); ?>favicon.ico" type="image/x-icon">

    <title>Unaviva B2B - Ponuda</title>

    <meta name="title" content="Unaviva B2B - Ponuda">
    <meta name="keywords" content="">
    <meta name="description" content="">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php getSiteUrl(); ?>idkadmin/">
    <meta property="og:title" content="Unaviva B2B - Ponuda">
    <meta property="og:description" content="">
    <meta property="og:image" content="<?php getSiteUrl(); ?>img/seo.jpg">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="<?php getSiteUrl(); ?>idkadmin/">
    <meta property="twitter:title" content="Unaviva B2B - Ponuda">
    <meta property="twitter:description" content="">
    <meta property="twitter:image" content="<?php getSiteUrl(); ?>img/seo.jpg">

    <!-- Style -->
    <link rel="stylesheet" type="text/css" href="<?php getSiteUrl(); ?>idkadmin/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php getSiteUrl(); ?>idkadmin/css/jasny-bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php getSiteUrl(); ?>idkadmin/css/jquery-ui.min.css">
    <link rel="stylesheet" type="text/css" href="<?php getSiteUrl(); ?>idkadmin/css/calendar.css" />
    <link rel="stylesheet" type="text/css" href="<?php getSiteUrl(); ?>idkadmin/css/bootstrap-select.css" />
    <link rel="stylesheet" type="text/css" href="<?php getSiteUrl(); ?>idkadmin/css/jquery.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="<?php getSiteUrl(); ?>idkadmin/css/responsive.dataTables.min.css">
    <link rel="stylesheet" type="text/css" href="<?php getSiteUrl(); ?>idkadmin/css/responsive.bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="<?php getSiteUrl(); ?>idkadmin/css/jquery.fancybox.css">
    <link rel="stylesheet" type="text/css" href="<?php getSiteUrl(); ?>idkadmin/js/ui/trumbowyg.min.css">
    <link rel="stylesheet" type="text/css" href="<?php getSiteUrl(); ?>idkadmin/css/select2.min.css">
    <!-- CDNs -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,700&amp;subset=latin-ext">
    <!-- Custom CSS -->
    <link rel="stylesheet" type="text/css" href="<?php getSiteUrl(); ?>idkadmin/css/style.css">
    <!-- Style END -->

    <!-- Scripts -->
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->

    <!-- jQuery -->
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/jquery-1.12.4.min.js"></script>
    <!-- CDNs -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Local JS -->
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/jquery-ui-1.12.4.min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/chart.min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/modernizr.custom.63321.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/jquery.calendario.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/jquery.slimscroll.min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/jquery.matchHeight-min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/jasny-bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/bootstrap-select.min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/langs/flatpickr_bs.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/dataTables.responsive.min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/responsive.bootstrap.min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/jquery.fancybox.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/jquery.mask.min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/trumbowyg.min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/timeago.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/select2.min.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/ajaxGet.js"></script>
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/langs/hr.min.js"></script>
    <!-- CDNs -->
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
    <!-- Custom JS -->
    <script type="text/javascript" src="<?php getSiteUrl(); ?>idkadmin/js/scripts.js"></script>
    <!-- Scripts END -->

</head>

<body>

    <?php

    $offer_id = $_GET['id'];
    $offer_key = $_GET['offer'];

    $query = $db->prepare("
        SELECT t1.client_name, t1.client_business_type, t1.client_code, t1.client_id_number, t1.client_pdv_number, t1.client_address, t1.client_city, t1.client_postal_code, t1.client_country, t2.offer_id, t2.offer_status, t2.offer_images, t2.created_at, t2.offer_total_price, t2.offer_total_tax, t2.offer_total_rabat, t2.offer_to_pay, t2.employee_id
        FROM idk_client t1
        INNER JOIN idk_offer t2
        ON t1.client_id = t2.client_id
        WHERE t2.offer_id = :offer_id AND t2.offer_key = :offer_key");

    $query->execute(array(
        ':offer_id' => $offer_id,
        ':offer_key' => $offer_key
    ));

    $client = $query->fetch();

    $client_name = $client['client_name'];
    $client_business_type = $client['client_business_type'];
    $client_code = $client['client_code'];
    $client_id_number = $client['client_id_number'];
    $client_pdv_number = $client['client_pdv_number'];
    $client_address = $client['client_address'];
    $client_city = $client['client_city'];
    $client_postal_code = $client['client_postal_code'];
    $client_country = $client['client_country'];
    $offer_id = $client['offer_id'];
    $offer_created_at = $client['created_at'];
    $offer_status = $client['offer_status'];
    $offer_total_price = $client['offer_total_price'];
    $offer_total_tax = $client['offer_total_tax'];
    $offer_total_rabat = $client['offer_total_rabat'];
    $offer_to_pay = $client['offer_to_pay'];
    $offer_images = $client['offer_images'];
    $employee_id = $client['employee_id'];

    $select_employee_query = $db->prepare("
		SELECT employee_first_name, employee_last_name
		FROM idk_employee
		WHERE employee_id = :employee_id");

    $select_employee_query->execute(array(
        ':employee_id' => $employee_id
    ));

    $select_employee_row = $select_employee_query->fetch();

    $employee_first_name = $select_employee_row['employee_first_name'];
    $employee_last_name = $select_employee_row['employee_last_name'];

    $select_query = $db->prepare("
		SELECT od_data
		FROM idk_client_otherdata
		WHERE od_group = :od_group AND od_value = :od_value");

    $select_query->execute(array(
        ':od_group' => 1,
        ':od_value' => $client_business_type
    ));

    $select_row = $select_query->fetch();

    $client_business_type_echo = $select_row['od_data'];

    $number_of_rows = $select_query->rowCount();

    if ($number_of_rows !== 0) {

        $owner_query = $db->prepare("
            SELECT owner_name, owner_image
            FROM idk_owner");

        $owner_query->execute();

        $owner = $owner_query->fetch();

        $owner_name = $owner['owner_name'];
        $owner_image = $owner['owner_image'];

    ?>

        <div id="print_header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-xs-6">
                        <h3>Ponuda/Predračun #<?php echo $offer_id; ?></h3>
                        <p class="idk_margin_top30">
                            <strong>Klijent</strong> <br>
                            <!-- <?php //echo $client_name . " " . $client_business_type_echo; ?> -->
                            <?php echo $client_name; ?>
                            <!-- <?php //if (isset($client_code) and $client_code != "") { ?>
                                <br>
                                <strong>Šifra klijenta:</strong> <br>
                                <?php //echo $client_code; ?>
                            <?php //} ?> -->
                            <?php if (isset($client_id_number) and $client_id_number != "") { ?>
                                <br>
                                <strong>ID broj klijenta:</strong> <br>
                                <?php echo $client_id_number; ?>
                            <?php } ?>
                            <?php if (isset($client_pdv_number) and $client_pdv_number != "") { ?>
                                <br>
                                <strong>PDV broj klijenta:</strong> <br>
                                <?php echo $client_pdv_number; ?>
                            <?php } ?>
                            <br>
                            <strong>Datum ponude/predračuna:</strong> <br>
                            <?php echo date('d.m.Y.', strtotime($offer_created_at)); ?> <br>
                            <strong>Komercijalista</strong> <br>
                            <?php echo "${employee_first_name} ${employee_last_name}"; ?> <br>
                        </p>
                    </div>
                    <div class="col-xs-6 text-right">
                        <img src="<?php getSiteUrl(); ?>idkadmin/files/owners/images/<?php echo $owner_image; ?>" class="idk_print_logo" alt="<?php echo $owner_name; ?> logo">
                        <p class="idk_margin_top30">
                            <strong>Unaviva d.o.o.</strong> <br>
                            Dr. Irfana Ljubijankića 87 <br>
                            77000 Bihać <br>
                            Tel: 00 387 37 961 131 <br>
                            E-Mail: info@unaviva.ba <br>
                            Web: www.unaviva.ba <br>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid idk_margin_top50">
            <div class="row">
                <div class="col-xs-12">

                    <table width="100%" class="tg">
                        <thead>
                            <tr>
                                <?php if ($offer_images == 1) { ?>
                                    <th width="10%" class="text-center tg-3och">Slika</th>
                                    <th width="30%" class="tg-kj9p">Proizvod</th>
                                    <th width="10%" class="text-center tg-3och">Kol</th>
                                    <th width="10%" class="text-right tg-nj7c">Jed. VPC</th>
                                    <th width="10%" class="text-right tg-nj7c">Jed. rabat</th>
                                    <th width="30%" class="text-right tg-nj7c">Iznos</th>
                                <?php } else { ?>
                                    <th width="40%" class="tg-kj9p">Proizvod</th>
                                    <th width="10%" class="text-center tg-3och">Kol</th>
                                    <th width="10%" class="text-right tg-nj7c">Jed. VPC</th>
                                    <th width="10%" class="text-right tg-nj7c">Jed. rabat</th>
                                    <th width="30%" class="text-right tg-nj7c">Iznos</th>
                                <?php } ?>
                            </tr>
                        </thead>
                        <tbody>

                            <!-- Get data for product -->
                            <?php

                            $query = $db->prepare("
                                SELECT *
                                FROM idk_product_offer
                                WHERE offer_id = :offer_id");

                            $query->execute(array(
                                ':offer_id' => $offer_id
                            ));

                            while ($product = $query->fetch()) {

                                $product_id = $product['product_id'];
                                $product_name = $product['product_name'];
                                $product_quantity = $product['product_quantity'];
                                $product_unit = $product['product_unit'];
                                $product_price = $product['product_price'];
                                $product_currency = $product['product_currency'];
                                $product_tax_name = $product['product_tax_name'];
                                $product_tax_percentage = $product['product_tax_percentage'];
                                $product_tax_value = $product['product_tax_value'];
                                $product_rabat_percentage = $product['product_rabat_percentage'];
                                $product_rabat_value = $product['product_rabat_value'];
                                $product_total_price = $product_price * $product_quantity;

                            ?>

                                <tr>
                                    <!-- Get product image -->
                                    <?php
                                    $query_product_image = $db->prepare("
                                        SELECT product_image
                                        FROM idk_product
                                        WHERE product_id = :product_id");

                                    $query_product_image->execute(array(
                                        ':product_id' => $product_id
                                    ));

                                    $product_image_row = $query_product_image->fetch();

                                    $product_image = isset($product_image_row['product_image']) ? $product_image_row['product_image'] : "none.jpg";
                                    ?>
                                    <?php if ($offer_images == 1) { ?>
                                        <td width="10%" class="text-center tg-deay">
                                            <img class="idk_offer_product_img" src="<?php getSiteUrl(); ?>idkadmin/files/products/images/<?php echo $product_image; ?>">
                                        </td>
                                        <td width="30%" class="tg-2qw4"><?php echo $product_name; ?></td>
                                        <td width="10%" class="text-center tg-deay"><?php echo $product_quantity; ?></td>
                                        <td width="10%" class="text-right tg-sfdd"><?php echo number_format($product_price, 3, ',', '.'); ?></td>
                                        <td width="10%" class="text-right tg-sfdd">
                                            <?php if ($product_rabat_percentage and $product_rabat_value) {
                                                echo number_format($product_rabat_value, 3, ',', '.') . " (" . number_format($product_rabat_percentage, 2, ',', '.') . "%)";
                                            } else {
                                                echo "0,00 (0,00%)";
                                            } ?>
                                        </td>
                                        <td width="30%" class="text-right tg-nj7c"><?php echo number_format($product_total_price, 3, ',', '.'); ?></td>
                                    <?php } else { ?>
                                        <td width="40%" class="tg-2qw4"><?php echo $product_name; ?></td>
                                        <td width="10%" class="text-center tg-deay"><?php echo $product_quantity; ?></td>
                                        <td width="10%" class="text-right tg-sfdd"><?php echo number_format($product_price, 3, ',', '.'); ?></td>
                                        <td width="10%" class="text-right tg-sfdd"><?php echo number_format($product_rabat_value, 3, ',', '.'); ?></td>
                                        <td width="30%" class="text-right tg-nj7c"><?php echo number_format($product_total_price, 3, ',', '.'); ?></td>
                                    <?php } ?>
                                </tr>

                            <?php } ?>

                            <tr>
                                <?php if ($offer_images == 1) { ?>
                                    <th colspan="5" class="text-right tg-ofj5">Rabat</th>
                                <?php } else { ?>
                                    <th colspan="4" class="text-right tg-ofj5">Rabat</th>
                                <?php } ?>
                                <th class="text-right tg-nj7c"><?php echo number_format($offer_total_rabat, 3, ',', '.'); ?> KM</th>
                            </tr>
                            <tr>
                                <?php if ($offer_images == 1) { ?>
                                    <th colspan="5" class="text-right tg-ofj5">Iznos bez PDV-a</th>
                                <?php } else { ?>
                                    <th colspan="4" class="text-right tg-ofj5">Iznos bez PDV-a</th>
                                <?php } ?>
                                <th class="text-right tg-nj7c"><?php echo number_format($offer_total_price, 3, ',', '.'); ?> KM</th>
                            </tr>
                            <tr>
                                <?php if ($offer_images == 1) { ?>
                                    <th colspan="5" class="text-right tg-ofj5">PDV 17%</th>
                                <?php } else { ?>
                                    <th colspan="4" class="text-right tg-ofj5">PDV 17%</th>
                                <?php } ?>
                                <th class="text-right tg-nj7c"><?php echo number_format($offer_total_tax, 3, ',', '.'); ?> KM</th>
                            </tr>
                            <tr>
                                <?php if ($offer_images == 1) { ?>
                                    <th colspan="5" class="text-right tg-ofj5">Ukupno</th>
                                <?php } else { ?>
                                    <th colspan="4" class="text-right tg-ofj5">Ukupno</th>
                                <?php } ?>
                                <th class="text-right tg-nj7c"><?php echo number_format($offer_to_pay, 2, ',', '.'); ?> KM</th>
                            </tr>
                        </tbody>
                        <tfoot class="print_footer">
                            <tr>
                                <?php if ($offer_images == 1) { ?>
                                    <th colspan="6">
                                        <p class="idk_margin_top20"><em>Dokument je obrađen elektronskim putem i važeći je bez potpisa i pečata.</em></p>
                                    </th>
                                <?php } else { ?>
                                    <th colspan="5">
                                        <p class="idk_margin_top20"><em>Dokument je obrađen elektronskim putem i važeći je bez potpisa i pečata.</em></p>
                                    </th>
                                <?php } ?>
                            </tr>
                        </tfoot>
                    </table>

                </div>
            </div>
        </div>

        <!-- <div class="container-fluid">
            <div class="row">
                <div class="col-xs-12">
                    <p class="idk_margin_top20"><em>Dokument je obrađen elektronskim putem i važeći je bez potpisa i pečata.</em></p>
                </div>
            </div>
        </div> -->

    <?php } else { ?>
        <h1>Nema rezultata!</h1>
    <?php } ?>

    <script>
        window.print();
    </script>

</body>

</html>