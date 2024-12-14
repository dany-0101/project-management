<?php
echo "PHP Version: " . phpversion() . "\n";
echo "OpenSSL loaded: " . (extension_loaded('openssl') ? 'Yes' : 'No') . "\n";
echo "OpenSSL version: " . (defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : 'Not available') . "\n";

if (function_exists('openssl_get_cert_locations')) {
    echo "OpenSSL Certificate Locations:\n";
    print_r(openssl_get_cert_locations());
} else {
    echo "openssl_get_cert_locations function not available\n";
}

echo "Available SSL/TLS transports:\n";
print_r(stream_get_transports());

echo "Loaded extensions:\n";
print_r(get_loaded_extensions());