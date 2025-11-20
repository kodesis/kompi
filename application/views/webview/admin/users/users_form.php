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
            <h6 class="m-0 font-weight-bold text-primary"><?= $title ?> Users</h6>

        </div>
        <div class="card-body">
            <?php
            if ($title == "Add") {
            ?>
                <form action="<?= base_url('users/proccess_add') ?>" method="post" class="row">
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Username</label>
                        <input type="text" class="form-control" name="username" id="username_add" value="<?= set_value('username', $form_data['username'] ?? '') ?>">
                        <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Nama</label>
                        <input type="text" class="form-control" name="nama" id="nama_add" value="<?= set_value('nama', $form_data['nama'] ?? '') ?>">
                        <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Password</label>
                        <input type="password" class="form-control" name="password" id="password_add" value="12345" placeholder="Enter password">
                        <small id="emailHelp" class="form-text text-muted">Default Password : 12345</small>
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Password Confirmation</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation_add" value="12345" placeholder="Confirm password">
                        <small id="password_confirmation_text" class="form-text text-muted font-weight-bold">Password need to be same</small>
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Golongan</label>
                        <select class="form-control" name="golongan" id="golongan_add">
                            <option selected disabled>Pilih Golongan</option>
                            <?php
                            foreach ($golongan as $g) {
                            ?>
                                <option value="<?= $g->kategori ?>"><?= $g->kategori ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <!-- <input type="text" class="form-control" name="password_confirmation" id="password_confirmation_add" value="12345" placeholder="Confirm password"> -->
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Fasilitas</label>
                        <select class="form-control" name="fasilitas" id="fasilitas_add">
                            <option selected disabled>Pilih Fasilitas</option>
                            <?php
                            foreach ($golongan as $g) {
                            ?>
                                <option value="<?= $g->kategori ?>"><?= $g->kategori ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <!-- <input type="text" class="form-control" name="password_confirmation" id="password_confirmation_add" value="12345" placeholder="Confirm password"> -->
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Limit</label>
                        <input type="number" class="form-control" name="limit" id="limit_edit" placeholder="Limit" value="<?= set_value('limit', $form_data['limit'] ?? '') ?>">
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
                <form action="<?= base_url('users/proccess_edit') ?>" method="post" class="row">
                    <!-- <input type="hidden" name="uid" value="<?= $user->uid ?>"> -->
                    <!-- <div class="form-group col-6">
                        <label for="exampleInputEmail1">Username</label>
                        <input type="text" class="form-control" name="username" id="username_edit" value="<?= $user->username ?>">
                    </div> -->
                    <input type="hidden" class="form-control" name="username" id="username_edit" value="<?= $user->username ?>">
                    <div class="form-group col-6">
                        <label for="exampleInputEmail1">Nama</label>
                        <input type="text" class="form-control" name="nama" id="nama_edit" value="<?= $user->nama ?>">
                        <!-- <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small> -->
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Password</label>
                        <input type="password" class="form-control" name="password" id="password_edit" placeholder="Enter password">
                        <small id="emailHelp" class="form-text text-muted">Leave blank if you do not wish to change the password.
                        </small>
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Password Confirmation</label>
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation_edit" placeholder="Confirm password">
                        <small id="password_confirmation_text" class="form-text text-muted font-weight-bold">Password need to be same</small>
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Golongan</label>
                        <select class="form-control" name="golongan" id="golongan_edit">
                            <!-- <option selected disabled>Pilih Golongan</option> -->
                            <?php
                            foreach ($golongan as $g) {
                            ?>
                                <option <?= $g->kategori == $user->golongan ? 'selected' : '' ?> value="<?= $g->kategori ?>"><?= $g->kategori ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <!-- <input type="text" class="form-control" name="password_confirmation" id="password_confirmation_edit" value="12345" placeholder="Confirm password"> -->
                    </div>
                    <div class="form-group col-6">
                        <label for="exampleInputPassword1">Fasilitas</label>
                        <select class="form-control" name="fasilitas" id="fasilitas_edit">
                            <!-- <option selected disabled>Pilih Fasilitas</option> -->
                            <?php
                            foreach ($golongan as $g) {
                            ?>
                                <option <?= $g->kategori == $user->golongan ? 'selected' : '' ?> value="<?= $g->kategori ?>"><?= $g->kategori ?></option>
                            <?php
                            }
                            ?>
                        </select>
                        <!-- <input type="text" class="form-control" name="password_confirmation" id="password_confirmation_edit" value="12345" placeholder="Confirm password"> -->
                    </div>

                    <div class="form-group col-12">
                        <label for="exampleInputPassword1">Limit</label>
                        <input type="number" class="form-control" name="limit" id="limit_edit" placeholder="Limit" value="<?= $user->limit ?>">
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