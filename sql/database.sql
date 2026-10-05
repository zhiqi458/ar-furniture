CREATE DATABASE IF NOT EXISTS `ar_furniture` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ar_furniture`;

-- 管理员表
CREATE TABLE IF NOT EXISTS `admins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 初始化默认管理员 (账号: admin , 密码: adminpassword)
-- INSERT INTO `admins` (`username`, `password_hash`) VALUES
-- ('admin', '$2y$10$hZVMHJapHXcbFAuwJHZ8LOY4wnVZ9xMpyTAVNq4QikW3Lv2gkswlS'); -- 实际部署请重新生成密码

-- 分类表
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `sort_order` INT DEFAULT 0,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 插入示例分类
-- INSERT INTO `categories` (`slug`, `name`, `sort_order`, `is_active`) VALUES
-- ('chairs', '椅子 / Chairs', 1, 1),
-- ('sofas', '沙发 / Sofas', 2, 1),
-- ('tables', '桌子 / Tables', 3, 1),
-- ('lighting', '灯具 / Lighting', 4, 1);

-- 产品表
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `slug` VARCHAR(120) NOT NULL UNIQUE,
  `name` VARCHAR(150) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) DEFAULT 'RM',
  `glb_path` VARCHAR(255) NOT NULL,
  `usdz_path` VARCHAR(255) DEFAULT '',
  `thumb_path` VARCHAR(255) DEFAULT '',
  `width_cm` DECIMAL(8,2) DEFAULT 0.00,
  `height_cm` DECIMAL(8,2) DEFAULT 0.00,
  `depth_cm` DECIMAL(8,2) DEFAULT 0.00,
  `is_active` TINYINT(1) DEFAULT 1,
  `sort_order` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 插入示例产品
-- INSERT INTO `products` (`category_id`, `slug`, `name`, `description`, `price`, `currency`, `glb_path`, `thumb_path`, `width_cm`, `height_cm`, `depth_cm`, `is_active`) VALUES
-- (1, 'modern-armchair', '现代单人休闲椅', '符合人体工学的现代单人沙发椅，采用高密度海绵与舒适织物。', 499.00, 'RM', 'models/sample_chair.glb', 'uploads/thumbs/sample_chair.jpg', 75.00, 85.00, 70.00, 1),
-- (3, 'minimalist-coffee-table', '极简木质茶几', '北欧极简风格实木茶几，稳固耐用，适合现代客厅布局。', 299.00, 'RM', 'models/sample_table.glb', 'uploads/thumbs/sample_table.jpg', 110.00, 45.00, 60.00, 1);