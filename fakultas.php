<?php
declare(strict_types=1);
require_once __DIR__ . "/crud.php";
crudRun([
 "perm"=>"fakultas","table"=>"fakultas","pk"=>"id_fakultas","title"=>"Fakultas","desc"=>"Kelola data fakultas.",
 "uri"=>["http://example.org/fakultas/","kode_fakultas"],
 "sql"=>"SELECT f.*, u.nama_universitas FROM fakultas f JOIN universitas u ON u.id_universitas=f.id_universitas","order"=>"f.nama_fakultas","search"=>["f.kode_fakultas","f.nama_fakultas","f.dekan"],
 "cols"=>["kode_fakultas"=>"Kode","nama_fakultas"=>"Fakultas","dekan"=>"Dekan","nama_universitas"=>"Universitas"],
 "fields"=>["id_universitas"=>["Universitas","select",1,"SELECT id_universitas,nama_universitas FROM universitas"],"kode_fakultas"=>["Kode","text",1],"nama_fakultas"=>["Nama Fakultas","text",1],"dekan"=>["Dekan","text"]],
]);
