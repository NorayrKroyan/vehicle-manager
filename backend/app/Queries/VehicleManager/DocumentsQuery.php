<?php

namespace App\Queries\VehicleManager;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DocumentsQuery
{
    /**
     * Documents table relates to vehicles:
     * - id_docowner = 2 (vehicles)
     * - id_owner = vehicle_id
     */
    public function forVehicle(int $vehicleId): array
    {
        $table = null;

        if (Schema::hasTable('document')) {
            $table = 'document';
        } elseif (Schema::hasTable('documents')) {
            $table = 'documents';
        }

        if (!$table) return [];

        $rows = DB::table($table)
            ->where('is_deleted', 0)
            ->where('id_docowner', 2)
            ->where('id_owner', $vehicleId)
            ->orderByDesc('date_created')
            ->get([
                'id_document',
                'path',
                'id_doctype',
                'doc_name',
                'doc_description',
                'id_docowner',
                'id_owner',
                'doc_expiration',
                'date_created',
                'date_modified',
                'is_deleted',
                'document_size',
            ]);

        $base = rtrim(config('app.url'), '/'); // from APP_URL

        return $rows->map(function ($r) use ($base) {
            $raw = (string)($r->path ?? '');

            // Normalize windows paths -> URL paths
            $p = str_replace('\\', '/', $raw);
            $p = ltrim($p, '/');

            // If already absolute, keep it
            if ($p !== '' && preg_match('#^https?://#i', $p)) {
                $fileUrl = $p;
            } else {
                // Ensure it goes through Laravel public /storage symlink
                // Accept:
                //   storage/vehicle-docs/demo/x.pdf
                //   vehicle-docs/demo/x.pdf  -> becomes storage/vehicle-docs/demo/x.pdf
                if ($p !== '' && !str_starts_with($p, 'storage/')) {
                    $p = 'storage/' . $p;
                }

                $fileUrl = ($p !== '') ? ($base . '/' . $p) : null;
            }

            return [
                'id_document' => $r->id_document,
                'path' => $raw,
                'title' => $r->doc_name ?: ('Document #' . $r->id_document),
                'description' => $r->doc_description,
                'file_url' => $fileUrl,
                'doc_expiration' => $r->doc_expiration,
                'date_created' => $r->date_created,
                'date_modified' => $r->date_modified,
            ];
        })->values()->toArray();
    }
}
