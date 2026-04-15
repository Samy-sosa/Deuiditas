<?php
// generar_hash_admin.php - Versión sin Laravel
$password = 'admin123';
$hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);

echo "==========================================\n";
echo "GENERADOR DE HASH - SUPER ADMIN\n";
echo "==========================================\n\n";
echo "Contraseña: " . $password . "\n";
echo "Hash generado: " . $hash . "\n";
echo "Verificación: " . (password_verify('admin123', $hash) ? "✅ CORRECTO" : "❌ ERROR") . "\n\n";
echo "==========================================\n";
echo "COPIA ESTE HASH EN PHPMYADMIN:\n";
echo $hash . "\n";
echo "==========================================\n";
?>