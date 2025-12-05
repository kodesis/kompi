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
                <a href="<?= base_url('kasbon/add') ?>" class="btn btn-primary btn-sm">Add Kasbon</a>

            <?php
            }
            ?>
        </div>
        <div class="card-body">
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
<!-- /.container-fluid -->