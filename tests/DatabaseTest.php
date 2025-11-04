<?php

test('database connection works', function () {
    require __DIR__ . '/../database/koneksi.php';

    expect($koneksi)->not->toBeNull()
        ->and($koneksi->connect_error)->toBeNull()
        ->and($koneksi->ping())->toBeTrue();
});
