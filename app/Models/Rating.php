<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doc_request_id',
        'responsiveness',
        'reliability',
        'access_facilities',
        'communication',
        'cost',
        'integrity',
        'assurance',
        'outcome',
        'overall',
    ];

    /**
     * Get the user that owns the rating.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the document request that owns the rating.
     */
    public function docRequest()
    {
        return $this->belongsTo(DocRequest::class);
    }

    /**
     * Get the document type for the rating.
     */
    // public function getDocumentTypeAttribute()
    // {
    //     return $this->docRequest ? $this->docRequest->document_type : 'Unknown';
    // }
}
