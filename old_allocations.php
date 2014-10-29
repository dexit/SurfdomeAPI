<?php


error_reporting(E_ALL);
ini_set('display_errors', '1');




	include('ms-dbfunc.php');

	$ms_connect = ms_connect();
	$arr_data = array();

	if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "getData")
	{

		$date ="";
		if (isset($_REQUEST['date']))
		{
			$date = " AND CONVERT(CHAR(10),SalesOrder.RequestedDeliveryDate,120) <= '". $_REQUEST['date']."'";
		}

		/*
				$sql= "SELECT 
						so.[SalesOrderId]
				     FROM 
						[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder] as so
					 WHERE
						StatusId NOT IN (4,5,6) ". $date;



			$arr_orders = ms_query_col($sql);

			$str_salesorders = "('". implode("','", $arr_orders) ."')";





			//print_r($arr_orders);


			$sql = "SELECT 
						ActionDetail.SalesOrderId as SalesOrderId
						,ActionGroup.Name as ActionName
					 FROM 
						[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[ActionDetail]
					  JOIN  
						ActionGroup on (ActionDetail.ActionGroupId = ActionGroup.ActionGroupId)
					  WHERE 
						ActionDetail.SalesOrderId in ".$str_salesorders."
					";

			$arr_data['action_m'] = ms_query_col_assoc($sql);


		$sql = "SELECT
					SalesOrderId
					,name
				FROM 
					[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrderActionGroup]
				  JOIN 
					ActionGroup on ([SalesOrderActionGroup].ActionGroupId = ActionGroup.ActionGroupId)
				WHERE
				SalesOrderId in  ".$str_salesorders."";

  			$arr_data['action_b'] = ms_query_col_assoc($sql);

 


			
			//print_r($arr_data['action']);



		$sql= "SELECT 
				   soi.[SalesOrderId] as SalesOrderId
				  ,max([SalesOrderNumber]) as SalesOrderNumber
				  ,max([ChannelName]) as ChannelName
				  ,sum(QuantityOrdered) as qty
				  ,max(CONVERT(CHAR(10),so.RequestedDeliveryDate,120)) as date
				  ,MAX(sos.name) as statusname
			   FROM 
					[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder] as so
				 JOIN 
					SalesOrderItem as soi on (so.SalesOrderId = soi.SalesOrderId)
				JOIN
				    SalesOrderStatus AS SOS ON (so.StatusId =SOS.SalesOrderStatusId)
			  WHERE
				StatusId NOT IN (4,5,6) ". $date ." 
			  GROUP BY 
				soi.SalesOrderId
			 ORDER by 
				max(CONVERT(CHAR(10),so.RequestedDeliveryDate,120))";
*/

		$sql = "select 
					SalesOrder.SalesOrderNumber as SalesOrderNumber,
					max(ActionGroup.Name) as ActionName ,
					max(ActionGroup.Priority) as Priority,
					max(SalesOrder.ChannelName) as ChannelName,
					max(CONVERT(CHAR(10), SalesOrder.RequestedDeliveryDate,120)) as date,
					max([SalesOrderStatus].name) as statusname,
					MAX(ActionGroup.CompletedTimestamp) as ct,
					MAX(SalesOrder.ReleasedToId) as ReleasedToId,
					SUM(SalesOrderItem.QuantityOrdered) as qty
				FROM 
					 ActionDetail  
					 inner join 
						[SalesOrderActionGroup] on (ActionDetail.ActionGroupId = [SalesOrderActionGroup].ActionGroupId)
					 inner join ActionGroup on (ActionDetail.ActionGroupId =ActionGroup.ActionGroupId)
						  inner join SalesOrderItem on  ( ActionDetail.ItemTypeId = SalesOrderItem.ItemTypeId)
					  inner join SalesOrder on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
					  inner join SalesOrderStatus on (SalesOrder.StatusId= [SalesOrderStatus].[SalesOrderStatusId])
				where  SalesOrder.StatusId  NOT IN (4,5,6)  ". $date ."   and  ActionGroup.Name not like 'STUDIO%'
					  group by SalesOrder.SalesOrderNumber
					  order by max(CONVERT(CHAR(10), SalesOrder.RequestedDeliveryDate,120))";


//echo $sql;
		$arr_data1 = ms_query_all_assoc($sql);

	//	print_r($arr_data);
		//$arr_data = $sql;

		$arr_orderNumber = array_keys($arr_data1);


		$sql = "SELECT 
SalesOrder.SalesOrderNumber as SalesOrderNumber,
min(SalesOrder.ReleasedToId) as ReleasedToId,
SUM(SalesOrderItem.QuantityOrdered) as qty
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  join SalesOrderItem on  (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
  where salesordernumber  in ('". implode("','", $arr_orderNumber) ."')
  group by salesordernumber";


	$arr_data2 = ms_query_all_assoc($sql);


	$arr_rows = array();
	$i=0;
	foreach($arr_data1 as $k => $v)
	{
		$arr_data1[$k][0]['qty'] = $arr_data2[$k][0]['qty'];
		$arr_data['orders'][] = $arr_data1[$k][0];
	}


//print_r($arr_data);
		
	}



	$data = base64_encode(serialize($arr_data));

	echo $data;