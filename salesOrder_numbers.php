<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

include('ms-dbfunc.php');

$ms_connect = ms_connect();


if (isset($_REQUEST['mode']) && $_REQUEST['mode'] == "getData")
{


//total number of order uploaded to pvx
$date = date("Y-m-d");

$fivedays = (isset($_REQUEST['fivedays']) && !empty($_REQUEST['fivedays'])) ? $_REQUEST['fivedays'] : date('Y-m-d', strtotime("-8 day"));

$sql = "SELECT
     count([SalesOrderNumber])

  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  where
  SalesOrderNumber Not like 'STUDIO%'
  and
   CONVERT(CHAR(10),[RequestedDeliveryDate],120) =   '".$date."'";

$arr['uploaded'] = ms_query_value($sql);



$sql ="SELECT
     CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,
     count([SalesOrderNumber]) as numord

  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  where
  SalesOrderNumber Not like 'STUDIO%'
  and 
   CONVERT(CHAR(10),[RequestedDeliveryDate],120) >= '".$fivedays."'
   group by CONVERT(CHAR(10),[RequestedDeliveryDate],120)
   order by CONVERT(CHAR(10),[RequestedDeliveryDate],120)
";

$arr['past_uploaded_orders'] = ms_query_all_assoc($sql);



$sql= "SELECT
     CONVERT(CHAR(10),SalesOrder.RequestedDeliveryDate,120) as date,
     SUM(SalesOrderItem.QuantityOrdered) as items

  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  inner join SalesOrderItem on (SalesOrder.SalesOrderId = SalesOrderItem.SalesOrderId)
  where
	SalesOrderNumber Not like 'STUDIO%'
	and 
   CONVERT(CHAR(10),SalesOrder.RequestedDeliveryDate,120) >= '".$fivedays."'
   group by CONVERT(CHAR(10),SalesOrder.RequestedDeliveryDate,120)
   order by CONVERT(CHAR(10),SalesOrder.RequestedDeliveryDate,120)";


$arr['past_uploaded_orders_items'] = ms_query_all_assoc($sql);



  $sql= "SELECT 
	CONVERT(CHAR(10),[RequestedDeliveryDate],120)  as date,
	COUNT(*) as numord
	FROM  
	SalesOrder 
	WHERE 
	SalesOrderId in (
		select 
			SalesOrderItem.SalesOrderId
		FROM 
			SalesOrder 
		join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrderNumber Not like 'STUDIO%'
			and 
			CONVERT(CHAR(10),SalesOrder.RequestedDeliveryDate,120) >= '".$fivedays."'
		group by 
			SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered]) =1
	)
	group by CONVERT(CHAR(10),[RequestedDeliveryDate],120)
	order by CONVERT(CHAR(10),[RequestedDeliveryDate],120)";

$arr['past_uploaded_orders_single'] = ms_query_all_assoc($sql);



  $sql= "    SELECT 
	CONVERT(CHAR(10),[RequestedDeliveryDate],120)  as date,
	COUNT(*) as numord
	FROM  
	SalesOrder 
	WHERE 
	SalesOrderId in (
		select 
			SalesOrderItem.SalesOrderId
		FROM 
			SalesOrder 
		join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrderNumber Not like 'STUDIO%'
			and 
			CONVERT(CHAR(10),SalesOrder.RequestedDeliveryDate,120) >= '".$fivedays."'
		group by 
			SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered])<>1
	)
	group by CONVERT(CHAR(10),[RequestedDeliveryDate],120)
	order by CONVERT(CHAR(10),[RequestedDeliveryDate],120)";

$arr['past_uploaded_orders_multi'] = ms_query_all_assoc($sql);



$sql ="SELECT count(*)
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[ActionGroup]
  where Name like 'ACT%' and CompletedTimestamp is null";

$arr['numactions'] = ms_query_value($sql);


$sql = "SELECT 
ActionGroup.Priority as Priority,
count(*) as PriorityCount
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[ActionGroup]
  where Name like 'ACT%' and CompletedTimestamp is null
  group by ActionGroup.Priority";

 $arr['actionPriority'] = ms_query_all($sql);

$sql= "SELECT 
				COUNT(ItemTypeId) as items
			  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[NonSerializedInventoryItem] AS STOCK
			JOIN [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[Holder] AS LOC ON (STOCK.HolderId =LOC.HolderId)
			WHERE
			Barcode like 'SD1.BOX%'";

		$arr['boxitems'] = ms_query_value($sql);


// sales order in the different status
$sql = "SELECT
s2.Name
     ,count([SalesOrderNumber]) as numso

  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder] s1
  join 
  [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrderStatus] s2 on (s1.StatusId =s2.SalesOrderStatusId)
  where s1.StatusId != 5
group by s2.Name";

$arr['orders'] = ms_query_all($sql);



$sql = "SELECT SUM(SalesOrderItem.QuantityOrdered)
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  join SalesOrderItem on (SalesOrder.SalesOrderId= SalesOrderItem.SalesOrderId)
  where SalesOrderNumber Not like 'STUDIO%' and SalesOrder.StatusId =2";


$arr['allocated_items'] =  ms_query_value($sql);


$sql = "SELECT SUM(SalesOrderItem.QuantityOrdered)
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  join SalesOrderItem on (SalesOrder.SalesOrderId= SalesOrderItem.SalesOrderId)
  where SalesOrderNumber  like 'STUDIO%' and SalesOrder.StatusId =2";


$arr['allocated_studio_items'] =  ms_query_value($sql);



$sql = "SELECT count(*)as Despatched_Today
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrderHistory]
  where StatusId =5 and  CONVERT(CHAR(10),[DateTimestamp],120) = '". $date ."'";

  $arr['Despatched Today'] = ms_query_value($sql);

$arr['orders'][] = array('Name' => 'Despatched Today', 'numso' => $arr['Despatched Today']);



$sql = "SELECT CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,
COUNT(SalesOrderId)as numord
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  where StatusId = 2 and SalesOrderNumber Not like 'STUDIO%'
  group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) 
  order by date";

$arr['allocated_orders'] = ms_query_all($sql);
$arr['allocated_orders1'] = ms_query_all_assoc($sql);

$sql = "SELECT 
COUNT(SalesOrderId)as numord
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  where StatusId = 2 and SalesOrderNumber  like 'STUDIO%'
  group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";

$arr['allocated_studio_orders'] = ms_query_value($sql);


$sql = "SELECT 
CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,
			COUNT(*)as numord
		FROM  
			SalesOrder 
		WHERE 
		SalesOrderId in (
select 

SalesOrderItem.SalesOrderId

FROM 
			SalesOrder 
			join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrder.StatusId =2 
		group by SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered])=1
		)
		group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";

$arr['allocated_orders_single'] = ms_query_all_assoc($sql);



$sql = "SELECT 
CONVERT(CHAR(10),[RequestedDeliveryDate],120)  as date,
			COUNT(*) as numord
		FROM  
			SalesOrder 
		WHERE 
		SalesOrderId in (
select 

SalesOrderItem.SalesOrderId

FROM 
			SalesOrder 
			join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrder.StatusId =2 
		group by SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered])<>1
		)
		group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";


$arr['allocated_orders_multi'] = ms_query_all_assoc($sql);




$sql = "SELECT CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,  COUNT([SalesOrderId])  AS numbersalesorder
 FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
 WHERE salesorderid IN (
SELECT 
	salesorderid
 FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[ActionDetail]
  where [CompletedTimeStamp] is null
  group by salesorderid
 )
 GROUP BY CONVERT(CHAR(10),[RequestedDeliveryDate],120)";

$arr['released_orders'] = ms_query_all_assoc($sql);




$sql = "SELECT CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,
COUNT(SalesOrderId)as numord
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  where StatusId = 7  and SalesOrderNumber Not like 'STUDIO%'
  group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";

$arr['partially_allocated'] = ms_query_all_assoc($sql);



$sql = "SELECT 
CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,
			COUNT(*)as numord
		FROM  
			SalesOrder 
		WHERE 
		SalesOrderId in (
select 

SalesOrderItem.SalesOrderId

FROM 
			SalesOrder 
			join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrder.StatusId =7 
		group by SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered])=1
		)
		group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";

$arr['partially_allocated_single'] = ms_query_all_assoc($sql);



$sql = "SELECT 
CONVERT(CHAR(10),[RequestedDeliveryDate],120)  as date,
			COUNT(*) as numord
		FROM  
			SalesOrder 
		WHERE 
		SalesOrderId in (
select 

SalesOrderItem.SalesOrderId

FROM 
			SalesOrder 
			join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrder.StatusId =7   and SalesOrderNumber Not like 'STUDIO%'
		group by SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered])<>1
		)
		group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";


$arr['partially_allocated_multi'] = ms_query_all_assoc($sql);

$sql = "SELECT CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,
COUNT(SalesOrderId)as numord
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  where StatusId =3  and SalesOrderNumber Not like 'STUDIO%'
  group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";

//$arr['picked'] = ms_query_all($sql);


$sql = "SELECT CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,
COUNT(SalesOrderId)as numord
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  where StatusId =0  and SalesOrderNumber Not like 'STUDIO%'
  group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";

$arr['new'] = ms_query_all_assoc($sql);


$sql = "SELECT 
CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,
			COUNT(*)as numord
		FROM  
			SalesOrder 
		WHERE 
		SalesOrderId in (
select 

SalesOrderItem.SalesOrderId

FROM 
			SalesOrder 
			join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrder.StatusId =0   and SalesOrderNumber Not like 'STUDIO%'
		group by SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered])=1
		)
		group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";

$arr['new_single'] = ms_query_all_assoc($sql);



$sql = "SELECT 
CONVERT(CHAR(10),[RequestedDeliveryDate],120)  as date,
			COUNT(*) as numord
		FROM  
			SalesOrder 
		WHERE 
		SalesOrderId in (
select 

SalesOrderItem.SalesOrderId

FROM 
			SalesOrder 
			join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrder.StatusId =0   and SalesOrderNumber Not like 'STUDIO%'
		group by SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered])<>1
		)
		group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";


$arr['new_multi'] = ms_query_all_assoc($sql);


$sql = "SELECT CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,
COUNT(SalesOrderId)as numord
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  where StatusId =8
  group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";

$arr['partially_picked'] = ms_query_all_assoc($sql);


$sql = "SELECT 
CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,
			COUNT(*)as numord
		FROM  
			SalesOrder 
		WHERE 
		SalesOrderId in (
select 

SalesOrderItem.SalesOrderId

FROM 
			SalesOrder 
			join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrder.StatusId =8  and SalesOrderNumber Not like 'STUDIO%'
		group by SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered])=1
		)
		group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";

$arr['partially_picked_single'] = ms_query_all_assoc($sql);



$sql = "SELECT 
CONVERT(CHAR(10),[RequestedDeliveryDate],120)  as date,
			COUNT(*) as numord
		FROM  
			SalesOrder 
		WHERE 
		SalesOrderId in (
select 

SalesOrderItem.SalesOrderId

FROM 
			SalesOrder 
			join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrder.StatusId =8   and SalesOrderNumber Not like 'STUDIO%'
		group by SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered])<>1
		)
		group by CONVERT(CHAR(10),[RequestedDeliveryDate],120) ";


$arr['partially_picked_multi'] = ms_query_all_assoc($sql);

$sql ="SELECT channelname,
COUNT(SalesOrderId)as numord
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  where SalesOrderNumber Not like 'STUDIO%'
  and CONVERT(CHAR(10),[RequestedDeliveryDate],120) = '".$date."'
  group by CONVERT(CHAR(10),[RequestedDeliveryDate],120),channelname
  order by CONVERT(CHAR(10),[RequestedDeliveryDate],120)";

$arr['channel_today'] =ms_query_all_assoc($sql);



$sql = "SELECT CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,channelname,
COUNT(SalesOrderId)as numord
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
  where StatusId = 2  and SalesOrderNumber Not like 'STUDIO%'
  group by CONVERT(CHAR(10),[RequestedDeliveryDate],120),channelname
  order by CONVERT(CHAR(10),[RequestedDeliveryDate],120)";

 $arr['channel_alloc'] =  ms_query_all_assoc($sql);






$sql = "SELECT 
			CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date,  
			COUNT([SalesOrderId])  AS numord, 
			channelname
		 FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[SalesOrder]
		 WHERE salesorderid IN (
		SELECT 
			salesorderid
		 FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[ActionDetail]
		  where [CompletedTimeStamp] is null
		  group by salesorderid
		 )
		 GROUP BY CONVERT(CHAR(10),[RequestedDeliveryDate],120), channelname
		 order by CONVERT(CHAR(10),[RequestedDeliveryDate],120)";


//$arr['channel_released'] =  ms_query_all_assoc($sql);


$sql ="select 
CONVERT(CHAR(10),[RequestedDeliveryDate],120)
from SalesOrder
join
ActionDetail on (SalesOrder.SalesOrderId= ActionDetail.SalesOrderId)
where ActionDetail.CompletedTimeStamp is null
group by CONVERT(CHAR(10),[RequestedDeliveryDate],120)
order by CONVERT(CHAR(10),[RequestedDeliveryDate],120)";

$arr['order_dates'] =  ms_query_col($sql);


$sql = "SELECT 
			COUNT(*) as numord
		FROM  
			SalesOrder 
		WHERE 
		SalesOrderId in (
select 

SalesOrderItem.SalesOrderId

FROM 
			SalesOrder 
			join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrder.StatusId =2  and SalesOrderNumber Not like 'STUDIO%'
		group by SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered])=1
		)";

 $arr['single_pick'] = ms_query_value($sql);



 $sql = "SELECT 
			COUNT(*) as numord
		FROM  
			SalesOrder 
		WHERE 
		SalesOrderId in (
select 

SalesOrderItem.SalesOrderId

FROM 
			SalesOrder 
			join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrder.StatusId =2
		group by SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered])<>1
		)";

 $arr['multi_pick'] = ms_query_value($sql);


 $sql = "SELECT 
			SUM([QuantityOrdered])
		FROM  
			SalesOrderItem 
		WHERE 
		SalesOrderId in (
select 

SalesOrderItem.SalesOrderId

FROM 
			SalesOrder 
			join 
			SalesOrderItem on (SalesOrderItem.SalesOrderId = SalesOrder.SalesOrderId)
		WHERE
			SalesOrder.StatusId =2  and SalesOrderNumber Not like 'STUDIO%'
		group by SalesOrderItem.SalesOrderId having sum(SalesOrderItem.[QuantityOrdered])<>1
		)";
 $arr['multi_pick_items'] = ms_query_value($sql);


 $sql = "SELECT channelname FROM SalesOrder WHERE channelname is not null GROUP BY ChannelName";
 $arr['ChannelName'] = ms_query_col($sql);


$sql = "SELECT  
			CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date, 
			Region, 
			count(salesorder.salesorderid) as numord
		FROM 
			SalesOrder 
		JOIN 
			Address on (SalesOrder.ShippingAddressId = Address.AddressId)
		WHERE
			SalesOrder.StatusId =2 and Address.Region in 
			(
				SELECT
					  [Region]
				  FROM 
					[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[Address]
				  WHERE 
					(region is  null or region  = '' or Region like '[A-Z]%')

				  GROUP BY 
						Region
			  )
			   and SalesOrderNumber Not like 'STUDIO%'
	   GROUP BY 
			CONVERT(CHAR(10),[RequestedDeliveryDate],120), Region
	   ORDER BY 
			CONVERT(CHAR(10),[RequestedDeliveryDate],120)";

$arr['shipping_allocated'] =  ms_query_all_assoc($sql);


$sql = "SELECT
		  Region
		FROM 
			[PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[Address]
		 WHERE 
				(region is  null or region  = '' or Region like '[A-Z]%')
		GROUP BY 
			Region";

$arr['shipping_type'] = ms_query_col($sql);




$sql ="select 
CONVERT(CHAR(10),[RequestedDeliveryDate],120) as date, 
			Region, 
			count(salesorder.salesorderid) as numord
		FROM 
			SalesOrder 
		JOIN 
			Address on (SalesOrder.ShippingAddressId = Address.AddressId)
where 
statusid = 2 and

(region is not null and region  <> '' and Region like '[A-Z]%') and 
salesorderid  
in (
SELECT
[SalesOrderId]
      
  FROM [PeopleVox.OneBusinessPortal.Surfdome2536].[dbo].[ActionDetail]
    where CompletedTimeStamp is null
    group by SalesOrderId 
    )
    
     GROUP BY 
			CONVERT(CHAR(10),[RequestedDeliveryDate],120), Region
	   ORDER BY 
			CONVERT(CHAR(10),[RequestedDeliveryDate],120)";

//$arr['shipping_released'] = ms_query_all_assoc($sql);

	$data = base64_encode(serialize($arr));
	echo $data;
}
else
{
	echo false;
}
