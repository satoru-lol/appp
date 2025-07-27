<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Orchid\Screen\AsSource;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ContentVideo extends Model
{
    use HasFactory, AsSource;
    protected $table = 'content_video';
    protected $fillable = ['title', 'url', 'category_id'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['thumbnail_url'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the thumbnail URL for the video.
     *
     * @return string|null
     */
    public function getThumbnailUrlAttribute()
    {
        $url = $this->url;
        $cacheKey = 'video_thumbnail_' . md5($url);

        // Попробуем получить из кэша
        return Cache::remember($cacheKey, now()->addDay(), function () use ($url) {
            // YouTube
            if (strpos($url, 'youtube.com') !== false || strpos($url, 'youtu.be') !== false) {
                if (preg_match('/(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|\S*?[?&]v=)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $url, $matches)) {
                    return "https://img.youtube.com/vi/{$matches[1]}/mqdefault.jpg";
                }
            }

            // Kinescope (через OEmbed API)
            if (strpos($url, 'kinescope.io') !== false) {
                try {
                    $response = Http::get('https://kinescope.io/oembed', [
                        'url' => $url,
                    ]);

                    if ($response->successful() && isset($response->json()['thumbnail_url'])) {
                        return $response->json()['thumbnail_url'];
                    }
                } catch (\Exception $e) {
                    // В случае ошибки просто возвращаем null
                    return null;
                }
            }

            return null;
        });
    }
}
