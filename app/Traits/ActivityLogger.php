<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait ActivityLogger
{
    /**
     * Log aktivitas user
     *
     * @param string $type (login, logout, create, update, delete, export, dll)
     * @param string $description
     * @param string $category (Produk, Pelanggan, Invoice, dll)
     * @param mixed $model (optional)
     * @param array $properties (optional - untuk menyimpan detail perubahan)
     */
    public static function logActivity($type, $description, $category, $model = null, $properties = [])
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'type' => $type,
            'category' => $category,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'properties' => !empty($properties) ? $properties : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log login activity
     */
    public static function logLogin($user)
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'type' => 'login',
            'category' => 'Pengguna',
            'description' => "User '{$user->name}' melakukan login",
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log logout activity
     */
    public static function logLogout()
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::create([
                'user_id' => $user->id,
                'type' => 'logout',
                'category' => 'Pengguna',
                'description' => "User '{$user->name}' melakukan logout",
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
            ]);
        }
    }

    /**
     * Log create activity
     *
     * @param mixed $model
     * @param string $modelName (Nama model untuk deskripsi: Produk, Pelanggan, dll)
     * @param string $category (Kategori modul: Produk, Pelanggan, dll)
     */
    public static function logCreate($model, $modelName, $category = null)
    {
        $user = Auth::user();
        $identifier = self::getModelIdentifier($model);

        // Jika category tidak diberikan, gunakan modelName sebagai category
        $category = $category ?? $modelName;

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'type' => 'create',
            'category' => $category,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => "User '{$user->name}' membuat {$modelName} baru: {$identifier}",
            'properties' => ['attributes' => $model->getAttributes()],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log update activity
     *
     * @param mixed $model
     * @param string $modelName
     * @param array $oldValues
     * @param array $newValues
     * @param string $category
     */
    public static function logUpdate($model, $modelName, $oldValues = [], $newValues = [], $category = null)
    {
        $user = Auth::user();
        $identifier = self::getModelIdentifier($model);

        // Jika category tidak diberikan, gunakan modelName sebagai category
        $category = $category ?? $modelName;

        $changes = [];
        foreach ($newValues as $key => $newValue) {
            if (isset($oldValues[$key]) && $oldValues[$key] != $newValue) {
                $changes[$key] = [
                    'old' => $oldValues[$key],
                    'new' => $newValue,
                ];
            }
        }

        $changesText = self::formatChanges($changes);

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'type' => 'update',
            'category' => $category,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => "User '{$user->name}' mengubah {$modelName}: {$identifier}. {$changesText}",
            'properties' => ['old' => $oldValues, 'new' => $newValues, 'changes' => $changes],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log delete activity
     *
     * @param mixed $model
     * @param string $modelName
     * @param string $category
     */
    public static function logDelete($model, $modelName, $category = null)
    {
        $user = Auth::user();
        $identifier = self::getModelIdentifier($model);

        // Jika category tidak diberikan, gunakan modelName sebagai category
        $category = $category ?? $modelName;

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'type' => 'delete',
            'category' => $category,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => "User '{$user->name}' menghapus {$modelName}: {$identifier}",
            'properties' => ['deleted_attributes' => $model->getAttributes()],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log export activity
     *
     * @param string $exportType
     * @param array $filters
     * @param string $category
     */
    public static function logExport($exportType, $filters = [], $category = null)
    {
        $user = Auth::user();

        // Jika category tidak diberikan, coba extract dari exportType
        if (!$category) {
            if (str_contains($exportType, 'Invoice')) {
                $category = 'Invoice';
            } elseif (str_contains($exportType, 'Keuangan')) {
                $category = 'Riwayat Keuangan';
            } elseif (str_contains($exportType, 'Produk')) {
                $category = 'Produk';
            } else {
                $category = $exportType;
            }
        }

        $filterText = !empty($filters) ? ' dengan filter: ' . json_encode($filters) : '';

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'type' => 'export',
            'category' => $category,
            'description' => "User '{$user->name}' melakukan export data {$exportType}{$filterText}",
            'properties' => ['export_type' => $exportType, 'filters' => $filters],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Log status change
     *
     * @param mixed $model
     * @param string $modelName
     * @param string $oldStatus
     * @param string $newStatus
     * @param string $category
     */
    public static function logStatusChange($model, $modelName, $oldStatus, $newStatus, $category = null)
    {
        $user = Auth::user();
        $identifier = self::getModelIdentifier($model);

        // Jika category tidak diberikan, gunakan modelName sebagai category
        $category = $category ?? $modelName;

        ActivityLog::create([
            'user_id' => $user ? $user->id : null,
            'type' => 'status_change',
            'category' => $category,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'description' => "User '{$user->name}' mengubah status {$modelName}: {$identifier} dari '{$oldStatus}' menjadi '{$newStatus}'",
            'properties' => ['old_status' => $oldStatus, 'new_status' => $newStatus],
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    /**
     * Get model identifier (nama, nomor, dll)
     * ✅ UPDATED: Tambah support untuk Product dan ProductBatch
     */
    private static function getModelIdentifier($model)
    {
        // ✅ PRIORITAS 1: ProductBatch → gunakan batch_number
        if (isset($model->batch_number)) {
            return $model->batch_number;
        }

        // ✅ PRIORITAS 2: Product → gunakan nama_produk
        if (isset($model->nama_produk)) {
            return $model->nama_produk;
        }

        // PRIORITAS 3: Field umum lainnya
        if (isset($model->name)) {
            return $model->name;
        } elseif (isset($model->nama)) {
            return $model->nama;
        } elseif (isset($model->nama_customer)) {
            return $model->nama_customer;
        } elseif (isset($model->invoice_number)) {
            return $model->invoice_number;
        } elseif (isset($model->email)) {
            return $model->email;
        }

        // FALLBACK: Jika tidak ada field yang cocok, return ID
        return "ID: {$model->id}";
    }

    /**
     * Format changes untuk deskripsi yang mudah dibaca
     */
    private static function formatChanges($changes)
    {
        if (empty($changes)) {
            return '';
        }

        $changeTexts = [];
        foreach ($changes as $field => $values) {
            $changeTexts[] = "{$field}: '{$values['old']}' → '{$values['new']}'";
        }

        return 'Perubahan: ' . implode(', ', $changeTexts);
    }
}
