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
$table = 'idk_client';

// Table's primary key
$primaryKey = 'client_id';

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array(
	array('db' => 'client_image', 'dt' => 0),
	array('db' => 'client_name',  'dt' => 1),
	// array('db' => 'client_code',  'dt' => 2),
	array('db' => 'client_city',  'dt' => 2),
	array('db' => 'client_id_number',   'dt' => 3),
	array('db' => 'client_pdv_number', 'dt' => 4),
	array('db' => 'client_id', 'dt' => 5),
	array('db' => 'client_address', 'dt' => 6),
	array('db' => 'client_postal_code', 'dt' => 7),
	array('db' => 'client_region', 'dt' => 8),
	array('db' => 'client_country', 'dt' => 9)
);

require('includes/ssp.class.php');

$where = "client_active = 1";
global $db;

echo json_encode(
	SSP::complex($_GET, $db, $table, $primaryKey, $columns, $where)
);
