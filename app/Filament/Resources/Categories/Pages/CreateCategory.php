<?php

namespace App\Filament\Resources\Categories\Pages;

use App\Filament\Resources\Categories\CategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        if (($data['addition_type'] ?? 'main') === 'sub_only') {
            $parentCategory = \App\Models\Category::findOrFail($data['parent_category_id']);
            
            if (!empty($data['subCategories'])) {
                foreach ($data['subCategories'] as $subCategoryData) {
                    $parentCategory->subCategories()->create($subCategoryData);
                }
            }
            
            // Return the parent category so Filament redirects to its edit/view page
            return $parentCategory;
        }

        return parent::handleRecordCreation($data);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
