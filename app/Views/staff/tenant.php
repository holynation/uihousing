<?php include_once ROOTPATH."template/header.php"; ?>
<!-- Page header -->
<div class="container-p-y container-p-x">
  <div class="d-flex">
    <h4><span>Staff Tenant Page</h4>
  </div>

  <div class="d-flex">
    <div class="breadcrumb">
      <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <a href="#" class="breadcrumb-item">Tenant</a>
      <span class="breadcrumb-item active">Current</span>
    </div>
  </div>
</div>
<!-- /page header -->
<!-- Content -->
<div class="container-xxl flex-grow-1">
  <div class="row">
    <?php if(isset($staff)): ?>
    <div class="card d-flex">
        <div class="my-2 d-grid justify-content-end">
            <a href="#" class="btn btn-primary" data-bs-toggle='modal' data-bs-target='#myModal'><em class="menu-icon tf-icons bx bx-plus-medical"></em><span>Register Tenant</span>
            </a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Content area -->
    <div class="content">
      <!-- Basic card -->
      <div class="card">
        <!-- this is the view table for each model -->
        <div class="card-body">
          <div class="card-inner mx-4 my-3">
            <?php
            $model = 'tenant';
            $tableAttr = ['class'=>'table'];
            $tableExclude = isset($staff) && $staff ? ['staff_id'] : [];
            $tableAction = [];
            $model_id = isset($id) && $id ? urldecode($id) : $staff->ID;
            $tableData = $tableWithHeaderModel->openTableHeader($model,$tableAttr,$tableExclude)
              ->appendTableAction($tableAction)
              ->appendEmptyIcon('<i class="icon-stack-empty mr-2 mb-2 icon-2x"></i>')
              ->generateTableBody(null,true,0,null,' order by ID desc '," where staff_id = $model_id ")
              ->pagedTable(true,20)
              ->generate();

            echo $tableData;
            ?>
          </div>
        </div>
      </div>
      <!-- /basic card -->
    </div>
    <!-- /content area -->
  </div>

  <?php if(isset($staff)): ?>
  <!-- this is the modal form -->
  <div class="modal fade" tabindex="-1" id="myModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top" role="document">
        <div class="modal-content">
            <div class="modal-header">
                  <h5 class="modal-title">Tenant Form</h5>
                  <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                  ></button>
            </div>
            <div class="modal-body">
              <?php
                  $hidden = ['staff_id'=>$staff->ID];
                  $showStatus = false;
                  $exclude = [];
                  $submitLabel = 'Submit';

                  $formContent = $modelFormBuilder->start('tenant_table')
                  ->appendInsertForm('tenant',true,$hidden,'',$showStatus,$exclude)
                  ->addSubmitLink(null)
                  ->appendResetButton('Reset','btn btn-md btn-danger mt-3')
                  ->appendSubmitButton($submitLabel,'btn btn-md btn-primary mt-3')
                  ->build();

                  echo $formContent;
              ?>
            </div>
        </div>
    </div>
  </div>
<?php endif; ?>
</div>

<?php include_once ROOTPATH."template/footer.php"; ?>

<script>
    var inserted = false;

    let formGrp = $('div[class=form-group]');
    let formLabel = $('label[for]');
    formGrp.addClass('mb-3');
    formLabel.addClass('form-label');

    $(document).ready(function() {
      $('.modal').on('hidden.bs.modal', function (e) {
        if (inserted) {
          inserted = false;
          location.reload();
        }
      });
      $('.close').click(function(event) {
        if (inserted) {
          inserted = false;
          location.reload();
        }
      });
    });

    function ajaxFormSuccess(target,data) {
      if (data.status) {
        inserted=true;
        $('form').trigger('reset');
      }
      showNotification(data.status,data.message);
      var btnSubmit = $('input[type=submit]');
      btnSubmit.removeClass('disabled');
      btnSubmit.prop('disabled', false);
      btnSubmit.html('Submit');
      if (typeof target ==='undefined') {
        location.reload();
      }
    }
</script>

