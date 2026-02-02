<?php
/**
 * CLI helper to test DB connection and show safe diagnostics.
 * Usage: php scripts/test_db.php
 */
require_once __DIR__ . '/../inc/db.php';

$debug = db_connect_debug();

echo "DB connection diagnostics:\n";
foreach ($debug as $k => $v) {
    if (is_null($v)) continue;
    if (is_bool($v)) $v = $v ? 'true' : 'false';
    if (is_array($v)) $v = json_encode($v);
    printf("%-18s: %s\n", $k, $v);
}

if (!empty($debug['ok'])) {
    echo "\nSuggestions: you appear connected. If you still see app errors, ensure the DB user has privileges on 'db_biyai'.\n";
} else {
    echo "\nTroubleshooting suggestions:\n";
    echo "- Verify DB credentials (DB_USER / DB_PASS) match the MySQL user and password.\n";
    echo "- Ensure the MySQL user exists for the specific host (localhost vs 127.0.0.1).\n";
    echo "- If authentication plugin is 'caching_sha2_password' and PHP can't authenticate, run (as root):\n";
    echo "    ALTER USER 'biyai_user'@'localhost' IDENTIFIED WITH mysql_native_password BY 'YourStrongPassword';\n";
    echo "- To create/grant the user (as root):\n";
    echo "    CREATE USER 'biyai_user'@'localhost' IDENTIFIED BY 'YourStrongPassword';\n";
    echo "    GRANT ALL PRIVILEGES ON db_biyai.* TO 'biyai_user'@'localhost';\n";
    echo "    FLUSH PRIVILEGES;\n";
}

exit(0);
