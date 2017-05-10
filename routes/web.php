<?php
//for $request->all();
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
	$templates = \App\template::all();
    return view('welcome',['templates' => $templates]);
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/submit', function(){
	return view('submit');
});
Route::post('submit',function(Request $request){
	$validator = Validator::make($request->all(),[
		'name' => 'required|max:255',
		'url' => 'required|max:255',
		'description' => 'required|max:255',
	]);
	if($validator->fails()) {
		return back()->withInput()->withErrors($validator);
	}
	$template = new \App\template;
	$template->name = $request->name;
	$template->url = $request->url;
	$template->description = $request->description;
	$template->save();
	return redirect('/');
});
Route::get('/pdf',function(){
	$snappy = App::make('snappy.pdf');
	//To file
	$html = '<h1>Bill</h1><p>You owe me money, dude.</p>';
	$snappy->generateFromHtml($html, '/tmp/bill-123.pdf');
	$snappy->generate('http://www.github.com', '/tmp/github.pdf');
	//Or output:
	return new Response(
	    $snappy->getOutputFromHtml($html),
	    200,
	    array(
	        'Content-Type'          => 'application/pdf',
	        'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    )
	);
});