<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie List</title>
    <style>
        /* Reset dasar untuk margin, padding, dan box-sizing */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Gaya umum untuk body */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #2c3e50;
            color: #ecf0f1;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
        }

        /* Gaya untuk container utama */
        .container {
            width: 100%;
            max-width: 800px;
            background-color: #34495e;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Gaya untuk judul */
        h1 {
            font-size: 2em;
            margin-bottom: 20px;
            color: #e74c3c;
            text-align: center;
        }

        /* Gaya untuk form pencarian */
        .search-form {
            display: flex;
            margin-bottom: 20px;
            gap: 10px; /* Memberikan jarak antara tombol */
        }

        .search-input {
            flex-grow: 1;
            padding: 10px;
            border: none;
            border-radius: 5px 0 0 5px;
            font-size: 1em;
        }

        .search-button, .reset-button, .upload-button {
            padding: 10px 20px;
            background-color: #1abc9c;
            color: #2c3e50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        .search-button:hover, .reset-button:hover, .upload-button:hover {
            background-color: #16a085;
        }

        /* Gaya untuk daftar */
        ul {
            list-style-type: none;
            padding: 0;
            margin-bottom: 20px;
            border: 1px solid #1abc9c;
            border-radius: 8px;
        }

        li {
            margin: 10px;
            padding: 10px;
            background-color: #1abc9c;
            border-radius: 5px;
            transition: background-color 0.3s, transform 0.2s;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        li:hover {
            background-color: #16a085;
            transform: scale(1.02);
        }

        a {
            text-decoration: none;
            color: #2c3e50;
            font-weight: bold;
            display: block;
        }

        /* Gaya untuk tombol kembali */
        .back-button {
            margin-bottom: 20px;
            display: inline-block;
            text-decoration: none;
            color: #ecf0f1;
            background-color: #e74c3c;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #c0392b;
        }

        /* Gaya untuk ikon tonton status */
        .status-icon {
            margin-left: 10px;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php
        $baseDir = 'videos/';

        if (isset($_GET['category'])) {
            // Menampilkan daftar film dalam kategori
            $category = basename($_GET['category']);
            $dir = $baseDir . $category;

            echo "<h1>Movies in $category</h1>";
            echo "<a href='index.php' class='back-button'>Back to Categories</a>";
            
            // Form pencarian film dalam kategori
            echo "<form class='search-form' method='GET'>
                    <input type='hidden' name='category' value='$category'>
                    <input type='text' name='search' class='search-input' placeholder='Search movies...'>
                    <button type='submit' class='search-button'>Search</button>
                    <a href='index.php?category=$category' class='reset-button'>Reset</a>
                  </form>";

            echo "<ul>";

            $videos = array_diff(scandir($dir), array('.', '..'));

            // Filter hasil berdasarkan pencarian film
            if (isset($_GET['search'])) {
                $searchQuery = strtolower($_GET['search']);
                $videos = array_filter($videos, function($video) use ($searchQuery) {
                    return strpos(strtolower($video), $searchQuery) !== false;
                });
            }

            if (count($videos) == 0) {
                echo "<li>No movies found.</li>";
            } else {
                foreach ($videos as $video) {
                    $videoName = basename($video, ".mp4");
                    $videoPath = urlencode("$dir/$video");
                    echo "<li>
                            <a href='play.php?movie=$videoPath'>$videoName</a>
                            <span class='status-icon' id='$videoPath'></span>
                          </li>";
                }
            }

            echo "</ul>";
        } else {
            // Menampilkan daftar kategori
            echo "<h1>Movie Categories</h1>";

            // Form pencarian kategori
            echo "<form class='search-form' method='GET'>
                    <input type='text' name='search' class='search-input' placeholder='Search categories...'>
                    <button type='submit' class='search-button'>Search</button>
                    <a href='index.php' class='reset-button'>Reset</a>
                  </form>";

            echo "<ul>";

            $categories = array_filter(glob($baseDir . '*'), 'is_dir');

            // Filter kategori berdasarkan pencarian
            if (isset($_GET['search'])) {
                $searchQuery = strtolower($_GET['search']);
                $categories = array_filter($categories, function($category) use ($searchQuery) {
                    return strpos(strtolower(basename($category)), $searchQuery) !== false;
                });
            }

            if (count($categories) == 0) {
                echo "<li>No categories found.</li>";
            } else {
                foreach ($categories as $category) {
                    $categoryName = basename($category);
                    echo "<li><a href='index.php?category=$categoryName'>$categoryName</a></li>";
                }
            }

            echo "</ul>";
        }

        // Tombol Upload
        echo "<a href='upload.php' class='upload-button' target='_blank'>Upload Movie</a>";
        ?>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cek status tonton untuk setiap video
            const movieLinks = document.querySelectorAll('.status-icon');
            movieLinks.forEach(icon => {
                const movie = icon.id;
                if (localStorage.getItem(movie) === 'watched') {
                    icon.textContent = '✔️'; // Tanda video sudah ditonton
                    icon.classList.add('watched-icon');
                } else {
                    icon.textContent = '⏳'; // Tanda video belum ditonton
                    icon.classList.add('unwatched-icon');
                }
            });
        });
    </script>
</body>
</html>
