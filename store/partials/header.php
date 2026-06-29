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
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af',
                            900: '#0f172a'
                        }
                    },
                    boxShadow: {
                        soft: '0 10px 30px rgba(15, 23, 42, 0.08)'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?= e(asset_url('css/app.css')) ?>">
</head>
<body class="bg-slate-50 text-slate-900">
<div class="mx-auto min-h-screen max-w-md bg-white shadow-soft md:max-w-6xl">
