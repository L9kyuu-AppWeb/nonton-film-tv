<?php
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
            <form id="create-folder-form" class="space-y-4">
                <input type="text" id="folder-name" class="w-full p-2 border border-gray-300 rounded-lg" placeholder="Nama Folder" required>
                <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg">Buat Folder</button>
                <div id="folder-response"></div>
            </form>
        </div>

        <!-- Form untuk mengunggah file -->
        <div class="max-w-lg mx-auto bg-white p-6 rounded-lg shadow-md mt-8">
            <h2 class="text-xl font-semibold mb-4">Upload Film ke Folder</h2>
            <form id="upload-file-form" enctype="multipart/form-data" class="space-y-4">
                <label class="block">
                    <span class="text-gray-700">Pilih Folder:</span>
                    <select id="current-folder" class="w-full p-2 border border-gray-300 rounded-lg" required>
                        <option value="">Pilih Folder</option>
                        <?php foreach ($folders as $folder): ?>
                            <option value="<?= $folder ?>"><?= basename($folder) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <input type="file" id="file" class="w-full p-2 border border-gray-300 rounded-lg" required>
                <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg">Upload</button>
                <div id="upload-response"></div>
                <!-- Animasi Loader -->
                <div id="loader" class="hidden text-center">
                    <div class="spinner"></div>
                    <p id="progress-text"></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const createFolderForm = document.getElementById('create-folder-form');
            const folderResponse = document.getElementById('folder-response');
            const uploadFileForm = document.getElementById('upload-file-form');
            const uploadResponse = document.getElementById('upload-response');
            const loader = document.getElementById('loader');
            const progressText = document.getElementById('progress-text');

            // AJAX untuk membuat folder
            createFolderForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const folderName = document.getElementById('folder-name').value;
                const formData = new FormData();
                formData.append('create_folder', true);
                formData.append('folder_name', folderName);

                fetch('upload.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    folderResponse.innerHTML = `<div class="p-2 ${data.status === 'success' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'}">${data.message}</div>`;
                })
                .catch(error => console.error('Error:', error));
            });

            // AJAX untuk mengunggah file secara chunked
            uploadFileForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const currentFolder = document.getElementById('current-folder').value;
                const fileInput = document.getElementById('file').files[0];

                if (!fileInput || !currentFolder) {
                    uploadResponse.innerHTML = "<div class='bg-red-100 text-red-700 p-3 rounded-lg'>Pilih folder dan file untuk diunggah!</div>";
                    return;
                }

                const chunkSize = 5 * 1024 * 1024; // 5MB per chunk
                const totalChunks = Math.ceil(fileInput.size / chunkSize);
                let currentChunk = 0;

                const uploadChunk = () => {
                    const start = currentChunk * chunkSize;
                    const end = Math.min(fileInput.size, start + chunkSize);
                    const chunk = fileInput.slice(start, end);

                    const formData = new FormData();
                    formData.append('upload_chunk', true);
                    formData.append('current_folder', currentFolder);
                    formData.append('file', chunk);
                    formData.append('chunk_number', currentChunk);
                    formData.append('total_chunks', totalChunks);
                    formData.append('file_name', fileInput.name);

                    // Tampilkan animasi loader dan progress
                    loader.classList.remove('hidden');
                    progressText.innerText = `Mengunggah chunk ${currentChunk + 1} dari ${totalChunks}`;

                    fetch('upload_chunk.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            currentChunk++;
                            if (currentChunk < totalChunks) {
                                uploadChunk(); // Upload chunk berikutnya
                            } else {
                                loader.classList.add('hidden');
                                uploadResponse.innerHTML = `<div class="p-2 bg-green-100 text-green-700">${data.message}</div>`;
                            }
                        } else {
                            loader.classList.add('hidden');
                            uploadResponse.innerHTML = `<div class="p-2 bg-red-100 text-red-700">${data.message}</div>`;
                        }
                    })
                    .catch(error => {
                        loader.classList.add('hidden');
                        uploadResponse.innerHTML = "<div class='bg-red-100 text-red-700 p-3 rounded-lg'>Terjadi kesalahan saat mengunggah file!</div>";
                    });
                };

                uploadChunk(); // Mulai unggah chunk pertama
            });
        });
    </script>

    <style>
        /* CSS untuk loader animasi */
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border-left-color: #3498db;
            animation: spin 1s linear infinite;
            margin: auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</body>
</html>
