<?php
/**
 * File Konfigurasi Aplikasi Biyai Finance Tracker
 * Data disimpan dalam file JSON (tanpa database)
 */

define('DATA_FILE', __DIR__ . '/data/transaksi.json');
define('DATA_DIR', __DIR__ . '/data');

// Pastikan folder data ada
if (!is_dir(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}

// Pastikan file data ada
if (!file_exists(DATA_FILE)) {
    file_put_contents(DATA_FILE, json_encode([]));
}

/**
 * Fungsi untuk membaca semua transaksi
 */
function getTransaksi() {
    $json = file_get_contents(DATA_FILE);
    return json_decode($json, true) ?: [];
}

/**
 * Fungsi untuk menyimpan transaksi
 */
function saveTransaksi($data) {
    file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Fungsi untuk menambah transaksi baru
 */
function tambahTransaksi($transaksi) {
    $data = getTransaksi();
    $transaksi['id'] = uniqid();
    $transaksi['createdAt'] = date('Y-m-d H:i:s');
    array_unshift($data, $transaksi);
    saveTransaksi($data);
    return $transaksi['id'];
}

/**
 * Fungsi untuk mengupdate transaksi
 */
function updateTransaksi($id, $transaksi) {
    $data = getTransaksi();
    foreach ($data as $key => $item) {
        if ($item['id'] === $id) {
            $data[$key] = array_merge($item, $transaksi);
            $data[$key]['updatedAt'] = date('Y-m-d H:i:s');
            saveTransaksi($data);
            return true;
        }
    }
    return false;
}

/**
 * Fungsi untuk menghapus transaksi
 */
function hapusTransaksi($id) {
    $data = getTransaksi();
    foreach ($data as $key => $item) {
        if ($item['id'] === $id) {
            array_splice($data, $key, 1);
            saveTransaksi($data);
            return true;
        }
    }
    return false;
}

/**
 * Fungsi untuk mendapatkan transaksi berdasarkan ID
 */
function getTransaksiById($id) {
    $data = getTransaksi();
    foreach ($data as $item) {
        if ($item['id'] === $id) {
            return $item;
        }
    }
    return null;
}
?>