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
            <div>
                <a href="<?= base_url('tabungan/add') ?>" class="btn btn-primary btn-sm">Add Tabungan</a>
                <a href="<?= base_url('tabungan/transaksi_simpanan') ?>" class="btn btn-primary btn-sm">Transaksi Simpanan</a>
            </div>
        </div>
        <div class="card-body">
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
<!-- /.container-fluid -->