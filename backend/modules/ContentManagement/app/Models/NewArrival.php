<?php

namespace Modules\ContentManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ContentManagement\Enum\NewArrivalsPositionEnum;

/**
 * @property string $arrival_id
 * @property array $content
 * @property NewArrivalsPositionEnum $position
 * @property string $arrival_url
 * @property string $image_source
 * @property string $image_url
 */
class NewArrival extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = 'arrival_id';

    protected $table = 'content_new_arrivals';

    public $incrementing = false;

    protected $fillable = [
        'arrival_id',
        'content',
        'position',
        'arrival_url',
        'image_source',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'position' => NewArrivalsPositionEnum::class,
        ];
    }
}
