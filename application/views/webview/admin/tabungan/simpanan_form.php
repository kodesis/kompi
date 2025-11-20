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