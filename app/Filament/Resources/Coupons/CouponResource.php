<?php

namespace App\Filament\Resources\Coupons;

use App\Filament\Resources\Coupons\Pages\CreateCoupon;
use App\Filament\Resources\Coupons\Pages\EditCoupon;
use App\Filament\Resources\Coupons\Pages\ListCoupons;
use App\Models\Coupon;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CouponResource extends Resource
{
    protected static ?string $model = Coupon::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    public static function getNavigationLabel(): string
    {
        return app()->getLocale() == 'ar' ? 'الكوبونات والخصومات' : 'Coupons & Discounts';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('messages.shop');
    }

    public static function getModelLabel(): string
    {
        return app()->getLocale() == 'ar' ? 'كوبون' : 'Coupon';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() == 'ar' ? 'كوبونات الخصم' : 'Discount Coupons';
    }

    public static function form(Schema $schema): Schema
    {
        $isAr = app()->getLocale() == 'ar';
        return $schema
            ->components([
                \Filament\Schemas\Components\Section::make($isAr ? 'بيانات الكوبون الأساسية' : 'Basic Coupon Data')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('code')
                            ->label($isAr ? 'كود الكوبون (تلقائي رقمي)' : 'Coupon Code (Auto Numeric)')
                            ->default(fn () => (string) rand(10000000, 99999999))
                            ->readOnly()
                            ->required()
                            ->unique(ignoreRecord: true),
                        \Filament\Forms\Components\Select::make('type')
                            ->label($isAr ? 'نوع الخصم' : 'Discount Type')
                            ->options([
                                'fixed' => $isAr ? 'قيمة ثابتة' : 'Fixed Amount',
                                'percentage' => $isAr ? 'نسبة مئوية' : 'Percentage',
                            ])
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('value')
                            ->label($isAr ? 'قيمة الخصم' : 'Discount Value')
                            ->numeric()
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('min_order_value')
                            ->label($isAr ? 'الحد الأدنى لقيمة الطلب' : 'Min Order Value')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make($isAr ? 'تفاصيل الكوبون والترجمة' : 'Coupon Details & Translation')
                    ->schema([
                        \Filament\Forms\Components\TextInput::make('title_ar')
                            ->label($isAr ? 'عنوان الكوبون (بالعربية)' : 'Title (Arabic)')
                            ->required(),
                        \Filament\Forms\Components\TextInput::make('title_en')
                            ->label($isAr ? 'عنوان الكوبون (بالإنجليزية)' : 'Title (English)')
                            ->required(),
                        \Filament\Forms\Components\Textarea::make('description_ar')
                            ->label($isAr ? 'الوصف (بالعربية)' : 'Description (Arabic)')
                            ->rows(3),
                        \Filament\Forms\Components\Textarea::make('description_en')
                            ->label($isAr ? 'الوصف (بالإنجليزية)' : 'Description (English)')
                            ->rows(3),
                    ])->columns(2),

                \Filament\Schemas\Components\Section::make($isAr ? 'شروط وحدود الاستخدام' : 'Usage Terms & Limits')
                    ->schema([
                        \Filament\Forms\Components\Select::make('user_id')
                            ->label($isAr ? 'مخصص لمستخدم معين (اختياري)' : 'Specific User (Optional)')
                            ->relationship('user', 'full_name')
                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name ?? $record->email ?? 'User #' . $record->id)
                            ->searchable()
                            ->preload(),
                        \Filament\Forms\Components\DateTimePicker::make('start_date')
                            ->label($isAr ? 'تاريخ بداية الفعالية' : 'Start Date'),
                        \Filament\Forms\Components\DateTimePicker::make('end_date')
                            ->label($isAr ? 'تاريخ نهاية الفعالية' : 'End Date'),
                        \Filament\Forms\Components\TextInput::make('usage_limit')
                            ->label($isAr ? 'الحد الأقصى للاستخدام الكلي' : 'Total Usage Limit')
                            ->numeric()
                            ->placeholder($isAr ? 'بلا حد أقصى' : 'No Limit'),
                        \Filament\Forms\Components\TextInput::make('user_usage_limit')
                            ->label($isAr ? 'الحد الأقصى للاستخدام لكل مستخدم' : 'Usage Limit Per User')
                            ->numeric()
                            ->default(1)
                            ->required(),
                        \Filament\Forms\Components\Toggle::make('is_active')
                            ->label($isAr ? 'تفعيل الكوبون' : 'Is Active')
                            ->default(true),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isAr = app()->getLocale() == 'ar';
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('code')
                    ->label($isAr ? 'كود الكوبون' : 'Coupon Code')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('title_ar')
                    ->label($isAr ? 'العنوان' : 'Title')
                    ->searchable()
                    ->getStateUsing(fn ($record) => $isAr ? $record->title_ar : $record->title_en),
                \Filament\Tables\Columns\TextColumn::make('type')
                    ->label($isAr ? 'نوع الخصم' : 'Discount Type')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->type === 'fixed' ? ($isAr ? 'قيمة ثابتة' : 'Fixed') : ($isAr ? 'نسبة مئوية' : 'Percentage'))
                    ->color(fn ($record) => $record->type === 'fixed' ? 'success' : 'warning'),
                \Filament\Tables\Columns\TextColumn::make('value')
                    ->label($isAr ? 'قيمة الخصم' : 'Value')
                    ->getStateUsing(fn ($record) => $record->type === 'fixed' ? $record->value . ' EGP' : $record->value . ' %')
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('used_count')
                    ->label($isAr ? 'عدد مرات الاستخدام' : 'Used Times')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                \Filament\Tables\Columns\IconColumn::make('is_active')
                    ->label($isAr ? 'نشط' : 'Active')
                    ->boolean()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('end_date')
                    ->label($isAr ? 'تاريخ الانتهاء' : 'Expiry Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\TernaryFilter::make('is_active')
                    ->label($isAr ? 'حالة الكوبون' : 'Coupon Status')
                    ->trueLabel($isAr ? 'نشط' : 'Active')
                    ->falseLabel($isAr ? 'غير نشط' : 'Inactive'),
            ])
            ->actions([
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCoupons::route('/'),
            'create' => CreateCoupon::route('/create'),
            'edit' => EditCoupon::route('/{record}/edit'),
        ];
    }
}
