<?php

namespace App\Http\Controllers;
use App\Models\Brand;
use App\Models\Currency;
use App\Models\User; 
use App\Models\Admin; 
use App\Models\Subcat;
use App\Models\ProductCategory;
use App\Models\GeneralSetting;
use App\Models\Offer;
use App\Models\Order;
use App\Models\Seller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Language;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use App\Models\Frontend;
use App\Traits\SupportTicketManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Mail;

class SiteController extends Controller
{
    use SupportTicketManager;

    public function __construct(){
        $this->activeTemplate = activeTemplate();
    }
    
      
       public function orderemail()
      {
          
     $porder = Order::wherePaymentStatus(0)->wherePaymentReminder(0)->get();
     //return $order;
     foreach($porder as $order)
     {
    $customer = User::whereId($order->user_id)->first();
             $name = $customer->username;
             $email = $customer->email;
             $text = "You have a pending order on BudMall with transaction number ".$order->order_number." Please login to your customer's account on ".url('/')." to complete the payment process. We look forward to delivery your order soon. ";
              
             $message = $text;
             $subject = "Pending Order";
             
              try {
                $data = array("name"=>$name, "content" => $message, "subject" => $subject, "otp" => "Thank You");
            
                Mail::send("emails.otp", $data, function($message) use ($name, $email, $subject) {
                    $message->to($email, $name)->subject($subject);
                    $message->from(env("MAIL_USERNAME"),$subject);
                    });
                } catch (Exception $e) {
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                
                $headers .= 'From: <hi@budmall.ng>' . "\r\n";
                mail($email,$subject,$message,$headers);
                } 
                $order->payment_reminder += 1;
                $order->save();
            }
         
      return 2;
         
        
        
       
      }

    
      public function testemail()
      {
          
    try {
    //Server settings
    $name = "Bolade";
    $email = "kayode@bud.africa";
    $message = "This is a test mail";
    $subject = "Test Mail";
    $data = array("name"=>$name, "content" => $message, "subject" => $subject);
    Mail::send("emails.mail", $data, function($message) use ($name, $email, $subject) {
        $message->to($email, $name)->subject($subject);
        $message->from(env("MAIL_USERNAME"),$subject);
        });
    } catch (Exception $e) {
         
    }
        
       
      }
      
      
    
      
      
        public function migratesubcat()
      {
          
     $sub = ProductCategory::get();
     foreach($sub as $data)
     {
      $prod =  Product::whereId($data->product_id)->where('id','>',952)-> first();
      if($prod)
      {
         if($data->category_id  > 0)
         {
          if($data->category_id < 30)
          {
          $prod->category = $data->category_id;
          $prod->save();
          }
         
         if($data->category_id > 30)
          {
          $prod->subcategory = $data->category_id;
          $prod->save();
          }
          
         }
        
      }
       
        } 
         
      return 2;
         
        
        
       
      }
      
 /*   public function migratesubcathh()
      {
          
     $sub = Product::get();
     foreach($sub as $data)
     {
       
        $category = new ProductCategory();
        $category->product_id = $data->id;
        $category->category_id = $data->category;
        $category->save();
       
        $category = new ProductCategory();
        $category->product_id = $data->id;
        $category->category_id = $data->subcategory;
        $category->save();
      
   
        } 
         
      return 2;
         
        
        
       
      }*/
      
      public function migratecustomer()
      {
          
     $customers = Customer::get();
     foreach($customers as $data)
     {
        $registered = User::whereEmail($data->email)->first();
        
        if(!$registered)
        {
        
        $general = GeneralSetting::first();
        //User Create
        $user = new User();
        $user->firstname = $data->name;
        $user->lastname = null;
        $user->email = strtolower(trim($data->email));
        $user->password = Hash::make($data->irrelivant);
        $user->username = trim($data->email);

        $user->country_code = null;

        $user->mobile = $data->phone;

        $user->address = [
            'address' => '',
            'state' => '',
            'zip' => '',
            'country' => null,
            'city' => ''
        ];
        $user->status = 1;
        $user->ev = 1;
        $user->sv = 1;
        $user->ts = 0;
        $user->tv = 1;

        $user->save();
       
        } 
         
        
     }
      return 2;
         
        
        
       
      }
      
       public function staffemail()
      {
          
     $staff = Admin::whereType(1)->get();
     foreach($staff as $data)
     {
        
    $text = "Welcome to your new Staff Account on BudMall V2. Your Account Username Is: ".$data->username."  Please use the password below to login to your staff account.";
            $name = $data->username;
            $email = $data->email;
            $message = $text;
            $subject = "Account Activation";
             
              try {
                $data = array("name"=>$name, "content" => $message, "subject" => $subject, "otp" => "staff123");
            
                Mail::send("emails.otp", $data, function($message) use ($name, $email, $subject) {
                    $message->to($email, $name)->subject($subject);
                    $message->from(env("MAIL_USERNAME"),$subject);
                    });
                } catch (Exception $e) {
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                
                $headers .= 'From: <hi@budmall.ng>' . "\r\n";
                mail($email,$subject,$message,$headers);
                }
            }
         
      return "Mail Sent";
         
        
        
       
      }

    public function index(){

        $topSellingProducts = Product::topSales(9);
        $featuredProducts   = Product::active()->featured()->where('status', 1)->inRandomOrder()->take(6)->get();
        $latestProducts     = Product::active()->latest()->where('status', 1)->inRandomOrder()->take(12)->get();
        $featuredSeller     = Seller::active()->featured()->whereHas('shop')->with('shop')->inRandomOrder()->take(16)->get();
        $topBrands          = Brand::top()->inRandomOrder()->take(16)->get();
        $pageTitle          = 'Store Front';
        $catego = Category::latest()->where('id','!=',27)->get();
        $superpack = Category::latest()->whereId(27)->get();

        $offers             = Offer::where('status', 1)->where('end_date', '>', now())
                                ->with(['products'=> function($q){ return $q->whereHas('categories')->whereHas('brand');},
                                    'products.reviews'
                                ])->get();

        return view($this->activeTemplate . 'home', compact('superpack','catego','pageTitle', 'offers', 'topSellingProducts','featuredProducts','featuredSeller','topBrands', 'latestProducts'));
    }

    public function contact()
    {
        $pageTitle = "Contact Us";
        return view($this->activeTemplate . 'contact',compact('pageTitle'));
    }

    public function contactSubmit(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:191',
            'email' => 'required|max:191',
            'subject' => 'required|max:100',
            'message' => 'required',
        ]);
        $request->merge(['priority' => 2]);
        $ticket = $this->storeTicket($request, null, null);
        return redirect()->route('ticket.view.guest', $ticket['ticket'])->withNotify($ticket['message']);
    }

    public function changeLanguage($lang = null)
    {
        $language = Language::where('code', $lang)->first();
        if (!$language) $lang = 'en';
        session()->put('lang', $lang);
        return $lang;
        return redirect()->back();
    }

      public function changecur($lang = null)
    {
        $language = Currency::where('id', $lang)->first();
        if (!$language) $lang = 'USD';
        session()->put('syscurrency', $lang);
        return redirect()->back();
    }

   public function addSubscriber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if($validator->fails()) {
            return response()->json($validator->errors());
        }

        $if_exist = Subscriber::where('email', $request->email)->first();
        if (!$if_exist) {
            Subscriber::create([
                'email' => $request->email
            ]);
            
             $name = "Valued Customer";
             $email = $request->email;
             $text = "You have successfully subscribed for Email Notifications from BudMall. ";
              
             $message = $text;
             $subject = "Email Subscription";
              try {
                $data = array("name"=>$name, "content" => $message, "subject" => $subject, "otp" => "Thank You");
            
                Mail::send("emails.otp", $data, function($message) use ($name, $email, $subject) {
                    $message->to($email, $name)->subject($subject);
                    $message->from(env("MAIL_USERNAME"),$subject);
                    });
                } catch (Exception $e) {
                
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                
                $headers .= 'From: <hi@budmall.ng>' . "\r\n";
                mail($email,$subject,$message,$headers);
                } 
                
            return response()->json(['success' => 'Email Subscription Was Successful']);
        } else {
            return response()->json(['error' => 'Already Subscribed']);
        }
    }
    
      public function pages($slug)
    {
      $pageTitle = $slug;
      $info = json_decode(json_encode(getIpInfo()), true);
      $mobile_code = @implode(',', $info['code']);
      $countries = json_decode(file_get_contents(resource_path('views/partials/country.json')));
      
       return view($this->activeTemplate.$slug,compact('pageTitle','info','mobile_code','countries'));
    }

    public function pageDetails($id,$slug)
    {
        $pageDetails  = Frontend::findOrFail($id);
        $pageTitle = $pageDetails->data_values->pageTitle;
       return view($this->activeTemplate.'page_details',compact('pageTitle','pageDetails'));
    }


    public function cookieAccept(){
        header('Access-Control-Allow-Origin:  *');
        session()->put('cookie_accepted',true);
        return response()->json(['success' => 'Cookie has been accepted']);
    }

    public function placeholderImage($size = null){
        $imgWidth = explode('x',$size)[0];
        $imgHeight = explode('x',$size)[1];
        $text = $imgWidth . '×' . $imgHeight;
        $fontFile = realpath('assets/font') . DIRECTORY_SEPARATOR . 'RobotoMono-Regular.ttf';
        $fontSize = round(($imgWidth - 50) / 8);
        if ($fontSize <= 9) {
            $fontSize = 9;
        }
        if($imgHeight < 100 && $fontSize > 30){
            $fontSize = 30;
        }

        $image     = imagecreatetruecolor($imgWidth, $imgHeight);
        $colorFill = imagecolorallocate($image, 100, 100, 100);
        $bgFill    = imagecolorallocate($image, 255, 255, 255);
        imagefill($image, 0, 0, $bgFill);
        $textBox = imagettfbbox($fontSize, 0, $fontFile, $text);
        $textWidth  = abs($textBox[4] - $textBox[0]);
        $textHeight = abs($textBox[5] - $textBox[1]);
        $textX      = ($imgWidth - $textWidth) / 2;
        $textY      = ($imgHeight + $textHeight) / 2;
        header('Content-Type: image/jpeg');
        imagettftext($image, $fontSize, 0, $textX, $textY, $colorFill, $fontFile, $text);
        imagejpeg($image);
        imagedestroy($image);
    }
    
     public function triggeranalitics()
     {

   return;
    }

}
