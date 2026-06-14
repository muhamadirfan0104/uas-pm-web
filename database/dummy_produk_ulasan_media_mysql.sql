-- Data dummy SiTahu: produk, user pembeli, pesanan selesai, item pesanan, dan ulasan.
-- Jalankan setelah php artisan migrate. File ini khusus MySQL/MariaDB.
START TRANSACTION;

-- Bersihkan data dummy lama agar aman jika file di-import ulang.
DELETE u FROM ulasan u JOIN pesanan p ON p.id = u.pesanan_id WHERE p.nomor_invoice LIKE 'DUMMY-SITAHU-%';
DELETE ip FROM item_pesanan ip JOIN pesanan p ON p.id = ip.pesanan_id WHERE p.nomor_invoice LIKE 'DUMMY-SITAHU-%';
DELETE pg FROM pengiriman pg JOIN pesanan p ON p.id = pg.pesanan_id WHERE p.nomor_invoice LIKE 'DUMMY-SITAHU-%';
DELETE pb FROM pembayaran pb JOIN pesanan p ON p.id = pb.pesanan_id WHERE p.nomor_invoice LIKE 'DUMMY-SITAHU-%';
DELETE FROM pesanan WHERE nomor_invoice LIKE 'DUMMY-SITAHU-%';
DELETE FROM gambar_produk WHERE produk_id IN (SELECT id FROM produk WHERE nama IN (
    'Tahu Putih Premium 10 pcs',
    'Tahu Putih Premium 20 pcs',
    'Tahu Kuning Gurih 10 pcs',
    'Tahu Kuning Gurih 20 pcs',
    'Tahu Susu Lembut 10 pcs',
    'Tahu Susu Lembut 20 pcs',
    'Tahu Sumedang Mini 25 pcs',
    'Tahu Sumedang Jumbo 15 pcs',
    'Tahu Pong Renyah 20 pcs',
    'Tahu Bakso Ayam 10 pcs',
    'Tahu Bakso Sapi 10 pcs',
    'Tahu Isi Sayur 10 pcs',
    'Tahu Walik Ayam 12 pcs',
    'Tahu Crispy Original 15 pcs',
    'Tahu Crispy Pedas 15 pcs',
    'Tahu Bulat Original 25 pcs',
    'Tahu Gejrot Pack 12 pcs',
    'Tahu Bacem Siap Goreng 10 pcs',
    'Tahu Tempe Campur Hemat',
    'Paket Tahu Harian Keluarga',
    'Paket Tahu Usaha Kecil',
    'Paket Tahu Acara 50 pcs',
    'Paket Tahu Acara 100 pcs',
    'Tahu Putih Kotak Besar 5 pcs',
    'Tahu Kuning Kotak Besar 5 pcs',
    'Tahu Sutra Lembut 4 pcs',
    'Tahu Organik Putih 10 pcs',
    'Tahu Organik Kuning 10 pcs',
    'Tahu Kukus Diet 8 pcs',
    'Tahu Kulit Goreng 20 pcs',
    'Tahu Petis Mini 20 pcs',
    'Tahu Mercon 10 pcs',
    'Tahu Aci 15 pcs',
    'Tahu Kriuk Balado 15 pcs',
    'Tahu Kukus Bumbu Kuning 10 pcs',
    'Paket Campur Tahu Premium'
));
DELETE FROM produk WHERE nama IN (
    'Tahu Putih Premium 10 pcs',
    'Tahu Putih Premium 20 pcs',
    'Tahu Kuning Gurih 10 pcs',
    'Tahu Kuning Gurih 20 pcs',
    'Tahu Susu Lembut 10 pcs',
    'Tahu Susu Lembut 20 pcs',
    'Tahu Sumedang Mini 25 pcs',
    'Tahu Sumedang Jumbo 15 pcs',
    'Tahu Pong Renyah 20 pcs',
    'Tahu Bakso Ayam 10 pcs',
    'Tahu Bakso Sapi 10 pcs',
    'Tahu Isi Sayur 10 pcs',
    'Tahu Walik Ayam 12 pcs',
    'Tahu Crispy Original 15 pcs',
    'Tahu Crispy Pedas 15 pcs',
    'Tahu Bulat Original 25 pcs',
    'Tahu Gejrot Pack 12 pcs',
    'Tahu Bacem Siap Goreng 10 pcs',
    'Tahu Tempe Campur Hemat',
    'Paket Tahu Harian Keluarga',
    'Paket Tahu Usaha Kecil',
    'Paket Tahu Acara 50 pcs',
    'Paket Tahu Acara 100 pcs',
    'Tahu Putih Kotak Besar 5 pcs',
    'Tahu Kuning Kotak Besar 5 pcs',
    'Tahu Sutra Lembut 4 pcs',
    'Tahu Organik Putih 10 pcs',
    'Tahu Organik Kuning 10 pcs',
    'Tahu Kukus Diet 8 pcs',
    'Tahu Kulit Goreng 20 pcs',
    'Tahu Petis Mini 20 pcs',
    'Tahu Mercon 10 pcs',
    'Tahu Aci 15 pcs',
    'Tahu Kriuk Balado 15 pcs',
    'Tahu Kukus Bumbu Kuning 10 pcs',
    'Paket Campur Tahu Premium'
);
DELETE FROM alamat WHERE user_id IN (SELECT id FROM users WHERE email LIKE 'dummy.pembeli.%@sitahu.local');
DELETE FROM users WHERE email LIKE 'dummy.pembeli.%@sitahu.local';

-- User pembeli dummy. Semua password: password
INSERT INTO users (name, email, telepon, password, role, aktif, created_at, updated_at) VALUES
('Dewi Lestari', 'dummy.pembeli.01@sitahu.local', '081230001001', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'pembeli', 1, NOW(), NOW()),
('Rizky Pratama', 'dummy.pembeli.02@sitahu.local', '081230001002', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'pembeli', 1, NOW(), NOW()),
('Siti Rahma', 'dummy.pembeli.03@sitahu.local', '081230001003', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'pembeli', 1, NOW(), NOW()),
('Andi Saputra', 'dummy.pembeli.04@sitahu.local', '081230001004', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'pembeli', 1, NOW(), NOW()),
('Maya Putri', 'dummy.pembeli.05@sitahu.local', '081230001005', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'pembeli', 1, NOW(), NOW()),
('Bima Santoso', 'dummy.pembeli.06@sitahu.local', '081230001006', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'pembeli', 1, NOW(), NOW()),
('Nadia Amelia', 'dummy.pembeli.07@sitahu.local', '081230001007', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'pembeli', 1, NOW(), NOW()),
('Fajar Nugroho', 'dummy.pembeli.08@sitahu.local', '081230001008', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'pembeli', 1, NOW(), NOW()),
('Lina Wulandari', 'dummy.pembeli.09@sitahu.local', '081230001009', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'pembeli', 1, NOW(), NOW()),
('Yoga Mahendra', 'dummy.pembeli.10@sitahu.local', '081230001010', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'pembeli', 1, NOW(), NOW()),
('Intan Sari', 'dummy.pembeli.11@sitahu.local', '081230001011', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'pembeli', 1, NOW(), NOW()),
('Hendra Wijaya', 'dummy.pembeli.12@sitahu.local', '081230001012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.', 'pembeli', 1, NOW(), NOW());

-- Alamat dummy. Satu pembeli dapat menyimpan lebih dari satu nama/alamat penerima.
INSERT INTO alamat (user_id, nama_penerima, telepon, email_penerima, alamat_lengkap, latitude, longitude, utama, created_at, updated_at)
SELECT id, name, telepon, email, CONCAT('Jl. Melati No. ', id, ', RT 03/RW 02, Kecamatan Sukamaju, Kota Demo'), NULL, NULL, 1, NOW(), NOW()
FROM users
WHERE email LIKE 'dummy.pembeli.%@sitahu.local';

INSERT INTO alamat (user_id, nama_penerima, telepon, email_penerima, alamat_lengkap, latitude, longitude, utama, created_at, updated_at)
SELECT id, CONCAT('Keluarga ', SUBSTRING_INDEX(name, ' ', 1)), CONCAT('08223', LPAD(id, 7, '0')), CONCAT('alamat.', id, '@sitahu.local'), CONCAT('Jl. Mawar No. ', id, ', dekat Pasar Pagi, Kecamatan Sukamaju, Kota Demo'), NULL, NULL, 0, NOW(), NOW()
FROM users
WHERE email LIKE 'dummy.pembeli.%@sitahu.local';

-- Produk dummy
INSERT INTO produk (nama, harga, stok, min_stok, satuan, isi_per_satuan, berat, deskripsi, masa_simpan, saran_penyimpanan, saran_penyajian, aktif, created_at, updated_at) VALUES
('Tahu Putih Premium 10 pcs', 12000.00, 180, 25, 'pack', 10, 0.70, 'Tahu putih lembut untuk tumisan, sayur berkuah, sup, dan masakan harian keluarga.', 2, 'Simpan di kulkas dan rendam dengan air bersih.', 'Goreng sebentar atau masak bersama bumbu favorit.', 1, NOW(), NOW()),
('Tahu Putih Premium 20 pcs', 23000.00, 150, 20, 'pack', 20, 1.40, 'Paket tahu putih isi lebih banyak untuk kebutuhan rumah dan dapur usaha kecil.', 2, 'Simpan tertutup di suhu dingin.', 'Cocok untuk tumisan, pepes, dan tahu isi.', 1, NOW(), NOW()),
('Tahu Kuning Gurih 10 pcs', 13000.00, 170, 25, 'pack', 10, 0.70, 'Tahu kuning gurih dengan tekstur padat dan rasa yang cocok untuk digoreng.', 2, 'Simpan dalam wadah tertutup di kulkas.', 'Goreng hingga bagian luar renyah.', 1, NOW(), NOW()),
('Tahu Kuning Gurih 20 pcs', 25000.00, 140, 20, 'pack', 20, 1.40, 'Paket tahu kuning isi banyak untuk lauk keluarga, katering kecil, dan warung makan.', 2, 'Ganti air rendaman bila disimpan lebih dari sehari.', 'Nikmat digoreng kering atau dibuat tahu bacem.', 1, NOW(), NOW()),
('Tahu Susu Lembut 10 pcs', 18000.00, 120, 15, 'pack', 10, 0.65, 'Tahu susu lembut dengan rasa ringan dan tekstur creamy.', 2, 'Simpan dingin agar tekstur tetap lembut.', 'Goreng dengan minyak panas hingga keemasan.', 1, NOW(), NOW()),
('Tahu Susu Lembut 20 pcs', 35000.00, 90, 12, 'pack', 20, 1.30, 'Tahu susu isi banyak untuk stok lauk praktis dan menu camilan keluarga.', 2, 'Simpan di chiller, jangan terkena panas langsung.', 'Sajikan dengan cabai rawit atau sambal kecap.', 1, NOW(), NOW()),
('Tahu Sumedang Mini 25 pcs', 22000.00, 160, 20, 'pack', 25, 0.85, 'Tahu sumedang ukuran mini yang cocok untuk camilan hangat.', 2, 'Simpan di tempat dingin sebelum digoreng.', 'Goreng sampai kopong dan renyah.', 1, NOW(), NOW()),
('Tahu Sumedang Jumbo 15 pcs', 24000.00, 110, 15, 'pack', 15, 0.95, 'Tahu sumedang ukuran besar untuk camilan atau pelengkap menu prasmanan.', 2, 'Simpan dengan kemasan rapat.', 'Sajikan panas bersama cabai rawit.', 1, NOW(), NOW()),
('Tahu Pong Renyah 20 pcs', 26000.00, 100, 15, 'pack', 20, 0.90, 'Tahu pong berongga dengan hasil goreng ringan dan renyah.', 2, 'Simpan di kulkas sebelum digunakan.', 'Cocok untuk tahu petis atau tahu gejrot.', 1, NOW(), NOW()),
('Tahu Bakso Ayam 10 pcs', 28000.00, 95, 12, 'pack', 10, 0.80, 'Tahu bakso ayam siap goreng untuk camilan dan lauk praktis.', 2, 'Simpan beku atau dingin sesuai kebutuhan.', 'Goreng sampai matang merata.', 1, NOW(), NOW()),
('Tahu Bakso Sapi 10 pcs', 32000.00, 85, 12, 'pack', 10, 0.80, 'Tahu bakso sapi dengan isian padat dan rasa gurih.', 2, 'Simpan di freezer untuk masa simpan lebih lama.', 'Kukus atau goreng sebelum disajikan.', 1, NOW(), NOW()),
('Tahu Isi Sayur 10 pcs', 25000.00, 90, 12, 'pack', 10, 0.85, 'Tahu isi sayur siap goreng dengan isian wortel dan kol berbumbu.', 1, 'Simpan dingin dan goreng di hari yang sama.', 'Goreng hingga tepung luar matang keemasan.', 1, NOW(), NOW()),
('Tahu Walik Ayam 12 pcs', 30000.00, 80, 10, 'pack', 12, 0.80, 'Tahu walik isi ayam untuk camilan gurih dan menu jualan.', 2, 'Simpan beku bila belum langsung digoreng.', 'Goreng kering lalu sajikan dengan saus sambal.', 1, NOW(), NOW()),
('Tahu Crispy Original 15 pcs', 21000.00, 130, 20, 'pack', 15, 0.75, 'Tahu crispy siap goreng dengan tekstur luar renyah.', 1, 'Simpan dalam kemasan tertutup.', 'Goreng menggunakan minyak panas dan api sedang.', 1, NOW(), NOW()),
('Tahu Crispy Pedas 15 pcs', 23000.00, 120, 20, 'pack', 15, 0.75, 'Tahu crispy dengan bumbu pedas ringan untuk camilan.', 1, 'Simpan dalam suhu dingin.', 'Cocok disantap dengan saus atau bubuk cabai.', 1, NOW(), NOW()),
('Tahu Bulat Original 25 pcs', 20000.00, 180, 25, 'pack', 25, 0.80, 'Tahu bulat original untuk camilan keluarga dan jualan kecil.', 2, 'Simpan dingin sebelum digoreng.', 'Goreng sampai mengembang dan berwarna kuning.', 1, NOW(), NOW()),
('Tahu Gejrot Pack 12 pcs', 27000.00, 95, 12, 'pack', 12, 0.90, 'Paket tahu gejrot berisi tahu siap saji untuk olahan kuah pedas manis.', 1, 'Simpan tahu dalam wadah bersih.', 'Sajikan dengan kuah gejrot dan cabai ulek.', 1, NOW(), NOW()),
('Tahu Bacem Siap Goreng 10 pcs', 26000.00, 85, 10, 'pack', 10, 0.90, 'Tahu bacem berbumbu manis gurih yang praktis untuk digoreng.', 2, 'Simpan dalam kulkas bersama bumbunya.', 'Goreng sebentar hingga permukaan kecokelatan.', 1, NOW(), NOW()),
('Tahu Tempe Campur Hemat', 24000.00, 100, 15, 'pack', 1, 1.20, 'Paket campur tahu dan tempe untuk belanja harian lebih praktis.', 2, 'Simpan di kulkas setelah diterima.', 'Cocok untuk gorengan, orek, dan tumisan.', 1, NOW(), NOW()),
('Paket Tahu Harian Keluarga', 36000.00, 75, 10, 'paket', 1, 2.00, 'Paket tahu lengkap untuk kebutuhan lauk keluarga selama beberapa hari.', 2, 'Simpan sesuai jenis tahu masing-masing.', 'Gunakan bergantian untuk goreng, sayur, dan tumis.', 1, NOW(), NOW()),
('Paket Tahu Usaha Kecil', 75000.00, 60, 8, 'paket', 1, 4.50, 'Paket stok tahu untuk warung makan, katering kecil, atau usaha gorengan.', 2, 'Simpan di chiller agar kualitas terjaga.', 'Atur porsi sesuai kebutuhan produksi harian.', 1, NOW(), NOW()),
('Paket Tahu Acara 50 pcs', 55000.00, 70, 8, 'paket', 50, 3.50, 'Paket tahu untuk acara keluarga, arisan, dan konsumsi sederhana.', 2, 'Pesan mendekati hari acara agar lebih segar.', 'Cocok untuk gorengan, semur, dan menu prasmanan.', 1, NOW(), NOW()),
('Paket Tahu Acara 100 pcs', 105000.00, 45, 6, 'paket', 100, 7.00, 'Paket tahu jumlah besar untuk acara dan kebutuhan dapur ramai.', 2, 'Simpan di wadah besar bersih dan dingin.', 'Siapkan bumbu sesuai menu acara.', 1, NOW(), NOW()),
('Tahu Putih Kotak Besar 5 pcs', 15000.00, 115, 15, 'pack', 5, 1.00, 'Tahu putih kotak besar untuk dipotong sesuai kebutuhan masakan.', 2, 'Simpan dalam air bersih di wadah tertutup.', 'Cocok untuk sup, capcay, dan tahu goreng.', 1, NOW(), NOW()),
('Tahu Kuning Kotak Besar 5 pcs', 16000.00, 105, 15, 'pack', 5, 1.00, 'Tahu kuning ukuran besar dengan rasa gurih untuk lauk utama.', 2, 'Simpan di kulkas dan jauhkan dari panas.', 'Potong sesuai porsi lalu goreng atau bacem.', 1, NOW(), NOW()),
('Tahu Sutra Lembut 4 pcs', 18000.00, 90, 12, 'pack', 4, 0.60, 'Tahu sutra lembut untuk sup, sapo tahu, dan masakan berkuah.', 2, 'Simpan dingin dan gunakan dengan hati-hati.', 'Masukkan terakhir agar tekstur tidak hancur.', 1, NOW(), NOW()),
('Tahu Organik Putih 10 pcs', 22000.00, 70, 8, 'pack', 10, 0.70, 'Tahu putih dari bahan pilihan untuk pelanggan yang mengutamakan kualitas.', 2, 'Simpan di kulkas setelah diterima.', 'Cocok untuk menu sehat dan masakan rumahan.', 1, NOW(), NOW()),
('Tahu Organik Kuning 10 pcs', 23000.00, 65, 8, 'pack', 10, 0.70, 'Tahu kuning bahan pilihan dengan rasa gurih natural.', 2, 'Simpan dingin dalam kemasan tertutup.', 'Goreng ringan atau olah menjadi pepes tahu.', 1, NOW(), NOW()),
('Tahu Kukus Diet 8 pcs', 20000.00, 85, 12, 'pack', 8, 0.60, 'Tahu kukus rendah minyak untuk pilihan menu lebih ringan.', 2, 'Simpan di chiller.', 'Sajikan dengan sayur atau saus rendah minyak.', 1, NOW(), NOW()),
('Tahu Kulit Goreng 20 pcs', 24000.00, 120, 15, 'pack', 20, 0.80, 'Tahu kulit goreng dengan tekstur kenyal dan rasa gurih.', 2, 'Simpan dalam wadah bersih.', 'Cocok untuk tumis, sayur lodeh, atau isian bakso tahu.', 1, NOW(), NOW()),
('Tahu Petis Mini 20 pcs', 26000.00, 95, 12, 'pack', 20, 0.80, 'Tahu mini untuk sajian tahu petis dan camilan sore.', 2, 'Simpan dingin sebelum penyajian.', 'Goreng lalu sajikan dengan petis.', 1, NOW(), NOW()),
('Tahu Mercon 10 pcs', 27000.00, 75, 10, 'pack', 10, 0.85, 'Tahu pedas isi sayur berbumbu untuk penggemar camilan pedas.', 1, 'Simpan dingin dan segera goreng.', 'Sajikan hangat agar pedasnya lebih terasa.', 1, NOW(), NOW()),
('Tahu Aci 15 pcs', 22000.00, 110, 15, 'pack', 15, 0.80, 'Tahu aci gurih untuk camilan renyah khas rumahan.', 2, 'Simpan di tempat dingin.', 'Goreng kering dan sajikan dengan cabai rawit.', 1, NOW(), NOW()),
('Tahu Kriuk Balado 15 pcs', 24000.00, 100, 15, 'pack', 15, 0.75, 'Tahu kriuk dengan bumbu balado yang praktis untuk camilan.', 1, 'Simpan tertutup sebelum digoreng.', 'Tambahkan bubuk balado setelah matang.', 1, NOW(), NOW()),
('Tahu Kukus Bumbu Kuning 10 pcs', 26000.00, 80, 10, 'pack', 10, 0.80, 'Tahu kukus berbumbu kuning untuk lauk praktis tanpa banyak persiapan.', 2, 'Simpan di kulkas dalam kemasan rapat.', 'Kukus ulang atau goreng sebentar sebelum makan.', 1, NOW(), NOW()),
('Paket Campur Tahu Premium', 68000.00, 55, 8, 'paket', 1, 3.20, 'Paket campur berisi beberapa jenis tahu favorit untuk variasi menu.', 2, 'Simpan masing-masing jenis tahu sesuai anjuran.', 'Cocok untuk keluarga yang ingin mencoba banyak varian.', 1, NOW(), NOW());

-- Pesanan selesai dan ulasan dummy
-- Ulasan 01
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.01@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kuning Gurih 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0001', DATE_SUB(NOW(), INTERVAL 1 DAY), @harga * 1, 2.50, 8000.00, (@harga * 1) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 0 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 1, @harga, @harga * 1, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0001', (@harga * 1) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu datang masih segar, teksturnya lembut dan cocok untuk masakan harian.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 0 DAY), DATE_SUB(NOW(), INTERVAL 0 DAY));

-- Ulasan 02
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.02@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Bakso Sapi 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0002', DATE_SUB(NOW(), INTERVAL 2 DAY), @harga * 2, NULL, 0.00, (@harga * 2) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 2, @harga, @harga * 2, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0002', (@harga * 2) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 2 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Kemasannya rapi dan porsinya pas untuk keluarga.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Ulasan 03
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.03@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Bacem Siap Goreng 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0003', DATE_SUB(NOW(), INTERVAL 3 DAY), @harga * 3, NULL, 0.00, (@harga * 3) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 3, @harga, @harga * 3, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0003', (@harga * 3) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 3 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Rasa tahunya gurih, digoreng sebentar sudah enak.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Ulasan 04
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.04@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kuning Kotak Besar 5 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0004', DATE_SUB(NOW(), INTERVAL 4 DAY), @harga * 4, 2.50, 8000.00, (@harga * 4) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 4, @harga, @harga * 4, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0004', (@harga * 4) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 4 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Pesanan sesuai, stoknya juga aman untuk kebutuhan dapur.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Ulasan 05
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.05@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Mercon 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0005', DATE_SUB(NOW(), INTERVAL 5 DAY), @harga * 5, NULL, 0.00, (@harga * 5) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 5, @harga, @harga * 5, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0005', (@harga * 5) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 5 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu masih bagus saat diterima dan tidak mudah hancur.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY));

-- Ulasan 06
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.06@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kuning Gurih 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0006', DATE_SUB(NOW(), INTERVAL 6 DAY), @harga * 6, NULL, 0.00, (@harga * 6) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 6, @harga, @harga * 6, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0006', (@harga * 6) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 6 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Cocok untuk jualan gorengan, hasilnya renyah dan disukai pelanggan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY));

-- Ulasan 07
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.07@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Bakso Ayam 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0007', DATE_SUB(NOW(), INTERVAL 7 DAY), @harga * 8, 2.50, 8000.00, (@harga * 8) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 8, @harga, @harga * 8, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0007', (@harga * 8) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 7 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Harga masih masuk akal untuk kualitas seperti ini.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY));

-- Ulasan 08
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.08@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Gejrot Pack 12 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0008', DATE_SUB(NOW(), INTERVAL 8 DAY), @harga * 10, NULL, 0.00, (@harga * 10) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 10, @harga, @harga * 10, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0008', (@harga * 10) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 8 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Pengambilan di toko cepat dan produknya sudah disiapkan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY));

-- Ulasan 09
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.09@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Putih Kotak Besar 5 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0009', DATE_SUB(NOW(), INTERVAL 9 DAY), @harga * 1, NULL, 0.00, (@harga * 1) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 9 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 1, @harga, @harga * 1, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0009', (@harga * 1) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 9 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Varian produknya banyak, jadi gampang pilih sesuai kebutuhan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY));

-- Ulasan 10
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.10@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Petis Mini 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0010', DATE_SUB(NOW(), INTERVAL 10 DAY), @harga * 2, 2.50, 8000.00, (@harga * 2) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 9 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 2, @harga, @harga * 2, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0010', (@harga * 2) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 10 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Saya suka karena rasanya bersih dan tidak asam.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 9 DAY), DATE_SUB(NOW(), INTERVAL 9 DAY));

-- Ulasan 11
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.11@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Putih Premium 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0011', DATE_SUB(NOW(), INTERVAL 11 DAY), @harga * 3, NULL, 0.00, (@harga * 3) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 11 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 3, @harga, @harga * 3, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0011', (@harga * 3) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 11 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Paketnya hemat untuk stok lauk beberapa hari.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY));

-- Ulasan 12
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.12@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Pong Renyah 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0012', DATE_SUB(NOW(), INTERVAL 12 DAY), @harga * 4, NULL, 0.00, (@harga * 4) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 11 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 4, @harga, @harga * 4, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0012', (@harga * 4) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 12 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu kuningnya gurih, anak-anak juga suka.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 11 DAY), DATE_SUB(NOW(), INTERVAL 11 DAY));

-- Ulasan 13
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.01@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Bulat Original 25 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0013', DATE_SUB(NOW(), INTERVAL 13 DAY), @harga * 5, 2.50, 8000.00, (@harga * 5) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 13 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 5, @harga, @harga * 5, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0013', (@harga * 5) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 13 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Teksturnya padat tapi tetap lembut saat dimasak.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY));

-- Ulasan 14
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.02@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Paket Tahu Acara 100 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0014', DATE_SUB(NOW(), INTERVAL 14 DAY), @harga * 6, NULL, 0.00, (@harga * 6) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 14 DAY), DATE_SUB(NOW(), INTERVAL 13 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 6, @harga, @harga * 6, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0014', (@harga * 6) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 14 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Pelayanan toko ramah dan produk sesuai foto.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 13 DAY), DATE_SUB(NOW(), INTERVAL 13 DAY));

-- Ulasan 15
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.03@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kulit Goreng 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0015', DATE_SUB(NOW(), INTERVAL 15 DAY), @harga * 8, NULL, 0.00, (@harga * 8) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 14 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 8, @harga, @harga * 8, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0015', (@harga * 8) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 15 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Untuk acara keluarga cukup membantu karena jumlahnya banyak.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 14 DAY), DATE_SUB(NOW(), INTERVAL 14 DAY));

-- Ulasan 16
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.04@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Putih Premium 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0016', DATE_SUB(NOW(), INTERVAL 16 DAY), @harga * 10, 2.50, 8000.00, (@harga * 10) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 16 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 10, @harga, @harga * 10, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0016', (@harga * 10) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 16 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu baksonya enak, isiannya terasa dan tidak pelit.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY));

-- Ulasan 17
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.05@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Sumedang Jumbo 15 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0017', DATE_SUB(NOW(), INTERVAL 17 DAY), @harga * 1, NULL, 0.00, (@harga * 1) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 17 DAY), DATE_SUB(NOW(), INTERVAL 16 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 1, @harga, @harga * 1, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0017', (@harga * 1) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 17 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Tahu crispy cepat matang dan hasil gorengnya bagus.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 16 DAY), DATE_SUB(NOW(), INTERVAL 16 DAY));

-- Ulasan 18
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.06@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Crispy Pedas 15 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0018', DATE_SUB(NOW(), INTERVAL 18 DAY), @harga * 2, NULL, 0.00, (@harga * 2) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 17 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 2, @harga, @harga * 2, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0018', (@harga * 2) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 18 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Repeat order karena kualitasnya konsisten.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 17 DAY), DATE_SUB(NOW(), INTERVAL 17 DAY));

-- Ulasan 19
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.07@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Paket Tahu Acara 50 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0019', DATE_SUB(NOW(), INTERVAL 19 DAY), @harga * 3, 2.50, 8000.00, (@harga * 3) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 19 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 3, @harga, @harga * 3, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0019', (@harga * 3) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 19 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Paket usaha cocok untuk warung, lebih praktis daripada beli satuan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY));

-- Ulasan 20
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.08@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kukus Diet 8 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0020', DATE_SUB(NOW(), INTERVAL 20 DAY), @harga * 4, NULL, 0.00, (@harga * 4) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 19 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 4, @harga, @harga * 4, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0020', (@harga * 4) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 20 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu sutranya lembut untuk sup dan sapo tahu.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 19 DAY), DATE_SUB(NOW(), INTERVAL 19 DAY));

-- Ulasan 21
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.09@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Paket Campur Tahu Premium' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0021', DATE_SUB(NOW(), INTERVAL 21 DAY), @harga * 5, NULL, 0.00, (@harga * 5) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 21 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 5, @harga, @harga * 5, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0021', (@harga * 5) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 21 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Tahu acinya gurih dan cocok untuk camilan sore.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY));

-- Ulasan 22
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.10@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Sumedang Mini 25 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0022', DATE_SUB(NOW(), INTERVAL 22 DAY), @harga * 6, 2.50, 8000.00, (@harga * 6) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 21 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 6, @harga, @harga * 6, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0022', (@harga * 6) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 22 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Produk sampai dalam kondisi rapi, tidak berantakan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 21 DAY), DATE_SUB(NOW(), INTERVAL 21 DAY));

-- Ulasan 23
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.11@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Crispy Original 15 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0023', DATE_SUB(NOW(), INTERVAL 23 DAY), @harga * 8, NULL, 0.00, (@harga * 8) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 23 DAY), DATE_SUB(NOW(), INTERVAL 22 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 8, @harga, @harga * 8, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0023', (@harga * 8) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 23 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu merconnya pedasnya pas, enak dimakan hangat.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 22 DAY));

-- Ulasan 24
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.12@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Paket Tahu Usaha Kecil' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0024', DATE_SUB(NOW(), INTERVAL 24 DAY), @harga * 10, NULL, 0.00, (@harga * 10) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 24 DAY), DATE_SUB(NOW(), INTERVAL 23 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 10, @harga, @harga * 10, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0024', (@harga * 10) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 24 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu putihnya bersih dan mudah diolah jadi berbagai menu.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 23 DAY), DATE_SUB(NOW(), INTERVAL 23 DAY));

-- Ulasan 25
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.01@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Organik Kuning 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0025', DATE_SUB(NOW(), INTERVAL 25 DAY), @harga * 1, 2.50, 8000.00, (@harga * 1) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 24 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 1, @harga, @harga * 1, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0025', (@harga * 1) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 25 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Paket campurnya menarik karena bisa coba beberapa varian.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 24 DAY), DATE_SUB(NOW(), INTERVAL 24 DAY));

-- Ulasan 26
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.02@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kukus Bumbu Kuning 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0026', DATE_SUB(NOW(), INTERVAL 26 DAY), @harga * 2, NULL, 0.00, (@harga * 2) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 26 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 2, @harga, @harga * 2, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0026', (@harga * 2) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 26 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu datang masih segar, teksturnya lembut dan cocok untuk masakan harian.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY));

-- Ulasan 27
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.03@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Susu Lembut 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0027', DATE_SUB(NOW(), INTERVAL 27 DAY), @harga * 3, NULL, 0.00, (@harga * 3) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 27 DAY), DATE_SUB(NOW(), INTERVAL 26 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 3, @harga, @harga * 3, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0027', (@harga * 3) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 27 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Kemasannya rapi dan porsinya pas untuk keluarga.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 26 DAY), DATE_SUB(NOW(), INTERVAL 26 DAY));

-- Ulasan 28
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.04@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Walik Ayam 12 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0028', DATE_SUB(NOW(), INTERVAL 28 DAY), @harga * 4, 2.50, 8000.00, (@harga * 4) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 28 DAY), DATE_SUB(NOW(), INTERVAL 27 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 4, @harga, @harga * 4, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0028', (@harga * 4) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 28 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Rasa tahunya gurih, digoreng sebentar sudah enak.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 27 DAY), DATE_SUB(NOW(), INTERVAL 27 DAY));

-- Ulasan 29
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.05@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Paket Tahu Harian Keluarga' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0029', DATE_SUB(NOW(), INTERVAL 29 DAY), @harga * 5, NULL, 0.00, (@harga * 5) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 29 DAY), DATE_SUB(NOW(), INTERVAL 28 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 5, @harga, @harga * 5, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0029', (@harga * 5) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 29 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Pesanan sesuai, stoknya juga aman untuk kebutuhan dapur.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 28 DAY), DATE_SUB(NOW(), INTERVAL 28 DAY));

-- Ulasan 30
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.06@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Organik Putih 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0030', DATE_SUB(NOW(), INTERVAL 30 DAY), @harga * 6, NULL, 0.00, (@harga * 6) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 29 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 6, @harga, @harga * 6, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0030', (@harga * 6) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu masih bagus saat diterima dan tidak mudah hancur.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 29 DAY), DATE_SUB(NOW(), INTERVAL 29 DAY));

-- Ulasan 31
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.07@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kriuk Balado 15 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0031', DATE_SUB(NOW(), INTERVAL 31 DAY), @harga * 8, 2.50, 8000.00, (@harga * 8) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 31 DAY), DATE_SUB(NOW(), INTERVAL 30 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 8, @harga, @harga * 8, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0031', (@harga * 8) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 31 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Cocok untuk jualan gorengan, hasilnya renyah dan disukai pelanggan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 30 DAY));

-- Ulasan 32
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.08@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Susu Lembut 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0032', DATE_SUB(NOW(), INTERVAL 32 DAY), @harga * 10, NULL, 0.00, (@harga * 10) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 32 DAY), DATE_SUB(NOW(), INTERVAL 31 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 10, @harga, @harga * 10, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0032', (@harga * 10) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 32 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Harga masih masuk akal untuk kualitas seperti ini.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 31 DAY), DATE_SUB(NOW(), INTERVAL 31 DAY));

-- Ulasan 33
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.09@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Isi Sayur 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0033', DATE_SUB(NOW(), INTERVAL 33 DAY), @harga * 1, NULL, 0.00, (@harga * 1) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 33 DAY), DATE_SUB(NOW(), INTERVAL 32 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 1, @harga, @harga * 1, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0033', (@harga * 1) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 33 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Pengambilan di toko cepat dan produknya sudah disiapkan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 32 DAY), DATE_SUB(NOW(), INTERVAL 32 DAY));

-- Ulasan 34
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.10@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Tempe Campur Hemat' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0034', DATE_SUB(NOW(), INTERVAL 34 DAY), @harga * 2, 2.50, 8000.00, (@harga * 2) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 34 DAY), DATE_SUB(NOW(), INTERVAL 33 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 2, @harga, @harga * 2, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0034', (@harga * 2) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 34 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Varian produknya banyak, jadi gampang pilih sesuai kebutuhan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 33 DAY), DATE_SUB(NOW(), INTERVAL 33 DAY));

-- Ulasan 35
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.11@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Sutra Lembut 4 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0035', DATE_SUB(NOW(), INTERVAL 35 DAY), @harga * 3, NULL, 0.00, (@harga * 3) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_SUB(NOW(), INTERVAL 34 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 3, @harga, @harga * 3, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0035', (@harga * 3) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 35 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Saya suka karena rasanya bersih dan tidak asam.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 34 DAY), DATE_SUB(NOW(), INTERVAL 34 DAY));

-- Ulasan 36
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.12@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Aci 15 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0036', DATE_SUB(NOW(), INTERVAL 36 DAY), @harga * 4, NULL, 0.00, (@harga * 4) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 36 DAY), DATE_SUB(NOW(), INTERVAL 35 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 4, @harga, @harga * 4, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0036', (@harga * 4) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 36 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Paketnya hemat untuk stok lauk beberapa hari.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_SUB(NOW(), INTERVAL 35 DAY));

-- Ulasan 37
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.01@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kuning Gurih 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0037', DATE_SUB(NOW(), INTERVAL 37 DAY), @harga * 5, 2.50, 8000.00, (@harga * 5) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 37 DAY), DATE_SUB(NOW(), INTERVAL 36 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 5, @harga, @harga * 5, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0037', (@harga * 5) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 37 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu kuningnya gurih, anak-anak juga suka.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 36 DAY), DATE_SUB(NOW(), INTERVAL 36 DAY));

-- Ulasan 38
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.02@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Bakso Sapi 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0038', DATE_SUB(NOW(), INTERVAL 38 DAY), @harga * 6, NULL, 0.00, (@harga * 6) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 38 DAY), DATE_SUB(NOW(), INTERVAL 37 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 6, @harga, @harga * 6, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0038', (@harga * 6) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 38 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Teksturnya padat tapi tetap lembut saat dimasak.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 37 DAY), DATE_SUB(NOW(), INTERVAL 37 DAY));

-- Ulasan 39
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.03@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Bacem Siap Goreng 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0039', DATE_SUB(NOW(), INTERVAL 39 DAY), @harga * 8, NULL, 0.00, (@harga * 8) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 39 DAY), DATE_SUB(NOW(), INTERVAL 38 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 8, @harga, @harga * 8, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0039', (@harga * 8) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 39 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Pelayanan toko ramah dan produk sesuai foto.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 38 DAY), DATE_SUB(NOW(), INTERVAL 38 DAY));

-- Ulasan 40
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.04@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kuning Kotak Besar 5 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0040', DATE_SUB(NOW(), INTERVAL 40 DAY), @harga * 10, 2.50, 8000.00, (@harga * 10) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 40 DAY), DATE_SUB(NOW(), INTERVAL 39 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 10, @harga, @harga * 10, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0040', (@harga * 10) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 40 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Untuk acara keluarga cukup membantu karena jumlahnya banyak.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 39 DAY), DATE_SUB(NOW(), INTERVAL 39 DAY));

-- Ulasan 41
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.05@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Mercon 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0041', DATE_SUB(NOW(), INTERVAL 41 DAY), @harga * 1, NULL, 0.00, (@harga * 1) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 41 DAY), DATE_SUB(NOW(), INTERVAL 40 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 1, @harga, @harga * 1, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0041', (@harga * 1) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 41 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu baksonya enak, isiannya terasa dan tidak pelit.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 40 DAY), DATE_SUB(NOW(), INTERVAL 40 DAY));

-- Ulasan 42
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.06@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kuning Gurih 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0042', DATE_SUB(NOW(), INTERVAL 42 DAY), @harga * 2, NULL, 0.00, (@harga * 2) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 42 DAY), DATE_SUB(NOW(), INTERVAL 41 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 2, @harga, @harga * 2, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0042', (@harga * 2) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 42 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Tahu crispy cepat matang dan hasil gorengnya bagus.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 41 DAY), DATE_SUB(NOW(), INTERVAL 41 DAY));

-- Ulasan 43
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.07@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Bakso Ayam 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0043', DATE_SUB(NOW(), INTERVAL 43 DAY), @harga * 3, 2.50, 8000.00, (@harga * 3) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 43 DAY), DATE_SUB(NOW(), INTERVAL 42 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 3, @harga, @harga * 3, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0043', (@harga * 3) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 43 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Repeat order karena kualitasnya konsisten.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 42 DAY), DATE_SUB(NOW(), INTERVAL 42 DAY));

-- Ulasan 44
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.08@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Gejrot Pack 12 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0044', DATE_SUB(NOW(), INTERVAL 44 DAY), @harga * 4, NULL, 0.00, (@harga * 4) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 44 DAY), DATE_SUB(NOW(), INTERVAL 43 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 4, @harga, @harga * 4, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0044', (@harga * 4) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 44 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Paket usaha cocok untuk warung, lebih praktis daripada beli satuan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 43 DAY), DATE_SUB(NOW(), INTERVAL 43 DAY));

-- Ulasan 45
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.09@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Putih Kotak Besar 5 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0045', DATE_SUB(NOW(), INTERVAL 45 DAY), @harga * 5, NULL, 0.00, (@harga * 5) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 45 DAY), DATE_SUB(NOW(), INTERVAL 44 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 5, @harga, @harga * 5, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0045', (@harga * 5) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 45 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu sutranya lembut untuk sup dan sapo tahu.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 44 DAY), DATE_SUB(NOW(), INTERVAL 44 DAY));

-- Ulasan 46
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.10@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Petis Mini 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0046', DATE_SUB(NOW(), INTERVAL 46 DAY), @harga * 6, 2.50, 8000.00, (@harga * 6) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 46 DAY), DATE_SUB(NOW(), INTERVAL 45 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 6, @harga, @harga * 6, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0046', (@harga * 6) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 46 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Tahu acinya gurih dan cocok untuk camilan sore.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 45 DAY), DATE_SUB(NOW(), INTERVAL 45 DAY));

-- Ulasan 47
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.11@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Putih Premium 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0047', DATE_SUB(NOW(), INTERVAL 47 DAY), @harga * 8, NULL, 0.00, (@harga * 8) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 47 DAY), DATE_SUB(NOW(), INTERVAL 46 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 8, @harga, @harga * 8, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0047', (@harga * 8) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 47 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Produk sampai dalam kondisi rapi, tidak berantakan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 46 DAY), DATE_SUB(NOW(), INTERVAL 46 DAY));

-- Ulasan 48
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.12@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Pong Renyah 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0048', DATE_SUB(NOW(), INTERVAL 48 DAY), @harga * 10, NULL, 0.00, (@harga * 10) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 48 DAY), DATE_SUB(NOW(), INTERVAL 47 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 10, @harga, @harga * 10, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0048', (@harga * 10) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 48 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu merconnya pedasnya pas, enak dimakan hangat.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 47 DAY), DATE_SUB(NOW(), INTERVAL 47 DAY));

-- Ulasan 49
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.01@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Bulat Original 25 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0049', DATE_SUB(NOW(), INTERVAL 49 DAY), @harga * 1, 2.50, 8000.00, (@harga * 1) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 49 DAY), DATE_SUB(NOW(), INTERVAL 48 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 1, @harga, @harga * 1, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0049', (@harga * 1) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 49 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu putihnya bersih dan mudah diolah jadi berbagai menu.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 48 DAY), DATE_SUB(NOW(), INTERVAL 48 DAY));

-- Ulasan 50
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.02@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Paket Tahu Acara 100 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0050', DATE_SUB(NOW(), INTERVAL 50 DAY), @harga * 2, NULL, 0.00, (@harga * 2) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 50 DAY), DATE_SUB(NOW(), INTERVAL 49 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 2, @harga, @harga * 2, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0050', (@harga * 2) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 50 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Paket campurnya menarik karena bisa coba beberapa varian.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 49 DAY), DATE_SUB(NOW(), INTERVAL 49 DAY));

-- Ulasan 51
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.03@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kulit Goreng 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0051', DATE_SUB(NOW(), INTERVAL 1 DAY), @harga * 3, NULL, 0.00, (@harga * 3) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 0 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 3, @harga, @harga * 3, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0051', (@harga * 3) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 1 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu datang masih segar, teksturnya lembut dan cocok untuk masakan harian.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 0 DAY), DATE_SUB(NOW(), INTERVAL 0 DAY));

-- Ulasan 52
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.04@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Putih Premium 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0052', DATE_SUB(NOW(), INTERVAL 2 DAY), @harga * 4, 2.50, 8000.00, (@harga * 4) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 4, @harga, @harga * 4, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0052', (@harga * 4) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 2 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Kemasannya rapi dan porsinya pas untuk keluarga.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY));

-- Ulasan 53
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.05@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Sumedang Jumbo 15 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0053', DATE_SUB(NOW(), INTERVAL 3 DAY), @harga * 5, NULL, 0.00, (@harga * 5) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 5, @harga, @harga * 5, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0053', (@harga * 5) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 3 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Rasa tahunya gurih, digoreng sebentar sudah enak.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY));

-- Ulasan 54
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.06@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Crispy Pedas 15 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0054', DATE_SUB(NOW(), INTERVAL 4 DAY), @harga * 6, NULL, 0.00, (@harga * 6) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 6, @harga, @harga * 6, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0054', (@harga * 6) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 4 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Pesanan sesuai, stoknya juga aman untuk kebutuhan dapur.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Ulasan 55
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.07@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Paket Tahu Acara 50 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0055', DATE_SUB(NOW(), INTERVAL 5 DAY), @harga * 8, 2.50, 8000.00, (@harga * 8) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 8, @harga, @harga * 8, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0055', (@harga * 8) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 5 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu masih bagus saat diterima dan tidak mudah hancur.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY));

-- Ulasan 56
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.08@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kukus Diet 8 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0056', DATE_SUB(NOW(), INTERVAL 6 DAY), @harga * 10, NULL, 0.00, (@harga * 10) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 10, @harga, @harga * 10, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0056', (@harga * 10) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 6 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Cocok untuk jualan gorengan, hasilnya renyah dan disukai pelanggan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY));

-- Ulasan 57
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.09@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Paket Campur Tahu Premium' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0057', DATE_SUB(NOW(), INTERVAL 7 DAY), @harga * 1, NULL, 0.00, (@harga * 1) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 1, @harga, @harga * 1, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0057', (@harga * 1) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 7 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Harga masih masuk akal untuk kualitas seperti ini.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY));

-- Ulasan 58
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.10@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Sumedang Mini 25 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0058', DATE_SUB(NOW(), INTERVAL 8 DAY), @harga * 2, 2.50, 8000.00, (@harga * 2) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 2, @harga, @harga * 2, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0058', (@harga * 2) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 8 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Pengambilan di toko cepat dan produknya sudah disiapkan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY));

-- Ulasan 59
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.11@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Crispy Original 15 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0059', DATE_SUB(NOW(), INTERVAL 9 DAY), @harga * 3, NULL, 0.00, (@harga * 3) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 9 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 3, @harga, @harga * 3, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0059', (@harga * 3) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 9 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Varian produknya banyak, jadi gampang pilih sesuai kebutuhan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY));

-- Ulasan 60
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.12@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Paket Tahu Usaha Kecil' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0060', DATE_SUB(NOW(), INTERVAL 10 DAY), @harga * 4, NULL, 0.00, (@harga * 4) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 9 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 4, @harga, @harga * 4, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0060', (@harga * 4) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 10 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Saya suka karena rasanya bersih dan tidak asam.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 9 DAY), DATE_SUB(NOW(), INTERVAL 9 DAY));

-- Ulasan 61
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.01@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Organik Kuning 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0061', DATE_SUB(NOW(), INTERVAL 11 DAY), @harga * 5, 2.50, 8000.00, (@harga * 5) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 11 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 5, @harga, @harga * 5, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0061', (@harga * 5) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 11 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Paketnya hemat untuk stok lauk beberapa hari.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY));

-- Ulasan 62
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.02@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kukus Bumbu Kuning 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0062', DATE_SUB(NOW(), INTERVAL 12 DAY), @harga * 6, NULL, 0.00, (@harga * 6) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 11 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 6, @harga, @harga * 6, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0062', (@harga * 6) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 12 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu kuningnya gurih, anak-anak juga suka.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 11 DAY), DATE_SUB(NOW(), INTERVAL 11 DAY));

-- Ulasan 63
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.03@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Susu Lembut 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0063', DATE_SUB(NOW(), INTERVAL 13 DAY), @harga * 8, NULL, 0.00, (@harga * 8) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 13 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 8, @harga, @harga * 8, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0063', (@harga * 8) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 13 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Teksturnya padat tapi tetap lembut saat dimasak.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY));

-- Ulasan 64
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.04@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Walik Ayam 12 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0064', DATE_SUB(NOW(), INTERVAL 14 DAY), @harga * 10, 2.50, 8000.00, (@harga * 10) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 14 DAY), DATE_SUB(NOW(), INTERVAL 13 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 10, @harga, @harga * 10, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0064', (@harga * 10) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 14 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Pelayanan toko ramah dan produk sesuai foto.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 13 DAY), DATE_SUB(NOW(), INTERVAL 13 DAY));

-- Ulasan 65
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.05@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Paket Tahu Harian Keluarga' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0065', DATE_SUB(NOW(), INTERVAL 15 DAY), @harga * 1, NULL, 0.00, (@harga * 1) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 14 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 1, @harga, @harga * 1, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0065', (@harga * 1) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 15 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Untuk acara keluarga cukup membantu karena jumlahnya banyak.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 14 DAY), DATE_SUB(NOW(), INTERVAL 14 DAY));

-- Ulasan 66
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.06@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Organik Putih 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0066', DATE_SUB(NOW(), INTERVAL 16 DAY), @harga * 2, NULL, 0.00, (@harga * 2) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 16 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 2, @harga, @harga * 2, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0066', (@harga * 2) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 16 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu baksonya enak, isiannya terasa dan tidak pelit.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY));

-- Ulasan 67
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.07@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kriuk Balado 15 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0067', DATE_SUB(NOW(), INTERVAL 17 DAY), @harga * 3, 2.50, 8000.00, (@harga * 3) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 17 DAY), DATE_SUB(NOW(), INTERVAL 16 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 3, @harga, @harga * 3, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0067', (@harga * 3) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 17 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Tahu crispy cepat matang dan hasil gorengnya bagus.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 16 DAY), DATE_SUB(NOW(), INTERVAL 16 DAY));

-- Ulasan 68
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.08@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Susu Lembut 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0068', DATE_SUB(NOW(), INTERVAL 18 DAY), @harga * 4, NULL, 0.00, (@harga * 4) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 17 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 4, @harga, @harga * 4, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0068', (@harga * 4) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 18 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Repeat order karena kualitasnya konsisten.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 17 DAY), DATE_SUB(NOW(), INTERVAL 17 DAY));

-- Ulasan 69
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.09@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Isi Sayur 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0069', DATE_SUB(NOW(), INTERVAL 19 DAY), @harga * 5, NULL, 0.00, (@harga * 5) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 19 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 5, @harga, @harga * 5, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0069', (@harga * 5) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 19 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Paket usaha cocok untuk warung, lebih praktis daripada beli satuan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY));

-- Ulasan 70
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.10@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Tempe Campur Hemat' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0070', DATE_SUB(NOW(), INTERVAL 20 DAY), @harga * 6, 2.50, 8000.00, (@harga * 6) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 19 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 6, @harga, @harga * 6, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0070', (@harga * 6) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 20 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu sutranya lembut untuk sup dan sapo tahu.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 19 DAY), DATE_SUB(NOW(), INTERVAL 19 DAY));

-- Ulasan 71
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.11@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Sutra Lembut 4 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0071', DATE_SUB(NOW(), INTERVAL 21 DAY), @harga * 8, NULL, 0.00, (@harga * 8) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 21 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 8, @harga, @harga * 8, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0071', (@harga * 8) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 21 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Tahu acinya gurih dan cocok untuk camilan sore.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY));

-- Ulasan 72
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.12@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Aci 15 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0072', DATE_SUB(NOW(), INTERVAL 22 DAY), @harga * 10, NULL, 0.00, (@harga * 10) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 21 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 10, @harga, @harga * 10, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0072', (@harga * 10) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 22 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Produk sampai dalam kondisi rapi, tidak berantakan.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 21 DAY), DATE_SUB(NOW(), INTERVAL 21 DAY));

-- Ulasan 73
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.01@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kuning Gurih 20 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0073', DATE_SUB(NOW(), INTERVAL 23 DAY), @harga * 1, 2.50, 8000.00, (@harga * 1) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 23 DAY), DATE_SUB(NOW(), INTERVAL 22 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 1, @harga, @harga * 1, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0073', (@harga * 1) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 23 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu merconnya pedasnya pas, enak dimakan hangat.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 22 DAY));

-- Ulasan 74
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.02@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Bakso Sapi 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0074', DATE_SUB(NOW(), INTERVAL 24 DAY), @harga * 2, NULL, 0.00, (@harga * 2) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 24 DAY), DATE_SUB(NOW(), INTERVAL 23 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 2, @harga, @harga * 2, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0074', (@harga * 2) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 24 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu putihnya bersih dan mudah diolah jadi berbagai menu.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 23 DAY), DATE_SUB(NOW(), INTERVAL 23 DAY));

-- Ulasan 75
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.03@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Bacem Siap Goreng 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0075', DATE_SUB(NOW(), INTERVAL 25 DAY), @harga * 3, NULL, 0.00, (@harga * 3) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 24 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 3, @harga, @harga * 3, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0075', (@harga * 3) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 25 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Paket campurnya menarik karena bisa coba beberapa varian.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 24 DAY), DATE_SUB(NOW(), INTERVAL 24 DAY));

-- Ulasan 76
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.04@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kuning Kotak Besar 5 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0076', DATE_SUB(NOW(), INTERVAL 26 DAY), @harga * 4, 2.50, 8000.00, (@harga * 4) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 26 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 4, @harga, @harga * 4, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0076', (@harga * 4) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 26 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu datang masih segar, teksturnya lembut dan cocok untuk masakan harian.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY));

-- Ulasan 77
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.05@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Mercon 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0077', DATE_SUB(NOW(), INTERVAL 27 DAY), @harga * 5, NULL, 0.00, (@harga * 5) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 27 DAY), DATE_SUB(NOW(), INTERVAL 26 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 5, @harga, @harga * 5, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0077', (@harga * 5) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 27 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Kemasannya rapi dan porsinya pas untuk keluarga.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 26 DAY), DATE_SUB(NOW(), INTERVAL 26 DAY));

-- Ulasan 78
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.06@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Kuning Gurih 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0078', DATE_SUB(NOW(), INTERVAL 28 DAY), @harga * 6, NULL, 0.00, (@harga * 6) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 28 DAY), DATE_SUB(NOW(), INTERVAL 27 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 6, @harga, @harga * 6, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0078', (@harga * 6) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 28 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Rasa tahunya gurih, digoreng sebentar sudah enak.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 27 DAY), DATE_SUB(NOW(), INTERVAL 27 DAY));

-- Ulasan 79
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.07@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Bakso Ayam 10 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0079', DATE_SUB(NOW(), INTERVAL 29 DAY), @harga * 8, 2.50, 8000.00, (@harga * 8) + 8000.00, 'kurir_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 29 DAY), DATE_SUB(NOW(), INTERVAL 28 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 8, @harga, @harga * 8, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'cod', 'DUMMY-PAY-0079', (@harga * 8) + 8000.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 29 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'kurir_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, 2.50, 8000.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 4, 'Pesanan sesuai, stoknya juga aman untuk kebutuhan dapur.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 28 DAY), DATE_SUB(NOW(), INTERVAL 28 DAY));

-- Ulasan 80
SET @user_id := (SELECT id FROM users WHERE email = 'dummy.pembeli.08@sitahu.local' LIMIT 1);
SET @produk_id := (SELECT id FROM produk WHERE nama = 'Tahu Gejrot Pack 12 pcs' ORDER BY id DESC LIMIT 1);
SET @harga := (SELECT harga FROM produk WHERE id = @produk_id);
INSERT INTO pesanan (user_id, nomor_invoice, tanggal_pesanan, subtotal_produk, jarak_km, biaya_pengiriman, total_bayar, metode_pengambilan, alamat_pengiriman_id, status, status_pembayaran, created_at, updated_at) VALUES (@user_id, 'DUMMY-SITAHU-0080', DATE_SUB(NOW(), INTERVAL 30 DAY), @harga * 10, NULL, 0.00, (@harga * 10) + 0.00, 'ambil_toko', NULL, 'selesai', 'dibayar', DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(NOW(), INTERVAL 29 DAY));
SET @pesanan_id := LAST_INSERT_ID();
INSERT INTO item_pesanan (pesanan_id, produk_id, jumlah, harga_satuan, subtotal, created_at, updated_at) VALUES (@pesanan_id, @produk_id, 10, @harga, @harga * 10, NOW(), NOW());
INSERT INTO pembayaran (pesanan_id, metode_pembayaran, referensi_pembayaran, jumlah, status, tautan_pembayaran, qr_code, dibayar_pada, created_at, updated_at) VALUES (@pesanan_id, 'transfer_bank', 'DUMMY-PAY-0080', (@harga * 10) + 0.00, 'dibayar', NULL, NULL, DATE_SUB(NOW(), INTERVAL 30 DAY), NOW(), NOW());
INSERT INTO pengiriman (pesanan_id, metode, status_pengiriman, alamat_toko, alamat_tujuan, latitude_tujuan, longitude_tujuan, jarak_km, biaya, created_at, updated_at) VALUES (@pesanan_id, 'ambil_toko', 'selesai', 'Alamat toko SiTahu', NULL, NULL, NULL, NULL, 0.00, NOW(), NOW());
INSERT INTO ulasan (pesanan_id, produk_id, user_id, rating, komentar, foto_ulasan, video_ulasan, ditampilkan, created_at, updated_at) VALUES (@pesanan_id, @produk_id, @user_id, 5, 'Tahu masih bagus saat diterima dan tidak mudah hancur.', NULL, NULL, 1, DATE_SUB(NOW(), INTERVAL 29 DAY), DATE_SUB(NOW(), INTERVAL 29 DAY));


-- Media dummy untuk ulasan foto dan video
-- Catatan: path berikut akan tampil sebagai media dummy. Untuk file asli, letakkan file di storage/app/public/dummy/ulasan/ atau upload lewat form ulasan.
CREATE TABLE IF NOT EXISTS media_ulasan (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ulasan_id BIGINT UNSIGNED NOT NULL,
    jenis ENUM('foto','video') NOT NULL,
    path VARCHAR(255) NOT NULL,
    caption VARCHAR(255) NULL,
    urutan INT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    INDEX media_ulasan_ulasan_id_jenis_index (ulasan_id, jenis)
);

UPDATE ulasan u
JOIN pesanan p ON p.id = u.pesanan_id
SET u.foto_ulasan = CONCAT('dummy/ulasan/foto-', RIGHT(p.nomor_invoice, 4), '-utama.jpg')
WHERE p.nomor_invoice LIKE 'DUMMY-SITAHU-%'
  AND CAST(RIGHT(p.nomor_invoice, 4) AS UNSIGNED) % 3 = 0;

UPDATE ulasan u
JOIN pesanan p ON p.id = u.pesanan_id
SET u.video_ulasan = CONCAT('dummy/ulasan/video-', RIGHT(p.nomor_invoice, 4), '-utama.mp4')
WHERE p.nomor_invoice LIKE 'DUMMY-SITAHU-%'
  AND CAST(RIGHT(p.nomor_invoice, 4) AS UNSIGNED) % 5 = 0;

INSERT INTO media_ulasan (ulasan_id, jenis, path, caption, urutan, created_at, updated_at)
SELECT u.id, 'foto', CONCAT('dummy/ulasan/foto-', RIGHT(p.nomor_invoice, 4), '-tambahan-1.jpg'), 'Foto tambahan ulasan produk', 1, NOW(), NOW()
FROM ulasan u
JOIN pesanan p ON p.id = u.pesanan_id
WHERE p.nomor_invoice LIKE 'DUMMY-SITAHU-%'
  AND CAST(RIGHT(p.nomor_invoice, 4) AS UNSIGNED) % 4 = 0
  AND NOT EXISTS (
      SELECT 1 FROM media_ulasan m
      WHERE m.ulasan_id = u.id
        AND m.path = CONCAT('dummy/ulasan/foto-', RIGHT(p.nomor_invoice, 4), '-tambahan-1.jpg')
  );

INSERT INTO media_ulasan (ulasan_id, jenis, path, caption, urutan, created_at, updated_at)
SELECT u.id, 'foto', CONCAT('dummy/ulasan/foto-', RIGHT(p.nomor_invoice, 4), '-tambahan-2.jpg'), 'Foto kemasan produk', 2, NOW(), NOW()
FROM ulasan u
JOIN pesanan p ON p.id = u.pesanan_id
WHERE p.nomor_invoice LIKE 'DUMMY-SITAHU-%'
  AND CAST(RIGHT(p.nomor_invoice, 4) AS UNSIGNED) % 6 = 0
  AND NOT EXISTS (
      SELECT 1 FROM media_ulasan m
      WHERE m.ulasan_id = u.id
        AND m.path = CONCAT('dummy/ulasan/foto-', RIGHT(p.nomor_invoice, 4), '-tambahan-2.jpg')
  );

INSERT INTO media_ulasan (ulasan_id, jenis, path, caption, urutan, created_at, updated_at)
SELECT u.id, 'video', CONCAT('dummy/ulasan/video-', RIGHT(p.nomor_invoice, 4), '-tambahan.mp4'), 'Video singkat ulasan produk', 3, NOW(), NOW()
FROM ulasan u
JOIN pesanan p ON p.id = u.pesanan_id
WHERE p.nomor_invoice LIKE 'DUMMY-SITAHU-%'
  AND CAST(RIGHT(p.nomor_invoice, 4) AS UNSIGNED) % 7 = 0
  AND NOT EXISTS (
      SELECT 1 FROM media_ulasan m
      WHERE m.ulasan_id = u.id
        AND m.path = CONCAT('dummy/ulasan/video-', RIGHT(p.nomor_invoice, 4), '-tambahan.mp4')
  );


UPDATE pengaturan_toko
SET info_pembayaran = 'Transfer sesuai total bayar ke rekening toko, lalu unggah bukti pembayaran saat checkout. Pembayaran COD tetap tersedia.',
    bank_nama = 'BCA',
    bank_nomor_rekening = '1234567890',
    bank_atas_nama = 'SiTahu Premium'
WHERE id = 1;

COMMIT;

-- Cek singkat:
-- SELECT COUNT(*) AS total_produk_dummy FROM produk WHERE nama IN (...);
-- SELECT COUNT(*) AS total_ulasan_dummy FROM ulasan u JOIN pesanan p ON p.id = u.pesanan_id WHERE p.nomor_invoice LIKE 'DUMMY-SITAHU-%';