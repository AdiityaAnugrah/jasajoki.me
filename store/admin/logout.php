<?php
require_once __DIR__ . '/../../app/auth.php';

admin_logout();
redirect(route_url('admin/login.php'));
