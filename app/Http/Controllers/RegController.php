<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\BlogComment;
use App\Models\BlogContent;
use App\Models\Dislike;
use App\Models\Like;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegController extends Controller
{


    public function add(Request $request): RedirectResponse
    {

        $user = $request->user();

        $request->validate([
            'name' => 'required|max:200',
            'time_read' => 'max:20',
            //'author' => 'required',
            'category_id' => 'required|exists:blog_categories,id',
           // 'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
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

        $attach = [
            'image' => $imageName,
        ];

        $content = BlogContent::create([
            'text' => $request->text
        ]);

        Blog::create([
            'name' => $request->name,
            'time_read' => $request->time_read,
			'reg'=>'1',
            'image' => $imageName,
            'status' => 1,
            'user_id' => $request->author === 'site' ? 0 : $user->id,
            'blog_category_id' => $request->category_id,
            'blog_content_id' => $content->id,
            "amount" => $request->get("practice"),
            "product_level" => $request->get("product_level"),
            "feedback" => $request->get("feedback"),
            "date" => $request->get("date")
        ]);

        return redirect("regmerop");
    }

    public function updateReg(Request $request)
    {

        if (!empty($request->get("reg_id"))) {
            $blog = Blog::where("id", $request->get("reg_id"))->first();
            $user = $request->user();
            $blogContent = BlogContent::where("id", $blog->blog_content_id)->first();

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
            $blog->status = 1;
            $blog->user_id = $request->author === 'site' ? 0 : $user->id;
            $blog->blog_category_id = $request->category_id;
            $blog->blog_content_id = $blogContent->id;
            $blog->feedback = $request->get("feedback");
            $blog->product_level = $request->get("product_level");
            $blog->amount = $request->get("practice");

            $blog->save();

            $blogContent->text = $request->text;
            $blogContent->save();
        }


        return redirect("regmerop");

    }

    public function editRegMerop($id, Request $request)
    {
        $blog = Blog::where("id", $id)->first();
        $blogContent = BlogContent::where("id", $blog->blog_content_id)->first();
        $user = $request->user();
        $products = Product::all();

        if($user && $user->group === 'admin')
            $categories = BlogCategory::all();
        else
            $categories = BlogCategory::where('status', 1)->get();

        return view('reg.add', compact('user', 'categories', 'products', 'blog', 'blogContent'));

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
        $products = Product::all();

        if($user && $user->group === 'admin')
            $categories = BlogCategory::all();
        else
            $categories = BlogCategory::where('status', 1)->get();

        return view('reg.add', compact('user', 'categories', 'products'));
    }



    private function checkAccess(Request $request)
    {
        if($request->user()->group !== 'admin')
            return back()->response('Not access', 403);
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
