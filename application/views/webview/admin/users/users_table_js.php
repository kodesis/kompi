<script>
    $(document).ready(function() {
        $('#dataTable').dataTable({
            // responsive: true,
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?php echo site_url('users/ajax_list') ?>",
                type: "POST"
            },
            order: [],
            iDisplayLength: 10,
            columnDefs: [{
                targets: -1,
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
                window.location.href = "<?= base_url('users/delete/') ?>" + no_cib;
            }
        });
    }
</script>