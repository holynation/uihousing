<?php
$exclude = ($configData && array_key_exists('exclude', $configData))?$configData['exclude']:array();
$has_upload = ($configData && array_key_exists('has_upload', $configData))?$configData['has_upload']:false;
$hidden = ($configData && array_key_exists('hidden', $configData))?$configData['hidden']:array();
$showStatus = ($configData && array_key_exists('show_status', $configData))?$configData['show_status']:false;
$showAddForm = ($configData && array_key_exists('show_add', $configData))?$configData['show_add']:true;
$submitLabel = ($configData && array_key_exists('submit_label', $configData))?$configData['submit_label']:"Save";
$extraLink = ($configData && array_key_exists('extra_link', $configData))?$configData['extra_link']:false;
$extraValue = ($configData && array_key_exists('extra_value', $configData))?$configData['extra_value']:"Add";
$tableAction = ($configData && array_key_exists('table_action', $configData))?$configData['table_action']:$modelObj::$tableAction;
$tableExclude = ($configData && array_key_exists('table_exclude', $configData))?$configData['table_exclude']:array();
$query = ($configData && array_key_exists('query', $configData))?$configData['query']:"";
$tableTitle = ($configData && array_key_exists('table_title', $configData))?$configData['table_title']:"Table of ".ucfirst(removeUnderscore($model));
$icon = ($configData && array_key_exists('table_icon', $configData))?$configData['table_icon']:"";
$search = ($configData && array_key_exists('search', $configData))?$configData['search']:"";
$searchPlaceholder = ($configData && array_key_exists('search_placeholder', $configData))?$configData['search_placeholder']:"";
$searchOrderBy = ($configData && array_key_exists('order_by', $configData))?$configData['order_by']:"";
$filter = ($configData && array_key_exists('filter', $configData))?$configData['filter']:"";
$show_add = ($configData && array_key_exists('show_add', $configData))?$configData['show_add']:false;
$checkBox = ($configData && array_key_exists('table_checkbox', $configData))?$configData['table_checkbox']:false;
$tableAttr = ($configData && array_key_exists('table_attr', $configData))?$configData['table_attr']:array('class'=>'table', 'id'=> 'datatable-buttons'); # 'id'=>'datatable-buttons-customer'
$editMessageInfo = ($configData && array_key_exists('edit_message_info', $configData))?$configData['edit_message_info']:"";
$headerTitle = ($configData && array_key_exists('header_title', $configData))?$configData['header_title']:"";

$where ='';
$orderBy=' order by ID desc';
if ($filter) {
  foreach ($filter as $item) {
    $display = (isset($item['filter_display'])&&$item['filter_display'])?$item['filter_display']:$item['filter_label'];

    if (isset($_GET[$display]) && $_GET[$display]) {
      $value = $this->db->conn_id->escape_string($_GET[$display]);
      $where.= $where?" and {$item['filter_label']}='$value' ":"where {$item['filter_label']}='$value' ";
    }
  }
}

if ($search) {
 $val = isset($_GET['q'])?$_GET['q']:'';
 $val = $db->escape($val);
  if (isset($_GET['q']) && $_GET['q']) {
    $whereQ = (strpos($query,'where') !== false) ? " and " : "where ";
    $temp=$where?" and (":" $whereQ ";
    $count =0;
    foreach ($search as $criteria) {
      $temp.=$count==0?" $criteria like '%$val%'":" or $criteria like '%$val%' ";
      $count++;
    }
    $temp .= (strpos($temp, 'and (') !== false) ? ")" : "";
    $where.=$temp;
  }
}

if (isset($_GET['export'])) {
  $queryHtmlTableObjModel->export=true;
  $tableWithHeaderModel->export=true;
}

$tableData='';

if($query) {
  $query.=' '.$where;
  if($searchOrderBy){
    $countFil = 0;
    $tempOrder='';
    foreach($searchOrderBy as $valFilter){
      $tempOrder .= $countFil == 0? " $valFilter " : " , $valFilter ";
      $countFil++;
    }
    $query .= "order by $tempOrder desc";
  }
  $tableData = $queryHtmlTableObjModel->openTableHeader($query,array(),null,$tableAttr,$tableExclude)
    ->excludeSerialNumber(true)
    ->paging(true,0,100)
    ->appendTableAction($tableAction,null)
    ->appendCheckBox($checkBox,array('class'=>'form-control'))
    ->generateTable();
}
else{
  $tableData = $tableWithHeaderModel->openTableHeader($model,$tableAttr,$tableExclude,true)
  ->excludeSerialNumber(true)
  ->appendTableAction($tableAction)
  ->appendEmptyIcon(null)
  ->generateTableBody()
  ->pagedTable(true,100)
  ->generate();
}
?>

<?php
$modelPath = null;
$extra = "";

$formContent= $modelFormBuilder->start($model.'_table')
->appendInsertForm($model,true,$hidden,'',$showStatus,$exclude)
->addSubmitLink($modelPath)
->appendExtra($extra)
->appendResetButton('Reset','btn btn-md btn-danger mt-3')
->appendSubmitButton($submitLabel,'btn btn-md btn-primary mt-3')
->build();
?>

<?php include_once ROOTPATH."template/header.php"; ?>
    <!-- Page header -->
    <div class="container-p-y container-p-x">
      <div class="d-flex">
        <h4><span><?php echo ucfirst($userType); ?> </span> - <?php echo ucfirst(removeUnderscore($model)); ?> Page</h4>
      </div>

      <div class="d-flex">
        <div class="breadcrumb">
          <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
          <a href="#" class="breadcrumb-item"><?php echo ucfirst(removeUnderscore($model)); ?></a>
          <span class="breadcrumb-item active">Current</span>
        </div>
      </div>
    </div>
    <!-- /page header -->  

    <!-- Content -->
    <div class="container-xxl flex-grow-1">
      <?php if($show_add): ?>
        <div class="float-right mb-3">
            <a href="#" class="btn btn-primary" data-bs-toggle='modal' data-bs-target='#myModal'><em class="menu-icon tf-icons bx bx-plus-medical"></em><span>Add</span>
            </a>

            <?php if($has_upload): ?>
            <a href="#" class="btn btn-primary ml-1" data-bs-toggle='modal' data-bs-target='#modal-upload'><em class="menu-icon tf-icons bx bx-cloud-upload"></em><span>Batch Upload</span>
            </a>
            <?php endif; ?> <!-- end batch upload -->
        </div>
      <?php endif; ?> <!-- end the show add -->

        <!-- here is the section for table filter option on the server level -->
        <div style="margin-left: -1rem;">
          <form action="">
            <?php $where=''; ?>
            <div class="row col-lg-12">
              <?php 
              if ($filter): ?>
                <?php foreach ($filter as $item): ?>
                     <?php $display = (isset($item['filter_display'])&&$item['filter_display'])?$item['filter_display']:$item['filter_label']; ?>
                      <?php 
                        if (isset($_GET[$display]) && $_GET[$display]) {
                          $value = $this->db->escape_str($_GET[$display]);
                          $where.= $where?" and {$item['filter_label']}='$value' ":"where {$item['filter_label']}='$value' ";
                        }
                      ?>
                    <div class="form-group mb-2">
                      <div class="col-lg-12">
                        <select class="form-control <?php echo isset($item['child'])?'autoload':'' ?>" name="<?php echo $display; ?>" id="<?php echo $display; ?>" <?php echo isset($item['child'])?"data-child='{$item['child']}'":""?> <?php echo isset($item['load'])?"data-load='{$item['load']}'":""?> >
                          <option value="">..select <?php echo removeUnderscore(rtrim($display,'_id')) ?>...</option>
                            <?php if (isset($item['preload_query'])&& $item['preload_query']): ?>
                              <?php echo buildOptionFromQuery($this->db,$item['preload_query'],null,isset($_GET[$display])?$_GET[$display]:''); ?>
                            <?php endif; ?>
                            <!-- end for the option value -->
                        </select>
                      </div>
                    </div>

                <?php if ($search): ?>

                  <?php 
                    $filterLabel = ($searchPlaceholder) ? $searchPlaceholder : $search;
                    $placeholder = implode(',', $filterLabel);
                    $val = isset($_GET['q'])?$_GET['q']:'';
                    $val = $this->db->escape_str($val);
                   ?>
                  <div class="row mx-0">
                    <div class="form-control-wrap col-lg-12 mb-2">
                      <input class="form-control" type="text" name="q" placeholder="<?php echo $placeholder; ?>" value="<?php echo $val; ?>">
                    </div>
                  </div>
                <?php endif; ?> <!-- end the search input -->

                  <?php if ($search || $filter): ?>
                    <div class="form-group col-lg-3 mb-3">
                      <input type="submit" value="Filter" class="btn btn-dark btn-block">
                    </div>
                  <?php endif; ?> <!-- end submit filter -->
                <?php endforeach; ?> <!-- end foreach for filter looop -->
              <?php endif; ?> <!-- end if filter -->
            </div>
          </form>
        </div>

        <!-- this is for the table section for the admin -->
        <div class="card">
            <div class="card-inner mx-4 my-3">
                <?php echo $tableData; ?>
            </div>
        </div>
    </div>
    <!-- / Content -->

    <!-- modal for batch uploading -->
    <?php if ($configData==false || array_key_exists('has_upload', $configData)==false || $configData['has_upload']): ?>
      <div class="modal modal-default fade" id="modal-upload">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title"><?php echo removeUnderscore($model) ?> Batch Upload</h5>
                  <a href="#" class="close" data-dismiss="modal" aria-label="Close">
                      <em class="icon ni ni-cross"></em>
                  </a>
            </div>
            <div class="modal-body">
              <?php
                $batchUrl = "mc/template/$model?exec=name";
                $batchActionUrl = "mc/sFile/$model";
              ?>
              <div>
                <a  class='btn btn-info' href="<?=base_url($batchUrl)?>">Download Template</a>
              </div>
              <br/>
              <h5>Upload <?php echo removeUnderscore($model) ?></h5>
              <form method="post" action="<?php echo base_url($batchActionUrl) ?>" enctype="multipart/form-data">
                <div class="form-group">
                  <input type="file" name="bulk-upload" class="form-control">
                  <input type="hidden" name="MAX_FILE_SIZE" value="4194304">
                </div>
                <div class="form-group">
                  <input type="submit" class='btn btn-lg btn-primary' name="submit" value="Upload">
                </div>
              </form>
            </div>
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
    <?php endif; ?>
    <!-- batch uploading end -->

    <!-- add modal -->
    <div class="modal fade" tabindex="-1" id="myModal" role="dialog" aria-hidden="true">
      <div class="modal-dialog modal-dialog-top" role="document">
          <div class="modal-content">
              <div class="modal-header">
                    <h5 class="modal-title"><?php echo removeUnderscore($model);  ?> Entry Form</h5>
                    <button
                      type="button"
                      class="btn-close"
                      data-bs-dismiss="modal"
                      aria-label="Close"
                    ></button>
              </div>
              <div class="modal-body">
                <?php echo $formContent; ?>
              </div>
          </div>
      </div>
    </div>

    <!-- modal for editing form -->
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
    <!-- modal edit form end -->

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

