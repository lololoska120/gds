<?php
session_start();
require 'includes/database.php';
require 'includes/functions.php';

if (!isLoggedIn()) {
    redirect('/login.php');
}

switch (userRole()) {
    case 'volunteer':
        redirect('/views/volunteer/project_list.php');
        break;
    case 'beneficiary':
        redirect('/views/beneficiary/request_help.php');
        break;
    case 'fund':
        redirect('/views/fund/post_project.php');
        break;
    case 'admin':
        redirect('/admin/dashboard.php');
        break;
    default:
        redirect('/login.php');
}
?>