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

    <title>Login UIHousing</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo base_url("assets/img/logo/favicon.ico"); ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/fonts/boxicons.css"); ?>" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/core.css"); ?>" class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/theme-default.css"); ?>" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo base_url("assets/css/demo.css"); ?>" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"); ?>" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/pages/page-auth.css"); ?>" />
    <!-- Helpers -->
    <script src="<?php echo base_url("assets/vendor/js/helpers.js"); ?> "></script>
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
              <div class="app-brand justify-content-center">
                <a href="<?php echo base_url('/'); ?>" class="app-brand-link gap-2">
                  <span class="app-brand-logo demo">
                    <a href="<?php echo base_url('/'); ?>" class="logo-link">
                        <img class="logo-img logo-img-lg" src="<?php echo base_url('assets/img/logo/logo.png'); ?>" alt="logo">
                    </a>
                  </span>
                </a>
              </div>
              <!-- /Logo -->
              <h4 class="mb-2">Welcome to UIHOUSING App! 👋</h4>
              <p class="mb-4">Please sign-in to your account and enjoy your biddings</p>

                <?php echo form_open("auth/web", array('class' => 'mb-3', 'id' => 'loginForm')); ?>
                <?= csrf_field(); ?>
                <!-- this is the notification section -->
                <div id="notify"></div>
                <!-- end notification -->

                <div class="mb-3">
                  <label for="email" class="form-label">Email or Username</label>
                  <input
                    type="text"
                    class="form-control"
                    id="email"
                    name="email"
                    placeholder="Enter your email or username"
                    autofocus
                  />
                </div>
                <div class="mb-3 form-password-toggle">
                  <div class="d-flex justify-content-between">
                    <label class="form-label" for="password">Password</label>
                    <a href="<?php echo base_url('forget_password'); ?>">
                      <small>Forgot Password?</small>
                    </a>
                  </div>
                  <div class="input-group input-group-merge">
                    <input
                      type="password"
                      id="password"
                      class="form-control"
                      name="password"
                      placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                      aria-describedby="password"
                    />
                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                  </div>
                </div>
                <div class="mb-3">
                </div>
                <input type="hidden" name="isajax" value="true">
                <input type="hidden" id='base_path' value="<?php echo base_url(); ?>">
                <div class="mb-3">
                  <button class="btn btn-primary d-grid w-100" type="submit" id="btnLogin">Sign in</button>
                </div>
              </form>
              <p class="text-center">
                <span>New on our platform?</span>
                <a href="<?php echo base_url('register'); ?>">
                  <span>Create an account</span>
                </a>
              </p>
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
    <script src="<?php echo base_url("assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"); ?> "></script>

    <script src="<?php echo base_url("assets/vendor/js/menu.js"); ?> "></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="<?php echo base_url("assets/js/main.js"); ?> "></script>
    <script src="<?php echo base_url('assets/js/custom.js'); ?>"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var form = $('#loginForm');
            var note = $("#notify");
            note.text('').hide();

            form.submit(function(event) {
                event.preventDefault();
                loginStatus = true;
                $("#btnLogin").html("Authenticating...").addClass('disabled').prop('disabled', true);
                submitAjaxForm($(this));
                // $("#btnLogin").removeClass("disabled").removeAttr('disabled').html("Sign in");
            });
        });

        function ajaxFormSuccess(target, data) {
            // using this to track ajax login auth
            if (loginStatus) {
                $("#notify").text('').show();
                if (data.status) {
                    var path = data.message;
                    location.assign(path);
                } else {
                    $("#btnLogin").removeClass("disabled").removeAttr('disabled').html("Sign in");
                    $("#notify").text(data.message).addClass("alert alert-danger alert-dismissible show text-center").css({
                        "font-size": "12.368px"
                    });
                }
            }
        }
    </script>
  </body>
</html>
