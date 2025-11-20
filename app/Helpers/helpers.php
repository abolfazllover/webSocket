<?php



use App\Models\SettingModel;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

function getCountriesInPersian() {
    return [
        "آرژانتین", "آذربایجان", "آلمان", "آفریقای جنوبی", "آلبانی", "امارات متحده عربی", "اندونزی", "ایالات متحده آمریکا",
        "ایران", "ایتالیا", "اسپانیا", "استرالیا", "افغانستان", "اتریش", "برزیل", "بنگلادش", "بلژیک", "پاکستان",
        "پرتغال", "تایلند", "ترکیه", "چین", "دانمارک", "روسیه", "ژاپن", "فرانسه", "فنلاند", "قزاقستان", "کانادا",
        "کره جنوبی", "کرواسی", "کویت", "لبنان", "مکزیک", "مصر", "نروژ", "هند", "هلند", "یونان",
        "آنگولا", "ارمنستان", "بحرین", "بولیوی", "جمهوری چک", "سری‌لانکا", "سوئد", "سوئیس", "عربستان سعودی", "عمان",
        "فیلیپین", "قطر", "کلمبیا", "کنیا", "لهستان", "مراکش", "مالزی", "نیجریه", "نیوزیلند", "ونزوئلا",
        "ویتنام", "یمن", "آندورا", "آنتیگوا و باربودا", "آنگویلا", "آروبا", "جزایر آلند", "بوسنی و هرزگوین",
        "باربادوس", "بورکینا فاسو", "بلغارستان", "بوروندی", "بنین", "سنت بارثلمی", "برمودا", "برونئی",
        "بوتان", "جزیره بووه", "بوتسوانا", "بلاروس", "بلیز", "جزایر کوکوس", "جمهوری دموکراتیک کنگو",
        "جمهوری آفریقای مرکزی", "جمهوری کنگو", "سوئیس", "ساحل عاج", "جزایر کوک", "شیلی", "کامرون", "کاستاریکا",
        "کوبا", "کیپ ورد", "کوراسائو", "جزیره کریسمس", "جزایر فالکلند", "جزایر فارو", "فیجی", "گرجستان",
        "غنا", "گرنادا", "گواتمالا", "گینه", "گینه بیسائو", "گینه استوایی", "هائیتی", "هندوراس",
        "هنگ کنگ", "مجارستان", "ایسلند", "ایرلند", "عراق", "اردن", "قزاقستان", "کنیا", "قرقیزستان",
        "لائوس", "لتونی", "لسوتو", "لیبریا", "لیختن‌اشتاین", "لیتوانی", "لوکزامبورگ", "ماکائو",
        "مقدونیه شمالی", "مالاوی", "مالدیو", "مالی", "مالت", "جزایر مارشال", "موریس", "مکزیک",
        "مولداوی", "موناکو", "مغولستان", "مونته‌نگرو", "موزامبیک", "میانمار", "نامیبیا", "نائورو",
        "نپال", "نیکاراگوئه", "نیجر", "مقدونیه", "پالائو", "پاناما", "پاپوآ گینه نو", "پاراگوئه",
        "پرو", "رومانی", "رواندا", "سان مارینو", "سائوتومه و پرنسیپ", "سنگال", "صربستان", "سیشل",
        "سیرالئون", "اسلواکی", "اسلوونی", "جزایر سلیمان", "سومالی", "سودان", "سورینام", "اسواتینی",
        "سوریه", "تاجیکستان", "تانزانیا", "تیمور شرقی", "توگو", "تونگا", "ترینیداد و توباگو", "تونس",
        "ترکمنستان", "تووالو", "اوگاندا", "اوکراین", "امارات متحده عربی", "بریتانیا", "اروگوئه", "ازبکستان",
        "واتیکان", "زامبیا", "زیمبابوه"
    ];
}

function generate_file_name($fine_name){
    $secret_key = "my_super_secret_key";
    $filename = base64_encode($fine_name);
    $expires = time() + 60000; // لینک تا ۵ دقیقه دیگه معتبره

    $hash = hash_hmac('sha256', $filename . $expires, $secret_key);
    $link = "?file=".$filename."&expires=".$expires."&hash=".$hash;
//   $link = $filename."/".$expires."/".$hash;
    return $link;
}

function get_setting($name){
    return SettingModel::where('name',$name)->first()->val;
}

function base_domain(){
    return parse_url(config('app.url'), PHP_URL_HOST);
}
function domain(){
    return config('app.url');
}

function logo_address()
{
    return route('home')."/img/logo.png";
}
function min_logo_address()
{
    return route('home')."/img/min_logo.png";
}


 function parsi_number_to_en($string)
{
    $persinaDigits1= array('۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹');
    $persinaDigits2= array('٩', '٨', '٧', '٦', '٥', '٤', '٣', '٢', '١', '٠');
    $allPersianDigits=array_merge($persinaDigits1, $persinaDigits2);
    $replaces = array('0','1','2','3','4','5','6','7','8','9','0','1','2','3','4','5','6','7','8','9');
    return str_replace($allPersianDigits, $replaces , $string);
}

function normal_array_parsi_number_to_en($array): array
{
    foreach ($array as $key=>$item){
        if (is_array($item)){
            foreach ($item as $new_key=>$new_item){
                $array[$key][$new_key]=parsi_number_to_en($new_item);
            }
        }else{
            $array[$key]=parsi_number_to_en($item);
        }
    }
    return $array;
}

function error_access(){
    return view('errors.no_access');
}

function saveCookie($name, $value, $minutes = 60) {
    $cookie = Cookie::make($name, $value, $minutes);
    Cookie::queue($cookie);
}

function getCookie($name) {
    return Cookie::get($name);
}

function encode_string($string){
    return Illuminate\Support\Facades\Crypt::encryptString($string);
}
function decode_string($string){
    try {
        return \Illuminate\Support\Facades\Crypt::decryptString($string);
    }catch (Exception $exception){
        return abort(403,$exception->getMessage());
    }
}

function success_json($result)
{
    return response()->json(['status'=>'success','result'=>$result]);
}

function error_json($msg)
{
    return response()->json(['status'=>'error','msg'=>$msg]);
}

function liara_disk($file_name){
    return Storage::disk('liara')->get($file_name);
}

function liara_delete($file_name){
    return Storage::disk('liara')->delete($file_name);
}

function liara_link($file_name){
    $file_name=\Illuminate\Support\Str::replace('/','*',$file_name);
    return route('download_file',$file_name);
}

function image_is_server($url){
    if (Str::contains($url,'localhost') or Str::contains($url,'bluema')){
        return true;
    };
    return false;
}

function liara_upload($file){
    $f_name=$file->getClientOriginalName();
    return Storage::disk('liara')->put($f_name,$file);
}

function liara_downloadAndUpload($url) {
    // ایجاد یک کلاینت Guzzle برای دانلود فایل
    $client = new Client();

    try {
        // دانلود فایل از URL
        $response = $client->get($url);

        // گرفتن محتوا و نوع فایل
        $fileContent = $response->getBody()->getContents();
        $contentType = $response->getHeader('Content-Type')[0];

        // ایجاد نام فایل موقت
        $fileName = uniqid() . '.' . explode('/', $contentType)[1];
        $tempPath = storage_path('app/temp/' . $fileName);

        // ذخیره فایل موقت در دیسک لوکال
        File::ensureDirectoryExists(storage_path('app/temp'));
        File::put($tempPath, $fileContent);

        // ارسال فایل به تابع liara_upload
        $file = new \Illuminate\Http\UploadedFile(
            $tempPath,
            $fileName,
            $contentType,
            null,
            true // برای اینکه فایل آپلود شده به صورت تست باشد
        );

        // آپلود فایل در دیسک liara
        $result = liara_upload($file);

        // حذف فایل موقت
        File::delete($tempPath);

        return $result;
    } catch (\Exception $e) {
        // مدیریت خطا
        return $url;
    }
}

function convertPersianNumbersToEnglish($input) {
    $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    return str_replace($persianNumbers, $englishNumbers, $input);
}

function create_paginate($paginate){
    echo "<div class='ui divider hidden'></div>";
    return $paginate->appends(request()->query())->links('vendor.pagination.semantic-ui');
}

function filter_modal($array,$model){
    foreach ($array as $key=>$val){
        if ($val!=null){
            $model=$model->where($key,'like',"%$val%");
        }
    }
    return $model->paginate(10000);
}

function generate_param_pdf(\Illuminate\Http\Request $request,$method): array
{
    $a=[];
    $a[]=$method;
    foreach ($request->all() as $key=>$item){
        $a[$key]=$item ?? '';
    }
    return $a;
}

function filter_search_html($name,$is_pdf=null){
    if ($is_pdf==null){
        return view('components.filtering_tbl.search_input')->with([
            'name'=>$name,
        ]);
    }else{
        return  '';
    }
}

function isValidMobileNumber($number)
{
    // بررسی اینکه آیا شماره فقط شامل اعداد است، با 09 شروع می‌شود و دقیقاً 11 رقم دارد
    return preg_match('/^09\d{9}$/', $number);
}

function isPersian($text) {
    // بررسی وجود کاراکترهای فارسی در متن
    if (preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $text)) {
        return true; // متن فارسی است
    } else {
        return false; // متن فارسی نیست
    }
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidLandlineNumber($number)
{
    // بررسی اینکه آیا شماره فقط شامل اعداد است، با 0 شروع می‌شود و دقیقاً 11 رقم دارد
    return preg_match('/^0\d{10}$/', $number);
}

function convert_num_2word_persian($number)
{
    $dictionary = new MojtabaaHN\PersianNumberToWords\Dictionary();

    $converter = new MojtabaaHN\PersianNumberToWords\PersianNumberToWords($dictionary);
    return $converter->convert($number);
}


function proxy_file($movie,$file){

    return route('proxy_file',[$movie->server_id,base64_encode($file),\Morilog\Jalali\Jalalian::now()->getTimestamp()]);
}


function encryptNumber($number, $key = 'my_secret_key') {
    $salt = crc32($key); // تولید عدد ثابت ولی پیچیده از کلید
    $mixed = ($number + $salt) * 73; // ضرب برای پنهان‌سازی
    $base = base_convert($mixed, 10, 36); // تبدیل به مبنای ۳۶ برای فشرده‌سازی
    $shuffled = strrev($base); // برعکس‌کردن برای گیج‌کردن

    return str_pad($shuffled, 6, 'X'); // تکمیل تا ۶ کاراکتر
}

function decryptNumber($encoded, $key = 'my_secret_key') {
    $salt = crc32($key);
    $base = strrev(rtrim($encoded, 'X')); // برعکس کردن و حذف padding
    $mixed = base_convert($base, 36, 10);
    $number = ($mixed / 73) - $salt;

    return intval($number);
}

 function formatTime($seconds) {
    $hours = floor($seconds / 3600);
    $minutes = floor(($seconds % 3600) / 60);
    $secs = $seconds % 60;

    $timeParts = [];

    // فقط بخش‌هایی که بزرگ‌تر از صفر هستند اضافه می‌شوند
    if ($hours > 0) {
        $timeParts[] = $hours . ' ساعت';
    }

    if ($minutes > 0) {
        $timeParts[] = $minutes . ' دقیقه';
    }

    if ($secs > 0 || empty($timeParts)) { // اگر هیچ چیزی نیست، حداقل ثانیه نمایش داده شود
        $timeParts[] = $secs . ' ثانیه';
    }

    return implode(' و ', $timeParts);
}

function getRequestInfo(): array
{
    $request=\request();
    // گرفتن IP کاربر
    $ip = $request->ip();

    // گرفتن User Agent
    $userAgent = strtolower($request->header('User-Agent'));


    return [
        'ip' => $ip,
        'device' => $userAgent
    ];
}
