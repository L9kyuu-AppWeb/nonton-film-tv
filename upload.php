<?php
// Fungsi untuk membuat folder
if (isset($_POST['create_folder'])) {
    $folder_name = $_POST['folder_name'];
    $path = 'videos/' . $folder_name;
    
    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        echo "<div class='bg-green-100 text-green-700 p-3 rounded-lg'>Folder '$folder_name' berhasil dibuat!</div>";
    } else {
        echo "<div class='bg-yellow-100 text-yellow-700 p-3 rounded-lg'>Folder sudah ada!</div>";
    }
}

// Fungsi untuk mengunggah file
if (isset($_POST['upload_file']) && isset($_POST['current_folder'])) {
    $current_folder = $_POST['current_folder'];
    $file = $_FILES['file'];

    // Periksa apakah folder ada
    if (file_exists($current_folder)) {
        $allowed_types = ['mp4', 'mkv', 'avi'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            $upload_path = $current_folder . '/' . basename($file['name']);

            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                echo "<div class='bg-green-100 text-green-700 p-3 rounded-lg'>File berhasil diunggah!</div>";
            } else {
                echo "<div class='bg-red-100 text-red-700 p-3 rounded-lg'>Gagal mengunggah file!</div>";
            }
        } else {
            echo "<div class='bg-red-100 text-red-700 p-3 rounded-lg'>Hanya file film yang diperbolehkan (mp4, mkv, avi)!</div>";
        }
    } else {
        echo "<div class='bg-red-100 text-red-700 p-3 rounded-lg'>Folder tidak ditemukan!</div>";
    }
}

// Dapatkan semua folder di direktori 'videos/'
$folders = array_filter(glob('videos/*'), 'is_dir');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Film</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-center mb-8">Upload Film</h1>

        <!-- Form untuk membuat folder -->
        <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-xl font-semibold mb-4">Buat Folder</h2>
            <form method="POST" class="space-y-4">
                <input type="text" name="folder_name" class="w-full p-2 border border-gray-300 rounded-lg" placeholder="Nama Folder" required>
                <button type="submit" name="create_folder" class="w-full bg-blue-500 text-white py-2 rounded-lg">Buat Folder</button>
            </form>
        </div>

        <!-- Form untuk memilih folder dan mengunggah file -->
        <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md mt-8">
            <h2 class="text-xl font-semibold mb-4">Upload Film ke Folder</h2>
            <form method="POST" enctype="multipart/form-data" class="space-y-4">
                <label class="block">
                    <span class="text-gray-700">Pilih Folder:</span>
                    <select name="current_folder" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        <option value="">Pilih Folder</option>
                        <?php foreach ($folders as $folder): ?>
                            <option value="<?= $folder ?>"><?= basename($folder) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <input type="file" name="file" class="w-full p-2 border border-gray-300 rounded-lg" required>
                <button type="submit" name="upload_file" class="w-full bg-green-500 text-white py-2 rounded-lg">Upload</button>
            </form>
        </div>

        <!-- Tampilkan folder dan file di dalamnya -->
        <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md mt-8">
            <h2 class="text-xl font-semibold mb-4">Daftar Folder dan File</h2>
            <?php if (!empty($folders)): ?>
                <ul class="space-y-4">
                    <?php foreach ($folders as $folder): ?>
                        <li>
                            <strong class="block text-lg"><?= basename($folder) ?></strong>
                            <ul class="list-disc ml-5 mt-2">
                                <?php 
                                $files = array_filter(glob("$folder/*"), 'is_file');
                                foreach ($files as $file): ?>
                                    <li><?= basename($file) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-500">Tidak ada folder yang ditemukan.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
