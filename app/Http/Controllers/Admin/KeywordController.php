<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Http\Requests\CategoryRequest;
use App\Http\Requests\KeywordRequest;
use App\Models\Category;
use App\Models\Keyword;
use Illuminate\Http\Request;

class KeywordController extends Controller
{
    public function __construct()
    {
        $this->route = "keywords";
        $this->path = "admin.keywords";
    }

    public function index(Request $request)
    {

        $items = Keyword::latest();
        if (!auth()->user()->is_admin)
            $items->where('user_id',auth()->id());

        if (\request()->filled('title'))
            $items->where('title', 'like', "%$request->title%");

        $items = $items->get();

        return view("{$this->path}.home", ['items' => $items]);


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("{$this->path}.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */


function getData($path)
    {

        $myfile = fopen("$path", "r");

        $data = fread($myfile, filesize("$path"));
        fclose($myfile);

        return $data;
}

    public function store(KeywordRequest $request)
    {
        Keyword::create($request->validated() + ['user_id' => auth()->id()]);
 $path =  public_path('NewsCrawlers/Run.py');

	$title = $request->title;
        $locale = app()->getLocale();
        $path2 =     shell_exec("python3.8 $path -q '$title' -l  $locale");
	$data  =  $this->getData($path2);

//dd(collect($data)[0],json_decode($data));
  dd($data ,json_decode(str_replace('"','\'',collect($data)[0]),true),collect($data)[0]);
//       $data['posts'] = Content::where('from_admin',false)->paginate();
  //    $data =     shell_exec("python3.8 getTrends.py -l ".app()->getLocale()."  >/dev/null &");
        $news = collect(json_decode($data))["{$locale}Trends"];
        return redirect()->back()->with('status', __('cp.create'));
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Keyword $keyword
     * @return \Illuminate\Http\Response
     */
    public function show(Keyword $Keyword)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Keyword $keyword
     * @return \Illuminate\Http\Response
     */
    public function edit(Keyword $keyword)
    {
        return view("{$this->path}.edit")->with([
            'item' => $keyword
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Keyword $keyword
     * @return \Illuminate\Http\Response
     */
    public function update(KeywordRequest $request, Keyword $keyword)
    {
        $keyword->update($request->validated());

        return redirect()->back()->with('status', __('cp.update'));
    }

    public function resetKeywords ( Request  $request ,$keyword_id)
    {

//            $keyword  = Keyword::where('user_id',auth()->id())->findOrFail($keyword_id);
            $keyword  = Keyword::findOrFail($keyword_id);

        $keyword->update(['status'=>1]);

        return redirect()->back()->with('status', __('cp.update'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Keyword $Keyword
     * @return \Illuminate\Http\Response
     */
    public function destroy(Keyword $keyword)
    {
        $keyword->delete();
        return response()->json(['status' => true, 'message' => 'success']);
    }
}
