<?php

return [
    'unique' => ':attribute sudah digunakan.',
    'confirmed' => ':attribute tidak cocok.',
    'required' => ':attribute wajib diisi.',
    'required_if' => ':attribute wajib diisi.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'mimes' => ':attribute harus berupa file dengan format: :values.',

    'in' => 'Pilihan :attribute tidak valid.',
    
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
        'material_name' => 'nama bahan',
        'unit'          => 'satuan',
        'category'      => 'kategori',
        'description'   => 'deskripsi',
        'supplier_name' => 'nama supplier',
        'contact' => 'kontak',
        'city' => 'kota',
        'supplier_id' => 'supplier',
        'items' => 'bahan',
        'formula_name' => 'nama formula',
        'product_id' => 'produk',
        'production_quantity' => 'jumlah produksi',
        'product_name' => 'nama produk',
        'source'     => 'sumber produk',
        'type'       => 'tipe produk',
        'harga'      => 'harga jual',
        'quantity'     => 'jumlah',
        'items.*.material_id' => 'bahan baku',
        'expiration_date' => 'tanggal kadaluarsa',
    'items.*.product_id'  => 'produk',
    'items.*.jumlah'      => 'jumlah',
    'items.*.harga'       => 'harga',
    'items.0.expiration_date' => 'tanggal kadaluarsa',
    'items.1.expiration_date' => 'tanggal kadaluarsa',
    'items.1.received_quantity' => 'jumlah diterima',
    'items.0.quantity' => 'jumlah'
    ],
];
