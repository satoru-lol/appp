<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Course;
use App\Models\User;
use App\Models\ParticipantActions;
use App\Models\Introduction;
use App\Models\CourseCategory;
use App\Models\CourseContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    public function index(Request $request, int $course_category_id = 0): View
    {

		//Course::truncate();

        $user = $request->user();
        $today = now()->toDateString();

        if($course_category_id) {
            /*if($user && $user->group === 'admin')
                $courses = Course::where('course_category_id', $course_category_id)->orderBy('views')->paginate(15);
            else
                $courses = Course::where('status', 1)->where('course_category_id', $course_category_id)->orderBy('views')->paginate(15);*/
//            $courses = CourseContent::with('content')->where('course_id',$course_category_id)->get();

            $courses = CourseContent::where('date', '>=', $today)->with('course')
                ->where('id', $course_category_id)
                ->orderBy('date')
                ->get();
        } else {
            /*if($user && $user->group === 'admin')
                $courses = Course::orderBy('views')->paginate(15);
            else
                $courses = Course::where('is_hidden', false)->orWhere('is_hidden', null)->orderBy('views')->paginate(15);*/
           /* $courses = CourseContent::where('date', '>', $today)->with('course')
                ->whereRaw('date = (SELECT MIN(date) FROM course_contents WHERE  date > ?)', [$today])
                ->groupBy('course_id')
                ->get();*/

            $courses = CourseContent::where('date', '>=', $today)
                ->with('course')
                ->orderBy('date')
                ->get();
        }

        if($user && $user->group === 'admin')
            $categories = CourseCategory::all();
        else
            $categories = CourseCategory::where('status', 1)->get();

        $isPermittedAdd = true;
		
        $title = 'Курсы для психологов - АЧПП';
        $description = 'Профессиональные курсы и обучающие программы для психологов и психотерапевтов от Ассоциации частнопрактикующих психологов и психотерапевтов.';

        return view('courses.index', compact('courses', 'categories', 'user', 'course_category_id', 'isPermittedAdd', 'title', 'description'));
    }

    public function show(int $course_id, Request $request): View
    {
        $user = $request->user();



//        $course = Course::where('id', $course_id)->first();
        $today = now()->toDateString();
        $course = CourseContent::where('date', '>=', $today)->with('course')
            ->where('id', $course_id)
            ->first();

        if (!$course) {
            abort(404, 'Курс не найден');
        }

        $feedback = Course::where('id', $course->course_id)->first();

        $content = CourseContent::where('course_id', $course_id)->first();

        // Проверяем, существует ли course->course и есть ли у него product_level
        $product = null;
        if ($course->course && isset($course->course->product_level)) {
            $product = Product::where('level', $course->course->product_level)->first();
        }

        $title = $course->course->title . ' - Курс АЧПП';
        $description = 'Подробная информация о курсе "' . $course->course->title . '". ' . \Illuminate\Support\Str::limit(strip_tags($course->course->text), 120);

        return view('courses.show', compact('course', 'feedback','user', 'content', 'product', 'title', 'description'));
    }

    public function showAdd(Request $request): View
    {
        $products = Product::all();

        return view('courses.add', compact('products'));
    }

    public function addCategory(Request $request): RedirectResponse
    {
        $this->checkAccess($request);

        $request->validate([
            'name' => 'required',
            'status' => 'required'
        ]);

        CourseCategory::create([
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

        $CourseCategory = CourseCategory::where('id', $category)->update([
            'name' => $request->name,
            'status' => $request->status
        ]);

        return back()->with('success', __('Изменения успешно сохранены'));
    }
    
    public function destroyCategory(int $category, Request $request): RedirectResponse
    {
        $this->checkAccess($request);

        CourseCategory::where('id', $category)->delete();
        Course::where('course_category_id', $category)->delete();

        return back()->with('success', __('Категория удалена'));
    }

    private function checkAccess(Request $request)
    {
        if($request->user()->group !== 'admin')
            return back()->response('Not access', 403);;
    }
	
	public function store(Request $request)
    {
		
		//Course::where('id', 2)->delete();
        $request->validate([
            'title' => 'required|string|max:255',
            'speakers' => 'required|string|max:255',
            'practice' => 'required|string|max:255',
            'theory' => 'required|string|max:255',
            'feedback' => 'required|string|max:255',
            'course_category_id' => 'required|integer',
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
		$course->course_category_id = $request->input('course_category_id'); // Set course_category_id
		$course->speakers = $request->input('speakers'); // Set course_category_id
		$course->theory = $request->input('theory'); // Set course_category_id
		$course->practice = $request->input('practice'); // Set course_category_id
		$course->feedback = $request->input('feedback'); // Set course_category_id
		$course->text = $request->input('desc'); // Set course_category_id

        $course->times = json_encode($request->input('times'));
        
        if ($request->hasFile('image')) {
			
            $imageName = time().'.'.$request->image->extension();  
            $request->image->move(public_path('images'), $imageName);
            $course->image = $imageName;
        }

        $course->save();

        /*foreach ($request->input('content') as $key => $value) {
            if (!empty($value)) {
                CourseContent::create([
                    'course_id' => $course->id,
                    'text' => json_encode($value)
                ]);
            }
        }*/

        return redirect()->route('courses')->with('success', 'Курс успешно добавлен');
    }
	
	public function webhook(Request $request){
		
		$mrh_pass2 = 'YU401zuWFzOY8kbV8eQW';
		$tm=getdate(time()+9*3600);
		$date="$tm[year]-$tm[mon]-$tm[mday] $tm[hours]:$tm[minutes]:$tm[seconds]";
		$out_summ = $_REQUEST["OutSum"];
		$inv_id = $_REQUEST["InvId"];
		$shp_item = $_REQUEST["Shp_item"];
		$crc = $_REQUEST["SignatureValue"];
		$crc = strtoupper($crc);
		
		$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass2:Shp_item=$shp_item"));
		if ($my_crc !=$crc)
		{
		  echo "bad sign\n";
		  exit();
		}
		
		
	}
	public function paymentsuc(Request $request){
	
		$mrh_pass1 = "pn3Y0a3X1W1sWJlJxKWZ";
		$out_summ = $_REQUEST["OutSum"];
		$inv_id = $_REQUEST["InvId"];
		//$shp_item = $_REQUEST["Shp_item"];
		$crc = $_REQUEST["SignatureValue"];

		$crc = strtoupper($crc);

		$my_crc = strtoupper(md5("$out_summ:$inv_id:$mrh_pass1"));

		// проверка корректности подписи
		if ($my_crc != $crc) {
			echo "bad sign\n";
			exit();
		}
		User::where('id', $inv_id)->increment('balance', $out_summ);
		header('Location: /');
		exit;
	}
}
