<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <!-- <h1 class="h3 mb-2 text-gray-800">Tabungan Table</h1> -->
    <!-- <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
        For more information about DataTables, please visit the <a target="_blank"
            href="https://datatables.net">official DataTables documentation</a>.</p> -->

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tabungan Table</h6>

            <?php
            if ($this->session->userdata('role') == 1) {
            ?>
                <div>
                    <a href="<?= base_url('tabungan/add') ?>" class="btn btn-primary btn-sm">Add Tabungan</a>
                    <a href="<?= base_url('tabungan/transaksi_simpanan') ?>" class="btn btn-primary btn-sm">Transaksi Simpanan</a>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exportTabunganModal">
                        <i class="fas fa-file-excel"></i> Export Tabungan
                    </button>
                    <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#exportDetailTabunganModal">
                        <i class="fas fa-file-excel"></i> Export Detail Tabungan
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
                </div>
            <?php
            } else {
            ?>
                <input type="hidden" name="nasabah" value="ALL">
            <?php
            }
            ?>
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No Urut</th>
                            <th>No Tabungan</th>
                            <th>Nama</th>
                            <th>Jenis Tabungan</th>
                            <th>Status</th>
                            <th>Nominal</th>
                            <th>Spread Rate</th>
                            <th>Nominal Blokir</th>
                            <th>Pos Rate</th>
                            <th>No LSP</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="5" style="text-align:right">Total:</th>
                            <th id="total_nominal_display">0</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
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
                            <th>Tipe Tabungan</th>
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

<div class="modal fade" id="exportTabunganModal" tabindex="-1" role="dialog" aria-labelledby="exportTabunganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportTabunganModalLabel">Export Laporan Tabungan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('tabungan/export_tabungan_by_date') ?>" method="POST" id="formExportTabungan">
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

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">Export Excel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="exportDetailTabunganModal" tabindex="-1" role="dialog" aria-labelledby="exportDetailTabunganModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportDetailTabunganModalLabel">Export Laporan Detail Tabungan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?= base_url('tabungan/export_detail_tabungan_by_date') ?>" method="POST" id="formExportTabungan">
                <div class="modal-body">

                    <div class="form-group">
                        <label for="">Pilih Nasabah</label>
                        <br>
                        <select class="form-control" name="nasabah" id="nasabah_search_export_detail" style="width: 100%;">
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