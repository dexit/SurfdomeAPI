<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

include('ms-dbfunc.php');

$ms_connect = ms_connect();
$arr_data = array();


if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "getData")
{

	$date =  (isset($_REQUEST['date'])) ? $_REQUEST['date'] : date('Y-m-d',strtotime("yesterday"));

	$channel = (isset($_REQUEST['channel'])) ?" AND SalesOrder.ChannelName ='".$_REQUEST['channel']."' " :"";

	$shipping = (isset($_REQUEST['type'])) ?" AND ServiceType.code ='".$_REQUEST['type']."' " :"";



	$sql = "SELECT  
			CONVERT(CHAR(16),[RequestedDeliveryDate],120) as date, 
			ServiceType.Name as shipping, 
			SalesOrder.ServiceTypeId,
			SalesOrderNumber as SalesOrderNumber,
			SalesOrder.CustomerPurchaseOrderReferenceNumber as webNumber,
			SalesOrder.ChannelName,
			Address.Region as OAorder
		FROM 
			SalesOrder 
		JOIN 
			ServiceType on (SalesOrder.ServiceTypeId = ServiceType.ServiceTypeId)
		JOIN
			Address ON (SalesOrder.InvoiceAddressId = Address.AddressId)
		WHERE
				SalesOrder.StatusId =2 
			and 
				SalesOrderNumber Not like 'STUDIO%'
			AND
			CONVERT(CHAR(10),[RequestedDeliveryDate],120) = '".$date."'	".$channel.$shipping." 
	   ORDER BY 
			CONVERT(CHAR(16),[RequestedDeliveryDate],120)";
	//echo $sql;
	$arr_data = ms_query_all($sql);

}


$data = base64_encode(serialize($arr_data));

echo $data;
