<?php

/*
 * MSK Devs bridge. Goes in the main site's web folder (public_html/msk-bridge/index.php).
 * The main site's .htaccess sends every school domain (aischoolofsciences.com ...) here,
 * and this file runs the MSK Devs software from its own folder, wherever that folder is.
 *
 * Normally the software folder is found by itself. If not, write its full path here, e.g.
 * '/home/u123456789/domains/products.assanzameen.com/public_html'
 */
const MSK_APP = '';

$candidates = array_filter([
    MSK_APP,
    dirname(__DIR__, 3).'/products.assanzameen.com/public_html',
    dirname(__DIR__, 2).'/products',
    dirname(__DIR__, 2).'/products.assanzameen.com',
    dirname(__DIR__).'/products',
    dirname(__DIR__, 4).'/public_html/products',
    dirname(__DIR__, 3).'/public_html/products',
]);

$app = null;
foreach ($candidates as $dir) {
    if (is_file($dir.'/index.php') && is_file($dir.'/artisan') && is_file($dir.'/vendor/autoload.php')) {
        $app = rtrim($dir, '/');
        break;
    }
}

if (! $app) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "MSK Devs bridge: the software folder was not found.\nOpen msk-bridge/index.php and write the full path of the products.assanzameen.com folder in MSK_APP.";
    exit;
}

// CSS, JS, pictures and fonts in the software's public/ folder are sent straight from there.
// They use /msk-public/ on school domains: the main site has its own public/ folder, which would answer /public/ itself.
$path = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
if (str_starts_with($path, '/msk-public/')) {
    $path = '/public/'.substr($path, strlen('/msk-public/'));
}
if (str_starts_with($path, '/public/')) {
    $root = realpath($app.'/public');
    $file = realpath($app.$path);
    $types = [
        'css' => 'text/css', 'js' => 'text/javascript', 'mjs' => 'text/javascript', 'map' => 'application/json', 'json' => 'application/json',
        'png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'gif' => 'image/gif', 'webp' => 'image/webp', 'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon', 'woff' => 'font/woff', 'woff2' => 'font/woff2', 'ttf' => 'font/ttf', 'txt' => 'text/plain',
    ];
    $ext = strtolower(pathinfo((string) $file, PATHINFO_EXTENSION));

    if ($root && $file && str_starts_with($file, $root.DIRECTORY_SEPARATOR) && is_file($file) && isset($types[$ext])) {
        $mtime = filemtime($file);
        header('Content-Type: '.$types[$ext]);
        header('Content-Length: '.filesize($file));
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', $mtime).' GMT');
        header('Cache-Control: '.(str_starts_with($path, '/public/build/') ? 'public, max-age=31536000, immutable' : 'public, max-age=86400'));
        if (($_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '') === gmdate('D, d M Y H:i:s', $mtime).' GMT') {
            http_response_code(304);
            exit;
        }
        readfile($file);
        exit;
    }
}

// Everything else is a page of the software. It must see itself at the root of this domain,
// not inside /msk-bridge, so its links, CSS and JS point to the right place.
$https = ($_SERVER['HTTPS'] ?? '') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
$_SERVER['ASSET_URL'] = $_ENV['ASSET_URL'] = ($https ? 'https' : 'http').'://'.($_SERVER['HTTP_HOST'] ?? '').'/msk-public';
$_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'] = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $app.'/index.php';
chdir($app);
require $app.'/index.php';
