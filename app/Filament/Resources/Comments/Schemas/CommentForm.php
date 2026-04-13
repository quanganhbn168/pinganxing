<?php

namespace App\Filament\Resources\Comments\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CommentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('commentable_type')
                    ->required(),
                TextInput::make('commentable_id')
                    ->required()
                    ->numeric(),
                Select::make('parent_id')
                    ->relationship('parent', 'id'),
                TextInput::make('author_name')
                    ->required(),
                TextInput::make('author_email')
                    ->email(),
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('rating')
                    ->numeric(),
                Select::make('status')
                    ->options(['pending' => 'Pending', 'approved' => 'Approved', 'spam' => 'Spam'])
                    ->default('pending')
                    ->required(),
            ]);
    }
}
