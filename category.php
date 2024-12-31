<?php include 'layout/header.php'; ?>
<?php include 'database/login.php'; ?>
<?php
function seo_friendly_url($string) {
    $find = array('Ç', 'Ş', 'Ğ', 'Ü', 'İ', 'Ö', 'ç', 'ş', 'ğ', 'ü', 'ö', 'ı', '+', '#');
    $replace = array('c', 's', 'g', 'u', 'i', 'o', 'c', 's', 'g', 'u', 'o', 'i', 'plus', 'sharp');
    $string = strtolower(str_replace($find, $replace, $string));
    $string = preg_replace("@[^A-Za-z0-9\-_\.\+]@i", ' ', $string);
    $string = trim(preg_replace('/\s+/', ' ', $string));
    $string = str_replace(' ', '-', $string);
    return $string;
}
?>
<div class="space-y-8">
<?php
if (isset($_GET['category_name'])) {
    $category = $_GET['category_name'];
    $categoryQuery = $conn->prepare("SELECT * FROM category where slug = :category_name");
    $categoryQuery->execute(['category_name' => $category]);
    $category = $categoryQuery->fetch(PDO::FETCH_ASSOC);
    
    if ($category) {
        $filmsQuery = $conn->prepare("SELECT * FROM movie_and_series WHERE category_id = :category_id");
        $filmsQuery->execute(['category_id' => $category['id']]);
        $films = $filmsQuery->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="container mx-auto px-4 py-8">
            <h2 class="text-3xl font-bold text-white mb-8"><?php echo $category['name']; ?></h2>
            
            <div class="swiper mySwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($films as $film): 
                        $watchlist = false;
                        if (isset($_SESSION['user'])) {
                            $watchlistQuery = $conn->prepare("SELECT * FROM watchlist WHERE user_id = :user_id AND movie_and_series_id = :movie_id");
                            $watchlistQuery->execute([
                                'user_id' => $_SESSION['user']['id'],
                                'movie_id' => $film['id']
                            ]);
                            $watchlist = $watchlistQuery->fetch(PDO::FETCH_ASSOC);
                        }
                    ?>
                        <div class="swiper-slide p-2">
                            <div class="bg-gray-800/80 backdrop-blur-sm rounded-lg overflow-hidden shadow-lg 
                                        border border-gray-700/50 group h-[400px] relative">
                                <div class="relative h-[300px] overflow-hidden">
                                    <img src="<?php echo $film['poster']; ?>" 
                                         alt="<?php echo $film['name']; ?>" 
                                         class="w-full h-full object-cover cursor-pointer"
                                         onclick="window.location.href='/movie/<?php echo seo_friendly_url($film['name']) . '-' . $film['id']; ?>'">
                                </div>
        
                                <div class="p-3">
                                    <h3 class="font-semibold text-white text-sm truncate mb-1"><?php echo $film['name']; ?></h3>
                                    <div class="flex items-center justify-between text-xs">
                                        <span class="text-gray-400"><?php echo $film['release_date']; ?></span>
                                        <div class="flex items-center text-yellow-500">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.953a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.458a1 1 0 00-.364 1.118l1.287 3.953c.3.921-.755 1.688-1.54 1.118l-3.38-2.458a1 1 0 00-1.175 0l-3.38 2.458c-.784.57-1.838-.197-1.54-1.118l1.287-3.953a1 1 0 00-.364-1.118L2.41 9.38c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69L9.049 2.927z"/>
                                            </svg>
                                            <span class="ml-1"><?php echo $film['rating']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        
        <script>
            var swiper = new Swiper(".mySwiper", {
                slidesPerView: 1,
                spaceBetween: 10,
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                breakpoints: {
                    640: {
                        slidesPerView: 2,
                        spaceBetween: 20,
                    },
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                    1024: {
                        slidesPerView: 5,
                        spaceBetween: 20,
                    },
                },
            });
        </script>
    <?php }
    else {
        echo '<div class="min-h-screen bg-gray-900 flex items-center justify-center">';
        echo '<h1 class="text-3xl font-bold text-white">Film bulunamadı</h1>';
        echo '</div>';
    }
}
?>
</div>
<?php include 'layout/footer.php'; ?>