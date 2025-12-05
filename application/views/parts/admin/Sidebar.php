<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url('dashboard') ?>">
                <div class="sidebar-brand-icon rotate-n-15">
                    <!-- <i class="fas fa-laugh-wink"></i> -->
                </div>
                <div class="sidebar-brand-text mx-3">
                    <!-- KOMPI -->
                    <img class="img-fluid" src="<?= base_url() ?>assets/img/logokompi.png" alt="">
                </div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="<?= base_url('dashboard') ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Menu
            </div>

            <li class="nav-item <?= $this->uri->segment(1) == 'dashboard' ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('dashboard') ?>">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- <li class="nav-item <?= $this->uri->segment(1) == 'users' ? 'active' : '' ?>">
                <a class="nav-link" href="<?= base_url('users') ?>">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Users</span></a>
            </li> -->
            <?php
            if ($this->session->userdata('role') == 1) {
            ?>
                <li class="nav-item <?= $this->uri->segment(1) == 'nasabah' ? 'active' : '' ?>">
                    <a class="nav-link" href="<?= base_url('nasabah') ?>">
                        <i class="fas fa-fw fa-table"></i>
                        <span>Nasabah</span></a>
                </li>
            <?php
            }
            ?>
            <li class="nav-item <?= $this->uri->segment(1) == 'tabungan' ? 'active' : '' ?>">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#TabunganNav"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-university"></i>
                    <span>Menu Tabungan</span>
                </a>
                <div id="TabunganNav" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Menu Tabungan:</h6>
                        <a class="collapse-item <?= $this->uri->segment(1) == 'tabungan' && $this->uri->segment(2) == ''  ? 'active' : '' ?>" href="<?= base_url() ?>tabungan">Tabungan</a>
                        <!-- <a class="collapse-item" <?= $this->uri->segment(1) == 'tabungan' && $this->uri->segment(2) == 'detail_tabungan'  ? 'active' : '' ?>href="<?= base_url() ?>tabungan/detail_tabungan">Detail Tabungan</a> -->
                        <!-- <a class="collapse-item" <?= $this->uri->segment(1) == 'tabungan' && $this->uri->segment(2) == 'detail_tabungan'  ? 'active' : '' ?>href="<?= base_url() ?>tabungan/detail_tabungan">Detail Tabungan</a> -->
                    </div>
                </div>
            </li>
            <li class="nav-item <?= $this->uri->segment(1) == 'kasbon' ? 'active' : '' ?>">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#KasbonNav"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-university"></i>
                    <span>Menu Kasbon</span>
                </a>
                <div id="KasbonNav" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Menu Kasbon:</h6>
                        <a class="collapse-item <?= $this->uri->segment(1) == 'kasbon' && $this->uri->segment(2) == ''  ? 'active' : '' ?>" href="<?= base_url() ?>kasbon/">Kasbon</a>
                        <?php
                        if ($this->session->userdata('role') == 1) {
                        ?>
                            <a class="collapse-item <?= $this->uri->segment(1) == 'kasbon' && $this->uri->segment(2) == 'add'  ? 'active' : '' ?>" href="<?= base_url() ?>kasbon/add">Tambah Kasbon</a>
                            <a class="collapse-item" <?= $this->uri->segment(1) == 'kasbon' && $this->uri->segment(2) == 'verfikasi'  ? 'active' : '' ?>href="<?= base_url() ?>kasbon/verifikasi">Verifikasi</a>
                            <!-- <a class="collapse-item" <?= $this->uri->segment(1) == 'kasbon' && $this->uri->segment(2) == 'detail_tabungan'  ? 'active' : '' ?>href="<?= base_url() ?>tabungan/detail_tabungan">Detail Tabungan</a> -->

                        <?php
                        }
                        ?>
                    </div>
                </div>
            </li>

            <!-- Nav Item - Pages Collapse Menu -->
            <!-- <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Components</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="buttons.html">Buttons</a>
                        <a class="collapse-item" href="cards.html">Cards</a>
                    </div>
                </div>
            </li> -->

            <!-- <div class="sidebar-heading">
                Interface
            </div> -->

            <!-- Nav Item - Pages Collapse Menu -->
            <!-- <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseTwo"
                    aria-expanded="true" aria-controls="collapseTwo">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Components</span>
                </a>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Components:</h6>
                        <a class="collapse-item" href="buttons.html">Buttons</a>
                        <a class="collapse-item" href="cards.html">Cards</a>
                    </div>
                </div>
            </li> -->

            <!-- Nav Item - Utilities Collapse Menu -->
            <!-- <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseUtilities"
                    aria-expanded="true" aria-controls="collapseUtilities">
                    <i class="fas fa-fw fa-wrench"></i>
                    <span>Utilities</span>
                </a>
                <div id="collapseUtilities" class="collapse" aria-labelledby="headingUtilities"
                    data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Custom Utilities:</h6>
                        <a class="collapse-item" href="utilities-color.html">Colors</a>
                        <a class="collapse-item" href="utilities-border.html">Borders</a>
                        <a class="collapse-item" href="utilities-animation.html">Animations</a>
                        <a class="collapse-item" href="utilities-other.html">Other</a>
                    </div>
                </div>
            </li> -->

            <!-- Divider -->
            <!-- <hr class="sidebar-divider"> -->

            <!-- Heading -->
            <!-- <div class="sidebar-heading">
                Addons
            </div> -->

            <!-- Nav Item - Pages Collapse Menu -->
            <!-- <li class="nav-item">
                <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages"
                    aria-expanded="true" aria-controls="collapsePages">
                    <i class="fas fa-fw fa-folder"></i>
                    <span>Pages</span>
                </a>
                <div id="collapsePages" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                    <div class="bg-white py-2 collapse-inner rounded">
                        <h6 class="collapse-header">Login Screens:</h6>
                        <a class="collapse-item" href="login.html">Login</a>
                        <a class="collapse-item" href="register.html">Register</a>
                        <a class="collapse-item" href="forgot-password.html">Forgot Password</a>
                        <div class="collapse-divider"></div>
                        <h6 class="collapse-header">Other Pages:</h6>
                        <a class="collapse-item" href="404.html">404 Page</a>
                        <a class="collapse-item" href="blank.html">Blank Page</a>
                    </div>
                </div>
            </li> -->

            <!-- Nav Item - Charts -->
            <!-- <li class="nav-item">
                <a class="nav-link" href="charts.html">
                    <i class="fas fa-fw fa-chart-area"></i>
                    <span>Charts</span></a>
            </li> -->

            <!-- Nav Item - Tables -->
            <!-- <li class="nav-item">
                <a class="nav-link" href="tables.html">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Tables</span></a>
            </li> -->

            <!-- <li class="nav-item">
                <a class="nav-link" href="tables.html">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Tables</span></a>
            </li> -->
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <li class="nav-item">
                <a class="nav-link" href="<?= base_url('auth/logout') ?>">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span></a>
            </li>

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

            <!-- Sidebar Message -->
            <!-- <div class="sidebar-card d-none d-lg-flex">
                <img class="sidebar-card-illustration mb-2" src="<?= base_url('assets/admin/') ?>img/undraw_rocket.svg" alt="...">
                <p class="text-center mb-2"><strong>SB Admin Pro</strong> is packed with premium features, components, and more!</p>
                <a class="btn btn-success btn-sm" href="https://startbootstrap.com/theme/sb-admin-pro">Upgrade to Pro!</a>
            </div> -->

        </ul>
        <!-- End of Sidebar -->