<?php
// Clear Laravel caches
echo "Clearing Laravel caches...\n";

// Clear route cache
if (file_exists('../bootstrap/cache/routes-v7.php')) {
    unlink('../bootstrap/cache/routes-v7.php');
    echo "Route cache cleared.\n";
}

// Clear config cache
if (file_exists('../bootstrap/cache/config.php')) {
    unlink('../bootstrap/cache/config.php');
    echo "Config cache cleared.\n";
}

// Clear application cache
if (file_exists('../storage/framework/cache/data')) {
    $files = glob('../storage/framework/cache/data/*');
    foreach($files as $file) {
        if(is_file($file)) {
            unlink($file);
        }
    }
    echo "Application cache cleared.\n";
}

// Clear view cache
if (file_exists('../storage/framework/views')) {
    $files = glob('../storage/framework/views/*.php');
    foreach($files as $file) {
        if(is_file($file)) {
            unlink($file);
        }
    }
    echo "View cache cleared.\n";
}

echo "All caches cleared successfully!\n";
echo "You can now delete this file for security.\n";
?>
