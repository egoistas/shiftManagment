<?php
// auto einai test file na blepo ta hash ton kodikon ton users

// 2oGr165 --> 165mpep2oGr$
// commander165 --> 165mpepCommander$
// eas165 --> 165mpepEas-askChristides!
// aydm165 --> 165mpepAydmFroyra$
// baydm165 --> 165mpepBaydmKanadezaSummer!
// apil165 --> 165mpepPiliStratopedou!
$password = "165mpepPiliStratopedou!";
$options = ['cost' => 10];
$hash = password_hash($password, PASSWORD_DEFAULT, $options);

echo $hash;

?>