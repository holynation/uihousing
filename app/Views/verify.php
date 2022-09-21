<!DOCTYPE html>
<html
  lang="en"
  class="light-style customizer-hide"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="<?php echo base_url("assets/") ?>"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Verification Equipro</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo base_url("assets/img/logo/favicon.png"); ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/core.css"); ?>" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/theme-default.css"); ?>" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo base_url("assets/css/demo.css"); ?>" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/pages/page-auth.css"); ?>" />
    <!-- Helpers -->
    <script src="<?php echo base_url("assets/vendor/js/helpers.js"); ?> "></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?php echo base_url("assets/js/config.js"); ?> "></script>
  </head>

  <body>
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner">
          <!-- Register -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand justify-content-center mb-0">
                <a href="<?php echo base_url('/'); ?>" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <a href="<?php echo base_url('/'); ?>" class="logo-link">
                        <img class="logo-img logo-img-lg" src="<?php echo base_url('assets/img/logo/logo.png'); ?>" alt="logo">
                    </a>
                  </span>
                </a>
              </div>
              <!-- /Logo -->

              <!-- this is the notification div for verify_account -->
                <?php  if(isset($type) && $type == 'verify_account'){ ?>
                <h3 class="text-center">Account Verification Page</h3>
                <?php if(isset($success)): ?>
                <div class="alert alert-success">
                  <p class="text-center mt-2" style="font-size:17.5px;"><?php echo $success; ?></p>
                </div>
                <?php endif; } ?>
              <!-- end verify_account -->

              <!-- using this to display response coming as JSON type -->
                <?php  if(isset($type) && $type == 'verify_transaction'){
                    $response = json_decode($response);
                ?>
                  <?php if($response->status){ ?>
                      <div class="alert alert-success">
                        <p class="text-center mt-2" style="font-size:18px;">
                          Your transaction was successful. Kindly close the window
                        </p>
                      </div>
                  <?php }else{ ?>
                      <div class="alert alert-danger">
                        <p class="text-center mt-3" style="font-size:18px;">
                          <?php echo $response->message; ?>
                        </p>
                      </div>
                  <?php } ?>

                <?php } ?>
              <!-- end display response as JSON -->

              <!-- error notification -->
                <?php if(isset($error)): ?>
                <div class="alert alert-danger">
                  <p class="text-center mt-3"><?php echo $error; ?></p>
                </div>
                <?php endif;  ?>
              <!-- end error notification -->
            </div>
          </div>
          <!-- /Register -->
        </div>
      </div>
    </div>

    <!-- / Content -->

    <script src="<?php echo base_url("assets/vendor/libs/jquery/jquery.js"); ?> "></script>
    <script src="<?php echo base_url("assets/vendor/libs/popper/popper.js"); ?> "></script>
    <script src="<?php echo base_url("assets/vendor/js/bootstrap.js"); ?> "></script>
    <!-- endbuild -->
  </body>
</html>
