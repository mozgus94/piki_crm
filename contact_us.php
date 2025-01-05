<!--
	Made by:
	                                          .*****,..
                                              .*********,
                                              ,*********,
                                              **********          .((/
                                             ***********       .((((((((/
                                            .**********.     *(((((((((((((.
                                            ***********    *(((((((((((((((((.
                                          .***********   ,(((((((((((((((.
                                         ,***********   ((((((((((((((,
                                        ************. .(((((((((((((
                                      (/***********  (((((((((((((
                                    (((/**********  ((((((((((((.
                                 .(((((/********.  ((((((((((((
                              ,((((((((********   *(((((((((((
                         ./((((((((((((*****,    .(((((((((((
       (((((((((((((((((((((((((((((((/***,      (((((((((((
       (((((((((((((((((((((((((((((((**.       ,((((((((((.
      *((((((((((((((((((((((((((((((.          (((((((####
      (((((((((((((((((((((((((((*              ((((#####%,
      ((((((((((((((((((((((*.                  ((###%###%
           ...,,,,..                           .#########%
       ./(############(*.                      .#########%
      %#####################%#,                 ##########.
      ############################(             ##########*
      ,##############################(.         (%#########
       ###############################/(/       .##########,
       /##(*,......,/##################((((,     (########%%.
                           ,###########/(((((,    %#########%
                               *#######((((((((.  .%#########%
                                  *####(((((((((/  /%%########%*
                                     ###((((((((((. .%###########
                                       ((((((((((((. .%#########%%#
                                        ,(((((((((((*  ##########%##(
                                         .(((((((((((.  /%#############,
                                           /((((((((((,   (#############%%#.
                                            (((((((((((     #%############%#,
                                            .((((((((((*      #%########%#*
                                             *((((((((((         #%%%%#(
                                              ((((((((((.          .*
                                              /(((((((((*
                                              ,(((((((((/
-->
<?php
include("includes/functions.php");

// Ne ide common.php zato sto ne mora biti logiran da posalje upit
// include("includes/common.php");

?>

<!DOCTYPE html>
<html lang="bs">

<head>

    <?php include('includes/head.php'); ?>

</head>

<body>

    <!-- Header -->
    <header id="contact_us_header" class="header">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-12 text-center idk_logo_wrapper">

                    <!-- Get owner name from db -->
                    <?php
                    $owner_query = $db->prepare("
                        SELECT owner_id, owner_name, owner_image
                        FROM idk_owner");

                    $owner_query->execute();

                    $owner = $owner_query->fetch();

                    $owner_id = $owner['owner_id'];
                    $owner_name = $owner['owner_name'];
                    $owner_image = $owner['owner_image'];

                    ?>

                    <a href="<?php getSiteUrl(); ?>"><img class="idk_logo_small" src="<?php getSiteUrl(); ?>idkadmin/files/owners/images/<?php echo $owner_image; ?>" alt="<?php echo $owner_name; ?> logo"></a>
                </div>
            </div>
        </div>

        <div class="idk_back_arrow">
            <a href="javascript: history.go(-1)">
                <span class="lnr lnr-arrow-left"></span>
            </a>
        </div>
    </header> <!-- End header -->

    <!-- Main -->
    <main>

        <!-- Contact us inputs section -->
        <section id="idk_contact_us_section">
            <div class="container">
                <div class="row">
                    <div class="col-12">

                        <?php
                        if (isset($_GET['mess'])) {
                            $mess = $_GET['mess'];
                        } else {
                            $mess = 0;
                        }

                        if ($mess == 1) {
                            echo '<div class="alert material-alert material-alert_success">Hvala! Vaša poruka je uspješno poslana.</div>';
                        } elseif ($mess == 2) {
                            echo '<div class="alert material-alert material-alert_danger">Greška: Polja označena * su obavezna</div>';
                        }
                        ?>

                        <!-- Contact form -->
                        <form action="<?php getSiteUrl(); ?>do.php?form=send_contact_us_message" method="POST">
                            <div class="form-group">
                                <label class="sr-only" for="contact_name">Vaše ime*</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><span class="lnr lnr-user"></span></div>
                                    </div>
                                    <input type="text" class="form-control" name="contact_name" id="contact_name" placeholder="Vaše ime*" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="contact_tel">Kontakt telefon</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><span class="lnr lnr-phone-handset"></span></div>
                                    </div>
                                    <input type="text" class="form-control" name="contact_tel" id="contact_tel" placeholder="Kontakt telefon">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="sr-only" for="contact_email">Kontakt email*</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><span class="lnr lnr-envelope"></span></div>
                                    </div>
                                    <input type="email" class="form-control" name="contact_email" id="contact_email" placeholder="Kontakt email*" required>
                                </div>
                            </div>
                            <div class="form-group idk_textarea_form_group">
                                <label class="sr-only" for="contact_message">Vaša poruka*</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><span class="lnr lnr-pencil"></span></div>
                                    </div>
                                    <textarea class="form-control" id="contact_message" class="form-control" name="contact_message" rows="3" placeholder="Vaša poruka*"></textarea>
                                </div>
                            </div>
                            <style>
                                .dispnon {
                                    display: none
                                }
                            </style>
                            <div class="form-group">
                                <input class="dispnon" class="form-control" name="field_name_honey" type="text">
                            </div>
                            <button type="submit" class="btn idk_btn btn-block">POŠALJITE UPIT</button>
                        </form> <!-- End contact form -->

                    </div>
                </div>
            </div>
        </section> <!-- End contact us inputs section -->

    </main><!-- End main -->

    <!-- Footer -->
    <footer id="footer">
        <div class="container">
            <div class="row">
                <div class="col-4">
                    <p>&copy; 2021</p>
                </div>
                <div class="col-8 text-right">
                    <p>Powered by IDK B2B</p>
                </div>
            </div>
        </div>
    </footer>
    <!-- End footer -->

</body>

</html>