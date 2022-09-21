<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="<?php echo base_url("assets/"); ?>"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    <title>Dashboard - Equipro</title>
    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo base_url('assets/img/logo/favicon.png'); ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />
    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/fonts/boxicons.css"); ?> " />
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/core.css"); ?> " class="template-customizer-core-css" />
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/css/theme-default.css"); ?> " class="template-customizer-theme-css" />
    <link rel="stylesheet" href="<?php echo base_url("assets/css/demo.css"); ?> " />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="<?php echo base_url("assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"); ?> " />

    <!-- Morris chart -->
    <link rel="stylesheet" href="<?php echo base_url('assets/vendor/libs/morris/morris.css'); ?>">
    
    <!-- Page CSS -->
    <link href="<?php echo base_url('assets/vendor/libs/toastr/toastr.css'); ?>" rel="stylesheet" type="text/css">
    <link rel="stylesheet" type="text/css" href="<?php echo base_url('assets/vendor/libs/datatables/dataTables.bootstrap4.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url("assets/css/custom.css"); ?> " />
    <style type="text/css">
        #notification{
            display: none;
            position: absolute;
            width: 50%;
            z-index: 4000;
        }
        select{
            wdith:100%;
            display: block;
        }
    </style>

    <!-- Helpers -->
    <script src="<?php echo base_url("assets/vendor/js/helpers.js"); ?> "></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js  in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="<?php echo base_url("assets/js/config.js"); ?> "></script>
    <script src="<?php echo base_url("assets/vendor/libs/jquery/jquery.js"); ?> "></script>
  </head>

  <body>
    <div id="notification" class="alert alert-dismissable text-center"></div>
    <input type="hidden" value="<?php echo base_url(); ?>" id='baseurl'>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            <!-- Menu -->
            <?php include_once ROOTPATH."template/nav.php"; ?>
            <!-- / Menu -->

            <!-- Layout container -->
            <div class="layout-page">
              <!-- Navbar -->
                <nav
                    class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                    id="layout-navbar"
                >
                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                        <i class="bx bx-menu bx-sm"></i>
                      </a>
                    </div>

                    <?php 
                        $firstname = $webSessionManager->getCurrentUserProp('firstname');
                        $lastname = ($webSessionManager->getCurrentUserProp('lastname')) ? $webSessionManager->getCurrentUserProp('lastname') : $webSessionManager->getCurrentUserProp('surname');
                         $fullname = $firstname.' '.$lastname;
                    ?>
                    <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                      <div class="nav-item d-flex align-items-center">
                        <h4 class="my-3">Admin Dashboard</h4>
                      </div>
                      <ul class="navbar-nav flex-row align-items-center ms-auto">
                        <!-- User -->
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                            <div class="avatar avatar-online mb-2">
                              <!-- <img src="<?php //echo base_url("assets/img/avatars/1.png"); ?> " alt class="w-px-40 h-auto rounded-circle" /> -->
                              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" style="fill: rgba(105, 122, 141);transform: ;msFilter:;padding-top: 0.8rem;"><path d="M7.5 6.5C7.5 8.981 9.519 11 12 11s4.5-2.019 4.5-4.5S14.481 2 12 2 7.5 4.019 7.5 6.5zM20 21h1v-1c0-3.859-3.141-7-7-7h-4c-3.86 0-7 3.141-7 7v1h17z"></path></svg>
                            </div>
                          </a>
                          
                          <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                              <a class="dropdown-item" href="#">
                                <div class="d-flex">
                                  <div class="flex-shrink-0 me-3">
                                    <div class="avatar avatar-online">
                                     <!--  <img src="<?php //echo base_url("assets/img/avatars/1.png"); ?>" alt class="w-px-40 h-auto rounded-circle" /> -->
                                      <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" style="fill: rgba(105, 122, 141);transform: ;msFilter:;padding-top: 0.8rem;"><path d="M7.5 6.5C7.5 8.981 9.519 11 12 11s4.5-2.019 4.5-4.5S14.481 2 12 2 7.5 4.019 7.5 6.5zM20 21h1v-1c0-3.859-3.141-7-7-7h-4c-3.86 0-7 3.141-7 7v1h17z"></path></svg>
                                    </div>
                                  </div>
                                  <div class="flex-grow-1">
                                    <span class="fw-semibold d-block"><?php echo $fullname ?? ""; ?></span>
                                    <small class="text-muted">Admin</small>
                                  </div>
                                </div>
                              </a>
                            </li>
                            <li>
                              <div class="dropdown-divider"></div>
                            </li>
                            <li>
                              <a class="dropdown-item" href="<?php echo base_url('logout'); ?>">
                                <i class="bx bx-power-off me-2"></i>
                                <span class="align-middle">Log Out</span>
                              </a>
                            </li>
                          </ul>
                        </li>
                        <!--/ User -->
                      </ul>
                    </div>
                </nav>
              <!-- / Navbar -->

                <!-- Content wrapper -->
                <div class="content-wrapper">