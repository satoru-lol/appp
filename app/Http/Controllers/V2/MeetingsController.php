<?php

/**
 * @deprecated Этот контроллер устарел. Используйте App\Http\Controllers\V2\Refactored\MeetingsController
 * 
 * ВНИМАНИЕ: Данный файл будет удален в будущих версиях.
 * Новая архитектура находится в app/Http/Controllers/V2/Refactored/MeetingsController.php
 * 
 * Миграция:
 * - Старые маршруты: /v2/meetings/*
 * - Новые маршруты: /v2/refactored/meetings/*
 * 
 * @see App\Http\Controllers\V2\Refactored\MeetingsController
 */

namespace App\Http\Controllers\V2;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogContent;
use App\Models\MeetingFormat;
use App\Models\ObjectViewControl;
use App\Models\ParticipantActions;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MeetingsController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        $blogs = Blog::where('status', 1)
            ->where('is_meeting', true)
            ->where('date', '>=', $today)
            ->orderBy('date')
            ->paginate(15);

        foreach ($blogs as &$blog) {
            $blog->format = MeetingFormat::find($blog->format_id);
            $blog->formattedDate = Carbon::parse($blog->date)->translatedFormat('d F Y \г. \в H:i');
            
            // Добавляем количество участников
            $blog->participants_count = ParticipantActions::where('object_id', $blog->id)
                ->where('object_name', 'meeting')
                ->count();
                
            // Добавляем количество комментариев
            $blog->comments_count = \App\Models\BlogComment::where('blog_id', $blog->id)->count();
        }

        $title = 'Наши встречи - АЧПП';
        $description = 'Календарь предстоящих встреч, семинаров и мероприятий для психологов и психотерапевтов. Присоединяйтесь к профессиональному сообществу.';
        $keywords = 'встречи психологов, семинары для психотерапевтов, мероприятия АЧПП, профессиональные события, календарь мероприятий';

        return view('v2.meetings.index', compact('blogs', 'title', 'description', 'keywords'));
    }

    public function previous()
    {
        $today = Carbon::today();
        $blogs = Blog::where('status', 1)
            ->where('is_meeting', true)
            ->where('date', '<', $today)
            ->orderByDesc('date')
            ->paginate(15);

        foreach ($blogs as &$blog) {
            $blog->format = MeetingFormat::find($blog->format_id);
            $blog->formattedDate = Carbon::parse($blog->date)->translatedFormat('d F Y \г.');
            
            // Добавляем количество участников
            $blog->participants_count = ParticipantActions::where('object_id', $blog->id)
                ->where('object_name', 'meeting')
                ->count();
                
            // Добавляем количество комментариев
            $blog->comments_count = \App\Models\BlogComment::where('blog_id', $blog->id)->count();
        }

        $title = 'Прошедшие встречи - АЧПП';
        $description = 'Архив прошедших встреч, семинаров и мероприятий Ассоциации. Материалы и записи для участников.';
        $keywords = 'архив встреч, прошедшие мероприятия, записи семинаров, материалы АЧПП, история событий';

        return view('v2.meetings.previous', compact('blogs', 'title', 'description', 'keywords'));
    }

    public function show($id)
    {
        $blog = Blog::with('blogContent')->where('status', 1)->find($id);

        if (!$blog) {
            abort(404, 'Встреча не найдена');
        }

        if (Auth::check()) {
            ObjectViewControl::firstOrCreate(
                ['user_id' => Auth::id(), 'object_name' => 'meeting', 'object_id' => $blog->id],
                []
            );
            if(ObjectViewControl::where('user_id', Auth::id())->where('object_name', 'meeting')->where('object_id', $blog->id)->first()->wasRecentlyCreated) {
                $blog->increment('views');
            }
        }
        
        $format = MeetingFormat::find($blog->format_id);
        $formattedDate = Carbon::parse($blog->date)->translatedFormat('d F Y \г. \в H:i');

        // Отладочный код для встречи с ID 146
        if ($id == 146) {
            $comments = \App\Models\BlogComment::where('blog_id', $blog->id)->with('user')->latest()->get();
            
            // Добавляем отладочную информацию в сессию
            session()->flash('debug_comments', [
                'count' => $comments->count(),
                'comments' => $comments->map(function($comment) {
                    return [
                        'id' => $comment->id,
                        'user_id' => $comment->user_id,
                        'blog_id' => $comment->blog_id,
                        'blog_content_id' => $comment->blog_content_id,
                        'comment' => $comment->comment,
                        'status' => $comment->status,
                        'created_at' => $comment->created_at->format('Y-m-d H:i:s'),
                        'user' => $comment->user ? [
                            'id' => $comment->user->id,
                            'firstname' => $comment->user->firstname,
                            'lastname' => $comment->user->lastname
                        ] : null
                    ];
                })
            ]);
        }

        $title = $blog->name . ' - Встреча АЧПП';
        $description = 'Приглашаем на встречу "' . $blog->name . '". ';
        if ($blog->blogContent) {
            $description .= \Illuminate\Support\Str::limit(strip_tags($blog->blogContent->text), 120);
        }
        $keywords = $blog->name . ', встреча, семинар, ' . $blog->fio . ', АЧПП, психология';


        return view('v2.meetings.show', compact('blog', 'format', 'formattedDate', 'title', 'description', 'keywords'));
    }

    public function create()
    {
        return view('v2.meetings.add');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|max:200',
            'date' => 'required|date',
            'fio' => 'required|max:200',
            'format_id' => 'required|exists:meeting_format,id',
            'feedback' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text' => 'nullable|string',
            'amount' => 'nullable|string|max:100',
            'quantity' => 'nullable|integer',
        ]);

        $blogContent = BlogContent::create(['text' => $validated['text'] ?? '']);

        $blog = new Blog($validated);
        $blog->is_meeting = true;
        $blog->status = 1; 
        $blog->user_id = Auth::id();
        $blog->blog_content_id = $blogContent->id;

        if ($request->hasFile('image')) {
            $imageName = md5(Auth::id() . time()) . '.' . $request->image->extension();
            $request->image->move(public_path('img/blog'), $imageName);
            $blog->image = $imageName;
        }

        $blog->save();

        return redirect()->route('v2.meetings.show', $blog->id)->with('success', 'Встреча успешно создана!');
    }

    public function edit($id)
    {
        $blog = Blog::with('blogContent')->findOrFail($id);
        // Проверяем, что пользователь имеет право редактировать эту встречу
        if (Auth::id() !== $blog->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'У вас нет прав для редактирования этой встречи');
        }
        return view('v2.meetings.add', compact('blog'));
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::findOrFail($id);
        // Проверяем, что пользователь имеет право редактировать эту встречу
        if (Auth::id() !== $blog->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'У вас нет прав для редактирования этой встречи');
        }

        $validated = $request->validate([
            'name' => 'required|max:200',
            'date' => 'required|date',
            'fio' => 'required|max:200',
            'format_id' => 'required|exists:meeting_format,id',
            'feedback' => 'nullable|string|max:500',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'text' => 'nullable|string',
            'amount' => 'nullable|string|max:100',
            'quantity' => 'nullable|integer',
        ]);

        $blog->update($validated);

        if ($blog->blogContent) {
            $blog->blogContent->update(['text' => $validated['text'] ?? '']);
        }

        if ($request->hasFile('image')) {
            // delete old image if exists
            if ($blog->image && file_exists(public_path('img/blog/' . $blog->image))) {
                unlink(public_path('img/blog/' . $blog->image));
            }
            $imageName = md5(Auth::id() . time()) . '.' . $request->image->extension();
            $request->image->move(public_path('img/blog'), $imageName);
            $blog->image = $imageName;
            $blog->save();
        }

        return redirect()->route('v2.meetings.show', $blog->id)->with('success', 'Встреча успешно обновлена!');
    }

    public function destroy($id)
    {
        $blog = Blog::findOrFail($id);
        // Проверяем, что пользователь имеет право удалить эту встречу
        if (Auth::id() !== $blog->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'У вас нет прав для удаления этой встречи');
        }
        
        if ($blog->image && file_exists(public_path('img/blog/' . $blog->image))) {
            unlink(public_path('img/blog/' . $blog->image));
        }

        if ($blog->blogContent) {
            $blog->blogContent->delete();
        }

        $blog->delete();

        return redirect()->route('v2.meetings.index')->with('success', 'Встреча успешно удалена!');
    }

    public function takePart($id)
    {
        $meeting = Blog::findOrFail($id);
        $user = Auth::user();

        $isParticipant = ParticipantActions::where('object_id', $meeting->id)
            ->where('object_name', 'meeting')
            ->where('user_id', $user->id)
            ->exists();

        if ($isParticipant) {
            return back()->with('info', 'Вы уже являетесь участником этой встречи.');
        }
        
        if (!is_null($meeting->quantity)) {
            $currentCount = ParticipantActions::where('object_id', $meeting->id)
                ->where('object_name', 'meeting')
                ->count();
            if ($currentCount >= $meeting->quantity) {
                return back()->withErrors(['error' => 'К сожалению, все места на встречу заняты.']);
            }
        }
        
        ParticipantActions::create([
            "user_id" => $user->id,
            "object_id" => $meeting->id,
            "object_name" => 'meeting'
        ]);

        return back()->with('success', 'Вы успешно записались на встречу!');
    }

    public function addComment(Request $request)
    {
        $request->validate([
            'blog_id' => 'required|exists:blogs,id',
            'comment' => 'required|string|min:1|max:1000',
            'id_com' => 'nullable|exists:blog_comments,id'
        ]);

        // Дополнительная проверка на пустой комментарий
        if (empty(trim($request->comment))) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Комментарий не может быть пустым'
                ], 422);
            }
            return back()->withErrors(['comment' => 'Комментарий не может быть пустым']);
        }

        // Получаем блог для получения blog_content_id
        $blog = Blog::find($request->blog_id);

        $comment = new \App\Models\BlogComment();
        $comment->user_id = Auth::id();
        $comment->blog_id = $request->blog_id;
        $comment->blog_content_id = $blog->blog_content_id;
        $comment->comment = trim($request->comment);
        $comment->id_com = $request->id_com ?? null;
        $comment->status = 1;
        $comment->save();

        // Загружаем пользователя для отображения
        $comment->load('user');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Комментарий успешно добавлен!',
                'comment' => [
                    'id' => $comment->id,
                    'comment' => $comment->comment,
                    'id_com' => $comment->id_com,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'user' => [
                        'id' => $comment->user->id,
                        'firstname' => $comment->user->firstname,
                        'lastname' => $comment->user->lastname,
                        'phone' => $comment->user->phone,
                        'avatar' => $comment->user->avatar
                    ]
                ]
            ]);
        }

        return back()->with('success', 'Комментарий успешно добавлен!');
    }

    public function addLike(Request $request, $id)
    {
        $user = Auth::user();
        $post_id = $id;

        // Удаляем дизлайк, если есть
        if (\App\Models\Dislike::where('user_id', $user->id)->where('post_id', $post_id)->exists()) {
            \App\Models\Dislike::where('user_id', $user->id)->where('post_id', $post_id)->delete();
        }

        // Проверяем, есть ли уже лайк
        if (\App\Models\Like::where('user_id', $user->id)->where('post_id', $post_id)->exists()) {
            \App\Models\Like::where('user_id', $user->id)->where('post_id', $post_id)->delete();
            $action = 'removed';
        } else {
            \App\Models\Like::create([
                'user_id' => $user->id,
                'post_id' => $post_id
            ]);
            $action = 'added';
        }

        $likesCount = \App\Models\Like::where('post_id', $post_id)->count();
        $dislikesCount = \App\Models\Dislike::where('post_id', $post_id)->count();

        return response()->json([
            'success' => true,
            'action' => $action,
            'likes_count' => $likesCount,
            'dislikes_count' => $dislikesCount,
            'user_liked' => \App\Models\Like::where('user_id', $user->id)->where('post_id', $post_id)->exists(),
            'user_disliked' => \App\Models\Dislike::where('user_id', $user->id)->where('post_id', $post_id)->exists()
        ]);
    }

    public function addDislike(Request $request, $id)
    {
        $user = Auth::user();
        $post_id = $id;

        // Удаляем лайк, если есть
        if (\App\Models\Like::where('user_id', $user->id)->where('post_id', $post_id)->exists()) {
            \App\Models\Like::where('user_id', $user->id)->where('post_id', $post_id)->delete();
        }

        // Проверяем, есть ли уже дизлайк
        if (\App\Models\Dislike::where('user_id', $user->id)->where('post_id', $post_id)->exists()) {
            \App\Models\Dislike::where('user_id', $user->id)->where('post_id', $post_id)->delete();
            $action = 'removed';
        } else {
            \App\Models\Dislike::create([
                'user_id' => $user->id,
                'post_id' => $post_id
            ]);
            $action = 'added';
        }

        $likesCount = \App\Models\Like::where('post_id', $post_id)->count();
        $dislikesCount = \App\Models\Dislike::where('post_id', $post_id)->count();

        return response()->json([
            'success' => true,
            'action' => $action,
            'likes_count' => $likesCount,
            'dislikes_count' => $dislikesCount,
            'user_liked' => \App\Models\Like::where('user_id', $user->id)->where('post_id', $post_id)->exists(),
            'user_disliked' => \App\Models\Dislike::where('user_id', $user->id)->where('post_id', $post_id)->exists()
        ]);
    }

    public function cancelPart($id)
    {
        $meeting = Blog::findOrFail($id);
        $user = Auth::user();

        $participation = ParticipantActions::where('object_id', $meeting->id)
            ->where('object_name', 'meeting')
            ->where('user_id', $user->id);

        if ($participation->exists()) {
            $participation->delete();
            return back()->with('success', 'Вы отменили свое участие во встрече.');
        }

        return back()->with('info', 'Вы не были записаны на эту встречу.');
    }
} 