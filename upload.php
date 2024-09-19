<?php
// Fungsi untuk membuat folder
if (isset($_POST['create_folder'])) {
    $folder_name = $_POST['folder_name'];
    $path = 'videos/' . $folder_name;

    if (!file_exists($path)) {
        mkdir($path, 0777, true);
        $response = ['status' => 'success', 'message' => "Folder '$folder_name' berhasil dibuat!"];
    } else {
        $response = ['status' => 'error', 'message' => "Folder sudah ada!"];
    }
    echo json_encode($response);
    exit;
}

// Fungsi untuk mengunggah file
if (isset($_POST['upload_file']) && isset($_POST['current_folder'])) {
    $current_folder = $_POST['current_folder'];
    $file = $_FILES['file'];

    if (file_exists($current_folder)) {
        $allowed_types = ['mp4', 'mkv', 'avi'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_types)) {
            $upload_path = $current_folder . '/' . basename($file['name']);
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $response = ['status' => 'success', 'message' => 'File berhasil diunggah!'];
            } else {
                $response = ['status' => 'error', 'message' => 'Gagal mengunggah file!'];
            }
        } else {
            $response = ['status' => 'error', 'message' => 'Hanya file film yang diperbolehkan (mp4, mkv, avi)!'];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'Folder tidak ditemukan!'];
    }
    echo json_encode($response);
    exit;
}

// Dapatkan semua folder di direktori 'videos/'
$folders = array_filter(glob('videos/*'), 'is_dir');
?>
