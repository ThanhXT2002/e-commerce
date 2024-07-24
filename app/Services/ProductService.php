<?php

namespace App\Services;

use App\Services\Interfaces\ProductServiceInterface;
use App\Services\BaseService;
use App\Repositories\Interfaces\ProductRepositoryInterface as ProductRepository;
use App\Repositories\Interfaces\RouterRepositoryInterface as RouterRepository;
use App\Repositories\Interfaces\ProductVariantLanguageRepositoryInterface as ProductVariantLanguageRepository;
use App\Repositories\Interfaces\ProductVariantAttributeRepositoryInterface as ProductVariantAttributeRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Class ProductService
 * @package App\Services
 */
class ProductService extends BaseService implements ProductServiceInterface
{
    protected $productRepository;
    protected $routerRepository;
    protected $productVariantLanguageRepository;
    protected $productVariantAttributeRepository;
    
    public function __construct(
        ProductRepository $productRepository,
        RouterRepository $routerRepository,
        ProductVariantLanguageRepository $productVariantLanguageRepository,
        ProductVariantAttributeRepository $productVariantAttributeRepository,
    ){
        $this->productRepository = $productRepository;
        $this->routerRepository = $routerRepository;
        $this->productVariantLanguageRepository = $productVariantLanguageRepository;
        $this->productVariantAttributeRepository = $productVariantAttributeRepository;
        $this->controllerName = 'ProductController';
    }

    public function paginate($request, $languageId){
        $perPage = $request->integer('perpage');
        $condition = [
            'keyword' => addslashes($request->input('keyword')),
            'publish' => $request->integer('publish'),
            'where' => [
                ['tb2.language_id', '=', $languageId],
            ],
        ];
        $paginationConfig = [
            'path' => 'product.index', 
            'groupBy' => $this->paginateSelect()
        ];
        $orderBy = ['products.id', 'DESC'];
        $relations = ['product_catalogues'];
        $rawQuery = $this->whereRaw($request, $languageId);
        // dd($rawQuery);
        $joins = [
            ['product_language as tb2', 'tb2.product_id', '=', 'products.id'],
            ['product_catalogue_product as tb3', 'products.id', '=', 'tb3.product_id'],
        ];

        $products = $this->productRepository->pagination(
            $this->paginateSelect(), 
            $condition, 
            $perPage,
            $paginationConfig,  
            $orderBy,
            $joins,  
            $relations,
            $rawQuery
        ); 
        return $products;
    }

    public function create($request, $languageId){
        DB::beginTransaction();
        try{
            $product = $this->createProduct($request);
            if($product->id > 0){
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->updateCatalogueForProduct($product, $request);
                $this->createRouter($product, $request, $this->controllerName,$languageId);

                $this->createVariant($product, $request, $languageId);
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

    public function update($id, $request, $languageId){
        DB::beginTransaction();
        try{
            $product = $this->productRepository->findById($id);
            if($this->uploadProduct($product, $request)){
                $this->updateLanguageForProduct($product, $request, $languageId);
                $this->updateCatalogueForProduct($product, $request);
                $this->updateRouter(
                    $product, $request, $this->controllerName, $languageId
                );
                $product->product_variants()->each(function($variant){
                    $variant->languages()->detach();
                    $variant->attributes()->detach();
                    $variant->delete();
                });
                $this->createVariant($product, $request, $languageId);
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

    public function destroy($id){
        DB::beginTransaction();
        try{
            $product = $this->productRepository->delete($id);
            $this->routerRepository->forceDeleteByCondition([
                ['module_id', '=', $id],
                ['controllers', '=', 'App\Http\Controllers\Frontend\ProductController'],
            ]);

            
            DB::commit();
            return true;
        }catch(\Exception $e ){
            DB::rollBack();
            // Log::error($e->getMessage());
            // echo $e->getMessage();die();
            return false;
        }
    }



    private function createVariant($product, $request, $languageId){
        $payload = $request->only(['variant', 'productVariant', 'attribute']);
        $variant = $this->createVariantArray($payload);
        
        $variants = $product->product_variants()->createMany($variant);
        $variantsId = $variants->pluck('id');
        $productVariantLanguage = [];
        $variantAttribute = [];
        $attributeCombines = $this->combineAttribute(array_values($payload['attribute']));
        if(count($variantsId)){
            foreach ($variantsId as $key => $val) {
                $productVariantLanguage[] = [
                    'product_variant_id' => $val,
                    'language_id' => $languageId,
                    'name' => $payload['productVariant']['name'][$key]
                ];

                if(count($attributeCombines)){
                    foreach ($attributeCombines[$key] as $attributeId) {
                        $variantAttribute[]= [
                            'product_variant_id' => $val,
                            'attribute_id' =>$attributeId
                        ];
                    }
                }
            }
        }
        $variantLanguage = $this->productVariantLanguageRepository->createBatch($productVariantLanguage);
        $variantAttribute = $this->productVariantAttributeRepository->createBatch($variantAttribute);    
    }

    private function combineAttribute($attributes = [], $index = 0) {
        if ($index === count($attributes)) return [[]];
        $subCombines = $this->combineAttribute($attributes, $index + 1); // Sửa lại từ $arributes thành $attributes
        $combines = [];
        foreach ($attributes[$index] as $key => $val) {
            foreach ($subCombines as $keySub => $valSub) {
                $combines[] = array_merge([$val], $valSub);
            }
        }
        return $combines;
    }
    
    private function createVariantArray(array $payload = []):array{
        $variant = [];
        if(isset($payload['variant']['sku']) && count($payload['variant']['sku'])){
           foreach ($payload['variant']['sku'] as $key => $val) {
               $variant[] = [
                   'code' =>($payload['productVariant']['id'][$key]) ?? '',
                   'quantity' =>($payload['variant']['quantity'][$key]) ?? '',
                   'sku' =>$val,
                   'price' =>($payload['variant']['price'][$key]) ? $this->convert_price($payload['variant']['price'][$key]) : '',
                   'barcode' =>($payload['variant']['barcode'][$key]) ?? '',
                   'file_name' =>($payload['variant']['file_name'][$key]) ?? '',
                   'file_url' =>($payload['variant']['file_url'][$key]) ?? '',
                   'album' =>($payload['variant']['album'][$key]) ?? '',
                   'user_id' => Auth::id(),
               ];
           }
        }
        return $variant;
    }

    private function convert_price($inputPrice)
    {
        // Loại bỏ dấu chấm trong chuỗi giá nhập vào
        $cleanedPrice = str_replace('.', '', $inputPrice);

        // Chuyển chuỗi thành số nguyên
        $price = (int) $cleanedPrice;

        return $price;
    }
    private function createProduct($request){
        $payload = $request->only($this->payload());
        
        $payload['user_id'] = Auth::id();
        $payload['album'] = $this->formatAlbum($request);
        $payload['price'] = $this->convert_price($payload['price']);
        $payload['attributeCatalogue'] = $this->formatJson($request,'attributeCatalogue');
        $payload['attribute'] = $this->formatJson($request,'attribute');
        $payload['variant'] = $this->formatJson($request,'variant');
        $product = $this->productRepository->create($payload);
        return $product;
    }

    private function uploadProduct($product, $request){
        $payload = $request->only($this->payload());
        $payload['album'] = $this->formatAlbum($request);
        $payload['price'] = $this->convert_price($payload['price']);
        $payload['attributeCatalogue'] = $this->formatJson($request,'attributeCatalogue');
        $payload['attribute'] = $this->formatJson($request,'attribute');
        $payload['variant'] = $this->formatJson($request,'variant');
        return $this->productRepository->update($product->id, $payload);
    }

    private function updateLanguageForProduct($product, $request, $languageId){
        $payload = $request->only($this->payloadLanguage());
        $payload = $this->formatLanguagePayload($payload, $product->id, $languageId);
        $product->languages()->detach([$languageId, $product->id]);
        return $this->productRepository->createPivot($product, $payload, 'languages');
    }

    private function updateCatalogueForProduct($product, $request){
        $product->product_catalogues()->sync($this->catalogue($request));
    }

    private function formatLanguagePayload($payload, $productId, $languageId){
        $payload['canonical'] = Str::slug($payload['canonical']);
        $payload['language_id'] =  $languageId;
        $payload['product_id'] = $productId;
        return $payload;
    }


    private function catalogue($request){
        if($request->input('catalogue') != null){
            return array_unique(array_merge($request->input('catalogue'), [$request->product_catalogue_id]));
        }
        return [$request->product_catalogue_id];
    }
    
    

    private function whereRaw($request, $languageId){
        $rawCondition = [];
        if($request->integer('product_catalogue_id') > 0){
            $rawCondition['whereRaw'] =  [
                [
                    'tb3.product_catalogue_id IN (
                        SELECT id
                        FROM product_catalogues
                        JOIN product_catalogue_language ON product_catalogues.id = product_catalogue_language.product_catalogue_id
                        WHERE lft >= (SELECT lft FROM product_catalogues as pc WHERE pc.id = ?)
                        AND rgt <= (SELECT rgt FROM product_catalogues as pc WHERE pc.id = ?)
                        AND product_catalogue_language.language_id = '.$languageId.'
                    )',
                    [$request->integer('product_catalogue_id'), $request->integer('product_catalogue_id')]
                ]
            ];
            
        }
        return $rawCondition;
    }

    private function paginateSelect(){
        return [
            'products.id', 
            'products.publish',
            'products.image',
            'products.order',
            'tb2.name', 
            'tb2.canonical',
        ];
    }

    private function payload(){
        return [
            'follow',
            'publish',
            'image',
            'album',
            'product_catalogue_id',
            'price',
            'made_in',
            'code',
            'user_id',
            'attributeCatalogue',
            'attribute',
            'variant'
        ];
    }

    private function payloadLanguage(){
        return [
            'name',
            'description',
            'content',
            'meta_title',
            'meta_keyword',
            'meta_description',
            'canonical'
        ];
    }


}
