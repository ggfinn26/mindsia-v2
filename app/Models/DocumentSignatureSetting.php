<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentSignatureSetting extends Model
{
    protected $fillable = ['document_type', 'signer_name', 'signer_title'];

    public static array $documentTypes = ['SP', 'kwitansi', 'kontrak'];
}
