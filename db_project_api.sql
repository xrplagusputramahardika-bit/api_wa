-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 10 Sep 2026 pada 08.23
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_project_api`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_whatsapp`
--

CREATE TABLE `log_whatsapp` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `no_tujuan` varchar(20) NOT NULL,
  `pesan` text NOT NULL,
  `status` enum('success','failed') NOT NULL,
  `response` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `log_whatsapp`
--

INSERT INTO `log_whatsapp` (`id`, `user_id`, `no_tujuan`, `pesan`, `status`, `response`, `created_at`) VALUES
(1, NULL, '62851347776766', 'Akun dihapus\n\nHalo, Nardi!\nAkun Fonnte API Anda telah dihapus sesuai permintaan.', 'success', '{\"detail\":\"success! message in queue\",\"id\":[178352717],\"process\":\"pending\",\"quota\":{\"082132980932\":{\"details\":\"deduced from total quota\",\"quota\":992,\"remaining\":991,\"used\":1}},\"requestid\":724393995,\"status\":true,\"target\":[\"62851347776766\"],\"http_code\":200}', '2026-09-10 01:46:49'),
(2, NULL, '6288976575385', 'Login berhasil\n\nHalo, Nartoy!\nAkun Anda baru saja digunakan untuk login ke dashboard Fonnte API.', 'success', '{\"detail\":\"success! message in queue\",\"id\":[178356368],\"process\":\"pending\",\"quota\":{\"082132980932\":{\"details\":\"deduced from total quota\",\"quota\":990,\"remaining\":989,\"used\":1}},\"requestid\":724416407,\"status\":true,\"target\":[\"6288976575385\"],\"http_code\":200}', '2026-09-10 01:57:52'),
(3, NULL, '6288976575385', 'Profil diperbarui\n\nHalo, Narto!\nData profil akun Fonnte API Anda berhasil diperbarui.', 'success', '{\"detail\":\"success! message in queue\",\"id\":[178356597],\"process\":\"pending\",\"quota\":{\"082132980932\":{\"details\":\"deduced from total quota\",\"quota\":989,\"remaining\":988,\"used\":1}},\"requestid\":724418405,\"status\":true,\"target\":[\"6288976575385\"],\"http_code\":200}', '2026-09-10 01:58:53'),
(4, NULL, '6288976575385', 'Akun dihapus\n\nHalo, Narto!\nAkun Fonnte API Anda telah dihapus sesuai permintaan.', 'success', '{\"detail\":\"success! message in queue\",\"id\":[178356889],\"process\":\"pending\",\"quota\":{\"082132980932\":{\"details\":\"deduced from total quota\",\"quota\":988,\"remaining\":987,\"used\":1}},\"requestid\":724419945,\"status\":true,\"target\":[\"6288976575385\"],\"http_code\":200}', '2026-09-10 01:59:48'),
(5, NULL, '6285731637000', 'Login berhasil\n\nHalo, Agus Putra Mahardika!\nAkun Anda baru saja digunakan untuk login ke dashboard Fonnte API.', 'success', '{\"detail\":\"success! message in queue\",\"id\":[178418569],\"process\":\"pending\",\"quota\":{\"082132980932\":{\"details\":\"deduced from total quota\",\"quota\":986,\"remaining\":985,\"used\":1}},\"requestid\":724881732,\"status\":true,\"target\":[\"6285731637000\"],\"http_code\":200}', '2026-09-10 05:55:46'),
(6, NULL, '6285731637000', 'Profil diperbarui\n\nHalo, Agus Putra Mahardika!\nData profil akun Fonnte API Anda berhasil diperbarui.', 'success', '{\"detail\":\"success! message in queue\",\"id\":[178418634],\"process\":\"pending\",\"quota\":{\"082132980932\":{\"details\":\"deduced from total quota\",\"quota\":985,\"remaining\":984,\"used\":1}},\"requestid\":724882267,\"status\":true,\"target\":[\"6285731637000\"],\"http_code\":200}', '2026-09-10 05:56:08'),
(7, NULL, '6285731637000', 'Akun dihapus\n\nHalo, Agus Putra Mahardika!\nAkun Fonnte API Anda telah dihapus sesuai permintaan.', 'success', '{\"detail\":\"success! message in queue\",\"id\":[178418681],\"process\":\"pending\",\"quota\":{\"082132980932\":{\"details\":\"deduced from total quota\",\"quota\":984,\"remaining\":983,\"used\":1}},\"requestid\":724882418,\"status\":true,\"target\":[\"6285731637000\"],\"http_code\":200}', '2026-09-10 05:56:21'),
(8, 18, '6288976575385', 'Login berhasil\n\nHalo, Nartoy!\nAkun Anda baru saja digunakan untuk login ke dashboard Fonnte API.', 'success', '{\"detail\":\"success! message in queue\",\"id\":[178421740],\"process\":\"pending\",\"quota\":{\"082132980932\":{\"details\":\"deduced from total quota\",\"quota\":982,\"remaining\":981,\"used\":1}},\"requestid\":724907683,\"status\":true,\"target\":[\"6288976575385\"],\"http_code\":200}', '2026-09-10 06:07:41'),
(9, 19, '6285134777683', 'Login berhasil\n\nHalo, Narto!\nAkun Anda baru saja digunakan untuk login ke dashboard Fonnte API.', 'success', '{\"detail\":\"success! message in queue\",\"id\":[178424552],\"process\":\"pending\",\"quota\":{\"082132980932\":{\"details\":\"deduced from total quota\",\"quota\":980,\"remaining\":979,\"used\":1}},\"requestid\":724930976,\"status\":true,\"target\":[\"6285134777683\"],\"http_code\":200}', '2026-09-10 06:19:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','siswa') NOT NULL DEFAULT 'siswa',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `no_hp`, `password`, `role`, `created_at`) VALUES
(18, 'Nartoy', 'xrplagusputramahardika@gmail.com', '6288976575385', '$2y$10$VG3cCrN4tjUO4VBJptCuJuBI2X5ykpBK6tA/Yn.9PVbYjKd0qHr6u', 'admin', '2026-09-10 06:07:01'),
(19, 'Narto', 'narto@gmail.com', '6285134777683', '$2y$10$kwPqxsgDPqQbnGC5BYC22unQmRnqdmx3rUhlb.R3.NxuiFlaSayb2', 'siswa', '2026-09-10 06:15:57');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `log_whatsapp`
--
ALTER TABLE `log_whatsapp`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `no_hp` (`no_hp`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `log_whatsapp`
--
ALTER TABLE `log_whatsapp`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `log_whatsapp`
--
ALTER TABLE `log_whatsapp`
  ADD CONSTRAINT `log_whatsapp_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
