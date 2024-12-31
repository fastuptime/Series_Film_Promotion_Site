-- Film ve dizi kategorilerini tutan tablo
CREATE TABLE `category` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,      -- Kategorinin adı
    `slug` VARCHAR(255) NOT NULL,      -- Kategorinin URL dostu ismi
    PRIMARY KEY (`id`)                -- Kategorinin birincil anahtarı
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Film ve dizileri tutan tablo
CREATE TABLE `movie_and_series` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,         -- Filmin/dizinin adı
    `original_name` VARCHAR(255),         -- Orijinal adı
    `description` TEXT NOT NULL,          -- Film/dizi açıklaması
    `duration` INT(11) NOT NULL,          -- Süresi (dakika cinsinden)
    `trailer` VARCHAR(512) NOT NULL,      -- Fragman URL'si
    `poster` VARCHAR(512) NOT NULL,       -- Poster URL'si
    `backdrop` VARCHAR(512),              -- Arka plan resmi URL'si
    `release_date` DATE NOT NULL,         -- Yayınlanma tarihi
    `rating` DECIMAL(2,1) NOT NULL,       -- Puanı
    `country` VARCHAR(255) NOT NULL,      -- Ülke
    `category_id` INT(11) NOT NULL,       -- Kategori ID'si
    `type` ENUM('movie','series') NOT NULL, -- Film mi, dizi mi
    `status` ENUM('active','coming_soon','ended') NOT NULL, -- Durum (aktif, yakında, bitmiş)
    `views` INT(11) NOT NULL DEFAULT 0,   -- Görüntülenme sayısı
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Oluşturulma tarihi
    PRIMARY KEY (`id`),
    FOREIGN KEY (`category_id`) REFERENCES `category`(`id`) -- Kategoriye referans
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Aktör bilgilerini tutan tablo
CREATE TABLE `actor` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,     -- Aktörün adı
    `birthday` DATE NOT NULL,         -- Aktörün doğum tarihi
    `country` VARCHAR(255) NOT NULL,  -- Aktörün memleketi
    `biography` TEXT,                 -- Aktörün biyografisi
    `image_url` VARCHAR(512),         -- Aktörün fotoğrafının URL'si
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Aktörün kaydedildiği tarih
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Yönetmen bilgilerini tutan tablo
CREATE TABLE `director` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,     -- Yönetmenin adı
    `birthday` DATE NOT NULL,         -- Yönetmenin doğum tarihi
    `country` VARCHAR(255) NOT NULL,  -- Yönetmenin memleketi
    `biography` TEXT,                 -- Yönetmenin biyografisi
    `image_url` VARCHAR(512),         -- Yönetmenin fotoğrafının URL'si
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Yönetmenin kaydedildiği tarih
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Kullanıcıları tutan tablo
CREATE TABLE `user` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(255) NOT NULL,    -- Kullanıcı adı
    `password` VARCHAR(255) NOT NULL,    -- Kullanıcı şifresi
    `email` VARCHAR(255) NOT NULL,       -- Kullanıcı e-posta adresi
    `role` ENUM('admin', 'moderator', 'user') NOT NULL DEFAULT 'user', -- Kullanıcı rolü
    `avatar` VARCHAR(512),               -- Kullanıcı profil fotoğrafı URL'si
    `ip_address` VARCHAR(50),            -- Kullanıcının IP adresi
    `is_active` BOOLEAN DEFAULT TRUE,    -- Kullanıcının aktif olup olmadığı
    `last_login` TIMESTAMP,              -- Kullanıcının son giriş zamanı
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Kullanıcı hesabının oluşturulma zamanı
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_email` (`email`), -- E-posta adresi benzersiz
    UNIQUE KEY `unique_username` (`username`) -- Kullanıcı adı benzersiz
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- Yeni sezonları tutan tablo
CREATE TABLE `season` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `movie_and_series_id` INT(11) NOT NULL, -- Film ya da dizinin referansı
    `season_number` INT NOT NULL,           -- Sezon numarası
    `name` VARCHAR(255) NOT NULL,           -- Sezon adı
    `poster` VARCHAR(512),                 -- Sezon posterinin URL'si
    `release_date` DATE,                    -- Sezonun yayınlanma tarihi
    PRIMARY KEY (`id`),
    FOREIGN KEY (`movie_and_series_id`) REFERENCES `movie_and_series`(`id`) -- Film/diziye referans
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Yeni bölümleri tutan tablo
CREATE TABLE `episode` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `season_id` INT(11) NOT NULL,           -- Sezon ID'si
    `episode_number` INT NOT NULL,          -- Bölüm numarası
    `name` VARCHAR(255) NOT NULL,           -- Bölüm adı
    `description` TEXT,                     -- Bölüm açıklaması
    `duration` INT NOT NULL,                -- Bölüm süresi
    `release_date` DATE,                    -- Bölüm yayınlanma tarihi
    `video_url` VARCHAR(512) NOT NULL,      -- Bölüm video URL'si
    PRIMARY KEY (`id`),
    FOREIGN KEY (`season_id`) REFERENCES `season`(`id`) -- Sezona referans
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Kullanıcıların izleme listelerini tutan tablo
CREATE TABLE `watchlist` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,           -- Kullanıcı ID'si
    `movie_and_series_id` INT(11) NOT NULL, -- Film ya da dizi ID'si
    `status` ENUM('planning','watching','completed','dropped') NOT NULL, -- İzleme durumu
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- İzleme listesine eklenme tarihi
    PRIMARY KEY (`id`),
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`), -- Kullanıcıya referans
    FOREIGN KEY (`movie_and_series_id`) REFERENCES `movie_and_series`(`id`) -- Film/diziye referans
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Film ve diziler için altyazıları tutan tablo
CREATE TABLE `subtitle` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `movie_and_series_id` INT(11) NOT NULL, -- Film ya da dizi ID'si
    `episode_id` INT(11),                   -- Bölüm ID'si (opsiyonel)
    `language` VARCHAR(50) NOT NULL,         -- Altyazı dili
    `subtitle_url` VARCHAR(512) NOT NULL,    -- Altyazı dosyasının URL'si
    PRIMARY KEY (`id`),
    FOREIGN KEY (`movie_and_series_id`) REFERENCES `movie_and_series`(`id`), -- Film/diziye referans
    FOREIGN KEY (`episode_id`) REFERENCES `episode`(`id`) -- Bölüme referans (opsiyonel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Film ve diziler için türleri tutan tablo
CREATE TABLE `genre` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL, -- Tür adı
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Film ve dizilerin türlerini tutan ilişkilendirme tablosu
CREATE TABLE `movie_genre` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `movie_and_series_id` INT(11) NOT NULL, -- Film/dizi ID'si
    `genre_id` INT(11) NOT NULL,            -- Tür ID'si
    PRIMARY KEY (`id`),
    FOREIGN KEY (`movie_and_series_id`) REFERENCES `movie_and_series`(`id`), -- Film/diziye referans
    FOREIGN KEY (`genre_id`) REFERENCES `genre`(`id`) -- Türlere referans
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- Kullanıcı yorumlarını tutan tablo
CREATE TABLE `comment` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `movie_and_series_id` INT(11) NOT NULL, -- Film ya da dizinin referansı
    `user_id` INT(11) NOT NULL,             -- Yorumu yapan kullanıcının referansı
    `content` TEXT NOT NULL,                -- Yorumun içeriği
    `parent_id` INT(11) DEFAULT NULL,       -- Üst yorumun ID'si (cevaplı yorumlar için)
    `status` ENUM('active','pending','deleted') DEFAULT 'pending', -- Yorumun durumu
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Yorumun oluşturulma zamanı
    PRIMARY KEY (`id`),
    FOREIGN KEY (`movie_and_series_id`) REFERENCES `movie_and_series`(`id`), -- Film/diziye referans
    FOREIGN KEY (`user_id`) REFERENCES `user`(`id`), -- Kullanıcıya referans
    FOREIGN KEY (`parent_id`) REFERENCES `comment`(`id`) -- Yorumun kendisine referans (cevaplı yorumlar)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
