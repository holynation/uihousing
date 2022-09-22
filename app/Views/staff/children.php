<?php include_once ROOTPATH."template/header.php"; ?>
<!-- Page header -->
<div class="container-p-y container-p-x">
  <div class="d-flex">
    <h4><span>Staff Children Page</h4>
  </div>

  <div class="d-flex">
    <div class="breadcrumb">
      <a href="<?php echo base_url("vc/$userType/dashboard"); ?>" class="breadcrumb-item"><i class="icon-home2 mr-2"></i> Home</a>
      <a href="#" class="breadcrumb-item">Children</a>
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
            $model = 'children';
            $tableAttr = array('class'=>'table');
            $tableExclude = array();
            $tableAction = array();
            $model_id = urldecode($id);
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
</div>

<?php include_once ROOTPATH."template/footer.php"; ?>

