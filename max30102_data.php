<?php
// Konfigurasi database
$hostname = "localhost"; 
$username = "root"; 
$password = ""; 
$database = "sensor_max30102"; 

// Koneksi ke database
$conn = mysqli_connect($hostname, $username, $password, $database);

// Periksa koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Jika menerima data POST
if (isset($_POST['detak_jantung']) && isset($_POST['kadar_oksigen'])) {
    $detak_jantung = $_POST['detak_jantung'];
    $kadar_oksigen = $_POST['kadar_oksigen'];

    // Simpan data ke database
	$sql = "INSERT INTO max30102 (`Detak Jantung`, `Kadar Oksigen`) VALUES ('$detak_jantung', '$kadar_oksigen')";
    if (mysqli_query($conn, $sql)) {
        echo "Data berhasil disimpan!";
    } else {
        echo "Gagal menyimpan data: " . mysqli_error($conn);
    }
}

// Ambil data dari database
$sql = "SELECT * FROM max30102 ORDER BY datetime DESC";
$result = mysqli_query($conn, $sql);

// Data untuk grafik
$detak_jantung_data = [];
$kadar_oksigen_data = [];
$datetime_data = [];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $detak_jantung_data[] = $row['Detak Jantung'];
        $kadar_oksigen_data[] = $row['Kadar Oksigen'];
        $datetime_data[] = $row['datetime'];
    }
}

// Tutup koneksi database
mysqli_close($conn);
?>

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
