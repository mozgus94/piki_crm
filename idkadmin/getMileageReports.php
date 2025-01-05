<?php

include("includes/functions.php");
include("includes/common.php");

$output = '';
$report_employee = !empty($_POST['report_employee']) ? explode('|', $_POST['report_employee'])[1] : NULL;
$report_employee_name = !empty($_POST['report_employee']) ? explode('|', $_POST['report_employee'])[0] : NULL;
$report_date_from = !empty($_POST['report_date_from']) ? $_POST['report_date_from'] : NULL;
$report_date_to = !empty($_POST['report_date_to']) ? $_POST['report_date_to'] : NULL;
$type = !empty($_POST['type']) ? $_POST['type'] : NULL;

$owner_query = $db->prepare("
  SELECT owner_name, owner_image
  FROM idk_owner");

$owner_query->execute();

$owner = $owner_query->fetch();

$owner_name = $owner['owner_name'];
$owner_image = $owner['owner_image'];

if (isset($type) and $type == "print") {
  $output .= '
  <div id="print_header">
    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-6">
          <h3>Izvještaj - kilometraža</h3>
          <p class="idk_margin_top30">
            <strong>Unaviva d.o.o.</strong> <br>
            Dr. Irfana Ljubijankića 87 <br>
            77000 Bihać <br>
            Tel: 00 387 37 961 131 <br>
            E-Mail: info@unaviva.ba <br>
            Web: www.unaviva.ba <br>
          </p>
        </div>
        <div class="col-xs-6 text-right">
          <img src="' . getSiteUrlr() . 'idkadmin/files/owners/images/' . $owner_image . '" class="idk_print_logo" alt="' . $owner_name . '">
          <p class="idk_margin_top30" id="idk_print_header_right_col">
              <strong>Datum</strong><br>
              ' . date('d.m.Y.') . '
          </p>
        </div>
      </div>
    </div>
  </div>

  <div id="print_main">
    <div class="container-fluid">
      <div class="row idk_margin_top50">
        <div class="col-xs-6">
          <h5>Parametri</h5>
          <div class="row idk_margin_top30">
            <div class="col-xs-5 text-right">
              <p><strong>Komercijalista:</strong></p>
            </div>
            <div class="col-xs-7">
              <p>' . $report_employee_name . '</p>
            </div>
          </div>';

  if (isset($report_date_from)) {
    $output .= '
          <div class="row">
            <div class="col-xs-5 text-right">
              <p><strong>Datum od:</strong></p>
            </div>
            <div class="col-xs-7">
              <p>' . date('d.m.Y.', strtotime($report_date_from)) . '</p>
            </div>
          </div>';
  }

  if (isset($report_date_to)) {
    $output .= '
          <div class="row">
            <div class="col-xs-5 text-right">
              <p><strong>Datum do:</strong></p>
            </div>
            <div class="col-xs-7">
              <p>' . date('d.m.Y.', strtotime($report_date_to)) . '</p>
            </div>
          </div>';
  }

  $output .= '
        </div>

        <div class="col-xs-6">
          <h5>Izvještaj</h5>
          <div class="row idk_margin_top30">
            <div class="col-xs-5 text-right">
              <p><strong>Ukupno kilometara:</strong></p>
            </div>
            <div class="col-xs-7">
              <p id="idk_print_total_mileage"></p>
            </div>
          </div>
        </div>
        <div class="col-xs-12 text-center idk_margin_top30">
          <table width="100%" class="tg" id="idk_print_table">
            <thead>
              <tr>
                <th width="10%" class="tg-kj9p">ID</th>
                <th width="15%" class="tg-kj9p">Komercijalista</th>
                <th width="15%" class="tg-kj9p">Početno vrijeme</th>
                <th width="15%" class="tg-kj9p">Početna kilometraža</th>
                <th width="15%" class="tg-kj9p">Završno vrijeme</th>
                <th width="15%" class="tg-kj9p">Završna kilometraža</th>
                <th width="15%" class="tg-kj9p">Razlika</th>
              </tr>
            </thead>
            <tbody>';
}

if (isset($report_employee)) {
  $query = $db->prepare("
    SELECT mileage_id, mileage_employee_id, mileage_start_time, mileage_end_time, mileage_amount_start, mileage_amount_end
    FROM idk_mileage
    WHERE mileage_employee_id = :mileage_employee_id AND (mileage_start_time BETWEEN :mileage_start_time AND :mileage_end_time) AND ((mileage_end_time BETWEEN :mileage_start_time AND :mileage_end_time) OR mileage_end_time IS NULL)
    ORDER BY mileage_id DESC");

  if (isset($report_date_from) and isset($report_date_to)) {
    $query->execute(array(
      ':mileage_employee_id' => $report_employee,
      ':mileage_start_time' => date('Y-m-d', strtotime($report_date_from)),
      ':mileage_end_time' => date('Y-m-d', strtotime($report_date_to))
    ));
  } elseif (isset($report_date_from)) {

    $query->execute(array(
      ':mileage_employee_id' => $report_employee,
      ':mileage_start_time' => date('Y-m-d', strtotime($report_date_from)),
      ':mileage_end_time' => date('Y-m-d')
    ));
  } elseif (isset($report_date_to)) {

    $query->execute(array(
      ':mileage_employee_id' => $report_employee,
      ':mileage_start_time' => date('Y-m-d', strtotime('1970-01-01')),
      ':mileage_end_time' => date('Y-m-d', strtotime($report_date_to))
    ));
  } else {
    $query = $db->prepare("
      SELECT mileage_id, mileage_employee_id, mileage_start_time, mileage_end_time, mileage_amount_start, mileage_amount_end
      FROM idk_mileage
      WHERE mileage_employee_id = :mileage_employee_id
      ORDER BY mileage_id DESC");

    $query->execute(array(
      ':mileage_employee_id' => $report_employee
    ));
  }
} else {
  $query = $db->prepare("
    SELECT mileage_id, mileage_employee_id, mileage_start_time, mileage_end_time, mileage_amount_start, mileage_amount_end
    FROM idk_mileage
    ORDER BY mileage_id DESC");

  $query->execute();
}

$number_of_rows = $query->rowCount();

if ($number_of_rows > 0) {
  while ($row = $query->fetch()) {

    $mileage_id = $row['mileage_id'];
    $mileage_employee_id = $row['mileage_employee_id'];
    $mileage_start_time = $row['mileage_start_time'];
    $mileage_end_time = $row['mileage_end_time'];
    $mileage_amount_start = $row['mileage_amount_start'];
    $mileage_amount_end = $row['mileage_amount_end'];

    if (isset($mileage_employee_id)) {
      $query_employee = $db->prepare("
        SELECT employee_first_name, employee_last_name
        FROM idk_employee
        WHERE employee_id = :employee_id");

      $query_employee->execute(array(
        ':employee_id' => $mileage_employee_id
      ));

      $row_employee = $query_employee->fetch();

      $employee_first_name = $row_employee['employee_first_name'];
      $employee_last_name = $row_employee['employee_last_name'];
    }

    if (isset($type) and $type == "print") {

      $output .= '
          <tr>
            <td width="10%" class="tg-2qw4">' . $mileage_id . '</td>
            <td width="15%" class="tg-2qw4">' . $employee_first_name . ' ' . $employee_last_name . '</td>
            <td width="15%" class="tg-2qw4">';
      if (isset($mileage_start_time)) {
        $output .= date('d.m.Y. H:i', strtotime($mileage_start_time));
      }
      $output .= '
            </td>
            <td width="15%" class="tg-2qw4">';
      if (isset($mileage_amount_start)) {
        $output .= $mileage_amount_start . ' km';
      }
      $output .= '
            </td>
            <td width="15%" class="tg-2qw4">';
      if (isset($mileage_end_time)) {
        $output .= date('d.m.Y. H:i', strtotime($mileage_end_time));
      }
      $output .= '
            </td>
            <td width="15%" class="tg-2qw4">';
      if (isset($mileage_amount_end)) {
        $output .= $mileage_amount_end . ' km';
      }
      $output .= '
            </td>
            <td width="15%" class="tg-2qw4">';
      if (isset($mileage_amount_end)) {
        $output .= ($mileage_amount_end - $mileage_amount_start) . ' km';
      }
      $output .= '
            </td>';
    } else {

      $output .= '
        <tr>
          <td>
            ' . $mileage_id . '
          </td>
          <td>
            <a href="' . getSiteUrlr() . 'idkadmin/employees?page=open&id=' . $mileage_employee_id . '">' . $employee_first_name . ' ' . $employee_last_name . '</a>
          </td>
          <td data-sort="' . $mileage_start_time . '">';

      if (isset($mileage_start_time)) {
        $output .= date('d.m.Y. H:i', strtotime($mileage_start_time));
      }

      $output .= '
          </td>
          <td data-sort="' . $mileage_amount_start . '">';

      if (isset($mileage_amount_start)) {
        $output .= $mileage_amount_start . ' km';
      }

      $output .= '
          </td>
          <td data-sort="' . $mileage_end_time . '">';

      if (isset($mileage_end_time)) {
        $output .= date('d.m.Y. H:i', strtotime($mileage_end_time));
      }

      $output .= '
          </td>
          <td data-sort="' . $mileage_amount_end . '">';

      if (isset($mileage_amount_end)) {
        $output .= $mileage_amount_end . ' km';
      }

      $output .= '
          </td>
          <td data-sort="' . ($mileage_amount_end - $mileage_amount_start) . '">';

      if (isset($mileage_amount_end)) {
        $output .= ($mileage_amount_end - $mileage_amount_start) . ' km';
      }

      $output .= '
          </td>';
    }
  }
}

if (isset($type) and $type == "print") {
  $output .= '</tbody>
            </table>
          </div>
        </div>
      </div>
    </div>';
}
echo $output;
