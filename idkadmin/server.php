<?php

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simply to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

include("includes/connect.php");

// DB table to use
$table = 'idk_product';

// Table's primary key
$primaryKey = 'product_id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array('db' => 'product_image', 'dt' => 0),
	array('db' => 'product_name',  'dt' => 1),
	array('db' => 'product_api_id',  'dt' => 2),
	array('db' => 'product_supplier',  'dt' => 3),
	array('db' => 'product_price', 'dt' => 4, 'formatter' => function ($d, $row) {
		return number_format($d, 3, ',', '.');
	}),
	array('db' => 'product_quantity', 'dt' => 5),
	array('db' => 'product_id', 'dt' => 6),
	array('db' => 'product_currency', 'dt' => 7),
	array('db' => 'product_unit', 'dt' => 8),
	array('db' => 'product_sku', 'dt' => 9)
);

require('includes/ssp.class.php');

$where = "product_active = 1";
global $db;

echo json_encode(
	SSP::complex($_GET, $db, $table, $primaryKey, $columns, $where)
);
