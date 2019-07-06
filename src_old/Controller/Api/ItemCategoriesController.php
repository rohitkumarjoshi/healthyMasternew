<?php
namespace App\Controller\Api;
use App\Controller\Api\AppController;
use Cake\Event\Event;
class ItemCategoriesController extends AppController
{
	public function home()
    {
		$customer_id=$this->request->query('customer_id');
		$dynamic=[];
		
		$HomeScreens=$this->ItemCategories->HomeScreens->find()->where(['HomeScreens.section_show'=>'Yes']);
		//pr($HomeScreens->toArray()); exit;
		
		foreach($HomeScreens as $HomeScreen){
			
			if($HomeScreen->layout=='Banner'){
				
				$banners = $this->ItemCategories->Banners->find('All')->where(['Banners.status'=>'Active']);
				
				$Banners = array("title"=>$HomeScreen->title,'layout'=>$HomeScreen->layout,'category_id'=>$HomeScreen->category_id,'item_id'=>$HomeScreen->item_id,'image'=>$HomeScreen->image,"Banners"=>$banners);
				array_push($dynamic,$Banners);
			}
			if($HomeScreen->layout=='CategoryItems'){
				
				$itemCategories = $this->ItemCategories->Items->find()->where(['Items.ready_to_sale'=>'Yes','item_category_id'=>$HomeScreen->category_id])->contain(['ItemVariations']);
				
				$ItemCategories = array("title"=>$HomeScreen->title,'layout'=>$HomeScreen->layout,'category_id'=>$HomeScreen->category_id,'item_id'=>$HomeScreen->item_id,'image'=>$HomeScreen->image,"ItemCategories"=>$itemCategories);
				array_push($dynamic,$ItemCategories);
			}
			if($HomeScreen->layout=='SingleImage'){
				
				$items = $this->ItemCategories->Items->find()->where(['Items.ready_to_sale'=>'Yes','id'=>$HomeScreen->item_id])->contain(['ItemVariations']);
				
				$Items = array("title"=>$HomeScreen->title,'layout'=>$HomeScreen->layout,'category_id'=>$HomeScreen->category_id,'item_id'=>$HomeScreen->item_id,'image'=>$HomeScreen->image,"Items"=>$items);
				array_push($dynamic,$Items);
			}
			if($HomeScreen->layout=='Category'){
				 $category = $this->ItemCategories->find()->where(['is_deleted'=>0]);
				
				$Category = array("title"=>$HomeScreen->title,'layout'=>$HomeScreen->layout,'category_id'=>$HomeScreen->category_id,'item_id'=>$HomeScreen->item_id,'image'=>$HomeScreen->image,"Category"=>$category);
				array_push($dynamic,$Category);
			}
			
			if($HomeScreen->layout=='MostSelling'){
				
				$querys=$this->ItemCategories->Items->ItemLedgers->find();
				$recently_bought=$querys
				->select(['total_rows' => $querys->func()->count('ItemLedgers.id'),'item_id'])
				->where(['inventory_transfer'=>'no','status'=>'out'])
				->group(['ItemLedgers.item_id'])
				->order(['total_rows'=>'DESC'])
				->limit(10)
				->contain(['Items'=>function($q) use($customer_id) {
				return $q->select(['name', 'image','ready_to_sale','is_new'])
				->contain(['ItemVariations'=>
				function($q) use($customer_id) {
					return $q->where(['ready_to_sale' =>'Yes'])
					->contain(['Units','Carts'=>
						function($q) use($customer_id){
							return $q->where(['customer_id'=>$customer_id]);
					}]);
				}
				]);
				}]);
				
				$Selling = array("title"=>$HomeScreen->title,'layout'=>$HomeScreen->layout,'category_id'=>$HomeScreen->category_id,'item_id'=>$HomeScreen->item_id,'image'=>$HomeScreen->image,"MostSelling"=>$recently_bought);
				array_push($dynamic,$Selling);
				
			}
			
			if($HomeScreen->layout=='MostView'){
				
				$query=$this->ItemCategories->Items->ItemLedgers->find();
				$popular_items=$query
				->select(['total_rows' => $query->func()->count('ItemLedgers.id'),'item_id'])
				->where(['inventory_transfer'=>'no','status'=>'out'])
				->group(['ItemLedgers.item_id'])
				->order(['total_rows'=>'DESC'])
				->limit(10)
				->contain(['Items'=>function($q) use($customer_id){
				return $q->select(['name', 'image','ready_to_sale','is_new'])

				->contain(['ItemVariations'=>
					function($q) use($customer_id) {
						return $q->where(['ready_to_sale' =>'Yes'])
						->contain(['Units','Carts'=>
							function($q) use($customer_id){
								return $q->where(['customer_id'=>$customer_id]);
						}]);
					}
				]);
				}]);
				
				$PopularItems = array("title"=>$HomeScreen->title,'layout'=>$HomeScreen->layout,'category_id'=>$HomeScreen->category_id,'item_id'=>$HomeScreen->item_id,'image'=>$HomeScreen->image,"PopularItems"=>$popular_items);
				array_push($dynamic,$PopularItems);
				
			}
			
		}
		
		$cart_count = $this->ItemCategories->Carts->find('All')->where(['Carts.customer_id'=>$customer_id])->count();
		
		//-- New Arrival

		//$this->ItemCategories->Items->find()->contain(['ItemVariations']);
		
		/* 

	    $itemCategories = $this->ItemCategories->find('All')->where(['is_deleted'=>0]);
		$itemCategories->select(['image_url' => $itemCategories->func()->concat(['http://healthymaster.in'.$this->request->webroot.'itemcategories/','image' => 'identifier' ])])
                                ->autoFields(true);
		$Category = array("title"=>'SELECT FROM OUR CATEGORY','type'=>'category',"List"=>$itemCategories);
		array_push($dynamic,$Category);
		
	    $banners = $this->ItemCategories->Banners->find('All')->where(['Banners.status'=>'Active']);
		$banners->select(['image_url' => $banners->func()->concat(['http://healthymaster.in'.$this->request->webroot.'banners/','image' => 'identifier' ])])->autoFields(true);

		$query=$this->ItemCategories->Items->ItemLedgers->find();
		$popular_items=$query
			->select(['total_rows' => $query->func()->count('ItemLedgers.id'),'item_id'])
			->where(['inventory_transfer'=>'no','status'=>'out'])
			->group(['ItemLedgers.item_id'])
			->order(['total_rows'=>'DESC'])
			->limit(10)
			->contain(['Items'=>function($q) use($customer_id){
				return $q->select(['name', 'image','ready_to_sale','is_new'])
				
				->contain(['ItemVariations'=>
					function($q) use($customer_id) {
						return $q->where(['ready_to_sale' =>'Yes'])
						->contain(['Units','Carts'=>
							function($q) use($customer_id){
								return $q->where(['customer_id'=>$customer_id]);
						}]);
					}
				]);
			}]);
						
		if(!empty($popular_items->toArray()))
		{
			foreach($popular_items as $popular_item)
			{
				$popular_item->item->image = 'http://healthymaster.in'.$this->request->webroot.'img/item_images/'.$popular_item->item->image;	
			}
		}						
		
		$popular_items_list = array("title"=>'Most Viewed','type'=>'item',"List"=>$popular_items);
		array_push($dynamic,$popular_items_list);
		
		$querys=$this->ItemCategories->Items->ItemLedgers->find();
		$recently_bought=$querys
			->select(['total_rows' => $querys->func()->count('ItemLedgers.id'),'item_id'])
			->where(['inventory_transfer'=>'no','status'=>'out'])
			->group(['ItemLedgers.item_id'])
			->order(['total_rows'=>'DESC'])
			->limit(10)
			->contain(['Items'=>function($q) use($customer_id) {
				return $q->select(['name', 'image','ready_to_sale','is_new'])
				->contain(['ItemVariations'=>
					function($q) use($customer_id) {
						return $q->where(['ready_to_sale' =>'Yes'])
						->contain(['Units','Carts'=>
							function($q) use($customer_id){
								return $q->where(['customer_id'=>$customer_id]);
						}]);
					}
				]);
			}]);
					
		if(!empty($recently_bought->toArray()))
		{
			foreach($recently_bought as $popular_item)
			{
				$popular_item->item->image = 'http://healthymaster.in'.$this->request->webroot.'img/item_images/'.$popular_item->item->image;	
			}
		}

		$top_selling_list = array("title"=>'Most Selling','type'=>'item',"List"=>$recently_bought);
		array_push($dynamic,$top_selling_list);

		$cart_count = $this->ItemCategories->Carts->find('All')->where(['Carts.customer_id'=>$customer_id])->count();	 */					

		$status=true;
		$error="Data found successfully";
        $this->set(compact('status', 'error', 'dynamic','cart_count'));
        $this->set('_serialize', ['status', 'error','cart_count','dynamic']);
    }
	
	public function categoryList()
	{
		$customer_id=$this->request->query('customer_id');
	    $categoryList = $this->ItemCategories->find('All')->contain(['Items'])->where(['ItemCategories.is_deleted'=>0]);
		$categoryList->select(['image_url' => $categoryList->func()->concat(['http://healthymaster.in'.$this->request->webroot.'itemcategories/','image' => 'identifier' ])])->autoFields(true);		
		
		$cart_count = $this->ItemCategories->Carts->find('All')->where(['Carts.customer_id'=>$customer_id])->count();	
		$status=true;
		$error="Category List Successfully";
        $this->set(compact('status', 'error', 'categoryList','cart_count'));
        $this->set('_serialize', ['status', 'error', 'categoryList','cart_count']);		
	}
	
	
	
	
	
	
}