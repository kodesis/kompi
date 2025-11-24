<script>
    function verifikasi_Kasbon() {
        const ttltitleValue = $('#kode_add').val();


        if (!ttltitleValue) {
            swal.fire({
                customClass: 'slow-animation',
                icon: 'error',
                showConfirmButton: false,
                title: 'Kolom Kode Verifikasi Tidak Boleh Kosong',
                timer: 1500
            });
        } else {
            var url;
            var formData;
            url = "<?php echo site_url('kasbon/proses_verifikasi') ?>";

            // window.location = url_base;
            var formData = new FormData($("#verifikasi_kasbon")[0]);
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                dataType: "JSON",
                beforeSend: function() {
                    swal.fire({
                        icon: 'info',
                        timer: 3000,
                        showConfirmButton: false,
                        title: 'Loading...'

                    });
                },
                success: function(data) {
                    /* if(!data.status)alert("ho"); */
                    if (!data.status) {
                        Swal.fire({
                            icon: 'error', // 🚨 Use 'error' for the icon type
                            title: 'Gagal Menyimpan Data',
                            text: data.Pesan || 'Terjadi kesalahan saat menyimpan data.', // Use the server's error message (data.Pesan)
                            footer: 'Silakan coba lagi atau hubungi administrator.'
                        });
                    } else {

                        // document.getElementById('rumahadat').reset();
                        // $('#add_modal').modal('hide');
                        (JSON.stringify(data));
                        // alert(data)
                        swal.fire({
                            customClass: 'slow-animation',
                            icon: 'success',
                            showConfirmButton: false,
                            title: 'Berhasil Verifikasi Kasbon',
                            timer: 1500
                        });
                        // location.reload();
                        setTimeout(function() {
                            console.log('Redirecting to Verifikasi...');
                            // location.href = '<?= base_url('Anggota') ?>';
                        }, 1500); // Delay for smooth transition
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    swal.fire('Operation Failed!', errorThrown, 'error');
                },
                complete: function() {
                    console.log('Editing job done');
                }
            });


        }
    }
</script>