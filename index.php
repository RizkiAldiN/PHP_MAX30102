<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Sensor MAX30102</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #444;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background: #007bff;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        tr:hover {
            background: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Data Sensor MAX30102</h1>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Detak Jantung (BPM)</th>
                    <th>Kadar Oksigen (%)</th>
                    <th>Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (!empty($datetime_data)) {
                    foreach (array_reverse($datetime_data) as $index => $datetime) {
                        echo "<tr>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $detak_jantung_data[$index] . "</td>";
                        echo "<td>" . $kadar_oksigen_data[$index] . "</td>";
                        echo "<td>" . $datetime . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Tidak ada data tersedia</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <h2>Grafik Pemantauan</h2>
        <canvas id="grafik" width="400" height="200"></canvas>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Data untuk grafik
        var detakJantungData = <?php echo json_encode(array_reverse($detak_jantung_data)); ?>;
        var kadarOksigenData = <?php echo json_encode(array_reverse($kadar_oksigen_data)); ?>;
        var datetimeData = <?php echo json_encode(array_reverse($datetime_data)); ?>;

        // Membuat grafik
        var ctx = document.getElementById('grafik').getContext('2d');
        var grafik = new Chart(ctx, {
            type: 'line',
            data: {
                labels: datetimeData,
                datasets: [{
                    label: 'Detak Jantung (BPM)',
                    data: detakJantungData,
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    fill: false
                }, {
                    label: 'Kadar Oksigen (%)',
                    data: kadarOksigenData,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Waktu'
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nilai'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
