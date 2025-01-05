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

?>

<!DOCTYPE html>
<html lang="bs">

<head>

  <?php include('includes/head.php'); ?>

</head>

<body>

  <!-- Header -->
  <header id="login_header" class="header">
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
          <a href="<?php getSiteUrl(); ?>">
            <img src="<?php getSiteUrl(); ?>idkadmin/files/owners/images/<?php echo $owner_image; ?>" alt="<?php echo $owner_name; ?> logo">
          </a>
        </div>
      </div>
    </div>
  </header> <!-- End header -->

  <!-- Main -->
  <main>

    <!-- Login inputs section -->
    <section id="idk_login_section">
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
              echo '<div class="alert material-alert material-alert_success">Uspješno ste se odjavili.</div>';
            } elseif ($mess == 2) {
              echo '<div class="alert material-alert material-alert_danger">Greška: Email/korisničko ime ili lozinka nisu validni!</div>';
            }
            ?>

            <!-- Form - login -->
            <form action="<?php getSiteUrl(); ?>do.php?form=login" method="post" role="form">
              <div class="form-group">
                <label class="sr-only" for="login_email_or_username">Korisničko ime ili email</label>
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text"><span class="lnr lnr-user"></span></div>
                  </div>
                  <input type="text" class="form-control" name="login_email_or_username" id="login_email_or_username" placeholder="Korisničko ime ili email" required>
                </div>
              </div>
              <div class="form-group">
                <label class="sr-only" for="login_password">Lozinka</label>
                <div class="input-group mb-2">
                  <div class="input-group-prepend">
                    <div class="input-group-text"><span class="lnr lnr-lock"></span></div>
                  </div>
                  <input type="password" name="login_password" class="form-control" id="login_password" placeholder="Lozinka" required>
                </div>
              </div>
              <div class="form-group">
                <div class="main-container__column material-checkbox-group material-checkbox-group_primary">
                  <input type="checkbox" id="checkbox2" name="login_rm" value="1" class="material-checkbox">
                  <label class="material-checkbox-group__label" for="checkbox2" id="checkboxLabel">Zapamti me</label>
                </div>
              </div>

              <!-- Include foot.php so we can use jQuery -->
              <?php include('includes/foot.php'); ?>

              <script>
                $(document).ready(function() {
                  $("#checkbox2").click(function() {

                    $('#checkbox2').attr('checked', function(index, attr) {
                      return attr == 'checked' ? false : 'checked';
                    });

                  });
                });
              </script>
              <button type="submit" class="btn idk_btn btn-block">ULAZ</button>
            </form><!-- End form - login -->

            <p class="text-center">
              Postani naš kupac! <a href="contact_us">Kontaktirajte nas</a>
            </p>

          </div>
        </div>
      </div>
    </section> <!-- End login inputs section -->

  </main> <!-- End main -->

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