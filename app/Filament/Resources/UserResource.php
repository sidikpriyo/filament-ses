<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('User')
                    ->description('Form to create or edit user data')
                    ->schema([
                        \Filament\Forms\Components\Fieldset::make('User Data')
                            ->columns([
                                'sm' => 3,
                                'md' => 4,
                                'xl' => 6,
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(100)
                                    ->minLength(3)
                                    ->columnSpan([
                                        'sm' => 3,
                                        'md' => 2,
                                        'xl' => 3,
                                    ]),
                                Forms\Components\TextInput::make('email')
                                    ->required()
                                    ->email()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(100)
                                    ->minLength(3)
                                    ->columnSpan([
                                        'sm' => 3,
                                        'md' => 2,
                                        'xl' => 3,
                                    ]),
                            ]),
                        \Filament\Forms\Components\Fieldset::make('User Auth')
                            ->columns([
                                'sm' => 3,
                                'md' => 4,
                                'xl' => 6,
                            ])
                            ->schema([
                                Forms\Components\TextInput::make('password')
                                    ->password()
                                    ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                                    ->dehydrated(fn(?string $state): bool => filled($state))
                                    ->required(fn(string $operation): bool => $operation === 'create')
                                    ->revealable()
                                    ->minLength(8)
                                    ->maxLength(12)
                                    ->confirmed()
                                    ->columnSpan([
                                        'sm' => 3,
                                        'md' => 2,
                                        'xl' => 3,
                                    ]),
                                Forms\Components\TextInput::make('password_confirmation')
                                    ->password()
                                    ->dehydrated(false)
                                    ->revealable()
                                    ->minLength(8)
                                    ->maxLength(12)
                                    ->columnSpan([
                                        'sm' => 3,
                                        'md' => 2,
                                        'xl' => 3,
                                    ]),
                            ])
                    ])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('No')
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // public static function table(Table $table): Table
    // {
    //     return $table
    //         ->columns([
    //             Tables\Columns\TextColumn::make('No')
    //                 ->rowIndex(),
    //             Tables\Columns\TextColumn::make('name')
    //                 ->searchable(),
    //             Tables\Columns\TextColumn::make('email')
    //                 ->searchable(),
    //             Tables\Columns\TextColumn::make('roles.name')
    //                 ->listWithLineBreaks(),
    //             Tables\Columns\ImageColumn::make('avatar_url')
    //                 ->label('Profile Photo')
    //                 ->size(60)
    //                 ->rounded()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //             Tables\Columns\TextColumn::make('email_verified_at')
    //                 ->label('Email Verified At')
    //                 ->dateTime()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //             Tables\Columns\TextColumn::make('created_at')
    //                 ->dateTime()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true),
    //             Tables\Columns\TextColumn::make('updated_at')
    //                 ->dateTime()
    //                 ->sortable()
    //                 ->toggleable(isToggledHiddenByDefault: true)
    //         ])
    //         ->filters([
    //             Tables\Filters\TrashedFilter::make(),
    //             Filter::make('created_at')
    //                 ->form([DatePicker::make('created_from'), DatePicker::make('created_until')])
    //                 ->query(function (Builder $query, array $data): Builder {
    //                     return $query->when($data['created_from'], fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date))->when($data['created_until'], fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date));
    //                 })
    //                 ->indicateUsing(function (array $data): ?array {
    //                     $indicators = [];
    //                     if ($data['created_from'] ?? null) {
    //                         $indicators['created_from'] = 'Created at ' . Carbon::parse($data['created_from'])->toFormattedDateString();
    //                     }
    //                     if ($data['created_until'] ?? null) {
    //                         $indicators['created_until'] = 'Created at ' . Carbon::parse($data['created_until'])->toFormattedDateString();
    //                     }
    //                     return $indicators;
    //                 }),
    //         ])
    //         ->actions([
    //             Tables\Actions\EditAction::make(),
    //             Tables\Actions\ViewAction::make(),
    //             Tables\Actions\DeleteAction::make()
    //                 ->modalHeading(fn(User $record) => 'Delete User ' . $record->name . ' ?')
    //                 ->modalDescription('Are you sure you\'d like to delete this user? This data will be moved to trash.')
    //                 ->modalSubmitActionLabel('Yes, delete it')
    //                 ->successNotification(
    //                     Notification::make()
    //                         ->success()
    //                         ->title('User Deleted Successfully!')
    //                         ->body('The user has been successfully deleted.')
    //                 ),
    //             Tables\Actions\ForceDeleteAction::make()
    //                 ->modalHeading(fn(User $record) => 'Delete User ' . $record->name . ' Permanently ?')
    //                 ->modalDescription('Are you sure you\'d like to delete this user? This data will be permanently deleted.')
    //                 ->modalSubmitActionLabel('Yes, delete permanently')
    //                 ->successNotification(
    //                     Notification::make()
    //                         ->success()
    //                         ->title('User Permanently Deleted Successfully!')
    //                         ->body('The user has been successfully deleted permanently.')
    //                 ),
    //             Tables\Actions\RestoreAction::make()
    //                 ->modalHeading(fn(User $record) => 'Restore User ' . $record->name . '?')
    //                 ->modalDescription('Are you sure you\'d like to restore this user? This data will be restored in the list.')
    //                 ->modalSubmitActionLabel('Yes, restore')
    //                 ->successNotification(
    //                     Notification::make()
    //                         ->success()
    //                         ->title('User Restored Successfully!')
    //                         ->body('The user has been successfully restored.')
    //                 ),
    //         ])
    //         ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make(), Tables\Actions\ForceDeleteBulkAction::make(), Tables\Actions\RestoreBulkAction::make()])]);
    // }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
