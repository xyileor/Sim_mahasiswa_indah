<?php
declare(strict_types=1);
require_once __DIR__ . "/crud.php";
crudRun([
 "perm"=>"universitas","table"=>"universitas","pk"=>"id_universitas","title"=>"Universitas","desc"=>"Kelola profil universitas.",
 "uri"=>["http://example.org/universitas/","kode_universitas"],
 "sql"=>"SELECT * FROM universitas","order"=>"nama_universitas","search"=>["kode_universitas","nama_universitas","kota"],
 "cols"=>["kode_universitas"=>"Kode","nama_universitas"=>"Nama","kota"=>"Kota","provinsi"=>"Provinsi","email"=>"Email"],
 "fields"=>["kode_universitas"=>["Kode","text",1],"nama_universitas"=>["Nama Universitas","text",1],"slogan"=>["Slogan","text"],"kota"=>["Kota","text"],"provinsi"=>["Provinsi","text"],"kode_pos"=>["Kode Pos","text"],"email"=>["Email","email"],"telepon"=>["Telepon","text"],"website"=>["Website","text"],"alamat"=>["Alamat","textarea"]],
]);
