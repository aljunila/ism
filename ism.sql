-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 08, 2025 at 12:38 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ism`
--

-- --------------------------------------------------------

--
-- Table structure for table `akses`
--

CREATE TABLE `akses` (
  `id` int(11) NOT NULL,
  `id_karyawan` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `akses`
--

INSERT INTO `akses` (`id`, `id_karyawan`, `id_menu`) VALUES
(11, 4, 3),
(12, 4, 4),
(13, 2, 2),
(14, 2, 8),
(15, 2, 3),
(16, 2, 4),
(17, 2, 6),
(30, 3, 2),
(31, 3, 8),
(32, 3, 3),
(33, 3, 4),
(34, 3, 7),
(35, 3, 6),
(42, 12, 8),
(43, 12, 9);

-- --------------------------------------------------------

--
-- Table structure for table `form_aturan`
--

CREATE TABLE `form_aturan` (
  `id` int(11) NOT NULL,
  `uid` varchar(50) NOT NULL,
  `kode` varchar(30) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `isi` text NOT NULL,
  `enforced_by` int(11) NOT NULL,
  `publish` enum('Y','N') NOT NULL DEFAULT 'Y',
  `status` enum('A','D') NOT NULL DEFAULT 'A',
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `changed_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_aturan`
--

INSERT INTO `form_aturan` (`id`, `uid`, `kode`, `nama`, `isi`, `enforced_by`, `publish`, `status`, `created_by`, `created_date`, `changed_by`, `changed_date`) VALUES
(1, '30bb180d-7f19-4f7f-82a5-056c4f7f0c88', 'EL-02-01', 'Kebijakan Perusahaan Tentang Manajemen Keselamatan dan Perlindungan Lingkungan', '<h2 style=\"text-align: center;\"><strong>Kebijakan Perusahaan Tentang Manajemen Keselamatan</strong></h2>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p><span style=\"font-size: 14pt;\">Kebijakan perusahaan dirumuskan sebagai berikut <em><strong>\'Perusahaan berkomitmen dalam memberikan perhatian\'</strong></em></span></p>', 3, 'Y', 'A', 1, '2025-09-06 19:19:12', NULL, '2025-09-06 19:31:28');

-- --------------------------------------------------------

--
-- Table structure for table `jabatan`
--

CREATE TABLE `jabatan` (
  `id` int(11) NOT NULL,
  `uid` varchar(50) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `status` enum('A','D') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jabatan`
--

INSERT INTO `jabatan` (`id`, `uid`, `nama`, `status`) VALUES
(1, '5746089f-0d52-4227-9d45-243e6bfa0ac6', 'Nahkoda', 'A'),
(2, 'f1f7faa6-a1fc-46b4-8f02-66c236237d22', 'Mualim', 'D'),
(3, '894f1df7-28c8-444c-9004-8bfc6e8850c7', 'Masinis', 'A'),
(4, '3623fa7d-e385-47d5-bcc5-c966630b62d1', 'Direktur', 'A'),
(5, 'cd21b878-b46d-4eff-97f3-d422adc575b1', 'Mualim', 'A'),
(6, '761b3582-5b08-4d67-bc13-13aa76af104c', 'Manager', 'D'),
(7, '41cc341a-ee72-46da-9cd0-623703948579', 'Team Leader', 'D'),
(8, 'c9a56e86-becd-484d-b91a-e12b88b29132', 'asfsf', 'D'),
(9, 'b7693716-6877-47b8-a5a0-16acd1202b86', 'dgfdgdf', 'D'),
(10, 'b2562449-1f18-4831-bf38-27bd225136ab', 'CS', 'D'),
(11, 'f0d131fd-f7b7-46d8-ba36-9e5c83d7277a', 'aaaa', 'D');

-- --------------------------------------------------------

--
-- Table structure for table `kapal`
--

CREATE TABLE `kapal` (
  `id` int(11) NOT NULL,
  `uid` varchar(50) NOT NULL,
  `nama` varchar(30) NOT NULL,
  `pendaftaran` varchar(30) NOT NULL,
  `no_siup` varchar(30) NOT NULL,
  `no_akte` varchar(30) NOT NULL,
  `dikeluarkan_di` varchar(30) DEFAULT NULL,
  `selar` varchar(30) NOT NULL,
  `pemilik` int(11) NOT NULL,
  `call_sign` varchar(30) NOT NULL,
  `galangan` varchar(30) DEFAULT NULL,
  `konstruksi` varchar(30) DEFAULT NULL,
  `type` varchar(30) DEFAULT NULL,
  `loa` float DEFAULT NULL,
  `lbp` float DEFAULT NULL,
  `lebar` float DEFAULT NULL,
  `dalam` float DEFAULT NULL,
  `summer_draft` float DEFAULT NULL,
  `winter_draft` float DEFAULT NULL,
  `draft_air_tawar` float DEFAULT NULL,
  `tropical_draft` float DEFAULT NULL,
  `isi_kotor` float DEFAULT NULL,
  `bobot_mati` float DEFAULT NULL,
  `nt` float DEFAULT NULL,
  `merk_mesin_induk` varchar(75) DEFAULT NULL,
  `tahun_mesin_induk` int(11) DEFAULT NULL,
  `no_mesin_induk` varchar(30) DEFAULT NULL,
  `merk_mesin_bantu` varchar(75) DEFAULT NULL,
  `tahun_mesin_bantu` int(11) DEFAULT NULL,
  `no_mesin_bantu` varchar(30) DEFAULT NULL,
  `max_speed` float DEFAULT NULL,
  `normal_speed` float DEFAULT NULL,
  `min_speed` float DEFAULT NULL,
  `bahan_bakar` varchar(30) DEFAULT NULL,
  `jml_butuh` int(11) DEFAULT NULL,
  `berkas` varchar(100) DEFAULT NULL,
  `status` enum('A','D') NOT NULL,
  `created_by` varchar(30) NOT NULL,
  `created_date` date NOT NULL,
  `changed_by` varchar(30) DEFAULT NULL,
  `changed_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kapal`
--

INSERT INTO `kapal` (`id`, `uid`, `nama`, `pendaftaran`, `no_siup`, `no_akte`, `dikeluarkan_di`, `selar`, `pemilik`, `call_sign`, `galangan`, `konstruksi`, `type`, `loa`, `lbp`, `lebar`, `dalam`, `summer_draft`, `winter_draft`, `draft_air_tawar`, `tropical_draft`, `isi_kotor`, `bobot_mati`, `nt`, `merk_mesin_induk`, `tahun_mesin_induk`, `no_mesin_induk`, `merk_mesin_bantu`, `tahun_mesin_bantu`, `no_mesin_bantu`, `max_speed`, `normal_speed`, `min_speed`, `bahan_bakar`, `jml_butuh`, `berkas`, `status`, `created_by`, `created_date`, `changed_by`, `changed_date`) VALUES
(1, 'fca14e14-f3ae-4138-a9c8-72c21987ff79', 'ALS ELVINA', '2017 Pst No. 9468/L', '41/1/SIUPAL/PMDN/2017', '9468', 'Jakarta', 'GT. 6913 No. 707/Ab', 1, 'YBWT2', 'China/2016', 'Baja', 'Ro-Ro Ferry', 106.25, 100.7, 20.4, 6.5, 4.18, NULL, 4.87, NULL, 6913, 13826, 2074, '2 MESIN DIESEL NINGBO GB300ZC31B,  4 TAK KERJA TUNGGAL 2 X 3001 HP', 2016, '711 / 712', '3 WEICHAI , WP12CD317E200, 3 X 391  HP', 2016, NULL, 15.5, 10.3, 10, 'HSD/SOLAR', 5, NULL, 'A', '1', '2025-08-20', NULL, '2025-08-22 20:23:37'),
(2, '050bee7c-f1d7-4b96-8db7-bbc3cff6c69f', 'ALS ELISA', '2017 Pst No. 9382/L', '41/1/SIUPAL/PMDN/2017', '9382', 'Jakarta', 'GT. 6913 No. 705/Ab', 1, 'YBSC2', 'CHINA/2016', 'BAJA', 'Ro-Ro Ferry', 106.25, 100.7, 20.4, 6.5, 4.18, NULL, 4.27, NULL, 6913, 13826, 2074, '2 BUAH MESIN DIESEL NINGBO,  GB300ZC31B, 4 TAK KERJA TUNGGAL,  2 X 3001 HP', 2015, '708 (PS), 709 (SB)', '3 BUAH WEICHAI, WP12CD317E2003  X 391 HP', 2016, NULL, 15.5, 10.3, 10, 'HSD/SOLAR', 5, NULL, 'A', '1', '2025-08-31', NULL, '2025-09-03 20:53:58');

-- --------------------------------------------------------

--
-- Table structure for table `karyawan`
--

CREATE TABLE `karyawan` (
  `id` int(11) NOT NULL,
  `uid` varchar(50) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `nik` bigint(20) NOT NULL,
  `id_jabatan` int(11) NOT NULL,
  `tanda_tangan` varchar(50) DEFAULT NULL,
  `foto` varchar(50) DEFAULT NULL,
  `status` enum('A','D') NOT NULL DEFAULT 'A',
  `resign` enum('Y','N') NOT NULL DEFAULT 'N',
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `changed_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karyawan`
--

INSERT INTO `karyawan` (`id`, `uid`, `nama`, `nik`, `id_jabatan`, `tanda_tangan`, `foto`, `status`, `resign`, `created_by`, `created_date`, `changed_by`, `changed_date`) VALUES
(1, '09a95305-64a3-4a68-838d-fb57f5f35740', 'Citra Aljunila', 3603162706790004, 1, NULL, NULL, 'D', 'N', 1, '2025-08-24 14:47:01', NULL, '2025-08-28 20:34:29'),
(2, '1befb145-1d9a-489b-ac05-6aacb1d43cb3', 'Wantek Heru W.', 3603162706790004, 1, NULL, NULL, 'D', 'N', 1, '2025-08-24 14:49:00', NULL, '2025-08-26 21:25:54'),
(3, '6c662243-6258-44ff-9310-95dd6938b390', 'Wantek Heru W.', 360316270679000456, 1, '1756313781_ttd_wantek.png', NULL, 'A', 'N', 1, '2025-08-24 14:50:18', 1, '2025-09-01 16:15:21'),
(4, '4229dd21-f9ce-4cb2-bada-c717252bc692', 'NURFATHONI. F', 3509272301880001, 5, '1757008460_ttd_nurfathoni.png', NULL, 'A', 'N', 1, '2025-08-25 21:22:32', NULL, '2025-09-04 17:54:20'),
(5, '495d598b-4548-429e-b4af-368ce546f448', 'AHMAD FAIZAL', 3525171008990001, 5, NULL, NULL, 'A', 'Y', 1, '2025-08-26 20:30:34', 1, '2025-09-01 16:15:51'),
(11, '8561655f-25a8-413a-bf92-1df5cb1865aa', 'Mohamad Saiful', 3516081706730003, 5, '1756309073_ttd_mohsaiful.png', NULL, 'A', 'Y', 1, '2025-08-27 15:37:53', 1, '2025-09-01 15:59:15'),
(12, 'b3472a93-8336-4b88-a676-e9a8fecfb7ce', 'GUNTUR CAHYO. S', 3519060905910002, 3, NULL, NULL, 'A', 'N', 1, '2025-08-31 14:41:42', NULL, '2025-08-31 14:41:42');

-- --------------------------------------------------------

--
-- Table structure for table `kode_form`
--

CREATE TABLE `kode_form` (
  `id` int(11) NOT NULL,
  `kode` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `ket` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kode_form`
--

INSERT INTO `kode_form` (`id`, `kode`, `nama`, `ket`) VALUES
(1, 'el0101', 'Daftar Buku Refrensi Sistem Manajemen Keselamatan di Kantor', 'EL-01-01'),
(2, 'el0102', 'Daftar Buku Refrensi Sistem Manajemen Keselamatan di Kapal', 'EL-01-02'),
(3, 'el0103', 'Daftar Gambar Kapal', 'EL-01-03'),
(4, 'el0104', 'Daftar Peta', 'EL-01-04'),
(5, 'el0201', 'Kebijakan Perusahaan Tentang Manajemen Keselamatan dan Perlindungan Lingkungan', 'EL-02-01'),
(6, 'el0202', 'Kebijakan Perusahaan Tentang Pelarangan Penggunaan Obat Terlarang', 'EL-02-02');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `kode` varchar(30) NOT NULL,
  `link` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `id_parent` int(11) NOT NULL DEFAULT 0,
  `no` int(11) NOT NULL,
  `status` enum('A','D') NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `nama`, `kode`, `link`, `icon`, `id_parent`, `no`, `status`) VALUES
(1, 'Companies', 'perusahaan', '/perusahaan', '<i data-feather=\'file-text\'></i>', 0, 1, 'A'),
(2, 'Ferries', 'kapal', '/kapal', '<i data-feather=\'anchor\'></i>', 0, 2, 'A'),
(3, 'Elemen 1', 'element1', '/element1', '<i data-feather=\'folder-minus\'></i>', 10, 2, 'A'),
(4, 'EL-01-01', 'el0101', '/el0101', NULL, 3, 1, 'A'),
(5, 'Position', 'jabatan', '/jabatan', '<i data-feather=\'award\'></i>', 7, 1, 'A'),
(6, 'Access Account ', 'akses', 'akses', '<i data-feather=\'key\'></i>', 7, 2, 'A'),
(7, 'Master', 'master', '/master', '<i data-feather=\'server\'></i>', 0, 10, 'A'),
(8, 'Crewing Management', 'karyawan', '/karyawan', '<i data-feather=\'users\'></i>', 0, 3, 'A'),
(9, 'Procedures', 'prosedur', '/prosedur', '<i data-feather=\'book\'></i>', 10, 1, 'A'),
(10, 'ISM Code', 'ism', 'ism', '<i data-feather=\'airplay\'></i>', 0, 4, 'A'),
(11, 'Purchasing&Logistics', 'purchas', 'purchas', '<i data-feather=\'tag\'></i>', 0, 5, 'A'),
(12, 'Production', 'production', 'production', '<i data-feather=\'package\'></i>', 0, 6, 'A'),
(13, 'EL-01-02', 'el0102', '/el0102', NULL, 3, 2, 'A'),
(14, 'EL-01-03', 'el0103', '/el0103', NULL, 3, 3, 'A'),
(15, 'EL-01-04', 'el0104', '/el0104', NULL, 3, 4, 'A'),
(16, 'Elemen 2', 'elemen2', '/elemen2', '<i data-feather=\'bookmark\'></i>', 10, 3, 'A');

-- --------------------------------------------------------

--
-- Table structure for table `perusahaan`
--

CREATE TABLE `perusahaan` (
  `id` int(11) NOT NULL,
  `uid` varchar(50) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `email` varchar(50) NOT NULL,
  `telp` varchar(20) NOT NULL,
  `direktur` int(11) DEFAULT NULL,
  `logo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perusahaan`
--

INSERT INTO `perusahaan` (`id`, `uid`, `nama`, `alamat`, `email`, `telp`, `direktur`, `logo`) VALUES
(1, '615421db-fd39-4d54-9e96-9fa6a53bf278', 'PT. AMAN LINTAS SAMUDRA', 'Kantor Pusat 	: Jl. Raya Delta III No. 20 Waru – Sidoarjo, Jawa Timur<br>\r\nKantor Cabang 	: Link. Sukajaya RT/03, RW/06, Kel. Mekarsari, Kec. Pulomerak, Kota Cilegon, Prov. Banten-42438', 'amanlintassamudra@gmail.com', '( 031 ) 855 7079', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `previllage`
--

CREATE TABLE `previllage` (
  `id` int(11) NOT NULL,
  `nama` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `previllage`
--

INSERT INTO `previllage` (`id`, `nama`) VALUES
(1, 'SuperAdmin'),
(2, 'Admin Perusahaan'),
(3, 'Admin Kapal'),
(4, 'Karyawan');

-- --------------------------------------------------------

--
-- Table structure for table `prosedur`
--

CREATE TABLE `prosedur` (
  `id` int(11) NOT NULL,
  `uid` varchar(50) NOT NULL,
  `kode` varchar(25) NOT NULL,
  `judul` varchar(100) NOT NULL,
  `no_dokumen` varchar(30) DEFAULT NULL,
  `edisi` varchar(20) DEFAULT NULL,
  `tgl_terbit` date NOT NULL,
  `status_manual` varchar(50) DEFAULT NULL,
  `cover` text DEFAULT NULL,
  `isi` text DEFAULT NULL,
  `prepered_by` int(11) NOT NULL,
  `enforced_by` int(11) NOT NULL,
  `status` enum('A','D') NOT NULL DEFAULT 'A',
  `file` text DEFAULT NULL,
  `created_by` varchar(30) NOT NULL,
  `created_date` datetime NOT NULL,
  `changed_by` varchar(30) DEFAULT NULL,
  `changed_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prosedur`
--

INSERT INTO `prosedur` (`id`, `uid`, `kode`, `judul`, `no_dokumen`, `edisi`, `tgl_terbit`, `status_manual`, `cover`, `isi`, `prepered_by`, `enforced_by`, `status`, `file`, `created_by`, `created_date`, `changed_by`, `changed_date`) VALUES
(1, '8f088ea8-009f-47b1-8f7a-9ac8e0a5aae0', 'efefer', 'dsfwe', 'grege', 'grdg', '2025-09-04', 'wefewt', '<p>wqrew</p>', '<p>qwrew</p>', 4, 3, 'D', NULL, '1', '2025-09-03 22:03:20', NULL, '2025-09-03 22:07:23'),
(2, '4ca6cf87-3d9b-43c9-84fc-1f2a02608722', 'Elemen 1', 'SISTEM MANAJEMEN KESELAMATAN', '1', '1', '2017-01-01', 'Controlled', '<h2 style=\"text-align: center;\"><img src=\"http://127.0.0.1:8000/storage/uploads/1757005969_logo-als.jpg\" alt=\"\" width=\"175\" height=\"108\" /></h2>\r\n<h2 style=\"text-align: center;\">SISTEM MANAJEMEN KESELAMATAN</h2>\r\n<h2 style=\"text-align: center;\">PT. AMAN LINTAS SAMUDRA</h2>\r\n<h2 style=\"text-align: center;\"><span style=\"color: #3598db;\">ELEMEN 1 : U M U M</span></h2>\r\n<p><strong>A. DEFINISI</strong></p>\r\n<p><strong>&nbsp; &nbsp;1)&nbsp; ISM Code</strong></p>\r\n<p><strong>&nbsp; &nbsp;2)&nbsp; Perusahaan</strong></p>\r\n<p><strong>&nbsp; &nbsp;3)&nbsp; Pemerintah</strong></p>\r\n<p><strong>&nbsp; &nbsp;4)&nbsp; Sistem Manajemen Keselamatan</strong></p>\r\n<p><strong>&nbsp; &nbsp;5)&nbsp; <em>Document of Compliance</em></strong></p>\r\n<p><strong>&nbsp; &nbsp;6)&nbsp; <em>Safety Management Certificate</em></strong></p>\r\n<p><strong>&nbsp; &nbsp;7)&nbsp; Tujuan <em>(objective evidence)</em></strong></p>\r\n<p><strong>&nbsp; &nbsp;8)&nbsp; Observasi</strong></p>\r\n<p><strong>&nbsp; &nbsp;9)&nbsp; Ketidak sesuaian <em>(Non Conformity)</em></strong></p>\r\n<p><strong>&nbsp; 10) Ketidak sesuaian Mayor <em>(Major Non Conformity)</em></strong></p>\r\n<p><strong>&nbsp; 11) Istilah dan singkatan</strong></p>\r\n<p><strong>B. SASARAN</strong></p>\r\n<p><strong>&nbsp; &nbsp;1)&nbsp; Sasaran ISM Code</strong></p>\r\n<p><strong>&nbsp; &nbsp;2)&nbsp; Sasaran Manajemen Keselamatan Perusahaan</strong></p>\r\n<p><strong>&nbsp; &nbsp;3)&nbsp; Referensi Sistem Manajemen Keselamatan&nbsp;</strong></p>\r\n<p><strong>C. PENERAPAN</strong></p>\r\n<p><strong>D. PERSYARATAN SISTEM MANAJEMEN KESELAMATAN</strong></p>\r\n<p><strong>E. DAFTAR KAPAL YANG DIOPERASIKAN PT. AMAN LINTAS SAMUDRA</strong></p>', '<p>ISM Code</p>', 3, 4, 'A', NULL, '1', '2025-09-03 22:14:28', NULL, '2025-09-05 20:05:31'),
(8, '460924ff-f719-47c4-ab4b-678d05950c9e', 'Elemen 2', 'KEBIJAKAN KESELAMATAN DAN PERLINDUNGAN LINGKUNGAN', '1', '1', '2017-01-01', 'Controlled', NULL, NULL, 4, 3, 'A', '1757101648_5._ELEMEN_2_KEBIJAKAN_KESELAMATAN_&_PERLINDUNGAN_LINGKUNGAN.doc', '1', '2025-09-05 19:47:28', NULL, '2025-09-05 19:47:28');

-- --------------------------------------------------------

--
-- Table structure for table `refrensi_doc`
--

CREATE TABLE `refrensi_doc` (
  `id` int(11) NOT NULL,
  `uid` varchar(50) NOT NULL,
  `kode` varchar(30) NOT NULL,
  `nama_doc` varchar(100) NOT NULL,
  `edisi` varchar(100) NOT NULL,
  `id_pj` int(11) NOT NULL,
  `lokasi` text NOT NULL,
  `file` varchar(100) DEFAULT NULL,
  `status` enum('A','D') NOT NULL DEFAULT 'A',
  `created_by` int(11) NOT NULL,
  `created_date` datetime NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `changed_date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refrensi_doc`
--

INSERT INTO `refrensi_doc` (`id`, `uid`, `kode`, `nama_doc`, `edisi`, `id_pj`, `lokasi`, `file`, `status`, `created_by`, `created_date`, `changed_by`, `changed_date`) VALUES
(1, 'e6727ad2-55dd-4ed8-bafe-42e870a3fd24', 'el0101', 'Prosedur Kapal', '1', 12, 'ruang perpus', NULL, 'A', 1, '2025-09-05 22:05:37', 1, '2025-09-05 22:33:59'),
(2, 'e1667404-dadf-4891-b2b6-56bafb1930eb', 'el0101', 'Data Kapal', '1', 4, 'ruang nahkoda', NULL, 'A', 1, '2025-09-05 22:12:10', NULL, '2025-09-06 17:24:15');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('eV6bi5U6DljKUZ9uTa02zvhZXxg6waV0QD3sysRK', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YToxMDp7czo2OiJfdG9rZW4iO3M6NDA6ImkyOGpSUHpnc3g4anN4SWRjdVg1bHdSTEE2ZzRVeWF0bE9NejZGa3oiO3M6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NjoidXNlcmlkIjtpOjE7czo4OiJ1c2VybmFtZSI7czoxNDoiYWRtaW5AdGVzdC5jb20iO3M6NDoibmFtZSI7czo1OiJBZG1pbiI7czoxMDoicHJldmlsbGFnZSI7aToxO3M6MTE6ImlkX2thcnlhd2FuIjtpOjE7czozOiJwaWMiO3M6Njoic2Fkc2FkIjtzOjU6ImxvZ2luIjtiOjE7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Njk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hdHVyYW4vcGRmLzMwYmIxODBkLTdmMTktNGY3Zi04MmE1LTA1NmM0ZjdmMGM4OCI7fX0=', 1757187094);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` text DEFAULT NULL,
  `nama` varchar(50) NOT NULL,
  `pic` text DEFAULT NULL,
  `id_previllage` int(11) DEFAULT 4,
  `status` enum('A','D') NOT NULL DEFAULT 'A',
  `id_karyawan` int(11) NOT NULL,
  `id_perusahaan` int(11) NOT NULL,
  `id_kapal` int(11) NOT NULL,
  `created_by` varchar(35) DEFAULT NULL,
  `created_date` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `changed_by` varchar(35) DEFAULT NULL,
  `changed_date` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `nama`, `pic`, `id_previllage`, `status`, `id_karyawan`, `id_perusahaan`, `id_kapal`, `created_by`, `created_date`, `changed_by`, `changed_date`) VALUES
(1, 'admin@test.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Admin', 'sadsad', 1, 'A', 1, 0, 0, 'citra', '2025-06-18 23:31:11', 'dsads', '2025-06-18 16:30:54'),
(2, 'wantekheru@als.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Wantek Heru W.', NULL, NULL, 'A', 3, 1, 1, NULL, '2025-09-01 23:15:21', '1', NULL),
(3, 'nurfathoni@als.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'NURFATHONI. F', NULL, 3, 'A', 4, 1, 1, '1', '2025-08-27 23:13:17', '1', NULL),
(4, 'mohsaiful@als.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Mohamad Saiful', NULL, 4, 'A', 11, 1, 1, '1', '2025-08-27 15:37:53', NULL, NULL),
(5, 'gunturcahyo@als.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'GUNTUR CAHYO. S', NULL, 3, 'A', 12, 1, 2, '1', '2025-09-01 22:24:55', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akses`
--
ALTER TABLE `akses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_aturan`
--
ALTER TABLE `form_aturan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jabatan`
--
ALTER TABLE `jabatan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kapal`
--
ALTER TABLE `kapal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `karyawan`
--
ALTER TABLE `karyawan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kode_form`
--
ALTER TABLE `kode_form`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `perusahaan`
--
ALTER TABLE `perusahaan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `previllage`
--
ALTER TABLE `previllage`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prosedur`
--
ALTER TABLE `prosedur`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `refrensi_doc`
--
ALTER TABLE `refrensi_doc`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `akses`
--
ALTER TABLE `akses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `form_aturan`
--
ALTER TABLE `form_aturan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `jabatan`
--
ALTER TABLE `jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `kapal`
--
ALTER TABLE `kapal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `karyawan`
--
ALTER TABLE `karyawan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `kode_form`
--
ALTER TABLE `kode_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `perusahaan`
--
ALTER TABLE `perusahaan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `previllage`
--
ALTER TABLE `previllage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `prosedur`
--
ALTER TABLE `prosedur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `refrensi_doc`
--
ALTER TABLE `refrensi_doc`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
