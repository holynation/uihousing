<?php $userType = (!isset($userType)) ? $webSessionManager->getCurrentUserProp('user_type') : $userType ;
$dashLink = '';
$dashName = "Dashboard";
$dashIcon = "bx-home-circle";
if($userType == 'staff'){
    $dashLink = 'vc/staff/dashboard';
    $dashName = 'Dashboard';
} else if($userType == 'admin'){
    $dashLink = 'vc/admin/dashboard';
}
?>

<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="<?php echo $dashLink; ?>" class="app-brand-link">
            <span class="app-brand-logo demo">
              <a href="<?php echo base_url($dashLink); ?>" class="logo-link">
                  <img class="logo-img logo-img-lg" src="<?php echo base_url('assets/img/logo/logo.png'); ?>" alt="logo" style="width:4rem;height:4rem;margin:0 auto;">
              </a>
            </span>
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
          <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item mb-3 active">
          <a href="<?php echo base_url($dashLink); ?>" class="menu-link">
            <i class="menu-icon tf-icons bx <?php echo $dashIcon; ?>"></i>
            <div data-i18n="Dashboard">Dashboard</div>
          </a>
        </li>

        <?php if($userType == 'admin'){  ?>
            <?php 
              if(isset($canView)){
                foreach ($canView as $key => $value): ?>
               <?php 
                   $state='';
                    if ($canView[$key]['state']===0) {
                     continue;
                   }
            ?>
        <!-- Layouts -->
        <li class="menu-item mb-2">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx <?php echo $value['class']; ?>"></i>
                <div data-i18n="Layouts"><?php echo $key; ?></div>
            </a>

            <ul class="menu-sub">
                <?php foreach ($value['children'] as $label =>$link): ?>
                <li class="menu-item">
                  <a href="<?php echo base_url($link); ?>" class="menu-link">
                    <div data-i18n="<?php echo $label; ?>"><?php echo $label; ?></div>
                  </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <?php endforeach; } } ?>

        <?php if($userType == 'staff'): ?>
            <li class="menu-item mb-3">
              <a href="<?php echo base_url('vc/staff/apply'); ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-notepad"></i>
                <div data-i18n="Apply">House Apply</div>
              </a>
            </li>
            <li class="menu-item mb-3">
              <a href="<?php echo base_url('vc/staff/tenant'); ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-plus"></i>
                <div data-i18n="Tenant">Tenant</div>
              </a>
            </li>
            <li class="menu-item mb-3">
              <a href="<?php echo base_url('vc/staff/profile'); ?>" class="menu-link">
                <i class="menu-icon tf-icons bx bx-user-circle"></i>
                <div data-i18n="Profile">Profile</div>
              </a>
            </li>
        <?php endif; ?>
    </ul>
</aside>