<?php

namespace App\Services\API\General;

use App\Http\Resources\API\ProductListResource;
use App\Http\Resources\API\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponse;

class ProductService
{
    use ApiResponse;
    public function getProducts()
    {
        $products = Product::with(['offers','reviews','images','specifications'])->paginate(10);
        if($products->isEmpty()){
            return [
                'status' => false,
                'message' => __('messages.products_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.products_retrieved_successfully'),
            'data' => ProductResource::collection($products)
        ];
    }
    public function getProduct($data)
    {
        $product = Product::with(['specifications','images','subCategory','brand','offers','reviews.user','provider.user'])->find($data['product_id']);
        if(!$product){
            return [
                'status' => false,
                'message' => __('messages.product_not_found'),
                'data' => []
            ];
        }
        return [
            'status' => true,
            'message' => __('messages.product_retrieved_successfully'),
            'data' => new ProductListResource($product)
        ];
    }

    public function filter(array $filters = [])
    {
        $query = Product::with(['offers', 'images', 'reviews', 'specifications']);

        // 1. الفلترة بالتصنيف الفرعي (SubCategory Filter)
        if (!empty($filters['category_id'])) {
            $query->where('sub_category_id', $filters['category_id']);
        }

        // 2. الفلترة بالسعر (Price Range)
        if (isset($filters['min_price']) || isset($filters['max_price'])) {
            $min = $filters['min_price'] ?? 0;
            $max = $filters['max_price'] ?? 999999;
            $query->whereBetween('price', [$min, $max]);
        }

        // 3. عروض خاصة (Special Offers)
        if (!empty($filters['special_offers'])) {
            $query->whereHas('offers', function ($q) {
                $q->where('is_active', true)
                  ->where(function($sq){
                      $sq->whereNull('end_date')->orWhere('end_date', '>=', now());
                  });
            });
        }

        // 4. التقييمات (Ratings)
        if (!empty($filters['ratings'])) {
            $query->withAvg('reviews', 'rating')
                ->having('reviews_avg_rating', '>=', $filters['ratings']);
        }

        // 5. الترتيب (Sorting)
        $sort = $filters['sort'] ?? 'latest';

        switch ($sort) {
            case 'min_price':
                $query->orderBy('price', 'asc');
                break;
            case 'max_price':
                $query->orderBy('price', 'desc');
                break;

            case 'offers':
                // ترتيب المنتجات التي لديها عروض نشطة لتظهر في البداية
                $query->leftJoin('offers', function($join) {
                        $join->on('products.id', '=', 'offers.product_id')
                            ->where('offers.is_active', true)
                            ->where(function($q){
                                $q->whereNull('offers.end_date')->orWhere('offers.end_date', '>=', now());
                            });
                    })
                    ->select('products.*')
                    ->orderByRaw('CASE WHEN offers.id IS NOT NULL THEN 0 ELSE 1 END');
                break;

            case 'latest':
            default:
                $query->orderByDesc('id');
                break;
        }

        $products = $query->paginate(10);

        if ($products->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.products_not_found'),
                'data' => [],
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.products_retrieved_successfully'),
            'data' => $products, // Will be wrapped in Controller if needed, or use Resource here
        ];
    }

    public function searchProducts($searchTerm)
    {
        $locale = app()->getLocale();
        $products = Product::with(['offers', 'reviews'])
            ->where(function($query) use ($searchTerm, $locale) {
                $query->where("name_{$locale}", 'LIKE', '%' . $searchTerm . '%')
                      ->orWhere("description_{$locale}", 'LIKE', '%' . $searchTerm . '%');
            })
            ->paginate(10);

        if ($products->isEmpty()) {
            return [
                'status' => false,
                'message' => __('messages.products_not_found'),
                'data' => [],
            ];
        }

        return [
            'status' => true,
            'message' => __('messages.products_retrieved_successfully'),
            'data' => $products,
        ];
    }

    public function getProviderProducts()
    {
        $user = auth()->user();
        $provider = \App\Models\Provider::where('user_id', $user->id)->first();
        if (!$provider) {
            return [
                'status' => false,
                'message' => __('messages.provider_not_found'),
                'data' => []
            ];
        }

        $products = Product::where('provider_id', $provider->id)->with(['offers', 'reviews', 'images', 'specifications', 'provider.user'])->paginate(10);
        
        return [
            'status' => true,
            'message' => __('messages.products_retrieved_successfully'),
            'data' => $products
        ];
    }

    public function createProduct(array $data)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $user = auth()->user();

            if ($user->type != 'service_provider') {
                return [
                    'status' => false,
                    'message' => __('messages.unauthorized_provider'),
                    'data' => []
                ];
            }

            $provider = \App\Models\Provider::where('user_id', $user->id)->first();
            if (!$provider) {
                return [
                    'status' => false,
                    'message' => __('messages.provider_not_found'),
                    'data' => []
                ];
            }

            // Handle main image
            $imageName = null;
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $imageName = time() . '_' . uniqid() . '.' . $data['image']->getClientOriginalExtension();
                $data['image']->move(public_path('storage/products'), $imageName);
            }

            $product = Product::create([
                'provider_id' => $provider->id,
                'sub_category_id' => $data['sub_category_id'],
                'brand_id' => $data['brand_id'] ?? null,
                'name_ar' => $data['name_ar'],
                'name_en' => $data['name_en'] ?? null,
                'description_ar' => $data['description_ar'],
                'description_en' => $data['description_en'] ?? null,
                'price' => $data['price'],
                'stock' => $data['stock'],
                'image' => $imageName,
                'status' => $data['status'] ?? 'active',
                'is_featured' => $data['is_featured'] ?? false,
            ]);

            // Handle gallery images
            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $img) {
                    if ($img instanceof \Illuminate\Http\UploadedFile) {
                        $galleryImageName = time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
                        $img->move(public_path('storage/products/images'), $galleryImageName);
                        
                        \App\Models\ProductImage::create([
                            'product_id' => $product->id,
                            'images' => $galleryImageName,
                        ]);
                    }
                }
            }

            // Handle specifications
            if (isset($data['specifications']) && is_array($data['specifications'])) {
                foreach ($data['specifications'] as $spec) {
                    $iconName = null;
                    if (isset($spec['icon']) && $spec['icon'] instanceof \Illuminate\Http\UploadedFile) {
                        $iconName = time() . '_' . uniqid() . '.' . $spec['icon']->getClientOriginalExtension();
                        $spec['icon']->move(public_path('storage/specifications'), $iconName);
                    }
                    \App\Models\Specification::create([
                        'product_id' => $product->id,
                        'key_ar'     => $spec['key_ar'],
                        'key_en'     => $spec['key_en'],
                        'value_ar'   => $spec['value_ar'],
                        'value_en'   => $spec['value_en'],
                        'icon'       => $iconName,
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            $product->load(['images', 'subCategory', 'brand', 'specifications']);

            return [
                'status' => true,
                'message' => __('messages.product_created_successfully'),
                'data' => new ProductListResource($product)
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function updateProduct(array $data)
    {
        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $user = auth()->user();
            $provider = \App\Models\Provider::where('user_id', $user->id)->first();
            if (!$provider) {
                return [
                    'status' => false,
                    'message' => __('messages.provider_not_found'),
                    'data' => []
                ];
            }

            $product = Product::where('id', $data['product_id'])
                              ->where('provider_id', $provider->id)
                              ->first();

            if (!$product) {
                return [
                    'status' => false,
                    'message' => __('messages.product_not_found'),
                    'data' => []
                ];
            }

            // Update main fields
            $fields = ['sub_category_id', 'brand_id', 'name_ar', 'name_en', 'description_ar', 'description_en', 'price', 'stock', 'status', 'is_featured'];
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $product->$field = $data[$field];
                }
            }

            // Handle main image update
            if (isset($data['image']) && $data['image'] instanceof \Illuminate\Http\UploadedFile) {
                $imageName = time() . '_' . uniqid() . '.' . $data['image']->getClientOriginalExtension();
                $data['image']->move(public_path('storage/products'), $imageName);
                $product->image = $imageName;
            }

            $product->save();

            // Handle gallery images (Add new ones)
            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $img) {
                    if ($img instanceof \Illuminate\Http\UploadedFile) {
                        $galleryImageName = time() . '_' . uniqid() . '.' . $img->getClientOriginalExtension();
                        $img->move(public_path('storage/products/images'), $galleryImageName);
                        
                        \App\Models\ProductImage::create([
                            'product_id' => $product->id,
                            'images' => $galleryImageName,
                        ]);
                    }
                }
            }

            // Handle specifications update (if provided)
            if (isset($data['specifications']) && is_array($data['specifications'])) {
                \App\Models\Specification::where('product_id', $product->id)->delete();
                foreach ($data['specifications'] as $spec) {
                    $iconName = null;
                    if (isset($spec['icon']) && $spec['icon'] instanceof \Illuminate\Http\UploadedFile) {
                        $iconName = time() . '_' . uniqid() . '.' . $spec['icon']->getClientOriginalExtension();
                        $spec['icon']->move(public_path('storage/specifications'), $iconName);
                    }
                    \App\Models\Specification::create([
                        'product_id' => $product->id,
                        'key_ar'     => $spec['key_ar'],
                        'key_en'     => $spec['key_en'],
                        'value_ar'   => $spec['value_ar'],
                        'value_en'   => $spec['value_en'],
                        'icon'       => $iconName,
                    ]);
                }
            }

            \Illuminate\Support\Facades\DB::commit();
            $product->load(['images', 'subCategory', 'brand', 'specifications']);

            return [
                'status' => true,
                'message' => __('messages.product_updated_successfully'),
                'data' => new ProductListResource($product)
            ];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function deleteProduct($product_id)
    {
        try {
            $user = auth()->user();
            $provider = \App\Models\Provider::where('user_id', $user->id)->first();
            if (!$provider) {
                return [
                    'status' => false,
                    'message' => __('messages.provider_not_found'),
                    'data' => []
                ];
            }

            $product = Product::where('id', $product_id)
                              ->where('provider_id', $provider->id)
                              ->first();

            if (!$product) {
                return [
                    'status' => false,
                    'message' => __('messages.product_not_found'),
                    'data' => []
                ];
            }

            $product->delete();

            return [
                'status' => true,
                'message' => __('messages.product_deleted_successfully'),
                'data' => []
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }
}
