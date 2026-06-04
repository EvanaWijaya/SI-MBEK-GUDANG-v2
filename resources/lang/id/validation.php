<?php

return [
    'unique' => ':attribute sudah digunakan.',
    'confirmed' => ':attribute tidak cocok.',
    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'mimes' => ':attribute harus berupa file dengan format: :values.',
    
    'min' => [
        'string' => ':attribute harus terdiri dari minimal :min karakter.',
        'numeric' => ':attribute minimal bernilai :min.',
        'array' => ':attribute harus memiliki minimal :min item.',
    ],
    'max' => [
        'string' => ':attribute tidak boleh lebih dari :max karakter.',
        'numeric' => ':attribute tidak boleh lebih dari :max.',
        'array' => ':attribute tidak boleh memiliki lebih dari :max item.',
    ],
    'current_password' => 'Kata sandi saat ini tidak sesuai.',
    'lowercase' => ':attribute harus menggunakan huruf kecil.',

    // Alias nama atribut agar pesan error lebih manusiawi
    'attributes' => [
        'email' => 'email',
        'password' => 'kata sandi',
        'password_confirmation' => 'konfirmasi kata sandi',
        'name' => 'nama',
        'current_password' => 'kata sandi saat ini',
        'image' => 'gambar',
        'faksin_status' => 'status vaksin',
        'formula_id' => 'formula',
        'kode'       => 'kode produk',
    'nama'       => 'nama produk',
    'source'     => 'sumber produk',
    'type'       => 'tipe produk',
    'harga'      => 'harga jual',
        'items.*.material_id' => 'bahan baku',
    'items.*.product_id'  => 'produk',
    'items.*.jumlah'      => 'jumlah',
    'items.*.harga'       => 'harga',
    ],
];
