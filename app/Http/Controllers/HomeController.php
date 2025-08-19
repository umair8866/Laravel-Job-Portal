<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\JobType;
use App\Models\Vacancy;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::where('status',1)->orderBy('name', 'ASC')->take(8)->get();
        $jobtypes = JobType::where('status',1)->orderBy('name', 'ASC')->take(8)->get();

        $newCategories = Category::where('status',1)->orderBy('name', 'ASC')->get();

        $featuredJobs = Vacancy::where('status',1)->orderBy('created_at', 'DESC')->with('JobType')->where('isFeatured',0)->take(6)->get();
        $latestJobs = Vacancy::where('status',1)->with('JobType')->orderBy('created_at', 'DESC')->take(6)->get();


        return view('front.home',[
            'categories' => $categories,
            'jobtypes' => $jobtypes,
            'featuredJobs' => $featuredJobs,
            'latestJobs' => $latestJobs,
            'newCategories' => $newCategories,
        ]);

        return redirect()->back();


    }
}
