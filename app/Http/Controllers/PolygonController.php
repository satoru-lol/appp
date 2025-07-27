<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogContent;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseContent;
use App\Models\Dislike;
use App\Models\Introduction;
use App\Models\Like;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class PolygonController extends Controller
{

    public function editPolygon($id, Request $request)
    {
        if (!Auth::check()) {
            return redirect("/");
        }
        if (Auth::user()->group != "admin") {
            return redirect("/");
        }

        $course = Course::where("id", $id)->first();
        $user = $request->user();
        $products = Product::all();

        return view('polygon.add', compact('course', 'user', 'products'));
    }

    public function sendMail(Request $request)
    {
        if (\auth()->user()) {
            $mail = \auth()->user()->email;
            $user = Introduction::where("email", $mail)->first();
            $authUserID = \auth()->user()->id;

            $data = [
                'name' => $user,
                'date' => date('Y-m-d H:i:s'),
                'url' => "/user/info/$authUserID"
            ];

            if (!empty($mail)) {
                Config::set('mail.mailers.smtp.username', 'appppsy@gmail.com');
                Config::set('mail.mailers.smtp.password', 'wwtnsnjpgsedhkfz');
                Config::set('mail.mailers.smtp.host', 'smtp.gmail.com');
                Config::set('mail.mailers.smtp.port', 465);
                Config::set('mail.mailers.smtp.encryption', 'ssl');

                // Отправка письма
                Mail::send('mail.polygon', $data, function ($message) use ($mail) {
                    $message->from($mail, "Appp Psy");
                    $message->to('appppsy@gmail.com')->subject('Заявка на участие в полигоне');
                });

                return true;
            }
        }

    }

    public function updatePolygon(Request $request)
    {
        if (!Auth::check()) {
            return redirect("/");
        }
        if (Auth::user()->group != "admin") {
            return redirect("/");
        }

        ini_set('memory_limit', '512M');
        ini_set('upload_max_filesize', '512M');
        ini_set('post_max_size', '512M');

        $course = Course::where("id", $request->get("course_id"))->first();
        $user = $request->user();
        if (!empty($request->get("title")) && !empty($course)) {
            $request->validate([
                'title' => 'required|string|max:255',
                'speakers' => 'required|string|max:255',
                'theory' => 'required|string|max:255',
                //'course_category_id' => 'required|integer',
                'times.read' => 'nullable|string|max:255',
                'times.training' => 'nullable|string|max:255',
                'times.start' => 'nullable|string|max:255',
               // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'desc' => 'nullable|string',
                'content.characteristics1' => 'nullable|string|max:255',
                'content.characteristics2' => 'nullable|string|max:255',
                'content.characteristics3' => 'nullable|string|max:255',
                'content.characteristics4' => 'nullable|string|max:255',
                'content.characteristics5' => 'nullable|string|max:255',
                'content.teach1.title' => 'nullable|string|max:255',
                'content.teach1.subtitle' => 'nullable|string|max:255',
                // Add validations for other fields as necessary
            ]);

            $course->title = $request->input('title');//$course->course_category_id = $request->input('course_category_id'); // Set course_category_id
            $course->speakers = $request->input('speakers'); // Set course_category_id
            $course->theory = $request->input('theory'); // Set course_category_id
            $course->feedback = ""; // Set course_category_id
            $course->text = $request->input('desc'); // Set course_category_id

            if (!empty($request->get("product_level"))) {
                $course->product_level = $request->get("product_level");
            }

            $course->times = json_encode($request->input('times'));

            if (!empty($request->get("options-outlined")) && $request->get("options-outlined") != "on") {
                $course->pay_method = $request->get("options-outlined");
                $course->practice = "0"; // Set course_category_id
            } else {
                $course->practice = $request->input('practice'); // Set course_category_id
            }

            if (!empty($request->input("uploaded_image"))) {
                $course->image = $request->input("uploaded_image");
            } else {
                if ($request->hasFile('image')) {
                    $imageName = time().'.'.$request->image->extension();
                    $request->image->move(public_path('images'), $imageName);
                    $course->image = $imageName;
                }
            }

            if ($request->hasFile('video')) {
                $videoName = time().date("y-m-d").'.'.$request->video->extension();
                $request->video->move(public_path('images'), $videoName);
                $course->video = $videoName;
            }


            $course->save();

            return redirect("polygons");
        }
    }

    public function index(Request $request, int $course_category_id = 0): View
    {

        //Course::truncate();

        $user = $request->user();

        if($course_category_id) {
            if($user && $user->group === 'admin')
                $courses = Course::where('course_category_id', $course_category_id)->orderBy('views')->paginate(15);
            else
                $courses = Course::where('status', 1)->where('course_category_id', $course_category_id)->orderBy('views')->paginate(15);
        } else {
            if($user && $user->group === 'admin')
                $courses = Course::orderBy('views')->paginate(15);
            else
                $courses = Course::where('status', 1)->orderBy('views')->paginate(15);
        }

        if($user && $user->group === 'admin') {
            $categories = CourseCategory::all();
        } else {
            $categories = CourseCategory::where('status', 1)->get();

        }
        $courses = Course::where("is_polygon", true)->orderBy('views')->paginate(15);
        $isPermittedAdd = false;

        if ($user && $user->group === 'admin'){
            $isPermittedAdd = true;
        }

        /*if (!empty($user->permissions)) {
            $perms = json_decode($user->permissions, true);
            if (!empty($perms) && !empty($perms["add"]["polygon"]) && $perms["add"]["polygon"] == true) {
                $isPermittedAdd = true;
            }
        }*/

        return view('polygon.index', compact('courses', 'categories', 'user', 'course_category_id', 'isPermittedAdd'));
    }

    public function editBlog($id, Request $request)
    {
        $user = $request->user();
        $blog = Blog::where("id", $id)->first();
        $blogContent = BlogContent::where("id", $blog->blog_content_id)->first();
        if($user && $user->group === 'admin') {
            $categories = BlogCategory::all();
        } else {
            $categories = BlogCategory::where('status', 1)->get();
        }

        if (!empty($request->get("name")) && !empty($id) && !empty($blog)) {
            $request->validate([
                'name' => 'required|max:200',
                'time_read' => 'max:20',
                //'author' => 'required',
                'category_id' => 'required|exists:blog_categories,id',
                'text' => 'max:5000'
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
                $videoName = time().date("y-m-d").'.'.$request->video->extension();
                $request->video->move(public_path('images'), $videoName);
            }

            if (!empty($request->input("uploaded_image"))) {
                $blog->image = $request->input("uploaded_image");
            } else {
                if ($request->hasFile('image')) {
                    $imageName = md5($user->id.time()).'.'.$request->image->extension();
                    $request->image->move(public_path('img/blog'), $imageName);
                }
            }

            $blog->name = $request->name;
            $blog->time_read = $request->time_read;
            $blog->video = $videoName;
            $blog->reg = "0";
            $blog->status = 1;
            $blog->user_id = $request->author === 'site' ? 0 : $user->id;
            $blog->blog_category_id = $request->category_id;
            $blog->blog_content_id = $blogContent->id;
            $blog->feedback = "";
            $blog->save();

            $blogContent->text = $request->text;
            $blogContent->save();

            return redirect("blog");
        }

        return view("polygon.add", compact("blog", "user", "categories", 'blogContent'));
    }

    public function showAdd(Request $request): View
    {
        $products = Product::all();

        return view('polygon.add', compact("products"));
    }

    public function add(Request $request): RedirectResponse
    {
        ini_set('memory_limit', '512M');
        ini_set('upload_max_filesize', '512M');
        ini_set('post_max_size', '512M');

        //Course::where('id', 2)->delete();
        $request->validate([
            'title' => 'required|string|max:255',
            'speakers' => 'required|string|max:255',
            'theory' => 'required|string|max:255',
            //'course_category_id' => 'required|integer',
            'times.read' => 'nullable|string|max:255',
            'times.training' => 'nullable|string|max:255',
            'times.start' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'desc' => 'nullable|string',
            'content.characteristics1' => 'nullable|string|max:255',
            'content.characteristics2' => 'nullable|string|max:255',
            'content.characteristics3' => 'nullable|string|max:255',
            'content.characteristics4' => 'nullable|string|max:255',
            'content.characteristics5' => 'nullable|string|max:255',
            'content.teach1.title' => 'nullable|string|max:255',
            'content.teach1.subtitle' => 'nullable|string|max:255',
            // Add validations for other fields as necessary
        ]);
        $course = new Course();
        $course->title = $request->input('title');
        $course->course_category_id = "1"; // Set course_category_id
        $course->speakers = $request->input('speakers'); // Set course_category_id
        $course->theory = $request->input('theory'); // Set course_category_id
        $course->feedback = ""; // Set course_category_id
        $course->text = $request->input('desc'); // Set course_category_id

        if (!empty($request->get("product_level"))) {
            $course->product_level = $request->get("product_level");
        }

        $course->times = json_encode($request->input('times'));

        if (!empty($request->get("options-outlined")) && $request->get("options-outlined") != "on") {
            $course->pay_method = $request->get("options-outlined");
            $course->practice = "0"; // Set course_category_id
        } else {
            $course->practice = $request->input('practice'); // Set course_category_id
        }

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $course->image = $imageName;
        }

        if ($request->hasFile('video')) {
            $videoName = time().date("y-m-d").'.'.$request->video->extension();
            $request->video->move(public_path('images'), $videoName);
            $course->video = $videoName;
        }

        $course->is_polygon = true;
        $course->save();

        /*foreach ($request->input('content') as $key => $value) {
            if (!empty($value)) {
                CourseContent::create([
                    'course_id' => $course->id,
                    'text' => json_encode($value)
                ]);
            }
        }*/

        return redirect()->route('polygons')->with('success', 'Курс успешно добавлен');
    }

    public function show(int $id, Request $request): View
    {

        $user = $request->user();

        $course = Course::where('id', $id)->first();
        $content = CourseContent::where('course_id', $id)->first();
        $product = Product::where("level", $course->product_level)->first();

        return view('polygon.show', compact('course', 'user', 'content', 'product'));
    }

    public function addCategory(Request $request): RedirectResponse
    {
        $this->checkAccess($request);

        $request->validate([
            'name' => 'required',
            'status' => 'required'
        ]);

        $BlogCategory = BlogCategory::create([
            'name' => $request->name,
            'status' => $request->status
        ]);

        return back()->with('success', __('Категория добавлена'));
    }

    public function updateCategory(int $category, Request $request): RedirectResponse
    {
        $this->checkAccess($request);

        $request->validate([
            'name' => 'required',
            'status' => 'required'
        ]);

        $BlogCategory = BlogCategory::where('id', $category)->update([
            'name' => $request->name,
            'status' => $request->status
        ]);

        return back()->with('success', __('Изменения успешно сохранены'));
    }

    public function destroyCategory(int $category, Request $request): RedirectResponse
    {
        $this->checkAccess($request);

        BlogCategory::where('blog_category_id', $category)->deleteOrFail();

        return back()->with('success', __('Категория удалена'));
    }

    private function checkAccess(Request $request)
    {
        if($request->user()->group !== 'admin')
            return back()->response('Not access', 403);
    }

    public function deleteBlog(int $id, Request $request): RedirectResponse
    {

        if (Blog::where("id", $id)->exists()) {
            Blog::where("id", $id)->delete();
        }
        return back()->with("success", __("Блог удален"));
    }

	public function addcomment(Request $request){
		$data = $request->all();

		$id_post = $data['id_post'];
		$id_user = auth()->user()->id;
		$id_com = $data['id_com'];
		$comment = $data['comment'];
		$blog_content_id = $data['blog_content_id'];
		$b = new BlogComment();
		$b->user_id = $id_user;
		$b->blog_id = $id_post;
		$b->blog_content_id = $blog_content_id;
		$b->id_com = $id_com;
		$b->comment = $comment;
		$b->save();
		return redirect('/blog/'.$id_post);
	}
	public function addlike(Request $request, $post_id)
    {
        if (Dislike::where('user_id', auth()->user()->id)->where('post_id', $post_id)->exists()) {
            Dislike::where('user_id', auth()->user()->id)->where('post_id', $post_id)->delete();
            $like = Like::create([
                'user_id' => auth()->id(),
                'post_id' => $post_id
            ]);
            return redirect('/blog/'.$post_id);
        }
		if(Like::where('user_id', auth()->user()->id)->where('post_id', $post_id)->exists()){
			Like::where('user_id', auth()->user()->id)->where('post_id', $post_id)->delete();
		}else{
			$like = Like::create([
				'user_id' => auth()->id(),
				'post_id' => $post_id
			]);
		}

        return redirect('/blog/'.$post_id);
    }
	public function adddislike(Request $request, $post_id)
    {
        if (Like::where('user_id', auth()->user()->id)->where('post_id', $post_id)->exists()) {
            Like::where('user_id', auth()->user()->id)->where('post_id', $post_id)->delete();
            $like = Dislike::create([
                'user_id' => auth()->id(),
                'post_id' => $post_id
            ]);
            return redirect('/blog/'.$post_id);
        }
		if(Dislike::where('user_id', auth()->user()->id)->where('post_id', $post_id)->exists()){
			Dislike::where('user_id', auth()->user()->id)->where('post_id', $post_id)->delete();
		}else{
			$like = Dislike::create([
				'user_id' => auth()->id(),
				'post_id' => $post_id
			]);
		}

        return redirect('/blog/'.$post_id);
    }
}
