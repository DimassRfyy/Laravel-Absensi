<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceSessionResource\Pages;
use App\Filament\Resources\AttendanceSessionResource\RelationManagers;
use App\Models\AttendanceSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\ActionGroup;

class AttendanceSessionResource extends Resource
{
    protected static ?string $model = AttendanceSession::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Settings';

    public static function canAccess(): bool
    {
        return Auth::user()->role === 'admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TimePicker::make('start_time')
                    ->required(),
                Forms\Components\TimePicker::make('end_time')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true)
                    ->helperText('Aktifkan atau nonaktifkan sesi absensi ini'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_time')
                    ->label('Waktu Mulai')
                    ->time('H:i:s'),
                Tables\Columns\TextColumn::make('end_time')
                    ->label('Waktu Selesai')
                    ->time('H:i:s'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif')
                    ->placeholder('Semua Status')
                    ->trueLabel('Hanya Aktif')
                    ->falseLabel('Hanya Nonaktif'),
            ])
            ->actions([
                ActionGroup::make([
                    ActionGroup::make([
                        Tables\Actions\Action::make('toggle_active')
                            ->label(fn (AttendanceSession $record) => $record->is_active ? 'Nonaktifkan' : 'Aktifkan')
                            ->icon(fn (AttendanceSession $record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                            ->color(fn (AttendanceSession $record) => $record->is_active ? 'warning' : 'success')
                            ->requiresConfirmation()
                            ->modalHeading(fn (AttendanceSession $record) => ($record->is_active ? 'Nonaktifkan' : 'Aktifkan') . ' Sesi Absensi')
                            ->modalDescription(fn (AttendanceSession $record) => 'Apakah Anda yakin ingin ' . ($record->is_active ? 'menonaktifkan' : 'mengaktifkan') . ' sesi absensi "' . $record->name . '"?')
                            ->action(fn (AttendanceSession $record) => $record->update(['is_active' => !$record->is_active]))
                            ->after(fn () => redirect()->back()->with('success', 'Status sesi berhasil diubah')),
                        Tables\Actions\EditAction::make(),
                    ])
                        ->dropdown(false),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-bars-3')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Aktifkan Sesi Terpilih')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aktifkan Sesi Absensi')
                        ->modalDescription('Apakah Anda yakin ingin mengaktifkan semua sesi yang dipilih?')
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Nonaktifkan Sesi Terpilih')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalHeading('Nonaktifkan Sesi Absensi')
                        ->modalDescription('Apakah Anda yakin ingin menonaktifkan semua sesi yang dipilih?')
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAttendanceSessions::route('/'),
        ];
    }
}
