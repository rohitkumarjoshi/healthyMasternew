		
			<div class="row">
			
			<?php echo $this->Html->link('<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
					<div class="dashboard-stat" style="background-color:#CDCDCD; height:40px">
						
						<div class="details">
							
						</div>
						<p class="more  tooltips" data-placement="bottom" data-original-title="" href="" style="background-color:#ef0907; color:#fff; text-align:center"><b style="color:#fff;">Info Message</b>
						</p>
					</div>
				</div>','/AppNotifications/home?page=home',['escape'=>false]) ?>
			<!--	
				<?php echo $this->Html->link('<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
					<div class="dashboard-stat" style="background-color:#888888;height:40px">
						
						<div class="details">
							
						</div>
						<p class="more  tooltips" data-placement="bottom" data-original-title="" href="" style="background-color:#ffffff; color:#000; text-align:center"><b>Bulk Booking</b>
						</p>
					</div>
				</div>','/AppNotifications/home?page=bulkbooking',['escape'=>false]) ?>
				
				
				<?php echo $this->Html->link('
				<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
					<div class="dashboard-stat  red-intense" style="height:40px">
						
						<div class="details" >
						
						</div><p class="more  tooltips" data-placement="bottom" data-original-title="" href="" style="background-color:#ffffff; color:#000; text-align:center; height:30px"><b>Bulk Booking</b>
						</p>
					</div>
				</div>
				</a>
				</div>','/AppNotifications/home?page=referfriend',['escape'=>false]) ?>
				
				
				<?php echo $this->Html->link('
				<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
					<div class="dashboard-stat red-intense" style="height:40px">
						
						<div class="details">
						
						</div><p class="more  tooltips" data-placement="bottom" data-original-title="" href="" style="background-color:#ffffff; color:#000; text-align:center"><b>Add Money</b>
						</p>
					</div>
				</div>','/AppNotifications/home?page=addmoney',['escape'=>false]) ?>
				
				
				<?php echo $this->Html->link('
				<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
					<div class="dashboard-stat green-haze" style="height:40px">
						
						<div class="details">
						
						</div><p class="more  tooltips" data-placement="bottom" data-original-title="" href="" style="background-color:#ffffff; color:#000; text-align:center"><b>View Cart</b>
						</p>
					</div>
				</div>	','/AppNotifications/home?page=viewcart',['escape'=>false]) ?>
				
				
				
				
				<?php echo $this->Html->link('
				<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
					<div class="dashboard-stat purple-plum" style="height:40px">
						
						<div class="details">
						
						</div><p class="more  tooltips" data-placement="bottom" data-original-title="" href="" style="background-color:#ffffff; color:#000; text-align:center"><b>Combo Offers</b>
						</p>
					</div>
				</div>	','/AppNotifications/home?page=specialoffers',['escape'=>false]) ?>
				-->
				<!-- <?php echo $this->Html->link('
				<div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
					<div class="dashboard-stat red-intense" style="height:40px">
						
						<div class="details">
						
						</div><p class="more  tooltips" data-placement="bottom" data-original-title="" href="" style="background-color:#ef0907; color:#fff; text-align:center"><b style="color:#fff;">Product Description</b>
						</p>
					</div>

				</div>','/AppNotifications/item_view',['escape'=>false]) ?> -->
			</div>

			<style>
.table>thead>tr>th{
	font-size:12px !important;
}
</style>
<div class="row">
	<div class="col-md-12">
		<div class="portlet light bordered">
			<div class="portlet-title">
				<div class="caption">
					<i class="font-purple-intense"></i>
					<span class="caption-subject font-purple-intense ">
						<i class="fa fa-book"></i> Notification History</span>
				</div>
				
			</div>
			<div class="portlet-body">
				<table class="table table-condensed table-hover table-bordered" id="main_tble">
					<thead>
						<tr>
							<th scope="col"  width="1%">Sr. No.</th>
							<th scope="col"  width="10%">Notification Date</th>
							<th scope="col"  width="10%">Image</th>
							<th scope="col"  width="60%">Message</th>
							<th scope="col"  width="5%">Type</th>
							<th scope="col"  width="5%">Total Send</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$sr_no=0; foreach ($appNotifications as $appNotification): $sr_no++;
						?>
						<tr>
							<td><?= $sr_no ?></td>
							<td><?= $appNotification->created_on ?></td>
							<td><?php if (!empty($appNotification->image)) { ?><?= @$this->Html->image('/img/Notify_images/'.$appNotification->image, ['style'=>'width:50px; height:50px;']); ?><?php } else { echo 'No Image'; } ?></td>
							<td><?= h($appNotification->message) ?></td> 
							<td><?= h($appNotification->screen_type) ?></td>
							<td><?= h(@$appNotification->app_notification_customers[0]->count_customer) ?></td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
			
				 
				