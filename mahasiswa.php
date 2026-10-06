<?php
declare(strict_types=1);
require_once __DIR__ . "/crud.php";
crudRun([
 "perm"=>"mahasiswa","table"=>"mahasiswa","pk"=>"id_mahasiswa","title"=>"Mahasiswa","desc"=>"Kelola data mahasiswa.",
 "uri"=>["http://example.org/mahasiswa/","nim"],
 "sql"=>"SELECT m.*, p.nama_prodi FROM mahasiswa m JOIN program_studi p ON p.id_prodi=m.id_prodi","order"=>"m.nim","search"=>["m.nim","m.nama_lengkap","p.nama_prodi"],
 "cols"=>["nim"=>"NIM","nama_lengkap"=>"Nama","nama_prodi"=>"Prodi","angkatan"=>"Angkatan","semester"=>"Smt","status_mahasiswa"=>"Status"],
 "fields"=>["nim"=>["NIM","text",1],"nama_lengkap"=>["Nama Lengkap","text",1],"id_prodi"=>["Program Studi","select",1,"SELECT id_prodi,nama_prodi FROM program_studi"],"jenis_kelamin"=>["Jenis Kelamin","select",0,["L"=>"Laki-laki","P"=>"Perempuan"]],"tempat_lahir"=>["Tempat Lahir","text"],"tanggal_lahir"=>["Tanggal Lahir","date"],"angkatan"=>["Angkatan","number",1,null,date("Y")],"semester"=>["Semester","number"],"status_mahasiswa"=>["Status","select",1,["Aktif"=>"Aktif","Cuti"=>"Cuti","Lulus"=>"Lulus","Nonaktif"=>"Nonaktif","Drop Out"=>"Drop Out"],"Aktif"],"email"=>["Email","email"]],
]);
