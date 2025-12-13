<?php
require_once 'koneksi.php';

// Fungsi escape string
if (!function_exists('escape')) {
    function escape($value) {
        global $koneksi; // atau $GLOBALS['conn'] jika kamu pakai
        return mysqli_real_escape_string($koneksi, $value);
    }
}


// Fungsi insert generic
function insertData($table, $data) {
    $columns = implode(',', array_keys($data));
    $values = implode(',', array_map(fn($v) => "'" . escape($v) . "'", array_values($data)));
    $query = "INSERT INTO $table ($columns) VALUES ($values)";
    return query($query);
}

// Fungsi update generic
function updateData($table, $data, $where) {
    $set = [];
    foreach ($data as $k => $v) {
        $set[] = "$k='" . escape($v) . "'";
    }
    $query = "UPDATE $table SET " . implode(',', $set) . " WHERE $where";
    return query($query);
}

// Fungsi delete generic
function deleteData($table, $where) {
    $query = "DELETE FROM $table WHERE $where";
    return query($query);
}

// Fungsi select generic
function getData($table, $columns='*', $where='1=1', $order='') {
    $query = "SELECT $columns FROM $table WHERE $where";
    if ($order) $query .= " ORDER BY $order";
    $result = query($query);
    $rows = [];
    if ($result) {
        while ($row = fetch_array($result)) $rows[] = $row;
    }
    return $rows;
}
