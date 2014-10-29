<?php


error_reporting(E_ALL);
ini_set('display_errors', '1');

include('ms-dbfunc.php');

$ms_connect = ms_connect();
$arr = array();


if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "getData")
{


	$sql ="SELECT 
				CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date, 
				ServiceType.Name, 
				count(salesorder.salesorderid) as numord
			FROM 
				SalesOrder 
			JOIN 
				ServiceType on (SalesOrder.ServiceTypeId = ServiceType.ServiceTypeId)
			WHERE 
				statusid = 2
			GROUP BY 
				CONVERT(CHAR(10),[RequestedDeliveryDate],120), ServiceType.Name 
			ORDER BY 
				CONVERT(CHAR(10),[RequestedDeliveryDate],120)";

	$arr['shipping_allocated'] =  ms_query_all_assoc($sql);


	$sql = "SELECT
				ServiceTypeid, 
				Name
			FROM 
				ServiceType";

	$arr['shipping_type'] = ms_query_all($sql);

}



$data = base64_encode(serialize($arr));

echo $data;


