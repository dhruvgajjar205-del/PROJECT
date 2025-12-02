<?php
include 'functions.php';

// Logout the customer
logoutCustomer();

// Redirect to home page
header('Location: index.php');
exit();
?>
