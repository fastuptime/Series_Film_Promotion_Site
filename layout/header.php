
<?php include 'database/connect.php'; ?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CinemaHub - Film ve Dizi Platformu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css" integrity="sha512-5Hs3dF2AEPkpNAR7UiOHba+lRSJNeM2ECkwxUIxC1Q/FLycGTbNapWXB4tP889k5T5Ju8fs4b1P5z/iB4nMfSQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Aldrich&family=Dancing+Script:wght@400..700&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Playwrite+BE+VLG:wght@100..400&display=swap" rel="stylesheet">    
</head>
<body class="bg-gray-900 text-white">
    <div class="container mx-auto px-4 dark">
        <nav class="flex items-center justify-between py-4 border-b border-gray-700">
            <div class="flex items-center" onclick="window.location.href = '/index.php'" style="cursor: pointer;">
                <i><h1 class="text-3xl font-bold text-red-500" style="font-family: 'Aldrich', sans-serif;">Cinema<span class="text-white">Hub</span></h1></i>
            </div>
            <div class="space-x-4 flex items-center">
                <a href="/category/action" class="hover:text-red-500 transition">Aksiyon</a>
                <a href="/category/science-fiction" class="hover:text-red-500 transition">Bilim Kurgu</a>
                <a href="/category/animation" class="hover:text-red-500 transition">Anime</a>
                <?php if (isset($_SESSION['user'])): ?>
                    <div class="relative">
                        <button class="flex items-center space-x-2" id="menu">
                            <img src="https://avataaars.io/?avatarStyle=Circle&topType=LongHairStraight&accessoriesType=Blank&hairColor=BrownDark&facialHairType=Blank&clotheType=BlazerShirt&eyeType=Default&eyebrowType=Default&mouthType=Default&skinColor=Light" alt="Profil" class="w-8 h-8 rounded-full">
                            <span>Hoşgeldin, <?php echo $_SESSION['user']['username']; ?></span>
                        </button>
                        <div class="absolute bg-gray-800 w-48 right-0 mt-2 rounded hidden" id="menu-items">
                            <?php if ($_SESSION['user']['role'] == 'admin'): ?>
                                <a href="/admin/panel" class="block px-4 py-2 hover:bg-gray-700 transition">Yönetim Paneli</a>
                            <?php endif; ?>
                            <a href="logout.php" class="block px-4 py-2 hover:bg-gray-700 transition">Çıkış Yap</a>
                        </div>
                    </div>

                    <script>
                        document.getElementById('menu').addEventListener('click', () => {
                            document.getElementById('menu-items').classList.toggle('hidden');
                        });

                        document.addEventListener('click', (e) => {
                            if (!document.getElementById('menu').contains(e.target)) {
                                document.getElementById('menu-items').classList.add('hidden');
                            }
                        });
                    </script>

                <?php else: ?>
                    <button 
                        data-modal-target="login-modal" 
                        class="login-modal-trigger bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition"
                    >
                        Giriş
                    </button>
                <?php endif; ?>
            </div>
        </nav>
        <?php if (!isset($_SESSION['user'])): ?>
        <div 
            id="login-modal" 
            class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center"
        >
            <div class="bg-gray-800 rounded-lg p-8 w-96 relative">
                <button class="modal-close absolute top-4 right-4 text-white hover:text-red-500">
                    ✕
                </button>
                <h2 class="text-2xl font-bold mb-6 text-center text-red-500">Giriş Yap</h2>
                <form class="space-y-4" action="/index.php" method="POST">
                    <input 
                        type="email" 
                        placeholder="E-posta" 
                        name="email"
                        class="w-full px-4 py-2 bg-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                    <input 
                        type="password" 
                        name="password"
                        placeholder="Şifre" 
                        class="w-full px-4 py-2 bg-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                    <button 
                        type="submit" 
                        class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition"
                    >
                        Giriş Yap
                    </button>

                    <hr class="border-gray-700">

                    <a class="register-modal-trigger text-center block text-gray-500 hover:underline" data-modal-target="register-modal" >Hesabın yok mu? Kayıt ol</a>
                </form>
            </div>
        </div>

        <div 
            id="register-modal" 
            class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center"
        >
            <div class="bg-gray-800 rounded-lg p-8 w-96 relative">
                <button class="modal-close absolute top-4 right-4 text-white hover:text-red-500">
                    ✕
                </button>
                <h2 class="text-2xl font-bold mb-6 text-center text-red-500">Kayıt Ol</h2>
                <form class="space-y-4" action="/register.php" method="POST">
                    <input 
                        type="text" 
                        placeholder="Kullanıcı Adı" 
                        name="username"
                        class="w-full px-4 py-2 bg-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                    <input 
                        type="email" 
                        placeholder="E-posta" 
                        name="email"
                        class="w-full px-4 py-2 bg-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                    <input 
                        type="password" 
                        name="password"
                        placeholder="Şifre" 
                        class="w-full px-4 py-2 bg-gray-700 rounded focus:outline-none focus:ring-2 focus:ring-red-500"
                    >
                    <button 
                        type="submit" 
                        class="w-full bg-red-600 text-white py-2 rounded hover:bg-red-700 transition"
                    >
                        Kayıt Ol
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>