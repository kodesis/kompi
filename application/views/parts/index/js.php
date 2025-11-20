<?php
// Check for success message first, as it's typically the most important
if ($this->session->flashdata('message_name')) {
?>
    <script>
        Swal.fire({
            title: "Success!! ",
            text: '<?= $this->session->flashdata('message_name') ?>',
            icon: "success",
            confirmButtonText: 'Lanjut Login',

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
<!-- Scroll Top -->
<a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

<!-- Preloader -->
<div id="preloader"></div>

<!-- Vendor JS Files -->
<script src="<?= base_url() ?>assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- <script src="<?= base_url() ?>assets/vendor/php-email-form/validate.js"></script> -->
<script src="<?= base_url() ?>assets/vendor/aos/aos.js"></script>
<script src="<?= base_url() ?>assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="<?= base_url() ?>assets/vendor/purecounter/purecounter_vanilla.js"></script>
<script src="<?= base_url() ?>assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
<script src="<?= base_url() ?>assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>
<script src="<?= base_url() ?>assets/vendor/swiper/swiper-bundle.min.js"></script>

<!-- Main JS File -->
<script src="assets/js/main.js"></script>

</body>

</html>