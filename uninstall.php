<?php
global $_W;
$sql = "
drop table if exists " . tablename('yz_menu') . " ;
drop table if exists " . tablename('yz_options') . " ;

";
pdo_query($sql);
