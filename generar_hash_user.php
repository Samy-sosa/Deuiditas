<?php
// generar_hash_user.php
$password = '123456';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

echo "==========================================\n";
echo "GENERADOR DE HASH - USUARIOS TIENDA\n";
echo "==========================================\n\n";
echo "Contraseña: " . $password . "\n";
echo "Hash generado: " . $hash . "\n";
echo "Verificación: " . (password_verify('123456', $hash) ? "✅ CORRECTO" : "❌ ERROR") . "\n\n";
echo "==========================================\n";
echo "COPIA ESTE HASH EN PHPMYADMIN:\n";
echo $hash . "\n";
echo "==========================================\n";
?>