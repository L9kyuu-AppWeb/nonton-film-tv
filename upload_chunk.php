<?php
if (isset($_POST['upload_chunk'])) {
    $folder = $_POST['current_folder'];
    $chunkNumber = (int)$_POST['chunk_number'];
    $totalChunks = (int)$_POST['total_chunks'];
    $fileName = $_POST['file_name'];

    // Pastikan folder tujuan ada
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    // Simpan chunk di folder sementara
    $chunkPath = $folder . '/' . $fileName . '.part' . $chunkNumber;
    if (move_uploaded_file($_FILES['file']['tmp_name'], $chunkPath)) {
        if ($chunkNumber + 1 === $totalChunks) {
            // Gabungkan semua chunk setelah upload selesai
            $finalPath = $folder . '/' . $fileName;
            $finalFile = fopen($finalPath, 'w');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkFilePath = $folder . '/' . $fileName . '.part' . $i;
                $chunkFile = fopen($chunkFilePath, 'r');
                fwrite($finalFile, fread($chunkFile, filesize($chunkFilePath)));
                fclose($chunkFile);
                unlink($chunkFilePath); // Hapus chunk setelah digabung
            }

            fclose($finalFile);

            echo json_encode([
                'status' => 'success',
                'message' => 'Upload selesai dan file berhasil digabung.'
            ]);
        } else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Chunk ' . ($chunkNumber + 1) . ' dari ' . $totalChunks . ' berhasil diunggah.'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal mengunggah chunk ' . ($chunkNumber + 1) . '.'
        ]);
    }
}
?>
