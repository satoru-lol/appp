<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ClubController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $clubs = Club::where('date', '>=', Carbon::now())
            ->orderBy('date', 'asc')
            ->get();

        foreach ($clubs as $club) {
            if (!empty($club->date)) {
                $date = Carbon::parse($club->date);

                $club->date = $date->translatedFormat('d F Y, H:i');
            }
        }
        $isPermittedAdd = false;
        if (!empty($user->permissions)) {
            $perms = json_decode($user->permissions, true);
            if (!empty($perms) && !empty($perms["add"]["club"]) && $perms["add"]["club"] == true) {
                $isPermittedAdd = true;
            }
        }

        return view('club.index', compact('user', 'clubs', 'isPermittedAdd'));
    }

    public function indexOld(Request $request): View
    {
        $user = $request->user();
        $clubs = Club::where('date', '>=', Carbon::now())
            ->orderBy('date', 'desc')
            ->get();

        foreach ($clubs as $club) {
            if (!empty($club->date)) {
                $date = Carbon::parse($club->date);

                $club->date = $date->translatedFormat('d F Y, H:i');
            }
        }
        $isPermittedAdd = false;
        if (!empty($user->permissions)) {
            $perms = json_decode($user->permissions, true);
            if (!empty($perms) && !empty($perms["add"]["club"]) && $perms["add"]["club"] == true) {
                $isPermittedAdd = true;
            }
        }

        return view('club.index', compact('user', 'clubs', 'isPermittedAdd'));
    }

    public function editClub($id, Request $request)
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

        $club = Club::where("id", $id)->first();
        $user = $request->user();
        $products = Product::all();
        if (!empty($request->get("title")) && !empty($club)) {
            $request->validate([
                'date' => 'required',
                'title' => 'required|string|max:255',
                'speakers' => 'required|string|max:255',
                'theory' => 'required|string|max:255',
                'feedback' => 'required|string|max:255',
                'course_category_id' => 'required|integer',
                'times.read' => 'nullable|string|max:255',
                'times.training' => 'nullable|string|max:255',
                //'times.start' => 'nullable|string|max:255',
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

            $club->title = $request->input('title');
            //  $club->course_category_id = $request->input('course_category_id'); // Set course_category_id
            $club->speakers = $request->input('speakers'); // Set course_category_id
            $club->theory = $request->input('theory'); // Set course_category_id
            $club->feedback = $request->input('feedback'); // Set course_category_id
            $club->text = $request->input('desc'); // Set course_category_id
            $club->date = $request->input("date");

            if (!empty($request->get("product_level"))) {
                $club->product_level = $request->get("product_level");
            }

            $club->times = json_encode($request->input('times'));

            if (!empty($request->get("options-outlined")) && $request->get("options-outlined") != "on") {
                $club->pay_method = $request->get("options-outlined");
                $club->practice = "0"; // Set course_category_id
            } else {
                $club->practice = $request->input('practice'); // Set course_category_id
            }

            if (!empty($request->input("uploaded_image"))) {
                $club->image = $request->input("uploaded_image");
            } else {
                if ($request->hasFile('image')) {
                    $imageName = time().'.'.$request->image->extension();
                    $request->image->move(public_path('images'), $imageName);
                    $club->image = $imageName;
                }
            }



            if ($request->hasFile('video')) {
                $videoName = time().date("y-m-d").'.'.$request->video->extension();
                $request->video->move(public_path('images'), $videoName);
                $club->video = $videoName;
            }


            $club->save();

            return redirect("club");
        }

        return view('club.add', compact('club', 'user', 'products'));
    }

    public function show(int $id, Request $request): View
    {
        $user = $request->user();


        $club = Club::where('id', $id)->first();
        $date = Carbon::parse($club->date);
        $club->date = $date->translatedFormat('d F Y, H:i');

        //$content = CourseContent::where('course_id', $course_id)->first();
        $product = Product::where("level", $club->product_level)->first();

        return view('club.show', compact('club', 'user', 'product'));
    }

    public function createShow()
    {
        if (!Auth::check()) {
            return redirect("/");
        }
        if (Auth::user()->group != "admin") {
            return redirect("/");
        }
        $products = Product::all();

        return view('club.add', compact("products"));
    }

    public function store(Request $request)
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

        //Course::where('id', 2)->delete();
        $request->validate([
            'date' => 'required',
            'title' => 'required|string|max:255',
            'speakers' => 'required|string|max:255',
            'theory' => 'required|string|max:255',
            'feedback' => 'required|string|max:255',
            'course_category_id' => 'required|integer',
            'times.read' => 'nullable|string|max:255',
            'times.training' => 'nullable|string|max:255',
            //'times.start' => 'nullable|string|max:255',
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
        $club = new Club();
        $club->title = $request->input('title');
      //  $club->course_category_id = $request->input('course_category_id'); // Set course_category_id
        $club->speakers = $request->input('speakers'); // Set course_category_id
        $club->theory = $request->input('theory'); // Set course_category_id
        $club->feedback = $request->input('feedback'); // Set course_category_id
        $club->text = $request->input('desc'); // Set course_category_id
        $club->date = $request->input("date");

        if (!empty($request->get("product_level"))) {
            $club->product_level = $request->get("product_level");
        }

        $club->times = json_encode($request->input('times'));

        if (!empty($request->get("options-outlined")) && $request->get("options-outlined") != "on") {
            $club->pay_method = $request->get("options-outlined");
            $club->practice = "0"; // Set course_category_id
        } else {
            $club->practice = $request->input('practice'); // Set course_category_id
        }

        if ($request->hasFile('image')) {
            $imageName = time().'.'.$request->image->extension();
            $request->image->move(public_path('images'), $imageName);
            $club->image = $imageName;
        }

        if ($request->hasFile('video')) {
            $videoName = time().date("y-m-d").'.'.$request->video->extension();
            $request->video->move(public_path('images'), $videoName);
            $club->video = $videoName;
        }


        $club->save();

        /*foreach ($request->input('content') as $key => $value) {
            if (!empty($value)) {
                CourseContent::create([
                    'course_id' => $course->id,
                    'text' => json_encode($value)
                ]);
            }
        }*/

        return redirect()->route('club')->with('success', 'Курс успешно добавлен');
    }
}
