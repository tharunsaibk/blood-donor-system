<?php
require_once __DIR__ . '/includes/bootstrap.php';
auth_logout();
session_start();
flash('success', 'You have been logged out.');
redirect('login.php');
