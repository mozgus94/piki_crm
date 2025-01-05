<?php

include("includes/functions.php");
include("includes/common.php");

$output = '';
$count_clients = 0;
$route_clients = !empty($_POST['routeClients']) ? $_POST['routeClients'] : NULL;

if (isset($route_clients)) {

  foreach ($route_clients as $route_client) {
    if (isset($route_client) and $route_client != "") {
      $count_clients++;

      $client_query = $db->prepare("
        SELECT client_name
        FROM idk_client
        WHERE client_id = :client_id");

      $client_query->execute(array(
        ':client_id' => $route_client
      ));

      $client_row = $client_query->fetch();
      $client_name = $client_row['client_name'];

      $output .= '<div class="list-group-item handle">
                  <input type="hidden" name="route_client_id[]" id="route_client_id_' . $route_client . '" value="' . $route_client . '">
                    <div class="row">
                      <div class="col-xs-1">
                        <div>
                          <i class="fa fa-bars"></i>
                        </div>
                      </div>
                      <div class="col-xs-11">
                        <p>' . $client_name . '</p>
                      </div>
                    </div>
                  </div>';
    }
  }
}

echo $output;
?>