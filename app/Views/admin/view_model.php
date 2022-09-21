<?php include_once ROOTPATH."template/header.php"; ?>
<!-- Page header -->
<div class="container-p-y container-p-x">
  <div class="d-flex">
    <h4><span><?php echo ucfirst($userType); ?> </span> - <?php echo ucwords(removeUnderscore($pageTitle)); ?> Page</h4>
  </div>

  <div class="d-flex">
    <div class="breadcrumb">
      <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <?php
        $tempModel = $modelName;
        $modelName = removeUnderscore($modelName);
      ?>
      <a href="#" class="breadcrumb-item"><?php echo ucfirst(removeUnderscore($pageTitle)); ?></a>
      <span class="breadcrumb-item active">Current</span>
    </div>
  </div>
</div>
<!-- /page header -->
<!-- Content -->
<div class="container-xxl flex-grow-1">
  <div class="row">
    <!-- Content area -->
    <div class="content">
      <!-- Basic card -->
      <div class="card">
        <!-- this is the view table for each model -->
        <div class="card-body">
          <div class="card-inner mx-4 my-3">
            <?php
            $type = $_GET['type'] ?? null;
              $enableParam = [];
              $action = [
                'edit' => "edit/{$tempModel}",
                'view more' => "vc/admin/view_more/{$tempModel}/{$type}"
              ];
              
              if($tempModel == 'equip_request' and ($type == 'approved' || $type == 'failed')){
                $action = [
                  'View More' => "vc/admin/view_more/{$tempModel}/{$type}",
                  'View extended Booking' => "vc/admin/extend_equip_request",
                  'Delivery Status' => "vc/admin/equip_delivery_status"
                ];
              }
              else if($tempModel == 'equipments' || $tempModel == 'equip_payment'){
                $action = [
                  'View More' => "vc/admin/view_more/{$tempModel}/{$type}",
                ];
              }
              else if($tempModel == 'owners' and empty($dataParam)){
                $action = [
                  'delete' => "delete/{$tempModel}",
                  'view more' => "vc/admin/view_more/{$tempModel}/owners",
                  'View Equipment' => "vc/admin/view_model/{$tempModel}",
                  'edit' => "edit/{$tempModel}",
                  'enable' => 'getEnabled',
                ];
              }
              else if(($tempModel == 'owners' || $tempModel == 'hirers') and !empty($dataParam)){
                $action = [];
              }
              else if($tempModel == 'hirers' && empty($type)){
                $action = [
                  'view more' => "vc/admin/view_more/{$tempModel}/hirers",
                  'View Bookings' => "vc/admin/view_model/{$tempModel}",
                  'edit' => "edit/{$tempModel}",
                  'enable' => 'getEnabled',
                ];
              }
              else if($tempModel == 'hirers' && !empty($type)){
                $action = [
                  'view more' => "vc/admin/view_more/{$tempModel}/hirers",
                  'enable' => 'getEnabled',
                ];
              }

              $param = !empty($dataParam) ? array($dataParam) : array();
              $tableData = $queryHtmlTableObjModel->openTableHeader($queryString,$param,null,array('id'=>'datatable-buttons', 'class'=>'table'))
                // ->paging(true,0,10)
                ->excludeSerialNumber(true)
                ->appendTableAction($action)
                ->generateTable();
              echo $tableData;
            ?>
          </div>
        </div>
      </div>
      <!-- /basic card -->
    </div>
    <!-- /content area -->
  </div>
</div>

<!-- this is for edit modal form -->
<div id="modal-edit" class="modal fade" role="dialog" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-top" role="document">
    <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>
        </div>
        <div class="modal-body">
            <p id="edit-container"> </p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>
  </div>
</div>
<!-- end edit modal form -->

<?php include_once ROOTPATH."template/footer.php"; ?>
<script>
    var inserted=false;

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
      $('span[data-ajax-edit=1] a').click(function(event){
        event.preventDefault();
        var link = $(this).attr('href');
        var action = $(this).text();
        sendAjax(null,link,'','get',showUpdateForm);
      });
    });

    function showUpdateForm(target,data) {
      var data = JSON.parse(data);
      if (data.status==false) {
        showNotification(false,data.message);
        return;
      }

       var container = $('#edit-container');
       container.html(data.message);
       //rebind the autoload functions inside
       
      let formGrp = $('div[class=form-group]');
      let formLabel = $('label[for]');
      formGrp.addClass('mb-3');
      formLabel.addClass('form-label');

      $('#modal-edit').modal('show');
    }

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
      if(data.status){
        inserted = false;
        $('#modal-edit').modal('close');
        location.reload();
      }
    }
</script>

