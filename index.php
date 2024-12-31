<?php include 'layout/header.php'; ?>
<?php include 'database/login.php'; ?>
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

?>
        <div class="container mx-auto px-4 py-8">
            <div class="max-w-6xl mx-auto mb-12">
                <h2 class="text-2xl font-bold text-white mb-8 text-center">Popüler Kategoriler</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                     <?php 
                        $query = $conn->query("
                            SELECT DISTINCT c.name, c.id 
                            FROM category c 
                            JOIN movie_and_series ms ON c.id = ms.category_id 
                            WHERE ms.type = 'movie' 
                            ORDER BY ms.views DESC 
                            LIMIT 5
                        ");
                        $categories = $query->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($categories as $category) {
                    ?>
                    <a class="bg-gray-800/50 backdrop-blur-sm p-4 rounded-xl border border-gray-700/50 
                                      hover:bg-gray-700/50 transition-all duration-300 
                                      hover:bg-red-500 hover:border-red-500 hover:scale-105
                                      transform group"
                                      onclick="document.getElementById('search').value = '<?php echo $category['name']; ?>'; document.getElementById('search').focus();"
                        <span class="text-red-500 text-lg group-hover:text-red-400">#</span>
                        <span class="text-gray-200 group-hover:text-white"><?php echo $category['name']; ?></span>
                    </a>
                    <?php } ?>
                </div>
            </div>
            <div class="max-w-xl mx-auto mb-12">
                <div class="relative mb-8">
                    <input 
                        type="text" 
                        placeholder="Film, dizi veya anime ara..." 
                        id="search"
                        class="w-full px-6 py-4 bg-gray-800/50 backdrop-blur-sm border border-gray-700/50 rounded-2xl 
                               focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent
                               text-gray-200 placeholder-gray-400
                               transition-all duration-300 shadow-lg"
                    >
                    <button class="absolute right-4 top-1/2 -translate-y-1/2 bg-gradient-to-r from-red-500 to-red-600 
                                   text-white p-3 rounded-xl hover:from-red-600 hover:to-red-700 
                                   transition-all duration-300 shadow-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                
                    <div id="searchResults" class="absolute w-full max-h-[400px] overflow-y-auto mt-2 bg-gray-800/90 
                                                  backdrop-blur-sm border border-gray-700/50 rounded-xl shadow-lg hidden z-50">
                        <div class="p-4" id="searchResultsContent"></div>
                    </div>
                </div>
            </div>
        
        </div>

        <script>

            function seo_friendly_url(string) {
                const turkish = ['ş', 'Ş', 'ı', 'İ', 'ç', 'Ç', 'ü', 'Ü', 'ö', 'Ö', 'ğ', 'Ğ'];
                const english = ['s', 'S', 'i', 'I', 'c', 'C', 'u', 'U', 'o', 'O', 'g', 'G'];
                string = string.split('');
                for (let i = 0; i < string.length; i++) {
                    const index = turkish.indexOf(string[i]);
                    if (index > -1) {
                        string[i] = english[index];
                    }
                }
                string = string.join('');
                string = string.replace(/[^A-Za-z0-9\-]/g, '');
                string = string.toLowerCase();
                string = string.replace(/ /g, '-');
                string = string.replace(/-+/g, '-');
                return string;
            }

            document.getElementById('search').addEventListener('input', function() {
                const search = this.value;
                if (search.length > 0) {
                    fetch(`/search.php?search=${search}`)
                        .then(response => response.json())
                        .then(data => {
                            const searchResultsContent = document.getElementById('searchResultsContent');
                            searchResultsContent.innerHTML = '';
                            data.forEach(result => {
                                searchResultsContent.innerHTML += `
                                    <div class="flex items-center gap-4 p-2 hover:bg-gray-700 rounded-lg cursor-pointer" onclick="window.location.href = '/movie/${seo_friendly_url(result.name)}-${result.id}'">
                                        <img src="${result.poster}" alt="${result.name} Posteri" class="w-16 rounded-lg">
                                        <div>
                                            <h3 class="font-bold">${result.name}</h3>
                                            <p class="text-gray-400">${result.release_date}</p>
                                        </div>
                                    </div>
                                `;
                            });
                            document.getElementById('searchResults').classList.remove('hidden');
                        });
                } else {
                    document.getElementById('searchResults').classList.add('hidden');
                }
            });

            document.addEventListener('click', function(e) {
                if (!document.getElementById('search').contains(e.target)) {
                    document.getElementById('searchResults').classList.add('hidden');
                }
            });

            document.getElementById('search').addEventListener('focus', function() {
                if (this.value.length > 0) {
                    document.getElementById('searchResults').classList.remove('hidden');
                }
            });
        </script>

        <div class="space-y-8">
            <section>
                <h2 class="text-2xl font-semibold mb-4 ms-2" style="font-family: 'Montserrat', sans-serif; font:italic;">Trend İçerikler</h2>
                <div class="swiper trend-swiper">
                    <div class="swiper-wrapper">
                        <?php 
                            $query = $conn->query("SELECT * FROM movie_and_series ORDER BY views DESC LIMIT 10");
                            $contents = $query->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($contents as $content) {
                                $categoryQuery = $conn->prepare("SELECT * FROM category WHERE id = :id");
                                $categoryQuery->execute(['id' => $content['category_id']]);
                                $category = $categoryQuery->fetch(PDO::FETCH_ASSOC);

                        ?>
                        <div class="swiper-slide">
                            <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg relative group">
                                <img src="<?php echo $content['poster']; ?>" alt="<?php echo $content['name']; ?> Posteri" class="w-full  cursor-pointer" style="height: 27rem;" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                                    <?php if (isset($_SESSION['user'])) { ?>
                                        <?php 
                                            $watchlistQuery = $conn->prepare("SELECT * FROM watchlist WHERE user_id = :user_id AND movie_and_series_id = :movie_and_series_id");
                                            $watchlistQuery->execute(['user_id' => $_SESSION['user']['id'], 'movie_and_series_id' => $content['id']]);
                                            $watchlist = $watchlistQuery->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <?php if ($watchlist) { ?>
                                            <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700" onclick="removeFromWatchlist(<?php echo $content['id']; ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Edit / Remove_Minus"> <path id="Vector" d="M6 12H18" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g>
                                            </svg>
                                            </button>
                                        <?php } else { ?>
                                            <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div class="p-3" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                    <h3 class="font-bold text-sm truncate"><?php echo $content['name']; ?></h3>
                                    <div class="flex justify-between items-center">
                                        <p class="text-xs text-gray-400"><?php echo $category['name']; ?> | <?php echo $content['release_date']; ?></p>
                                        <div class="flex text-yellow-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.953a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.458a1 1 0 00-.364 1.118l1.287 3.953c.3.921-.755 1.688-1.54 1.118l-3.38-2.458a1 1 0 00-1.175 0l-3.38 2.458c-.784.57-1.838-.197-1.54-1.118l1.287-3.953a1 1 0 00-.364-1.118L2.41 9.38c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69L9.049 2.927z" />
                                            </svg>
                                            <span class="text-xs ml-1"><?php echo $content['rating']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <!-- Navigation buttons -->
                    <div class="swiper-button-next text-red-500"></div>
                    <div class="swiper-button-prev text-red-500"></div>
                </div>
            </section>
            <?php if (isset($_SESSION['user'])) { ?>
                <section>
                    <h2 class="text-2xl font-semibold mb-4 ms-2" style="font-family: 'Montserrat', sans-serif; font:italic;">İzleme Listesi</h2>
                    <div class="swiper trend-swiper">
                        <div class="swiper-wrapper">
                            <?php 
                                $query = $conn->prepare("SELECT * FROM watchlist WHERE user_id = :user_id AND status = 'watching'");
                                $query->execute(['user_id' => $_SESSION['user']['id']]);
                                $watchlist = $query->fetchAll(PDO::FETCH_ASSOC);
                                foreach ($watchlist as $content) {
                                    $contentQuery = $conn->prepare("SELECT * FROM movie_and_series WHERE id = :id");
                                    $contentQuery->execute(['id' => $content['movie_and_series_id']]);
                                    $content = $contentQuery->fetch(PDO::FETCH_ASSOC);

                                    $categoryQuery = $conn->prepare("SELECT * FROM category WHERE id = :id");
                                    $categoryQuery->execute(['id' => $content['category_id']]);
                                    $category = $categoryQuery->fetch(PDO::FETCH_ASSOC);
                            ?>
                            <div class="swiper-slide">
                                <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg relative group">
                                    <img src="<?php echo $content['poster']; ?>" alt="<?php echo $content['name']; ?> Posteri" class="w-full  cursor-pointer" style="height: 27rem;" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                    <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                                        <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700" onclick="removeFromWatchlist(<?php echo $content['id']; ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Edit / Remove_Minus"> <path id="Vector" d="M6 12H18" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="p-3" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                        <h3 class="font-bold text-sm truncate"><?php echo $content['name']; ?></h3>
                                        <div class="flex justify-between items-center">
                                            <p class="text-xs text-gray-400"><?php echo $category['name']; ?> | <?php echo $content['release_date']; ?></p>
                                            <div class="flex text-yellow-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.953a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.458a1 1 0 00-.364 1.118l1.287 3.953c.3.921-.755 1.688-1.54 1.118l-3.38-2.458a1 1 0 00-1.175 0l-3.38 2.458c-.784.57-1.838-.197-1.54-1.118l1.287-3.953a1 1 0 00-.364-1.118L2.41 9.38c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69L9.049 2.927z" />
                                                </svg>
                                                <span class="text-xs ml-1"><?php echo $content['rating']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="swiper-button-next text-red-500"></div>
                        <div class="swiper-button-prev text-red-500"></div>
                    </div>
                </section>
            <?php } ?>
            <section>
                <h2 class="text-2xl font-semibold mb-4 ms-2" style="font-family: 'Montserrat', sans-serif; font:italic;">En Çok İzlenenler</h2>
                <div class="swiper trend-swiper">
                    <div class="swiper-wrapper">
                        <?php 
                            $query = $conn->query("SELECT * FROM movie_and_series WHERE type = 'movie' ORDER BY views DESC LIMIT 10");
                            $contents = $query->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($contents as $content) {
                                $categoryQuery = $conn->prepare("SELECT * FROM category WHERE id = :id");
                                $categoryQuery->execute(['id' => $content['category_id']]);
                                $category = $categoryQuery->fetch(PDO::FETCH_ASSOC);

                        ?>
                        <div class="swiper-slide">
                            <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg relative group">
                                <img src="<?php echo $content['poster']; ?>" alt="<?php echo $content['name']; ?> Posteri" class="w-full  cursor-pointer" style="height: 27rem;" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                                    <?php if (isset($_SESSION['user'])) { ?>
                                        <?php 
                                            $watchlistQuery = $conn->prepare("SELECT * FROM watchlist WHERE user_id = :user_id AND movie_and_series_id = :movie_and_series_id");
                                            $watchlistQuery->execute(['user_id' => $_SESSION['user']['id'], 'movie_and_series_id' => $content['id']]);
                                            $watchlist = $watchlistQuery->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <?php if ($watchlist) { ?>
                                            <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700" onclick="removeFromWatchlist(<?php echo $content['id']; ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Edit / Remove_Minus"> <path id="Vector" d="M6 12H18" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g>
                                            </svg>
                                            </button>
                                        <?php } else { ?>
                                            <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700" onclick="addToWatchlist(<?php echo $content['id']; ?>)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div class="p-3" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                    <h3 class="font-bold text-sm truncate"><?php echo $content['name']; ?></h3>
                                    <div class="flex justify-between items-center">
                                        <p class="text-xs text-gray-400"><?php echo $category['name']; ?> | <?php echo $content['release_date']; ?></p>
                                        <div class="flex text-yellow-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.953a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.458a1 1 0 00-.364 1.118l1.287 3.953c.3.921-.755 1.688-1.54 1.118l-3.38-2.458a1 1 0 00-1.175 0l-3.38 2.458c-.784.57-1.838-.197-1.54-1.118l1.287-3.953a1 1 0 00-.364-1.118L2.41 9.38c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69L9.049 2.927z" />
                                            </svg>
                                            <span class="text-xs ml-1"><?php echo $content['rating']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <!-- Navigation buttons -->
                    <div class="swiper-button-next text-red-500"></div>
                    <div class="swiper-button-prev text-red-500"></div>
                </div>
            </section>
            <section>
                <h2 class="text-2xl font-semibold mb-4 ms-2" style="font-family: 'Montserrat', sans-serif; font:italic;">Uzun Metraj Filmler</h2>
                <div class="swiper trend-swiper">
                    <div class="swiper-wrapper">
                        <?php 
                            $query = $conn->query("SELECT * FROM movie_and_series WHERE type = 'movie' ORDER BY duration DESC LIMIT 10");
                            $contents = $query->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($contents as $content) {
                                $categoryQuery = $conn->prepare("SELECT * FROM category WHERE id = :id");
                                $categoryQuery->execute(['id' => $content['category_id']]);
                                $category = $categoryQuery->fetch(PDO::FETCH_ASSOC);

                        ?>
                        <div class="swiper-slide">
                            <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg relative group">
                                <img src="<?php echo $content['poster']; ?>" alt="<?php echo $content['name']; ?> Posteri" class="w-full  cursor-pointer" style="height: 27rem;" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                                    <?php if (isset($_SESSION['user'])) { ?>
                                        <?php 
                                            $watchlistQuery = $conn->prepare("SELECT * FROM watchlist WHERE user_id = :user_id AND movie_and_series_id = :movie_and_series_id");
                                            $watchlistQuery->execute(['user_id' => $_SESSION['user']['id'], 'movie_and_series_id' => $content['id']]);
                                            $watchlist = $watchlistQuery->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <?php if ($watchlist) { ?>
                                            <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700" onclick="removeFromWatchlist(<?php echo $content['id']; ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Edit / Remove_Minus"> <path id="Vector" d="M6 12H18" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g>
                                            </svg>
                                            </button>
                                        <?php } else { ?>
                                            <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700" onclick="addToWatchlist(<?php echo $content['id']; ?>)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div class="p-3" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                    <h3 class="font-bold text-sm truncate"><?php echo $content['name']; ?></h3>
                                    <div class="flex justify-between items-center">
                                        <p class="text-xs text-gray-400"><?php echo $category['name']; ?> | <?php echo $content['release_date']; ?></p>
                                        <div class="flex text-yellow-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.953a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.458a1 1 0 00-.364 1.118l1.287 3.953c.3.921-.755 1.688-1.54 1.118l-3.38-2.458a1 1 0 00-1.175 0l-3.38 2.458c-.784.57-1.838-.197-1.54-1.118l1.287-3.953a1 1 0 00-.364-1.118L2.41 9.38c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69L9.049 2.927z" />
                                            </svg>
                                            <span class="text-xs ml-1"><?php echo $content['rating']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <!-- Navigation buttons -->
                    <div class="swiper-button-next text-red-500"></div>
                    <div class="swiper-button-prev text-red-500"></div>
                </div>
            </section>
            <section>
                <h2 class="text-2xl font-semibold mb-4 ms-2" style="font-family: 'Montserrat', sans-serif; font:italic;">Kısa Metraj Filmler</h2>
                <div class="swiper trend-swiper">
                    <div class="swiper-wrapper">
                        <?php 
                            $query = $conn->query("SELECT * FROM movie_and_series WHERE type = 'movie' ORDER BY duration ASC LIMIT 10");
                            $contents = $query->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($contents as $content) {
                                $categoryQuery = $conn->prepare("SELECT * FROM category WHERE id = :id");
                                $categoryQuery->execute(['id' => $content['category_id']]);
                                $category = $categoryQuery->fetch(PDO::FETCH_ASSOC);

                        ?>
                        <div class="swiper-slide">
                            <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg relative group">
                                <img src="<?php echo $content['poster']; ?>" alt="<?php echo $content['name']; ?> Posteri" class="w-full  cursor-pointer" style="height: 27rem;" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                                    <?php if (isset($_SESSION['user'])) { ?>
                                        <?php 
                                            $watchlistQuery = $conn->prepare("SELECT * FROM watchlist WHERE user_id = :user_id AND movie_and_series_id = :movie_and_series_id");
                                            $watchlistQuery->execute(['user_id' => $_SESSION['user']['id'], 'movie_and_series_id' => $content['id']]);
                                            $watchlist = $watchlistQuery->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <?php if ($watchlist) { ?>
                                            <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700" onclick="removeFromWatchlist(<?php echo $content['id']; ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Edit / Remove_Minus"> <path id="Vector" d="M6 12H18" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g>
                                            </svg>
                                            </button>
                                        <?php } else { ?>
                                            <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700" onclick="addToWatchlist(<?php echo $content['id']; ?>)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div class="p-3" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                    <h3 class="font-bold text-sm truncate"><?php echo $content['name']; ?></h3>
                                    <div class="flex justify-between items-center">
                                        <p class="text-xs text-gray-400"><?php echo $category['name']; ?> | <?php echo $content['release_date']; ?></p>
                                        <div class="flex text-yellow-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.953a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.458a1 1 0 00-.364 1.118l1.287 3.953c.3.921-.755 1.688-1.54 1.118l-3.38-2.458a1 1 0 00-1.175 0l-3.38 2.458c-.784.57-1.838-.197-1.54-1.118l1.287-3.953a1 1 0 00-.364-1.118L2.41 9.38c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69L9.049 2.927z" />
                                            </svg>
                                            <span class="text-xs ml-1"><?php echo $content['rating']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <!-- Navigation buttons -->
                    <div class="swiper-button-next text-red-500"></div>
                    <div class="swiper-button-prev text-red-500"></div>
                </div>
            </section>
            <section>
                <h2 class="text-2xl font-semibold mb-4 ms-2" style="font-family: 'Montserrat', sans-serif; font:italic;">Eski Filmler</h2>
                <div class="swiper trend-swiper">
                    <div class="swiper-wrapper">
                        <?php 
                            $query = $conn->query("SELECT * FROM movie_and_series WHERE type = 'movie' ORDER BY release_date ASC LIMIT 10");
                            $contents = $query->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($contents as $content) {
                                $categoryQuery = $conn->prepare("SELECT * FROM category WHERE id = :id");
                                $categoryQuery->execute(['id' => $content['category_id']]);
                                $category = $categoryQuery->fetch(PDO::FETCH_ASSOC);

                        ?>
                        <div class="swiper-slide">
                            <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg relative group">
                                <img src="<?php echo $content['poster']; ?>" alt="<?php echo $content['name']; ?> Posteri" class="w-full  cursor-pointer" style="height: 27rem;" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                                    <?php if (isset($_SESSION['user'])) { ?>
                                        <?php 
                                            $watchlistQuery = $conn->prepare("SELECT * FROM watchlist WHERE user_id = :user_id AND movie_and_series_id = :movie_and_series_id");
                                            $watchlistQuery->execute(['user_id' => $_SESSION['user']['id'], 'movie_and_series_id' => $content['id']]);
                                            $watchlist = $watchlistQuery->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <?php if ($watchlist) { ?>
                                            <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700" onclick="removeFromWatchlist(<?php echo $content['id']; ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Edit / Remove_Minus"> <path id="Vector" d="M6 12H18" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g>
                                            </svg>
                                            </button>
                                        <?php } else { ?>
                                            <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700" onclick="addToWatchlist(<?php echo $content['id']; ?>)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div class="p-3" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                    <h3 class="font-bold text-sm truncate"><?php echo $content['name']; ?></h3>
                                    <div class="flex justify-between items-center">
                                        <p class="text-xs text-gray-400"><?php echo $category['name']; ?> | <?php echo $content['release_date']; ?></p>
                                        <div class="flex text-yellow-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.953a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.458a1 1 0 00-.364 1.118l1.287 3.953c.3.921-.755 1.688-1.54 1.118l-3.38-2.458a1 1 0 00-1.175 0l-3.38 2.458c-.784.57-1.838-.197-1.54-1.118l1.287-3.953a1 1 0 00-.364-1.118L2.41 9.38c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69L9.049 2.927z" />
                                            </svg>
                                            <span class="text-xs ml-1"><?php echo $content['rating']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <!-- Navigation buttons -->
                    <div class="swiper-button-next text-red-500"></div>
                    <div class="swiper-button-prev text-red-500"></div>
                </div>
            </section>
            <section>
                <h2 class="text-2xl font-semibold mb-4 ms-2" style="font-family: 'Montserrat', sans-serif; font:italic;">Yeni Filmler</h2>
                <div class="swiper trend-swiper">
                    <div class="swiper-wrapper">
                        <?php 
                            $query = $conn->query("SELECT * FROM movie_and_series WHERE type = 'movie' ORDER BY release_date DESC LIMIT 10");
                            $contents = $query->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($contents as $content) {
                                $categoryQuery = $conn->prepare("SELECT * FROM category WHERE id = :id");
                                $categoryQuery->execute(['id' => $content['category_id']]);
                                $category = $categoryQuery->fetch(PDO::FETCH_ASSOC);

                        ?>
                        <div class="swiper-slide">
                            <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg relative group">
                                <img src="<?php echo $content['poster']; ?>" alt="<?php echo $content['name']; ?> Posteri" class="w-full  cursor-pointer" style="height: 27rem;" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                                    <?php if (isset($_SESSION['user'])) { ?>
                                        <?php 
                                            $watchlistQuery = $conn->prepare("SELECT * FROM watchlist WHERE user_id = :user_id AND movie_and_series_id = :movie_and_series_id");
                                            $watchlistQuery->execute(['user_id' => $_SESSION['user']['id'], 'movie_and_series_id' => $content['id']]);
                                            $watchlist = $watchlistQuery->fetch(PDO::FETCH_ASSOC);
                                        ?>
                                        <?php if ($watchlist) { ?>
                                            <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700" onclick="removeFromWatchlist(<?php echo $content['id']; ?>)">
                                            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor">
                                                <g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <g id="Edit / Remove_Minus"> <path id="Vector" d="M6 12H18" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g> </g>
                                            </svg>
                                            </button>
                                        <?php } else { ?>
                                            <button class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700" onclick="addToWatchlist(<?php echo $content['id']; ?>)">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                                <div class="p-3" onclick="window.location.href = '/movie/<?php echo seo_friendly_url($content['name']) . '-' . $content['id']; ?>'">
                                    <h3 class="font-bold text-sm truncate"><?php echo $content['name']; ?></h3>
                                    <div class="flex justify-between items-center">
                                        <p class="text-xs text-gray-400"><?php echo $category['name']; ?> | <?php echo $content['release_date']; ?></p>
                                        <div class="flex text-yellow-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.953a1 1 0 00.95.69h4.175c.969 0 1.371 1.24.588 1.81l-3.38 2.458a1 1 0 00-.364 1.118l1.287 3.953c.3.921-.755 1.688-1.54 1.118l-3.38-2.458a1 1 0 00-1.175 0l-3.38 2.458c-.784.57-1.838-.197-1.54-1.118l1.287-3.953a1 1 0 00-.364-1.118L2.41 9.38c-.783-.57-.38-1.81.588-1.81h4.175a1 1 0 00.95-.69L9.049 2.927z" />
                                            </svg>
                                            <span class="text-xs ml-1"><?php echo $content['rating']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <!-- Navigation buttons -->
                    <div class="swiper-button-next text-red-500"></div>
                    <div class="swiper-button-prev text-red-500"></div>
                </div>
            </section>
        </div>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    function addToWatchlist(contentId) {
        $.ajax({
            url: '/database/index.php?delete=false',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                contentId: contentId,
                status: 'watching'
            }),
            success: function(response) {
                response = JSON.parse(response);
                if (response.success == 'true' || response.success == true) {
                    Swal.fire('Başarılı!', 'İzleme listesine eklendi.', 'success');
                } else {
                    Swal.fire('Hata!', 'İzleme listesine eklenirken bir hata oluştu.', 'error');
                }

                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        });
    }

    function removeFromWatchlist(contentId) {
        $.ajax({
            url: '/database/index.php?delete=true',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                contentId: contentId
            }),
            success: function(response) {
                response = JSON.parse(response);
                if (response.success == 'true' || response.success == true) {
                    Swal.fire('Başarılı!', 'İzleme listesinden kaldırıldı.', 'success');
                } else {
                    Swal.fire('Hata!', 'İzleme listesinden kaldırılırken bir hata oluştu.', 'error');
                }

                setTimeout(() => {
                    location.reload();
                }, 1000);
            }
        });
    }
</script>
<style>
    img:hover {
        transform: scale(1.05);
        transition: all 0.3s ease-in-out;
        box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        cursor: pointer;
    }
</style>
<?php include 'layout/footer.php'; ?>