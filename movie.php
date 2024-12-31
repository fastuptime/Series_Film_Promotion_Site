<?php include 'layout/header.php'; ?>
<?php include 'database/login.php'; ?>
<div class="space-y-8">
<?php
function seo_friendly_url($string) {
    $turkish = array('ş', 'Ş', 'ı', 'İ', 'ç', 'Ç', 'ü', 'Ü', 'ö', 'Ö', 'ğ', 'Ğ');
    $english = array('s', 'S', 'i', 'I', 'c', 'C', 'u', 'U', 'o', 'O', 'g', 'G');
    $string = str_replace($turkish, $english, $string);
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); 
    $string = strtolower($string);
    $string = str_replace(' ', '-', $string); 
    $string = preg_replace('/-+/', '-', $string);
    return $string;
}

if (isset($_GET['movie_name'])) {
    $movieName = $_GET['movie_name'];
    $movieName = explode('-', $movieName);
    $movieName = end($movieName);
    $movieQuery = $conn->prepare("SELECT * FROM movie_and_series WHERE id = :movie_name");
    $movieQuery->execute(['movie_name' => $movieName]);
    $movie = $movieQuery->fetch(PDO::FETCH_ASSOC);

    
    if ($movie) {
        $categoryQuery = $conn->prepare("SELECT * FROM category WHERE id = :category_id");
        $categoryQuery->execute(['category_id' => $movie['category_id']]);
        $category = $categoryQuery->fetch(PDO::FETCH_ASSOC);

        $updateViewsQuery = $conn->prepare("UPDATE movie_and_series SET views = views + 1 WHERE id = :id");
        $updateViewsQuery->execute(['id' => $movie['id']]);

        $videoId = '';
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $movie['trailer'], $matches)) {
            $videoId = $matches[1];
        }

        echo '<div class="min-h-screen bg-gray-900 text-white py-8">';
        echo '<button onclick="window.history.back()" class="bg-gray-800 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition"><i class="fas fa-arrow-left"></i> Geri Dön</button>';
        echo '<div class="container mx-auto px-4 mt-8">';
        
        echo '<div class="bg-gray-800 rounded-xl overflow-hidden shadow-2xl">';
        echo '<div class="relative h-96 overflow-hidden">';
        echo '<img src="' . $movie['backdrop'] . '" class="w-full h-full object-cover opacity-50 absolute inset-0">';
        echo '<div class="absolute inset-0 bg-gradient-to-t from-gray-800"></div>';
        echo '</div>';

        echo '<div class="flex flex-col md:flex-row gap-8 p-8 -mt-32 relative">';
        echo '<div class="md:w-1/4">';
        echo '<img src="' . $movie['poster'] . '" class="w-full rounded-lg shadow-2xl border border-gray-800">';
        echo '</div>';

        echo '<div class="md:w-3/4">';
        echo '<h1 class="text-4xl font-bold mb-2">' . $movie['name'] . '</h1>';
        echo '<p class="text-gray-400 mb-4">' . $movie['original_name'] . '</p>';
        echo '<div class="flex items-center gap-4 mb-6">';
        echo '<a href="/category/' . $category['slug'] . '" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-full font-bold"># ' . $category['name'] . '</a>';
        echo '<span class="bg-yellow-500 text-black px-3 py-1 rounded-full font-bold">' . $movie['rating'] . '</span>';
        echo '<span class="text-gray-400">' . $movie['duration'] . ' dk</span>';
        echo '<span class="text-gray-400">' . $movie['release_date'] . '</span>';
        echo '<span class="text-gray-400">' . number_format($movie['views']) . ' görüntülenme</span>';
        echo '</div>';
        echo '<p class="text-gray-300 leading-relaxed mb-6">' . $movie['description'] . '</p>';
        echo '</div>';
        echo '</div>';

        if ($videoId) {
            echo '<div class="mt-8 aspect-w-16 aspect-h-9 rounded-xl overflow-hidden">';
            echo '<iframe src="https://www.youtube.com/embed/' . $videoId . '" class="w-full h-[600px] rounded-xl" allowfullscreen></iframe>';
            echo '</div>';
        }

        $commentQuery = $conn->prepare("SELECT c.*, u.username, u.avatar FROM comment c JOIN user u ON c.user_id = u.id WHERE c.movie_and_series_id = :movie_id AND c.status = 'active' ORDER BY c.created_at DESC");
        $commentQuery->execute(['movie_id' => $movie['id']]);
        $comments = $commentQuery->fetchAll(PDO::FETCH_ASSOC);

        echo '<div class="mt-12 bg-gray-800 rounded-xl p-6">';
        echo '<h2 class="text-2xl font-bold mb-6">Yorumlar</h2>';

        if (isset($_SESSION['user'])) {
            echo '<form id="comment" class="mb-8">';
            echo '<textarea name="content" class="w-full bg-gray-700 text-white rounded-lg p-4 mb-2" placeholder="Yorumunuzu yazın..." required></textarea>';
            echo '<input type="hidden" name="movie_and_series_id" value="' . $movie['id'] . '">';
            echo '<button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition">Yorum Yap <i class="fas fa-paper-plane"></i></button>';
            echo '</form>';

            echo '<script>';
            echo 'document.getElementById("comment").addEventListener("submit", function(e) {';
            echo 'e.preventDefault();';
            echo 'var formData = new FormData(e.target);';
            echo 'fetch("/database/comment.php", {';
            echo 'method: "POST",';
            echo 'body: formData';
            echo '})';
            echo '.then(response => response.json())';
            echo '.then(data => {';
            echo 'if (data.success) {';
            echo 'window.location.reload();';
            echo '} else {';
            echo 'alert(data.message);';
            echo '}';
            echo '});';
            echo '});';
            echo '</script>';
        }

        if (empty($comments)) {
            echo '<p class="text-gray-300">Henüz yorum yapılmamış.</p>';
        }


        foreach ($comments as $comment) {
            echo '<div class="flex gap-4 mb-6 bg-gray-700 p-4 rounded-lg">';
            echo '<img src="https://avataaars.io/?avatarStyle=Circle&topType=LongHairStraight&accessoriesType=Blank&hairColor=BrownDark&facialHairType=Blank&clotheType=BlazerShirt&eyeType=Default&eyebrowType=Default&mouthType=Default&skinColor=Light" class="w-12 h-12 rounded-full">';
            echo '<div>';
            echo '<div class="flex items-center gap-2 mb-1">';
            echo '<span class="font-semibold">' . $comment['username'] . '</span>';
            echo '<span class="text-gray-400 text-sm">' . date('d.m.Y H:i', strtotime($comment['created_at'])) . '</span>';
            echo '</div>';
            echo '<p class="text-gray-300">' . $comment['content'] . '</p>';
            echo '</div>';
            echo '</div>';
        }

        if (isset($_SESSION['user'])) {
            $pendingCommentQuery = $conn->prepare("SELECT * FROM comment WHERE user_id = :user_id AND movie_and_series_id = :movie_id AND status = 'pending'");
            $pendingCommentQuery->execute(['user_id' => $_SESSION['user']['id'], 'movie_id' => $movie['id']]);
            $pendingComment = $pendingCommentQuery->fetch(PDO::FETCH_ASSOC);

            if ($pendingComment) {
                echo '<div class="bg-gray-700 p-4 rounded-lg mb-8">';
                echo '<p class="text-gray-300">Yorumunuz onay bekliyor.</p>';
                echo '</div>';
            }
        }
        echo '</div>';
        
        echo '</div>';

        $relatedQuery = $conn->prepare("SELECT * FROM movie_and_series WHERE category_id = :category_id AND id != :movie_id ORDER BY RAND() LIMIT 10");
        $relatedQuery->execute(['category_id' => $movie['category_id'], 'movie_id' => $movie['id']]);
        $relatedMovies = $relatedQuery->fetchAll(PDO::FETCH_ASSOC);

        if ($relatedMovies) {
            echo '<div class="container mx-auto px-4 mt-12">';
            echo '<h2 class="text-2xl font-bold mb-6">Benzer Filmler</h2>';
            echo '<div class="grid grid-cols-2 md:grid-cols-5 gap-8">';
            foreach ($relatedMovies as $relatedMovie) {
                echo '<a href="/movie/' . seo_friendly_url($relatedMovie['name']) . '-' . $relatedMovie['id'] . '" class="group">';
                echo '<div class="relative">';
                echo '<img src="' . $relatedMovie['poster'] . '" class="w-full rounded-lg shadow-2xl">';
                echo '<div class="absolute inset-0 bg-gradient-to-t from-gray-800 opacity-0 group-hover:opacity-100 transition"></div>';
                echo '<div class="absolute bottom-0 left-0 right-0 p-4">';
                echo '<h3 class="text-lg font-bold">' . $relatedMovie['name'] . '</h3>';
                echo '<p class="text-gray-300">' . $relatedMovie['rating'] . '</p>';
                echo '</div>';
                echo '</div>';
                echo '</a>';
            }
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';

    } else {
        echo '<div class="min-h-screen bg-gray-900 flex items-center justify-center">';
        echo '<h1 class="text-3xl font-bold text-white">Film bulunamadı</h1>';
        echo '</div>';
    }
}
?>
</div>
<?php include 'layout/footer.php'; ?>