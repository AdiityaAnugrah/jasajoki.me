<?php
require_once __DIR__ . '/../../app/helpers.php';
$pageTitle = $pageTitle ?? app_config()['app_name'];
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#fffaf0',
                            100: '#fdf1d9',
                            200: '#f7e2b5',
                            300: '#eed19a',
                            500: '#d0a96d',
                            700: '#9a7342',
                            900: '#654728'
                        },
                        accent: {
                            50: '#eef4f2',
                            100: '#d7e5e1',
                            300: '#95b5ad',
                            500: '#2f5d57',
                            600: '#204943',
                            700: '#143c36',
                            900: '#0b2f2a'
                        }
                    },
                    boxShadow: {
                        soft: '0 20px 50px rgba(24, 37, 34, 0.08)',
                        floaty: '0 14px 30px rgba(47, 93, 87, 0.10)'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?= e(asset_url('css/app.css')) ?>">
</head>
<body>
<div class="site-shell">
<header class="site-header">
    <div class="store-container px-5 py-4 md:px-7 lg:px-8">
        <div class="site-header-bar">
            <a href="<?= e(route_url('index.php')) ?>" class="site-brand">
                <span class="site-brand-mark">J</span>
                <span>
                    <span class="site-brand-kicker">Jasajoki</span>
                    <span class="site-brand-title">Digital Store</span>
                </span>
            </a>
            <nav class="site-nav">
                <a href="<?= e(route_url('index.php')) ?>" class="site-nav-link">Store</a>
                <a href="<?= e(app_setting('store_whatsapp') ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', (string) app_setting('store_whatsapp')) : '#') ?>" class="site-nav-link">WhatsApp</a>
                <a href="<?= e(route_url('admin/login.php')) ?>" class="site-nav-ghost">Admin</a>
            </nav>
        </div>
    </div>
</header>
