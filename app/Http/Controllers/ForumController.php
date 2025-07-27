<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\ForumCategory;
use App\Models\ForumComment;
use App\Models\ForumContent;
use App\Models\ForumTopic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ForumController extends Controller
{
    public function index(Request $request, int $forum_category_id = 0): View
    {
        $user = $request->user();

        if($forum_category_id) {
            if($user && $user->group === 'admin')
                $forums = Forum::where('forum_category_id', $forum_category_id)->paginate(15);
            else
                $forums = Forum::where('forum_category_id', $forum_category_id)->paginate(15);
        } else {
            if($user && $user->group === 'admin')
                $forums = Forum::paginate(15);
            else
                $forums = Forum::paginate(15);
        }

        if($user && $user->group === 'admin')
            $categories = ForumCategory::all();
        else
            $categories = ForumCategory::where('status', 1)->get();

        $isPermittedAdd = false;
        /*if (!empty($user->permissions)) {
            $perms = json_decode($user->permissions, true);
            if (!empty($perms) && !empty($perms["add"]["forum"]) && $perms["add"]["forum"] == true) {
                $isPermittedAdd = true;
            }
        }*/
        if ($user && $user->group === 'admin'){
            $isPermittedAdd = true;
        }
        
        return view('forum.index', compact('user', 'forums', 'categories', 'isPermittedAdd'));
    }

    public function search(Request $request): View
    {
        $search = $request->input('search');
        $topics = ForumTopic::where('name', 'like', "%$search%")->paginate(15);

        return view('forum.index', [
            'search' => true,
            'topics' => $topics
        ]);
    }

    public function showTopic(int $topic_id, Request $request): View
    {
        $user = $request->user();
        $topic = ForumTopic::where("id", $topic_id)->where('status', 1)->first();

        if (!$topic) {
            abort(404);
        }

        if($user && $user->group === 'admin')
            $comments = ForumComment::where('forum_topic_id', $topic_id)->get();
        else
            $comments = ForumComment::where(function ($query) use ($user) {
                if($user)
                    $query->where('status', '=', 1)
                        ->orWhere('user_id', '=', $user->id);
                else
                    $query->where('status', '=', 1);
            })->where('forum_topic_id', $topic_id)->get();

        return view('forum.show', compact('user', 'topic', 'comments'));
    }

    public function addCategory(Request $request): RedirectResponse
    {
        $this->checkAccess($request);

        $request->validate([
            'name' => 'required',
            'status' => 'required'
        ]);

        $ForumCategory = ForumCategory::create([
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

        $ForumCategory = ForumCategory::where('id', $category)->update([
            'name' => $request->name,
            'status' => $request->status
        ]);

        return back()->with('success', __('Изменения успешно сохранены'));
    }
    
    public function destroyCategory(int $category, Request $request): RedirectResponse
    {
        $this->checkAccess($request);

        $forums = Forum::where('forum_category_id', $category)->get();

        foreach($forums as $forum) {
            $topic = ForumTopic::where('forum_id', $forum->id)->firstOrFail(['id', 'forum_content_id']);

            ForumContent::findOrFail($topic->id)->deleteOrFail();
            ForumComment::where('forum_topic_id', $topic->id)->deleteOrFail();

            $topic->deleteOrFail();
            $forum->deleteOrFail();
        }

        ForumCategory::where('id', $category)->deleteOrFail();

        return back()->with('success', __('Категория удалена'));
    }
    
    public function addTopic(Request $request): RedirectResponse
    {
        $user = $request->user();

        if($user->group !== 'admin' && config('settings.forum_topic_add') !== 1)
            return back()->with('success', __('К сожалению, на данный момент создать тему запрещена'));

        $request->validate([
            'topic_name' => 'required|max:255',
            'topic_desc' => 'max:255',
            'topic_text' => 'max:65000',
            'topic_category' => 'required|exists:forum_categories,id',
        ]);

        $forum = Forum::create([
            'user_id' => $user->id,
            'forum_category_id' => $request->topic_category
        ]);

        $content = ForumContent::create([
            'text' => $request->topic_text
        ]);

        $topic = ForumTopic::create([
            'forum_id' => $forum->id,
            'forum_content_id' => $content->id,
            'name' => $request->topic_name,
            'desc' => $request->topic_desc,
            'status' => 1
        ]);

        return back()->with('success', __('Тема успешно создана'));
    }

    public function updateTopic(int $topic, Request $request): RedirectResponse
    {
        return back()->with('success', __('Тема успешно обновлена'));
    }

    public function addComment(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'comment_text' => 'required|max:5000',
            'topic_id' => 'required|exists:forum_topics,id',
            'reply_id' => 'exists:App\Models\ForumComment,id|nullable'
        ]);

        $content = ForumContent::create([
            'text' => $request->comment_text
        ]);

        ForumComment::create([
            'user_id' => $user->id,
            'forum_topic_id' => $request->topic_id,
            'forum_content_id' => $content->id,
            'forum_comment_id' => $request->reply_id,
            'status' => 1
        ]);

        return back()->with('success', __('Комментарий успешно добавлен'));
    }
    
    public function updateComment(int $topic, Request $request): RedirectResponse
    {
        return back()->with('success', __('Комментарий успешно обновлен'));
    }

    public function destroyComment(int $comment_id, Request $request): RedirectResponse
    {
        $user = $request->user();

        if($user->group === 'admin')
            $comment = ForumComment::findOrFail($comment_id);
        else
            $comment = ForumComment::where('user_id', $user->id)->findOrFail($comment_id);
        
        $comment->delete();
        ForumContent::findOrFail($comment->forum_content_id)->delete();

        return back()->with('success', __('Комментарий удален'));
    }

    private function checkAccess(Request $request)
    {
        if($request->user()->group !== 'admin')
            return back()->response('Not access', 403);;
    }
}
