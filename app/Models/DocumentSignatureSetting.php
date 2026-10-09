<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSignatureSetting extends Model
{
    protected $fillable = ['document_type', 'signer_name', 'signer_title'];

    /** @deprecated Use config('documents.signature_types') instead */
    public static function getTypes(): array
    {
        return array_keys(config('documents.signature_types', []));
    }
}
