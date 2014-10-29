<?php

	error_reporting(E_ALL);
	ini_set('display_errors', '1');

	include('ms-dbfunc.php');

	$ms_connect = ms_connect();

	$arr_so = array();

	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "getData")
	{

		 $sql = "SELECT 
					 LOC.Name,
					sum(Quantity) as items
				  FROM 
						[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[NonSerializedInventoryItem] AS STOCK
					JOIN 
						[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[Holder] AS LOC ON (STOCK.HolderId =LOC.HolderId)
				WHERE
						Barcode like 'SD1.BOX%'
				GROUP BY 
					LOC.Name
				ORDER BY 
					LOC.Name";


		$arr_so['loc'] = ms_query_all($sql);

		$sql= "SELECT 
				COUNT(ItemTypeId) as items
			  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[NonSerializedInventoryItem] AS STOCK
			JOIN [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[Holder] AS LOC ON (STOCK.HolderId =LOC.HolderId)
			WHERE
			Barcode like 'SD1.BOX%'";

		$arr_so['total'] = ms_query_value($sql);

		$sql = "SELECT 
				Holder.name as loc,
				ActionDetail.ItemTypeId as item,
				ActionDetail.ActionGroupId as actionid,
				CONVERT(CHAR(10),ActionDetail.CompletedTimeStamp,120) as pickeddate,
				ItemCode
		  FROM 
				[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[NonSerializedInventoryItem]
				  join ActionDetail on (ActionDetail.ItemTypeId = NonSerializedInventoryItem.ItemTypeId and ActionDetail.ToHolderId = [NonSerializedInventoryItem].HolderId)
				  join Holder on ([NonSerializedInventoryItem].HolderId = Holder.HolderId)
				  join ItemType on (ItemType.ItemTypeId = ActionDetail.ItemTypeId)
				  where Holder.Barcode like 'SD1.BOX%'
				  order by loc asc";

		 $arr_tmp = ms_query_all($sql);

		// $arr_so['inbox'] = $arr_tmp;

		foreach($arr_tmp as $tmp)
		{
			  $sql = "select 
							so.SalesOrderNumber
					  from 
						  SalesOrderActionGroup sa 
						  join 
						  SalesOrder so on (sa.SalesOrderId = so.SalesOrderId)
						  left join 
						  SalesOrderItem soi on (soi.SalesOrderId = so.SalesOrderId)
					  where 
						  sa.ActionGroupId = ".$tmp['actionid']." AND ItemTypeId =". $tmp['item'];
				
				$sale_number = ms_query_value($sql);

				$arrt = array();
				$arrt = $tmp;
				$arrt['salesnumber'] = $sale_number;

				$arr_so['inbox'][$tmp['loc']][] = $arrt;
		}
	}

	$rtndata = base64_encode(serialize($arr_so));
	echo $rtndata;
?>