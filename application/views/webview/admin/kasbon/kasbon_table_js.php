<script>
    $(document).ready(function() {
        $('#nasabah_search').select2({
            // Optional configuration:
            placeholder: 'Cari Nasabah', // Text displayed when nothing is selected
        });

        $('#nasabah_search_export').select2({
            // Optional configuration:
            placeholder: 'Cari Nasabah', // Text displayed when nothing is selected
        });

        $('#nasabah_search').on('change', function() {
            var selected_cib = $(this).val();
            var detail_limit_container = $('#nasabah_detail_kredit_limit'); // Cache the container element
            var detail_usage_container = $('#nasabah_detail_kredit_usage'); // Cache the container element

            // 1. Check for 'ALL' or empty selection
            if (selected_cib === 'ALL' || selected_cib === '') {
                // A. Hide the detail fields and clear the inputs
                detail_container.addClass('d-none');
                $('#limit_kredit_nasabah').val('');
                $('#usage_kredit_nasabah').val('');
                return;
            }

            // 2. Make an AJAX call to the backend
            $.ajax({
                url: '<?= base_url("kasbon/get_nasabah_detail") ?>',
                type: 'POST',
                data: {
                    no_cib: selected_cib
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;

                        // B. SHOW the detail fields
                        detail_limit_container.removeClass('d-none');
                        detail_usage_container.removeClass('d-none');

                        // Populate data (as before)
                        var formatted_limit = new Intl.NumberFormat('id-ID').format(data.limit_kredit);
                        var formatted_usage = new Intl.NumberFormat('id-ID').format(data.usage_kredit);

                        $('#limit_kredit_nasabah').val(formatted_limit);
                        $('#usage_kredit_nasabah').val(formatted_usage);
                    } else {
                        // C. Hide and clear if data is not found for the selected customer
                        detail_limit_container.addClass('d-none');
                        detail_usage_container.addClass('d-none');
                        $('#limit_kredit_nasabah').val('');
                        $('#usage_kredit_nasabah').val('');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                    // D. Hide on error
                    detail_limit_container.addClass('d-none');
                    detail_usage_container.addClass('d-none');
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        var table = $('#dataTable').DataTable({
            // responsive: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo site_url('kasbon/ajax_list') ?>",
                type: "POST",
                data: function(d) {
                    d.nasabah = $('#nasabah_search').val();
                },
                dataSrc: function(json) {
                    // Check if the custom total exists in the JSON response
                    if (json.total_nominal_sum !== undefined) {
                        // Update the HTML element with the total value
                        $('#total_nominal_display').text('Rp' + json.total_nominal_sum);
                    }

                    if (json.total_nominal_kredit_sum !== undefined) {
                        // Update the HTML element with the total value
                        $('#total_nominal_kredit_display').text('Rp' + json.total_nominal_kredit_sum);
                    }

                    if (json.total_nominal_cash_sum !== undefined) {
                        // Update the HTML element with the total value
                        $('#total_nominal_cash_display').text('Rp' + json.total_nominal_cash_sum);
                    }
                    // Return the 'data' array for the DataTable to render
                    return json.data;
                }
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                // targets: -1,
                orderable: false
            }], // The 'dom' property has been replaced with the 'layout' option
            // to place the search bar at the top, and the info and pagination controls at the bottom.
            // layout: {
            //     topStart: 'search',
            //     topEnd: '',
            //     bottomStart: 'info',
            //     bottomEnd: 'paging'
            // }
        });

        $('#nasabah_search').on('change', function() {
            table.ajax.reload();
        });
    })

    function confirmDelete(no_cib) {
        Swal.fire({
            title: 'Anda Yakin?',
            text: 'Data yang dihapus tidak dapat dikembalikan!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545', // Red color for danger
            cancelButtonColor: '#6c757d', // Gray color for cancel
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            // Check if the user clicked the confirm button (Ya, Hapus!)
            if (result.isConfirmed) {
                // If confirmed, execute the redirection to the delete endpoint
                window.location.href = "<?= base_url('nasabah/delete/') ?>" + no_cib;
            }
        });
    }
</script>