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
	//$html = '<h1>Bill</h1><p>You owe me money, dude.</p>';
	$html = 'test';
	//$snappy->generateFromHtml($html, '/tmp/bill-123.pdf');
	//$snappy->generate('http://uwgv.ca/', '/tmp/uwgv.pdf');
	//Or output:
	return Response(
	    $snappy->getOutputFromHtml($html),
	    200,
	    array(
	        'Content-Type'          => 'application/pdf',
	        'Content-Disposition'   => 'attachment; filename="file.pdf"'
	    )
	);

});

Route::get('/word',function(){
	$assembly = 'Microsoft.Office.Interop.Word, Version=15.0.0.0, Culture=neutral, PublicKeyToken=71e9bce111e9429c';
	$class = 'Microsoft.Office.Interop.Word.ApplicationClass';

	$w = new DOTNET($assembly, $class);
	$w->visible = true;


	$fn = __DIR__ . '\\TEMPLATE - RECEIPT (2017) - Print.docx';


	$d = $w->Documents->Open($fn);

	echo "Document opened.<br><hr>";

	$flds = $d->Fields;
	$count = $flds->Count;
	echo "There are $count fields in this document.<br>";
	echo "<ul>";
	//$myMMFields = $d->ActiveDocument->MailMerge->Fields;
	$mapping = setupfields();
	$inputs = seedData();

	foreach ($flds as $index => $f)
	{
	    $f->Select();

	    $key = $mapping[$index];
	    $value = $inputs[$key];
	    //echo $key;
	    //echo $value;

	    $w->Selection->TypeText($value);
	    echo "<li>Merging field $index: $key with value $value</li>";
	}
	echo "</ul>";

	echo "Merging done!<br><hr>";
	echo "Saving. Please wait...<br>";
	//$d->SaveAs(__DIR__ . '\\'.$inputs['Accno'].' - '.$inputs['Name_Part_2'].'docx');
	$d->ExportAsFixedFormat(__DIR__ . '\\'.$inputs['Accno'].' - '.$inputs['Name_Part_2'].'pdf', 17, false, 0, 0, 0, 0, 7, true, true, 2, true, true, false);

	//$d->PrintOut();
	//sleep(3);
	//echo "Done!";

	$w->Quit(false);
	$w=null;
	echo "Done!";




});

Route::get('/word2',function(){
	$assembly = 'Microsoft.Office.Interop.Word, Version=15.0.0.0, Culture=neutral, PublicKeyToken=71e9bce111e9429c';
	$class = 'Microsoft.Office.Interop.Word.ApplicationClass';

	//$w = new DOTNET($assembly, $class);
	//$w->visible = true;


	$fn = __DIR__ . '\\TEMPLATE - RECEIPT (2017) - Print.docx';
	$word = new COM("word.application") or die("Unable to instanciate Word"); 
	$word->Documents->Open($fn) or die("Cannot open file!"); 
	$dataFields = $word->ActiveDocument->MailMerge->fields;
	//$word->ActiveDocument->MailMerge->Execute(false); 

	$mapping = setupfields();
	$inputs = seedData();

	foreach ($dataFields as $index => $df)
	{
	    $df->Select();

	    $key = $mapping[$index];
	    $value = $inputs[$key];
	    echo $key;
	    echo $value;

	    $word->Selection->TypeText($value);

	    //$w->Selection->TypeText($value);
	    //echo "$index  $df";
	}
	$word->ActiveDocument->MailMerge->Execute(false); 
	/*$d = $w->Documents->Open($fn);

	echo "Document opened.<br><hr>";

	$flds = $d->Fields;
	$count = $flds->Count;
	echo "There are $count fields in this document.<br>";
	echo "<ul>";
	//$myMMFields = $d->ActiveDocument->MailMerge->Fields;


	echo "</ul>";

	echo "Merging done!<br><hr>";
	echo "Saving. Please wait...<br>";*/
	//$word->SaveAs(__DIR__ . '\\'.$inputs['Accno'].' - '.$inputs['Name_Part_2'].'docx');
	/*$d->ExportAsFixedFormat(__DIR__ . '\\'.$inputs['Accno'].' - '.$inputs['Name_Part_2'].'pdf', 17, false, 0, 0, 0, 0, 7, true, true, 2, true, true, false);*/

	//$d->PrintOut();
	//sleep(3);
	//echo "Done!";

	//$w->Quit(false);
	//$w=null;
	$word->Quit(false);
	echo "Done!";




});
function setupfields()
{
    $mapping = array();
    $mapping[0] = 'Receipt_Date';
    $mapping[1] = 'Name_Part_1';
    $mapping[2] = 'Name_Part_2';
    $mapping[3] = 'Address_Line_1';
    $mapping[4] = 'Address_Line_2';
    $mapping[5] = 'Address_Line_3';
    $mapping[6] = 'City';
    $mapping[7] = 'State_Or_Province';
    $mapping[8] = 'ZipPostal_Code';
    

    return $mapping;
}
function seedData()
{
    $mapping = array();
    $mapping['Accno'] = '666123';
    $mapping['Receipt_Date'] = '4/24/2017';
    $mapping['Name_Part_1'] = 'Ms. Karen';
    $mapping['Name_Part_2'] = 'Leahy-Trill';
    $mapping['Address_Line_1'] = '1440 Harrop Rd';
    $mapping['Address_Line_2'] = ' ';
    $mapping['Address_Line_3'] = ' ';
    $mapping['City'] = 'Victoria';
    $mapping['State_Or_Province'] = 'BC';
    $mapping['ZipPostal_Code'] = 'V8P 2S6';
    

    return $mapping;
}