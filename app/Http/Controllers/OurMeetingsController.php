<?php

/**
 * @deprecated Этот контроллер устарел. Используйте App\Http\Controllers\V2\Refactored\MeetingsController
 * 
 * ВНИМАНИЕ: Данный файл будет удален в будущих версиях.
 * Новая архитектура встреч находится в app/Http/Controllers/V2/Refactored/MeetingsController.php
 * 
 * Миграция:
 * - Старые маршруты: /our-meetings/*
 * - Новые маршруты: /v2/refactored/meetings/*
 * 
 * Новая архитектура включает:
 * - MeetingService для бизнес-логики
 * - MeetingRepository для работы с данными
 * - Оптимизированные запросы к БД
 * - API поддержку
 * 
 * @see App\Http\Controllers\V2\Refactored\MeetingsController
 */

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogContent;
use App\Models\MeetingFormat;
use App\Models\ObjectViewControl;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class OurMeetingsController
{

    public function show(int $meeting_id, Request $request): View
    {

        $user = $request->user();

        if ($user && $user->group === 'admin')
            $blog = Blog::findOrFail($meeting_id);
        else
            $blog = Blog::where('status', 1)->findOrFail($meeting_id);

        if (auth()->user()) {
            $objectControlView = ObjectViewControl::where("user_id", auth()->user()->id)
                ->where("object_name", "meeting")
                ->where("object_id", $blog->id)
                ->first();

            if (empty($objectControlView->user_id)) {
                ObjectViewControl::create([
                    "user_id" => auth()->user()->id,
                    "object_name" => "meeting",
                    "object_id" => $blog->id
                ]);


                $blog->views += 1;
                $blog->save();
            }
        }

        $format = MeetingFormat::where("id", $blog->format_id)->first();

        $date = Carbon::parse($blog->date);
        $formattedDate = $date->translatedFormat('d F Y \г. \в H:i');

        return view('our_meetings.show', compact('user', 'blog', 'format', 'formattedDate'));
    }

    public function index(Request $request, int $blog_category_id = 0)
    {
        $user = $request->user();
        $today = Carbon::today();
        $blogs = Blog::where('status', 1)->where('is_meeting', true)->where('date','>=',$today);
        if (isset($request->type_meet) && is_int($request->type_meet)){
            $blogs->where('format_id',$request->type_meet);
        }
        if (isset($request->place)){
            $blogs->whereLike('place','%'.$request->place.'%');
        }
        if (isset($request->price)){
            /*if ($request->price == 'free'){
                $blogs->whereNull('amount');
            }*/
            if ($request->price == 'paid' && isset($request->from_price)){
                $blogs->where('amount','>=',$request->from_price);
            }
            if ($request->price == 'paid' && isset($request->to_price)){
                $blogs->where('amount','<=',$request->to_price);
            }
        }
        if (isset($request->date_start)){
            $blogs->whereDate('date','>=',$request->date_start);
        }
        if (isset($request->end)){
            $blogs->whereDate('date','<=',$request->end);
        }
        $blogs = $blogs->orderBy('date')->paginate(15);
        /*if ($blog_category_id) {
            if ($user && $user->group === 'admin')
                $blogs = Blog::where('blog_category_id', $blog_category_id)->where('is_meeting', true)->paginate(15);
            else
                $blogs = Blog::where('blog_category_id', $blog_category_id)->where('is_meeting', true)->where('status', 1)->paginate(15);
        } else {
            if ($user && $user->group === 'admin')
                $blogs = Blog::where('is_meeting', true)->paginate(15);
            else
                $blogs = Blog::where('status', 1)->where('is_meeting', true)->paginate(15);
        }*/

        if ($user && $user->group === 'admin')
            $categories = BlogCategory::all();
        else
            $categories = BlogCategory::where('status', 1)->get();

        $isPermittedAdd = false;
        /*if (!empty($user->permissions)) {
            $perms = json_decode($user->permissions, true);
            if (!empty($perms) && !empty($perms["add"]["reg"]) && $perms["add"]["reg"] == true) {
                $isPermittedAdd = true;
            }
        }*/
        if ($user && $user->group === 'admin'){
            $isPermittedAdd = true;
        }

        foreach ($blogs as &$blog) {
            $blog["format"] = MeetingFormat::where("id", $blog->format_id)->first();

            $date = Carbon::parse($blog->date);
            $blog["formattedDate"] = $date->translatedFormat('d F Y \г. \в H:i');

        }

        return view('our_meetings.index', compact('user', 'blogs', 'categories', 'isPermittedAdd'));

    }

    public function delete($id){
        $delete = Blog::where('id',$id)->update(['image' => null]);
        if (!$delete){
            return response()->json(['error' => 'Попробуйте позже'],500);
        }
        return response()->json(['success' => 'Изображение удалено'],200);
    }

    public function previous(Request $request){
        $user = $request->user();
        $today = Carbon::today();
        $blogs = Blog::where('status', 1)->where('is_meeting', true)->where('date','<=',$today)->paginate(15);
        $isPermittedAdd = false;
        if (!empty($user->permissions)) {
            $perms = json_decode($user->permissions, true);
            if (!empty($perms) && !empty($perms["add"]["reg"]) && $perms["add"]["reg"] == true) {
                $isPermittedAdd = true;
            }
        }
        if ($user && $user->group === 'admin')
            $categories = BlogCategory::all();
        else
            $categories = BlogCategory::where('status', 1)->get();

        foreach ($blogs as &$blog) {
            $blog["format"] = MeetingFormat::where("id", $blog->format_id)->first();

            $date = Carbon::parse($blog->date);
            $blog["formattedDate"] = $date->translatedFormat('d F Y \г. \в H:i');

        }
        return view('our_meetings.previous', compact('user', 'blogs', 'categories', 'isPermittedAdd'));
    }

    public function showAdd(Request $request): View
    {
        $user = $request->user();
        $products = Product::all();

        if ($user && $user->group === 'admin')
            $categories = BlogCategory::all();
        else
            $categories = BlogCategory::where('status', 1)->get();

        return view('our_meetings.add', compact('user', 'categories', 'products'));
    }

    public function add(Request $request): RedirectResponse
    {

        $user = $request->user();

        $request->validate([
            'name' => 'required|max:200',
            'time_read' => 'max:20',
            //'author' => 'required',
            //'category_id' => 'required|exists:blog_categories,id',
            // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text' => 'max:5000',
            'fio' => 'required|max:200',
            'format_id' => 'required|max:100'
        ], [], [
            'name' => 'название',
            'time_read' => 'время чтения',
            'author' => 'автор',
            //  'category_id' => 'категория',
            'image' => 'изображение',
            'text' => 'текст'
        ]);
        if (isset($request->image)) {
            $imageName = md5($user->id . time()) . '.' . $request->image->extension();
            $request->image->move(public_path('img/blog'), $imageName);

            $attach = [
                'image' => $imageName,
            ];
        }


        $content = BlogContent::create([
            'text' => $request->text ?? 'Описание'
        ]);

        Blog::create([
            'name' => $request->name,
            'time_read' => $request->time_read,
            'is_meeting' => true,
            'image' => $imageName ?? null,
            'status' => 1,
            'explanation' => $request->get('explanation'),
            'user_id' => $request->author === 'site' ? 0 : $user->id,
            // 'blog_category_id' => $request->category_id,
            'blog_content_id' => $content->id,
            "amount" => $request->get("practice"),
            "product_level" => $request->get("product_level"),
            "feedback" => $request->get("feedback") ?? $request->get('place_meeting'),
            "date" => $request->get("date"),
            'format_id' => $request->get("format_id"),
            'fio' => $request->get("fio"),
            'quantity' => $request->get('quantity') ?? NULL,
        ]);

        return redirect("ourMeetings");
    }

    public function search(Request $request): RedirectResponse
    {
        $blogs = Blog::where('status',1);
        if (isset($request->type_meet) && is_int($request->type_meet)){
            $blogs->where('format_id',$request->type_meet);
        }
        if (isset($request->place)){
            $blogs->whereLike('place','%'.$request->place.'%');
        }
        if (isset($request->price)){
            /*if ($request->price == 'free'){
                $blogs->whereNull('amount');
            }*/
            if ($request->price == 'paid' && isset($request->from_price)){
                $blogs->where('amount','>=',$request->from_price);
            }
            if ($request->price == 'paid' && isset($request->to_price)){
                $blogs->where('amount','<=',$request->to_price);
            }
        }
        if (isset($request->date_start)){
            $blogs->whereDate('date','>=',$request->date_start);
        }
        if (isset($request->end)){
            $blogs->whereDate('date','<=',$request->end);
        }
//        $blogs = Blog::where('status',1)->paginate(15);
        $blogs = $blogs->paginate(15);
        $user = $request->user();
        $categories = BlogCategory::where('status', 1)->get();
        $isPermittedAdd = false;
        if (!empty($user->permissions)) {
            $perms = json_decode($user->permissions, true);
            if (!empty($perms) && !empty($perms["add"]["reg"]) && $perms["add"]["reg"] == true) {
                $isPermittedAdd = true;
            }
        }
        foreach ($blogs as &$blog) {
            $blog["format"] = MeetingFormat::where("id", $blog->format_id)->first();

            $date = Carbon::parse($blog->date);
            $blog["formattedDate"] = $date->translatedFormat('d F Y \г. \в H:i');

        }
//        return Redirect::route('ourMeetings')->with(['blogs'=>$blogs,'user' => $user,'categories' => $categories,'isPermittedAt' => $isPermittedAdd]);
        $userData = $user instanceof \Illuminate\Database\Eloquent\Model ? $user->toArray() : $user;
        $blogsData = $blogs instanceof \Illuminate\Database\Eloquent\Collection ? $blogs->toArray() : $blogs;
        $categoriesData = $categories instanceof \Illuminate\Database\Eloquent\Collection ? $categories->toArray() : $categories;

// Redirect with only serializable data
        return redirect()->to('/ourMeetings')->with([
            'user' => $userData,
            'categories' => $categoriesData,
            'isPermittedAdd' => $isPermittedAdd, // Assuming $isPermittedAdd is already serializable (e.g., boolean or string)
        ]);
    }

    public function editOurMeetings($id, Request $request)
    {
        $blog = Blog::where("id", $id)->first();
        $blogContent = BlogContent::where("id", $blog->blog_content_id)->first();
        $user = $request->user();
        $products = Product::all();

        if ($user && $user->group === 'admin')
            $categories = BlogCategory::all();
        else
            $categories = BlogCategory::where('status', 1)->get();

        return view('our_meetings.add', compact('user', 'categories', 'products', 'blog', 'blogContent'));

    }

    public function updateOurMeetings(Request $request)
    {

        if (!empty($request->get("reg_id"))) {
            $blog = Blog::where("id", $request->get("reg_id"))->first();
            $user = $request->user();
            $blogContent = BlogContent::where("id", $blog->blog_content_id)->first();

            $request->validate([
                'name' => 'required|max:200',
                //'time_read' => 'max:20',
                //'author' => 'required',
                // 'category_id' => 'required|exists:blog_categories,id',
                'text' => 'max:5000',
                'fio' => 'required|max:200',
                'format_id' => 'required|max:100'
            ], [], [
                'name' => 'название',
                'time_read' => 'время чтения',
                'author' => 'автор',
                'category_id' => 'категория',
                'image' => 'изображение',
                'text' => 'текст'
            ]);


            $videoName = "";
            if ($request->hasFile('video')) {
                $videoName = time() . date("y-m-d") . '.' . $request->video->extension();
                $request->video->move(public_path('images'), $videoName);
            }

            if (!empty($request->input("uploaded_image"))) {
                $blog->image = $request->input("uploaded_image");
            } else {
                if ($request->hasFile('image')) {
                    $imageName = md5($user->id . time()) . '.' . $request->image->extension();
                    $request->image->move(public_path('img/blog'), $imageName);
                    $blog->image = $imageName;
                }
            }

            $blog->name = $request->name;
            $blog->time_read = $request->time_read;
            $blog->video = $videoName;
            $blog->status = 1;
            $blog->user_id = $request->author === 'site' ? 0 : $user->id;
            //  $blog->blog_category_id = $request->category_id;
            //  $blog->blog_content_id = $blogContent->id;
            $feedback = $request->get('feedback');
            if (!is_null($request->get('place_meeting'))){
                $feedback = $request->get('place_meeting');
            }
            Log::info('feedback', ['feedback' => $feedback]);
            $blog->feedback = $feedback;
            $blog->product_level = $request->get("product_level");
            $blog->amount = $request->get("practice");
            $blog->format_id = $request->get("format_id");
            $blog->fio = $request->get("fio");
            $blog->explanation = $request->get('explanation');
            $blog->save();

            $blogContent->text = $request->text;
            $blogContent->save();
        }


        return redirect("ourMeetings");

    }

    public function deleteOurMeeting(int $id, Request $request): RedirectResponse
    {

        if (Blog::where("id", $id)->exists()) {
            Blog::where("id", $id)->delete();
        }
        return back()->with("success", __("Встреча удалена"));
    }

}
