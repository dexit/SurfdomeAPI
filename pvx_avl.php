<?php



  error_reporting(E_ALL);
ini_set('display_errors', '1');

include('ms-dbfunc.php');

$ms_connect = ms_connect();
$arr_pvx = array();

if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "getData")
{
 
  
			$sql ="SELECT 
						IT.ItemCode,
						COALESCE(SUM(ni.Quantity),0)
				  FROM 
						[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[ItemType] IT 
						LEFT  JOIN                           
						[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[NonSerializedInventoryItem] NI on (IT.ItemTypeId = NI.ItemTypeId)
						join 
						Holder on (ni.HolderId = Holder.HolderId)
						JOIN Location ON (Holder.HolderId = Location.HolderId)
				 WHERE 
						ItemCode not like '~D%'
					   AND
						Location.LocationUseTypeId !=4
					   AND 
						Holder.HolderTypeId =1
				GROUP BY 
						IT.ItemCode
						order by 
						IT.ItemCode";

			$arr_pvx = ms_query_col_assoc($sql);

			$sql = "SELECT
						it.ItemCode
						,sum([QuantityAllocated])
					FROM
						[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrderItemPicking] sp
						join 
						itemtype IT on (IT.ItemTypeId = sp.ItemTypeId)
					WHERE 
						 [QuantityPicked] < QuantityAllocated
					GROUP BY 
						it.ItemCode";


			$arr_pvx_all = ms_query_col_assoc($sql);

			foreach ($arr_pvx_all as $k  => $v)
			{
				if (isset($arr_pvx[$k]))
				{
					$tmp = 	$arr_pvx[$k] - $v;

					$av = ($tmp >0) ? $tmp : 0;

					$arr_pvx[$k] = 	$av;

					
				}
			}


			//print_r($arr_pvx);


}
$data = base64_encode(serialize($arr_pvx));
echo $data;


?>