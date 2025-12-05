<style>
    /* .select2-container {
        width: 100%;
        height: calc(1.5em + .75rem + 2px);
        padding: .375rem .75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #6e707e;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #d1d3e2;
        border-radius: .35rem;
    } */
</style>

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
            <h6 class="m-0 font-weight-bold text-primary"><?= $title ?></h6>
            <div class="d-flex justify-content-end">
                <a href="<?= base_url('assets/template/Template_Saldo_simpanan.xlsx') ?>" class="btn btn-secondary" download target="_blank">Download Template</a>
                <!-- <a href="<?= base_url('saldo_simpanan/export_template_simpanan') ?>" class="btn btn-secondary" target="_blank">Download Template</a> -->
                <button type="button" class="btn btn-success ms-2 ml-2" data-toggle="modal" data-target="#uploadModal">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" height="16" width="16">
                        <path fill="#ffffff" d="M64 0C28.7 0 0 28.7 0 64L0 448c0 35.3 28.7 64 64 64l256 0c35.3 0 64-28.7 64-64l0-288-128 0c-17.7 0-32-14.3-32-32L224 0 64 0zM256 0l0 128 128 0L256 0zM155.7 250.2L192 302.1l36.3-51.9c7.6-10.9 22.6-13.5 33.4-5.9s13.5 22.6 5.9 33.4L221.3 344l46.4 66.2c7.6 10.9 5 25.8-5.9 33.4s-25.8 5-33.4-5.9L192 385.8l-36.3 51.9c-7.6 10.9-22.6 13.5-33.4 5.9s-13.5-22.6-5.9-33.4L162.7 344l-46.4-66.2c-7.6-10.9-5-25.8 5.9-33.4s25.8-5 33.4 5.9z" />
                    </svg> Upload Excel
                </button>
            </div>
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
            <form action="<?= base_url('tabungan/process_transaksi_tabungan') ?>" method="post" class="row">
                <div class="form-group col-6">
                    <label for="exampleInputEmail1">No Tabungan</label>
                    <!-- <input type="text" class="form-control" name="no_tabungan" id="no_tabungan_add" value="<?= set_value('no_tabungan', $form_data['no_tabungan'] ?? $new_tabungan_number) ?>"> -->
                    <select class="form-control" name="no_tabungan" id="no_tabungan_select">
                        <option selected disabled>Pilih Nomor Tabungan</option>
                        <?php
                        foreach ($no_tabungan as $n) {
                        ?>
                            <option value="<?= $n->no_tabungan ?>"><?= $n->no_tabungan ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group col-6">
                    <label for="" class="form-label">Tipe Transaksi</label>
                    <select name="tipe_transaksi" id="tipe_transaksi_select" class="form-control" style="width: 100%;" required>
                        <option selected disabled> -- Pilih Tipe --</option>
                        <option value="Setor">Setor</option>
                        <option value="Kredit">Kredit</option>
                        <!-- <option value="1">Setor</option> -->
                        <!-- <option value="2">Kredit</option> -->
                    </select>
                </div>
                <div class="form-group col-6">
                    <label for="" class="form-label">Debit</label>
                    <select name="neraca_debit" id="neraca_debit" class="form-control" style="width: 100%;" required>
                        <option value="">-- Pilih pos neraca debit</option>
                        <?php
                        foreach ($debit as $c) :
                        ?>
                            <option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>" data-posisi="<?= $c->posisi ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?></option>
                        <?php
                        endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-6">
                    <label for="" class="form-label">Kredit</label>
                    <select name="neraca_kredit" id="neraca_kredit" class="form-control" style="width: 100%;" required>
                        <option value="">-- Pilih pos neraca kredit</option>
                        <?php
                        foreach ($kredit as $c) :
                        ?>
                            <option value="<?= $c->no_sbb ?>" data-nama="<?= $c->nama_perkiraan ?>" data-posisi="<?= $c->posisi ?>"><?= $c->no_sbb . ' - ' . $c->nama_perkiraan ?> </option>
                        <?php
                        endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12 col-xs-12 form-group has-feedback">
                    <div id="warningMessage" class="validation-error-alert">

                    </div>
                </div>
                <div class="form-group col-6">
                    <label for="" class="form-label">Nominal</label>
                    <!-- <input type="text" class="form-control" name="input_nominal" id="input_nominal" placeholder="Nominal" oninput="format_angka()" onkeypress="return onlyNumberKey(event)" autofocus required> -->
                    <input type="text" class="form-control uang" name="input_nominal" id="input_nominal" placeholder="Nominal" autofocus required>
                </div>


                <!-- <div class="form-group col-6">
                        <label for="exampleInputEmail1">Nominal</label>
                        <input type="number" class="form-control" name="nominal" id="nominal_add" value="<?= set_value('nominal', $form_data['nominal'] ?? 0) ?>">

                    </div> -->
                <div class="form-group col-6">
                    <label for="" class="form-label">Keterangan</label>
                    <input type="text" class="form-control" name="input_keterangan" id="input_keterangan" placeholder="Keterangan" oninput="this.value = this.value.toUpperCase()" required>
                </div>
                <div class="form-group col-12">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="upload_excel_form" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadModalLabel">Upload Excel File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="excelFile" class="form-label">Choose Excel File</label>
                        <input class="form-control" type="file" name="file_excel" id="excelFile" accept=".xlsx,.xls">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>

        </div>
    </div>
</div>