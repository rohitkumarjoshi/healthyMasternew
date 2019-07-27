<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Items Controller
 *
 * @property \App\Model\Table\ItemsTable $Items
 *
 * @method \App\Model\Entity\Item[] paginate($object = null, array $settings = [])
 */
class ItemsController extends AppController
{


    /**
     * Index method
     *
     * @return \Cake\Http\Response|null
     */
  public function check()
    {
        //$items='0';
         $code=$this->request->getData('input'); 
         //alert($mobile);
            if($this->Items->exists(['item_code'=>$code]))
            {
                echo"1";
            }
    
        exit;  
    }

    public function gstReport()
    {
       $this->viewBuilder()->layout('index_layout');
       $gsts=$this->Items->ItemVariations->find()
       ->group(['item_id'])
       ->contain(['Items'=>['GstFigures','ItemCategories']]);
        $this->set(compact('gsts'));
    }

    public function index()
    {
        //$paginate=['limit'=>20];
		$this->viewBuilder()->layout('index_layout');
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
        $items = $this->Items->find()->contain(['ItemCategories']);

        //pr($items->toArray());exit;
		
        $items = $this->Items->find()->contain(['ItemCategories','ItemVariations']);
		
		
		
        $this->set(compact('items', 'status'));
        $this->set('_serialize', ['items', 'status']);
    }

	public function defineSaleRate()
    {
		$this->viewBuilder()->layout('index_layout');
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
		$items = $this->Items->newEntity();
		
		if ($this->request->is(['post', 'put'])) {
			$items=$this->request->getData('items');
			
			foreach($items as $item){
				$item=(object)$item;
				$query = $this->Items->query();
                    //$query->update(['promote_date', 'due_amount', amount', 'discount', 'end_date'])
                    $query->update()
                            ->set([
                            'print_rate' => $item->print_rate,
                            'ready_to_sale' => $item->ready_to_sale,
                            'discount_per' => $item->discount_per,
                            'sales_rate' => $item->sales_rate,
                            'offline_sales_rate' => $item->offline_sales_rate
                            ])
                            ->where(['id'=>$item->item_id])
                    ->execute();
			}
			$this->Flash->success(__('Item rates have updated successfully.'));
		 }
		$items = $this->Items->find()->where(['Items.freeze'=>0])
        //->group(['ItemVariations.quantity_variation'])
        ->contain(['ItemCategories','ItemVariations']);
		//pr($items->toArray());exit;
		$this->set(compact('items', 'itemCategories', 'units'));
        $this->set('_serialize', ['items']);
    }

    /**
     * View method
     *
     * @param string|null $id Item id.
     * @return \Cake\Http\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $item = $this->Items->get($id, [
            'contain' => ['ItemCategories', 'Units']
        ]);

        $this->set('item', $item);
        $this->set('_serialize', ['item']);
    }
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
		$this->viewBuilder()->layout('index_layout');
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
        $item = $this->Items->newEntity();
        if ($this->request->is('post')) {
			
			/* $query = $this->Items->find();
			$item_codes=$query->select(['max_value' => $query->func()->max('item_code')])->toArray();
			$item_code=$item_codes[0]->max_value+1;
		    $this->request->data['item_code']=$item_code; */
			
			$item_keywords=$this->request->data['item_keyword'];
          
            $data=$this->request->data;
			$item = $this->Items->patchEntity($item,$data,['associated'=>['ItemVariations']]);
            //pr($item->toArray());exit;

			$file = $this->request->data['image'];
            //pr($file);
			$file_name=$file['name'];		
            //pr($file_name);exit;	
			$ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
            $arr_ext = array('jpg', 'jpeg', 'png'); //set allowed extensions
            $setNewFileName = uniqid();
            $img_name= $setNewFileName.'.'.$ext;
			if(!empty($file_name)){
				$item->image=$img_name;
			}if(empty($file_name)){
				
			}
			
			if ($this->Items->save($item))
			{
				if(!empty($item_keywords)){
					
					foreach($item_keywords as $item_keyword){
					 $ItemRows=$this->Items->ItemRows->newEntity();
					 $ItemRows->item_id=$item->id;
					 $ItemRows->item_category_id=$item_keyword;
					 $ItemRows->status=0;
					 $this->Items->ItemRows->save($ItemRows);	
					}
				}

                foreach($item->item_variations as $variation)
                {
					if(($variation->opening_stock != 0 )|| ($variation->opening_stock != null))
                    {  
                        $query = $this->Items->ItemLedgers->query();
                        $query->insert(['jain_thela_admin_id', 'driver_id', 'grn_id', 'item_id', 'warehouse_id', 'purchase_booking_id', 'rate', 'amount', 'status', 'quantity','unit_variation_id','rate_updated', 'transaction_date','item_variation_id'])
                        ->values([
                            'jain_thela_admin_id' => 1,
                            'driver_id' => 0,
                            //'grn_id' => $grn_id,
                            'item_id' => $item->id,
                            'warehouse_id' => 1,
                            'purchase_booking_id' => 0,
                            'rate' => $variation->sales_rate,
                            'item_variation_id' => $variation->id,
                            'amount' => 0,
                            'status' => 'In',
                            'quantity' => $variation->opening_stock,
                            'unit_variation_id' => $variation->unit_variation_id,
                            'rate_updated' => 'ok',
                            'transaction_date'=>date('Y-m-d')
                        ]);
                        $query->execute();
						
                      }
					  $UnitVariationdata = $this->Items->ItemVariations->UnitVariations->find()->where(['id'=>$variation->unit_variation_id])->first();
					  $query = $this->Items->ItemVariations->query();
						$query->update()
                            ->set([
                            'quantity_variation' => $UnitVariationdata->name,'unit_variation_id' => $variation->unit_variation_id])
                            ->where(['id'=>$variation->id])
						->execute();
					  
                }

				//pr($item);exit;
                $this->Flash->success(__('The item has been saved.'));
				  if (in_array($ext, $arr_ext)) {
                         $destination_url = WWW_ROOT . 'img/temp/'.$img_name;
                        if($ext=='png'){
                        $image = imagecreatefrompng($file['tmp_name']);
                        }else{
                        $image = imagecreatefromjpeg($file['tmp_name']);
                        }
                        imagejpeg($image, $destination_url, 95);
                        $keyname1 = 'item_images/'.$img_name;
                        $this->AwsFile->putObjectFile($keyname1,$destination_url,$file['type']);
            
                    //move_uploaded_file($file['tmp_name'], WWW_ROOT . 'img/item_images/'.$img_name);
                  }
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The item could not be saved. Please, try again.'));
        }
		
        $Keyword_itemCategories= $this->Items->ItemCategories->find()->where(['ItemCategories.id'=>2])
		->contain(['ChildItemCategories'])->first();
		foreach($Keyword_itemCategories->child_item_categories as $data){
			$keywords[$data->id]=$data->name;
		}
	  // pr($Keyword_itemCategories); exit;
		 $GstFigures= $this->Items->GstFigures->find('list');
		
		$itemCategories = $this->Items->ItemCategories->find('list')->where(['is_deleted'=>0,'parent_id !='=>'2'])->orwhere(['is_deleted'=>0,'parent_id IS'=>NULL]);
		//pr($itemCategories->toArray()); exit;
        $units = $this->Items->ItemVariations->Units->find()->where(['is_deleted'=>0]);
        $UnitVariations = $this->Items->ItemVariations->UnitVariations->find('list');
        $item_fetchs = $this->Items->find('list')->where(['freeze'=>0]);
		foreach($units as $unit_data){
			$unit_name=$unit_data->unit_name;
			$unit_option[]= ['value'=>$unit_data->id,'text'=>$unit_data->shortname,'unit_name'=>$unit_name];
		}
        $this->set(compact('item', 'itemCategories', 'units', 'unit_option', 'item_fetchs','GstFigures','keywords','UnitVariations'));
        $this->set('_serialize', ['item']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Item id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
		$this->viewBuilder()->layout('index_layout');
		$jain_thela_admin_id=$this->Auth->User('jain_thela_admin_id');
        $item = $this->Items->get($id,  [
            'contain' => ['ItemVariations','ItemRows']]
        );
		$old_image_name=$item->image;
        if ($this->request->is(['patch', 'post', 'put'])) {
			

            //$button_value=$this->request->data['button_value'];

			$item_keywords=$this->request->data['item_keyword'];
			
			$file = $this->request->data['image'];	
			$file_name=$file['name'];
			$ext = substr(strtolower(strrchr($file['name'], '.')), 1); //get the extension
            $arr_ext = array('jpg', 'jpeg', 'png'); //set allowed extensions
            $setNewFileName = uniqid();	
			$img_name= $setNewFileName.'.'.$ext;
			if(!empty($file_name)){
			$this->request->data['image']=$img_name;
			}if(empty($file_name)){
				$this->request->data['image']=$old_image_name;
			}
            $items = $this->Items->get($id,  [
            'contain' => ['ItemVariations']]
        );
            $item = $this->Items->patchEntity($items, $this->request->getData());
            $item->updated_on=date('Y-m-d');
            $item->jain_thela_admin_id=$jain_thela_admin_id;
			if ($this->Items->save($item)) {
				
				if(!empty($item_keywords)){
					$ItemRowsdatas=$this->Items->ItemRows->find()->where(['item_id'=>$item->id]);
					foreach($ItemRowsdatas as $ItemRowsdata){
						$delItemRows=$this->Items->ItemRows->get($ItemRowsdata->id);
						$this->Items->ItemRows->delete($delItemRows);
					}
					
					foreach($item_keywords as $item_keyword){
					 $ItemRows=$this->Items->ItemRows->newEntity();
					 $ItemRows->item_id=$item->id;
					 $ItemRows->item_category_id=$item_keyword;
					 $ItemRows->status=0;
					 $this->Items->ItemRows->save($ItemRows);	
					
					}
				}
				foreach($item->item_variations as $variation)
                {
					$UnitVariationdata = $this->Items->ItemVariations->UnitVariations->find()->where(['id'=>$variation->unit_variation_id])->first();
					  $query = $this->Items->ItemVariations->query();
						$query->update()
                            ->set([
                            'quantity_variation' => $UnitVariationdata->name,'unit_variation_id' => $variation->unit_variation_id])
                            ->where(['id'=>$variation->id])
						->execute();
				}
				
				
				if(!empty($file_name)){
					 if (in_array($ext, $arr_ext)) {
                         $destination_url = WWW_ROOT . 'img/temp/'.$img_name;
                        if($ext=='png'){
                        $image = imagecreatefrompng($file['tmp_name']);
                        }else{
                        $image = imagecreatefromjpeg($file['tmp_name']);
                        }
                        imagejpeg($image, $destination_url, 95);
                        $keyname1 = 'item_images/'.$img_name;
                        $this->AwsFile->putObjectFile($keyname1,$destination_url,$file['type']);
            
                    //move_uploaded_file($file['tmp_name'], WWW_ROOT . 'img/item_images/'.$img_name);
                  } 
				}
                $this->Flash->success(__('The item has been saved.'));	
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The item could not be saved. Please, try again.'));
        }
		$Keyword_itemCategories= $this->Items->ItemCategories->find()->where(['ItemCategories.id'=>2])
		->contain(['ChildItemCategories'])->first();
		foreach($Keyword_itemCategories->child_item_categories as $data){
			$keywords[$data->id]=$data->name;
		}
		$GstFigures= $this->Items->GstFigures->find('list');
		
		$itemCategories = $this->Items->ItemCategories->find('list', ['limit' => 200]);
		$units = $this->Items->ItemVariations->Units->find('list')->where(['is_deleted'=>0]);
		$item_fetchs = $this->Items->find('list')->where(['is_virtual'=> 'no','freeze'=>0]);
		// foreach($units as $unit_data){
		// 	$unit_name=$unit_data->unit_name;
		// 	$unit_option[]= ['value'=>$unit_data->id,'text'=>$unit_data->shortname,'unit_name'=>$unit_name];
		// }
        $variations = $this->Items->ItemVariations->find()->where(['item_id'=>$id])->contain(['UnitVariations','Items']);
		//pr($variations->toArray()); exit;
		$UnitVariations = $this->Items->ItemVariations->UnitVariations->find('list');
        $this->set(compact('item', 'itemCategories', 'units','item_fetchs','variations','GstFigures','keywords','UnitVariations'));
        $this->set('_serialize', ['item']);
    }

    /**
     * @param string|null $id Item id.
     * @return \Cake\Http\Response|null Redirects to index.
     *fsf
     *  ete method
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $item = $this->Items->get($id);
		    $item->freeze=1;
        if ($this->Items->save($item)) {
            $this->Flash->success(__('The item is made Deactive.'));
        } else {
            $this->Flash->error(__('The item could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
    public function delete1($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $item = $this->Items->get($id);
        $item->freeze=0;
        if ($this->Items->save($item)) {
            $this->Flash->success(__('The item is made Active.'));
        } else {
            $this->Flash->error(__('The item could not be unfreeze. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
