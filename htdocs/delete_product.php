<?php
// delete_product.php
//
// SiT (Support Incident Tracker) - Support call tracking system
// Copyright (C) 2000-2006 Salford Software Ltd.
//
// This software may be used and distributed according to the terms
// of the GNU General Public License, incorporated herein by reference.
//

// Author: Ivan Lucas <ivanlucas[at]users.sourceforge.net>

// Removes a product

$permission=65;  // Delete products
require('db_connect.inc.php');
require('functions.inc.php');

// This page requires authentication
require('auth.inc.php');

// External variables
$productid = cleanvar($_REQUEST['id']);

if (!empty($productid))
{
    $errors=0;
    // Check there are no contracts with this product
    $sql = "SELECT id FROM maintenance WHERE product=$productid LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result)>=1) $errors++;

    // check there are no incidents with this product
    $sql = "SELECT id FROM incidents WHERE product=$productid LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result)>=1) $errors++;

    // Check there is no software linked to this product
    $sql = "SELECT productid FROM softwareproducts WHERE productid=$productid LIMIT 1";
    $result = mysql_query($sql);
    if (mysql_num_rows($result)>=1) $errors++;

    if ($errors==0)
    {
        $sql = "DELETE FROM products WHERE id =$productid LIMIT 1";
        $result = mysql_query($sql);
        if (mysql_error()) trigger_error(mysql_error(),E_USER_ERROR);
        journal(CFG_LOGGING_NORMAL, 'Product Removed', "Product $productid was removed", CFG_JOURNAL_PRODUCTS, $productid);
        confirmation_page("2", "products.php", "<h2>Product removed Successfully</p><p align='center'>Please wait while you are redirected...</h2>");
    }
    else
    {
        include('htmlheader.inc.php');
        echo "<p class='error'>Sorry, this product cannot be deleted because it has been associated with one or more incidents, contracts or skills</p>";
        echo "<p align='center'><a href='products.php#{$productid}'>Return to products list</a></p>";
        include('htmlfooter.inc.php');
    }
}
else
{
    throw_error("Could not delete product", "Parameter(s) missing");
}
?>
