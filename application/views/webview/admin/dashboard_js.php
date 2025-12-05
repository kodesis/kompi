<script>
    $(document).ready(function() {
        // Tangani klik pada tombol di dalam list-group
        $('#tabungan-menu .list-group-item-action').on('click', function() {
            // 1. Dapatkan target konten dari atribut data-target
            var targetId = $(this).data('target');

            // --- LOGIKA TOMBOL (NAVIGASI) ---

            // Hapus kelas 'active' dan tambahkan 'bg-gray-100' dari SEMUA tombol
            $('#tabungan-menu .list-group-item-action').removeClass('active').addClass('bg-gray-100');

            // Tambahkan kelas 'active' dan hapus 'bg-gray-100' dari tombol yang diklik
            $(this).addClass('active').removeClass('bg-gray-100');

            // --- LOGIKA KONTEN ---

            // Sembunyikan SEMUA div konten dengan class 'tab-content'
            $('.tab-content').addClass('d-none');

            // Tampilkan div konten yang sesuai dengan tombol yang diklik
            $(targetId).removeClass('d-none');
        });
    });
</script>
<script>
    // --- DEFINISI FUNGSI number_format (Pemisah Ribuan Titik) ---
    function number_format(number, decimals, dec_point, thousands_sep) {
        // Set default values if not provided
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? ',' : dec_point,
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                    .toFixed(prec);
            };
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    // --- Inject PHP Data into JavaScript ---
    // Pastikan ID elemen chart Anda adalah myAreaChart_simpanan_bulanan
    var chartLabelsSimpanan = <?= $tabungan_bulanan_labels ?>;
    var chartDataSimpanan = <?= $tabungan_bulanan_data ?>;

    var ctx = document.getElementById("myAreaChart_simpanan_bulanan");
    var myAreaChartSimpanan = new Chart(ctx, {
        type: 'line',
        data: {
            // Gunakan data bulanan dari awal tahun
            labels: chartLabelsSimpanan,
            datasets: [{
                label: "Total Simpanan Bulanan",
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                // ... (properti styling lainnya) ...
                pointBorderWidth: 2,
                // Gunakan data nominal bulanan
                data: chartDataSimpanan,
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        // Maksimum 12 bulan
                        maxTicksLimit: 12
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        // Format Rupiah: 0 desimal, pemisah ribuan titik
                        callback: function(value, index, values) {
                            return 'Rp' + number_format(value, 0, ',', '.');
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                // ... (properti tooltips lainnya) ...
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        // Format Rupiah di tooltip
                        return datasetLabel + ': Rp' + number_format(tooltipItem.yLabel, 0, ',', '.');
                    }
                }
            }
        }
    });
</script>
<script>
    function number_format(number, decimals, dec_point, thousands_sep) {
        // Set default values if not provided
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep, // Use dot for thousands
            dec = (typeof dec_point === 'undefined') ? ',' : dec_point, // Use comma for decimals
            s = '',
            toFixedFix = function(n, prec) {
                var k = Math.pow(10, prec);
                return '' + (Math.round(n * k) / k)
                    .toFixed(prec);
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }

    // PHP data injected into JavaScript
    var chartLabels = <?= json_encode($chart_labels_yearly_tabungan); ?>;
    var chartData = <?= json_encode($chart_data_yearly_tabungan); ?>;


    var ctx = document.getElementById("myBarChart_simpanan_tahunan");
    var myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: "Total Simpanan",
                backgroundColor: "#4e73df",
                hoverBackgroundColor: "#2e59d9",
                borderColor: "#4e73df",
                data: chartData,
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    time: {
                        unit: 'year'
                    },
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 5
                    },
                    maxBarThickness: 25,
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        maxTicksLimit: 5,
                        padding: 10,
                        // 🏆 UPDATED: Explicitly set decimals=0, dec_point=',', thousands_sep='.'
                        callback: function(value, index, values) {
                            return 'Rp' + number_format(value, 0, ',', '.');
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                // ... other tooltip settings
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        // 🏆 UPDATED: Explicitly set decimals=0, dec_point=',', thousands_sep='.'
                        return datasetLabel + ': Rp' + number_format(tooltipItem.yLabel, 0, ',', '.');
                    }
                }
            },
        }
    });
</script>
<script>
    // --- Inject PHP Data into JavaScript ---
    // NOTE: This assumes you are echoing the PHP variables in your CodeIgniter View:
    var chartLabels = <?= $kasbon_labels ?>;
    var chartData = <?= $kasbon_data ?>;

    var ctx = document.getElementById("myAreaChart_kredit_bulanan");
    var myLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            // --- Use Dynamic Labels ---
            labels: chartLabels,
            datasets: [{
                label: "Kasbon Nominal", // Changed label to reflect the data
                lineTension: 0.3,
                backgroundColor: "rgba(78, 115, 223, 0.05)",
                borderColor: "rgba(78, 115, 223, 1)",
                pointRadius: 3,
                pointBackgroundColor: "rgba(78, 115, 223, 1)",
                pointBorderColor: "rgba(78, 115, 223, 1)",
                pointHoverRadius: 3,
                pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                pointHitRadius: 10,
                pointBorderWidth: 2,
                // --- Use Dynamic Data ---
                data: chartData,
            }],
        },
        options: {
            maintainAspectRatio: false,
            layout: {
                padding: {
                    left: 10,
                    right: 25,
                    top: 25,
                    bottom: 0
                }
            },
            scales: {
                xAxes: [{
                    // Removed 'time' unit since we are using month names
                    gridLines: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        maxTicksLimit: 6 // Set max ticks to 6 for the 6 months
                    }
                }],
                yAxes: [{
                    ticks: {
                        maxTicksLimit: 5,
                        padding: 10,
                        // --- Change Currency Symbol to Rp ---
                        // NOTE: This assumes 'number_format' is a globally available JavaScript function
                        callback: function(value, index, values) {
                            return 'Rp' + number_format(value);
                        }
                    },
                    gridLines: {
                        color: "rgb(234, 236, 244)",
                        zeroLineColor: "rgb(234, 236, 244)",
                        drawBorder: false,
                        borderDash: [2],
                        zeroLineBorderDash: [2]
                    }
                }],
            },
            legend: {
                display: false
            },
            tooltips: {
                backgroundColor: "rgb(255,255,255)",
                bodyFontColor: "#858796",
                titleMarginBottom: 10,
                titleFontColor: '#6e707e',
                titleFontSize: 14,
                borderColor: '#dddfeb',
                borderWidth: 1,
                xPadding: 15,
                yPadding: 15,
                displayColors: false,
                intersect: false,
                mode: 'index',
                caretPadding: 10,
                callbacks: {
                    label: function(tooltipItem, chart) {
                        var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                        // --- Change Currency Symbol to Rp ---
                        return datasetLabel + ': Rp' + number_format(tooltipItem.yLabel);
                    }
                }
            }
        }
    });
</script>