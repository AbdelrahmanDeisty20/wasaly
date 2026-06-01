<?php

namespace App\Filament\Resources\Contacts;

use App\Filament\Resources\Contacts\Pages\ListContacts;
use App\Filament\Resources\Contacts\Pages\ViewContact;
use App\Models\Contact;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    public static function getModelLabel(): string
    {
        return app()->getLocale() == 'ar' ? 'رسالة تواصل' : 'Contact Message';
    }

    public static function getPluralModelLabel(): string
    {
        return app()->getLocale() == 'ar' ? 'الرسائل والدعم' : 'Messages & Support';
    }

    public static function getNavigationGroup(): ?string
    {
        return app()->getLocale() == 'ar' ? 'التفاعل' : 'Engagement';
    }

    public static function form(Schema $schema): Schema
    {
        $isAr = app()->getLocale() == 'ar';
        return $schema
            ->components([
                \Filament\Forms\Components\TextInput::make('name')
                    ->label($isAr ? 'الاسم' : 'Name')
                    ->disabled(),
                \Filament\Forms\Components\TextInput::make('phone')
                    ->label($isAr ? 'الهاتف' : 'Phone')
                    ->disabled(),
                \Filament\Forms\Components\Select::make('user_id')
                    ->label($isAr ? 'المستخدم المرسل' : 'Sender User')
                    ->relationship('user', 'full_name')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->full_name ?? $record->email ?? 'User #' . $record->id)
                    ->disabled(),
                \Filament\Forms\Components\Select::make('provider_id')
                    ->label($isAr ? 'مقدم الخدمة الموجه إليه' : 'Target Provider')
                    ->relationship('provider', 'title_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->title_ar ?? $record->title_en ?? 'Provider #' . $record->id)
                    ->disabled(),
                \Filament\Forms\Components\Select::make('service_id')
                    ->label($isAr ? 'الخدمة الموجه إليها' : 'Target Service')
                    ->relationship('service', 'service_ar')
                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->service_ar ?? $record->service_en ?? 'Service #' . $record->id)
                    ->disabled(),
                \Filament\Forms\Components\Toggle::make('is_read')
                    ->label($isAr ? 'تمت القراءة' : 'Is Read'),
                \Filament\Forms\Components\Textarea::make('message')
                    ->label($isAr ? 'محتوى الرسالة' : 'Message Content')
                    ->disabled()
                    ->rows(6)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $isAr = app()->getLocale() == 'ar';
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('name')
                    ->label($isAr ? 'الاسم' : 'Name')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('phone')
                    ->label($isAr ? 'الهاتف' : 'Phone')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('is_read')
                    ->label($isAr ? 'الحالة' : 'Status')
                    ->badge()
                    ->getStateUsing(fn ($record) => $record->is_read ? ($isAr ? 'مقروءة' : 'Read') : ($isAr ? 'غير مقروءة' : 'Unread'))
                    ->color(fn ($state) => $state === 'مقروءة' || $state === 'Read' ? 'success' : 'danger'),
                \Filament\Tables\Columns\TextColumn::make('type')
                    ->label($isAr ? 'نوع التواصل' : 'Contact Type')
                    ->getStateUsing(function ($record) use ($isAr) {
                        if ($record->service_id) return ($isAr ? 'خدمة: ' : 'Service: ') . ($record->service->service_ar ?? $record->service->service_en);
                        if ($record->provider_id) return ($isAr ? 'مقدم خدمة: ' : 'Provider: ') . ($record->provider->title_ar ?? $record->provider->title_en);
                        return $isAr ? 'رسالة عامة / الدعم' : 'General / Support';
                    })
                    ->badge()
                    ->color(fn ($record) => $record->service_id ? 'primary' : ($record->provider_id ? 'warning' : 'success')),
                \Filament\Tables\Columns\TextColumn::make('message')
                    ->label($isAr ? 'الرسالة' : 'Message')
                    ->limit(40)
                    ->searchable(),
                \Filament\Tables\Columns\TextColumn::make('created_at')
                    ->label($isAr ? 'تاريخ الإرسال' : 'Sent At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('is_read')
                    ->label($isAr ? 'حالة الرسالة' : 'Message Status')
                    ->options([
                        '1' => $isAr ? 'مقروءة' : 'Read',
                        '0' => $isAr ? 'غير مقروءة' : 'Unread',
                    ]),
                \Filament\Tables\Filters\SelectFilter::make('message_type')
                    ->label($isAr ? 'التصنيف' : 'Classification')
                    ->options([
                        'general' => $isAr ? 'رسائل عامة' : 'General Messages',
                        'service' => $isAr ? 'استفسارات الخدمات' : 'Service Inquiries',
                        'provider' => $isAr ? 'تواصل مع مقدم خدمة' : 'Contact with Provider',
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'general' => $query->whereNull('service_id')->whereNull('provider_id'),
                            'service' => $query->whereNotNull('service_id'),
                            'provider' => $query->whereNotNull('provider_id'),
                            default => $query,
                        };
                    }),
            ])
            ->actions([
                \Filament\Tables\Actions\ViewAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                \Filament\Tables\Actions\BulkActionGroup::make([
                    \Filament\Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListContacts::route('/'),
            'view' => ViewContact::route('/{record}'),
        ];
    }
}
