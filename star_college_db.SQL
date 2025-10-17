-- Main Database for Star Group of Colleges
CREATE DATABASE star_college_db;
USE star_college_db;

-- Table for website visitors tracking
CREATE TABLE visitors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45),
    user_agent TEXT,
    page_visited VARCHAR(255),
    visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    referrer VARCHAR(255),
    country VARCHAR(100),
    device_type VARCHAR(50)
);

-- Table for student applications
CREATE TABLE applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    gender ENUM('Male', 'Female') NOT NULL,
    program VARCHAR(100) NOT NULL,
    transport ENUM('Yes', 'No') NOT NULL,
    remarks TEXT,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('Pending', 'Reviewed', 'Accepted', 'Rejected') DEFAULT 'Pending'
);

-- Table for student results
CREATE TABLE results (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_type ENUM('weekly', 'monthly') NOT NULL,
    program VARCHAR(100) NOT NULL,
    part ENUM('part1', 'part2') NOT NULL,
    title VARCHAR(255) NOT NULL,
    test_date DATE NOT NULL,
    image_url TEXT,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Table for website content (multilingual support)
CREATE TABLE website_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    content_key VARCHAR(100) UNIQUE NOT NULL,
    content_en TEXT NOT NULL,
    content_ur TEXT NOT NULL,
    section VARCHAR(50),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for gallery images
CREATE TABLE gallery (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_url TEXT NOT NULL,
    title_en VARCHAR(255),
    title_ur VARCHAR(255),
    description_en TEXT,
    description_ur TEXT,
    category VARCHAR(100),
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE
);

-- Table for admin users
CREATE TABLE admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    email VARCHAR(255),
    role ENUM('super_admin', 'admin', 'editor') DEFAULT 'editor',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for contact information
CREATE TABLE contact_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    phone1 VARCHAR(20),
    phone2 VARCHAR(20),
    address_en TEXT,
    address_ur TEXT,
    email VARCHAR(255),
    facebook_url VARCHAR(255),
    instagram_url VARCHAR(255),
    whatsapp_number VARCHAR(20),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table for website statistics
CREATE TABLE website_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    total_visitors INT DEFAULT 0,
    total_applications INT DEFAULT 0,
    total_results INT DEFAULT 0,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: ranakashan10)
INSERT INTO admin_users (username, password_hash, full_name, email, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Rana Kashan', 'kashan@stargroup.edu.pk', 'super_admin');

-- Insert default contact information
INSERT INTO contact_info (phone1, phone2, address_en, address_ur, email) 
VALUES (
    '0342-7120660', 
    '0302-9336614', 
    'Near Railway Road, Pahar Pur — Star Group of Colleges',
    'نزدیک ریلوے پھاٹک، پہاڑ پور — اسٹار گروپ آف کالجز',
    'starcollage294@gmail.com'
);

-- Insert default website content
INSERT INTO website_content (content_key, content_en, content_ur, section) VALUES
('site_title', 'Star Group of Colleges', 'سٹار گروپ آف کالجز', 'header'),
('site_tagline', 'Excellence in Education • Future Ready', 'تعلیم میں برتری • مستقبل کے لیے تیار', 'header'),
('hero_title', 'Shape Your Future With Quality Education', 'معیاری تعلیم کے ساتھ اپنا مستقبل سنواریں', 'hero'),
('hero_subtitle', 'Join Pakistan''s leading educational institution with experienced faculty, modern facilities, and career-focused programs.', 'پاکستان کے معروف تعلیمی ادارے میں شامل ہوں، تجربہ کار اساتذہ، جدید سہولیات اور کیریئر پر مبنی پروگراموں کے ساتھ۔', 'hero'),
('programs_title', 'Our Academic Programs', 'ہمارے تعلیمی پروگرام', 'programs'),
('programs_subtitle', 'Choose from a wide range of intermediate programs designed for your success', 'اپنی کامیابی کے لیے ڈیزائن کردہ انٹرمیڈیٹ پروگراموں میں سے انتخاب کریں', 'programs'),
('gallery_title', 'Campus Gallery', 'کیمپس گیلری', 'gallery'),
('gallery_subtitle', 'Explore our state-of-the-art facilities and vibrant campus life', 'ہماری جدید ترین سہولیات اور پرجوش کیمپس زندگی کو دریافت کریں', 'gallery'),
('contact_title', 'Get In Touch', 'رابطہ کریں', 'contact');