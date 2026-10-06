<?php
declare(strict_types=1);
require_once __DIR__ . "/crud.php";
crudRun([
 "perm"=>"prodi","table"=>"program_studi","pk"=>"id_prodi","title"=>"Program Studi","desc"=>"Kelola program studi.",
 "uri"=>["http://example.org/prodi/","kode_prodi"],
 "sql"=>"SELECT p.*, f.nama_fakultas, IF(p.aktif,\"Aktif\",\"Nonaktif\") AS status FROM program_studi p JOIN fakultas f ON f.id_fakultas=p.id_fakultas","order"=>"p.nama_prodi","search"=>["p.kode_prodi","p.nama_prodi","p.kaprodi"],
 "cols"=>["kode_prodi"=>"Kode","nama_prodi"=>"Program Studi","jenjang"=>"Jenjang","akreditasi"=>"Akreditasi","nama_fakultas"=>"Fakultas","status"=>"Status"],
 "fields"=>["id_fakultas"=>["Fakultas","select",1,"SELECT id_fakultas,nama_fakultas FROM fakultas"],"kode_prodi"=>["Kode","text",1],"nama_prodi"=>["Nama Prodi","text",1],"jenjang"=>["Jenjang","select",1,["D3"=>"D3","D4"=>"D4","S1"=>"S1","S2"=>"S2","S3"=>"S3"],"S1"],"akreditasi"=>["Akreditasi","text"],"kaprodi"=>["Kaprodi","text"],"jumlah_mahasiswa"=>["Jumlah Mahasiswa","number",1,null,"0"],"aktif"=>["Aktif","check"]],
]);
