 <?php


error_reporting(E_ALL);
ini_set('display_errors', '1');

include('ms-dbfunc.php');

$ms_connect = ms_connect();
$arr = array();


if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "getData")
{
 
	$sql ="SELECT 
				 SUM(ni.Quantity) as pvx_onhand
			FROM 
				[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[ItemType] IT 
				LEFT  JOIN                           
				[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[NonSerializedInventoryItem] NI on (IT.ItemTypeId = NI.ItemTypeId)
				join 
				Holder on (ni.HolderId = Holder.HolderId)
				JOIN Location ON (Holder.HolderId = Location.HolderId)
			WHERE 
				ItemCode not like '~D%'
				AND Location.LocationUseTypeId !=4
				and Holder.HolderTypeId =1";
	$arr['pvx_onhand'] = ms_query_value($sql);


	$sql="SELECT 
				sum(soi.QuantityOrdered) as pvx_allocated
			FROM 
				SalesOrder so
			join 
				SalesOrderItem soi on (so.SalesOrderId = soi.SalesOrderId)
			WHERE 
				so.StatusId =2";

	$arr['pvx_allocated'] = ms_query_value($sql);

	$arr['pvx_avl'] = $arr['pvx_onhand'] - $arr['pvx_allocated'];

}

$data = base64_encode(serialize($arr));

echo $data;

?>