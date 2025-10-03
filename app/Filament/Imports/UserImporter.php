<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Auth;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('class')
                ->rules(['max:255']),
            ImportColumn::make('major')
                ->rules(['max:255']),
            ImportColumn::make('parent_number')
                ->rules(['max:255']),
            ImportColumn::make('rfid')
                ->rules(['max:255']),
            ImportColumn::make('role')
                ->requiredMapping()
                ->rules(['required']),
        ];
    }

    public function resolveRecord(): ?User
    {
        // return User::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        $user = new User();
        
        // Set tenant_id dari user yang sedang login jika tidak ada di data import
        if (!isset($this->data['tenant_id']) && Auth::check()) {
            $user->tenant_id = Auth::user()->tenant_id;
        }

        return $user;
    }

    protected function afterSave(): void
    {
        // Pastikan tenant_id terisi setelah save jika masih kosong
        if (empty($this->record->tenant_id) && Auth::check()) {
            $this->record->update(['tenant_id' => Auth::user()->tenant_id]);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
