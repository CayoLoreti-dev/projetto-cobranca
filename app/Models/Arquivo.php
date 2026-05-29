<?php

namespace App\Models;

use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Arquivo extends Model
{
    use UsesUuid;

    protected $fillable = [
        'disk', 'path', 'original_name', 'mime_type', 'size', 'checksum_sha256',
        'uploaded_by_id', 'fileable_type', 'fileable_id', 'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function fileable()
    {
        return $this->morphTo();
    }
}
