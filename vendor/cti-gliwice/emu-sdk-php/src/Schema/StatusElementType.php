<?php

declare(strict_types=1);

namespace eMU\Schema;

abstract class StatusElementType {

	const All = 'all';
	const Task = 'tasks';
	const Order = 'orders';
	const TradeTask = 'trade_tasks';
	const TradeOrder = 'trade_orders';
	const Event = 'events';
	const Project = 'projects';
	const Offer = 'offers';
	const SalesOrder = 'sales_orders';
	const PurchaseOrder = 'purchase_orders';
	const FileManager = 'disk';

}

?>
