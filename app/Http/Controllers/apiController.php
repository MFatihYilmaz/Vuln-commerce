<?php

namespace App\Http\Controllers;

use App\Events\UserCreated;
use App\Models\Address;
use App\Models\Basket;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Token;
use App\Models\User;
use App\Models\Wallet;
use Error;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use PHPUnit\Event\Code\Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

class apiController extends Controller
{


    public function login(Request $request){
        $request->validate([
            'user_name'=>'required|string',
            'password' => 'required|string'
        ]);
        if($request->g_captcha!="true"){
            return response()->json([
                "error"=> "Captcha is not correct"
            ]);
        }
        $creds=$request->only('user_name','password');
        $user = User::where('user_name',$creds["user_name"])->first();
        
        try {
            if (!$token = JWTAuth::attempt($creds)) {
               
                if(!$user){
                    return response()->json(['error' => 'User not found'], 401);
                }
                if (!Hash::check($request->password, $user->password)) {
                    
                    return response()->json(['error' => 'Incorrect password'], 401);
                }
            }
        } catch (JWTException $e) {
            return response()->json(['error' =>'Couldn\'t create token'], 500);
        }
        $serialized=base64_encode(gzencode(serialize($user->toArray())));
        
        // $data = strval(implode($user->toArray()));


        //     $key = hash('sha256',$user->password,true); // 256-bit şifreleme anahtarı
        //     $iv = openssl_random_pseudo_bytes(16);  // Initialization Vector

        //     // Şifreleme
        //     $encrypted = openssl_encrypt($data, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv,$tag);

        //     $hash=hash_hmac('sha256',$encrypted.$iv,$key,true);
        //     // Şifrelenmiş veriyi ve IV'yi birleştirerek sakla
        
        //     $payload=[
        //         'iv'=> base64_encode ($iv),
        //         'value'=>base64_encode($encrypted),
        //         'mac'=>base64_encode($hash)
        //     ];
            
        //     $otherToken=JWT::encode($payload,env('JWT_SECRET'),'HS256');

        //     // Şifre çözme
            
        //     if (!hash_equals(hash_hmac('sha256', $encrypted . $iv, $key, true), $hash)) return null;
        //      $val=openssl_decrypt($encrypted,'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv,$tag);
        
            
        $customClaims=["iss"=>'xxx',"id"=>$user->user_id,"username"=>$user["user_name"],"user_role"=>$user['user_role']];
        $token = JWTAuth::claims($customClaims)->fromUser($user);
        
        return response()->json(['token'=>$token,'tracking'=>$serialized]);
    }

    public function register(Request $request){
    try {
        
       
        $request->validate([

            'user_name' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'surname'=>'required|string|max:255',
            'password' => 'required|string|min:5',
        ]);

    
       $user = User::create([
        'user_id'=>Str::uuid(),
        'user_name' => $request->user_name,
        'name' => $request->name,
        'surname' => $request->surname,
        'password' =>Hash::make($request->password),
    ]);   
    $forwallet=User::select('user_id')->where('user_name',$user->user_name)->first();
    event(new UserCreated($forwallet->user_id));    
    return response()->json([
            'message' => 'User created',
            'status'=>200
        ]);
    } 
    catch (ValidationException $th) {
        return response()->json([
            'message'=>json_encode($th),
            'status'=>500
        ]);
    }
    }

public function changePass(Request $req){
    try{
        $req->validate([
            'pass'=>'string|min:6',
            'repass'=>'string|min:6'
        ]);

        if($req->pass===$req->repass){
            $token=Token::where('token',$req->query('token_id'))->first();
            $user=User::where('user_name',$token->user_name)->first();
            if($user){
                $user->password=Hash::make($req->pass);
                $user->save();
                event(new PasswordReset($user));
                return response()->json([
                    'message'=>'Password successfully changed',
                    'status'=>200
                ],200);
            }
        }
        return response()->json([
            'message'=>'Password doesnt match'
         ]);
    }catch (Exception $e){
        return response()->json([
           'message'=>'Password must be min 6 char'
        ]);
    }
}

public function swagger(){
    $remoteImage = public_path().'/swagger.json';
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $remoteImage);
    header("Content-type: {$mimeType}");
    readfile($remoteImage);
}
  

public function resetPass(Request $req){
    
   try{
    $val=DB::table('users')->select('*')->whereRaw("user_name='".$req->user_name."'")->first();
    if($val){
        $token=Token::create([
            'token'=>Str::random(16),
            'user_name'=>$req->user_name
        ]);
        $createdToken = Token::find($token->id);
        return response()->json([
            'link'=>'http://'.$req->getHttpHost().'/reset.html?token='.$createdToken->token,
        ]);
    }
    return response()->json([
        'message'=>'User not found'

    ]);
    }catch(Exception $e){
        return response()->json([
            'message'=>'User not found'
        ]);
    }

}
    public function getUser(Request $req)
    {   
        try{
            
             $user=unserialize(gzdecode(base64_decode($req->query('userTracking'))));

            if($user){
                
                return response()->json([
                    'user'=>$user
                ]);
            }
            
            return response()->json([
                'error'=>'Error occured',
                'user'=>$user
            ]);
            
        }catch(JWTException $e){
            return response()->json([
                "error"=>"Token not found or expired ",
                
            ],401);
        }
       
      
        
    }

    public function updateUser(Request $req){
        try {
            $user=JWTAuth::parseToken()->getPayload();
            DB::statement("UPDATE users SET user_uname =$req->username,user_email=$req->email, user_pass =$req->pass WHERE id =$user[id]"); 
        } catch (\Throwable $th) {
        return 1;
        }

    }

    public function removeUser($user_id){
        
        try {
            $payloads=JWTAuth::parseToken()->getPayload();
        
            if($payloads['user_role']==1){
                User::where('user_id',$user_id)->delete();
                return response()->json([
                    'message'=>'Success'
                ]);
            }
            return response()->json([
                'message'=>'Forbidden'
            ],403);
        } catch (\Throwable $th) {
            return response()->json([
                'message'=>'Error occured'
            ]);
        }
        
    }

    public function getAllUsers(){
        return response()->json([
            'users'=>User::all()
        ]);
    }

    

    // PRODUCT SECTION
    public function listproducts(){
        return Product::all();
        
    }

    public function getProductDetails($id) {
        $id=urldecode($id);
        $val=DB::table('products')->select('*')->whereRaw("product_id='".$id."'")->get();
        
        if(count($val)){
            return response()->json([
                'products'=>$val
            ]    
            );
        } 

        return response()->json([
            'products'=>''
        ]    
        );        
             
    }

    public function cancelProduct(){
        $user=auth()->user();
        Basket::select('orders')->where('user_id',$user->user_id)->where('basket_status',0);
        return response()->json([
            'message'=>'ok'
        ]);
    }   
    
    public function getProductsInBasket() {
        $user=auth()->user();
        $products=Basket::select('orders')->where('user_id',$user->user_id)->where('basket_status',0);
        return response()->json([
            'products'=>$products
        ]);    
    
    }

    public function buyProduct(Request $req){

        $user=auth()->user();
        $eluser=User::find($user->user_name);
        $wallet=$eluser->wallet;
       
        if($wallet->deposit<$req->amount){
            return response()->json([
                'message'=>'insufficent balance'
            ]);
        }
        $coupon=Coupon::where('user_id',$user->user_id)->where('used',1)->first();
        if($coupon){
            $coupon->perm=1;
            $coupon->save();
        }
        $wallet->deposit-=$req->amount;          
        $basket=$eluser->basket->where('basket_status',0)->first();
        $basket->basket_status=1;
        $wallet->save();
       
        $basket->save();
        return response()->json([
            'message'=>'ok'
        ]);
        
       
    }
      
 /**--------------------------------------------------------------------------- /*/
    public function getCategories(){
        return Product::select('category')->groupBy('category')->get();
    }

  

    public function getWallet(){
        $user=auth()->user();
        $wal=Wallet::select('deposit')->where('user_id',$user->user_id);
        return response()->json([
            'deposit'=>$wal
        ]);
    }

    public function loadDeposit(Request $req){
        $user=auth()->user();
        $result=DB::table("wallets")->select('*')->where('user_id',$user->user_id)->first();
        if(!$result){
            $insertion=DB::insert("INSERT into wallets (user_id,deposit) VALUES ('$user->user_id',$req->deposit)");
 
            return response()->json([
                'status'=>$insertion?'Success':'Fail'
            ]);
        }

        try{
            if(is_numeric($req->deposit)){
                DB::table("wallets")->where('user_id',$user->user_id)->increment('deposit',$req->deposit);
                return response()->json([
                    'status'=>'Success'
                    ]) ;
            }
            return response()->json([
                'status'=>'Error occured'
                ]) ;
        }catch(Throwable $th){
           
        }
        
    }

    public function getMessage(){
        $postData =file_get_contents('php://input');
        $xml=simplexml_load_string($postData,'SimpleXMLElement',LIBXML_NOENT | LIBXML_DTDLOAD);
        return response()->json([
            'status'=>200,
            'message'=>'Thanks '.strval($xml->from).' your ticket is under review'
        ]);
    }
    public function clearBasket(){
        
        try {
            $user=auth()->user();
            $deleted=Basket::where('user_id',$user->user_id)->where('basket_status',0)->delete();
            return response()->json([
                'status'=>200,
                'message'=>'Basket is cleared'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
               'status'=>403,
               'message'=>'Error occured' 
            ]);

        }
        
        
    }


    public function setAddress(Request $req,$username){
        $user=DB::table("users")->select("*")->where('user_name',$username)->first();
        $result=DB::table("addresses")->select('*')->where('user_id',$user->user_id)->first();
        if(count((array)$result)==0){
            $val=$user->user_id;
            Address::create([
                'user_id'=>$val,
                'address_header'=>$req->address_header,
                'address'=>$req->address
            ]);
            return response()->json([
                "message"=>"Address added"
            ],200);
        }
        $control=DB::table("addresses")->where("user_id",$user->user_id)->update(['address_header'=>$req->address_header,'address'=>$req->address]);
        return $control ? response()->json(["message"=>"Address updated"],200):response()->json(["message"=>"Address not updated"],400);
    }

    public function getAddress($username){
        $user=auth()->user();
        $user_id=User::select('user_id')->where('user_name',$username)->first();
        $resp=Address::where('user_id',$user_id->user_id)->first();
        if($resp){
            return response()->json([
                'addresses'=>$resp
            ],200);
        }
        return response()->json([
            'addresses'=>""
        ],200);
        
    }

    public function getOrdersDetail($id){
        $user=auth()->user();
        $valueBasket=Basket::where('basket_id',$id)->where('basket_status',1)->get();
       
        if ($valueBasket->isNotEmpty()){
            $orders=$valueBasket[0]['orders'];
            $productIds=array_map(function($item){
                return $item[0];
            },$orders);
            $products=Product::whereIn('product_id',$productIds)->get();
            $date=Carbon::parse($valueBasket[0]['basket_date'])->toDateString();
            $valueBasket[0]['basket_date']=$date;
            return response()->json([
                'orders'=>$valueBasket,
                'products'=>$products
            ]);
            }
            return response()->json([
                'orders'=>'',
                'products'=>'',
                'message'=>'Order not found'
            ]);
       
       
    }

    
    public function getAllOrders(){
        $user=auth()->user();
        $basket=Basket::where('user_id',$user->user_id)->where('basket_status',1)->get();
        if($basket->isNotEmpty()){
        $date=Carbon::parse($basket[0]['basket_date'])->toDateString();    
        $basket[0]['basket_date']=$date;
            return response()->json([
                'orders'=>$basket,
            ]);
        }
        return response()->json([
            'orders'=>$basket,
            'message'=>'Order not found'
        ]);
    }

    public function search($search){
        
        try {
            $results = DB::table("products")->where('product_name', 'like', '%'.$search.'%')->get();
            if($results->isNotEmpty()){
                return response()->json([
                    "products"=>$results
                    ]);
            }
            return response()->json([
                'products'=>""
            ]);
        } catch (Error $th) {
            return response()->json([
                'error'=>$th,
            ]);
        }
        
        
    }

    private function ordersControl ($orders,$neworder) {
        $matched=false;
        foreach ($orders as &$item) {
            if($item[0]===$neworder[0]){
                $sum=intval($item[1])+intval($neworder[1]);
                $item[1]=strval($sum);
                $matched=true;
                break;
            }
        }
        if($matched===false){
            $orders[]=$neworder;
        }
        return $orders;
        
    }
    public function updateBasket(Request $req){
       try{ 
        $req->validate([
            "product_id"=>"required|string|max:255",
            'quantity'=>'int|max:10'
        ]);
        $user=auth()->user();
        $basket=Basket::where('user_id',$user->user_id)->where('basket_status',0)->first();
        $orders=$basket->orders;
        foreach($orders as &$order){
            if($order[0]==$req->product_id){
                $order[1]=$req->quantity;
                break;
            }
        }
        $total=array_reduce($orders,function($carry,$item){
                
            $carry+=$item[1]*$item[2];
            return $carry;

        });
        $basket->basket_total=$total;
        $basket->orders=$orders;
        $basket->save();
        return response()->json([
            'message'=>'ok'
        ]);
    }catch(ValidationException $e){
        return response()->json([
            'message'=>'Max amount exceed'
        ]);
    }
    }   
    public function addBasket(Request $req){
        $req->validate([
            "product_id"=>"required|string|max:255",
            "quantity"=>"required|numeric|min:0"
        ]);
        $payloads=auth()->user();
        $product=Product::where('product_id',$req->product_id)->first();

        $array=[
           $product['product_id'],$req->quantity,$product['product_price']
        ];
        $basketControl=Basket::select('*')->where('user_id','=',$payloads->user_id)->where('basket_status',0)->first();
        
        if($basketControl){
            $order=$basketControl->orders;
           
            $order=$this->ordersControl($order,$array);
            $total=array_reduce($order,function($carry,$item){
                
                $carry+=$item[1]*$item[2];
                return $carry;

            });
            Basket::where('basket_id', $basketControl->basket_id)
            ->update([
                'orders' => $order,
                'basket_total'=>$total
            ]);
        
        }else{
            Basket::create([
                'user_id'=>$payloads['user_id'],
                'orders'=>array($array),
                'basket_total'=>$product['product_price']*$req->quantity,
            ]);
        }
      
        return response()->json([
            'message'=>'Added to basket'
        ]);
        
    }
    

    public function getBasket(){
            $user=auth()->user();
            
        try {
            $valueBasket=Basket::where('user_id',$user->user_id)->where('basket_status',0)->first();
            if ($valueBasket){
            $orders=$valueBasket->orders;
            $productIds=array_map(function($item){
                return $item[0];
            },$orders);
            $products=Product::whereIn('product_id',$productIds)->get();
            
          
                return response()->json([
                    'basket'=>$valueBasket,
                    'products'=>$products
                ]);
            }
            return response()->json([
                'basket'=>''
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'basket'=>'Error '.$th
            ]);
        }
       
        // $productIds = array_map(function ($item) {
        //     return $item[0];
        // }, $orderData);
      
        // $products = Product::whereIn('product_id', $productIds)->get();
        // return response()->json([
        //     'data'=>$products
        // ]);
    }

    public function fileUpload(Request $req){
        if ($req->hasFile('file')) {
            $file = $req->file('file');
            $fileName=$file->getClientOriginalName();
            $file->move(public_path().'/uploads/',$fileName);
            return response()->json([
                'message'=>'success'
            ]);

        }
        return response()->json([
                'message'=>'File'
            ]);
    }

    public function getFile(Request $req){
        $remoteImage = public_path().'/img/'.$req->query('file');
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $remoteImage);
        header("Content-type: {$mimeType}");
        readfile($remoteImage);
            
            return response()->json([
                'data'=>'ok'
            ]);
        }
    
    private function controlURL($url){
            $blackedList=['127.1','127.0.1','127.000.000.1','localhost'];
            if(str_starts_with($url,'http://127.0.0.1:5000')||in_array(parse_url($url,PHP_URL_HOST),$blackedList)){
                return false;
            }
            return true;
    }
    public function checkStock(Request $req){
        $validator= Validator::make($req->all(),[
            'stockApi'=>'required|url'
        ]);
        if($validator->fails()){
            return response()->json([
                'error'=>'URL SCHEMA IS NOT CORRECT'
            ],403);     
        }
        $apiUrl=$req->stockApi;
        if($this->controlURL($apiUrl)){
        try{
            
            $response = Http::get($apiUrl);
            return response()->json([
                'stock'=>$response->body()
            ]);
        }catch(Error $e){
            return response()->json([
                'error'=>'Error occured'
            ]);
        }
        }
        return response()->json([
            'error'=>'ACCES DENIED'
        ]);
        
    }
    
   
   public function couponCode(Request $req){
    $user=auth()->user();
    
    try{

        $code=Coupon::where('user_id',$user->user_id)->first();
        if(!$code){
            $code=Coupon::create([
                'user_id'=>$user->user_id
            ]);
        }
        if($req->coupon=='SHOP20' && !$code->used){
        $userBasket=Basket::where('user_id',$user->user_id)->where('basket_status',0)->first();
        if($userBasket){
            $userBasket->basket_total=$userBasket->basket_total*8/10;
            $userBasket->save(); 
            $code->used=true;
            $code->save();  
            return response()->json([
                'message'=>'Code applied'
            ]);
       
        }
        return response()->json([
            'message'=>'Basket is empty'
        ]);    
           
          
          
        }
        
        return response()->json([
            'message'=>'Code used or not valid'
        ]);
    }catch(Exception $e){
        return response()->json([
            'message'=>'Error'.$e
        ]);
    } 
   }
   private function useCode($user){
   
   }

   private function basketCoupon($user){
        
   }

   public function removeCouponCode(){
    $user=auth()->user();
    $coupon=Coupon::where('user_id',$user->user_id)->where('perm',0)->first();
    $basket=Basket::where('user_id',$user->user_id)->where('basket_status',0)->first();
    
    if(!empty($basket->orders) && $coupon){
        $orders=$basket->orders;
        $total=array_reduce($orders,function($carry,$item){
                    
            $carry+=$item[1]*$item[2];
            return $carry;
        });
    
        $coupon->used=0;
        $coupon->save();
        $basket->basket_total=$total;
        $basket->save();
        return response()->json([
            'message'=>'OK'
        ],200);
    }
    return response()->json([
        'message'=>''
    ],200);
}

}


