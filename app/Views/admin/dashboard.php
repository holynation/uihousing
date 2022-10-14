<?php include_once ROOTPATH."template/header.php"; ?>

<!-- Content -->
<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-lg-4 col-md-6 col-6 mb-0">
      <div class="card">
        <div class="card-body">
          <span class="fw-semibold d-block mb-0">Total Approved Allocation</span>
          <h3 class="card-title mb-2"><?php echo number_format(@$countData['approvedAllocation']); ?></h3>
          <a href="<?php echo base_url("vc/admin/allocation"); ?>">
            <small class="text-primary fw-semibold">View Approved Allocation
              <i class="bx bx-up-arrow-alt"></i>
            </small>
          </a>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-6 mb-0">
      <div class="card">
        <div class="card-body">
          <span class="fw-semibold d-block mb-0">Total Applicant</span>
          <h3 class="card-title mb-2"><?php echo number_format(@$countData['applicant']); ?></h3>
          <a href="<?php echo base_url("vc/admin/applicant_allocation"); ?>">
            <small class="text-success fw-semibold">View Applied Applicant
              <i class="bx bx-up-arrow-alt"></i>
            </small>
          </a>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-6 mb-0">
      <div class="card">
        <div class="card-body">
          <span class="fw-semibold d-block mb-0">Staff</span>
          <h3 class="card-title mb-2"><?php echo number_format(@$countData['staff']); ?></h3>
          <a href="<?php echo base_url("vc/admin/view_model/staff"); ?>">
            <small class="text-info fw-semibold">Total Registered Staff
              <i class="bx bx-up-arrow-alt"></i>
            </small>
          </a>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-6 mb-0">
      <div class="card">
        <div class="card-body">
          <span class="fw-semibold d-block mb-0">Children</span>
          <h3 class="card-title mb-2"><?php echo @$countData['children']; ?></h3>
          <a href="<?php echo base_url("vc/create/children"); ?>">
            <small class="text-info fw-semibold">Total Registered Children
              <i class="bx bx-up-arrow-alt"></i>
            </small>
          </a>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-6 mb-0">
      <div class="card">
        <div class="card-body">
          <span class="fw-semibold d-block mb-0">Tenant</span>
          <h3 class="card-title mb-2"><?php echo number_format(@$countData['tenant']); ?></h3>
          <a href="<?php echo base_url("vc/create/tenant"); ?>">
            <small class="text-danger fw-semibold">Total Registered Tenant
              <i class="bx bx-up-arrow-alt"></i>
            </small>
          </a>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-6 col-6 mb-0">
      <div class="card">
        <div class="card-body">
          <span class="fw-semibold d-block mb-0">Departments</span>
          <h3 class="card-title mb-2"><?php echo number_format(@$countData['departments']); ?></h3>
          <a href="<?php echo base_url("vc/create/departments"); ?>">
            <small class="text-warning fw-semibold">Total Departments
              <i class="bx bx-up-arrow-alt"></i>
            </small>
          </a>
        </div>
      </div>
    </div>

    <!-- here is donut graph -->
    <div class="col-12 col-lg-12 order-2 order-md-3 order-lg-2 mb-4">
      <div class="card">
        <div class="row row-bordered g-0">
          <div class="col-md-6">
            <h5 class="card-header m-0 me-2 pb-3">Applicant Gender Chart</h5>
            <div class="chart has-fixed-height" id="gender-pie"></div>
          </div>
          <div class="col-md-6">
            <h5 class="card-header m-0 me-2 pb-3">Staff Academic Chart</h5>
            <div class="chart has-fixed-height" id="staff-pie"></div>
          </div>
        </div>
      </div>
    </div>

      <!-- Total Revenue -->
      <div class="col-12 col-lg-12 order-2 order-md-3 order-lg-2 mb-4">
        <div class="card">
          <div class="row row-bordered g-0">
            <div class="col-md-12">
              <h5 class="card-header m-0 me-2 pb-3">Current Applicant Distribution</h5>
              <div id="totalApplicantChart" class="px-2"></div>
            </div>
          </div>
        </div>
      </div>
  </div>
</div>
<!-- / Content & end for last graph-->

<?php include_once ROOTPATH."template/footer.php"; ?>
<script src="<?php echo base_url('assets/vendor/libs/morris/raphael-min.js') ?>"></script>
<script src="<?php echo base_url('assets/vendor/libs/morris/morris.min.js') ?>"></script>

<script>
  // addMoreEvent is loaded directly under custom.js
   function addMoreEvent() {
      loadApplicantChart();
      loadGenderChart();
      loadStaffStatusChart();
   }

   function loadApplicantChart() {
    var val = JSON.parse('<?php echo json_encode(@$applicantDistrix) ?>');
    if (val !== undefined){
      Morris.Bar({
        element: 'totalApplicantChart',
        data:val ,
        xkey: 'application_date',
        ykeys: ['total'],
        barColors: [
          '#5AB1EF',
          '#EB0F82',
          '#2EC7C9',
           '#B6A2DE',
           '#202A5A',
           '#5AB1EF',
           '#ffa9ce',
           '#f4aaa4',
           '#a5eed0',
           '#b695ff',
           '#5ce0aa'
        ],
        labels: ['Total', 'Z', 'A']
      });
    }
   }

   function loadGenderChart() {
    var val = JSON.parse('<?php echo json_encode(@$genderDistrix) ?>');
    if (val !== undefined){
      Morris.Donut({
        element: 'gender-pie',
        data:val,
        xkey: 'gender',
        ykeys: ['total'],
        labels: ['Y', 'Z'],
        colors: [
          '#00BCD4',
          '#B2EBF2',
          '#ffa9ce',
           '#B6A2DE',
           '#EB0F82',
           '#202A5A'
        ]
      });
    }
   }

   function loadStaffStatusChart() {
    var val = JSON.parse('<?php echo json_encode(@$staffStatusDistrix) ?>');
    if (val !== undefined){
      Morris.Donut({
        element: 'staff-pie',
        data:val,
        labels: ['Y', 'Z', 'A','B'],
        colors: [
          '#2EC7C9',
           '#B6A2DE',
           '#ffa9ce',
           '#B2EBF2',
        ]
      });
    }
   }
</script>
