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
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
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
<body class="bg-brand-50 text-slate-900">
<div class="mx-auto min-h-screen max-w-md bg-[#fffaf0] shadow-soft md:max-w-6xl">
