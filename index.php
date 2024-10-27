<?php
include_once("koneksi.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>To Do List</title>

        <style>
        td {
            vertical-align: middle; /* Menengahkan isi tabel secara vertikal */
        }
        .btn {
            margin: 0 2px; /* Memberikan sedikit jarak antara tombol */
        }
    </style>
    
</head>

<body>
    <div class="container mt-5">
        <h3>To Do List <small class="text-muted">Catat semua hal yang akan kamu kerjakan di sini.</small></h3>
        <hr>

        <!-- Form Input Data -->
        <form class="form row" method="POST" action="" name="myForm">
            <?php
            $isi = '';
            $tgl_awal = '';
            $tgl_akhir = '';
            $id = '';

            // Cek jika ada ID dan aksi ubah
            if (isset($_GET['id']) && isset($_GET['aksi']) && $_GET['aksi'] == 'ubah') {
                $id = $_GET['id'];
                $ambil = mysqli_query($mysqli, "SELECT * FROM kegiatan WHERE id='$id'");
                $row = mysqli_fetch_array($ambil);
                if ($row) {
                    $isi = $row['isi'];
                    $tgl_awal = $row['tgl_awal'];
                    $tgl_akhir = $row['tgl_akhir'];
                }
                echo '<input type="hidden" name="id" value="' . $id . '">';
            }
            ?>

            <div class="col">
                <label for="inputIsi" class="form-label fw-bold">Kegiatan</label>
                <input type="text" class="form-control" name="isi" id="inputIsi" placeholder="Kegiatan" value="<?php echo $isi; ?>" required>
            </div>
            <div class="col">
                <label for="inputTanggalAwal" class="form-label fw-bold">Tanggal Awal</label>
                <input type="date" class="form-control" name="tgl_awal" id="inputTanggalAwal" placeholder="Tanggal Awal" value="<?php echo $tgl_awal; ?>" required>
            </div>
            <div class="col mb-2">
                <label for="inputTanggalAkhir" class="form-label fw-bold">Tanggal Akhir</label>
                <input type="date" class="form-control" name="tgl_akhir" id="inputTanggalAkhir" placeholder="Tanggal Akhir" value="<?php echo $tgl_akhir; ?>" required>
            </div>
            <div class="col">
                <button type="submit" class="btn btn-primary rounded-pill px-3" name="simpan">Simpan</button>
            </div>
        </form>

        <!-- Table -->
        <table class="table table-hover mt-4">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Kegiatan</th>
                    <th scope="col">Awal</th>
                    <th scope="col">Akhir</th>
                    <th scope="col">Status</th>
                    <th scope="col">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = mysqli_query($mysqli, "SELECT * FROM kegiatan ORDER BY status, tgl_awal");
                $no = 1;
                while ($data = mysqli_fetch_array($result)) {
                ?>
                    <tr>
                        <th scope="row"><?php echo $no++ ?></th>
                        <td><?php echo $data['isi']; ?></td>
                        <td><?php echo $data['tgl_awal']; ?></td>
                        <td><?php echo $data['tgl_akhir']; ?></td>
                        <td>
                            <?php
                            // Menampilkan status dan membuat link untuk mengubah status
                            if ($data['status'] == '1') {
                                echo " <a class='btn btn-warning rounded-pill px-3' href='index.php?id=" . $data['id'] . "&aksi=ubah_status&status=0'>Belum</a>";
                            } else {
                                echo " <a class='btn btn-success rounded-pill px-3' href='index.php?id=" . $data['id'] . "&aksi=ubah_status&status=1'>Sudah</a>";
                            }
                            ?>
                        </td>
                        <td>
                            <a class="btn btn-danger rounded-pill px-3" href="index.php?id=<?php echo $data['id']; ?>&aksi=hapus">Hapus</a>
                            <a class="btn btn-primary rounded-pill px-3" href="index.php?id=<?php echo $data['id']; ?>&aksi=ubah">Ubah</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- aksi Simpan, Hapus, Ubah, dan Ubah Status -->
        <?php
        if (isset($_POST['simpan'])) {
            if (isset($_POST['id']) && !empty($_POST['id'])) {
                
                //  Ubah data yang sudah ada
                $ubah = mysqli_query($mysqli, "UPDATE kegiatan SET
                    isi = '" . $_POST['isi'] . "',
                    tgl_awal = '" . $_POST['tgl_awal'] . "',
                    tgl_akhir = '" . $_POST['tgl_akhir'] . "'
                    WHERE id = '" . $_POST['id'] . "'");
            } else {

                // Tambahkan data baru
                $tambah = mysqli_query($mysqli, "INSERT INTO kegiatan(isi, tgl_awal, tgl_akhir, status) VALUES (
                    '" . $_POST['isi'] . "',
                    '" . $_POST['tgl_awal'] . "',
                    '" . $_POST['tgl_akhir'] . "',
                    '0')");
            }
            echo "<script>document.location='index.php';</script>";
        }
            // Mengubah status berdasarkan aksi
            if (isset($_GET['aksi'])) {
            if ($_GET['aksi'] == 'hapus') {
                $hapus = mysqli_query($mysqli, "DELETE FROM kegiatan WHERE id = '" . $_GET['id'] . "'");
                echo "<script>document.location='index.php';</script>";
                
                // Update status di database
            } elseif ($_GET['aksi'] == 'ubah_status') {
                $id = $_GET['id'];
                $status = $_GET['status'];
                $ubah_status = mysqli_query($mysqli, "UPDATE kegiatan SET status = '$status' WHERE id = '$id'");
                echo "<script>document.location='index.php';</script>";
            }
        }
        ?>
    </div>

    <script src="assets/js/bootstrap.bundle.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-HwwvtgBNo3bZJJLYd8oVXjrBZt8cqVSpeBNS5n7C8IVInixGAoxmnlMuBnhbgrkm" crossorigin="anonymous"></script>

</body>

</html>
