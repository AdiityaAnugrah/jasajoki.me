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
                            50: '#f7f7f5',
                            100: '#efeee8',
                            200: '#ddd8cb',
                            500: '#5b5b50',
                            700: '#34342f',
                            900: '#171717'
                        },
                        accent: {
                            500: '#1f3a5f',
                            600: '#152944'
                        }
                    },
                    boxShadow: {
                        soft: '0 18px 50px rgba(15, 23, 42, 0.08)'
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="<?= e(asset_url('css/app.css')) ?>">
</head>
<body class="bg-stone-100 text-slate-900">
<div class="mx-auto min-h-screen max-w-md bg-[#fcfcfa] shadow-soft md:max-w-6xl">
