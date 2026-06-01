<?php

namespace App\Filament\Resources\AppNotifications\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;

class AppNotificationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('messages.app_notification'))
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('user.full_name')
                                    ->label(__('messages.user'))
                                    ->icon('heroicon-m-user')
                                    ->placeholder(app()->getLocale() == 'ar' ? 'جميع المستخدمين (إرسال للكل)' : 'All Users'),
                                TextEntry::make('title_ar')
                                    ->label(__('messages.title_ar'))
                                    ->weight('bold'),
                                TextEntry::make('title_en')
                                    ->label(__('messages.title_en'))
                                    ->weight('bold'),
                                TextEntry::make('type')
                                    ->label(__('messages.type'))
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('is_read')
                                    ->label(__('messages.is_read'))
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'warning')
                                    ->formatStateUsing(fn ($state) => $state ? __('messages.read') : __('messages.unread')),
                                TextEntry::make('message_ar')
                                    ->label(__('messages.message_ar'))
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'p-4 bg-gray-50 dark:bg-gray-800 rounded-lg']),
                                TextEntry::make('message_en')
                                    ->label(__('messages.message_en'))
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'p-4 bg-gray-50 dark:bg-gray-800 rounded-lg']),
                                TextEntry::make('created_at')
                                    ->label(__('messages.created_at'))
                                    ->dateTime()
                                    ->icon('heroicon-m-calendar'),
                            ]),
                    ]),
            ]);
    }
}
