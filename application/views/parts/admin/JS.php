<?php
// Check for success message first, as it's typically the most important
if ($this->session->flashdata('message_name')) {
?>
    <script>
        Swal.fire({
            title: "Success!! ",
            text: '<?= $this->session->flashdata('message_name') ?>',
            icon: "success",
            confirmButtonText: 'Konfirmasi',

        });
    </script>
<?php
    unset($_SESSION['message_name']);
}
// Then check for error message
else if ($this->session->flashdata('message_error')) {
?>
    <script>
        Swal.fire({
            title: "Error!! ",
            text: '<?= $this->session->flashdata('message_error') ?>',
            icon: "error",
        });
    </script>
<?php
    unset($_SESSION['message_error']);
}
// If no flash data messages are present, show the default warning
?>

<!-- Bootstrap core JavaScript-->
<script src="<?= base_url('assets/admin/') ?>vendor/jquery/jquery.min.js"></script>
<script src="<?= base_url('assets/admin/') ?>vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="<?= base_url('assets/admin/') ?>vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="<?= base_url('assets/admin/') ?>js/sb-admin-2.min.js"></script>

<!-- Page level plugins -->
<script src="<?= base_url('assets/admin/') ?>vendor/chart.js/Chart.min.js"></script>

<!-- Page level plugins -->
<script src="<?= base_url('assets/admin/') ?>vendor/datatables/jquery.dataTables.min.js"></script>
<script src="<?= base_url('assets/admin/') ?>vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

<!-- Page level custom scripts -->
<!-- <script src="<?= base_url('assets/admin/') ?>js/demo/datatables-demo.js"></script> -->

<!-- Page level custom scripts -->
<script src="<?= base_url('assets/admin/') ?>js/demo/chart-area-demo.js"></script>
<script src="<?= base_url('assets/admin/') ?>js/demo/chart-pie-demo.js"></script>

</body>

</html>