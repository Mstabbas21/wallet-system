<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use PhpParser\Node\NullableType;

use function PHPUnit\Framework\isNull;

class ServiceController extends Controller
{
      /*'category_id', 'parent_id', 'name', 'description', 
        'price', 'currency', 'points_value', 'duration_days', 'is_active' */
public function store(Request $request)
    {
        //validation
         $validated = $request->validate([
            'category_id'=>'required|exists:categories,id',
            'parent_id'=>'nullable|exists:services,id',//iejene parent-id bdh shuf hed parent 3ndou parent he hiyi kel essa
            'name'=>'required|string|max:255',
            'description'=>'nullable|string|max:255',
            'price' =>'required|numeric|min:0',
            'currency' =>'nullable|in:USD,NGN,LBP',
            'point_value' =>'nullable|numeric|min:1',
            'duration_days' =>'nullable|numeric|min:1',
        ]);
        //ana msskt parent_id 
        //bdh rouh 3l id tb3ou la hed parent-id
        //iza hed id ma 3ndou parent_id ye3ne ok 
        //bas iza 3ndou parent_id ye3ne huwi child fa mmnu333 ykun parent kmn 
       
       //$currentCurrency=$user->currency;
        
        //$parent_id=$id->parent_id;
        //$parent_id->id;
       //haydi lal mother bshuf iza ma fi parent-id huwi bikun parent
       if(is_null($request->parent_id)){
       $validated['price']=0;
       $message="create company mother succesfully";
       } else {
        $parent_id = Service::find($validated['parent_id']);
                //   services/*(hiyi de5l data base) */;
        $id=$parent_id->parent_id;





        if($id!=null){
             
        return response()->json(['message' => 'sorry you cant use child to child please  '],400);
           // $message="sorry you cant use child to child please";
        }
       
$message="child company create successfully and connect with his mother";
            }
            
       

        //proccesing
        $services = Service::create($validated);
       //responce
       return response()->json([
      'status'=>'success',
      'message'=>$message,
      'data'=>$services
       ],201);
 
    }

public function showService($id){
//$services = Service::with(['Service:id,name,price,currency'])->where('Category_id',$id)->get();

//->orWhere('receiver_id',$id)->get();

//$service = Service::select('id', 'name', 'price', 'description')->find($id);

  ///  if (!$service) {
//     return response()->json(['message' => 'Service not found'], 404);
 //   }





 //awl shi 3mlne select lal emm wl moukawinet tab3ine tenne shi ma3 with haydi li rabet enno hiyi function parrent
 //3nde wled wl query hiyi shu bdnh wled ykun 3ndn w ahm shi 7ttlon parentid krml nussl 3l emm
 //telt shi find by id ennou mn id tb3 emm w bas 

$service = Service::select('id', 'name', 'price', 'description')
        ->with(['children' => function($query) {
            // Fiyyak kamen thadded el fields lal wled
            $query->select('id', 'name', 'price', 'description', 'parent_id');
        }])
        ->find($id);

    if (!$service) {
        return response()->json(['message' => 'Service mesh mawjoud'], 404);
    }



    
return response()->json([
'data'=>$service

],200);
 

   

}

public function index()
{
    return Service::all();
}

public function indexMother()
{
    return Service::whereNull('parent_id')->get();
}
}













