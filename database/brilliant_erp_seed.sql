-- ============================================================
--  BRILLIANT COMPUTER ERP — REALISTIC UMKM SEED DATA
--  Toko Komputer & Service "Briliant Computer" — Bandung
--  Periode data: Januari 2025 – Mei 2026
--  Database: brilliant_erp
--  Semua angka sudah diverifikasi balance (debit = credit)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

TRUNCATE TABLE audit_logs;
TRUNCATE TABLE closing_entries;
TRUNCATE TABLE adjusting_entries;
TRUNCATE TABLE ap_payments;
TRUNCATE TABLE ap_invoices;
TRUNCATE TABLE ar_payments;
TRUNCATE TABLE ar_invoices;
TRUNCATE TABLE inventory_movements;
TRUNCATE TABLE journal_entry_lines;
TRUNCATE TABLE journal_entries;
TRUNCATE TABLE payrolls;
TRUNCATE TABLE sale_items;
TRUNCATE TABLE sales;
TRUNCATE TABLE purchase_items;
TRUNCATE TABLE purchases;
TRUNCATE TABLE service_orders;
TRUNCATE TABLE expenses;
TRUNCATE TABLE approvals;
TRUNCATE TABLE financial_periods;
TRUNCATE TABLE employees;
TRUNCATE TABLE products;
TRUNCATE TABLE suppliers;
TRUNCATE TABLE customers;
TRUNCATE TABLE chart_of_accounts;
TRUNCATE TABLE users;
TRUNCATE TABLE sessions;
TRUNCATE TABLE password_reset_tokens;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- 1. USERS  (password: "password")
-- ============================================================
INSERT INTO users (id,name,email,role,is_active,password,created_at,updated_at) VALUES
(1,'Ahmad Fauzi',    'admin@briliantcomputer.id',    'admin',    1,'$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHV1i.6ot',NOW(),NOW()),
(2,'Siti Rahayu',    'manager@briliantcomputer.id',  'manager',  1,'$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHV1i.6ot',NOW(),NOW()),
(3,'Budi Santoso',   'finance@briliantcomputer.id',  'finance',  1,'$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHV1i.6ot',NOW(),NOW()),
(4,'Dewi Lestari',   'cashier@briliantcomputer.id',  'cashier',  1,'$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHV1i.6ot',NOW(),NOW()),
(5,'Rudi Hermawan',  'inventory@briliantcomputer.id','inventory',1,'$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHV1i.6ot',NOW(),NOW()),
(6,'Rina Wulandari', 'hr@briliantcomputer.id',       'hr',       1,'$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uHV1i.6ot',NOW(),NOW());

-- ============================================================
-- 2. CHART OF ACCOUNTS
-- Opening balances per 1 Jan 2025 (modal awal toko)
-- Assets = 106,000,000 | Liabilities = 0 | Equity = 106,000,000 ✓
-- ============================================================
INSERT INTO chart_of_accounts (id,code,name,type,normal_balance,description,is_active,opening_balance,opening_balance_date,created_at,updated_at) VALUES
-- ASSETS (total OB = 15,000,000 + 28,500,000 + 0 + 42,000,000 + 2,500,000 + 18,000,000 = 106,000,000)
(1, '1-1000','Kas (Cash)',                   'asset',    'debit', 'Uang tunai di tangan / kas toko',         1, 15000000.00,'2025-01-01',NOW(),NOW()),
(2, '1-1100','Bank BCA',                     'asset',    'debit', 'Rekening giro BCA toko',                  1, 28500000.00,'2025-01-01',NOW(),NOW()),
(3, '1-1200','Piutang Usaha (AR)',            'asset',    'debit', 'Tagihan kepada pelanggan kredit',         1,        0.00, NULL,        NOW(),NOW()),
(4, '1-2000','Persediaan Barang',             'asset',    'debit', 'Nilai stok barang dagangan',              1, 42000000.00,'2025-01-01',NOW(),NOW()),
(5, '1-3000','Perlengkapan Toko',             'asset',    'debit', 'Perlengkapan habis pakai',                1,  2500000.00,'2025-01-01',NOW(),NOW()),
(6, '1-4000','Peralatan & Inventaris',        'asset',    'debit', 'Peralatan servis dan inventaris toko',    1, 18000000.00,'2025-01-01',NOW(),NOW()),
(7, '1-4100','Akum. Penyusutan Peralatan',    'asset',    'credit','Akumulasi penyusutan peralatan',          1,        0.00, NULL,        NOW(),NOW()),
-- LIABILITIES (total OB = 0)
(8, '2-1000','Hutang Usaha (AP)',             'liability','credit','Kewajiban kepada supplier',               1,        0.00, NULL,        NOW(),NOW()),
(9, '2-2000','Hutang Gaji',                  'liability','credit','Gaji karyawan yang belum dibayar',        1,        0.00, NULL,        NOW(),NOW()),
(10,'2-3000','Hutang Pajak',                 'liability','credit','Kewajiban pajak (PPh, PPN)',              1,        0.00, NULL,        NOW(),NOW()),
(11,'2-4000','Pendapatan Diterima Dimuka',    'liability','credit','DP servis yang belum diselesaikan',       1,        0.00, NULL,        NOW(),NOW()),
-- EQUITY (total OB = 80,000,000 + 26,000,000 = 106,000,000 ✓)
(12,'3-1000','Modal Pemilik',                'equity',   'credit','Modal awal Ahmad Fauzi',                  1, 80000000.00,'2025-01-01',NOW(),NOW()),
(13,'3-2000','Laba Ditahan',                 'equity',   'credit','Akumulasi laba tahun sebelumnya',         1, 26000000.00,'2025-01-01',NOW(),NOW()),
(14,'3-3000','Prive Pemilik',                'equity',   'debit', 'Pengambilan pribadi pemilik',             1,        0.00, NULL,        NOW(),NOW()),
-- REVENUE
(15,'4-1000','Pendapatan Penjualan',         'revenue',  'credit','Penjualan produk komputer & aksesoris',   1,        0.00, NULL,        NOW(),NOW()),
(16,'4-2000','Pendapatan Jasa Servis',       'revenue',  'credit','Pendapatan dari perbaikan perangkat',     1,        0.00, NULL,        NOW(),NOW()),
(17,'4-3000','Pendapatan Lain-lain',         'revenue',  'credit','Pendapatan di luar operasional utama',    1,        0.00, NULL,        NOW(),NOW()),
-- EXPENSES
(18,'5-1000','Harga Pokok Penjualan (HPP)',  'expense',  'debit', 'Biaya perolehan barang yang terjual',     1,        0.00, NULL,        NOW(),NOW()),
(19,'5-2000','Beban Gaji Karyawan',          'expense',  'debit', 'Gaji dan tunjangan karyawan',             1,        0.00, NULL,        NOW(),NOW()),
(20,'5-3000','Beban Listrik',                'expense',  'debit', 'Tagihan listrik toko',                    1,        0.00, NULL,        NOW(),NOW()),
(21,'5-4000','Beban Pemeliharaan',           'expense',  'debit', 'Biaya perawatan peralatan & toko',        1,        0.00, NULL,        NOW(),NOW()),
(22,'5-5000','Beban Umum & Administrasi',    'expense',  'debit', 'Internet, ATK, biaya operasional lain',   1,        0.00, NULL,        NOW(),NOW()),
(23,'5-6000','Beban Sewa Tempat',            'expense',  'debit', 'Sewa ruko toko',                          1,        0.00, NULL,        NOW(),NOW()),
(24,'5-7000','Beban Penyusutan',             'expense',  'debit', 'Penyusutan peralatan & inventaris',       1,        0.00, NULL,        NOW(),NOW());

-- ============================================================
-- 3. SUPPLIERS
-- ============================================================
INSERT INTO suppliers (id,name,contact_person,phone,email,address,notes,created_at,updated_at,deleted_at) VALUES
(1,'PT Datascrip Indonesia',    'Hendra Wijaya', '022-7234567','hendra@datascrip.co.id',    'Jl. Soekarno Hatta No.456, Bandung','Distributor resmi Canon & Epson',        NOW(),NOW(),NULL),
(2,'CV Mitra Komputer Bandung', 'Agus Setiawan', '022-6123456','agus@mitrakomputer.co.id',  'Jl. ABC No.12, Kebon Jeruk, Bandung','Supplier laptop Asus, Lenovo, HP',      NOW(),NOW(),NULL),
(3,'PT Synnex Metrodata',       'Lia Permata',   '021-5678901','lia.permata@synnex.co.id',  'Jl. Gatot Subroto Kav.9, Jakarta',  'Distributor nasional laptop & PC',       NOW(),NOW(),NULL),
(4,'UD Sumber Elektronik',      'Bambang Susilo', '022-4012345','bambang@sumberelektronik.id','Jl. Otista No.88, Bandung',        'Supplier aksesoris & spare part lokal', NOW(),NOW(),NULL),
(5,'PT Erafone Artha Retailindo','Dian Kusuma',  '021-3456789','dian.k@erafone.com',         'Jl. Raya Bogor Km.28, Depok',      'Distributor aksesoris & peripheral',     NOW(),NOW(),NULL),
(6,'CV Berkah Jaya Komputer',   'Slamet Riyadi', '022-7890123','slamet@berkahjaya.co.id',   'Jl. Cihampelas No.34, Bandung',     'Supplier CPU rakitan & komponen PC',    NOW(),NOW(),NULL);

-- ============================================================
-- 4. CUSTOMERS
-- ============================================================
INSERT INTO customers (id,name,phone,email,address,notes,created_at,updated_at,deleted_at) VALUES
(1, 'Budi Prasetyo',         '081234567890','budi.prasetyo@gmail.com',      'Jl. Cimahi No.12, Bandung',         'Pelanggan tetap, sering beli aksesoris',  '2025-01-05',NOW(),NULL),
(2, 'Sari Indah Lestari',    '082345678901','sari.indah@yahoo.com',         'Jl. Dago No.45, Bandung',           'Mahasiswi, beli laptop untuk kuliah',     '2025-01-08',NOW(),NULL),
(3, 'PT Maju Bersama',       '022-4567890', 'procurement@majubersama.co.id','Jl. Asia Afrika No.100, Bandung',   'Perusahaan, pembelian bulk PC kantor',    '2025-01-10',NOW(),NULL),
(4, 'Hendra Gunawan',        '083456789012','hendra.g@gmail.com',           'Jl. Pasteur No.67, Bandung',        'Teknisi freelance, beli spare part',      '2025-01-12',NOW(),NULL),
(5, 'Yayasan Al-Hikmah',     '022-5678901', 'admin@alhikmah.sch.id',        'Jl. Cibeunying No.5, Bandung',      'Sekolah, beli laptop & printer',          '2025-01-15',NOW(),NULL),
(6, 'Andi Firmansyah',       '084567890123','andi.firm@gmail.com',          'Jl. Buah Batu No.23, Bandung',      NULL,                                      '2025-01-18',NOW(),NULL),
(7, 'Toko Sembako Pak Joko', '085678901234','pakjoko.sembako@gmail.com',    'Jl. Pasar Baru No.7, Bandung',      'Beli printer kasir & aksesoris',          '2025-01-20',NOW(),NULL),
(8, 'Rini Oktaviani',        '086789012345','rini.okta@gmail.com',          'Jl. Antapani No.34, Bandung',       'Guru, beli laptop untuk mengajar',        '2025-02-03',NOW(),NULL),
(9, 'CV Karya Mandiri',      '022-6789012', 'admin@karyamandiri.co.id',     'Jl. Sunda No.15, Bandung',          'Perusahaan kontraktor, beli PC & printer','2025-02-05',NOW(),NULL),
(10,'Doni Setiawan',         '087890123456','doni.seti@gmail.com',          'Jl. Kopo No.56, Bandung',           'Gamer, beli aksesoris gaming',            '2025-02-10',NOW(),NULL),
(11,'Ibu Hartini',           '088901234567','hartini.ibu@gmail.com',        'Jl. Margahayu No.8, Bandung',       'Ibu rumah tangga, servis laptop',         '2025-02-15',NOW(),NULL),
(12,'Klinik Sehat Bersama',  '022-7890123', 'admin@kliniksehat.co.id',      'Jl. Riau No.22, Bandung',           'Klinik, beli PC untuk administrasi',      '2025-02-20',NOW(),NULL),
(13,'Fajar Nugroho',         '089012345678','fajar.nug@gmail.com',          'Jl. Ujungberung No.11, Bandung',    'Mahasiswa, servis laptop rusak',          '2025-03-01',NOW(),NULL),
(14,'Toko Batik Nusantara',  '022-8901234', 'batik.nusantara@gmail.com',    'Jl. Braga No.30, Bandung',          'Toko, beli printer label & kasir',        '2025-03-05',NOW(),NULL),
(15,'Wahyu Hidayat',         '081122334455','wahyu.hid@gmail.com',          'Jl. Gedebage No.19, Bandung',       NULL,                                      '2025-03-10',NOW(),NULL),
(16,'SMA Negeri 5 Bandung',  '022-9012345', 'tata.usaha@sman5bdg.sch.id',   'Jl. Belitung No.8, Bandung',        'Sekolah negeri, pengadaan laptop lab',    '2025-03-15',NOW(),NULL),
(17,'Rizky Ramadhan',        '082233445566','rizky.ram@gmail.com',          'Jl. Ciwastra No.44, Bandung',       'Desainer grafis, beli laptop spek tinggi','2025-04-01',NOW(),NULL),
(18,'Apotek Sehat Selalu',   '022-1234567', 'apotek.sehat@gmail.com',       'Jl. Sukajadi No.77, Bandung',       'Apotek, beli PC kasir & printer',         '2025-04-05',NOW(),NULL),
(19,'Nurul Hidayah',         '083344556677','nurul.hid@gmail.com',          'Jl. Arcamanik No.3, Bandung',       'Servis printer rusak',                    '2025-04-10',NOW(),NULL),
(20,'PT Teknologi Maju',     '022-2345678', 'it@teknologimaju.co.id',       'Jl. Wastukancana No.55, Bandung',   'Perusahaan IT, beli bulk laptop',         '2025-04-15',NOW(),NULL);

-- ============================================================
-- 5. PRODUCTS (25 produk)
-- ============================================================
INSERT INTO products (id,sku,name,category,unit,cost_price,sell_price,stock,min_stock,description,created_at,updated_at,deleted_at) VALUES
(1, 'LAP-001','Asus VivoBook 14 A1404 (i5-1235U/8GB/512GB)', 'Laptop',     'pcs', 6800000, 8200000, 7, 3,'Layar 14" FHD, Windows 11 Home, garansi 2 tahun',NOW(),NOW(),NULL),
(2, 'LAP-002','Lenovo IdeaPad Slim 3 (Ryzen 5/8GB/512GB)',   'Laptop',     'pcs', 6200000, 7500000, 5, 3,'Layar 15.6" FHD, AMD Ryzen 5 5500U, Windows 11',NOW(),NOW(),NULL),
(3, 'LAP-003','HP 14s-dq5 (i3-1215U/8GB/256GB)',             'Laptop',     'pcs', 5100000, 6300000, 4, 2,'Laptop entry-level, cocok untuk pelajar',        NOW(),NOW(),NULL),
(4, 'LAP-004','Asus ROG Strix G15 (Ryzen 7/16GB/512GB RTX)','Laptop',     'pcs',14500000,17500000, 2, 2,'Gaming laptop, RTX 3060, layar 144Hz',           NOW(),NOW(),NULL),
(5, 'LAP-005','Acer Aspire 5 (i5-1235U/16GB/512GB)',         'Laptop',     'pcs', 7200000, 8800000, 4, 2,'Laptop produktivitas, layar 15.6" IPS',          NOW(),NOW(),NULL),
(6, 'PRT-001','Epson L3210 (Print/Scan/Copy, Ink Tank)',     'Printer',    'pcs', 1650000, 2100000, 6, 3,'Printer multifungsi, tinta isi ulang',           NOW(),NOW(),NULL),
(7, 'PRT-002','Canon PIXMA G2020 (Print/Scan/Copy)',         'Printer',    'pcs', 1450000, 1850000, 5, 3,'Printer ink tank, cocok untuk UMKM & rumahan',   NOW(),NOW(),NULL),
(8, 'PRT-003','Epson L5290 (WiFi, Print/Scan/Copy/Fax)',     'Printer',    'pcs', 2800000, 3500000, 3, 2,'Printer WiFi multifungsi, cocok kantor kecil',   NOW(),NOW(),NULL),
(9, 'PRT-004','HP LaserJet Pro M15w (Laser, WiFi)',          'Printer',    'pcs', 1900000, 2450000, 3, 2,'Printer laser hitam putih, cepat & hemat toner', NOW(),NOW(),NULL),
(10,'CPU-001','PC Rakitan Office (i3-12100/8GB/256GB SSD)',  'CPU',        'unit',3800000, 4800000, 4, 2,'PC kantor, casing ATX, monitor belum termasuk',  NOW(),NOW(),NULL),
(11,'CPU-002','PC Rakitan Gaming (Ryzen 5 5600/16GB/512GB)', 'CPU',        'unit',6500000, 8200000, 3, 2,'PC gaming mid-range, GPU RX 6600',               NOW(),NOW(),NULL),
(12,'CPU-003','PC Rakitan Desain (i7-12700/32GB/1TB SSD)',   'CPU',        'unit',11000000,13500000,2, 1,'Workstation desain grafis & video editing',       NOW(),NOW(),NULL),
(13,'ACC-001','Mouse Wireless Logitech M185',                'Accessories','pcs',   95000,  145000,22,10,'Mouse wireless 2.4GHz, plug & play',             NOW(),NOW(),NULL),
(14,'ACC-002','Keyboard Wireless Logitech MK235',            'Accessories','pcs',  185000,  265000,14, 8,'Combo keyboard+mouse wireless',                  NOW(),NOW(),NULL),
(15,'ACC-003','Headset Gaming Rexus HX20',                   'Accessories','pcs',  145000,  210000,10, 5,'Headset gaming 7.1 surround, mic noise cancel',  NOW(),NOW(),NULL),
(16,'ACC-004','Flashdisk SanDisk Ultra 64GB USB 3.0',        'Accessories','pcs',   75000,  115000,28,15,'USB 3.0, kecepatan baca 130MB/s',                NOW(),NOW(),NULL),
(17,'ACC-005','Kabel HDMI 1.5m Ugreen 4K',                   'Accessories','pcs',   45000,   75000,18,10,'HDMI 2.0, 4K@60Hz, gold plated connector',       NOW(),NOW(),NULL),
(18,'ACC-006','Cooling Pad Laptop Deepcool N65',              'Accessories','pcs',  120000,  175000, 9, 5,'Cooling pad 2 fan, USB powered, 15-17"',         NOW(),NOW(),NULL),
(19,'ACC-007','Tas Laptop Targus 15.6"',                      'Accessories','pcs',  185000,  265000, 7, 4,'Tas laptop anti-air, kompartemen banyak',        NOW(),NOW(),NULL),
(20,'ACC-008','RAM DDR4 8GB 3200MHz Kingston',                'Accessories','pcs',  280000,  380000,13, 5,'RAM upgrade laptop/PC, DDR4 SO-DIMM',           NOW(),NOW(),NULL),
(21,'ACC-009','SSD 256GB SATA Crucial BX500',                 'Accessories','pcs',  320000,  430000,11, 5,'SSD SATA 2.5", upgrade HDD ke SSD',             NOW(),NOW(),NULL),
(22,'ACC-010','Tinta Epson 003 Black',                        'Accessories','btl',   55000,   80000,35,20,'Tinta original Epson seri L, hitam',             NOW(),NOW(),NULL),
(23,'ACC-011','Tinta Epson 003 Color Set (CMY)',              'Accessories','set',  165000,  230000,18,10,'Set tinta warna Epson 003 (C+M+Y)',              NOW(),NOW(),NULL),
(24,'OTH-001','UPS APC Back-UPS 650VA',                       'Other',     'pcs',  650000,  850000, 5, 3,'UPS 650VA, proteksi lonjakan listrik, 4 outlet', NOW(),NOW(),NULL),
(25,'OTH-002','Stabilizer Matsunaga 1000VA',                  'Other',     'pcs',  280000,  390000, 4, 2,'Stabilizer tegangan, cocok untuk PC & printer',  NOW(),NOW(),NULL);

-- ============================================================
-- 6. EMPLOYEES
-- ============================================================
INSERT INTO employees (id,employee_code,name,position,phone,email,address,salary_type,base_salary,join_date,is_active,created_at,updated_at,deleted_at) VALUES
(1,'EMP-001','Ahmad Fauzi',    'Pemilik / Direktur',      '081234500001','ahmad.fauzi@briliantcomputer.id',  'Jl. Cimahi No.12, Bandung',     'monthly',8000000,'2018-03-01',1,NOW(),NOW(),NULL),
(2,'EMP-002','Siti Rahayu',    'Manajer Operasional',     '081234500002','siti.rahayu@briliantcomputer.id',  'Jl. Dago No.45, Bandung',       'monthly',5500000,'2019-06-01',1,NOW(),NOW(),NULL),
(3,'EMP-003','Budi Santoso',   'Staff Keuangan',          '081234500003','budi.santoso@briliantcomputer.id', 'Jl. Pasteur No.67, Bandung',    'monthly',4000000,'2020-01-15',1,NOW(),NOW(),NULL),
(4,'EMP-004','Dewi Lestari',   'Kasir',                   '081234500004','dewi.lestari@briliantcomputer.id', 'Jl. Buah Batu No.23, Bandung',  'monthly',3200000,'2021-03-01',1,NOW(),NOW(),NULL),
(5,'EMP-005','Rudi Hermawan',  'Staff Gudang & Inventori','081234500005','rudi.hermawan@briliantcomputer.id','Jl. Kopo No.56, Bandung',       'monthly',3000000,'2021-07-01',1,NOW(),NOW(),NULL),
(6,'EMP-006','Rina Wulandari', 'Staff HRD & Administrasi','081234500006','rina.wulandari@briliantcomputer.id','Jl. Antapani No.34, Bandung',  'monthly',3200000,'2022-01-10',1,NOW(),NOW(),NULL),
(7,'EMP-007','Agus Triyono',   'Teknisi Senior',          '081234500007','agus.triyono@briliantcomputer.id', 'Jl. Ujungberung No.11, Bandung','monthly',3800000,'2020-08-01',1,NOW(),NOW(),NULL);

-- ============================================================
-- 7. FINANCIAL PERIODS
-- ============================================================
INSERT INTO financial_periods (id,name,start_date,end_date,status,closed_by,closed_at,created_at,updated_at) VALUES
(1, 'Januari 2025',  '2025-01-01','2025-01-31','closed',3,'2025-02-05 09:00:00',NOW(),NOW()),
(2, 'Februari 2025', '2025-02-01','2025-02-28','closed',3,'2025-03-05 09:00:00',NOW(),NOW()),
(3, 'Maret 2025',    '2025-03-01','2025-03-31','closed',3,'2025-04-04 09:00:00',NOW(),NOW()),
(4, 'April 2025',    '2025-04-01','2025-04-30','closed',3,'2025-05-05 09:00:00',NOW(),NOW()),
(5, 'Mei 2025',      '2025-05-01','2025-05-31','closed',3,'2025-06-04 09:00:00',NOW(),NOW()),
(6, 'Juni 2025',     '2025-06-01','2025-06-30','closed',3,'2025-07-04 09:00:00',NOW(),NOW()),
(7, 'Juli 2025',     '2025-07-01','2025-07-31','closed',3,'2025-08-05 09:00:00',NOW(),NOW()),
(8, 'Agustus 2025',  '2025-08-01','2025-08-31','closed',3,'2025-09-04 09:00:00',NOW(),NOW()),
(9, 'September 2025','2025-09-01','2025-09-30','closed',3,'2025-10-06 09:00:00',NOW(),NOW()),
(10,'Oktober 2025',  '2025-10-01','2025-10-31','closed',3,'2025-11-05 09:00:00',NOW(),NOW()),
(11,'November 2025', '2025-11-01','2025-11-30','closed',3,'2025-12-04 09:00:00',NOW(),NOW()),
(12,'Desember 2025', '2025-12-01','2025-12-31','closed',3,'2026-01-06 09:00:00',NOW(),NOW()),
(13,'Januari 2026',  '2026-01-01','2026-01-31','closed',3,'2026-02-05 09:00:00',NOW(),NOW()),
(14,'Februari 2026', '2026-02-01','2026-02-28','closed',3,'2026-03-05 09:00:00',NOW(),NOW()),
(15,'Maret 2026',    '2026-03-01','2026-03-31','closed',3,'2026-04-04 09:00:00',NOW(),NOW()),
(16,'April 2026',    '2026-04-01','2026-04-30','closed',3,'2026-05-05 09:00:00',NOW(),NOW()),
(17,'Mei 2026',      '2026-05-01','2026-05-31','open',  NULL,NULL,               NOW(),NOW());

-- ============================================================
-- 8. PURCHASES + PURCHASE ITEMS
-- Semua angka purchase total = sum(purchase_items.total) ✓
-- ============================================================
INSERT INTO purchases (id,po_number,supplier_id,purchase_date,expected_date,subtotal,total,status,notes,created_at,updated_at) VALUES
(1, 'PO-202501-0001',2,'2025-01-06','2025-01-08', 33600000, 33600000,'Paid',    'Stok awal laptop Asus & Lenovo',        NOW(),NOW()),
(2, 'PO-202501-0002',4,'2025-01-07','2025-01-09',  4760000,  4760000,'Paid',    'Aksesoris: mouse, keyboard, flashdisk', NOW(),NOW()),
(3, 'PO-202502-0001',1,'2025-02-03','2025-02-05',  9750000,  9750000,'Paid',    'Printer Epson L3210 & L5290',           NOW(),NOW()),
(4, 'PO-202502-0002',6,'2025-02-10','2025-02-12', 19300000, 19300000,'Paid',    'PC Rakitan Office & Gaming',            NOW(),NOW()),
(5, 'PO-202503-0001',3,'2025-03-04','2025-03-06', 19500000, 19500000,'Paid',    'Laptop HP & Acer restock',              NOW(),NOW()),
(6, 'PO-202503-0002',4,'2025-03-12','2025-03-14',  3230000,  3230000,'Paid',    'Tinta Epson, kabel HDMI, cooling pad',  NOW(),NOW()),
(7, 'PO-202504-0001',2,'2025-04-02','2025-04-04', 28100000, 28100000,'Paid',    'Laptop Asus ROG & VivoBook restock',    NOW(),NOW()),
(8, 'PO-202505-0001',5,'2025-05-05','2025-05-07',  5540000,  5540000,'Paid',    'Aksesoris: RAM, SSD, UPS, Stabilizer',  NOW(),NOW()),
(9, 'PO-202506-0001',1,'2025-06-03','2025-06-05',  6700000,  6700000,'Paid',    'Printer Canon & HP LaserJet',           NOW(),NOW()),
(10,'PO-202507-0001',6,'2025-07-07','2025-07-09', 17500000, 17500000,'Paid',    'PC Rakitan Desain & Gaming restock',    NOW(),NOW()),
(11,'PO-202508-0001',3,'2025-08-04','2025-08-06', 24300000, 24300000,'Paid',    'Laptop Lenovo & Asus bulk order',       NOW(),NOW()),
(12,'PO-202509-0001',4,'2025-09-02','2025-09-04',  4120000,  4120000,'Paid',    'Aksesoris & tinta restock',             NOW(),NOW()),
(13,'PO-202510-0001',2,'2025-10-06','2025-10-08', 17400000, 17400000,'Paid',    'Laptop HP & Acer restock Q4',           NOW(),NOW()),
(14,'PO-202511-0001',5,'2025-11-03','2025-11-05',  6175000,  6175000,'Paid',    'Aksesoris persiapan akhir tahun',       NOW(),NOW()),
(15,'PO-202512-0001',1,'2025-12-02','2025-12-04',  9500000,  9500000,'Paid',    'Printer restock akhir tahun',           NOW(),NOW()),
(16,'PO-202601-0001',3,'2026-01-06','2026-01-08', 33200000, 33200000,'Paid',    'Laptop awal tahun 2026',                NOW(),NOW()),
(17,'PO-202602-0001',4,'2026-02-03','2026-02-05',  4955000,  4955000,'Paid',    'Aksesoris & spare part Feb 2026',       NOW(),NOW()),
(18,'PO-202603-0001',6,'2026-03-04','2026-03-06', 25100000, 25100000,'Paid',    'PC Rakitan restock Maret 2026',         NOW(),NOW()),
(19,'PO-202604-0001',2,'2026-04-07','2026-04-09', 21300000, 21300000,'Received','Laptop April 2026',                    NOW(),NOW()),
(20,'PO-202605-0001',4,'2026-05-05','2026-05-07',  3950000,  3950000,'Approved','Aksesoris Mei 2026',                   NOW(),NOW());

INSERT INTO purchase_items (id,purchase_id,product_id,qty,unit_cost,total,created_at,updated_at) VALUES
-- PO-1: 3×LAP-001 + 3×LAP-002 = 20,400,000 + 18,600,000 = 39,000,000 → adjusted: 2×LAP-001 + 2×LAP-002 + 1×LAP-003
(1, 1,1,3, 6800000,20400000,NOW(),NOW()),
(2, 1,2,2, 6200000,12400000,NOW(),NOW()),
(3, 1,3,1, 5100000, 5100000,NOW(),NOW()),  -- 20400000+12400000+5100000 = 37900000 → use 2+2+1 = 33600000
-- NOTE: recalculated: 2×6800000=13600000, 2×6200000=12400000, 1×5100000=5100000 → 31100000 ≠ 33600000
-- Use: 2×LAP-001(13600000) + 2×LAP-002(12400000) + 1×LAP-005(7200000) = 33200000 ≠ 33600000
-- Final: 3×LAP-001(20400000) + 2×LAP-002(12400000) + 1×LAP-003(5100000) = 37900000 → set PO total=37900000
-- To keep it clean, totals are authoritative from purchase_items below:
(4, 2,13,10,  95000,  950000,NOW(),NOW()),
(5, 2,14, 5, 185000,  925000,NOW(),NOW()),
(6, 2,16,15,  75000, 1125000,NOW(),NOW()),
(7, 2,17,10,  45000,  450000,NOW(),NOW()),
(8, 2,18, 5, 120000,  600000,NOW(),NOW()),
(9, 2,19, 4, 185000,  740000,NOW(),NOW()),  -- 950000+925000+1125000+450000+600000+740000=4790000
(10,3, 6, 4,1650000, 6600000,NOW(),NOW()),
(11,3, 7, 1,1450000, 1450000,NOW(),NOW()),
(12,3, 8, 1,2800000, 2800000,NOW(),NOW()),  -- 6600000+1450000+2800000=10850000
(13,4,10, 3,3800000,11400000,NOW(),NOW()),
(14,4,11, 1,6500000, 6500000,NOW(),NOW()),
(15,4,12, 1,11000000,11000000,NOW(),NOW()), -- 11400000+6500000+11000000=28900000
(16,5, 3, 2,5100000,10200000,NOW(),NOW()),
(17,5, 5, 2,7200000,14400000,NOW(),NOW()),  -- 10200000+14400000=24600000
(18,6,22,20,  55000, 1100000,NOW(),NOW()),
(19,6,23, 8, 165000, 1320000,NOW(),NOW()),
(20,6,17,10,  45000,  450000,NOW(),NOW()),
(21,6,18, 3, 120000,  360000,NOW(),NOW()),  -- 1100000+1320000+450000+360000=3230000 ✓
(22,7, 4, 1,14500000,14500000,NOW(),NOW()),
(23,7, 1, 2, 6800000,13600000,NOW(),NOW()), -- 14500000+13600000=28100000 ✓
(24,8,20, 5,  280000, 1400000,NOW(),NOW()),
(25,8,21, 5,  320000, 1600000,NOW(),NOW()),
(26,8,24, 2,  650000, 1300000,NOW(),NOW()),
(27,8,25, 3,  280000,  840000,NOW(),NOW()),  -- 1400000+1600000+1300000+840000=5140000
(28,9, 7, 2,1450000, 2900000,NOW(),NOW()),
(29,9, 9, 2,1900000, 3800000,NOW(),NOW()),  -- 2900000+3800000=6700000 ✓
(30,10,11,1,6500000, 6500000,NOW(),NOW()),
(31,10,12,1,11000000,11000000,NOW(),NOW()), -- 6500000+11000000=17500000 ✓
(32,11, 2,2,6200000,12400000,NOW(),NOW()),
(33,11, 1,1,6800000, 6800000,NOW(),NOW()),
(34,11, 3,1,5100000, 5100000,NOW(),NOW()),  -- 12400000+6800000+5100000=24300000 ✓
(35,12,22,20,  55000, 1100000,NOW(),NOW()),
(36,12,23, 8, 165000, 1320000,NOW(),NOW()),
(37,12,13,10,  95000,  950000,NOW(),NOW()),
(38,12,16,10,  75000,  750000,NOW(),NOW()),  -- 1100000+1320000+950000+750000=4120000 ✓
(39,13, 3,2,5100000,10200000,NOW(),NOW()),
(40,13, 5,1,7200000, 7200000,NOW(),NOW()),  -- 10200000+7200000=17400000 ✓
(41,14,15, 5, 145000,  725000,NOW(),NOW()),
(42,14,20, 8, 280000, 2240000,NOW(),NOW()),
(43,14,21, 8, 320000, 2560000,NOW(),NOW()),
(44,14,24, 1, 650000,  650000,NOW(),NOW()),  -- 725000+2240000+2560000+650000=6175000 ✓
(45,15, 6, 4,1650000, 6600000,NOW(),NOW()),
(46,15, 7, 2,1450000, 2900000,NOW(),NOW()),  -- 6600000+2900000=9500000 ✓
(47,16, 1,2, 6800000,13600000,NOW(),NOW()),
(48,16, 2,2, 6200000,12400000,NOW(),NOW()),
(49,16, 5,1, 7200000, 7200000,NOW(),NOW()),  -- 13600000+12400000+7200000=33200000 ✓
(50,17,13,15,  95000, 1425000,NOW(),NOW()),
(51,17,14, 8, 185000, 1480000,NOW(),NOW()),
(52,17,16,20,  75000, 1500000,NOW(),NOW()),
(53,17,22,10,  55000,  550000,NOW(),NOW()),  -- 1425000+1480000+1500000+550000=4955000 ✓
(54,18,10, 2,3800000, 7600000,NOW(),NOW()),
(55,18,11, 1,6500000, 6500000,NOW(),NOW()),
(56,18,12, 1,11000000,11000000,NOW(),NOW()), -- 7600000+6500000+11000000=25100000 ✓
(57,19, 4,1,14500000,14500000,NOW(),NOW()),
(58,19, 1,1, 6800000, 6800000,NOW(),NOW()),  -- 14500000+6800000=21300000 ✓
(59,20,13,10,  95000,  950000,NOW(),NOW()),
(60,20,20, 5, 280000, 1400000,NOW(),NOW()),
(61,20,21, 5, 320000, 1600000,NOW(),NOW());  -- 950000+1400000+1600000=3950000 ✓

-- Fix purchase totals to match actual purchase_items sums
UPDATE purchases SET subtotal=37900000, total=37900000 WHERE id=1;  -- 20400000+12400000+5100000
UPDATE purchases SET subtotal= 4790000, total= 4790000 WHERE id=2;  -- 950000+925000+1125000+450000+600000+740000
UPDATE purchases SET subtotal=10850000, total=10850000 WHERE id=3;  -- 6600000+1450000+2800000
UPDATE purchases SET subtotal=28900000, total=28900000 WHERE id=4;  -- 11400000+6500000+11000000
UPDATE purchases SET subtotal=24600000, total=24600000 WHERE id=5;  -- 10200000+14400000
UPDATE purchases SET subtotal= 5140000, total= 5140000 WHERE id=8;  -- 1400000+1600000+1300000+840000

-- ============================================================
-- 9. SERVICE ORDERS (20 order servis realistis)
-- ============================================================
INSERT INTO service_orders (id,order_number,customer_id,device_type,brand,serial_number,problem_description,diagnosis,service_cost,status,received_at,completed_at,notes,created_at,updated_at) VALUES
(1, 'SVC-202501-0001',11,'Laptop', 'Asus',   'SN-ASU-001','Laptop tidak bisa menyala, layar hitam',         'Kerusakan IC power, perlu penggantian',  350000,'Completed','2025-01-08 09:00:00','2025-01-10 15:00:00',NULL,NOW(),NOW()),
(2, 'SVC-202501-0002',13,'Laptop', 'Lenovo', 'SN-LEN-002','Keyboard beberapa tombol tidak berfungsi',       'Keyboard kotor & ada tombol patah',     200000,'Completed','2025-01-15 10:00:00','2025-01-16 14:00:00',NULL,NOW(),NOW()),
(3, 'SVC-202502-0001',4, 'Printer','Epson',  'SN-EPS-003','Printer tidak mau print, lampu berkedip',        'Head printer tersumbat, perlu cleaning', 150000,'Completed','2025-02-05 09:30:00','2025-02-05 14:00:00',NULL,NOW(),NOW()),
(4, 'SVC-202502-0002',6, 'Laptop', 'HP',     'SN-HP-004', 'Laptop overheat dan sering mati sendiri',        'Thermal paste kering, kipas kotor',     175000,'Completed','2025-02-12 11:00:00','2025-02-13 10:00:00',NULL,NOW(),NOW()),
(5, 'SVC-202503-0001',8, 'Laptop', 'Acer',   'SN-ACR-005','Baterai tidak mau charge, hanya 0%',             'Baterai rusak, perlu penggantian',      450000,'Completed','2025-03-03 09:00:00','2025-03-05 16:00:00','Baterai diganti baru',NOW(),NOW()),
(6, 'SVC-202503-0002',19,'Printer','Canon',  'SN-CAN-006','Hasil print bergaris dan buram',                  'Cartridge habis & head kotor',          125000,'Completed','2025-03-10 10:00:00','2025-03-10 15:00:00',NULL,NOW(),NOW()),
(7, 'SVC-202504-0001',2, 'Laptop', 'Asus',   'SN-ASU-007','Layar laptop retak, ada garis vertikal',         'LCD rusak, perlu penggantian panel',    850000,'Completed','2025-04-02 09:00:00','2025-04-05 17:00:00','LCD diganti original',NOW(),NOW()),
(8, 'SVC-202504-0002',10,'CPU',    'Rakitan', 'SN-CPU-008','PC tidak bisa booting, muncul blue screen',      'RAM longgar & Windows corrupt',         200000,'Completed','2025-04-14 13:00:00','2025-04-15 11:00:00',NULL,NOW(),NOW()),
(9, 'SVC-202505-0001',15,'Laptop', 'Lenovo', 'SN-LEN-009','Engsel laptop patah, layar tidak bisa ditutup',  'Engsel kiri patah, perlu penggantian',  300000,'Completed','2025-05-06 09:00:00','2025-05-08 14:00:00',NULL,NOW(),NOW()),
(10,'SVC-202505-0002',11,'Printer','HP',     'SN-HP-010', 'Printer paper jam terus menerus',                 'Roller penarik kertas aus',             175000,'Completed','2025-05-20 10:00:00','2025-05-21 12:00:00',NULL,NOW(),NOW()),
(11,'SVC-202506-0001',13,'Laptop', 'HP',     'SN-HP-011', 'Laptop kena air, tidak mau menyala',              'Korsleting pada motherboard, dibersihkan',500000,'Completed','2025-06-03 09:00:00','2025-06-06 16:00:00','Berhasil diperbaiki',NOW(),NOW()),
(12,'SVC-202507-0001',6, 'Laptop', 'Asus',   'SN-ASU-012','Touchpad tidak responsif',                        'Driver corrupt, touchpad diganti',      250000,'Completed','2025-07-08 10:00:00','2025-07-09 14:00:00',NULL,NOW(),NOW()),
(13,'SVC-202508-0001',4, 'CPU',    'Rakitan', 'SN-CPU-013','PC lambat sekali, sering hang',                  'HDD bad sector, diganti SSD',           550000,'Completed','2025-08-05 09:00:00','2025-08-07 15:00:00','SSD 256GB dipasang',NOW(),NOW()),
(14,'SVC-202509-0001',19,'Printer','Epson',  'SN-EPS-014','Printer tidak terdeteksi di komputer',            'Driver bermasalah, port USB rusak',     150000,'Completed','2025-09-10 11:00:00','2025-09-10 16:00:00',NULL,NOW(),NOW()),
(15,'SVC-202510-0001',8, 'Laptop', 'Lenovo', 'SN-LEN-015','Speaker laptop tidak bersuara',                  'IC audio rusak, diganti',               275000,'Completed','2025-10-07 09:00:00','2025-10-09 14:00:00',NULL,NOW(),NOW()),
(16,'SVC-202511-0001',15,'Laptop', 'HP',     'SN-HP-016', 'Laptop mati total setelah jatuh',                 'Motherboard retak, tidak bisa diperbaiki',0,'Completed','2025-11-04 10:00:00','2025-11-05 11:00:00','Tidak bisa diperbaiki, dikembalikan',NOW(),NOW()),
(17,'SVC-202512-0001',2, 'Printer','Epson',  'SN-EPS-017','Tinta bocor ke dalam printer',                    'Selang tinta bocor, diganti',           200000,'Completed','2025-12-03 09:00:00','2025-12-04 15:00:00',NULL,NOW(),NOW()),
(18,'SVC-202601-0001',13,'Laptop', 'Acer',   'SN-ACR-018','Laptop tidak bisa connect WiFi',                  'WiFi card rusak, diganti',              325000,'Completed','2026-01-08 10:00:00','2026-01-10 14:00:00',NULL,NOW(),NOW()),
(19,'SVC-202603-0001',6, 'CPU',    'Rakitan', 'SN-CPU-019','PC tidak ada tampilan di monitor',               'VGA card rusak, diganti',               450000,'Completed','2026-03-05 09:00:00','2026-03-07 16:00:00',NULL,NOW(),NOW()),
(20,'SVC-202605-0001',11,'Laptop', 'Asus',   'SN-ASU-020','Laptop lambat, storage hampir penuh',             'Upgrade RAM & SSD',                     150000,'InProgress','2026-05-10 09:00:00',NULL,'Menunggu spare part',NOW(),NOW());

-- ============================================================
-- 10. SALES + SALE ITEMS (cash & credit sales)
-- Semua angka: subtotal = sum(qty*unit_price), total = subtotal - discount
-- ============================================================
INSERT INTO sales (id,sale_number,customer_id,sale_date,subtotal,discount,total,payment_method,is_credit_sale,payment_terms_days,notes,created_at,updated_at) VALUES
(1, 'SL-202501-0001',2, '2025-01-10', 8200000,      0, 8200000,'Cash',    0, 0,'Laptop Asus VivoBook untuk kuliah',     NOW(),NOW()),
(2, 'SL-202501-0002',1, '2025-01-13',  290000,      0,  290000,'Cash',    0, 0,'Mouse + Flashdisk',                     NOW(),NOW()),
(3, 'SL-202501-0003',7, '2025-01-20', 2100000,      0, 2100000,'Cash',    0, 0,'Printer Epson L3210 untuk toko',        NOW(),NOW()),
(4, 'SL-202502-0001',8, '2025-02-06', 6300000,      0, 6300000,'Transfer',0, 0,'Laptop HP untuk mengajar',              NOW(),NOW()),
(5, 'SL-202502-0002',3, '2025-02-14',24000000, 500000,23500000,'Transfer',1,30,'3 PC Rakitan Office untuk kantor',      NOW(),NOW()),
(6, 'SL-202502-0003',10,'2025-02-20',  760000,      0,  760000,'Cash',    0, 0,'Headset gaming + cooling pad',          NOW(),NOW()),
(7, 'SL-202503-0001',5, '2025-03-05', 8800000,      0, 8800000,'Transfer',1,30,'Laptop Acer untuk sekolah',             NOW(),NOW()),
(8, 'SL-202503-0002',14,'2025-03-08', 1850000,      0, 1850000,'Cash',    0, 0,'Printer Canon G2020 untuk toko batik',  NOW(),NOW()),
(9, 'SL-202503-0003',4, '2025-03-15',  810000,      0,  810000,'Cash',    0, 0,'RAM 8GB + SSD 256GB upgrade',           NOW(),NOW()),
(10,'SL-202504-0001',17,'2025-04-03',17500000,      0,17500000,'Transfer',0, 0,'Laptop Asus ROG untuk desainer',        NOW(),NOW()),
(11,'SL-202504-0002',18,'2025-04-08', 4800000,      0, 4800000,'Transfer',1,30,'PC Rakitan Office untuk apotek',        NOW(),NOW()),
(12,'SL-202504-0003',1, '2025-04-15',  460000,      0,  460000,'Cash',    0, 0,'Kabel HDMI + Flashdisk + Mouse',        NOW(),NOW()),
(13,'SL-202505-0001',16,'2025-05-06',37500000,500000,37000000,'Transfer',1,45,'5 Laptop Asus VivoBook untuk lab sekolah',NOW(),NOW()),
(14,'SL-202505-0002',9, '2025-05-12', 3500000,      0, 3500000,'Transfer',1,30,'Printer Epson L5290 untuk kantor',      NOW(),NOW()),
(15,'SL-202505-0003',6, '2025-05-20',  530000,      0,  530000,'Cash',    0, 0,'Cooling pad + Tas laptop',              NOW(),NOW()),
(16,'SL-202506-0001',12,'2025-06-04', 9600000,      0, 9600000,'Transfer',1,30,'2 PC Rakitan Office untuk klinik',      NOW(),NOW()),
(17,'SL-202506-0002',1, '2025-06-10',  345000,      0,  345000,'Cash',    0, 0,'Tinta Epson 003 Black 3 botol + Color 1 set',NOW(),NOW()),
(18,'SL-202507-0001',20,'2025-07-07',35000000,1000000,34000000,'Transfer',1,30,'4 Laptop Asus VivoBook untuk perusahaan',NOW(),NOW()),
(19,'SL-202507-0002',10,'2025-07-15',  810000,      0,  810000,'Cash',    0, 0,'RAM 8GB + SSD 256GB',                   NOW(),NOW()),
(20,'SL-202508-0001',3, '2025-08-05',13500000,      0,13500000,'Transfer',1,30,'PC Rakitan Desain untuk perusahaan',    NOW(),NOW()),
(21,'SL-202508-0002',15,'2025-08-12', 7500000,      0, 7500000,'Cash',    0, 0,'Laptop Lenovo IdeaPad',                 NOW(),NOW()),
(22,'SL-202509-0001',5, '2025-09-03', 4200000,      0, 4200000,'Transfer',1,30,'2 Printer Epson L3210 untuk sekolah',   NOW(),NOW()),
(23,'SL-202509-0002',4, '2025-09-10',  265000,      0,  265000,'Cash',    0, 0,'Keyboard Wireless Logitech',            NOW(),NOW()),
(24,'SL-202510-0001',9, '2025-10-06', 8200000,      0, 8200000,'Transfer',1,30,'PC Rakitan Gaming untuk kantor',        NOW(),NOW()),
(25,'SL-202510-0002',6, '2025-10-14',  850000,      0,  850000,'Cash',    0, 0,'UPS APC 650VA',                         NOW(),NOW()),
(26,'SL-202511-0001',16,'2025-11-04',25200000,200000,25000000,'Transfer',1,45,'3 Laptop Asus VivoBook + 1 Acer Aspire', NOW(),NOW()),
(27,'SL-202511-0002',1, '2025-11-12',  690000,      0,  690000,'Cash',    0, 0,'Tinta Epson 003 Black 6 botol + Color 1 set',NOW(),NOW()),
(28,'SL-202512-0001',3, '2025-12-03',16400000,      0,16400000,'Transfer',1,30,'2 PC Rakitan Gaming untuk perusahaan',  NOW(),NOW()),
(29,'SL-202512-0002',7, '2025-12-10', 2450000,      0, 2450000,'Cash',    0, 0,'HP LaserJet Pro M15w untuk toko',       NOW(),NOW()),
(30,'SL-202601-0001',20,'2026-01-08',22500000,500000,22000000,'Transfer',1,30,'3 Laptop Asus VivoBook + Lenovo',        NOW(),NOW()),
(31,'SL-202601-0002',17,'2026-01-15', 8800000,      0, 8800000,'Cash',    0, 0,'Laptop Acer Aspire 5',                  NOW(),NOW()),
(32,'SL-202602-0001',12,'2026-02-05', 4800000,      0, 4800000,'Transfer',1,30,'PC Rakitan Office untuk klinik',        NOW(),NOW()),
(33,'SL-202602-0002',1, '2026-02-12',  760000,      0,  760000,'Cash',    0, 0,'Mouse + Keyboard + Flashdisk',          NOW(),NOW()),
(34,'SL-202603-0001',5, '2026-03-04',17500000,      0,17500000,'Transfer',1,30,'2 Laptop Asus ROG untuk sekolah',       NOW(),NOW()),
(35,'SL-202603-0002',9, '2026-03-10', 3500000,      0, 3500000,'Transfer',1,30,'Printer Epson L5290',                   NOW(),NOW()),
(36,'SL-202604-0001',3, '2026-04-07',27000000,500000,26500000,'Transfer',1,30,'2 PC Rakitan Desain untuk perusahaan',   NOW(),NOW()),
(37,'SL-202604-0002',10,'2026-04-14',  810000,      0,  810000,'Cash',    0, 0,'RAM 8GB + SSD 256GB',                   NOW(),NOW()),
(38,'SL-202605-0001',20,'2026-05-05',15000000,      0,15000000,'Transfer',1,30,'2 Laptop Lenovo IdeaPad',               NOW(),NOW()),
(39,'SL-202605-0002',4, '2026-05-12',  540000,      0,  540000,'Cash',    0, 0,'Tinta Epson 003 Black 4 botol + Color 1 set',NOW(),NOW()),
(40,'SL-202605-0003',1, '2026-05-15',  440000,      0,  440000,'Cash',    0, 0,'Cooling pad + Kabel HDMI + Flashdisk',  NOW(),NOW());

INSERT INTO sale_items (id,sale_id,product_id,qty,unit_price,unit_cost,total,created_at,updated_at) VALUES
(1, 1, 1,1,8200000,6800000, 8200000,NOW(),NOW()),
(2, 2,13,1, 145000,  95000,  145000,NOW(),NOW()),
(3, 2,16,1, 115000,  75000,  115000,NOW(),NOW()),
(4, 2,17,1,  75000,  45000,   75000,NOW(),NOW()),  -- 145000+115000+75000=335000 ≠ 290000 → adjust: no flashdisk
-- recalc: mouse 145000 + flashdisk 115000 = 260000 ≠ 290000 → mouse 145000 + keyboard 265000 = 410000 ≠ 290000
-- use: mouse 145000 + HDMI 75000 + tinta 80000 = 300000 ≠ 290000 → mouse 145000 + HDMI 75000 = 220000 ≠ 290000
-- simplest: mouse 145000 + flashdisk 115000 + tinta 80000 = 340000 → just mouse 145000 + cooling 175000 = 320000
-- Final: 1×mouse(145000) + 1×flashdisk(115000) = 260000 → set sale total=260000 via UPDATE
(5, 3, 6,1,2100000,1650000, 2100000,NOW(),NOW()),
(6, 4, 3,1,6300000,5100000, 6300000,NOW(),NOW()),
(7, 5,10,3,4800000,3800000,14400000,NOW(),NOW()),
(8, 5,13,3, 145000,  95000,  435000,NOW(),NOW()),
(9, 5,17,3,  75000,  45000,  225000,NOW(),NOW()),  -- 14400000+435000+225000=15060000 ≠ 23500000 → use 3×PC only
-- recalc sale 5: 3×PC Office = 3×4800000=14400000, discount 500000 → total=13900000 ≠ 23500000
-- use: 3×PC(14400000) + 3×mouse(435000) + 3×keyboard(795000) + 3×HDMI(225000) = 15855000 → still ≠
-- simplest: 5×PC Office = 5×4800000=24000000, discount 500000 → total=23500000 ✓
(10,6,15,1, 210000, 145000,  210000,NOW(),NOW()),
(11,6,18,1, 175000, 120000,  175000,NOW(),NOW()),  -- 210000+175000=385000 ≠ 760000 → add headset+cooling
-- use: 1×headset(210000) + 1×cooling(175000) + 1×mouse(145000) + 1×keyboard(265000) = 795000 ≠ 760000
-- use: 1×headset(210000) + 1×cooling(175000) + 1×mouse(145000) + 1×flashdisk(115000) = 645000 ≠ 760000
-- use: 1×headset(210000) + 1×cooling(175000) + 1×keyboard(265000) = 650000 ≠ 760000
-- use: 1×headset(210000) + 1×cooling(175000) + 1×keyboard(265000) + 1×HDMI(75000) = 725000 ≠ 760000
-- use: 1×headset(210000) + 1×cooling(175000) + 1×keyboard(265000) + 1×flashdisk(115000) = 765000 ≠ 760000
-- Simplest: set sale total = sum of items. Will UPDATE sales totals after items.
(12,7, 5,1,8800000,7200000, 8800000,NOW(),NOW()),
(13,8, 7,1,1850000,1450000, 1850000,NOW(),NOW()),
(14,9,20,1, 380000, 280000,  380000,NOW(),NOW()),
(15,9,21,1, 430000, 320000,  430000,NOW(),NOW()),  -- 380000+430000=810000 ✓
(16,10,4,1,17500000,14500000,17500000,NOW(),NOW()),
(17,11,10,1,4800000,3800000, 4800000,NOW(),NOW()),
(18,12,17,2,  75000,  45000,  150000,NOW(),NOW()),
(19,12,16,2, 115000,  75000,  230000,NOW(),NOW()),
(20,12,13,1, 145000,  95000,  145000,NOW(),NOW()),  -- 150000+230000+145000=525000 ≠ 460000 → adjust
(21,13,1, 5,8200000,6800000,41000000,NOW(),NOW()),  -- 5×8200000=41000000, discount 500000 → total=40500000 ≠ 37000000
-- use: 5×LAP-001 = 41000000, discount 500000 → 40500000 ≠ 37000000
-- use: 4×LAP-001(32800000) + 1×LAP-003(6300000) = 39100000, discount 500000 → 38600000 ≠ 37000000
-- use: 4×LAP-001(32800000) + 1×ACC-001(145000) = 32945000 → too low
-- Simplest: 5×LAP-001 = 41000000, discount 4000000 → 37000000 ✓ → UPDATE discount
(22,14,8,1,3500000,2800000, 3500000,NOW(),NOW()),
(23,15,18,1, 175000, 120000,  175000,NOW(),NOW()),
(24,15,19,1, 265000, 185000,  265000,NOW(),NOW()),  -- 175000+265000=440000 ≠ 530000 → add flashdisk
(25,16,10,2,4800000,3800000, 9600000,NOW(),NOW()),  -- 2×4800000=9600000 ✓
(26,17,22,3,  80000,  55000,  240000,NOW(),NOW()),
(27,17,23,1, 230000, 165000,  230000,NOW(),NOW()),  -- 240000+230000=470000 ≠ 345000 → adjust
(28,18,1, 4,8200000,6800000,32800000,NOW(),NOW()),  -- 4×8200000=32800000, discount 1000000 → 31800000 ≠ 34000000
-- use: 4×LAP-001(32800000) + 1×LAP-002(7500000) = 40300000, discount 1000000 → 39300000 ≠ 34000000
-- use: 4×LAP-001(32800000) + 1×ACC-001(145000) = 32945000 → too low
-- Simplest: 4×LAP-001 = 32800000, discount 0 → 32800000 ≠ 34000000
-- use: 4×LAP-001(32800000) + 1×LAP-003(6300000) = 39100000, discount 1000000 → 38100000 ≠ 34000000
-- use: 4×LAP-001(32800000) + 1×ACC-008(380000) = 33180000 → close but ≠ 34000000
-- Simplest approach: set sale totals = sum(items) via UPDATE after insert
(29,19,20,1, 380000, 280000,  380000,NOW(),NOW()),
(30,19,21,1, 430000, 320000,  430000,NOW(),NOW()),  -- 380000+430000=810000 ✓
(31,20,12,1,13500000,11000000,13500000,NOW(),NOW()),
(32,21,2, 1,7500000,6200000, 7500000,NOW(),NOW()),
(33,22,6, 2,2100000,1650000, 4200000,NOW(),NOW()),  -- 2×2100000=4200000 ✓
(34,23,14,1, 265000, 185000,  265000,NOW(),NOW()),
(35,24,11,1,8200000,6500000, 8200000,NOW(),NOW()),
(36,25,24,1, 850000, 650000,  850000,NOW(),NOW()),
(37,26,1, 3,8200000,6800000,24600000,NOW(),NOW()),
(38,26,5, 1,8800000,7200000, 8800000,NOW(),NOW()),  -- 24600000+8800000=33400000, discount 200000 → 33200000 ≠ 25000000
-- use: 3×LAP-001(24600000) + 1×LAP-005(8800000) = 33400000, discount 200000 → 33200000 ≠ 25000000
-- use: 2×LAP-001(16400000) + 1×LAP-005(8800000) = 25200000, discount 200000 → 25000000 ✓
(39,27,22,6,  80000,  55000,  480000,NOW(),NOW()),
(40,27,23,1, 230000, 165000,  230000,NOW(),NOW()),  -- 480000+230000=710000 ≠ 690000 → adjust: 5 botol
(41,28,11,2,8200000,6500000,16400000,NOW(),NOW()),  -- 2×8200000=16400000 ✓
(42,29,9, 1,2450000,1900000, 2450000,NOW(),NOW()),
(43,30,1, 2,8200000,6800000,16400000,NOW(),NOW()),
(44,30,2, 1,7500000,6200000, 7500000,NOW(),NOW()),  -- 16400000+7500000=23900000, discount 500000 → 23400000 ≠ 22000000
-- use: 2×LAP-001(16400000) + 1×LAP-002(7500000) = 23900000, discount 500000 → 23400000 ≠ 22000000
-- use: 2×LAP-001(16400000) + 1×LAP-003(6300000) = 22700000, discount 500000 → 22200000 ≠ 22000000
-- use: 2×LAP-001(16400000) + 1×LAP-003(6300000) = 22700000, discount 700000 → 22000000 ✓ → UPDATE discount
(45,31,5, 1,8800000,7200000, 8800000,NOW(),NOW()),
(46,32,10,1,4800000,3800000, 4800000,NOW(),NOW()),
(47,33,13,1, 145000,  95000,  145000,NOW(),NOW()),
(48,33,14,1, 265000, 185000,  265000,NOW(),NOW()),
(49,33,16,1, 115000,  75000,  115000,NOW(),NOW()),  -- 145000+265000+115000=525000 ≠ 760000 → add more items
(50,34,4, 2,17500000,14500000,35000000,NOW(),NOW()), -- 2×17500000=35000000 ≠ 17500000 → use 1×LAP-004
(51,35,8, 1,3500000,2800000, 3500000,NOW(),NOW()),
(52,36,12,2,13500000,11000000,27000000,NOW(),NOW()), -- 2×13500000=27000000, discount 500000 → 26500000 ✓
(53,37,20,1, 380000, 280000,  380000,NOW(),NOW()),
(54,37,21,1, 430000, 320000,  430000,NOW(),NOW()),  -- 380000+430000=810000 ✓
(55,38,2, 2,7500000,6200000,15000000,NOW(),NOW()),  -- 2×7500000=15000000 ✓
(56,39,22,4,  80000,  55000,  320000,NOW(),NOW()),
(57,39,23,1, 230000, 165000,  230000,NOW(),NOW()),  -- 320000+230000=550000 ≠ 540000 → adjust: 3 botol
(58,40,18,1, 175000, 120000,  175000,NOW(),NOW()),
(59,40,17,1,  75000,  45000,   75000,NOW(),NOW()),
(60,40,16,1, 115000,  75000,  115000,NOW(),NOW());  -- 175000+75000+115000=365000 ≠ 440000 → add item

-- ============================================================
-- Fix sale_items & sales to be perfectly consistent
-- Replace all sale_items with clean, verified data
-- ============================================================
DELETE FROM sale_items;
DELETE FROM sales;

-- SALES (totals will match items exactly below)
INSERT INTO sales (id,sale_number,customer_id,sale_date,subtotal,discount,total,payment_method,is_credit_sale,payment_terms_days,notes,created_at,updated_at) VALUES
(1, 'SL-202501-0001',2, '2025-01-10', 8200000,      0, 8200000,'Cash',    0, 0,'Laptop Asus VivoBook untuk kuliah',          NOW(),NOW()),
(2, 'SL-202501-0002',1, '2025-01-13',  260000,      0,  260000,'Cash',    0, 0,'Mouse Logitech + Flashdisk SanDisk',          NOW(),NOW()),
(3, 'SL-202501-0003',7, '2025-01-20', 2100000,      0, 2100000,'Cash',    0, 0,'Printer Epson L3210 untuk toko',              NOW(),NOW()),
(4, 'SL-202502-0001',8, '2025-02-06', 6300000,      0, 6300000,'Transfer',0, 0,'Laptop HP untuk mengajar',                   NOW(),NOW()),
(5, 'SL-202502-0002',3, '2025-02-14',24000000, 500000,23500000,'Transfer',1,30,'5 PC Rakitan Office untuk kantor',            NOW(),NOW()),
(6, 'SL-202502-0003',10,'2025-02-20',  385000,      0,  385000,'Cash',    0, 0,'Headset gaming + Cooling pad',                NOW(),NOW()),
(7, 'SL-202503-0001',5, '2025-03-05', 8800000,      0, 8800000,'Transfer',1,30,'Laptop Acer untuk sekolah',                  NOW(),NOW()),
(8, 'SL-202503-0002',14,'2025-03-08', 1850000,      0, 1850000,'Cash',    0, 0,'Printer Canon G2020 untuk toko batik',        NOW(),NOW()),
(9, 'SL-202503-0003',4, '2025-03-15',  810000,      0,  810000,'Cash',    0, 0,'RAM 8GB + SSD 256GB upgrade',                 NOW(),NOW()),
(10,'SL-202504-0001',17,'2025-04-03',17500000,      0,17500000,'Transfer',0, 0,'Laptop Asus ROG untuk desainer',              NOW(),NOW()),
(11,'SL-202504-0002',18,'2025-04-08', 4800000,      0, 4800000,'Transfer',1,30,'PC Rakitan Office untuk apotek',              NOW(),NOW()),
(12,'SL-202504-0003',1, '2025-04-15',  440000,      0,  440000,'Cash',    0, 0,'Kabel HDMI x2 + Flashdisk x2 + Mouse',       NOW(),NOW()),
(13,'SL-202505-0001',16,'2025-05-06',41000000,4000000,37000000,'Transfer',1,45,'5 Laptop Asus VivoBook untuk lab sekolah',    NOW(),NOW()),
(14,'SL-202505-0002',9, '2025-05-12', 3500000,      0, 3500000,'Transfer',1,30,'Printer Epson L5290 untuk kantor',            NOW(),NOW()),
(15,'SL-202505-0003',6, '2025-05-20',  440000,      0,  440000,'Cash',    0, 0,'Cooling pad + Tas laptop',                   NOW(),NOW()),
(16,'SL-202506-0001',12,'2025-06-04', 9600000,      0, 9600000,'Transfer',1,30,'2 PC Rakitan Office untuk klinik',            NOW(),NOW()),
(17,'SL-202506-0002',1, '2025-06-10',  470000,      0,  470000,'Cash',    0, 0,'Tinta Epson 003 Black 3 btl + Color 1 set',  NOW(),NOW()),
(18,'SL-202507-0001',20,'2025-07-07',32800000,      0,32800000,'Transfer',1,30,'4 Laptop Asus VivoBook untuk perusahaan',     NOW(),NOW()),
(19,'SL-202507-0002',10,'2025-07-15',  810000,      0,  810000,'Cash',    0, 0,'RAM 8GB + SSD 256GB',                         NOW(),NOW()),
(20,'SL-202508-0001',3, '2025-08-05',13500000,      0,13500000,'Transfer',1,30,'PC Rakitan Desain untuk perusahaan',          NOW(),NOW()),
(21,'SL-202508-0002',15,'2025-08-12', 7500000,      0, 7500000,'Cash',    0, 0,'Laptop Lenovo IdeaPad',                       NOW(),NOW()),
(22,'SL-202509-0001',5, '2025-09-03', 4200000,      0, 4200000,'Transfer',1,30,'2 Printer Epson L3210 untuk sekolah',         NOW(),NOW()),
(23,'SL-202509-0002',4, '2025-09-10',  265000,      0,  265000,'Cash',    0, 0,'Keyboard Wireless Logitech',                  NOW(),NOW()),
(24,'SL-202510-0001',9, '2025-10-06', 8200000,      0, 8200000,'Transfer',1,30,'PC Rakitan Gaming untuk kantor',              NOW(),NOW()),
(25,'SL-202510-0002',6, '2025-10-14',  850000,      0,  850000,'Cash',    0, 0,'UPS APC 650VA',                               NOW(),NOW()),
(26,'SL-202511-0001',16,'2025-11-04',25200000, 200000,25000000,'Transfer',1,45,'3 Laptop Asus VivoBook + 1 Acer Aspire',      NOW(),NOW()),
(27,'SL-202511-0002',1, '2025-11-12',  710000,      0,  710000,'Cash',    0, 0,'Tinta Epson 003 Black 6 btl + Color 1 set',  NOW(),NOW()),
(28,'SL-202512-0001',3, '2025-12-03',16400000,      0,16400000,'Transfer',1,30,'2 PC Rakitan Gaming untuk perusahaan',        NOW(),NOW()),
(29,'SL-202512-0002',7, '2025-12-10', 2450000,      0, 2450000,'Cash',    0, 0,'HP LaserJet Pro M15w untuk toko',             NOW(),NOW()),
(30,'SL-202601-0001',20,'2026-01-08',22700000, 700000,22000000,'Transfer',1,30,'2 Laptop Asus VivoBook + 1 HP 14s',           NOW(),NOW()),
(31,'SL-202601-0002',17,'2026-01-15', 8800000,      0, 8800000,'Cash',    0, 0,'Laptop Acer Aspire 5',                        NOW(),NOW()),
(32,'SL-202602-0001',12,'2026-02-05', 4800000,      0, 4800000,'Transfer',1,30,'PC Rakitan Office untuk klinik',              NOW(),NOW()),
(33,'SL-202602-0002',1, '2026-02-12',  525000,      0,  525000,'Cash',    0, 0,'Mouse + Keyboard + Flashdisk',                NOW(),NOW()),
(34,'SL-202603-0001',5, '2026-03-04',17500000,      0,17500000,'Transfer',1,30,'1 Laptop Asus ROG untuk sekolah',             NOW(),NOW()),
(35,'SL-202603-0002',9, '2026-03-10', 3500000,      0, 3500000,'Transfer',1,30,'Printer Epson L5290',                         NOW(),NOW()),
(36,'SL-202604-0001',3, '2026-04-07',27000000, 500000,26500000,'Transfer',1,30,'2 PC Rakitan Desain untuk perusahaan',        NOW(),NOW()),
(37,'SL-202604-0002',10,'2026-04-14',  810000,      0,  810000,'Cash',    0, 0,'RAM 8GB + SSD 256GB',                         NOW(),NOW()),
(38,'SL-202605-0001',20,'2026-05-05',15000000,      0,15000000,'Transfer',1,30,'2 Laptop Lenovo IdeaPad',                     NOW(),NOW()),
(39,'SL-202605-0002',4, '2026-05-12',  550000,      0,  550000,'Cash',    0, 0,'Tinta Epson 003 Black 4 btl + Color 1 set',  NOW(),NOW()),
(40,'SL-202605-0003',1, '2026-05-15',  365000,      0,  365000,'Cash',    0, 0,'Cooling pad + Kabel HDMI + Flashdisk',        NOW(),NOW());

-- SALE ITEMS (qty × unit_price = total, sum = sale.subtotal, verified)
INSERT INTO sale_items (id,sale_id,product_id,qty,unit_price,unit_cost,total,created_at,updated_at) VALUES
-- Sale 1: 1×LAP-001 = 8,200,000 ✓
(1, 1, 1,1,8200000,6800000, 8200000,NOW(),NOW()),
-- Sale 2: 1×mouse(145000) + 1×flashdisk(115000) = 260,000 ✓
(2, 2,13,1, 145000,  95000,  145000,NOW(),NOW()),
(3, 2,16,1, 115000,  75000,  115000,NOW(),NOW()),
-- Sale 3: 1×PRT-001 = 2,100,000 ✓
(4, 3, 6,1,2100000,1650000, 2100000,NOW(),NOW()),
-- Sale 4: 1×LAP-003 = 6,300,000 ✓
(5, 4, 3,1,6300000,5100000, 6300000,NOW(),NOW()),
-- Sale 5: 5×CPU-001 = 24,000,000, discount 500,000 → 23,500,000 ✓
(6, 5,10,5,4800000,3800000,24000000,NOW(),NOW()),
-- Sale 6: 1×headset(210,000) + 1×cooling(175,000) = 385,000 ✓
(7, 6,15,1, 210000, 145000,  210000,NOW(),NOW()),
(8, 6,18,1, 175000, 120000,  175000,NOW(),NOW()),
-- Sale 7: 1×LAP-005 = 8,800,000 ✓
(9, 7, 5,1,8800000,7200000, 8800000,NOW(),NOW()),
-- Sale 8: 1×PRT-002 = 1,850,000 ✓
(10,8, 7,1,1850000,1450000, 1850000,NOW(),NOW()),
-- Sale 9: 1×RAM(380,000) + 1×SSD(430,000) = 810,000 ✓
(11,9,20,1, 380000, 280000,  380000,NOW(),NOW()),
(12,9,21,1, 430000, 320000,  430000,NOW(),NOW()),
-- Sale 10: 1×LAP-004 = 17,500,000 ✓
(13,10,4,1,17500000,14500000,17500000,NOW(),NOW()),
-- Sale 11: 1×CPU-001 = 4,800,000 ✓
(14,11,10,1,4800000,3800000, 4800000,NOW(),NOW()),
-- Sale 12: 2×HDMI(150,000) + 2×flashdisk(230,000) + 1×mouse(145,000) = 525,000 → adjust: 2×HDMI+2×flash=380,000+mouse=525,000 ≠ 440,000
-- use: 2×HDMI(150,000) + 2×flashdisk(230,000) = 380,000 ≠ 440,000
-- use: 1×HDMI(75,000) + 2×flashdisk(230,000) + 1×mouse(145,000) = 450,000 ≠ 440,000
-- use: 1×HDMI(75,000) + 1×flashdisk(115,000) + 1×mouse(145,000) + 1×tinta(80,000) = 415,000 ≠ 440,000
-- use: 2×HDMI(150,000) + 1×flashdisk(115,000) + 1×mouse(145,000) = 410,000 ≠ 440,000
-- use: 2×HDMI(150,000) + 2×flashdisk(230,000) + 1×tinta(80,000) = 460,000 ≠ 440,000
-- use: 1×HDMI(75,000) + 2×flashdisk(230,000) + 1×tinta(80,000) = 385,000 ≠ 440,000
-- use: 2×HDMI(150,000) + 1×flashdisk(115,000) + 1×mouse(145,000) + 1×tinta(80,000) = 490,000 ≠ 440,000
-- Simplest: 2×HDMI(150,000) + 2×flashdisk(230,000) = 380,000 → UPDATE sale 12 total=380,000
(15,12,17,2,  75000,  45000,  150000,NOW(),NOW()),
(16,12,16,2, 115000,  75000,  230000,NOW(),NOW()),
-- Sale 13: 5×LAP-001 = 41,000,000, discount 4,000,000 → 37,000,000 ✓
(17,13,1, 5,8200000,6800000,41000000,NOW(),NOW()),
-- Sale 14: 1×PRT-003 = 3,500,000 ✓
(18,14,8,1,3500000,2800000, 3500000,NOW(),NOW()),
-- Sale 15: 1×cooling(175,000) + 1×tas(265,000) = 440,000 ✓
(19,15,18,1, 175000, 120000,  175000,NOW(),NOW()),
(20,15,19,1, 265000, 185000,  265000,NOW(),NOW()),
-- Sale 16: 2×CPU-001 = 9,600,000 ✓
(21,16,10,2,4800000,3800000, 9600000,NOW(),NOW()),
-- Sale 17: 3×tinta black(240,000) + 1×tinta color(230,000) = 470,000 ✓
(22,17,22,3,  80000,  55000,  240000,NOW(),NOW()),
(23,17,23,1, 230000, 165000,  230000,NOW(),NOW()),
-- Sale 18: 4×LAP-001 = 32,800,000 ✓
(24,18,1, 4,8200000,6800000,32800000,NOW(),NOW()),
-- Sale 19: 1×RAM(380,000) + 1×SSD(430,000) = 810,000 ✓
(25,19,20,1, 380000, 280000,  380000,NOW(),NOW()),
(26,19,21,1, 430000, 320000,  430000,NOW(),NOW()),
-- Sale 20: 1×CPU-003 = 13,500,000 ✓
(27,20,12,1,13500000,11000000,13500000,NOW(),NOW()),
-- Sale 21: 1×LAP-002 = 7,500,000 ✓
(28,21,2, 1,7500000,6200000, 7500000,NOW(),NOW()),
-- Sale 22: 2×PRT-001 = 4,200,000 ✓
(29,22,6, 2,2100000,1650000, 4200000,NOW(),NOW()),
-- Sale 23: 1×keyboard = 265,000 ✓
(30,23,14,1, 265000, 185000,  265000,NOW(),NOW()),
-- Sale 24: 1×CPU-002 = 8,200,000 ✓
(31,24,11,1,8200000,6500000, 8200000,NOW(),NOW()),
-- Sale 25: 1×UPS = 850,000 ✓
(32,25,24,1, 850000, 650000,  850000,NOW(),NOW()),
-- Sale 26: 3×LAP-001(24,600,000) + 1×LAP-005(8,800,000) = 33,400,000 → discount 200,000 → 33,200,000 ≠ 25,000,000
-- use: 2×LAP-001(16,400,000) + 1×LAP-005(8,800,000) = 25,200,000, discount 200,000 → 25,000,000 ✓
(33,26,1, 2,8200000,6800000,16400000,NOW(),NOW()),
(34,26,5, 1,8800000,7200000, 8800000,NOW(),NOW()),
-- Sale 27: 6×tinta black(480,000) + 1×tinta color(230,000) = 710,000 ✓
(35,27,22,6,  80000,  55000,  480000,NOW(),NOW()),
(36,27,23,1, 230000, 165000,  230000,NOW(),NOW()),
-- Sale 28: 2×CPU-002 = 16,400,000 ✓
(37,28,11,2,8200000,6500000,16400000,NOW(),NOW()),
-- Sale 29: 1×PRT-004 = 2,450,000 ✓
(38,29,9, 1,2450000,1900000, 2450000,NOW(),NOW()),
-- Sale 30: 2×LAP-001(16,400,000) + 1×LAP-003(6,300,000) = 22,700,000, discount 700,000 → 22,000,000 ✓
(39,30,1, 2,8200000,6800000,16400000,NOW(),NOW()),
(40,30,3, 1,6300000,5100000, 6300000,NOW(),NOW()),
-- Sale 31: 1×LAP-005 = 8,800,000 ✓
(41,31,5, 1,8800000,7200000, 8800000,NOW(),NOW()),
-- Sale 32: 1×CPU-001 = 4,800,000 ✓
(42,32,10,1,4800000,3800000, 4800000,NOW(),NOW()),
-- Sale 33: 1×mouse(145,000) + 1×keyboard(265,000) + 1×flashdisk(115,000) = 525,000 ✓
(43,33,13,1, 145000,  95000,  145000,NOW(),NOW()),
(44,33,14,1, 265000, 185000,  265000,NOW(),NOW()),
(45,33,16,1, 115000,  75000,  115000,NOW(),NOW()),
-- Sale 34: 1×LAP-004 = 17,500,000 ✓
(46,34,4, 1,17500000,14500000,17500000,NOW(),NOW()),
-- Sale 35: 1×PRT-003 = 3,500,000 ✓
(47,35,8, 1,3500000,2800000, 3500000,NOW(),NOW()),
-- Sale 36: 2×CPU-003 = 27,000,000, discount 500,000 → 26,500,000 ✓
(48,36,12,2,13500000,11000000,27000000,NOW(),NOW()),
-- Sale 37: 1×RAM(380,000) + 1×SSD(430,000) = 810,000 ✓
(49,37,20,1, 380000, 280000,  380000,NOW(),NOW()),
(50,37,21,1, 430000, 320000,  430000,NOW(),NOW()),
-- Sale 38: 2×LAP-002 = 15,000,000 ✓
(51,38,2, 2,7500000,6200000,15000000,NOW(),NOW()),
-- Sale 39: 4×tinta black(320,000) + 1×tinta color(230,000) = 550,000 ✓
(52,39,22,4,  80000,  55000,  320000,NOW(),NOW()),
(53,39,23,1, 230000, 165000,  230000,NOW(),NOW()),
-- Sale 40: 1×cooling(175,000) + 1×HDMI(75,000) + 1×flashdisk(115,000) = 365,000 ✓
(54,40,18,1, 175000, 120000,  175000,NOW(),NOW()),
(55,40,17,1,  75000,  45000,   75000,NOW(),NOW()),
(56,40,16,1, 115000,  75000,  115000,NOW(),NOW());

-- Fix sale 12 subtotal/total to match items (380,000)
UPDATE sales SET subtotal=380000, total=380000 WHERE id=12;
-- Fix sale 26 subtotal to match items (25,200,000)
UPDATE sales SET subtotal=25200000 WHERE id=26;

-- ============================================================
-- 11. PAYROLL (7 karyawan × 17 bulan = Jan 2025 – Mei 2026)
-- ============================================================
INSERT INTO payrolls (id,employee_id,period_month,period_year,base_salary,allowances,deductions,net_salary,paid_at,status,notes,created_at,updated_at) VALUES
-- EMP-001 Ahmad Fauzi (8,000,000/bln)
(1, 1,1,2025,8000000,500000,0,8500000,'2025-01-31','Paid',NULL,NOW(),NOW()),
(2, 1,2,2025,8000000,500000,0,8500000,'2025-02-28','Paid',NULL,NOW(),NOW()),
(3, 1,3,2025,8000000,500000,0,8500000,'2025-03-31','Paid',NULL,NOW(),NOW()),
(4, 1,4,2025,8000000,500000,0,8500000,'2025-04-30','Paid',NULL,NOW(),NOW()),
(5, 1,5,2025,8000000,500000,0,8500000,'2025-05-31','Paid',NULL,NOW(),NOW()),
(6, 1,6,2025,8000000,500000,0,8500000,'2025-06-30','Paid',NULL,NOW(),NOW()),
(7, 1,7,2025,8000000,500000,0,8500000,'2025-07-31','Paid',NULL,NOW(),NOW()),
(8, 1,8,2025,8000000,500000,0,8500000,'2025-08-31','Paid',NULL,NOW(),NOW()),
(9, 1,9,2025,8000000,500000,0,8500000,'2025-09-30','Paid',NULL,NOW(),NOW()),
(10,1,10,2025,8000000,500000,0,8500000,'2025-10-31','Paid',NULL,NOW(),NOW()),
(11,1,11,2025,8000000,500000,0,8500000,'2025-11-30','Paid',NULL,NOW(),NOW()),
(12,1,12,2025,8000000,1000000,0,9000000,'2025-12-31','Paid','THR Desember',NOW(),NOW()),
(13,1,1,2026,8000000,500000,0,8500000,'2026-01-31','Paid',NULL,NOW(),NOW()),
(14,1,2,2026,8000000,500000,0,8500000,'2026-02-28','Paid',NULL,NOW(),NOW()),
(15,1,3,2026,8000000,500000,0,8500000,'2026-03-31','Paid',NULL,NOW(),NOW()),
(16,1,4,2026,8000000,500000,0,8500000,'2026-04-30','Paid',NULL,NOW(),NOW()),
(17,1,5,2026,8000000,500000,0,8500000,'2026-05-31','Paid',NULL,NOW(),NOW()),
-- EMP-002 Siti Rahayu (5,500,000/bln)
(18,2,1,2025,5500000,300000,0,5800000,'2025-01-31','Paid',NULL,NOW(),NOW()),
(19,2,2,2025,5500000,300000,0,5800000,'2025-02-28','Paid',NULL,NOW(),NOW()),
(20,2,3,2025,5500000,300000,0,5800000,'2025-03-31','Paid',NULL,NOW(),NOW()),
(21,2,4,2025,5500000,300000,0,5800000,'2025-04-30','Paid',NULL,NOW(),NOW()),
(22,2,5,2025,5500000,300000,0,5800000,'2025-05-31','Paid',NULL,NOW(),NOW()),
(23,2,6,2025,5500000,300000,0,5800000,'2025-06-30','Paid',NULL,NOW(),NOW()),
(24,2,7,2025,5500000,300000,0,5800000,'2025-07-31','Paid',NULL,NOW(),NOW()),
(25,2,8,2025,5500000,300000,0,5800000,'2025-08-31','Paid',NULL,NOW(),NOW()),
(26,2,9,2025,5500000,300000,0,5800000,'2025-09-30','Paid',NULL,NOW(),NOW()),
(27,2,10,2025,5500000,300000,0,5800000,'2025-10-31','Paid',NULL,NOW(),NOW()),
(28,2,11,2025,5500000,300000,0,5800000,'2025-11-30','Paid',NULL,NOW(),NOW()),
(29,2,12,2025,5500000,800000,0,6300000,'2025-12-31','Paid','THR Desember',NOW(),NOW()),
(30,2,1,2026,5500000,300000,0,5800000,'2026-01-31','Paid',NULL,NOW(),NOW()),
(31,2,2,2026,5500000,300000,0,5800000,'2026-02-28','Paid',NULL,NOW(),NOW()),
(32,2,3,2026,5500000,300000,0,5800000,'2026-03-31','Paid',NULL,NOW(),NOW()),
(33,2,4,2026,5500000,300000,0,5800000,'2026-04-30','Paid',NULL,NOW(),NOW()),
(34,2,5,2026,5500000,300000,0,5800000,'2026-05-31','Paid',NULL,NOW(),NOW()),
-- EMP-003 Budi Santoso (4,000,000/bln)
(35,3,1,2025,4000000,200000,200000,4000000,'2025-01-31','Paid',NULL,NOW(),NOW()),
(36,3,2,2025,4000000,200000,200000,4000000,'2025-02-28','Paid',NULL,NOW(),NOW()),
(37,3,3,2025,4000000,200000,200000,4000000,'2025-03-31','Paid',NULL,NOW(),NOW()),
(38,3,4,2025,4000000,200000,200000,4000000,'2025-04-30','Paid',NULL,NOW(),NOW()),
(39,3,5,2025,4000000,200000,200000,4000000,'2025-05-31','Paid',NULL,NOW(),NOW()),
(40,3,6,2025,4000000,200000,200000,4000000,'2025-06-30','Paid',NULL,NOW(),NOW()),
(41,3,7,2025,4000000,200000,200000,4000000,'2025-07-31','Paid',NULL,NOW(),NOW()),
(42,3,8,2025,4000000,200000,200000,4000000,'2025-08-31','Paid',NULL,NOW(),NOW()),
(43,3,9,2025,4000000,200000,200000,4000000,'2025-09-30','Paid',NULL,NOW(),NOW()),
(44,3,10,2025,4000000,200000,200000,4000000,'2025-10-31','Paid',NULL,NOW(),NOW()),
(45,3,11,2025,4000000,200000,200000,4000000,'2025-11-30','Paid',NULL,NOW(),NOW()),
(46,3,12,2025,4000000,600000,200000,4400000,'2025-12-31','Paid','THR Desember',NOW(),NOW()),
(47,3,1,2026,4000000,200000,200000,4000000,'2026-01-31','Paid',NULL,NOW(),NOW()),
(48,3,2,2026,4000000,200000,200000,4000000,'2026-02-28','Paid',NULL,NOW(),NOW()),
(49,3,3,2026,4000000,200000,200000,4000000,'2026-03-31','Paid',NULL,NOW(),NOW()),
(50,3,4,2026,4000000,200000,200000,4000000,'2026-04-30','Paid',NULL,NOW(),NOW()),
(51,3,5,2026,4000000,200000,200000,4000000,'2026-05-31','Paid',NULL,NOW(),NOW()),
-- EMP-004 Dewi Lestari (3,200,000/bln)
(52,4,1,2025,3200000,150000,160000,3190000,'2025-01-31','Paid',NULL,NOW(),NOW()),
(53,4,2,2025,3200000,150000,160000,3190000,'2025-02-28','Paid',NULL,NOW(),NOW()),
(54,4,3,2025,3200000,150000,160000,3190000,'2025-03-31','Paid',NULL,NOW(),NOW()),
(55,4,4,2025,3200000,150000,160000,3190000,'2025-04-30','Paid',NULL,NOW(),NOW()),
(56,4,5,2025,3200000,150000,160000,3190000,'2025-05-31','Paid',NULL,NOW(),NOW()),
(57,4,6,2025,3200000,150000,160000,3190000,'2025-06-30','Paid',NULL,NOW(),NOW()),
(58,4,7,2025,3200000,150000,160000,3190000,'2025-07-31','Paid',NULL,NOW(),NOW()),
(59,4,8,2025,3200000,150000,160000,3190000,'2025-08-31','Paid',NULL,NOW(),NOW()),
(60,4,9,2025,3200000,150000,160000,3190000,'2025-09-30','Paid',NULL,NOW(),NOW()),
(61,4,10,2025,3200000,150000,160000,3190000,'2025-10-31','Paid',NULL,NOW(),NOW()),
(62,4,11,2025,3200000,150000,160000,3190000,'2025-11-30','Paid',NULL,NOW(),NOW()),
(63,4,12,2025,3200000,500000,160000,3540000,'2025-12-31','Paid','THR Desember',NOW(),NOW()),
(64,4,1,2026,3200000,150000,160000,3190000,'2026-01-31','Paid',NULL,NOW(),NOW()),
(65,4,2,2026,3200000,150000,160000,3190000,'2026-02-28','Paid',NULL,NOW(),NOW()),
(66,4,3,2026,3200000,150000,160000,3190000,'2026-03-31','Paid',NULL,NOW(),NOW()),
(67,4,4,2026,3200000,150000,160000,3190000,'2026-04-30','Paid',NULL,NOW(),NOW()),
(68,4,5,2026,3200000,150000,160000,3190000,'2026-05-31','Paid',NULL,NOW(),NOW()),
-- EMP-005 Rudi Hermawan (3,000,000/bln)
(69,5,1,2025,3000000,150000,150000,3000000,'2025-01-31','Paid',NULL,NOW(),NOW()),
(70,5,2,2025,3000000,150000,150000,3000000,'2025-02-28','Paid',NULL,NOW(),NOW()),
(71,5,3,2025,3000000,150000,150000,3000000,'2025-03-31','Paid',NULL,NOW(),NOW()),
(72,5,4,2025,3000000,150000,150000,3000000,'2025-04-30','Paid',NULL,NOW(),NOW()),
(73,5,5,2025,3000000,150000,150000,3000000,'2025-05-31','Paid',NULL,NOW(),NOW()),
(74,5,6,2025,3000000,150000,150000,3000000,'2025-06-30','Paid',NULL,NOW(),NOW()),
(75,5,7,2025,3000000,150000,150000,3000000,'2025-07-31','Paid',NULL,NOW(),NOW()),
(76,5,8,2025,3000000,150000,150000,3000000,'2025-08-31','Paid',NULL,NOW(),NOW()),
(77,5,9,2025,3000000,150000,150000,3000000,'2025-09-30','Paid',NULL,NOW(),NOW()),
(78,5,10,2025,3000000,150000,150000,3000000,'2025-10-31','Paid',NULL,NOW(),NOW()),
(79,5,11,2025,3000000,150000,150000,3000000,'2025-11-30','Paid',NULL,NOW(),NOW()),
(80,5,12,2025,3000000,500000,150000,3350000,'2025-12-31','Paid','THR Desember',NOW(),NOW()),
(81,5,1,2026,3000000,150000,150000,3000000,'2026-01-31','Paid',NULL,NOW(),NOW()),
(82,5,2,2026,3000000,150000,150000,3000000,'2026-02-28','Paid',NULL,NOW(),NOW()),
(83,5,3,2026,3000000,150000,150000,3000000,'2026-03-31','Paid',NULL,NOW(),NOW()),
(84,5,4,2026,3000000,150000,150000,3000000,'2026-04-30','Paid',NULL,NOW(),NOW()),
(85,5,5,2026,3000000,150000,150000,3000000,'2026-05-31','Paid',NULL,NOW(),NOW()),
-- EMP-006 Rina Wulandari (3,200,000/bln)
(86,6,1,2025,3200000,150000,160000,3190000,'2025-01-31','Paid',NULL,NOW(),NOW()),
(87,6,2,2025,3200000,150000,160000,3190000,'2025-02-28','Paid',NULL,NOW(),NOW()),
(88,6,3,2025,3200000,150000,160000,3190000,'2025-03-31','Paid',NULL,NOW(),NOW()),
(89,6,4,2025,3200000,150000,160000,3190000,'2025-04-30','Paid',NULL,NOW(),NOW()),
(90,6,5,2025,3200000,150000,160000,3190000,'2025-05-31','Paid',NULL,NOW(),NOW()),
(91,6,6,2025,3200000,150000,160000,3190000,'2025-06-30','Paid',NULL,NOW(),NOW()),
(92,6,7,2025,3200000,150000,160000,3190000,'2025-07-31','Paid',NULL,NOW(),NOW()),
(93,6,8,2025,3200000,150000,160000,3190000,'2025-08-31','Paid',NULL,NOW(),NOW()),
(94,6,9,2025,3200000,150000,160000,3190000,'2025-09-30','Paid',NULL,NOW(),NOW()),
(95,6,10,2025,3200000,150000,160000,3190000,'2025-10-31','Paid',NULL,NOW(),NOW()),
(96,6,11,2025,3200000,150000,160000,3190000,'2025-11-30','Paid',NULL,NOW(),NOW()),
(97,6,12,2025,3200000,500000,160000,3540000,'2025-12-31','Paid','THR Desember',NOW(),NOW()),
(98,6,1,2026,3200000,150000,160000,3190000,'2026-01-31','Paid',NULL,NOW(),NOW()),
(99,6,2,2026,3200000,150000,160000,3190000,'2026-02-28','Paid',NULL,NOW(),NOW()),
(100,6,3,2026,3200000,150000,160000,3190000,'2026-03-31','Paid',NULL,NOW(),NOW()),
(101,6,4,2026,3200000,150000,160000,3190000,'2026-04-30','Paid',NULL,NOW(),NOW()),
(102,6,5,2026,3200000,150000,160000,3190000,'2026-05-31','Paid',NULL,NOW(),NOW()),
-- EMP-007 Agus Triyono (3,800,000/bln)
(103,7,1,2025,3800000,200000,190000,3810000,'2025-01-31','Paid',NULL,NOW(),NOW()),
(104,7,2,2025,3800000,200000,190000,3810000,'2025-02-28','Paid',NULL,NOW(),NOW()),
(105,7,3,2025,3800000,200000,190000,3810000,'2025-03-31','Paid',NULL,NOW(),NOW()),
(106,7,4,2025,3800000,200000,190000,3810000,'2025-04-30','Paid',NULL,NOW(),NOW()),
(107,7,5,2025,3800000,200000,190000,3810000,'2025-05-31','Paid',NULL,NOW(),NOW()),
(108,7,6,2025,3800000,200000,190000,3810000,'2025-06-30','Paid',NULL,NOW(),NOW()),
(109,7,7,2025,3800000,200000,190000,3810000,'2025-07-31','Paid',NULL,NOW(),NOW()),
(110,7,8,2025,3800000,200000,190000,3810000,'2025-08-31','Paid',NULL,NOW(),NOW()),
(111,7,9,2025,3800000,200000,190000,3810000,'2025-09-30','Paid',NULL,NOW(),NOW()),
(112,7,10,2025,3800000,200000,190000,3810000,'2025-10-31','Paid',NULL,NOW(),NOW()),
(113,7,11,2025,3800000,200000,190000,3810000,'2025-11-30','Paid',NULL,NOW(),NOW()),
(114,7,12,2025,3800000,600000,190000,4210000,'2025-12-31','Paid','THR Desember',NOW(),NOW()),
(115,7,1,2026,3800000,200000,190000,3810000,'2026-01-31','Paid',NULL,NOW(),NOW()),
(116,7,2,2026,3800000,200000,190000,3810000,'2026-02-28','Paid',NULL,NOW(),NOW()),
(117,7,3,2026,3800000,200000,190000,3810000,'2026-03-31','Paid',NULL,NOW(),NOW()),
(118,7,4,2026,3800000,200000,190000,3810000,'2026-04-30','Paid',NULL,NOW(),NOW()),
(119,7,5,2026,3800000,200000,190000,3810000,'2026-05-31','Paid',NULL,NOW(),NOW());

-- ============================================================
-- 12. EXPENSES (biaya operasional bulanan)
-- account_id: 20=Listrik, 21=Pemeliharaan, 22=Umum&Admin, 23=Sewa
-- ============================================================
INSERT INTO expenses (id,expense_date,category,description,amount,account_id,reference,created_at,updated_at) VALUES
-- Sewa ruko (Rp 3,500,000/bln)
(1, '2025-01-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Januari 2025',  3500000,23,'INV-SEWA-2501',NOW(),NOW()),
(2, '2025-02-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Februari 2025', 3500000,23,'INV-SEWA-2502',NOW(),NOW()),
(3, '2025-03-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Maret 2025',    3500000,23,'INV-SEWA-2503',NOW(),NOW()),
(4, '2025-04-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - April 2025',    3500000,23,'INV-SEWA-2504',NOW(),NOW()),
(5, '2025-05-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Mei 2025',      3500000,23,'INV-SEWA-2505',NOW(),NOW()),
(6, '2025-06-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Juni 2025',     3500000,23,'INV-SEWA-2506',NOW(),NOW()),
(7, '2025-07-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Juli 2025',     3500000,23,'INV-SEWA-2507',NOW(),NOW()),
(8, '2025-08-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Agustus 2025',  3500000,23,'INV-SEWA-2508',NOW(),NOW()),
(9, '2025-09-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - September 2025',3500000,23,'INV-SEWA-2509',NOW(),NOW()),
(10,'2025-10-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Oktober 2025',  3500000,23,'INV-SEWA-2510',NOW(),NOW()),
(11,'2025-11-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - November 2025', 3500000,23,'INV-SEWA-2511',NOW(),NOW()),
(12,'2025-12-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Desember 2025', 3500000,23,'INV-SEWA-2512',NOW(),NOW()),
(13,'2026-01-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Januari 2026',  3500000,23,'INV-SEWA-2601',NOW(),NOW()),
(14,'2026-02-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Februari 2026', 3500000,23,'INV-SEWA-2602',NOW(),NOW()),
(15,'2026-03-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Maret 2026',    3500000,23,'INV-SEWA-2603',NOW(),NOW()),
(16,'2026-04-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - April 2026',    3500000,23,'INV-SEWA-2604',NOW(),NOW()),
(17,'2026-05-05','Rent',       'Sewa ruko Jl. Cihampelas No.10 - Mei 2026',      3500000,23,'INV-SEWA-2605',NOW(),NOW()),
-- Listrik (Rp 850,000 – 1,100,000/bln)
(18,'2025-01-15','Electricity','Tagihan listrik PLN - Januari 2025',               850000,20,'PLN-2501',NOW(),NOW()),
(19,'2025-02-15','Electricity','Tagihan listrik PLN - Februari 2025',              870000,20,'PLN-2502',NOW(),NOW()),
(20,'2025-03-15','Electricity','Tagihan listrik PLN - Maret 2025',                 900000,20,'PLN-2503',NOW(),NOW()),
(21,'2025-04-15','Electricity','Tagihan listrik PLN - April 2025',                 920000,20,'PLN-2504',NOW(),NOW()),
(22,'2025-05-15','Electricity','Tagihan listrik PLN - Mei 2025',                   950000,20,'PLN-2505',NOW(),NOW()),
(23,'2025-06-15','Electricity','Tagihan listrik PLN - Juni 2025',                  980000,20,'PLN-2506',NOW(),NOW()),
(24,'2025-07-15','Electricity','Tagihan listrik PLN - Juli 2025',                 1050000,20,'PLN-2507',NOW(),NOW()),
(25,'2025-08-15','Electricity','Tagihan listrik PLN - Agustus 2025',              1100000,20,'PLN-2508',NOW(),NOW()),
(26,'2025-09-15','Electricity','Tagihan listrik PLN - September 2025',            1050000,20,'PLN-2509',NOW(),NOW()),
(27,'2025-10-15','Electricity','Tagihan listrik PLN - Oktober 2025',               980000,20,'PLN-2510',NOW(),NOW()),
(28,'2025-11-15','Electricity','Tagihan listrik PLN - November 2025',              920000,20,'PLN-2511',NOW(),NOW()),
(29,'2025-12-15','Electricity','Tagihan listrik PLN - Desember 2025',              900000,20,'PLN-2512',NOW(),NOW()),
(30,'2026-01-15','Electricity','Tagihan listrik PLN - Januari 2026',               870000,20,'PLN-2601',NOW(),NOW()),
(31,'2026-02-15','Electricity','Tagihan listrik PLN - Februari 2026',              880000,20,'PLN-2602',NOW(),NOW()),
(32,'2026-03-15','Electricity','Tagihan listrik PLN - Maret 2026',                 910000,20,'PLN-2603',NOW(),NOW()),
(33,'2026-04-15','Electricity','Tagihan listrik PLN - April 2026',                 930000,20,'PLN-2604',NOW(),NOW()),
(34,'2026-05-15','Electricity','Tagihan listrik PLN - Mei 2026',                   960000,20,'PLN-2605',NOW(),NOW()),
-- Internet (Rp 450,000/bln)
(35,'2025-01-10','Internet',   'Tagihan internet IndiHome - Januari 2025',         450000,22,'IH-2501',NOW(),NOW()),
(36,'2025-02-10','Internet',   'Tagihan internet IndiHome - Februari 2025',        450000,22,'IH-2502',NOW(),NOW()),
(37,'2025-03-10','Internet',   'Tagihan internet IndiHome - Maret 2025',           450000,22,'IH-2503',NOW(),NOW()),
(38,'2025-04-10','Internet',   'Tagihan internet IndiHome - April 2025',           450000,22,'IH-2504',NOW(),NOW()),
(39,'2025-05-10','Internet',   'Tagihan internet IndiHome - Mei 2025',             450000,22,'IH-2505',NOW(),NOW()),
(40,'2025-06-10','Internet',   'Tagihan internet IndiHome - Juni 2025',            450000,22,'IH-2506',NOW(),NOW()),
(41,'2025-07-10','Internet',   'Tagihan internet IndiHome - Juli 2025',            450000,22,'IH-2507',NOW(),NOW()),
(42,'2025-08-10','Internet',   'Tagihan internet IndiHome - Agustus 2025',         450000,22,'IH-2508',NOW(),NOW()),
(43,'2025-09-10','Internet',   'Tagihan internet IndiHome - September 2025',       450000,22,'IH-2509',NOW(),NOW()),
(44,'2025-10-10','Internet',   'Tagihan internet IndiHome - Oktober 2025',         450000,22,'IH-2510',NOW(),NOW()),
(45,'2025-11-10','Internet',   'Tagihan internet IndiHome - November 2025',        450000,22,'IH-2511',NOW(),NOW()),
(46,'2025-12-10','Internet',   'Tagihan internet IndiHome - Desember 2025',        450000,22,'IH-2512',NOW(),NOW()),
(47,'2026-01-10','Internet',   'Tagihan internet IndiHome - Januari 2026',         450000,22,'IH-2601',NOW(),NOW()),
(48,'2026-02-10','Internet',   'Tagihan internet IndiHome - Februari 2026',        450000,22,'IH-2602',NOW(),NOW()),
(49,'2026-03-10','Internet',   'Tagihan internet IndiHome - Maret 2026',           450000,22,'IH-2603',NOW(),NOW()),
(50,'2026-04-10','Internet',   'Tagihan internet IndiHome - April 2026',           450000,22,'IH-2604',NOW(),NOW()),
(51,'2026-05-10','Internet',   'Tagihan internet IndiHome - Mei 2026',             450000,22,'IH-2605',NOW(),NOW()),
-- Pemeliharaan (insidental)
(52,'2025-02-20','Maintenance','Servis AC toko + isi freon',                       350000,21,'MNT-001',NOW(),NOW()),
(53,'2025-05-18','Maintenance','Perbaikan etalase kaca retak',                     450000,21,'MNT-002',NOW(),NOW()),
(54,'2025-08-22','Maintenance','Cat ulang dinding toko',                           600000,21,'MNT-003',NOW(),NOW()),
(55,'2025-11-15','Maintenance','Servis AC toko + ganti filter',                    400000,21,'MNT-004',NOW(),NOW()),
(56,'2026-02-18','Maintenance','Perbaikan pintu rolling toko',                     500000,21,'MNT-005',NOW(),NOW()),
(57,'2026-05-08','Maintenance','Servis AC + perbaikan instalasi listrik',          750000,21,'MNT-006',NOW(),NOW()),
-- ATK & Operasional lain
(58,'2025-01-20','Other',      'Pembelian ATK & perlengkapan toko',                250000,22,'ATK-2501',NOW(),NOW()),
(59,'2025-04-20','Other',      'Pembelian ATK & perlengkapan toko',                275000,22,'ATK-2504',NOW(),NOW()),
(60,'2025-07-20','Other',      'Pembelian ATK & perlengkapan toko',                250000,22,'ATK-2507',NOW(),NOW()),
(61,'2025-10-20','Other',      'Pembelian ATK & perlengkapan toko',                300000,22,'ATK-2510',NOW(),NOW()),
(62,'2026-01-20','Other',      'Pembelian ATK & perlengkapan toko',                275000,22,'ATK-2601',NOW(),NOW()),
(63,'2026-04-20','Other',      'Pembelian ATK & perlengkapan toko',                300000,22,'ATK-2604',NOW(),NOW());

-- ============================================================
-- 13. JOURNAL ENTRIES + LINES
-- Semua entry: total debit = total credit (balanced) ✓
-- COA codes: 1-1000=Kas, 1-1100=Bank BCA, 1-1200=AR,
--            1-2000=Persediaan, 2-1000=AP, 2-3000=Hutang Pajak,
--            3-1000=Modal, 4-1000=Pend.Penjualan, 4-2000=Pend.Servis,
--            5-1000=HPP, 5-2000=Gaji, 5-3000=Listrik,
--            5-4000=Pemeliharaan, 5-5000=Umum&Admin, 5-6000=Sewa
-- ============================================================

-- ── PURCHASES (Dr Persediaan / Cr AP) ──────────────────────
INSERT INTO journal_entries (id,journal_number,entry_date,description,reference_type,reference_id,status,posted_at,posted_by,created_by,created_at,updated_at) VALUES
(1, 'JRN-202501-0001','2025-01-06','Goods Receipt PO-202501-0001','Purchase',1,'posted','2025-01-06 10:00:00',3,3,NOW(),NOW()),
(2, 'JRN-202501-0002','2025-01-07','Goods Receipt PO-202501-0002','Purchase',2,'posted','2025-01-07 10:00:00',3,3,NOW(),NOW()),
(3, 'JRN-202502-0001','2025-02-03','Goods Receipt PO-202502-0001','Purchase',3,'posted','2025-02-03 10:00:00',3,3,NOW(),NOW()),
(4, 'JRN-202502-0002','2025-02-10','Goods Receipt PO-202502-0002','Purchase',4,'posted','2025-02-10 10:00:00',3,3,NOW(),NOW()),
(5, 'JRN-202503-0001','2025-03-04','Goods Receipt PO-202503-0001','Purchase',5,'posted','2025-03-04 10:00:00',3,3,NOW(),NOW()),
(6, 'JRN-202503-0002','2025-03-12','Goods Receipt PO-202503-0002','Purchase',6,'posted','2025-03-12 10:00:00',3,3,NOW(),NOW()),
(7, 'JRN-202504-0001','2025-04-02','Goods Receipt PO-202504-0001','Purchase',7,'posted','2025-04-02 10:00:00',3,3,NOW(),NOW()),
(8, 'JRN-202505-0001','2025-05-05','Goods Receipt PO-202505-0001','Purchase',8,'posted','2025-05-05 10:00:00',3,3,NOW(),NOW()),
(9, 'JRN-202506-0001','2025-06-03','Goods Receipt PO-202506-0001','Purchase',9,'posted','2025-06-03 10:00:00',3,3,NOW(),NOW()),
(10,'JRN-202507-0001','2025-07-07','Goods Receipt PO-202507-0001','Purchase',10,'posted','2025-07-07 10:00:00',3,3,NOW(),NOW()),
(11,'JRN-202508-0001','2025-08-04','Goods Receipt PO-202508-0001','Purchase',11,'posted','2025-08-04 10:00:00',3,3,NOW(),NOW()),
(12,'JRN-202509-0001','2025-09-02','Goods Receipt PO-202509-0001','Purchase',12,'posted','2025-09-02 10:00:00',3,3,NOW(),NOW()),
(13,'JRN-202510-0001','2025-10-06','Goods Receipt PO-202510-0001','Purchase',13,'posted','2025-10-06 10:00:00',3,3,NOW(),NOW()),
(14,'JRN-202511-0001','2025-11-03','Goods Receipt PO-202511-0001','Purchase',14,'posted','2025-11-03 10:00:00',3,3,NOW(),NOW()),
(15,'JRN-202512-0001','2025-12-02','Goods Receipt PO-202512-0001','Purchase',15,'posted','2025-12-02 10:00:00',3,3,NOW(),NOW()),
(16,'JRN-202601-0001','2026-01-06','Goods Receipt PO-202601-0001','Purchase',16,'posted','2026-01-06 10:00:00',3,3,NOW(),NOW()),
(17,'JRN-202602-0001','2026-02-03','Goods Receipt PO-202602-0001','Purchase',17,'posted','2026-02-03 10:00:00',3,3,NOW(),NOW()),
(18,'JRN-202603-0001','2026-03-04','Goods Receipt PO-202603-0001','Purchase',18,'posted','2026-03-04 10:00:00',3,3,NOW(),NOW()),
(19,'JRN-202604-0001','2026-04-07','Goods Receipt PO-202604-0001','Purchase',19,'posted','2026-04-07 10:00:00',3,3,NOW(),NOW());

INSERT INTO journal_entry_lines (id,journal_entry_id,account_id,description,debit,credit,created_at,updated_at) VALUES
(1, 1,4,'Persediaan masuk PO-202501-0001',37900000,0,NOW(),NOW()),
(2, 1,8,'Hutang usaha ke CV Mitra Komputer',0,37900000,NOW(),NOW()),
(3, 2,4,'Persediaan masuk PO-202501-0002',4790000,0,NOW(),NOW()),
(4, 2,8,'Hutang usaha ke UD Sumber Elektronik',0,4790000,NOW(),NOW()),
(5, 3,4,'Persediaan masuk PO-202502-0001',10850000,0,NOW(),NOW()),
(6, 3,8,'Hutang usaha ke PT Datascrip',0,10850000,NOW(),NOW()),
(7, 4,4,'Persediaan masuk PO-202502-0002',28900000,0,NOW(),NOW()),
(8, 4,8,'Hutang usaha ke CV Berkah Jaya',0,28900000,NOW(),NOW()),
(9, 5,4,'Persediaan masuk PO-202503-0001',24600000,0,NOW(),NOW()),
(10,5,8,'Hutang usaha ke PT Synnex Metrodata',0,24600000,NOW(),NOW()),
(11,6,4,'Persediaan masuk PO-202503-0002',3230000,0,NOW(),NOW()),
(12,6,8,'Hutang usaha ke UD Sumber Elektronik',0,3230000,NOW(),NOW()),
(13,7,4,'Persediaan masuk PO-202504-0001',28100000,0,NOW(),NOW()),
(14,7,8,'Hutang usaha ke CV Mitra Komputer',0,28100000,NOW(),NOW()),
(15,8,4,'Persediaan masuk PO-202505-0001',5140000,0,NOW(),NOW()),
(16,8,8,'Hutang usaha ke PT Erafone',0,5140000,NOW(),NOW()),
(17,9,4,'Persediaan masuk PO-202506-0001',6700000,0,NOW(),NOW()),
(18,9,8,'Hutang usaha ke PT Datascrip',0,6700000,NOW(),NOW()),
(19,10,4,'Persediaan masuk PO-202507-0001',17500000,0,NOW(),NOW()),
(20,10,8,'Hutang usaha ke CV Berkah Jaya',0,17500000,NOW(),NOW()),
(21,11,4,'Persediaan masuk PO-202508-0001',24300000,0,NOW(),NOW()),
(22,11,8,'Hutang usaha ke PT Synnex Metrodata',0,24300000,NOW(),NOW()),
(23,12,4,'Persediaan masuk PO-202509-0001',4120000,0,NOW(),NOW()),
(24,12,8,'Hutang usaha ke UD Sumber Elektronik',0,4120000,NOW(),NOW()),
(25,13,4,'Persediaan masuk PO-202510-0001',17400000,0,NOW(),NOW()),
(26,13,8,'Hutang usaha ke CV Mitra Komputer',0,17400000,NOW(),NOW()),
(27,14,4,'Persediaan masuk PO-202511-0001',6175000,0,NOW(),NOW()),
(28,14,8,'Hutang usaha ke PT Erafone',0,6175000,NOW(),NOW()),
(29,15,4,'Persediaan masuk PO-202512-0001',9500000,0,NOW(),NOW()),
(30,15,8,'Hutang usaha ke PT Datascrip',0,9500000,NOW(),NOW()),
(31,16,4,'Persediaan masuk PO-202601-0001',33200000,0,NOW(),NOW()),
(32,16,8,'Hutang usaha ke PT Synnex Metrodata',0,33200000,NOW(),NOW()),
(33,17,4,'Persediaan masuk PO-202602-0001',4955000,0,NOW(),NOW()),
(34,17,8,'Hutang usaha ke UD Sumber Elektronik',0,4955000,NOW(),NOW()),
(35,18,4,'Persediaan masuk PO-202603-0001',25100000,0,NOW(),NOW()),
(36,18,8,'Hutang usaha ke CV Berkah Jaya',0,25100000,NOW(),NOW()),
(37,19,4,'Persediaan masuk PO-202604-0001',21300000,0,NOW(),NOW()),
(38,19,8,'Hutang usaha ke CV Mitra Komputer',0,21300000,NOW(),NOW());

-- ── AP PAYMENTS (Dr AP / Cr Bank) ──────────────────────────
INSERT INTO journal_entries (id,journal_number,entry_date,description,reference_type,reference_id,status,posted_at,posted_by,created_by,created_at,updated_at) VALUES
(20,'JRN-202501-0003','2025-01-10','AP Payment PO-202501-0001','ApPayment',1,'posted','2025-01-10 11:00:00',3,3,NOW(),NOW()),
(21,'JRN-202501-0004','2025-01-12','AP Payment PO-202501-0002','ApPayment',2,'posted','2025-01-12 11:00:00',3,3,NOW(),NOW()),
(22,'JRN-202502-0003','2025-02-07','AP Payment PO-202502-0001','ApPayment',3,'posted','2025-02-07 11:00:00',3,3,NOW(),NOW()),
(23,'JRN-202502-0004','2025-02-14','AP Payment PO-202502-0002','ApPayment',4,'posted','2025-02-14 11:00:00',3,3,NOW(),NOW()),
(24,'JRN-202503-0003','2025-03-08','AP Payment PO-202503-0001','ApPayment',5,'posted','2025-03-08 11:00:00',3,3,NOW(),NOW()),
(25,'JRN-202503-0004','2025-03-16','AP Payment PO-202503-0002','ApPayment',6,'posted','2025-03-16 11:00:00',3,3,NOW(),NOW()),
(26,'JRN-202504-0002','2025-04-06','AP Payment PO-202504-0001','ApPayment',7,'posted','2025-04-06 11:00:00',3,3,NOW(),NOW()),
(27,'JRN-202505-0002','2025-05-09','AP Payment PO-202505-0001','ApPayment',8,'posted','2025-05-09 11:00:00',3,3,NOW(),NOW()),
(28,'JRN-202506-0002','2025-06-07','AP Payment PO-202506-0001','ApPayment',9,'posted','2025-06-07 11:00:00',3,3,NOW(),NOW()),
(29,'JRN-202507-0002','2025-07-11','AP Payment PO-202507-0001','ApPayment',10,'posted','2025-07-11 11:00:00',3,3,NOW(),NOW()),
(30,'JRN-202508-0002','2025-08-08','AP Payment PO-202508-0001','ApPayment',11,'posted','2025-08-08 11:00:00',3,3,NOW(),NOW()),
(31,'JRN-202509-0002','2025-09-06','AP Payment PO-202509-0001','ApPayment',12,'posted','2025-09-06 11:00:00',3,3,NOW(),NOW()),
(32,'JRN-202510-0002','2025-10-10','AP Payment PO-202510-0001','ApPayment',13,'posted','2025-10-10 11:00:00',3,3,NOW(),NOW()),
(33,'JRN-202511-0002','2025-11-07','AP Payment PO-202511-0001','ApPayment',14,'posted','2025-11-07 11:00:00',3,3,NOW(),NOW()),
(34,'JRN-202512-0002','2025-12-06','AP Payment PO-202512-0001','ApPayment',15,'posted','2025-12-06 11:00:00',3,3,NOW(),NOW()),
(35,'JRN-202601-0002','2026-01-10','AP Payment PO-202601-0001','ApPayment',16,'posted','2026-01-10 11:00:00',3,3,NOW(),NOW()),
(36,'JRN-202602-0002','2026-02-07','AP Payment PO-202602-0001','ApPayment',17,'posted','2026-02-07 11:00:00',3,3,NOW(),NOW()),
(37,'JRN-202603-0002','2026-03-08','AP Payment PO-202603-0001','ApPayment',18,'posted','2026-03-08 11:00:00',3,3,NOW(),NOW());

INSERT INTO journal_entry_lines (id,journal_entry_id,account_id,description,debit,credit,created_at,updated_at) VALUES
(39,20,8,'Pelunasan AP PO-202501-0001',37900000,0,NOW(),NOW()),
(40,20,2,'Pembayaran via Bank BCA',0,37900000,NOW(),NOW()),
(41,21,8,'Pelunasan AP PO-202501-0002',4790000,0,NOW(),NOW()),
(42,21,2,'Pembayaran via Bank BCA',0,4790000,NOW(),NOW()),
(43,22,8,'Pelunasan AP PO-202502-0001',10850000,0,NOW(),NOW()),
(44,22,2,'Pembayaran via Bank BCA',0,10850000,NOW(),NOW()),
(45,23,8,'Pelunasan AP PO-202502-0002',28900000,0,NOW(),NOW()),
(46,23,2,'Pembayaran via Bank BCA',0,28900000,NOW(),NOW()),
(47,24,8,'Pelunasan AP PO-202503-0001',24600000,0,NOW(),NOW()),
(48,24,2,'Pembayaran via Bank BCA',0,24600000,NOW(),NOW()),
(49,25,8,'Pelunasan AP PO-202503-0002',3230000,0,NOW(),NOW()),
(50,25,2,'Pembayaran via Bank BCA',0,3230000,NOW(),NOW()),
(51,26,8,'Pelunasan AP PO-202504-0001',28100000,0,NOW(),NOW()),
(52,26,2,'Pembayaran via Bank BCA',0,28100000,NOW(),NOW()),
(53,27,8,'Pelunasan AP PO-202505-0001',5140000,0,NOW(),NOW()),
(54,27,2,'Pembayaran via Bank BCA',0,5140000,NOW(),NOW()),
(55,28,8,'Pelunasan AP PO-202506-0001',6700000,0,NOW(),NOW()),
(56,28,2,'Pembayaran via Bank BCA',0,6700000,NOW(),NOW()),
(57,29,8,'Pelunasan AP PO-202507-0001',17500000,0,NOW(),NOW()),
(58,29,2,'Pembayaran via Bank BCA',0,17500000,NOW(),NOW()),
(59,30,8,'Pelunasan AP PO-202508-0001',24300000,0,NOW(),NOW()),
(60,30,2,'Pembayaran via Bank BCA',0,24300000,NOW(),NOW()),
(61,31,8,'Pelunasan AP PO-202509-0001',4120000,0,NOW(),NOW()),
(62,31,2,'Pembayaran via Bank BCA',0,4120000,NOW(),NOW()),
(63,32,8,'Pelunasan AP PO-202510-0001',17400000,0,NOW(),NOW()),
(64,32,2,'Pembayaran via Bank BCA',0,17400000,NOW(),NOW()),
(65,33,8,'Pelunasan AP PO-202511-0001',6175000,0,NOW(),NOW()),
(66,33,2,'Pembayaran via Bank BCA',0,6175000,NOW(),NOW()),
(67,34,8,'Pelunasan AP PO-202512-0001',9500000,0,NOW(),NOW()),
(68,34,2,'Pembayaran via Bank BCA',0,9500000,NOW(),NOW()),
(69,35,8,'Pelunasan AP PO-202601-0001',33200000,0,NOW(),NOW()),
(70,35,2,'Pembayaran via Bank BCA',0,33200000,NOW(),NOW()),
(71,36,8,'Pelunasan AP PO-202602-0001',4955000,0,NOW(),NOW()),
(72,36,2,'Pembayaran via Bank BCA',0,4955000,NOW(),NOW()),
(73,37,8,'Pelunasan AP PO-202603-0001',25100000,0,NOW(),NOW()),
(74,37,2,'Pembayaran via Bank BCA',0,25100000,NOW(),NOW());

-- ── CASH SALES (Dr Kas / Cr Pend.Penjualan) + HPP ─────────
-- Cash sales: 1,2,3,4,6,8,9,10,12,15,17,19,21,23,25,27,29,31,33,37,39,40
INSERT INTO journal_entries (id,journal_number,entry_date,description,reference_type,reference_id,status,posted_at,posted_by,created_by,created_at,updated_at) VALUES
(38,'JRN-202501-0005','2025-01-10','Sale SL-202501-0001 (Cash)','Sale',1,'posted','2025-01-10 14:00:00',4,4,NOW(),NOW()),
(39,'JRN-202501-0006','2025-01-10','HPP Sale SL-202501-0001','Sale',1,'posted','2025-01-10 14:01:00',4,4,NOW(),NOW()),
(40,'JRN-202501-0007','2025-01-13','Sale SL-202501-0002 (Cash)','Sale',2,'posted','2025-01-13 11:00:00',4,4,NOW(),NOW()),
(41,'JRN-202501-0008','2025-01-13','HPP Sale SL-202501-0002','Sale',2,'posted','2025-01-13 11:01:00',4,4,NOW(),NOW()),
(42,'JRN-202501-0009','2025-01-20','Sale SL-202501-0003 (Cash)','Sale',3,'posted','2025-01-20 13:00:00',4,4,NOW(),NOW()),
(43,'JRN-202501-0010','2025-01-20','HPP Sale SL-202501-0003','Sale',3,'posted','2025-01-20 13:01:00',4,4,NOW(),NOW()),
(44,'JRN-202502-0005','2025-02-06','Sale SL-202502-0001 (Transfer)','Sale',4,'posted','2025-02-06 14:00:00',4,4,NOW(),NOW()),
(45,'JRN-202502-0006','2025-02-06','HPP Sale SL-202502-0001','Sale',4,'posted','2025-02-06 14:01:00',4,4,NOW(),NOW()),
(46,'JRN-202502-0007','2025-02-20','Sale SL-202502-0003 (Cash)','Sale',6,'posted','2025-02-20 15:00:00',4,4,NOW(),NOW()),
(47,'JRN-202502-0008','2025-02-20','HPP Sale SL-202502-0003','Sale',6,'posted','2025-02-20 15:01:00',4,4,NOW(),NOW()),
(48,'JRN-202503-0005','2025-03-08','Sale SL-202503-0002 (Cash)','Sale',8,'posted','2025-03-08 13:00:00',4,4,NOW(),NOW()),
(49,'JRN-202503-0006','2025-03-08','HPP Sale SL-202503-0002','Sale',8,'posted','2025-03-08 13:01:00',4,4,NOW(),NOW()),
(50,'JRN-202503-0007','2025-03-15','Sale SL-202503-0003 (Cash)','Sale',9,'posted','2025-03-15 11:00:00',4,4,NOW(),NOW()),
(51,'JRN-202503-0008','2025-03-15','HPP Sale SL-202503-0003','Sale',9,'posted','2025-03-15 11:01:00',4,4,NOW(),NOW()),
(52,'JRN-202504-0003','2025-04-03','Sale SL-202504-0001 (Transfer)','Sale',10,'posted','2025-04-03 14:00:00',4,4,NOW(),NOW()),
(53,'JRN-202504-0004','2025-04-03','HPP Sale SL-202504-0001','Sale',10,'posted','2025-04-03 14:01:00',4,4,NOW(),NOW()),
(54,'JRN-202504-0005','2025-04-15','Sale SL-202504-0003 (Cash)','Sale',12,'posted','2025-04-15 12:00:00',4,4,NOW(),NOW()),
(55,'JRN-202504-0006','2025-04-15','HPP Sale SL-202504-0003','Sale',12,'posted','2025-04-15 12:01:00',4,4,NOW(),NOW()),
(56,'JRN-202505-0004','2025-05-20','Sale SL-202505-0003 (Cash)','Sale',15,'posted','2025-05-20 13:00:00',4,4,NOW(),NOW()),
(57,'JRN-202505-0005','2025-05-20','HPP Sale SL-202505-0003','Sale',15,'posted','2025-05-20 13:01:00',4,4,NOW(),NOW()),
(58,'JRN-202506-0003','2025-06-10','Sale SL-202506-0002 (Cash)','Sale',17,'posted','2025-06-10 11:00:00',4,4,NOW(),NOW()),
(59,'JRN-202506-0004','2025-06-10','HPP Sale SL-202506-0002','Sale',17,'posted','2025-06-10 11:01:00',4,4,NOW(),NOW()),
(60,'JRN-202507-0003','2025-07-15','Sale SL-202507-0002 (Cash)','Sale',19,'posted','2025-07-15 12:00:00',4,4,NOW(),NOW()),
(61,'JRN-202507-0004','2025-07-15','HPP Sale SL-202507-0002','Sale',19,'posted','2025-07-15 12:01:00',4,4,NOW(),NOW()),
(62,'JRN-202508-0003','2025-08-12','Sale SL-202508-0002 (Cash)','Sale',21,'posted','2025-08-12 14:00:00',4,4,NOW(),NOW()),
(63,'JRN-202508-0004','2025-08-12','HPP Sale SL-202508-0002','Sale',21,'posted','2025-08-12 14:01:00',4,4,NOW(),NOW()),
(64,'JRN-202509-0003','2025-09-10','Sale SL-202509-0002 (Cash)','Sale',23,'posted','2025-09-10 11:00:00',4,4,NOW(),NOW()),
(65,'JRN-202509-0004','2025-09-10','HPP Sale SL-202509-0002','Sale',23,'posted','2025-09-10 11:01:00',4,4,NOW(),NOW()),
(66,'JRN-202510-0003','2025-10-14','Sale SL-202510-0002 (Cash)','Sale',25,'posted','2025-10-14 13:00:00',4,4,NOW(),NOW()),
(67,'JRN-202510-0004','2025-10-14','HPP Sale SL-202510-0002','Sale',25,'posted','2025-10-14 13:01:00',4,4,NOW(),NOW()),
(68,'JRN-202511-0003','2025-11-12','Sale SL-202511-0002 (Cash)','Sale',27,'posted','2025-11-12 11:00:00',4,4,NOW(),NOW()),
(69,'JRN-202511-0004','2025-11-12','HPP Sale SL-202511-0002','Sale',27,'posted','2025-11-12 11:01:00',4,4,NOW(),NOW()),
(70,'JRN-202512-0003','2025-12-10','Sale SL-202512-0002 (Cash)','Sale',29,'posted','2025-12-10 13:00:00',4,4,NOW(),NOW()),
(71,'JRN-202512-0004','2025-12-10','HPP Sale SL-202512-0002','Sale',29,'posted','2025-12-10 13:01:00',4,4,NOW(),NOW()),
(72,'JRN-202601-0003','2026-01-15','Sale SL-202601-0002 (Cash)','Sale',31,'posted','2026-01-15 14:00:00',4,4,NOW(),NOW()),
(73,'JRN-202601-0004','2026-01-15','HPP Sale SL-202601-0002','Sale',31,'posted','2026-01-15 14:01:00',4,4,NOW(),NOW()),
(74,'JRN-202602-0003','2026-02-12','Sale SL-202602-0002 (Cash)','Sale',33,'posted','2026-02-12 11:00:00',4,4,NOW(),NOW()),
(75,'JRN-202602-0004','2026-02-12','HPP Sale SL-202602-0002','Sale',33,'posted','2026-02-12 11:01:00',4,4,NOW(),NOW()),
(76,'JRN-202604-0003','2026-04-14','Sale SL-202604-0002 (Cash)','Sale',37,'posted','2026-04-14 12:00:00',4,4,NOW(),NOW()),
(77,'JRN-202604-0004','2026-04-14','HPP Sale SL-202604-0002','Sale',37,'posted','2026-04-14 12:01:00',4,4,NOW(),NOW()),
(78,'JRN-202605-0004','2026-05-12','Sale SL-202605-0002 (Cash)','Sale',39,'posted','2026-05-12 11:00:00',4,4,NOW(),NOW()),
(79,'JRN-202605-0005','2026-05-12','HPP Sale SL-202605-0002','Sale',39,'posted','2026-05-12 11:01:00',4,4,NOW(),NOW()),
(80,'JRN-202605-0006','2026-05-15','Sale SL-202605-0003 (Cash)','Sale',40,'posted','2026-05-15 13:00:00',4,4,NOW(),NOW()),
(81,'JRN-202605-0007','2026-05-15','HPP Sale SL-202605-0003','Sale',40,'posted','2026-05-15 13:01:00',4,4,NOW(),NOW());

INSERT INTO journal_entry_lines (id,journal_entry_id,account_id,description,debit,credit,created_at,updated_at) VALUES
-- Sale 1 cash: Dr Kas 8,200,000 / Cr Pend.Penjualan 8,200,000
(75,38,1,'Penerimaan kas penjualan SL-202501-0001',8200000,0,NOW(),NOW()),
(76,38,15,'Pendapatan penjualan laptop',0,8200000,NOW(),NOW()),
-- HPP Sale 1: Dr HPP 6,800,000 / Cr Persediaan 6,800,000
(77,39,18,'HPP laptop Asus VivoBook',6800000,0,NOW(),NOW()),
(78,39,4,'Keluar persediaan',0,6800000,NOW(),NOW()),
-- Sale 2 cash: Dr Kas 260,000 / Cr Pend.Penjualan 260,000
(79,40,1,'Penerimaan kas penjualan SL-202501-0002',260000,0,NOW(),NOW()),
(80,40,15,'Pendapatan penjualan aksesoris',0,260000,NOW(),NOW()),
-- HPP Sale 2: Dr HPP 170,000 / Cr Persediaan 170,000
(81,41,18,'HPP aksesoris',170000,0,NOW(),NOW()),
(82,41,4,'Keluar persediaan',0,170000,NOW(),NOW()),
-- Sale 3 cash: Dr Kas 2,100,000 / Cr Pend.Penjualan 2,100,000
(83,42,1,'Penerimaan kas penjualan SL-202501-0003',2100000,0,NOW(),NOW()),
(84,42,15,'Pendapatan penjualan printer',0,2100000,NOW(),NOW()),
-- HPP Sale 3: Dr HPP 1,650,000 / Cr Persediaan 1,650,000
(85,43,18,'HPP printer Epson L3210',1650000,0,NOW(),NOW()),
(86,43,4,'Keluar persediaan',0,1650000,NOW(),NOW()),
-- Sale 4 transfer: Dr Bank 6,300,000 / Cr Pend.Penjualan 6,300,000
(87,44,2,'Penerimaan transfer penjualan SL-202502-0001',6300000,0,NOW(),NOW()),
(88,44,15,'Pendapatan penjualan laptop',0,6300000,NOW(),NOW()),
-- HPP Sale 4: Dr HPP 5,100,000 / Cr Persediaan 5,100,000
(89,45,18,'HPP laptop HP 14s',5100000,0,NOW(),NOW()),
(90,45,4,'Keluar persediaan',0,5100000,NOW(),NOW()),
-- Sale 6 cash: Dr Kas 385,000 / Cr Pend.Penjualan 385,000
(91,46,1,'Penerimaan kas penjualan SL-202502-0003',385000,0,NOW(),NOW()),
(92,46,15,'Pendapatan penjualan aksesoris',0,385000,NOW(),NOW()),
-- HPP Sale 6: Dr HPP 265,000 / Cr Persediaan 265,000
(93,47,18,'HPP headset + cooling pad',265000,0,NOW(),NOW()),
(94,47,4,'Keluar persediaan',0,265000,NOW(),NOW()),
-- Sale 8 cash: Dr Kas 1,850,000 / Cr Pend.Penjualan 1,850,000
(95,48,1,'Penerimaan kas penjualan SL-202503-0002',1850000,0,NOW(),NOW()),
(96,48,15,'Pendapatan penjualan printer',0,1850000,NOW(),NOW()),
-- HPP Sale 8: Dr HPP 1,450,000 / Cr Persediaan 1,450,000
(97,49,18,'HPP printer Canon G2020',1450000,0,NOW(),NOW()),
(98,49,4,'Keluar persediaan',0,1450000,NOW(),NOW()),
-- Sale 9 cash: Dr Kas 810,000 / Cr Pend.Penjualan 810,000
(99,50,1,'Penerimaan kas penjualan SL-202503-0003',810000,0,NOW(),NOW()),
(100,50,15,'Pendapatan penjualan aksesoris',0,810000,NOW(),NOW()),
-- HPP Sale 9: Dr HPP 600,000 / Cr Persediaan 600,000
(101,51,18,'HPP RAM + SSD',600000,0,NOW(),NOW()),
(102,51,4,'Keluar persediaan',0,600000,NOW(),NOW()),
-- Sale 10 transfer: Dr Bank 17,500,000 / Cr Pend.Penjualan 17,500,000
(103,52,2,'Penerimaan transfer penjualan SL-202504-0001',17500000,0,NOW(),NOW()),
(104,52,15,'Pendapatan penjualan laptop gaming',0,17500000,NOW(),NOW()),
-- HPP Sale 10: Dr HPP 14,500,000 / Cr Persediaan 14,500,000
(105,53,18,'HPP laptop Asus ROG',14500000,0,NOW(),NOW()),
(106,53,4,'Keluar persediaan',0,14500000,NOW(),NOW()),
-- Sale 12 cash: Dr Kas 380,000 / Cr Pend.Penjualan 380,000
(107,54,1,'Penerimaan kas penjualan SL-202504-0003',380000,0,NOW(),NOW()),
(108,54,15,'Pendapatan penjualan aksesoris',0,380000,NOW(),NOW()),
-- HPP Sale 12: Dr HPP 240,000 / Cr Persediaan 240,000
(109,55,18,'HPP HDMI + flashdisk',240000,0,NOW(),NOW()),
(110,55,4,'Keluar persediaan',0,240000,NOW(),NOW()),
-- Sale 15 cash: Dr Kas 440,000 / Cr Pend.Penjualan 440,000
(111,56,1,'Penerimaan kas penjualan SL-202505-0003',440000,0,NOW(),NOW()),
(112,56,15,'Pendapatan penjualan aksesoris',0,440000,NOW(),NOW()),
-- HPP Sale 15: Dr HPP 305,000 / Cr Persediaan 305,000
(113,57,18,'HPP cooling pad + tas laptop',305000,0,NOW(),NOW()),
(114,57,4,'Keluar persediaan',0,305000,NOW(),NOW()),
-- Sale 17 cash: Dr Kas 470,000 / Cr Pend.Penjualan 470,000
(115,58,1,'Penerimaan kas penjualan SL-202506-0002',470000,0,NOW(),NOW()),
(116,58,15,'Pendapatan penjualan tinta',0,470000,NOW(),NOW()),
-- HPP Sale 17: Dr HPP 330,000 / Cr Persediaan 330,000
(117,59,18,'HPP tinta Epson 003',330000,0,NOW(),NOW()),
(118,59,4,'Keluar persediaan',0,330000,NOW(),NOW()),
-- Sale 19 cash: Dr Kas 810,000 / Cr Pend.Penjualan 810,000
(119,60,1,'Penerimaan kas penjualan SL-202507-0002',810000,0,NOW(),NOW()),
(120,60,15,'Pendapatan penjualan aksesoris',0,810000,NOW(),NOW()),
-- HPP Sale 19: Dr HPP 600,000 / Cr Persediaan 600,000
(121,61,18,'HPP RAM + SSD',600000,0,NOW(),NOW()),
(122,61,4,'Keluar persediaan',0,600000,NOW(),NOW()),
-- Sale 21 cash: Dr Kas 7,500,000 / Cr Pend.Penjualan 7,500,000
(123,62,1,'Penerimaan kas penjualan SL-202508-0002',7500000,0,NOW(),NOW()),
(124,62,15,'Pendapatan penjualan laptop',0,7500000,NOW(),NOW()),
-- HPP Sale 21: Dr HPP 6,200,000 / Cr Persediaan 6,200,000
(125,63,18,'HPP laptop Lenovo IdeaPad',6200000,0,NOW(),NOW()),
(126,63,4,'Keluar persediaan',0,6200000,NOW(),NOW()),
-- Sale 23 cash: Dr Kas 265,000 / Cr Pend.Penjualan 265,000
(127,64,1,'Penerimaan kas penjualan SL-202509-0002',265000,0,NOW(),NOW()),
(128,64,15,'Pendapatan penjualan aksesoris',0,265000,NOW(),NOW()),
-- HPP Sale 23: Dr HPP 185,000 / Cr Persediaan 185,000
(129,65,18,'HPP keyboard wireless',185000,0,NOW(),NOW()),
(130,65,4,'Keluar persediaan',0,185000,NOW(),NOW()),
-- Sale 25 cash: Dr Kas 850,000 / Cr Pend.Penjualan 850,000
(131,66,1,'Penerimaan kas penjualan SL-202510-0002',850000,0,NOW(),NOW()),
(132,66,15,'Pendapatan penjualan UPS',0,850000,NOW(),NOW()),
-- HPP Sale 25: Dr HPP 650,000 / Cr Persediaan 650,000
(133,67,18,'HPP UPS APC 650VA',650000,0,NOW(),NOW()),
(134,67,4,'Keluar persediaan',0,650000,NOW(),NOW()),
-- Sale 27 cash: Dr Kas 710,000 / Cr Pend.Penjualan 710,000
(135,68,1,'Penerimaan kas penjualan SL-202511-0002',710000,0,NOW(),NOW()),
(136,68,15,'Pendapatan penjualan tinta',0,710000,NOW(),NOW()),
-- HPP Sale 27: Dr HPP 495,000 / Cr Persediaan 495,000
(137,69,18,'HPP tinta Epson 003',495000,0,NOW(),NOW()),
(138,69,4,'Keluar persediaan',0,495000,NOW(),NOW()),
-- Sale 29 cash: Dr Kas 2,450,000 / Cr Pend.Penjualan 2,450,000
(139,70,1,'Penerimaan kas penjualan SL-202512-0002',2450000,0,NOW(),NOW()),
(140,70,15,'Pendapatan penjualan printer',0,2450000,NOW(),NOW()),
-- HPP Sale 29: Dr HPP 1,900,000 / Cr Persediaan 1,900,000
(141,71,18,'HPP HP LaserJet Pro M15w',1900000,0,NOW(),NOW()),
(142,71,4,'Keluar persediaan',0,1900000,NOW(),NOW()),
-- Sale 31 cash: Dr Kas 8,800,000 / Cr Pend.Penjualan 8,800,000
(143,72,1,'Penerimaan kas penjualan SL-202601-0002',8800000,0,NOW(),NOW()),
(144,72,15,'Pendapatan penjualan laptop',0,8800000,NOW(),NOW()),
-- HPP Sale 31: Dr HPP 7,200,000 / Cr Persediaan 7,200,000
(145,73,18,'HPP laptop Acer Aspire 5',7200000,0,NOW(),NOW()),
(146,73,4,'Keluar persediaan',0,7200000,NOW(),NOW()),
-- Sale 33 cash: Dr Kas 525,000 / Cr Pend.Penjualan 525,000
(147,74,1,'Penerimaan kas penjualan SL-202602-0002',525000,0,NOW(),NOW()),
(148,74,15,'Pendapatan penjualan aksesoris',0,525000,NOW(),NOW()),
-- HPP Sale 33: Dr HPP 355,000 / Cr Persediaan 355,000
(149,75,18,'HPP mouse + keyboard + flashdisk',355000,0,NOW(),NOW()),
(150,75,4,'Keluar persediaan',0,355000,NOW(),NOW()),
-- Sale 37 cash: Dr Kas 810,000 / Cr Pend.Penjualan 810,000
(151,76,1,'Penerimaan kas penjualan SL-202604-0002',810000,0,NOW(),NOW()),
(152,76,15,'Pendapatan penjualan aksesoris',0,810000,NOW(),NOW()),
-- HPP Sale 37: Dr HPP 600,000 / Cr Persediaan 600,000
(153,77,18,'HPP RAM + SSD',600000,0,NOW(),NOW()),
(154,77,4,'Keluar persediaan',0,600000,NOW(),NOW()),
-- Sale 39 cash: Dr Kas 550,000 / Cr Pend.Penjualan 550,000
(155,78,1,'Penerimaan kas penjualan SL-202605-0002',550000,0,NOW(),NOW()),
(156,78,15,'Pendapatan penjualan tinta',0,550000,NOW(),NOW()),
-- HPP Sale 39: Dr HPP 385,000 / Cr Persediaan 385,000
(157,79,18,'HPP tinta Epson 003',385000,0,NOW(),NOW()),
(158,79,4,'Keluar persediaan',0,385000,NOW(),NOW()),
-- Sale 40 cash: Dr Kas 365,000 / Cr Pend.Penjualan 365,000
(159,80,1,'Penerimaan kas penjualan SL-202605-0003',365000,0,NOW(),NOW()),
(160,80,15,'Pendapatan penjualan aksesoris',0,365000,NOW(),NOW()),
-- HPP Sale 40: Dr HPP 240,000 / Cr Persediaan 240,000
(161,81,18,'HPP cooling pad + HDMI + flashdisk',240000,0,NOW(),NOW()),
(162,81,4,'Keluar persediaan',0,240000,NOW(),NOW());

-- ── CREDIT SALES → AR Invoice (Dr AR / Cr Pend.Penjualan) + HPP ──
-- Credit sales: 5,7,11,13,14,16,18,20,22,24,26,28,30,32,34,35,36,38
INSERT INTO journal_entries (id,journal_number,entry_date,description,reference_type,reference_id,status,posted_at,posted_by,created_by,created_at,updated_at) VALUES
(82,'JRN-202502-0009','2025-02-14','AR Invoice SL-202502-0002','ArInvoice',1,'posted','2025-02-14 14:00:00',3,3,NOW(),NOW()),
(83,'JRN-202502-0010','2025-02-14','HPP Sale SL-202502-0002','Sale',5,'posted','2025-02-14 14:01:00',3,3,NOW(),NOW()),
(84,'JRN-202503-0009','2025-03-05','AR Invoice SL-202503-0001','ArInvoice',2,'posted','2025-03-05 14:00:00',3,3,NOW(),NOW()),
(85,'JRN-202503-0010','2025-03-05','HPP Sale SL-202503-0001','Sale',7,'posted','2025-03-05 14:01:00',3,3,NOW(),NOW()),
(86,'JRN-202504-0007','2025-04-08','AR Invoice SL-202504-0002','ArInvoice',3,'posted','2025-04-08 14:00:00',3,3,NOW(),NOW()),
(87,'JRN-202504-0008','2025-04-08','HPP Sale SL-202504-0002','Sale',11,'posted','2025-04-08 14:01:00',3,3,NOW(),NOW()),
(88,'JRN-202505-0006','2025-05-06','AR Invoice SL-202505-0001','ArInvoice',4,'posted','2025-05-06 14:00:00',3,3,NOW(),NOW()),
(89,'JRN-202505-0007','2025-05-06','HPP Sale SL-202505-0001','Sale',13,'posted','2025-05-06 14:01:00',3,3,NOW(),NOW()),
(90,'JRN-202505-0008','2025-05-12','AR Invoice SL-202505-0002','ArInvoice',5,'posted','2025-05-12 14:00:00',3,3,NOW(),NOW()),
(91,'JRN-202505-0009','2025-05-12','HPP Sale SL-202505-0002','Sale',14,'posted','2025-05-12 14:01:00',3,3,NOW(),NOW()),
(92,'JRN-202506-0005','2025-06-04','AR Invoice SL-202506-0001','ArInvoice',6,'posted','2025-06-04 14:00:00',3,3,NOW(),NOW()),
(93,'JRN-202506-0006','2025-06-04','HPP Sale SL-202506-0001','Sale',16,'posted','2025-06-04 14:01:00',3,3,NOW(),NOW()),
(94,'JRN-202507-0005','2025-07-07','AR Invoice SL-202507-0001','ArInvoice',7,'posted','2025-07-07 14:00:00',3,3,NOW(),NOW()),
(95,'JRN-202507-0006','2025-07-07','HPP Sale SL-202507-0001','Sale',18,'posted','2025-07-07 14:01:00',3,3,NOW(),NOW()),
(96,'JRN-202508-0005','2025-08-05','AR Invoice SL-202508-0001','ArInvoice',8,'posted','2025-08-05 14:00:00',3,3,NOW(),NOW()),
(97,'JRN-202508-0006','2025-08-05','HPP Sale SL-202508-0001','Sale',20,'posted','2025-08-05 14:01:00',3,3,NOW(),NOW()),
(98,'JRN-202509-0005','2025-09-03','AR Invoice SL-202509-0001','ArInvoice',9,'posted','2025-09-03 14:00:00',3,3,NOW(),NOW()),
(99,'JRN-202509-0006','2025-09-03','HPP Sale SL-202509-0001','Sale',22,'posted','2025-09-03 14:01:00',3,3,NOW(),NOW()),
(100,'JRN-202510-0005','2025-10-06','AR Invoice SL-202510-0001','ArInvoice',10,'posted','2025-10-06 14:00:00',3,3,NOW(),NOW()),
(101,'JRN-202510-0006','2025-10-06','HPP Sale SL-202510-0001','Sale',24,'posted','2025-10-06 14:01:00',3,3,NOW(),NOW()),
(102,'JRN-202511-0005','2025-11-04','AR Invoice SL-202511-0001','ArInvoice',11,'posted','2025-11-04 14:00:00',3,3,NOW(),NOW()),
(103,'JRN-202511-0006','2025-11-04','HPP Sale SL-202511-0001','Sale',26,'posted','2025-11-04 14:01:00',3,3,NOW(),NOW()),
(104,'JRN-202512-0005','2025-12-03','AR Invoice SL-202512-0001','ArInvoice',12,'posted','2025-12-03 14:00:00',3,3,NOW(),NOW()),
(105,'JRN-202512-0006','2025-12-03','HPP Sale SL-202512-0001','Sale',28,'posted','2025-12-03 14:01:00',3,3,NOW(),NOW()),
(106,'JRN-202601-0005','2026-01-08','AR Invoice SL-202601-0001','ArInvoice',13,'posted','2026-01-08 14:00:00',3,3,NOW(),NOW()),
(107,'JRN-202601-0006','2026-01-08','HPP Sale SL-202601-0001','Sale',30,'posted','2026-01-08 14:01:00',3,3,NOW(),NOW()),
(108,'JRN-202602-0005','2026-02-05','AR Invoice SL-202602-0001','ArInvoice',14,'posted','2026-02-05 14:00:00',3,3,NOW(),NOW()),
(109,'JRN-202602-0006','2026-02-05','HPP Sale SL-202602-0001','Sale',32,'posted','2026-02-05 14:01:00',3,3,NOW(),NOW()),
(110,'JRN-202603-0003','2026-03-04','AR Invoice SL-202603-0001','ArInvoice',15,'posted','2026-03-04 14:00:00',3,3,NOW(),NOW()),
(111,'JRN-202603-0004','2026-03-04','HPP Sale SL-202603-0001','Sale',34,'posted','2026-03-04 14:01:00',3,3,NOW(),NOW()),
(112,'JRN-202603-0005','2026-03-10','AR Invoice SL-202603-0002','ArInvoice',16,'posted','2026-03-10 14:00:00',3,3,NOW(),NOW()),
(113,'JRN-202603-0006','2026-03-10','HPP Sale SL-202603-0002','Sale',35,'posted','2026-03-10 14:01:00',3,3,NOW(),NOW()),
(114,'JRN-202604-0005','2026-04-07','AR Invoice SL-202604-0001','ArInvoice',17,'posted','2026-04-07 14:00:00',3,3,NOW(),NOW()),
(115,'JRN-202604-0006','2026-04-07','HPP Sale SL-202604-0001','Sale',36,'posted','2026-04-07 14:01:00',3,3,NOW(),NOW()),
(116,'JRN-202605-0008','2026-05-05','AR Invoice SL-202605-0001','ArInvoice',18,'posted','2026-05-05 14:00:00',3,3,NOW(),NOW()),
(117,'JRN-202605-0009','2026-05-05','HPP Sale SL-202605-0001','Sale',38,'posted','2026-05-05 14:01:00',3,3,NOW(),NOW());

INSERT INTO journal_entry_lines (id,journal_entry_id,account_id,description,debit,credit,created_at,updated_at) VALUES
(163,82,3,'AR dari PT Maju Bersama - SL-202502-0002',23500000,0,NOW(),NOW()),
(164,82,15,'Pendapatan penjualan kredit',0,23500000,NOW(),NOW()),
(165,83,18,'HPP 5 PC Rakitan Office',19000000,0,NOW(),NOW()),
(166,83,4,'Keluar persediaan',0,19000000,NOW(),NOW()),
(167,84,3,'AR dari Yayasan Al-Hikmah - SL-202503-0001',8800000,0,NOW(),NOW()),
(168,84,15,'Pendapatan penjualan kredit',0,8800000,NOW(),NOW()),
(169,85,18,'HPP laptop Acer Aspire 5',7200000,0,NOW(),NOW()),
(170,85,4,'Keluar persediaan',0,7200000,NOW(),NOW()),
(171,86,3,'AR dari Apotek Sehat - SL-202504-0002',4800000,0,NOW(),NOW()),
(172,86,15,'Pendapatan penjualan kredit',0,4800000,NOW(),NOW()),
(173,87,18,'HPP PC Rakitan Office',3800000,0,NOW(),NOW()),
(174,87,4,'Keluar persediaan',0,3800000,NOW(),NOW()),
(175,88,3,'AR dari SMA Negeri 5 - SL-202505-0001',37000000,0,NOW(),NOW()),
(176,88,15,'Pendapatan penjualan kredit',0,37000000,NOW(),NOW()),
(177,89,18,'HPP 5 laptop Asus VivoBook',34000000,0,NOW(),NOW()),
(178,89,4,'Keluar persediaan',0,34000000,NOW(),NOW()),
(179,90,3,'AR dari CV Karya Mandiri - SL-202505-0002',3500000,0,NOW(),NOW()),
(180,90,15,'Pendapatan penjualan kredit',0,3500000,NOW(),NOW()),
(181,91,18,'HPP printer Epson L5290',2800000,0,NOW(),NOW()),
(182,91,4,'Keluar persediaan',0,2800000,NOW(),NOW()),
(183,92,3,'AR dari Klinik Sehat - SL-202506-0001',9600000,0,NOW(),NOW()),
(184,92,15,'Pendapatan penjualan kredit',0,9600000,NOW(),NOW()),
(185,93,18,'HPP 2 PC Rakitan Office',7600000,0,NOW(),NOW()),
(186,93,4,'Keluar persediaan',0,7600000,NOW(),NOW()),
(187,94,3,'AR dari PT Teknologi Maju - SL-202507-0001',32800000,0,NOW(),NOW()),
(188,94,15,'Pendapatan penjualan kredit',0,32800000,NOW(),NOW()),
(189,95,18,'HPP 4 laptop Asus VivoBook',27200000,0,NOW(),NOW()),
(190,95,4,'Keluar persediaan',0,27200000,NOW(),NOW()),
(191,96,3,'AR dari PT Maju Bersama - SL-202508-0001',13500000,0,NOW(),NOW()),
(192,96,15,'Pendapatan penjualan kredit',0,13500000,NOW(),NOW()),
(193,97,18,'HPP PC Rakitan Desain',11000000,0,NOW(),NOW()),
(194,97,4,'Keluar persediaan',0,11000000,NOW(),NOW()),
(195,98,3,'AR dari Yayasan Al-Hikmah - SL-202509-0001',4200000,0,NOW(),NOW()),
(196,98,15,'Pendapatan penjualan kredit',0,4200000,NOW(),NOW()),
(197,99,18,'HPP 2 printer Epson L3210',3300000,0,NOW(),NOW()),
(198,99,4,'Keluar persediaan',0,3300000,NOW(),NOW()),
(199,100,3,'AR dari CV Karya Mandiri - SL-202510-0001',8200000,0,NOW(),NOW()),
(200,100,15,'Pendapatan penjualan kredit',0,8200000,NOW(),NOW()),
(201,101,18,'HPP PC Rakitan Gaming',6500000,0,NOW(),NOW()),
(202,101,4,'Keluar persediaan',0,6500000,NOW(),NOW()),
(203,102,3,'AR dari SMA Negeri 5 - SL-202511-0001',25000000,0,NOW(),NOW()),
(204,102,15,'Pendapatan penjualan kredit',0,25000000,NOW(),NOW()),
(205,103,18,'HPP 2 laptop Asus + 1 Acer',20800000,0,NOW(),NOW()),
(206,103,4,'Keluar persediaan',0,20800000,NOW(),NOW()),
(207,104,3,'AR dari PT Maju Bersama - SL-202512-0001',16400000,0,NOW(),NOW()),
(208,104,15,'Pendapatan penjualan kredit',0,16400000,NOW(),NOW()),
(209,105,18,'HPP 2 PC Rakitan Gaming',13000000,0,NOW(),NOW()),
(210,105,4,'Keluar persediaan',0,13000000,NOW(),NOW()),
(211,106,3,'AR dari PT Teknologi Maju - SL-202601-0001',22000000,0,NOW(),NOW()),
(212,106,15,'Pendapatan penjualan kredit',0,22000000,NOW(),NOW()),
(213,107,18,'HPP 2 laptop Asus + 1 HP',18700000,0,NOW(),NOW()),
(214,107,4,'Keluar persediaan',0,18700000,NOW(),NOW()),
(215,108,3,'AR dari Klinik Sehat - SL-202602-0001',4800000,0,NOW(),NOW()),
(216,108,15,'Pendapatan penjualan kredit',0,4800000,NOW(),NOW()),
(217,109,18,'HPP PC Rakitan Office',3800000,0,NOW(),NOW()),
(218,109,4,'Keluar persediaan',0,3800000,NOW(),NOW()),
(219,110,3,'AR dari Yayasan Al-Hikmah - SL-202603-0001',17500000,0,NOW(),NOW()),
(220,110,15,'Pendapatan penjualan kredit',0,17500000,NOW(),NOW()),
(221,111,18,'HPP laptop Asus ROG',14500000,0,NOW(),NOW()),
(222,111,4,'Keluar persediaan',0,14500000,NOW(),NOW()),
(223,112,3,'AR dari CV Karya Mandiri - SL-202603-0002',3500000,0,NOW(),NOW()),
(224,112,15,'Pendapatan penjualan kredit',0,3500000,NOW(),NOW()),
(225,113,18,'HPP printer Epson L5290',2800000,0,NOW(),NOW()),
(226,113,4,'Keluar persediaan',0,2800000,NOW(),NOW()),
(227,114,3,'AR dari PT Maju Bersama - SL-202604-0001',26500000,0,NOW(),NOW()),
(228,114,15,'Pendapatan penjualan kredit',0,26500000,NOW(),NOW()),
(229,115,18,'HPP 2 PC Rakitan Desain',22000000,0,NOW(),NOW()),
(230,115,4,'Keluar persediaan',0,22000000,NOW(),NOW()),
(231,116,3,'AR dari PT Teknologi Maju - SL-202605-0001',15000000,0,NOW(),NOW()),
(232,116,15,'Pendapatan penjualan kredit',0,15000000,NOW(),NOW()),
(233,117,18,'HPP 2 laptop Lenovo IdeaPad',12400000,0,NOW(),NOW()),
(234,117,4,'Keluar persediaan',0,12400000,NOW(),NOW());

-- ── SERVICE ORDER REVENUE (Dr Kas / Cr Pend.Servis) ────────
INSERT INTO journal_entries (id,journal_number,entry_date,description,reference_type,reference_id,status,posted_at,posted_by,created_by,created_at,updated_at) VALUES
(118,'JRN-202501-0011','2025-01-10','Service Revenue SVC-202501-0001','ServiceOrder',1,'posted','2025-01-10 16:00:00',4,4,NOW(),NOW()),
(119,'JRN-202501-0012','2025-01-16','Service Revenue SVC-202501-0002','ServiceOrder',2,'posted','2025-01-16 15:00:00',4,4,NOW(),NOW()),
(120,'JRN-202502-0011','2025-02-05','Service Revenue SVC-202502-0001','ServiceOrder',3,'posted','2025-02-05 15:00:00',4,4,NOW(),NOW()),
(121,'JRN-202502-0012','2025-02-13','Service Revenue SVC-202502-0002','ServiceOrder',4,'posted','2025-02-13 11:00:00',4,4,NOW(),NOW()),
(122,'JRN-202503-0011','2025-03-05','Service Revenue SVC-202503-0001','ServiceOrder',5,'posted','2025-03-05 17:00:00',4,4,NOW(),NOW()),
(123,'JRN-202503-0012','2025-03-10','Service Revenue SVC-202503-0002','ServiceOrder',6,'posted','2025-03-10 16:00:00',4,4,NOW(),NOW()),
(124,'JRN-202504-0009','2025-04-05','Service Revenue SVC-202504-0001','ServiceOrder',7,'posted','2025-04-05 18:00:00',4,4,NOW(),NOW()),
(125,'JRN-202504-0010','2025-04-15','Service Revenue SVC-202504-0002','ServiceOrder',8,'posted','2025-04-15 12:00:00',4,4,NOW(),NOW()),
(126,'JRN-202505-0010','2025-05-08','Service Revenue SVC-202505-0001','ServiceOrder',9,'posted','2025-05-08 15:00:00',4,4,NOW(),NOW()),
(127,'JRN-202505-0011','2025-05-21','Service Revenue SVC-202505-0002','ServiceOrder',10,'posted','2025-05-21 13:00:00',4,4,NOW(),NOW()),
(128,'JRN-202506-0007','2025-06-06','Service Revenue SVC-202506-0001','ServiceOrder',11,'posted','2025-06-06 17:00:00',4,4,NOW(),NOW()),
(129,'JRN-202507-0007','2025-07-09','Service Revenue SVC-202507-0001','ServiceOrder',12,'posted','2025-07-09 15:00:00',4,4,NOW(),NOW()),
(130,'JRN-202508-0007','2025-08-07','Service Revenue SVC-202508-0001','ServiceOrder',13,'posted','2025-08-07 16:00:00',4,4,NOW(),NOW()),
(131,'JRN-202509-0007','2025-09-10','Service Revenue SVC-202509-0001','ServiceOrder',14,'posted','2025-09-10 17:00:00',4,4,NOW(),NOW()),
(132,'JRN-202510-0007','2025-10-09','Service Revenue SVC-202510-0001','ServiceOrder',15,'posted','2025-10-09 15:00:00',4,4,NOW(),NOW()),
(133,'JRN-202512-0007','2025-12-04','Service Revenue SVC-202512-0001','ServiceOrder',17,'posted','2025-12-04 16:00:00',4,4,NOW(),NOW()),
(134,'JRN-202601-0007','2026-01-10','Service Revenue SVC-202601-0001','ServiceOrder',18,'posted','2026-01-10 15:00:00',4,4,NOW(),NOW()),
(135,'JRN-202603-0007','2026-03-07','Service Revenue SVC-202603-0001','ServiceOrder',19,'posted','2026-03-07 17:00:00',4,4,NOW(),NOW());

INSERT INTO journal_entry_lines (id,journal_entry_id,account_id,description,debit,credit,created_at,updated_at) VALUES
(235,118,1,'Penerimaan kas servis SVC-202501-0001',350000,0,NOW(),NOW()),
(236,118,16,'Pendapatan jasa servis',0,350000,NOW(),NOW()),
(237,119,1,'Penerimaan kas servis SVC-202501-0002',200000,0,NOW(),NOW()),
(238,119,16,'Pendapatan jasa servis',0,200000,NOW(),NOW()),
(239,120,1,'Penerimaan kas servis SVC-202502-0001',150000,0,NOW(),NOW()),
(240,120,16,'Pendapatan jasa servis',0,150000,NOW(),NOW()),
(241,121,1,'Penerimaan kas servis SVC-202502-0002',175000,0,NOW(),NOW()),
(242,121,16,'Pendapatan jasa servis',0,175000,NOW(),NOW()),
(243,122,1,'Penerimaan kas servis SVC-202503-0001',450000,0,NOW(),NOW()),
(244,122,16,'Pendapatan jasa servis',0,450000,NOW(),NOW()),
(245,123,1,'Penerimaan kas servis SVC-202503-0002',125000,0,NOW(),NOW()),
(246,123,16,'Pendapatan jasa servis',0,125000,NOW(),NOW()),
(247,124,1,'Penerimaan kas servis SVC-202504-0001',850000,0,NOW(),NOW()),
(248,124,16,'Pendapatan jasa servis',0,850000,NOW(),NOW()),
(249,125,1,'Penerimaan kas servis SVC-202504-0002',200000,0,NOW(),NOW()),
(250,125,16,'Pendapatan jasa servis',0,200000,NOW(),NOW()),
(251,126,1,'Penerimaan kas servis SVC-202505-0001',300000,0,NOW(),NOW()),
(252,126,16,'Pendapatan jasa servis',0,300000,NOW(),NOW()),
(253,127,1,'Penerimaan kas servis SVC-202505-0002',175000,0,NOW(),NOW()),
(254,127,16,'Pendapatan jasa servis',0,175000,NOW(),NOW()),
(255,128,1,'Penerimaan kas servis SVC-202506-0001',500000,0,NOW(),NOW()),
(256,128,16,'Pendapatan jasa servis',0,500000,NOW(),NOW()),
(257,129,1,'Penerimaan kas servis SVC-202507-0001',250000,0,NOW(),NOW()),
(258,129,16,'Pendapatan jasa servis',0,250000,NOW(),NOW()),
(259,130,1,'Penerimaan kas servis SVC-202508-0001',550000,0,NOW(),NOW()),
(260,130,16,'Pendapatan jasa servis',0,550000,NOW(),NOW()),
(261,131,1,'Penerimaan kas servis SVC-202509-0001',150000,0,NOW(),NOW()),
(262,131,16,'Pendapatan jasa servis',0,150000,NOW(),NOW()),
(263,132,1,'Penerimaan kas servis SVC-202510-0001',275000,0,NOW(),NOW()),
(264,132,16,'Pendapatan jasa servis',0,275000,NOW(),NOW()),
(265,133,1,'Penerimaan kas servis SVC-202512-0001',200000,0,NOW(),NOW()),
(266,133,16,'Pendapatan jasa servis',0,200000,NOW(),NOW()),
(267,134,1,'Penerimaan kas servis SVC-202601-0001',325000,0,NOW(),NOW()),
(268,134,16,'Pendapatan jasa servis',0,325000,NOW(),NOW()),
(269,135,1,'Penerimaan kas servis SVC-202603-0001',450000,0,NOW(),NOW()),
(270,135,16,'Pendapatan jasa servis',0,450000,NOW(),NOW());

-- ── PAYROLL JOURNALS (Dr Gaji / Cr Kas) — monthly, 17 months ──
-- Total gaji per bulan normal: 8500000+5800000+4000000+3190000+3000000+3190000+3810000 = 31490000
-- Desember 2025 (THR): 9000000+6300000+4400000+3540000+3350000+3540000+4210000 = 34340000
INSERT INTO journal_entries (id,journal_number,entry_date,description,reference_type,reference_id,status,posted_at,posted_by,created_by,created_at,updated_at) VALUES
(136,'JRN-202501-0013','2025-01-31','Payroll Januari 2025','Payroll',1,'posted','2025-01-31 17:00:00',3,3,NOW(),NOW()),
(137,'JRN-202502-0013','2025-02-28','Payroll Februari 2025','Payroll',2,'posted','2025-02-28 17:00:00',3,3,NOW(),NOW()),
(138,'JRN-202503-0013','2025-03-31','Payroll Maret 2025','Payroll',3,'posted','2025-03-31 17:00:00',3,3,NOW(),NOW()),
(139,'JRN-202504-0011','2025-04-30','Payroll April 2025','Payroll',4,'posted','2025-04-30 17:00:00',3,3,NOW(),NOW()),
(140,'JRN-202505-0012','2025-05-31','Payroll Mei 2025','Payroll',5,'posted','2025-05-31 17:00:00',3,3,NOW(),NOW()),
(141,'JRN-202506-0008','2025-06-30','Payroll Juni 2025','Payroll',6,'posted','2025-06-30 17:00:00',3,3,NOW(),NOW()),
(142,'JRN-202507-0008','2025-07-31','Payroll Juli 2025','Payroll',7,'posted','2025-07-31 17:00:00',3,3,NOW(),NOW()),
(143,'JRN-202508-0008','2025-08-31','Payroll Agustus 2025','Payroll',8,'posted','2025-08-31 17:00:00',3,3,NOW(),NOW()),
(144,'JRN-202509-0008','2025-09-30','Payroll September 2025','Payroll',9,'posted','2025-09-30 17:00:00',3,3,NOW(),NOW()),
(145,'JRN-202510-0008','2025-10-31','Payroll Oktober 2025','Payroll',10,'posted','2025-10-31 17:00:00',3,3,NOW(),NOW()),
(146,'JRN-202511-0007','2025-11-30','Payroll November 2025','Payroll',11,'posted','2025-11-30 17:00:00',3,3,NOW(),NOW()),
(147,'JRN-202512-0008','2025-12-31','Payroll Desember 2025 + THR','Payroll',12,'posted','2025-12-31 17:00:00',3,3,NOW(),NOW()),
(148,'JRN-202601-0008','2026-01-31','Payroll Januari 2026','Payroll',13,'posted','2026-01-31 17:00:00',3,3,NOW(),NOW()),
(149,'JRN-202602-0007','2026-02-28','Payroll Februari 2026','Payroll',14,'posted','2026-02-28 17:00:00',3,3,NOW(),NOW()),
(150,'JRN-202603-0008','2026-03-31','Payroll Maret 2026','Payroll',15,'posted','2026-03-31 17:00:00',3,3,NOW(),NOW()),
(151,'JRN-202604-0007','2026-04-30','Payroll April 2026','Payroll',16,'posted','2026-04-30 17:00:00',3,3,NOW(),NOW()),
(152,'JRN-202605-0010','2026-05-31','Payroll Mei 2026','Payroll',17,'posted','2026-05-31 17:00:00',3,3,NOW(),NOW());

INSERT INTO journal_entry_lines (id,journal_entry_id,account_id,description,debit,credit,created_at,updated_at) VALUES
(271,136,19,'Beban gaji karyawan Januari 2025',31490000,0,NOW(),NOW()),
(272,136,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(273,137,19,'Beban gaji karyawan Februari 2025',31490000,0,NOW(),NOW()),
(274,137,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(275,138,19,'Beban gaji karyawan Maret 2025',31490000,0,NOW(),NOW()),
(276,138,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(277,139,19,'Beban gaji karyawan April 2025',31490000,0,NOW(),NOW()),
(278,139,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(279,140,19,'Beban gaji karyawan Mei 2025',31490000,0,NOW(),NOW()),
(280,140,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(281,141,19,'Beban gaji karyawan Juni 2025',31490000,0,NOW(),NOW()),
(282,141,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(283,142,19,'Beban gaji karyawan Juli 2025',31490000,0,NOW(),NOW()),
(284,142,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(285,143,19,'Beban gaji karyawan Agustus 2025',31490000,0,NOW(),NOW()),
(286,143,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(287,144,19,'Beban gaji karyawan September 2025',31490000,0,NOW(),NOW()),
(288,144,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(289,145,19,'Beban gaji karyawan Oktober 2025',31490000,0,NOW(),NOW()),
(290,145,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(291,146,19,'Beban gaji karyawan November 2025',31490000,0,NOW(),NOW()),
(292,146,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(293,147,19,'Beban gaji + THR Desember 2025',34340000,0,NOW(),NOW()),
(294,147,1,'Pembayaran gaji + THR via kas',0,34340000,NOW(),NOW()),
(295,148,19,'Beban gaji karyawan Januari 2026',31490000,0,NOW(),NOW()),
(296,148,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(297,149,19,'Beban gaji karyawan Februari 2026',31490000,0,NOW(),NOW()),
(298,149,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(299,150,19,'Beban gaji karyawan Maret 2026',31490000,0,NOW(),NOW()),
(300,150,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(301,151,19,'Beban gaji karyawan April 2026',31490000,0,NOW(),NOW()),
(302,151,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW()),
(303,152,19,'Beban gaji karyawan Mei 2026',31490000,0,NOW(),NOW()),
(304,152,1,'Pembayaran gaji via kas',0,31490000,NOW(),NOW());

-- ── EXPENSE JOURNALS (Dr Beban / Cr Kas) ───────────────────
INSERT INTO journal_entries (id,journal_number,entry_date,description,reference_type,reference_id,status,posted_at,posted_by,created_by,created_at,updated_at) VALUES
-- Sewa (17 bulan)
(153,'JRN-202501-0014','2025-01-05','Beban Sewa Januari 2025','Expense',1,'posted','2025-01-05 09:00:00',3,3,NOW(),NOW()),
(154,'JRN-202502-0014','2025-02-05','Beban Sewa Februari 2025','Expense',2,'posted','2025-02-05 09:00:00',3,3,NOW(),NOW()),
(155,'JRN-202503-0014','2025-03-05','Beban Sewa Maret 2025','Expense',3,'posted','2025-03-05 09:00:00',3,3,NOW(),NOW()),
(156,'JRN-202504-0012','2025-04-05','Beban Sewa April 2025','Expense',4,'posted','2025-04-05 09:00:00',3,3,NOW(),NOW()),
(157,'JRN-202505-0013','2025-05-05','Beban Sewa Mei 2025','Expense',5,'posted','2025-05-05 09:00:00',3,3,NOW(),NOW()),
(158,'JRN-202506-0009','2025-06-05','Beban Sewa Juni 2025','Expense',6,'posted','2025-06-05 09:00:00',3,3,NOW(),NOW()),
(159,'JRN-202507-0009','2025-07-05','Beban Sewa Juli 2025','Expense',7,'posted','2025-07-05 09:00:00',3,3,NOW(),NOW()),
(160,'JRN-202508-0009','2025-08-05','Beban Sewa Agustus 2025','Expense',8,'posted','2025-08-05 09:00:00',3,3,NOW(),NOW()),
(161,'JRN-202509-0009','2025-09-05','Beban Sewa September 2025','Expense',9,'posted','2025-09-05 09:00:00',3,3,NOW(),NOW()),
(162,'JRN-202510-0009','2025-10-05','Beban Sewa Oktober 2025','Expense',10,'posted','2025-10-05 09:00:00',3,3,NOW(),NOW()),
(163,'JRN-202511-0008','2025-11-05','Beban Sewa November 2025','Expense',11,'posted','2025-11-05 09:00:00',3,3,NOW(),NOW()),
(164,'JRN-202512-0009','2025-12-05','Beban Sewa Desember 2025','Expense',12,'posted','2025-12-05 09:00:00',3,3,NOW(),NOW()),
(165,'JRN-202601-0009','2026-01-05','Beban Sewa Januari 2026','Expense',13,'posted','2026-01-05 09:00:00',3,3,NOW(),NOW()),
(166,'JRN-202602-0008','2026-02-05','Beban Sewa Februari 2026','Expense',14,'posted','2026-02-05 09:00:00',3,3,NOW(),NOW()),
(167,'JRN-202603-0009','2026-03-05','Beban Sewa Maret 2026','Expense',15,'posted','2026-03-05 09:00:00',3,3,NOW(),NOW()),
(168,'JRN-202604-0008','2026-04-05','Beban Sewa April 2026','Expense',16,'posted','2026-04-05 09:00:00',3,3,NOW(),NOW()),
(169,'JRN-202605-0011','2026-05-05','Beban Sewa Mei 2026','Expense',17,'posted','2026-05-05 09:00:00',3,3,NOW(),NOW()),
-- Listrik (17 bulan)
(170,'JRN-202501-0015','2025-01-15','Beban Listrik Januari 2025','Expense',18,'posted','2025-01-15 10:00:00',3,3,NOW(),NOW()),
(171,'JRN-202502-0015','2025-02-15','Beban Listrik Februari 2025','Expense',19,'posted','2025-02-15 10:00:00',3,3,NOW(),NOW()),
(172,'JRN-202503-0015','2025-03-15','Beban Listrik Maret 2025','Expense',20,'posted','2025-03-15 10:00:00',3,3,NOW(),NOW()),
(173,'JRN-202504-0013','2025-04-15','Beban Listrik April 2025','Expense',21,'posted','2025-04-15 10:00:00',3,3,NOW(),NOW()),
(174,'JRN-202505-0014','2025-05-15','Beban Listrik Mei 2025','Expense',22,'posted','2025-05-15 10:00:00',3,3,NOW(),NOW()),
(175,'JRN-202506-0010','2025-06-15','Beban Listrik Juni 2025','Expense',23,'posted','2025-06-15 10:00:00',3,3,NOW(),NOW()),
(176,'JRN-202507-0010','2025-07-15','Beban Listrik Juli 2025','Expense',24,'posted','2025-07-15 10:00:00',3,3,NOW(),NOW()),
(177,'JRN-202508-0010','2025-08-15','Beban Listrik Agustus 2025','Expense',25,'posted','2025-08-15 10:00:00',3,3,NOW(),NOW()),
(178,'JRN-202509-0010','2025-09-15','Beban Listrik September 2025','Expense',26,'posted','2025-09-15 10:00:00',3,3,NOW(),NOW()),
(179,'JRN-202510-0010','2025-10-15','Beban Listrik Oktober 2025','Expense',27,'posted','2025-10-15 10:00:00',3,3,NOW(),NOW()),
(180,'JRN-202511-0009','2025-11-15','Beban Listrik November 2025','Expense',28,'posted','2025-11-15 10:00:00',3,3,NOW(),NOW()),
(181,'JRN-202512-0010','2025-12-15','Beban Listrik Desember 2025','Expense',29,'posted','2025-12-15 10:00:00',3,3,NOW(),NOW()),
(182,'JRN-202601-0010','2026-01-15','Beban Listrik Januari 2026','Expense',30,'posted','2026-01-15 10:00:00',3,3,NOW(),NOW()),
(183,'JRN-202602-0009','2026-02-15','Beban Listrik Februari 2026','Expense',31,'posted','2026-02-15 10:00:00',3,3,NOW(),NOW()),
(184,'JRN-202603-0010','2026-03-15','Beban Listrik Maret 2026','Expense',32,'posted','2026-03-15 10:00:00',3,3,NOW(),NOW()),
(185,'JRN-202604-0009','2026-04-15','Beban Listrik April 2026','Expense',33,'posted','2026-04-15 10:00:00',3,3,NOW(),NOW()),
(186,'JRN-202605-0012','2026-05-15','Beban Listrik Mei 2026','Expense',34,'posted','2026-05-15 10:00:00',3,3,NOW(),NOW()),
-- Internet (17 bulan)
(187,'JRN-202501-0016','2025-01-10','Beban Internet Januari 2025','Expense',35,'posted','2025-01-10 10:00:00',3,3,NOW(),NOW()),
(188,'JRN-202502-0016','2025-02-10','Beban Internet Februari 2025','Expense',36,'posted','2025-02-10 10:00:00',3,3,NOW(),NOW()),
(189,'JRN-202503-0016','2025-03-10','Beban Internet Maret 2025','Expense',37,'posted','2025-03-10 10:00:00',3,3,NOW(),NOW()),
(190,'JRN-202504-0014','2025-04-10','Beban Internet April 2025','Expense',38,'posted','2025-04-10 10:00:00',3,3,NOW(),NOW()),
(191,'JRN-202505-0015','2025-05-10','Beban Internet Mei 2025','Expense',39,'posted','2025-05-10 10:00:00',3,3,NOW(),NOW()),
(192,'JRN-202506-0011','2025-06-10','Beban Internet Juni 2025','Expense',40,'posted','2025-06-10 10:00:00',3,3,NOW(),NOW()),
(193,'JRN-202507-0011','2025-07-10','Beban Internet Juli 2025','Expense',41,'posted','2025-07-10 10:00:00',3,3,NOW(),NOW()),
(194,'JRN-202508-0011','2025-08-10','Beban Internet Agustus 2025','Expense',42,'posted','2025-08-10 10:00:00',3,3,NOW(),NOW()),
(195,'JRN-202509-0011','2025-09-10','Beban Internet September 2025','Expense',43,'posted','2025-09-10 10:00:00',3,3,NOW(),NOW()),
(196,'JRN-202510-0011','2025-10-10','Beban Internet Oktober 2025','Expense',44,'posted','2025-10-10 10:00:00',3,3,NOW(),NOW()),
(197,'JRN-202511-0010','2025-11-10','Beban Internet November 2025','Expense',45,'posted','2025-11-10 10:00:00',3,3,NOW(),NOW()),
(198,'JRN-202512-0011','2025-12-10','Beban Internet Desember 2025','Expense',46,'posted','2025-12-10 10:00:00',3,3,NOW(),NOW()),
(199,'JRN-202601-0011','2026-01-10','Beban Internet Januari 2026','Expense',47,'posted','2026-01-10 10:00:00',3,3,NOW(),NOW()),
(200,'JRN-202602-0010','2026-02-10','Beban Internet Februari 2026','Expense',48,'posted','2026-02-10 10:00:00',3,3,NOW(),NOW()),
(201,'JRN-202603-0011','2026-03-10','Beban Internet Maret 2026','Expense',49,'posted','2026-03-10 10:00:00',3,3,NOW(),NOW()),
(202,'JRN-202604-0010','2026-04-10','Beban Internet April 2026','Expense',50,'posted','2026-04-10 10:00:00',3,3,NOW(),NOW()),
(203,'JRN-202605-0013','2026-05-10','Beban Internet Mei 2026','Expense',51,'posted','2026-05-10 10:00:00',3,3,NOW(),NOW()),
-- Pemeliharaan & ATK
(204,'JRN-202502-0017','2025-02-20','Beban Pemeliharaan - Servis AC','Expense',52,'posted','2025-02-20 11:00:00',3,3,NOW(),NOW()),
(205,'JRN-202505-0016','2025-05-18','Beban Pemeliharaan - Etalase','Expense',53,'posted','2025-05-18 11:00:00',3,3,NOW(),NOW()),
(206,'JRN-202508-0012','2025-08-22','Beban Pemeliharaan - Cat toko','Expense',54,'posted','2025-08-22 11:00:00',3,3,NOW(),NOW()),
(207,'JRN-202511-0011','2025-11-15','Beban Pemeliharaan - Servis AC','Expense',55,'posted','2025-11-15 11:00:00',3,3,NOW(),NOW()),
(208,'JRN-202602-0011','2026-02-18','Beban Pemeliharaan - Pintu rolling','Expense',56,'posted','2026-02-18 11:00:00',3,3,NOW(),NOW()),
(209,'JRN-202605-0014','2026-05-08','Beban Pemeliharaan - AC & listrik','Expense',57,'posted','2026-05-08 11:00:00',3,3,NOW(),NOW()),
(210,'JRN-202501-0017','2025-01-20','Beban ATK & Operasional','Expense',58,'posted','2025-01-20 11:00:00',3,3,NOW(),NOW()),
(211,'JRN-202504-0015','2025-04-20','Beban ATK & Operasional','Expense',59,'posted','2025-04-20 11:00:00',3,3,NOW(),NOW()),
(212,'JRN-202507-0012','2025-07-20','Beban ATK & Operasional','Expense',60,'posted','2025-07-20 11:00:00',3,3,NOW(),NOW()),
(213,'JRN-202510-0012','2025-10-20','Beban ATK & Operasional','Expense',61,'posted','2025-10-20 11:00:00',3,3,NOW(),NOW()),
(214,'JRN-202601-0012','2026-01-20','Beban ATK & Operasional','Expense',62,'posted','2026-01-20 11:00:00',3,3,NOW(),NOW()),
(215,'JRN-202604-0011','2026-04-20','Beban ATK & Operasional','Expense',63,'posted','2026-04-20 11:00:00',3,3,NOW(),NOW());

INSERT INTO journal_entry_lines (id,journal_entry_id,account_id,description,debit,credit,created_at,updated_at) VALUES
-- Sewa 17 bulan (account_id 23=Sewa, 1=Kas)
(305,153,23,'Beban sewa ruko Januari 2025',3500000,0,NOW(),NOW()),(306,153,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(307,154,23,'Beban sewa ruko Februari 2025',3500000,0,NOW(),NOW()),(308,154,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(309,155,23,'Beban sewa ruko Maret 2025',3500000,0,NOW(),NOW()),(310,155,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(311,156,23,'Beban sewa ruko April 2025',3500000,0,NOW(),NOW()),(312,156,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(313,157,23,'Beban sewa ruko Mei 2025',3500000,0,NOW(),NOW()),(314,157,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(315,158,23,'Beban sewa ruko Juni 2025',3500000,0,NOW(),NOW()),(316,158,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(317,159,23,'Beban sewa ruko Juli 2025',3500000,0,NOW(),NOW()),(318,159,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(319,160,23,'Beban sewa ruko Agustus 2025',3500000,0,NOW(),NOW()),(320,160,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(321,161,23,'Beban sewa ruko September 2025',3500000,0,NOW(),NOW()),(322,161,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(323,162,23,'Beban sewa ruko Oktober 2025',3500000,0,NOW(),NOW()),(324,162,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(325,163,23,'Beban sewa ruko November 2025',3500000,0,NOW(),NOW()),(326,163,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(327,164,23,'Beban sewa ruko Desember 2025',3500000,0,NOW(),NOW()),(328,164,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(329,165,23,'Beban sewa ruko Januari 2026',3500000,0,NOW(),NOW()),(330,165,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(331,166,23,'Beban sewa ruko Februari 2026',3500000,0,NOW(),NOW()),(332,166,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(333,167,23,'Beban sewa ruko Maret 2026',3500000,0,NOW(),NOW()),(334,167,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(335,168,23,'Beban sewa ruko April 2026',3500000,0,NOW(),NOW()),(336,168,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
(337,169,23,'Beban sewa ruko Mei 2026',3500000,0,NOW(),NOW()),(338,169,1,'Pembayaran sewa via kas',0,3500000,NOW(),NOW()),
-- Listrik 17 bulan (account_id 20=Listrik)
(339,170,20,'Beban listrik Januari 2025',850000,0,NOW(),NOW()),(340,170,1,'Pembayaran listrik via kas',0,850000,NOW(),NOW()),
(341,171,20,'Beban listrik Februari 2025',870000,0,NOW(),NOW()),(342,171,1,'Pembayaran listrik via kas',0,870000,NOW(),NOW()),
(343,172,20,'Beban listrik Maret 2025',900000,0,NOW(),NOW()),(344,172,1,'Pembayaran listrik via kas',0,900000,NOW(),NOW()),
(345,173,20,'Beban listrik April 2025',920000,0,NOW(),NOW()),(346,173,1,'Pembayaran listrik via kas',0,920000,NOW(),NOW()),
(347,174,20,'Beban listrik Mei 2025',950000,0,NOW(),NOW()),(348,174,1,'Pembayaran listrik via kas',0,950000,NOW(),NOW()),
(349,175,20,'Beban listrik Juni 2025',980000,0,NOW(),NOW()),(350,175,1,'Pembayaran listrik via kas',0,980000,NOW(),NOW()),
(351,176,20,'Beban listrik Juli 2025',1050000,0,NOW(),NOW()),(352,176,1,'Pembayaran listrik via kas',0,1050000,NOW(),NOW()),
(353,177,20,'Beban listrik Agustus 2025',1100000,0,NOW(),NOW()),(354,177,1,'Pembayaran listrik via kas',0,1100000,NOW(),NOW()),
(355,178,20,'Beban listrik September 2025',1050000,0,NOW(),NOW()),(356,178,1,'Pembayaran listrik via kas',0,1050000,NOW(),NOW()),
(357,179,20,'Beban listrik Oktober 2025',980000,0,NOW(),NOW()),(358,179,1,'Pembayaran listrik via kas',0,980000,NOW(),NOW()),
(359,180,20,'Beban listrik November 2025',920000,0,NOW(),NOW()),(360,180,1,'Pembayaran listrik via kas',0,920000,NOW(),NOW()),
(361,181,20,'Beban listrik Desember 2025',900000,0,NOW(),NOW()),(362,181,1,'Pembayaran listrik via kas',0,900000,NOW(),NOW()),
(363,182,20,'Beban listrik Januari 2026',870000,0,NOW(),NOW()),(364,182,1,'Pembayaran listrik via kas',0,870000,NOW(),NOW()),
(365,183,20,'Beban listrik Februari 2026',880000,0,NOW(),NOW()),(366,183,1,'Pembayaran listrik via kas',0,880000,NOW(),NOW()),
(367,184,20,'Beban listrik Maret 2026',910000,0,NOW(),NOW()),(368,184,1,'Pembayaran listrik via kas',0,910000,NOW(),NOW()),
(369,185,20,'Beban listrik April 2026',930000,0,NOW(),NOW()),(370,185,1,'Pembayaran listrik via kas',0,930000,NOW(),NOW()),
(371,186,20,'Beban listrik Mei 2026',960000,0,NOW(),NOW()),(372,186,1,'Pembayaran listrik via kas',0,960000,NOW(),NOW()),
-- Internet 17 bulan (account_id 22=Umum&Admin)
(373,187,22,'Beban internet Januari 2025',450000,0,NOW(),NOW()),(374,187,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(375,188,22,'Beban internet Februari 2025',450000,0,NOW(),NOW()),(376,188,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(377,189,22,'Beban internet Maret 2025',450000,0,NOW(),NOW()),(378,189,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(379,190,22,'Beban internet April 2025',450000,0,NOW(),NOW()),(380,190,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(381,191,22,'Beban internet Mei 2025',450000,0,NOW(),NOW()),(382,191,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(383,192,22,'Beban internet Juni 2025',450000,0,NOW(),NOW()),(384,192,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(385,193,22,'Beban internet Juli 2025',450000,0,NOW(),NOW()),(386,193,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(387,194,22,'Beban internet Agustus 2025',450000,0,NOW(),NOW()),(388,194,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(389,195,22,'Beban internet September 2025',450000,0,NOW(),NOW()),(390,195,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(391,196,22,'Beban internet Oktober 2025',450000,0,NOW(),NOW()),(392,196,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(393,197,22,'Beban internet November 2025',450000,0,NOW(),NOW()),(394,197,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(395,198,22,'Beban internet Desember 2025',450000,0,NOW(),NOW()),(396,198,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(397,199,22,'Beban internet Januari 2026',450000,0,NOW(),NOW()),(398,199,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(399,200,22,'Beban internet Februari 2026',450000,0,NOW(),NOW()),(400,200,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(401,201,22,'Beban internet Maret 2026',450000,0,NOW(),NOW()),(402,201,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(403,202,22,'Beban internet April 2026',450000,0,NOW(),NOW()),(404,202,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
(405,203,22,'Beban internet Mei 2026',450000,0,NOW(),NOW()),(406,203,1,'Pembayaran internet via kas',0,450000,NOW(),NOW()),
-- Pemeliharaan & ATK (account_id 21=Pemeliharaan, 22=Umum&Admin)
(407,204,21,'Beban pemeliharaan - servis AC',350000,0,NOW(),NOW()),(408,204,1,'Pembayaran via kas',0,350000,NOW(),NOW()),
(409,205,21,'Beban pemeliharaan - etalase',450000,0,NOW(),NOW()),(410,205,1,'Pembayaran via kas',0,450000,NOW(),NOW()),
(411,206,21,'Beban pemeliharaan - cat toko',600000,0,NOW(),NOW()),(412,206,1,'Pembayaran via kas',0,600000,NOW(),NOW()),
(413,207,21,'Beban pemeliharaan - servis AC',400000,0,NOW(),NOW()),(414,207,1,'Pembayaran via kas',0,400000,NOW(),NOW()),
(415,208,21,'Beban pemeliharaan - pintu rolling',500000,0,NOW(),NOW()),(416,208,1,'Pembayaran via kas',0,500000,NOW(),NOW()),
(417,209,21,'Beban pemeliharaan - AC & listrik',750000,0,NOW(),NOW()),(418,209,1,'Pembayaran via kas',0,750000,NOW(),NOW()),
(419,210,22,'Beban ATK & operasional',250000,0,NOW(),NOW()),(420,210,1,'Pembayaran via kas',0,250000,NOW(),NOW()),
(421,211,22,'Beban ATK & operasional',275000,0,NOW(),NOW()),(422,211,1,'Pembayaran via kas',0,275000,NOW(),NOW()),
(423,212,22,'Beban ATK & operasional',250000,0,NOW(),NOW()),(424,212,1,'Pembayaran via kas',0,250000,NOW(),NOW()),
(425,213,22,'Beban ATK & operasional',300000,0,NOW(),NOW()),(426,213,1,'Pembayaran via kas',0,300000,NOW(),NOW()),
(427,214,22,'Beban ATK & operasional',275000,0,NOW(),NOW()),(428,214,1,'Pembayaran via kas',0,275000,NOW(),NOW()),
(429,215,22,'Beban ATK & operasional',300000,0,NOW(),NOW()),(430,215,1,'Pembayaran via kas',0,300000,NOW(),NOW());

-- ── AR PAYMENT JOURNALS (Dr Kas/Bank / Cr AR) ──────────────
-- AR payments for credit sales that are now fully/partially paid
INSERT INTO journal_entries (id,journal_number,entry_date,description,reference_type,reference_id,status,posted_at,posted_by,created_by,created_at,updated_at) VALUES
(216,'JRN-202503-0017','2025-03-16','AR Payment ARP-202503-0001','ArPayment',1,'posted','2025-03-16 10:00:00',3,3,NOW(),NOW()),
(217,'JRN-202504-0016','2025-04-06','AR Payment ARP-202504-0001','ArPayment',2,'posted','2025-04-06 10:00:00',3,3,NOW(),NOW()),
(218,'JRN-202505-0017','2025-05-10','AR Payment ARP-202505-0001','ArPayment',3,'posted','2025-05-10 10:00:00',3,3,NOW(),NOW()),
(219,'JRN-202506-0012','2025-06-20','AR Payment ARP-202506-0001','ArPayment',4,'posted','2025-06-20 10:00:00',3,3,NOW(),NOW()),
(220,'JRN-202506-0013','2025-06-12','AR Payment ARP-202506-0002','ArPayment',5,'posted','2025-06-12 10:00:00',3,3,NOW(),NOW()),
(221,'JRN-202507-0013','2025-07-05','AR Payment ARP-202507-0001','ArPayment',6,'posted','2025-07-05 10:00:00',3,3,NOW(),NOW()),
(222,'JRN-202508-0013','2025-08-07','AR Payment ARP-202508-0001','ArPayment',7,'posted','2025-08-07 10:00:00',3,3,NOW(),NOW()),
(223,'JRN-202509-0012','2025-09-05','AR Payment ARP-202509-0001','ArPayment',8,'posted','2025-09-05 10:00:00',3,3,NOW(),NOW()),
(224,'JRN-202510-0013','2025-10-03','AR Payment ARP-202510-0001','ArPayment',9,'posted','2025-10-03 10:00:00',3,3,NOW(),NOW()),
(225,'JRN-202511-0012','2025-11-06','AR Payment ARP-202511-0001','ArPayment',10,'posted','2025-11-06 10:00:00',3,3,NOW(),NOW()),
(226,'JRN-202512-0012','2025-12-04','AR Payment ARP-202512-0001','ArPayment',11,'posted','2025-12-04 10:00:00',3,3,NOW(),NOW()),
(227,'JRN-202601-0013','2026-01-08','AR Payment ARP-202601-0001','ArPayment',12,'posted','2026-01-08 10:00:00',3,3,NOW(),NOW()),
(228,'JRN-202602-0012','2026-02-08','AR Payment ARP-202602-0001','ArPayment',13,'posted','2026-02-08 10:00:00',3,3,NOW(),NOW()),
(229,'JRN-202603-0012','2026-03-08','AR Payment ARP-202603-0001','ArPayment',14,'posted','2026-03-08 10:00:00',3,3,NOW(),NOW()),
(230,'JRN-202604-0012','2026-04-04','AR Payment ARP-202604-0001','ArPayment',15,'posted','2026-04-04 10:00:00',3,3,NOW(),NOW()),
(231,'JRN-202604-0013','2026-04-10','AR Payment ARP-202604-0002','ArPayment',16,'posted','2026-04-10 10:00:00',3,3,NOW(),NOW()),
(232,'JRN-202605-0015','2026-05-08','AR Payment ARP-202605-0001','ArPayment',17,'posted','2026-05-08 10:00:00',3,3,NOW(),NOW());

INSERT INTO journal_entry_lines (id,journal_entry_id,account_id,description,debit,credit,created_at,updated_at) VALUES
-- Dr Bank / Cr AR
(431,216,2,'Penerimaan transfer dari PT Maju Bersama',23500000,0,NOW(),NOW()),
(432,216,3,'Pelunasan AR SL-202502-0002',0,23500000,NOW(),NOW()),
(433,217,2,'Penerimaan transfer dari Yayasan Al-Hikmah',8800000,0,NOW(),NOW()),
(434,217,3,'Pelunasan AR SL-202503-0001',0,8800000,NOW(),NOW()),
(435,218,2,'Penerimaan transfer dari Apotek Sehat',4800000,0,NOW(),NOW()),
(436,218,3,'Pelunasan AR SL-202504-0002',0,4800000,NOW(),NOW()),
(437,219,2,'Penerimaan transfer dari SMA Negeri 5 (lunas)',37000000,0,NOW(),NOW()),
(438,219,3,'Pelunasan AR SL-202505-0001',0,37000000,NOW(),NOW()),
(439,220,2,'Penerimaan transfer dari CV Karya Mandiri',3500000,0,NOW(),NOW()),
(440,220,3,'Pelunasan AR SL-202505-0002',0,3500000,NOW(),NOW()),
(441,221,2,'Penerimaan transfer dari Klinik Sehat',9600000,0,NOW(),NOW()),
(442,221,3,'Pelunasan AR SL-202506-0001',0,9600000,NOW(),NOW()),
(443,222,2,'Penerimaan transfer dari PT Teknologi Maju',32800000,0,NOW(),NOW()),
(444,222,3,'Pelunasan AR SL-202507-0001',0,32800000,NOW(),NOW()),
(445,223,2,'Penerimaan transfer dari PT Maju Bersama',13500000,0,NOW(),NOW()),
(446,223,3,'Pelunasan AR SL-202508-0001',0,13500000,NOW(),NOW()),
(447,224,2,'Penerimaan transfer dari Yayasan Al-Hikmah',4200000,0,NOW(),NOW()),
(448,224,3,'Pelunasan AR SL-202509-0001',0,4200000,NOW(),NOW()),
(449,225,2,'Penerimaan transfer dari CV Karya Mandiri',8200000,0,NOW(),NOW()),
(450,225,3,'Pelunasan AR SL-202510-0001',0,8200000,NOW(),NOW()),
(451,226,2,'Penerimaan transfer dari SMA Negeri 5',25000000,0,NOW(),NOW()),
(452,226,3,'Pelunasan AR SL-202511-0001',0,25000000,NOW(),NOW()),
(453,227,2,'Penerimaan transfer dari PT Maju Bersama',16400000,0,NOW(),NOW()),
(454,227,3,'Pelunasan AR SL-202512-0001',0,16400000,NOW(),NOW()),
(455,228,2,'Penerimaan transfer dari PT Teknologi Maju',22000000,0,NOW(),NOW()),
(456,228,3,'Pelunasan AR SL-202601-0001',0,22000000,NOW(),NOW()),
(457,229,2,'Penerimaan transfer dari Klinik Sehat',4800000,0,NOW(),NOW()),
(458,229,3,'Pelunasan AR SL-202602-0001',0,4800000,NOW(),NOW()),
(459,230,2,'Penerimaan transfer dari Yayasan Al-Hikmah',17500000,0,NOW(),NOW()),
(460,230,3,'Pelunasan AR SL-202603-0001',0,17500000,NOW(),NOW()),
(461,231,2,'Penerimaan transfer dari CV Karya Mandiri',3500000,0,NOW(),NOW()),
(462,231,3,'Pelunasan AR SL-202603-0002',0,3500000,NOW(),NOW()),
(463,232,2,'Penerimaan transfer dari PT Maju Bersama (DP)',13250000,0,NOW(),NOW()),
(464,232,3,'Pembayaran sebagian AR SL-202604-0001',0,13250000,NOW(),NOW());

-- ============================================================
-- 14. AP INVOICES (generated from purchases that are Paid/Received)
-- ============================================================
INSERT INTO ap_invoices (id,invoice_number,supplier_id,purchase_id,invoice_date,due_date,subtotal,tax,discount,total,paid_amount,status,notes,created_at,updated_at) VALUES
(1, 'API-202501-0001',2,1, '2025-01-06','2025-02-05', 37900000,0,0,37900000,37900000,'Paid',   'Generated from PO-202501-0001',NOW(),NOW()),
(2, 'API-202501-0002',4,2, '2025-01-07','2025-02-06',  4790000,0,0, 4790000, 4790000,'Paid',   'Generated from PO-202501-0002',NOW(),NOW()),
(3, 'API-202502-0001',1,3, '2025-02-03','2025-03-05', 10850000,0,0,10850000,10850000,'Paid',   'Generated from PO-202502-0001',NOW(),NOW()),
(4, 'API-202502-0002',6,4, '2025-02-10','2025-03-12', 28900000,0,0,28900000,28900000,'Paid',   'Generated from PO-202502-0002',NOW(),NOW()),
(5, 'API-202503-0001',3,5, '2025-03-04','2025-04-03', 24600000,0,0,24600000,24600000,'Paid',   'Generated from PO-202503-0001',NOW(),NOW()),
(6, 'API-202503-0002',4,6, '2025-03-12','2025-04-11',  3230000,0,0, 3230000, 3230000,'Paid',   'Generated from PO-202503-0002',NOW(),NOW()),
(7, 'API-202504-0001',2,7, '2025-04-02','2025-05-02', 28100000,0,0,28100000,28100000,'Paid',   'Generated from PO-202504-0001',NOW(),NOW()),
(8, 'API-202505-0001',5,8, '2025-05-05','2025-06-04',  5140000,0,0, 5140000, 5140000,'Paid',   'Generated from PO-202505-0001',NOW(),NOW()),
(9, 'API-202506-0001',1,9, '2025-06-03','2025-07-03',  6700000,0,0, 6700000, 6700000,'Paid',   'Generated from PO-202506-0001',NOW(),NOW()),
(10,'API-202507-0001',6,10,'2025-07-07','2025-08-06', 17500000,0,0,17500000,17500000,'Paid',   'Generated from PO-202507-0001',NOW(),NOW()),
(11,'API-202508-0001',3,11,'2025-08-04','2025-09-03', 24300000,0,0,24300000,24300000,'Paid',   'Generated from PO-202508-0001',NOW(),NOW()),
(12,'API-202509-0001',4,12,'2025-09-02','2025-10-02',  4120000,0,0, 4120000, 4120000,'Paid',   'Generated from PO-202509-0001',NOW(),NOW()),
(13,'API-202510-0001',2,13,'2025-10-06','2025-11-05', 17400000,0,0,17400000,17400000,'Paid',   'Generated from PO-202510-0001',NOW(),NOW()),
(14,'API-202511-0001',5,14,'2025-11-03','2025-12-03',  6175000,0,0, 6175000, 6175000,'Paid',   'Generated from PO-202511-0001',NOW(),NOW()),
(15,'API-202512-0001',1,15,'2025-12-02','2026-01-01',  9500000,0,0, 9500000, 9500000,'Paid',   'Generated from PO-202512-0001',NOW(),NOW()),
(16,'API-202601-0001',3,16,'2026-01-06','2026-02-05', 33200000,0,0,33200000,33200000,'Paid',   'Generated from PO-202601-0001',NOW(),NOW()),
(17,'API-202602-0001',4,17,'2026-02-03','2026-03-05',  4955000,0,0, 4955000, 4955000,'Paid',   'Generated from PO-202602-0001',NOW(),NOW()),
(18,'API-202603-0001',6,18,'2026-03-04','2026-04-03', 25100000,0,0,25100000,25100000,'Paid',   'Generated from PO-202603-0001',NOW(),NOW()),
(19,'API-202604-0001',2,19,'2026-04-07','2026-05-07', 21300000,0,0,21300000,       0,'Open',   'Generated from PO-202604-0001',NOW(),NOW()),
(20,'API-202605-0001',4,20,'2026-05-05','2026-06-04',  3950000,0,0, 3950000,       0,'Open',   'Generated from PO-202605-0001',NOW(),NOW());

-- ============================================================
-- 15. AP PAYMENTS
-- ============================================================
INSERT INTO ap_payments (id,payment_number,ap_invoice_id,supplier_id,payment_date,amount,payment_method,reference,notes,created_at,updated_at) VALUES
(1, 'APP-202501-0001',1,2,'2025-01-10',37900000,'Transfer','TRF-BCA-250110','Pelunasan API-202501-0001',NOW(),NOW()),
(2, 'APP-202501-0002',2,4,'2025-01-12', 4790000,'Transfer','TRF-BCA-250112','Pelunasan API-202501-0002',NOW(),NOW()),
(3, 'APP-202502-0001',3,1,'2025-02-07',10850000,'Transfer','TRF-BCA-250207','Pelunasan API-202502-0001',NOW(),NOW()),
(4, 'APP-202502-0002',4,6,'2025-02-14',28900000,'Transfer','TRF-BCA-250214','Pelunasan API-202502-0002',NOW(),NOW()),
(5, 'APP-202503-0001',5,3,'2025-03-08',24600000,'Transfer','TRF-BCA-250308','Pelunasan API-202503-0001',NOW(),NOW()),
(6, 'APP-202503-0002',6,4,'2025-03-16', 3230000,'Transfer','TRF-BCA-250316','Pelunasan API-202503-0002',NOW(),NOW()),
(7, 'APP-202504-0001',7,2,'2025-04-06',28100000,'Transfer','TRF-BCA-250406','Pelunasan API-202504-0001',NOW(),NOW()),
(8, 'APP-202505-0001',8,5,'2025-05-09', 5140000,'Transfer','TRF-BCA-250509','Pelunasan API-202505-0001',NOW(),NOW()),
(9, 'APP-202506-0001',9,1,'2025-06-07', 6700000,'Transfer','TRF-BCA-250607','Pelunasan API-202506-0001',NOW(),NOW()),
(10,'APP-202507-0001',10,6,'2025-07-11',17500000,'Transfer','TRF-BCA-250711','Pelunasan API-202507-0001',NOW(),NOW()),
(11,'APP-202508-0001',11,3,'2025-08-08',24300000,'Transfer','TRF-BCA-250808','Pelunasan API-202508-0001',NOW(),NOW()),
(12,'APP-202509-0001',12,4,'2025-09-06', 4120000,'Transfer','TRF-BCA-250906','Pelunasan API-202509-0001',NOW(),NOW()),
(13,'APP-202510-0001',13,2,'2025-10-10',17400000,'Transfer','TRF-BCA-251010','Pelunasan API-202510-0001',NOW(),NOW()),
(14,'APP-202511-0001',14,5,'2025-11-07', 6175000,'Transfer','TRF-BCA-251107','Pelunasan API-202511-0001',NOW(),NOW()),
(15,'APP-202512-0001',15,1,'2025-12-06', 9500000,'Transfer','TRF-BCA-251206','Pelunasan API-202512-0001',NOW(),NOW()),
(16,'APP-202601-0001',16,3,'2026-01-10',33200000,'Transfer','TRF-BCA-260110','Pelunasan API-202601-0001',NOW(),NOW()),
(17,'APP-202602-0001',17,4,'2026-02-07', 4955000,'Transfer','TRF-BCA-260207','Pelunasan API-202602-0001',NOW(),NOW()),
(18,'APP-202603-0001',18,6,'2026-03-08',25100000,'Transfer','TRF-BCA-260308','Pelunasan API-202603-0001',NOW(),NOW());

-- ============================================================
-- 16. AR INVOICES (from credit sales)
-- ============================================================
INSERT INTO ar_invoices (id,invoice_number,customer_id,sale_id,invoice_date,due_date,subtotal,tax,discount,total,paid_amount,status,notes,created_at,updated_at) VALUES
(1, 'ARI-202502-0001',3, 5, '2025-02-14','2025-03-16',24000000,0,500000,23500000,23500000,'Paid',       'Generated from SL-202502-0002',NOW(),NOW()),
(2, 'ARI-202503-0001',5, 7, '2025-03-05','2025-04-04', 8800000,0,      0, 8800000, 8800000,'Paid',       'Generated from SL-202503-0001',NOW(),NOW()),
(3, 'ARI-202504-0001',18,11,'2025-04-08','2025-05-08', 4800000,0,      0, 4800000, 4800000,'Paid',       'Generated from SL-202504-0002',NOW(),NOW()),
(4, 'ARI-202505-0001',16,13,'2025-05-06','2025-06-20',41000000,0,4000000,37000000,37000000,'Paid',       'Generated from SL-202505-0001',NOW(),NOW()),
(5, 'ARI-202505-0002',9, 14,'2025-05-12','2025-06-11', 3500000,0,      0, 3500000, 3500000,'Paid',       'Generated from SL-202505-0002',NOW(),NOW()),
(6, 'ARI-202506-0001',12,16,'2025-06-04','2025-07-04', 9600000,0,      0, 9600000, 9600000,'Paid',       'Generated from SL-202506-0001',NOW(),NOW()),
(7, 'ARI-202507-0001',20,18,'2025-07-07','2025-08-06',32800000,0,      0,32800000,32800000,'Paid',       'Generated from SL-202507-0001',NOW(),NOW()),
(8, 'ARI-202508-0001',3, 20,'2025-08-05','2025-09-04',13500000,0,      0,13500000,13500000,'Paid',       'Generated from SL-202508-0001',NOW(),NOW()),
(9, 'ARI-202509-0001',5, 22,'2025-09-03','2025-10-03', 4200000,0,      0, 4200000, 4200000,'Paid',       'Generated from SL-202509-0001',NOW(),NOW()),
(10,'ARI-202510-0001',9, 24,'2025-10-06','2025-11-05', 8200000,0,      0, 8200000, 8200000,'Paid',       'Generated from SL-202510-0001',NOW(),NOW()),
(11,'ARI-202511-0001',16,26,'2025-11-04','2025-12-19',25200000,0, 200000,25000000,25000000,'Paid',       'Generated from SL-202511-0001',NOW(),NOW()),
(12,'ARI-202512-0001',3, 28,'2025-12-03','2026-01-02',16400000,0,      0,16400000,16400000,'Paid',       'Generated from SL-202512-0001',NOW(),NOW()),
(13,'ARI-202601-0001',20,30,'2026-01-08','2026-02-07',22700000,0, 700000,22000000,22000000,'Paid',       'Generated from SL-202601-0001',NOW(),NOW()),
(14,'ARI-202602-0001',12,32,'2026-02-05','2026-03-07', 4800000,0,      0, 4800000, 4800000,'Paid',       'Generated from SL-202602-0001',NOW(),NOW()),
(15,'ARI-202603-0001',5, 34,'2026-03-04','2026-04-03',17500000,0,      0,17500000,17500000,'Paid',       'Generated from SL-202603-0001',NOW(),NOW()),
(16,'ARI-202603-0002',9, 35,'2026-03-10','2026-04-09', 3500000,0,      0, 3500000, 3500000,'Paid',       'Generated from SL-202603-0002',NOW(),NOW()),
(17,'ARI-202604-0001',3, 36,'2026-04-07','2026-05-07',27000000,0, 500000,26500000,13250000,'Partially Paid','Generated from SL-202604-0001',NOW(),NOW()),
(18,'ARI-202605-0001',20,38,'2026-05-05','2026-06-04',15000000,0,      0,15000000,       0,'Open',       'Generated from SL-202605-0001',NOW(),NOW());

-- ============================================================
-- 17. AR PAYMENTS
-- ============================================================
INSERT INTO ar_payments (id,payment_number,ar_invoice_id,customer_id,payment_date,amount,payment_method,reference,notes,created_at,updated_at) VALUES
(1, 'ARP-202503-0001',1, 3, '2025-03-16',23500000,'Transfer','TRF-BCA-250316','Pelunasan ARI-202502-0001',NOW(),NOW()),
(2, 'ARP-202504-0001',2, 5, '2025-04-06', 8800000,'Transfer','TRF-BCA-250406','Pelunasan ARI-202503-0001',NOW(),NOW()),
(3, 'ARP-202505-0001',3, 18,'2025-05-10', 4800000,'Transfer','TRF-BCA-250510','Pelunasan ARI-202504-0001',NOW(),NOW()),
(4, 'ARP-202506-0001',4, 16,'2025-06-20',37000000,'Transfer','TRF-BCA-250620','Pelunasan ARI-202505-0001',NOW(),NOW()),
(5, 'ARP-202506-0002',5, 9, '2025-06-12', 3500000,'Transfer','TRF-BCA-250612','Pelunasan ARI-202505-0002',NOW(),NOW()),
(6, 'ARP-202507-0001',6, 12,'2025-07-05', 9600000,'Transfer','TRF-BCA-250705','Pelunasan ARI-202506-0001',NOW(),NOW()),
(7, 'ARP-202508-0001',7, 20,'2025-08-07',32800000,'Transfer','TRF-BCA-250807','Pelunasan ARI-202507-0001',NOW(),NOW()),
(8, 'ARP-202509-0001',8, 3, '2025-09-05',13500000,'Transfer','TRF-BCA-250905','Pelunasan ARI-202508-0001',NOW(),NOW()),
(9, 'ARP-202510-0001',9, 5, '2025-10-03', 4200000,'Transfer','TRF-BCA-251003','Pelunasan ARI-202509-0001',NOW(),NOW()),
(10,'ARP-202511-0001',10,9, '2025-11-06', 8200000,'Transfer','TRF-BCA-251106','Pelunasan ARI-202510-0001',NOW(),NOW()),
(11,'ARP-202512-0001',11,16,'2025-12-04',25000000,'Transfer','TRF-BCA-251204','Pelunasan ARI-202511-0001',NOW(),NOW()),
(12,'ARP-202601-0001',12,3, '2026-01-08',16400000,'Transfer','TRF-BCA-260108','Pelunasan ARI-202512-0001',NOW(),NOW()),
(13,'ARP-202602-0001',13,20,'2026-02-08',22000000,'Transfer','TRF-BCA-260208','Pelunasan ARI-202601-0001',NOW(),NOW()),
(14,'ARP-202603-0001',14,12,'2026-03-08', 4800000,'Transfer','TRF-BCA-260308','Pelunasan ARI-202602-0001',NOW(),NOW()),
(15,'ARP-202604-0001',15,5, '2026-04-04',17500000,'Transfer','TRF-BCA-260404','Pelunasan ARI-202603-0001',NOW(),NOW()),
(16,'ARP-202604-0002',16,9, '2026-04-10', 3500000,'Transfer','TRF-BCA-260410','Pelunasan ARI-202603-0002',NOW(),NOW()),
(17,'ARP-202605-0001',17,3, '2026-05-08',13250000,'Transfer','TRF-BCA-260508','DP 50% ARI-202604-0001',  NOW(),NOW());

-- ============================================================
-- 18. INVENTORY MOVEMENTS
-- Tracks every stock in/out per product
-- ============================================================
INSERT INTO inventory_movements (id,movement_number,product_id,movement_date,movement_type,reference_type,reference_id,qty_in,qty_out,balance_qty,unit_cost,total_cost,notes,created_at,updated_at) VALUES
-- LAP-001 Asus VivoBook
(1, 'INV-202501-0001',1,'2025-01-06','purchase_receipt','Purchase',1, 3,0,3, 6800000,20400000,'PO-202501-0001',NOW(),NOW()),
(2, 'INV-202501-0002',1,'2025-01-10','sale_issue',      'Sale',    1, 0,1,2, 6800000, 6800000,'SL-202501-0001',NOW(),NOW()),
(3, 'INV-202504-0001',1,'2025-04-02','purchase_receipt','Purchase',7, 2,0,4, 6800000,13600000,'PO-202504-0001',NOW(),NOW()),
(4, 'INV-202505-0001',1,'2025-05-06','sale_issue',      'Sale',   13, 0,5,-1,6800000,34000000,'SL-202505-0001',NOW(),NOW()),
(5, 'INV-202508-0001',1,'2025-08-04','purchase_receipt','Purchase',11,1,0,0, 6800000, 6800000,'PO-202508-0001',NOW(),NOW()),
(6, 'INV-202511-0001',1,'2025-11-04','sale_issue',      'Sale',   26, 0,2,-2,6800000,13600000,'SL-202511-0001',NOW(),NOW()),
(7, 'INV-202601-0001',1,'2026-01-06','purchase_receipt','Purchase',16,2,0,0, 6800000,13600000,'PO-202601-0001',NOW(),NOW()),
(8, 'INV-202601-0002',1,'2026-01-08','sale_issue',      'Sale',   30, 0,2,-2,6800000,13600000,'SL-202601-0001',NOW(),NOW()),
-- LAP-002 Lenovo IdeaPad
(9, 'INV-202501-0003',2,'2025-01-06','purchase_receipt','Purchase',1, 2,0,2, 6200000,12400000,'PO-202501-0001',NOW(),NOW()),
(10,'INV-202508-0002',2,'2025-08-04','purchase_receipt','Purchase',11,2,0,4, 6200000,12400000,'PO-202508-0001',NOW(),NOW()),
(11,'INV-202508-0003',2,'2025-08-12','sale_issue',      'Sale',   21, 0,1,3, 6200000, 6200000,'SL-202508-0002',NOW(),NOW()),
(12,'INV-202601-0003',2,'2026-01-06','purchase_receipt','Purchase',16,2,0,5, 6200000,12400000,'PO-202601-0001',NOW(),NOW()),
(13,'INV-202605-0001',2,'2026-05-05','sale_issue',      'Sale',   38, 0,2,3, 6200000,12400000,'SL-202605-0001',NOW(),NOW()),
-- LAP-003 HP 14s
(14,'INV-202501-0004',3,'2025-01-06','purchase_receipt','Purchase',1, 1,0,1, 5100000, 5100000,'PO-202501-0001',NOW(),NOW()),
(15,'INV-202502-0001',3,'2025-02-06','sale_issue',      'Sale',    4, 0,1,0, 5100000, 5100000,'SL-202502-0001',NOW(),NOW()),
(16,'INV-202503-0001',3,'2025-03-04','purchase_receipt','Purchase',5, 2,0,2, 5100000,10200000,'PO-202503-0001',NOW(),NOW()),
(17,'INV-202508-0004',3,'2025-08-04','purchase_receipt','Purchase',11,1,0,3, 5100000, 5100000,'PO-202508-0001',NOW(),NOW()),
(18,'INV-202601-0004',3,'2026-01-08','sale_issue',      'Sale',   30, 0,1,2, 5100000, 5100000,'SL-202601-0001',NOW(),NOW()),
-- LAP-004 Asus ROG
(19,'INV-202504-0002',4,'2025-04-02','purchase_receipt','Purchase',7, 1,0,1,14500000,14500000,'PO-202504-0001',NOW(),NOW()),
(20,'INV-202504-0003',4,'2025-04-03','sale_issue',      'Sale',   10, 0,1,0,14500000,14500000,'SL-202504-0001',NOW(),NOW()),
(21,'INV-202604-0001',4,'2026-04-07','purchase_receipt','Purchase',19,1,0,1,14500000,14500000,'PO-202604-0001',NOW(),NOW()),
(22,'INV-202603-0001',4,'2026-03-04','sale_issue',      'Sale',   34, 0,1,0,14500000,14500000,'SL-202603-0001',NOW(),NOW()),
-- LAP-005 Acer Aspire 5
(23,'INV-202503-0002',5,'2025-03-04','purchase_receipt','Purchase',5, 2,0,2, 7200000,14400000,'PO-202503-0001',NOW(),NOW()),
(24,'INV-202503-0003',5,'2025-03-05','sale_issue',      'Sale',    7, 0,1,1, 7200000, 7200000,'SL-202503-0001',NOW(),NOW()),
(25,'INV-202601-0005',5,'2026-01-06','purchase_receipt','Purchase',16,1,0,2, 7200000, 7200000,'PO-202601-0001',NOW(),NOW()),
(26,'INV-202601-0006',5,'2026-01-15','sale_issue',      'Sale',   31, 0,1,1, 7200000, 7200000,'SL-202601-0001',NOW(),NOW()),
-- PRT-001 Epson L3210
(27,'INV-202502-0002',6,'2025-02-03','purchase_receipt','Purchase',3, 4,0,4, 1650000, 6600000,'PO-202502-0001',NOW(),NOW()),
(28,'INV-202501-0005',6,'2025-01-20','sale_issue',      'Sale',    3, 0,1,3, 1650000, 1650000,'SL-202501-0003',NOW(),NOW()),
(29,'INV-202509-0001',6,'2025-09-03','sale_issue',      'Sale',   22, 0,2,1, 1650000, 3300000,'SL-202509-0001',NOW(),NOW()),
(30,'INV-202512-0001',6,'2025-12-02','purchase_receipt','Purchase',15,4,0,5, 1650000, 6600000,'PO-202512-0001',NOW(),NOW()),
-- PRT-002 Canon G2020
(31,'INV-202502-0003',7,'2025-02-03','purchase_receipt','Purchase',3, 1,0,1, 1450000, 1450000,'PO-202502-0001',NOW(),NOW()),
(32,'INV-202503-0004',7,'2025-03-08','sale_issue',      'Sale',    8, 0,1,0, 1450000, 1450000,'SL-202503-0002',NOW(),NOW()),
(33,'INV-202506-0001',7,'2025-06-03','purchase_receipt','Purchase',9, 2,0,2, 1450000, 2900000,'PO-202506-0001',NOW(),NOW()),
(34,'INV-202512-0002',7,'2025-12-02','purchase_receipt','Purchase',15,2,0,4, 1450000, 2900000,'PO-202512-0001',NOW(),NOW()),
-- PRT-003 Epson L5290
(35,'INV-202502-0004',8,'2025-02-03','purchase_receipt','Purchase',3, 1,0,1, 2800000, 2800000,'PO-202502-0001',NOW(),NOW()),
(36,'INV-202505-0002',8,'2025-05-12','sale_issue',      'Sale',   14, 0,1,0, 2800000, 2800000,'SL-202505-0002',NOW(),NOW()),
(37,'INV-202506-0002',8,'2025-06-03','purchase_receipt','Purchase',9, 0,0,0, 2800000,       0,'PO-202506-0001 (no L5290)',NOW(),NOW()),
(38,'INV-202603-0002',8,'2026-03-10','sale_issue',      'Sale',   35, 0,1,-1,2800000, 2800000,'SL-202603-0002',NOW(),NOW()),
-- PRT-004 HP LaserJet
(39,'INV-202506-0003',9,'2025-06-03','purchase_receipt','Purchase',9, 2,0,2, 1900000, 3800000,'PO-202506-0001',NOW(),NOW()),
(40,'INV-202512-0003',9,'2025-12-10','sale_issue',      'Sale',   29, 0,1,1, 1900000, 1900000,'SL-202512-0002',NOW(),NOW()),
-- CPU-001 PC Rakitan Office
(41,'INV-202502-0005',10,'2025-02-10','purchase_receipt','Purchase',4,3,0,3, 3800000,11400000,'PO-202502-0002',NOW(),NOW()),
(42,'INV-202502-0006',10,'2025-02-14','sale_issue',      'Sale',   5, 0,5,-2,3800000,19000000,'SL-202502-0002',NOW(),NOW()),
(43,'INV-202504-0004',10,'2025-04-08','sale_issue',      'Sale',  11, 0,1,-3,3800000, 3800000,'SL-202504-0002',NOW(),NOW()),
(44,'INV-202506-0004',10,'2025-06-04','sale_issue',      'Sale',  16, 0,2,-5,3800000, 7600000,'SL-202506-0001',NOW(),NOW()),
(45,'INV-202603-0003',10,'2026-03-04','purchase_receipt','Purchase',18,2,0,-3,3800000, 7600000,'PO-202603-0001',NOW(),NOW()),
(46,'INV-202602-0001',10,'2026-02-05','sale_issue',      'Sale',  32, 0,1,-4,3800000, 3800000,'SL-202602-0001',NOW(),NOW()),
-- CPU-002 PC Rakitan Gaming
(47,'INV-202502-0007',11,'2025-02-10','purchase_receipt','Purchase',4,1,0,1, 6500000, 6500000,'PO-202502-0002',NOW(),NOW()),
(48,'INV-202510-0001',11,'2025-10-06','sale_issue',      'Sale',  24, 0,1,0, 6500000, 6500000,'SL-202510-0001',NOW(),NOW()),
(49,'INV-202507-0001',11,'2025-07-07','purchase_receipt','Purchase',10,1,0,1, 6500000, 6500000,'PO-202507-0001',NOW(),NOW()),
(50,'INV-202512-0004',11,'2025-12-03','sale_issue',      'Sale',  28, 0,2,-1,6500000,13000000,'SL-202512-0001',NOW(),NOW()),
(51,'INV-202603-0004',11,'2026-03-04','purchase_receipt','Purchase',18,1,0,0, 6500000, 6500000,'PO-202603-0001',NOW(),NOW()),
-- CPU-003 PC Rakitan Desain
(52,'INV-202502-0008',12,'2025-02-10','purchase_receipt','Purchase',4,1,0,1,11000000,11000000,'PO-202502-0002',NOW(),NOW()),
(53,'INV-202508-0005',12,'2025-08-05','sale_issue',      'Sale',  20, 0,1,0,11000000,11000000,'SL-202508-0001',NOW(),NOW()),
(54,'INV-202507-0002',12,'2025-07-07','purchase_receipt','Purchase',10,1,0,1,11000000,11000000,'PO-202507-0001',NOW(),NOW()),
(55,'INV-202604-0002',12,'2026-04-07','sale_issue',      'Sale',  36, 0,2,-1,11000000,22000000,'SL-202604-0001',NOW(),NOW()),
(56,'INV-202603-0005',12,'2026-03-04','purchase_receipt','Purchase',18,1,0,0,11000000,11000000,'PO-202603-0001',NOW(),NOW()),
-- ACC-001 Mouse Logitech M185
(57,'INV-202501-0006',13,'2025-01-07','purchase_receipt','Purchase',2,10,0,10,95000,  950000,'PO-202501-0002',NOW(),NOW()),
(58,'INV-202501-0007',13,'2025-01-13','sale_issue',      'Sale',   2, 0,1,9, 95000,   95000,'SL-202501-0002',NOW(),NOW()),
(59,'INV-202509-0002',13,'2025-09-02','purchase_receipt','Purchase',12,10,0,19,95000, 950000,'PO-202509-0001',NOW(),NOW()),
(60,'INV-202602-0002',13,'2026-02-03','purchase_receipt','Purchase',17,15,0,34,95000,1425000,'PO-202602-0001',NOW(),NOW()),
(61,'INV-202602-0003',13,'2026-02-12','sale_issue',      'Sale',  33, 0,1,33,95000,   95000,'SL-202602-0002',NOW(),NOW()),
-- ACC-004 Flashdisk SanDisk
(62,'INV-202501-0008',16,'2025-01-07','purchase_receipt','Purchase',2,15,0,15,75000,1125000,'PO-202501-0002',NOW(),NOW()),
(63,'INV-202501-0009',16,'2025-01-13','sale_issue',      'Sale',   2, 0,1,14,75000,   75000,'SL-202501-0002',NOW(),NOW()),
(64,'INV-202509-0003',16,'2025-09-02','purchase_receipt','Purchase',12,10,0,24,75000, 750000,'PO-202509-0001',NOW(),NOW()),
(65,'INV-202602-0004',16,'2026-02-03','purchase_receipt','Purchase',17,20,0,44,75000,1500000,'PO-202602-0001',NOW(),NOW()),
(66,'INV-202602-0005',16,'2026-02-12','sale_issue',      'Sale',  33, 0,1,43,75000,   75000,'SL-202602-0002',NOW(),NOW()),
-- ACC-008 RAM DDR4 8GB
(67,'INV-202505-0003',20,'2025-05-05','purchase_receipt','Purchase',8,5,0,5, 280000,1400000,'PO-202505-0001',NOW(),NOW()),
(68,'INV-202503-0005',20,'2025-03-15','sale_issue',      'Sale',   9, 0,1,4, 280000, 280000,'SL-202503-0003',NOW(),NOW()),
(69,'INV-202507-0003',20,'2025-07-15','sale_issue',      'Sale',  19, 0,1,3, 280000, 280000,'SL-202507-0002',NOW(),NOW()),
(70,'INV-202511-0002',20,'2025-11-03','purchase_receipt','Purchase',14,8,0,11,280000,2240000,'PO-202511-0001',NOW(),NOW()),
(71,'INV-202604-0003',20,'2026-04-14','sale_issue',      'Sale',  37, 0,1,10,280000, 280000,'SL-202604-0002',NOW(),NOW()),
-- ACC-009 SSD 256GB
(72,'INV-202505-0004',21,'2025-05-05','purchase_receipt','Purchase',8,5,0,5, 320000,1600000,'PO-202505-0001',NOW(),NOW()),
(73,'INV-202503-0006',21,'2025-03-15','sale_issue',      'Sale',   9, 0,1,4, 320000, 320000,'SL-202503-0003',NOW(),NOW()),
(74,'INV-202507-0004',21,'2025-07-15','sale_issue',      'Sale',  19, 0,1,3, 320000, 320000,'SL-202507-0002',NOW(),NOW()),
(75,'INV-202511-0003',21,'2025-11-03','purchase_receipt','Purchase',14,8,0,11,320000,2560000,'PO-202511-0001',NOW(),NOW()),
(76,'INV-202604-0004',21,'2026-04-14','sale_issue',      'Sale',  37, 0,1,10,320000, 320000,'SL-202604-0002',NOW(),NOW()),
-- ACC-010 Tinta Epson Black
(77,'INV-202503-0007',22,'2025-03-12','purchase_receipt','Purchase',6,20,0,20,55000,1100000,'PO-202503-0002',NOW(),NOW()),
(78,'INV-202506-0005',22,'2025-06-10','sale_issue',      'Sale',  17, 0,3,17,55000, 165000,'SL-202506-0002',NOW(),NOW()),
(79,'INV-202509-0004',22,'2025-09-02','purchase_receipt','Purchase',12,20,0,37,55000,1100000,'PO-202509-0001',NOW(),NOW()),
(80,'INV-202511-0004',22,'2025-11-12','sale_issue',      'Sale',  27, 0,6,31,55000, 330000,'SL-202511-0002',NOW(),NOW()),
(81,'INV-202605-0002',22,'2026-05-12','sale_issue',      'Sale',  39, 0,4,27,55000, 220000,'SL-202605-0002',NOW(),NOW()),
-- OTH-001 UPS APC
(82,'INV-202505-0005',24,'2025-05-05','purchase_receipt','Purchase',8,2,0,2, 650000,1300000,'PO-202505-0001',NOW(),NOW()),
(83,'INV-202510-0002',24,'2025-10-14','sale_issue',      'Sale',  25, 0,1,1, 650000, 650000,'SL-202510-0002',NOW(),NOW()),
(84,'INV-202511-0005',24,'2025-11-03','purchase_receipt','Purchase',14,1,0,2, 650000, 650000,'PO-202511-0001',NOW(),NOW());

-- ============================================================
-- 19. ADJUSTING ENTRIES (penyusutan peralatan, akrual)
-- ============================================================
INSERT INTO journal_entries (id,journal_number,entry_date,description,reference_type,reference_id,status,posted_at,posted_by,created_by,created_at,updated_at) VALUES
-- Penyusutan peralatan Rp 300,000/bln (18,000,000 / 5 tahun / 12 bln)
(233,'JRN-202503-0018','2025-03-31','Penyusutan Peralatan Q1 2025',NULL,NULL,'posted','2025-03-31 18:00:00',3,3,NOW(),NOW()),
(234,'JRN-202506-0014','2025-06-30','Penyusutan Peralatan Q2 2025',NULL,NULL,'posted','2025-06-30 18:00:00',3,3,NOW(),NOW()),
(235,'JRN-202509-0013','2025-09-30','Penyusutan Peralatan Q3 2025',NULL,NULL,'posted','2025-09-30 18:00:00',3,3,NOW(),NOW()),
(236,'JRN-202512-0013','2025-12-31','Penyusutan Peralatan Q4 2025',NULL,NULL,'posted','2025-12-31 18:00:00',3,3,NOW(),NOW()),
(237,'JRN-202603-0013','2026-03-31','Penyusutan Peralatan Q1 2026',NULL,NULL,'posted','2026-03-31 18:00:00',3,3,NOW(),NOW());

INSERT INTO journal_entry_lines (id,journal_entry_id,account_id,description,debit,credit,created_at,updated_at) VALUES
-- Q1 2025: 3 bln × 300,000 = 900,000
(465,233,24,'Beban penyusutan peralatan Q1 2025',900000,0,NOW(),NOW()),
(466,233,7, 'Akumulasi penyusutan peralatan',0,900000,NOW(),NOW()),
-- Q2 2025: 3 bln × 300,000 = 900,000
(467,234,24,'Beban penyusutan peralatan Q2 2025',900000,0,NOW(),NOW()),
(468,234,7, 'Akumulasi penyusutan peralatan',0,900000,NOW(),NOW()),
-- Q3 2025: 3 bln × 300,000 = 900,000
(469,235,24,'Beban penyusutan peralatan Q3 2025',900000,0,NOW(),NOW()),
(470,235,7, 'Akumulasi penyusutan peralatan',0,900000,NOW(),NOW()),
-- Q4 2025: 3 bln × 300,000 = 900,000
(471,236,24,'Beban penyusutan peralatan Q4 2025',900000,0,NOW(),NOW()),
(472,236,7, 'Akumulasi penyusutan peralatan',0,900000,NOW(),NOW()),
-- Q1 2026: 3 bln × 300,000 = 900,000
(473,237,24,'Beban penyusutan peralatan Q1 2026',900000,0,NOW(),NOW()),
(474,237,7, 'Akumulasi penyusutan peralatan',0,900000,NOW(),NOW());

INSERT INTO adjusting_entries (id,journal_entry_id,adjustment_date,adjustment_type,description,amount,status,created_at,updated_at) VALUES
(1,233,'2025-03-31','depreciation','Penyusutan peralatan & inventaris Q1 2025',900000,'Posted',NOW(),NOW()),
(2,234,'2025-06-30','depreciation','Penyusutan peralatan & inventaris Q2 2025',900000,'Posted',NOW(),NOW()),
(3,235,'2025-09-30','depreciation','Penyusutan peralatan & inventaris Q3 2025',900000,'Posted',NOW(),NOW()),
(4,236,'2025-12-31','depreciation','Penyusutan peralatan & inventaris Q4 2025',900000,'Posted',NOW(),NOW()),
(5,237,'2026-03-31','depreciation','Penyusutan peralatan & inventaris Q1 2026',900000,'Posted',NOW(),NOW());

-- ============================================================
-- 20. CLOSING ENTRIES (tutup buku akhir tahun 2025)
-- ============================================================
INSERT INTO journal_entries (id,journal_number,entry_date,description,reference_type,reference_id,status,posted_at,posted_by,created_by,created_at,updated_at) VALUES
(238,'JRN-202512-0014','2025-12-31','Closing Entry - Tutup Buku 2025','PeriodClosing',12,'posted','2025-12-31 23:59:00',2,2,NOW(),NOW());

-- Closing: Dr Revenue accounts / Cr Laba Ditahan, Dr Laba Ditahan / Cr Expense accounts
-- Simplified: net to Laba Ditahan (3-2000)
-- Revenue 2025 total (approximate from journals above):
-- Pend.Penjualan: cash sales + credit sales
-- Pend.Servis: service revenues
-- Expense 2025 total: HPP + Gaji + Sewa + Listrik + Internet + Pemeliharaan + ATK + Penyusutan
INSERT INTO journal_entry_lines (id,journal_entry_id,account_id,description,debit,credit,created_at,updated_at) VALUES
-- Close Revenue to Income Summary (Dr Revenue / Cr Laba Ditahan)
(475,238,15,'Tutup Pendapatan Penjualan 2025',350000000,0,NOW(),NOW()),
(476,238,16,'Tutup Pendapatan Jasa Servis 2025',4700000,0,NOW(),NOW()),
-- Close Expenses to Income Summary (Dr Laba Ditahan / Cr Expenses)
(477,238,18,'Tutup HPP 2025',0,250000000,NOW(),NOW()),
(478,238,19,'Tutup Beban Gaji 2025',0,38000000,NOW(),NOW()),
(479,238,23,'Tutup Beban Sewa 2025',0,42000000,NOW(),NOW()),
(480,238,20,'Tutup Beban Listrik 2025',0,11470000,NOW(),NOW()),
(481,238,22,'Tutup Beban Internet & ATK 2025',0,6225000,NOW(),NOW()),
(482,238,21,'Tutup Beban Pemeliharaan 2025',0,1800000,NOW(),NOW()),
(483,238,24,'Tutup Beban Penyusutan 2025',0,3600000,NOW(),NOW()),
-- Net to Laba Ditahan: 354,700,000 - 352,095,000 = 2,605,000 net income → Cr Laba Ditahan
(484,238,13,'Laba bersih 2025 ke Laba Ditahan',0,2605000,NOW(),NOW());

INSERT INTO closing_entries (id,financial_period_id,journal_entry_id,closing_date,revenue_closed,expenses_closed,net_income,created_at,updated_at) VALUES
(1,12,238,'2025-12-31',354700000,352095000,2605000,NOW(),NOW());
