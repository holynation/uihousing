<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <meta charset="utf-8">
    <meta name="author" content="Daabo">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Dashboard for Daabo Device Protection">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="./assets/Daabo icon.png">
    <!-- Page Title  -->
    <title>Login | Daabo Device Protection</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="./assets/css/dashlite.css">
</head>

<body class="nk-body bg-white npc-default pg-auth">
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- wrap @s -->
            <div class="nk-wrap nk-wrap-nosidebar">
                <!-- content @s -->
                <div class="nk-content ">
                    <div class="nk-split nk-split-page nk-split-md">
                        <div class="nk-split-content nk-block-area nk-block-area-column nk-auth-container bg-white">
                            <div class="absolute-top-right d-lg-none p-3 p-sm-5">
                                <a href="#" class="toggle btn-white btn btn-icon btn-light" data-target="athPromo"><em class="icon ni ni-info"></em></a>
                            </div>
                            <div class="nk-block nk-block-middle nk-auth-body">
                                <div class="brand-logo pb-5">
                                    <a href="index.html" class="logo-link">
                                        <img class="logo-light logo-img logo-img-lg" src="<?php echo base_url(); ?>assets/public/daabo.png" srcset="<?php echo base_url(); ?>assets/images/logo2x.png 2x" alt="logo">
                                        <img class="logo-dark logo-img logo-img-lg" src="<?php echo base_url(); ?>assets/public/daabo.png" alt="logo-dark">
                                    </a>
                                </div>
                                <!-- this is the notification div -->
                                  <?php  if(isset($type) && $type == 'verify_account'){ ?>
                                  <h3 class="text-center">Account Verification Page</h3><br/>
                                  <?php if(isset($success)): ?>
                                  <div class="alert alert-success">
                                    <p class="text-center mt-2" style="font-size:18px;"><?php echo $success; ?></p>
                                  </div>
                                  <div class="text-center">
                                    <a href="<?php echo base_url(); ?>" class="btn btn-primary">Now Login</a>
                                  </div>
                                  <?php endif; } ?>

                                  <?php if(isset($error)): ?>
                                  <div class="alert alert-danger">
                                    <p class="text-center mt-3"><?php echo $error; ?></p>
                                  </div>
                                  <?php endif;  ?>
                                <?php if(isset($type) && $type == 'forget'){ ?>
                                <div class="nk-block-head mb-2">
                                    <div class="nk-block-head-content">
                                        <h5 class="nk-block-title">Change Password</h5>
                                    </div>
                                </div><!-- .nk-block-head -->
                                <!-- this is the notification section -->
                                <div id="notify"></div> 
                                <!-- end notification -->
                                <form action="<?php echo base_url('auth/forgetPassword'); ?>" method="post" role="form" id="signForm">
                                    <?php if(isset($email_hash, $email_code)) { ?>
                                    <input type="hidden" name="email_hash" id="email_hash" value="<?php echo $email_hash; ?>" />
                                    <input type="hidden" name="email_code" id="email_code" value="<?php echo $email_code; ?>" />
                                    <?php  } ?>
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label">Email</label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <input type="email" class="form-control form-control-lg" placeholder="Email Address" name="email" id="email" value="<?php echo (isset($email)) ? $email : '';?>" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="password">New Password</label>
                                        </div>
                                        <div class="form-control-wrap">
                                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Enter new password">
                                        </div>
                                    </div><!-- .foem-group -->
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="confirm_password">Confirm Password</label>
                                            
                                        </div>
                                        <div class="form-control-wrap">
                                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="confirm_password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" class="form-control form-control-lg" name="confirm_password" id="confirm_password" placeholder="Confirm new password">
                                        </div>
                                    </div><!-- .foem-group -->
                                    <input type="hidden" name="isajax">
                                    <input type="hidden" id='base_path' value="<?php echo $base; ?>">
                                    <input type="hidden" name="task" value="update">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-lg btn-primary btn-block">Submit</button>
                                    </div>
                                </form><!-- form -->
                                <?php } ?>
                                
                            </div><!-- .nk-block -->
                            <div class="nk-block nk-auth-footer">
                                <div class="nk-block-between">
                                    <ul class="nav nav-sm">
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo base_url('terms'); ?>">Terms & Condition</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo base_url('privacy_policy'); ?>">Privacy Policy</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="<?php echo base_url('faq'); ?>">FAQs</a>
                                        </li>
                                        
                                    </ul><!-- nav -->
                                </div>
                                <div class="mt-3">
                                    <p>&copy; 2021 Daabo. All Rights Reserved.</p>
                                </div>
                            </div><!-- .nk-block -->
                        </div><!-- .nk-split-content -->
                        <div class="nk-split-content nk-split-stretch bg-abstract"></div><!-- .nk-split-content -->
                    </div><!-- .nk-split -->
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- JavaScript -->
    <script src="./assets/js/bundle.js"></script>
    <script src="./assets/js/scripts.js"></script>
</body>
</html>