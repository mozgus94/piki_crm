<?php

	include("includes/functions.php");
	include("includes/common.php");

	$output = array();

	$select_query = $db->prepare("
		SELECT debt_equipment
		FROM idk_debt
		ORDER BY debt_equipment");

	$select_query->execute();

	while ($select_row = $select_query->fetch()) {
		// Replace " with '
		$debt_equipment = str_replace('"', '\'', $select_row['debt_equipment']);
		array_push($output, $debt_equipment);
	}

	echo json_encode($output);
?>