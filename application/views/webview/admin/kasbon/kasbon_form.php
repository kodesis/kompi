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
            <form id="add_kasbon" method="post" class="row">
                <div class="form-group col-12">
                    <label for="nasabah_add">Nasabah</label>
                    <select class="form-control" name="nasabah" id="nasabah_add">
                        <option selected disabled>Pilih Nasabah</option>
                        <?php foreach ($nasabah as $n) { ?>
                            <option value="<?= $n->no_cib ?>"><?= $n->nama ?></option>
                        <?php } ?>
                    </select>
                </div>

                <div class="col-12 row my-2 p-0 mx-auto" id="kredit_info_section" style="display:none;">
                    <div class="form-group col-6">
                        <label for="kredit_limit">Limit Kredit</label>
                        <input disabled type="text" class="form-control" id="kredit_limit" value="">
                        <small class="form-text text-muted">Total pinjaman yang tersedia.</small>
                    </div>
                    <div class="form-group col-6">
                        <label for="kredit_usage">Penggunaan Kredit</label>
                        <input disabled type="text" class="form-control" id="kredit_usage" value="">
                        <small class="form-text text-muted">Jumlah pinjaman yang sudah terpakai.</small>
                    </div>
                    <div class="form-group col-12">
                        <label for="kredit_remaining">Sisa Kredit</label>
                        <input disabled type="text" class="form-control" id="kredit_remaining" value="">
                        <small class="form-text text-muted">Jumlah pinjaman yang tersisa (Limit - Penggunaan).</small>
                    </div>
                </div>
                <div class="form-group col-12">
                    <label for="nominal_add">Nominal</label>
                    <input disabled type="text" class="form-control" name="nominal" id="nominal_add" value="<?= set_value('nominal', $form_data['nominal'] ?? 0) ?>">
                </div>
                <div class="form-group col-6">
                    <label for="nominal_kredit_add">Nominal Kredit</label>
                    <input disabled type="text" class="form-control" name="nominal_kredit" id="nominal_kredit_add" value="<?= set_value('nominal_kredit', $form_data['nominal_kredit'] ?? 0) ?>">
                </div>
                <div class="form-group col-6">
                    <label for="nominal_cash_add">Nominal Cash</label>
                    <input disabled type="text" class="form-control" name="nominal_cash" id="nominal_cash_add" value="<?= set_value('nominal_cash', $form_data['nominal_cash'] ?? 0) ?>">
                </div>

            </form>

            <div class="row">
                <div class="form-group col-12">
                    <button disabled type="submit" class="btn btn-primary" id="submit_btn" onclick="save_Kasbon()">Submit</button>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->