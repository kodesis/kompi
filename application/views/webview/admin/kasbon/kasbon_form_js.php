<script>
    $(document).ready(function() {
        $('#nasabah_add').select2({
            // Optional configuration:
            placeholder: 'Cari Nasabah', // Text displayed when nothing is selected
            allowClear: true // Allows a user to clear the selection
        });

        $('#nasabah_add').on('change', function() {
            var nasabah_id = $(this).val();
            var infoSection = $('#kredit_info_section');

            // Define all elements that should be disabled/enabled
            var transactionFields = $('#nominal_add, #nominal_kredit_add, #nominal_cash_add, #submit_btn');

            if (nasabah_id) {
                // Show the loading section immediately
                infoSection.show();

                // Clear previous values while waiting for AJAX
                $('#kredit_limit').val('Loading...');
                $('#kredit_usage').val('Loading...');
                transactionFields.prop('disabled', true);

                // Perform AJAX request to your controller/model to get credit data
                $.ajax({
                    url: '<?= base_url('kasbon/get_kredit_info') ?>/' + nasabah_id,
                    method: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // 1. Get the values, removing formatting for a numerical comparison
                            // Assuming the server returns string/formatted numbers (e.g., "10.000")
                            var limit = parseFloat(response.data.limit.replace(/\./g, '').replace(/,/g, ''));
                            var usage = parseFloat(response.data.usage.replace(/\./g, '').replace(/,/g, ''));
                            var remaining = limit - usage;
                            // 2. Update the fields with the retrieved data (formatted)
                            $('#kredit_limit').val(response.data.limit);
                            $('#kredit_usage').val(response.data.usage);
                            $('#kredit_remaining').val(remaining.toLocaleString('id-ID'));
                            // 🌟 3. NEW LOGIC: Check if Usage equals Limit 🌟
                            if (usage >= limit) { // Use >= for safety, though they should be equal
                                transactionFields.prop('disabled', true);
                                // 🚀 SWEETALERT2 IMPLEMENTATION 🚀
                                Swal.fire({
                                    icon: 'error', // Displays a red X icon
                                    title: 'Limit Kredit Habis',
                                    text: 'Transaksi baru tidak dapat diajukan karena limit kredit nasabah telah terpakai sepenuhnya.',
                                    confirmButtonText: 'Tutup',
                                    footer: 'Silakan cek sisa limit nasabah.'
                                });
                            } else {
                                // Enable the transaction fields (Nominal, etc.)
                                transactionFields.prop('disabled', false);
                            }
                        } else {
                            // Handle error (e.g., limit not found)
                            alert('Data limit kredit tidak ditemukan.');
                            $('#kredit_limit').val('N/A');
                            $('#kredit_usage').val('N/A');
                            transactionFields.prop('disabled', true); // Disable on error
                        }
                    },
                    error: function() {
                        alert('Terjadi kesalahan saat mengambil data.');
                        infoSection.hide();
                        transactionFields.prop('disabled', true); // Disable on error
                    }
                });
            } else {
                // Hide and disable if "Pilih Nasabah" is selected
                infoSection.hide();
                $('#kredit_limit').val('');
                $('#kredit_usage').val('');
                transactionFields.prop('disabled', true);
            }
        });
    });

    // Helper function to clean and parse the numeric value
    function cleanNumber(value) {
        // Removes dots (thousands separator) and replaces commas (if used as decimal point)
        // Adjust regex if your currency format is different (e.g., comma for thousands)
        if (typeof value === 'string') {
            // Removes dots and commas, then parses as float
            return parseFloat(value.replace(/\./g, '').replace(/,/g, ''));
        }
        return parseFloat(value) || 0;
    }

    // Helper function to format the number back for display (use your existing formatter if available)
    function formatNumber(number) {
        // Format to Indonesian standard with thousands separators (e.g., 10.000)
        return number.toLocaleString('id-ID');
    }

    $('#nominal_add').on('input', function() {
        // 1. Get the current Nominal entered by the user
        var nominal = cleanNumber($(this).val());

        // 2. Get the available Sisa Kredit (Remaining Credit)
        // We use the value from the field that was calculated after AJAX
        var sisaKredit = cleanNumber($('#kredit_remaining').val());

        var nominalKredit = 0;
        var nominalCash = 0;

        // --- CALCULATION LOGIC ---

        if (nominal <= sisaKredit) {
            // CASE 1: Nominal is LESS THAN or EQUAL to Sisa Kredit
            // Entire Nominal is covered by Credit (Kredit)
            nominalKredit = nominal;
            nominalCash = 0;

        } else {
            // CASE 2: Nominal is MORE THAN Sisa Kredit
            // Kredit takes the full available Sisa, and the rest goes to Cash

            // Kredit is capped at the available remaining limit
            nominalKredit = sisaKredit;

            // Cash covers the remainder
            nominalCash = nominal - sisaKredit;
        }

        // --- UPDATE OUTPUT FIELDS ---

        $('#nominal_kredit_add').val(formatNumber(nominalKredit));
        $('#nominal_cash_add').val(formatNumber(nominalCash));

        // Optional: If nominal is 0 or NaN, clear the output fields
        if (nominal === 0 || isNaN(nominal)) {
            $('#nominal_kredit_add').val(formatNumber(0));
            $('#nominal_cash_add').val(formatNumber(0));
        }
    });

    function save_Kasbon() {
        const ttltitleValue = $('#nasabah_add').val();
        const ttlthumbnailValue = $('#nominal_add').val();


        if (!ttltitleValue) {
            swal.fire({
                customClass: 'slow-animation',
                icon: 'error',
                showConfirmButton: false,
                title: 'Kolom Anggota Tidak Boleh Kosong',
                timer: 1500
            });
        } else if (!ttlthumbnailValue || ttlthumbnailValue == 0) {
            swal.fire({
                customClass: 'slow-animation',
                icon: 'error',
                showConfirmButton: false,
                title: 'Kolom Nominal Tidak Boleh Kosong',
                timer: 1500
            });
        } else {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    InputEvent: 'form-control',
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Ingin Menambahkan Data Kasbon?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tambahkan',
                cancelButtonText: 'Tidak',
                reverseButtons: true
            }).then((result) => {

                if (result.isConfirmed) {

                    var url;
                    var formData;
                    url = "<?php echo site_url('kasbon/save_kasbon') ?>";

                    // window.location = url_base;
                    var formData = new FormData($("#add_kasbon")[0]);
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
                            if (!data.status) swal.fire('Gagal menyimpan data', 'error :' + data.Pesan);
                            else {

                                // document.getElementById('rumahadat').reset();
                                // $('#add_modal').modal('hide');
                                (JSON.stringify(data));
                                // alert(data)
                                swal.fire({
                                    customClass: 'slow-animation',
                                    icon: 'success',
                                    showConfirmButton: false,
                                    title: 'Berhasil Menambahkan Kasbon',
                                    timer: 1500
                                });
                                // location.reload();
                                setTimeout(function() {
                                    console.log('Redirecting to Verifikasi...');
                                    location.href = '<?= base_url('kasbon/verifikasi') ?>';
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

            })
        }
    }
</script>