<?php
// Usage: php generate_password_hash.php [password]
$pw = $argv[1] ?? null;
if (!$pw) {
    echo "Usage: php generate_password_hash.php [password]\n";
    exit(1);
}
echo password_hash($pw, PASSWORD_DEFAULT) . "\n";
