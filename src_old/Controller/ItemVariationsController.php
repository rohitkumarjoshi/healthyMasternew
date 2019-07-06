<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * ItemVariations Controller
 *
 * @property \App\Model\Table\ItemVariationsTable $ItemVariations
 *
 * @method \App\Model\Entity\ItemVariation[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class ItemVariationsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */

    public function defineSaleRate()
    {
        $this->viewBuilder()->layout('index_layout');
       $item_variations=[];
        $itemvariation = $this->ItemVariations->newEntity();
           $item_category_id=$this->request->query('item_category_id');
            if($item_category_id!=null)
            {

                $cat=$this->ItemVariations->Items->find()
                ->select('id')
                ->where(['item_category_id'=>$item_category_id]);
                // pr($cat->toArray());exit;

                foreach ($cat as $cats) {

                    $item_variations = $this->ItemVariations->find()
                    ->where(['item_id'=>$cats->id])
                    ->contain(['Items'=>['ItemCategories'],'Units']);
                
            } 
           // pr($item_variations->toArray());exit;
         }
         else
         {
             $item_variations = $this->ItemVariations->find()
        ->contain(['Items'=>['ItemCategories'],'Units']);
         }
        if ($this->request->is(['post', 'put'])) {
            $item_variation=$this->request->getData();
            //pr($item_variation);exit;
            foreach($item_variation['itemVariations'] as $itemVariations){
                //pr($itemVariations['print_rate']);exit;
                $itemVariation=(object)$itemVariations;
                $query = $this->ItemVariations->query();
                    //$query->update(['promote_date', 'due_amount', amount', 'discount', 'end_date'])
                    $query->update()
                            ->set([
                            'print_rate' => $itemVariations['print_rate'],
                            'ready_to_sale' => $itemVariations['ready_to_sale'],
                            'discount_per' => $itemVariations['discount_per'],
                            'sales_rate' => $itemVariations['sales_rate']
                            ])
                            ->where(['id'=>$itemVariations['id']])
                    ->execute();
            }

            
            $this->Flash->success(__('Item rates have updated successfully.'));
         }
       
        $category=$this->ItemVariations->Items->ItemCategories->find('list');
        //pr($item_variations->toArray());exit;
        $this->set(compact('item_variations','itemvariation', 'itemCategories', 'units','category'));
        $this->set('_serialize', ['items']);
    }

    public function index()
    {
        $this->paginate = [
            'contain' => ['Items', 'Units']
        ];
        $itemVariations = $this->paginate($this->ItemVariations);

        $this->set(compact('itemVariations'));
    }

    /**
     * View method
     *
     * @param string|null $id Item Variation id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $itemVariation = $this->ItemVariations->get($id, [
            'contain' => ['Items', 'Units']
        ]);

        $this->set('itemVariation', $itemVariation);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $itemVariation = $this->ItemVariations->newEntity();
        if ($this->request->is('post')) {
            $itemVariation = $this->ItemVariations->patchEntity($itemVariation, $this->request->getData());
            if ($this->ItemVariations->save($itemVariation)) {
                $this->Flash->success(__('The item variation has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The item variation could not be saved. Please, try again.'));
        }
        $items = $this->ItemVariations->Items->find('list', ['limit' => 200]);
        $units = $this->ItemVariations->Units->find('list', ['limit' => 200]);
        $this->set(compact('itemVariation', 'items', 'units'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Item Variation id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $itemVariation = $this->ItemVariations->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $itemVariation = $this->ItemVariations->patchEntity($itemVariation, $this->request->getData());
            if ($this->ItemVariations->save($itemVariation)) {
                $this->Flash->success(__('The item variation has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The item variation could not be saved. Please, try again.'));
        }
        $items = $this->ItemVariations->Items->find('list', ['limit' => 200]);
        $units = $this->ItemVariations->Units->find('list', ['limit' => 200]);
        $this->set(compact('itemVariation', 'items', 'units'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Item Variation id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    // public function delete($id = null)
    // {
    //     $this->request->allowMethod(['post', 'delete']);
    //     $itemVariation = $this->ItemVariations->get($id);
    //     if ($this->ItemVariations->delete($itemVariation)) {
    //         $this->Flash->success(__('The item variation has been deleted.'));
    //     } else {
    //         $this->Flash->error(__('The item variation could not be deleted. Please, try again.'));
    //     }

    //     return $this->redirect(['action' => 'index']);
    // }
    public function delete()
    {
        //pr("fdvfv");exit;
        if($this->request->is(['post']))
        {
            $success = 0;
            $id = $this->request->getData('id');
            pr($id);exit;
            $itemVariation = $this->ItemVariations->get($id);
            if ($this->ItemVariations->delete($itemVariation)) {
               $success = 1;
            }
        }
        $this->set(compact('success','response'));
        $this->set('_serialize',['success','response']);
    }
}