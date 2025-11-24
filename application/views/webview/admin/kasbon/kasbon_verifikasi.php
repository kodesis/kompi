<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <!-- <h1 class="h3 mb-2 text-gray-800">Users Table</h1> -->
    <!-- <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
        For more information about DataTables, please visit the <a target="_blank"
            href="https://datatables.net">official DataTables documentation</a>.</p> -->

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary"><?= $title ?> Kasbon</h6>

        </div>
        <div class="card-body">
            <?php
            // Retrieve the flash data array. Use the '?? []' structure to safely handle cases where no errors exist.
            $errors = $this->session->flashdata('form_errors') ?? [];

            if (!empty($errors)) {
                // Start an alert container (adjust classes for your CSS/framework, e.g., Bootstrap)
                echo '<div class="alert alert-danger" role="alert">';
                echo '<strong>Terjadi Kesalahan Input:</strong> Harap periksa kembali kolom berikut:';
                echo '<ul>';

                // Loop through the associative array (key is the field name, value is the error message)
                foreach ($errors as $field => $message) {
                    // Display only the error message in the list
                    echo '<li>' . htmlspecialchars($message) . '</li>';
                }

                echo '</ul>';
                echo '</div>';
            }
            ?>
            <!-- <form action="<?= base_url('kasbon/save_kasbon') ?>" method="post" class="row"> -->
            <form id="verifikasi_kasbon" method="post" class="row">
                <div class="form-group col-12">
                    <label for="nominal_add">Kode Verifikasi</label>
                    <input type="text" class="form-control" name="kode" id="kode_add">
                </div>
            </form>

            <div class="row">
                <div class="form-group col-12">
                    <button type="submit" class="btn btn-primary" id="submit_btn" onclick="verifikasi_Kasbon()">Submit</button>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->