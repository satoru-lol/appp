<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ContentVideo;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
    {
        $this->subscriptionService = $subscriptionService;
    }

    public function showCategories($id)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Для доступа к видео необходимо авторизоваться');
        }

        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser(auth()->user());

        if (!$subscriptionStatus->hasVideoStreamAccess) {
            return redirect()->route('videostream')->with('error', 'Видеотека доступна только по подписке "Пробная", "Премиум" или "Базовый"');
        }

        $categories = Category::with(['children', 'videos'])->where('id', $id)->whereNull('parent_id')->get();
        return view('video.index', compact('categories'));
    }

    public static function nestedCategories($categories)
    {
        $html = '<ul>';
        foreach ($categories as $category) {
            $html .= '<li id="test_video">';
//            $html .= '<span onclick="toggleCategory(' . $category->id . ')">' . $category->name . '</span>';


//            $html .= '<label class="folder"><span></span></label>';
            if (empty($category->parent_id)){
                $html .= '<span style="font-size: 16px">'.$category->name.'</span>';
            }else{
                $html .= '<span onclick="toggleCategory(' . $category->id . ')" style="cursor: pointer;">';
                $html .= '<span style="font-size: 16px" onclick="loadVideos('.$category->id.')"><span style="color: #613482">&#9658;</span>'.$category->name.'</span>';
            }
            $html .= '</span>';
            if ($category->children->isNotEmpty()) {
                $html .= '<div id="category-' . $category->id . '">';
                $html .= self::nestedCategories($category->children);
                $html .= '</div>';
            }
//             if ($category->videos->isNotEmpty()) {
//                 $html .= '<ul style="margin-left: 20px;margin-top: 5px;">';
//                 foreach ($category->videos as $video) {

//                     $html .= '<iframe src="'.$video->url.'" width="360" height="240" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
// //                    $html .= '<span>"'.$video->title.'"</span>';
//                 }
//                 $html .= '</ul>';

//             }
            $html .= '<div id="videos-' . $category->id . '" class="videos" style="display: none; margin-left: 20px;"></div>';


            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }
    
        public static function nestedCategories2($categories)
    {
        $html = '<ul>';
        foreach ($categories as $category) {
            $html .= '<li id="test_video">';
          if ($category->videos->isNotEmpty()) {
                $html .= '<ul style="margin-left: 20px;margin-top: 5px; list-style: none; padding: 0;">';
                foreach ($category->videos as $video) {

                    $html .= '<div style="display: inline-block; margin-right: 15px; margin-bottom: 15px;"><iframe src="'.$video->url.'" width="360" height="240" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe></div>';
//                    $html .= '<span>"'.$video->title.'"</span>';
                }
                $html .= '</ul>';

            }


            $html .= '</li>';
        }
        $html .= '</ul>';
        return $html;
    }

    public function getVideos($id)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Для доступа к видео необходимо авторизоваться'], 401);
        }

        $subscriptionStatus = $this->subscriptionService->getSubscriptionStatusForUser(auth()->user());

        if (!$subscriptionStatus->hasVideoStreamAccess) {
            return response()->json(['error' => 'Видеотека доступна только по подписке "Пробная", "Премиум" или "Базовый"'], 403);
        }

        $videos = ContentVideo::select('url', 'title')->where('category_id', $id)->orderByDesc('id')->get();
        return response()->json($videos);
    }
}
