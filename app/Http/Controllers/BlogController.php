<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogContent;
use App\Models\Dislike;
use App\Models\Like;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(Request $request, int $blog_category_id = 0): View
    {

        $user = $request->user();

        if($blog_category_id) {
            if($user && $user->group === 'admin')
                $blogs = Blog::where('blog_category_id', $blog_category_id)->whereNull('reg')->paginate(15);
            else
                $blogs = Blog::where('blog_category_id', $blog_category_id)->whereNull('reg')->where('status', 1)->paginate(15);
        } else {
            if($user && $user->group === 'admin')
                $blogs = Blog::where("reg", "0")->paginate(15);
            else
                $blogs = Blog::where('status', 1)->paginate(15);
        }

        if($user && $user->group === 'admin')
            $categories = BlogCategory::all();
        else
            $categories = BlogCategory::where('status', 1)->get();

        $isPermittedAdd = false;
        /*if (!empty($user->permissions)) {
            $perms = json_decode($user->permissions, true);
            if (!empty($perms) && !empty($perms["add"]["blog"]) && $perms["add"]["blog"] == true) {
                $isPermittedAdd = true;
            }
        }*/
        if ($user && $user->group === 'admin'){
            $isPermittedAdd = true;
        }

        return view('blog.index', compact('user', 'blogs', 'categories', 'isPermittedAdd'));
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

            $blog->save();

            $blogContent->text = $request->text;
            $blogContent->save();

            return redirect("blog");
        }

        return view("blog.add", compact("blog", "user", "categories", 'blogContent'));
    }

    public function add(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name' => 'required|max:200',
            'time_read' => 'max:20',
            //'author' => 'required',
            'category_id' => 'required|exists:blog_categories,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text' => 'max:5000'
        ], [], [
            'name' => 'название',
            'time_read' => 'время чтения',
            'author' => 'автор',
            'category_id' => 'категория',
            'image' => 'изображение',
            'text' => 'текст'
        ]);

        $imageName = md5($user->id.time()).'.'.$request->image->extension();
        $request->image->move(public_path('img/blog'), $imageName);

        $videoName = "";
        if ($request->hasFile('video')) {
            $videoName = time().date("y-m-d").'.'.$request->video->extension();
            $request->video->move(public_path('images'), $videoName);
        }


        $attach = [
            'image' => $imageName,
        ];

        $content = BlogContent::create([
            'text' => $request->text
        ]);

        Blog::create([
            'name' => $request->name,
            'time_read' => $request->time_read,

            'image' => $imageName,
            'video' => $videoName,
			'reg' => '0',
            'status' => 1,
            'user_id' => $request->author === 'site' ? 0 : $user->id,
            'blog_category_id' => $request->category_id,
            'blog_content_id' => $content->id,
        ]);

        return redirect("blog");
    }

    public function show(int $blog_id, Request $request): View
    {

        $user = $request->user();

        if($user && $user->group === 'admin')
            $blog = Blog::findOrFail($blog_id);
        else
            $blog = Blog::where('status', 1)->findOrFail($blog_id);

        $blog->views += 1;
        $blog->save();


        return view('reg.show', compact('user', 'blog'));
    }

    public function showAdd(Request $request): View
    {
        $user = $request->user();

        if($user && $user->group === 'admin')
            $categories = BlogCategory::all();
        else
            $categories = BlogCategory::where('status', 1)->get();

        return view('blog.add', compact('user', 'categories'));
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
