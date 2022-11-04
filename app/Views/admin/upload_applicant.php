<?php include_once ROOTPATH."template/header.php"; ?>

    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="card">
              <h5 class="card-header">Applicant Upload</h5>
              <div class="card-body">
                <form action="<?php echo base_url('mc/upload_applicant'); ?>" method="post" enctype="multipart/form-data">
                <div class="mb-3">
                  <label for="formFile" class="form-label">Upload Document</label>
                  <input class="form-control" type="file" id="bulk_applicant" name="bulk_applicant" />
                </div>
                <div class="form-group">
                    <label></label>
                    <button type="submit" class="btn btn-primary" name="btnSubmit" id="btnSubmit">Submit Upload</button>
                </div>
            </form>
              </div>
            </div>
        </div>
    </div>
    <!-- / Content & end for last graph-->
<?php include_once ROOTPATH."template/footer.php"; ?>

