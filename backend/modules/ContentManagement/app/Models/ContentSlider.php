<?php

namespace Modules\ContentManagement\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\ContentManagement\Database\Factories\SliderContentFactory;
use Ramsey\Uuid\Uuid;

/**
 * @property string $image_url
 * @property string $image_source
 * @property int $order
 * @property string $content_url
 * @property string $slide_id
 */
class ContentSlider extends Model
{
    use HasFactory;

    protected $primaryKey = 'slide_id';

    public $incrementing = false;

    protected $table = 'content_sliders';

    public $timestamps = false;

    protected $fillable = [
        'image_url',
        'image_source',
        'order',
        'content_url',
        'slide_id',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (ContentSlider $slider) {
            $slider->slide_id = Uuid::uuid7()->toString();
        });
    }

    protected static function newFactory(): SliderContentFactory
    {
        return SliderContentFactory::new();
    }
}
