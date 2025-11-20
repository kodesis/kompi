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
            <h6 class="m-0 font-weight-bold text-primary"><?= $title ?> Nasabah</h6>

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
            <?php
            if ($title == "Add") {
            ?>
                <form action="<?= base_url('nasabah/proccess_add') ?>" method="post" class="row">
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Nama</label>
                        <input type="text" class="form-control" name="nama" id="nama_add" value="<?= set_value('nama', $form_data['nama'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Alamat</label>
                        <textarea class="form-control" name="alamat" id="alamat_add" rows="3"><?= set_value('alamat', $form_data['alamat'] ?? '') ?></textarea>

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">No Ktp</label>
                        <input type="text" class="form-control" name="no_ktp" id="no_ktp_add" value="<?= set_value('no_ktp', $form_data['no_ktp'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">No Telp</label>
                        <input type="text" class="form-control" name="no_telp" id="no_telp_add" value="<?= set_value('no_telp', $form_data['no_telp'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Ahli Waris</label>
                        <input type="text" class="form-control" name="ahli_waris" id="ahli_waris_add" value="<?= set_value('ahli_waris', $form_data['ahli_waris'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Kode Pos</label>
                        <input type="text" class="form-control" name="kode_pos" id="kode_pos_add" value="<?= set_value('kode_pos', $form_data['kode_pos'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Nama Ibu Kandung</label>
                        <input type="text" class="form-control" name="nama_ibu_kandung" id="nama_ibu_kandung_add" value="<?= set_value('nama_ibu_kandung', $form_data['nama_ibu_kandung'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Pekerjaan</label>
                        <input type="text" class="form-control" name="pekerjaan" id="pekerjaan_add" value="<?= set_value('pekerjaan', $form_data['pekerjaan'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Kode AO</label>
                        <input type="text" class="form-control" name="kode_ao" id="kode_ao_add" value="<?= set_value('kode_ao', $form_data['kode_ao'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Nama Panggilan</label>
                        <input type="text" class="form-control" name="nama_panggilan" id="nama_panggilan_add" value="<?= set_value('nama_panggilan', $form_data['nama_panggilan'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Tgl Lahir</label>
                        <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir_add" value="<?= set_value('tgl_lahir', $form_data['tgl_lahir'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Tempat Lahir</label>
                        <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir_add" value="<?= set_value('tempat_lahir', $form_data['tempat_lahir'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Kota</label>
                        <input type="text" class="form-control" name="kota" id="kota_add" value="<?= set_value('kota', $form_data['kota'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Tgl Pendaftaran</label>
                        <input type="date" class="form-control" name="tgl_pendaftaran" id="tgl_pendaftaran_add" value="<?= set_value('tgl_pendaftaran', $form_data['tgl_pendaftaran'] ?? '') ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Tipe Nasbah</label>
                        <select class="form-control" name="tipe_nasabah" id="tipe_nasabah_add">
                            <option selected disabled>Pilih Tipe</option>
                            <?php
                            foreach ($tipe as $t) {
                            ?>
                                <option value="<?= $t->kode_tipe ?>"><?= $t->nama_tipe ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <!-- <input type="text" class="form-control" name="password_confirmation" id="password_confirmation_add" value="12345" placeholder="Confirm password"> -->
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Segmen Nasbah</label>
                        <select class="form-control" name="segmen_nasabah" id="segmen_nasabah_add">
                            <option selected disabled>Pilih Segmen</option>
                            <?php
                            foreach ($segnasabah as $s) {
                            ?>
                                <option value="<?= $s->kode_segmen ?>"><?= $s->nama_segmen ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <!-- <input type="text" class="form-control" name="password_confirmation" id="password_confirmation_add" value="12345" placeholder="Confirm password"> -->
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Warga Negara</label>
                        <!-- <input type="text" class="form-control" name="warga_negara" id="warga_negara_add"> -->
                        <select class="form-control" name="warga_negara" id="warga_negara_add">
                            <option selected value="WNI">WNI</option>
                            <option value="WNA">WNA</option>
                        </select>
                    </div>
                    <!-- <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div> -->
                    <div class="form-group col-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            <?php
            } else if ($title == "Edit") {
            ?>
                <form action="<?= base_url('nasabah/proccess_edit') ?>" method="post" class="row">
                    <input type="hidden" name="no_cib" value="<?= $nasabah->no_cib ?>">
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Nama</label>
                        <input type="text" class="form-control" name="nama" id="nama_edit" value="<?= $nasabah->nama ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Alamat</label>
                        <textarea class="form-control" name="alamat" id="alamat_edit" rows="3"><?= $nasabah->alamat ?></textarea>

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">No Ktp</label>
                        <input type="text" class="form-control" name="no_ktp" id="no_ktp_edit" value="<?= $nasabah->no_ktp ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">No Telp</label>
                        <input type="text" class="form-control" name="no_telp" id="no_telp_edit" value="<?= $nasabah->no_telp ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Ahli Waris</label>
                        <input type="text" class="form-control" name="ahli_waris" id="ahli_waris_edit" value="<?= $nasabah->ahli_waris ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Kode Pos</label>
                        <input type="text" class="form-control" name="kode_pos" id="kode_pos_edit" value="<?= $nasabah->kode_pos ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Nama Ibu Kandung</label>
                        <input type="text" class="form-control" name="nama_ibu_kandung" id="nama_ibu_kandung_edit" value="<?= $nasabah->nama_ibu_kandung ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Pekerjaan</label>
                        <input type="text" class="form-control" name="pekerjaan" id="pekerjaan_edit" value="<?= $nasabah->pekerjaan ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Kode AO</label>
                        <input type="text" class="form-control" name="kode_ao" id="kode_ao_edit" value="<?= $nasabah->kode_ao ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Nama Panggilan</label>
                        <input type="text" class="form-control" name="nama_panggilan" id="nama_panggilan_edit" value="<?= $nasabah->nama_panggilan ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Tgl Lahir</label>
                        <input type="date" class="form-control" name="tgl_lahir" id="tgl_lahir_edit" value="<?= $nasabah->tgl_lahir ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Tempat Lahir</label>
                        <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir_edit" value="<?= $nasabah->tempat_lahir ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Kota</label>
                        <input type="text" class="form-control" name="kota" id="kota_edit" value="<?= $nasabah->kota ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Tgl Pendaftaran</label>
                        <input type="date" class="form-control" name="tgl_pendaftaran" id="tgl_pendaftaran_edit" value="<?= $nasabah->tgl_pendaftaran ?>">

                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Tipe Nasbah</label>
                        <select class="form-control" name="tipe_nasabah" id="tipe_nasabah_edit">
                            <!-- <option selected disabled>Pilih Tipe</option> -->
                            <?php
                            foreach ($tipe as $t) {
                            ?>
                                <option <?= $nasabah->tipe_nasabah == $t->kode_tipe ? 'selected' : '' ?> value="<?= $t->kode_tipe ?>"><?= $t->nama_tipe ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <!-- <input type="text" class="form-control" name="password_confirmation" id="password_confirmation_edit" value="12345" placeholder="Confirm password"> -->
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Segmen Nasbah</label>
                        <select class="form-control" name="segmen_nasabah" id="segmen_nasabah_edit">
                            <!-- <option selected disabled>Pilih Segmen</option> -->
                            <?php
                            foreach ($segnasabah as $s) {
                            ?>
                                <option <?= $nasabah->segmen_nasabah == $s->kode_segmen ? 'selected' : '' ?> value="<?= $s->kode_segmen ?>"><?= $s->nama_segmen ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <!-- <input type="text" class="form-control" name="password_confirmation" id="password_confirmation_edit" value="12345" placeholder="Confirm password"> -->
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Warga Negara</label>
                        <!-- <input type="text" class="form-control" name="warga_negara" id="warga_negara_edit"> -->
                        <select class="form-control" name="warga_negara" id="warga_negara_edit">
                            <option <?= $nasabah->warga_negara == "WNI" ? 'selected' : '' ?> value="WNI">WNI</option>
                            <option <?= $nasabah->warga_negara == "WNA" ? 'selected' : '' ?> value="WNA">WNA</option>
                        </select>
                    </div>
                    <!-- <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="exampleCheck1">
                        <label class="form-check-label" for="exampleCheck1">Check me out</label>
                    </div> -->
                    <div class="form-group col-12">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>

            <?php
            }
            ?>
        </div>
    </div>

</div>
<!-- /.container-fluid -->