<?php
declare(strict_types=1);
require_once __DIR__ . "/crud.php";
crudRun([
 "perm"=>"pengguna","table"=>"pengguna","pk"=>"id_pengguna","title"=>"Pengguna","desc"=>"Kelola akun dan role pengguna.","protect_self"=>true,
 "sql"=>"SELECT u.*, r.nama_role FROM pengguna u JOIN roles r ON r.id_role=u.id_role","order"=>"u.username","search"=>["u.username","u.nama_lengkap","r.nama_role"],
 "cols"=>["username"=>"Username","nama_lengkap"=>"Nama","nama_role"=>"Role","email"=>"Email","last_login"=>"Login Terakhir"],
 "fields"=>["username"=>["Username","text",1],"nama_lengkap"=>["Nama Lengkap","text",1],"email"=>["Email","email"],"id_role"=>["Role","select",1,"SELECT id_role,nama_role FROM roles"],"password_hash"=>["Password","password",1],"aktif"=>["Aktif","check"]],
]);
