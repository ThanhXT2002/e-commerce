<?php

namespace App\Services;


use App\Services\Interfaces\MenuServiceInterface;
use App\Repositories\Interfaces\MenuRepositoryInterface as MenuRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Classes\Nestedsetbie;
/**
 * Class MenuService
 * @package App\Services
 */
class MenuService extends BaseService implements MenuServiceInterface
{

    protected $menuRepository;
    protected $nestedset;
    

    public function __construct(
        MenuRepository $menuRepository
    ){
        $this->menuRepository = $menuRepository;
    }

    private function initialize($languageId){
        $this->nestedset = new Nestedsetbie([
            'table'=>'menus',
            'foreignkey' => 'menu_id',
            'isMenu' => TRUE,
            'language_id' => $languageId,
        ]);
    }

    

    public function paginate($request){

        // $condition['keyword'] = addslashes($request->input('keyword'));
        // $condition['publish'] = $request->integer('publish');
        // $perPage = $request->integer('perpage');
        // $menus = $this->menuRepository->pagination(
        //     $this->paginateSelect(), 
        //     $condition, 
        //     $perPage, 
        //     ['path' => 'menu/index'], 
        // );
        return [];
    }

    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $payload = $request->only(['menu', 'menu_catalogue_id', 'type']);
            if(count($payload['menu']['name'])){
                foreach ($payload['menu']['name'] as $key => $val) {
                    $menuArray = [
                        'menu_catalogue_id' => $payload['menu_catalogue_id'],
                        'type'=>$payload['type'],
                        'order' => $payload['menu']['order'][$key],
                        'user_id'=> Auth::id(),                    
                    ];
                    $menu = $this->menuRepository->create($menuArray);
                    if($menu->id > 0){
                        $menu->languages()->detach([$languageId, $menu->id]);
                        $payloadLanguage= [
                            'language_id' =>$languageId,
                            'name' =>$val,
                            'canonical' => $payload['menu']['canonical'][$key]
                        ];
                        $this->menuRepository->createPivot($menu,$payloadLanguage,'languages');
                    }
                    
                }
                $this->initialize($languageId);
                $this->nestedset();
            }
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function saveChildren($request, $languageId, $menu){
        DB::beginTransaction();
        try{
            $payload = $request->only(['menu']);
            
            if(count($payload['menu']['name'])){
                foreach ($payload['menu']['name'] as $key => $val) {
                    $menuId = $payload['menu']['id'][$key];
                    $menuArray = [
                        'menu_catalogue_id' => $menu->menu_catalogue_id,
                        'parent_id' => $menu->id,
                        'order' => $payload['menu']['order'][$key],
                        'user_id'=> Auth::id(),                    
                    ];
                    if($menuId == 0){
                        $menuSave = $this->menuRepository->create($menuArray);
                    }else{
                        $menuSave = $this->menuRepository->update($menuId, $menuArray);
                    }
                    if($menuSave->id > 0){
                        $menuSave->languages()->detach([$languageId, $menuSave->id]);
                        $payloadLanguage= [
                            'language_id' =>$languageId,
                            'name' =>$val,
                            'canonical' => $payload['menu']['canonical'][$key]
                        ];
                        $this->menuRepository->createPivot($menuSave,$payloadLanguage,'languages');
                    }
                    
                }
                $this->initialize($languageId);
                $this->nestedset();
            }
            
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function update($id, $request){
        DB::beginTransaction();
        try{

            $payload = $request->except(['_token','send']);
            $menu = $this->menuRepository->update($id, $payload);
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function destroy($id){
        DB::beginTransaction();
        try{
            $menu = $this->menuRepository->delete($id);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function updateStatus($post = []){
        DB::beginTransaction();
        try{
            $payload[$post['field']] = (($post['value'] == 1)?2:1);
            $menu = $this->menuRepository->update($post['modelId'], $payload);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function updateStatusAll($post){
        DB::beginTransaction();
        try{
            $payload[$post['field']] = $post['value'];
            $flag = $this->menuRepository->updateByWhereIn('id', $post['id'], $payload);

            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            echo $e->getMessage();die();
            return false;
        }
    }

    public function getAndConvertMenu($menu = null, $language = 1): array
    {
        $menuList = $this->menuRepository->findByCondition([
            ['parent_id', '=', $menu->id]
        ], TRUE, [
            'languages' => function($query) use ($language) {
                $query->where('language_id', $language);
            }
        ]);

        $temp = [];
        $fields = ['name', 'canonical', 'order', 'id'];

        if (count($menuList)) {
            foreach ($menuList as $key => $val) {
                foreach ($fields as $field) {
                    if ($field == 'name' || $field == 'canonical') {
                        
                            $temp[$field][] = $val->languages->first()->pivot->{$field};
                       
                    } else {
                        
                            $temp[$field][] = $val->{$field};
                      
                    }
                }
            }
        }

        return $temp;
    }

    public function dragUpdate(array $json = [], int $menuCatalogueId = 0, int $languageId = 1 ,$parentId = 0 ){
        if(count($json)){
            foreach($json as $key => $val){
                $update = [
                    'order'=> count($json) - $key,
                    'parent_id' => $parentId,
                ];

                $menu = $this->menuRepository->update($val['id'], $update);
                if(isset($val['children']) && count($val['children'])){
                    $this->dragUpdate($val['children'], $menuCatalogueId, $languageId, $val['id']);
                }
            }
        }
        $this->initialize($languageId);
        $this->nestedset();
    }
    
    
    private function paginateSelect(){
        return [
            'menus.id',
            'menus.publish',
            'menus.image',
            'menus.order',
            'tb2.name',
            'tb2.canonical',
        ];
    }



}