<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <!-- <h1 class="h3 mb-2 text-gray-800">Nasabah Table</h1> -->
    <!-- <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
        For more information about DataTables, please visit the <a target="_blank"
            href="https://datatables.net">official DataTables documentation</a>.</p> -->

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Kasbon Table</h6>


            <?php
            if ($this->session->userdata('role') == 1) {
            ?>
                <div>
                    <a href="<?= base_url('kasbon/add') ?>" class="btn btn-primary btn-sm">Add Kasbon</a>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exportKasbonModal">
                        <i class="fas fa-file-excel"></i> Export Kasbon
                    </button>
                </div>
            <?php
            }
            ?>
        </div>
        <div class="card-body">
            <?php
            if ($this->session->userdata('role') == 1) {
            ?>
                <div class="row">
                    <div class="form-group col-4">
                        <label for="">Nasabah</label>
                        <select class="form-control" name="nasabah" id="nasabah_search">
                            <option selected>ALL</option>
                            <?php
                            foreach ($nasabah as $n) {
                            ?>
                                <option value="<?= $n->no_cib ?>"><?= $n->nama ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-4 d-none" id="nasabah_detail_kredit_limit">
                        <label for="">Limit Kredit</label>
                        <input type="text" class="form-control" disabled id="limit_kredit_nasabah">
                    </div>
                    <div class="form-group col-4 d-none" id="nasabah_detail_kredit_usage">
                        <label for="">Kredit Terpakai</label>
                        <input type="text" class="form-control" disabled id="usage_kredit_nasabah">
                    </div>
                </div>
            <?php
            }
            ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nasbah</th>
                            <th>Tanggal Jam</th>
                            <th>Nominal</th>
                            <th>Nominal Kredit</th>
                            <th>Nominal Cash</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="3" style="text-align:right">Total Keseluruhan:</th>
                            <th id="total_nominal_display"></th>
                            <th id="total_nominal_kredit_display"></th>
                            <th id="total_nominal_cash_display"></th>
                            <th></th>
                        </tr>
                    </tfoot>
                    <!-- <tfoot>
                        <tr>

                            <th>No</th>
                            <th>nama</th>
                            <th>Alamat</th>
                            <th>No Ktp</th>
                            <th>No Telp</th>
                            <th>Ahli Waris</th>
                            <th>Kode Pos</th>
                            <th>Nama Ibu Kandung</th>
                            <th>Pekerjaan</th>
                            <th>Kode AO</th>
                            <th>Nama Panggilan</th>
                            <th>Tgl Lahir</th>
                            <th>Tempat Lahir</th>
                            <th>Kota</th>
                            <th>Tgl Pendaftaran</th>
                            <th>Tipe Nasabah</th>
                            <th>Nama Segmen</th>
                            <th>Warga Negara</th>
                            <th>Action</th>
                        </tr>
                    </tfoot> -->
                </table>
            </div>
        </div>
    </div>

</div>

<div class="modal fade" id="exportKasbonModal" tabindex="-1" role="dialog" aria-labelledby="exportKasbonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportKasbonModalLabel">Export Laporan Kasbon</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('kasbon/export_kasbon_by_date') ?>" method="POST" id="formExportKasbon">
                <div class="modal-body">

                    <div class="form-group">
                        <label for="">Pilih Nasabah</label>
                        <br>
                        <select class="form-control" name="nasabah" id="nasabah_search_export" style="width: 100%;">
                            <option value="ALL" selected>ALL</option>
                            <?php
                            // Assuming $nasabah is the array of customer objects passed to the view
                            foreach ($nasabah as $n) {
                            ?>
                                <option value="<?= $n->no_cib ?>"><?= $n->nama ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="date_from">Dari Tanggal</label>
                        <input type="date"
                            class="form-control"
                            name="tanggal_dari"
                            id="tanggal_dari"
                            value="<?= date('Y-m-d') ?>"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="date_to">Sampai Tanggal</label>
                        <input type="date"
                            class="form-control"
                            name="tanggal_sampai"
                            id="tanggal_sampai"
                            value="<?= date('Y-m-d') ?>"
                            required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- /.container-fluid -->