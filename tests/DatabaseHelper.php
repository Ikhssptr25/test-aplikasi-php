<?php
// file: tests/DatabaseHelper.php

class DatabaseHelper
{
    private $koneksi;

    public function __construct()
    {
        // 1. Sertakan file koneksi Anda
        // Kita pakai require_once agar tidak error jika di-include berkali-kali
        require_once __DIR__ . '/../database/koneksi.php';

        // 2. Asumsikan file koneksi.php Anda membuat variabel global $koneksi
        // (Ini adalah pola umum di PHP native)
        // Jika nama variabelnya beda (misal: $conn, $db), silakan ganti.
        global $koneksi; 
        
        if (!isset($koneksi)) {
            throw new \Exception("Variabel koneksi (\$koneksi) tidak ditemukan. Periksa file koneksi.php Anda.");
        }
        
        $this->koneksi = $koneksi;
    }

    /**
     * Memastikan sebuah data ada di tabel.
     */
    public function assertDatabaseHas(string $table, array $data)
    {
        // Bangun query WHERE (misal: "nama = '... ' AND posisi = '...'")
        $wheres = [];
        foreach ($data as $key => $value) {
            // Lindungi dari SQL Injection
            $escapedValue = $this->koneksi->real_escape_string($value);
            $wheres[] = "`$key` = '$escapedValue'";
        }
        $whereClause = implode(' AND ', $wheres);

        // Eksekusi query
        $sql = "SELECT COUNT(*) as total FROM `$table` WHERE $whereClause";
        $result = $this->koneksi->query($sql);
        $row = $result->fetch_assoc();

        // Gunakan fungsi 'expect' bawaan Pest untuk asersi
        expect($row['total'])->toBeGreaterThan(0, "Gagal menemukan data di tabel '$table' dengan kriteria: " . json_encode($data));
    }

    /**
     * Menghapus data dari tabel untuk cleanup.
     */
    public function cleanupDatabase(string $table, array $data)
    {
        $wheres = [];
        foreach ($data as $key => $value) {
            $escapedValue = $this->koneksi->real_escape_string($value);
            $wheres[] = "`$key` = '$escapedValue'";
        }
        $whereClause = implode(' AND ', $wheres);

        $sql = "DELETE FROM `$table` WHERE $whereClause";
        $this->koneksi->query($sql);
    }

    public function seedDatabase(string $table, array $data): int
    {
        $keys = implode('`, `', array_keys($data));
        $values = [];
        foreach ($data as $value) {
            $escapedValue = $this->koneksi->real_escape_string($value);
            $values[] = "'$escapedValue'";
        }
        $valueString = implode(', ', $values);

        $sql = "INSERT INTO `$table` (`$keys`) VALUES ($valueString)";
        
        if ($this->koneksi->query($sql) === TRUE) {
            // Mengembalikan ID dari data yang baru dimasukkan
            return $this->koneksi->insert_id; 
        } else {
            throw new \Exception("Gagal seeding database: " . $this->koneksi->error);
        }
    }

    /**
     * Memastikan sebuah data TIDAK ADA di tabel.
     */
    public function assertDatabaseMissing(string $table, array $data)
    {
        $wheres = [];
        foreach ($data as $key => $value) {
            $escapedValue = $this->koneksi->real_escape_string($value);
            $wheres[] = "`$key` = '$escapedValue'";
        }
        $whereClause = implode(' AND ', $wheres);

        $sql = "SELECT COUNT(*) as total FROM `$table` WHERE $whereClause";
        $result = $this->koneksi->query($sql);
        $row = $result->fetch_assoc();

        // Asersi kebalikan dari assertDatabaseHas
        expect($row['total'])->toBe(0, "Data DITEMUKAN di tabel '$table' padahal seharusnya tidak: " . json_encode($data));
    }
}