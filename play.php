<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Movie</title>
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
            background-color: #000;
            color: #ecf0f1;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* Gaya untuk container fullscreen */
        .fullscreen-container {
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #000;
            position: relative;
            padding: 10px; /* Add padding for small screens */
            box-sizing: border-box;
        }

        /* Gaya untuk video */
        video {
            width: 100%;
            height: 100%;
            object-fit: contain; /* Fit video within container */
            border: 1px solid #ecf0f1; /* Optional: Border for better visibility */
        }

        /* Gaya untuk tombol kembali */
        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            text-decoration: none;
            color: #ecf0f1;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
            font-size: 14px; /* Adjust font size for smaller screens */
        }

        .back-button:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        @media (max-width: 600px) {
            /* Gaya khusus untuk perangkat dengan lebar maksimum 600px (ponsel) */
            .back-button {
                font-size: 12px;
                padding: 8px 16px;
                top: 10px;
                left: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="fullscreen-container">
        <?php
        if (isset($_GET['movie'])) {
            $movie = urldecode($_GET['movie']);
            $filePath = $movie;

            if (file_exists($filePath)) {
                echo "<video controls autoplay onplay=\"markAsWatched('$movie')\">
                        <source src='$filePath' type='video/mp4'>
                        Your browser does not support the video tag.
                      </video>";
            } else {
                echo "<p>Movie not found.</p>";
            }
        } else {
            echo "<p>No movie selected.</p>";
        }
        ?>
    </div>
    <a href="index.php?category=<?php echo urlencode(dirname($movie)); ?>" class="back-button">Back to Category</a>

    <script>
        function markAsWatched(movie) {
            localStorage.setItem(movie, 'watched');
        }
    </script>
</body>
</html>
