<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;
use App\Http\Controllers\WishController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\RecommendController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\PolicyController;
use App\Http\Controllers\PushController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\ApplyController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\QnaController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\ChatController;

use App\Models\User;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/*Route::middleware('auth:api')->put('/partner/hotel/regist', function (Request $request) {
    //return $request->partner();
});*/

Route::middleware('auth:sanctum')->post('/admin/regist', [AdminController::class, 'regist']);
Route::get('/admin/login', [AdminController::class, 'login']);
Route::get('/admin/logout', [AdminController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/admin/list', [AdminController::class, 'list']);
Route::middleware('auth:sanctum')->get('/admin/detail', [AdminController::class, 'detail']);
Route::middleware('auth:sanctum')->put('/admin/update', [AdminController::class, 'update']);
Route::middleware('auth:sanctum')->put('/admin/update_password', [AdminController::class, 'update_password']);

Route::middleware('auth:sanctum')->get('/admin/user/list', [UserController::class, 'list']);
Route::middleware('auth:sanctum')->get('/admin/profile/detail', [UserController::class, 'profile_detail_admin']);
Route::middleware('auth:sanctum')->get('/admin/company/detail', [UserController::class, 'company_detail_admin']);
Route::middleware('auth:sanctum')->put('/admin/profile/update', [UserController::class, 'update_user_admin']);
Route::middleware('auth:sanctum')->put('/admin/jobhistory/update', [UserController::class, 'update_jobhistory_admin']);
Route::middleware('auth:sanctum')->put('/admin/company/update', [UserController::class, 'update_company_admin']);
Route::middleware('auth:sanctum')->get('/admin/apply/list', [ApplyController::class, 'list']);
Route::middleware('auth:sanctum')->get('/admin/apply/detail', [ApplyController::class, 'detail_admin']);
Route::middleware('auth:sanctum')->get('/admin/qna/list', [QnaController::class, 'list_admin']);
Route::middleware('auth:sanctum')->get('/admin/qna/detail', [QnaController::class, 'detail_admin']);
Route::middleware('auth:sanctum')->put('/admin/qna/answer', [QnaController::class, 'answer']);
Route::middleware('auth:sanctum')->delete('/admin/qna/delete', [QnaController::class, 'delete']);
Route::middleware('auth:sanctum')->get('/admin/notice/list', [NoticeController::class, 'list_admin']);
Route::middleware('auth:sanctum')->get('/admin/notice/detail', [NoticeController::class, 'detail_admin']);
Route::middleware('auth:sanctum')->delete('/admin/notice/delete', [NoticeController::class, 'delete']);
Route::middleware('auth:sanctum')->put('/admin/notice/update', [NoticeController::class, 'update']);
Route::middleware('auth:sanctum')->put('/admin/banner/update', [BannerController::class, 'update']);
Route::middleware('auth:sanctum')->get('/admin/banner/list', [BannerController::class, 'list_admin']);
Route::middleware('auth:sanctum')->delete('/admin/banner/delete', [BannerController::class, 'delete']);
Route::middleware('auth:sanctum')->post('/admin/banner/regist', [BannerController::class, 'regist']);
Route::middleware('auth:sanctum')->get('/admin/banner/detail', [BannerController::class, 'detail']);

Route::middleware('auth:sanctum')->put('/admin/blog/update', [BlogController::class, 'update']);
Route::middleware('auth:sanctum')->get('/admin/blog/detail', [BlogController::class, 'detail']);
Route::middleware('auth:sanctum')->get('/admin/blog/list', [BlogController::class, 'list_admin']);
Route::middleware('auth:sanctum')->delete('/admin/blog/delete', [BlogController::class, 'delete']);
Route::middleware('auth:sanctum')->post('/admin/blog/regist', [BlogController::class, 'regist']);
Route::middleware('auth:sanctum')->get('/admin/payment/list', [PaymentController::class, 'list']);
Route::middleware('auth:sanctum')->get('/admin/payment/detail', [PaymentController::class, 'detail']);

Route::get('/excel/download/user_list', [ExcelController::class, 'user_list']);
Route::get('/excel/download/payment_list', [ExcelController::class, 'payment_list']);


Route::post('/user/regist', [UserController::class, 'regist']);
Route::post('/company/regist', [UserController::class, 'regist_company']);
Route::post('/login', [UserController::class, 'login']);
Route::get('login', [UserController::class, 'not_login'])->name('login');
Route::post('/user/certify_email', [UserController::class, 'certify_email']);
Route::post('/user/certify_phone', [UserController::class, 'certify_phone']);
Route::get('/user/check_email_code', [UserController::class, 'check_email_code']);
Route::get('/user/check_phone_code', [UserController::class, 'check_phone_code']);
Route::get('/user/get_certify_info', [UserController::class, 'get_certify_info']);
Route::middleware('auth:sanctum')->get('/user/info', [UserController::class, 'user_info']);
Route::middleware('auth:sanctum')->post('/profile/regist', [UserController::class, 'regist_profile']);
Route::middleware('auth:sanctum')->post('/jobhistory/regist', [UserController::class, 'regist_jobhistory']);
Route::get('/profile/detail', [UserController::class, 'profile_detail']);
Route::get('/company/detail', [UserController::class, 'company_detail']);
Route::get('/user/new_list', [UserController::class, 'new_list']);
Route::get('/company/popular_list', [UserController::class, 'popular_list']);
Route::get('/profile/list', [UserController::class, 'profile_list']);
Route::get('/company/list', [UserController::class, 'company_list']);
Route::put('/user/update_password', [UserController::class, 'update_password']);


Route::get('/recommend/list', [RecommendController::class, 'list']);

Route::get('/area/list1', [AreaController::class, 'area_list1']);
Route::get('/area/list2', [AreaController::class, 'area_list2']);

Route::post('/notice/regist', [NoticeController::class, 'regist']);
Route::get('/notice/list', [NoticeController::class, 'list']);
Route::get('/notice/detail', [NoticeController::class, 'detail']);

Route::middleware('auth:sanctum')->put('/user/update_info', [UserController::class, 'update_user_info']);
Route::middleware('auth:sanctum')->put('/company/update_info', [UserController::class, 'update_company_info']);
Route::get('/company/search', [UserController::class, 'company_search']);
Route::get('/profile/search', [UserController::class, 'profile_search']);
Route::get('/search/keyword/list', [UserController::class, 'search_keyword_list']);
Route::get('/company/search_option', [UserController::class, 'search_option_company']);


Route::middleware('auth:sanctum')->post('/apply/regist', [ApplyController::class, 'regist']);
Route::middleware('auth:sanctum')->get('/apply/list_by_user', [ApplyController::class, 'list_by_user']);
Route::middleware('auth:sanctum')->get('/apply/success_list_by_user', [ApplyController::class, 'success_list_by_user']);
Route::middleware('auth:sanctum')->get('/apply/cancel_list_by_user', [ApplyController::class, 'cancel_list_by_user']);
Route::middleware('auth:sanctum')->get('/apply/detail', [ApplyController::class, 'detail']);

Route::middleware('auth:sanctum')->get('/apply/list_by_company', [ApplyController::class, 'list_by_company']);
Route::middleware('auth:sanctum')->get('/apply/success_list_by_company', [ApplyController::class, 'success_list_by_company']);
Route::middleware('auth:sanctum')->get('/apply/cancel_list_by_company', [ApplyController::class, 'cancel_list_by_company']);
Route::middleware('auth:sanctum')->get('/apply/detail_apply', [ApplyController::class, 'detail_apply']);


Route::get('/banner/list', [BannerController::class, 'list']);

Route::get('/blog/list', [BlogController::class, 'list']);

Route::middleware('auth:sanctum')->get('/qna/list', [QnaController::class, 'list']);
Route::middleware('auth:sanctum')->post('/qna/regist', [QnaController::class, 'regist']);

Route::middleware('auth:sanctum')->post('/wish/toggle/company', [WishController::class, 'toggle_company']);
Route::middleware('auth:sanctum')->post('/wish/toggle/profile', [WishController::class, 'toggle_profile']);
Route::middleware('auth:sanctum')->get('/wish/list/company', [WishController::class, 'list_company']);
Route::middleware('auth:sanctum')->get('/wish/list/profile', [WishController::class, 'list_profile']);


Route::middleware('auth:sanctum')->post('/logout', [UserController::class, 'logout']);
Route::middleware('auth:sanctum')->get('/login_check', [UserController::class, 'login_check']);

Route::get('/get_corp_state', [UserController::class, 'get_corp_state']);

Route::middleware('auth:sanctum')->post('/payment/regist', [PaymentController::class, 'regist']);


Route::middleware('auth:sanctum')->put('/user/leave', [UserController::class, 'leave']);

Route::middleware('auth:sanctum')->post('chat/create', [ChatController::class, 'create']);
Route::middleware('auth:sanctum')->get('chat/list', [ChatController::class, 'list']);



Route::middleware('auth:sanctum')->put('/push/regist', [PushController::class, 'regist']);
Route::middleware('auth:sanctum')->get('/push/list', [PushController::class, 'list']);


Route::middleware('auth:sanctum')->put('/faq/regist', [FaqController::class, 'regist']);
Route::get('/faq/list', [FaqController::class, 'list']);
Route::get('/faq/detail', [FaqController::class, 'detail']);
Route::middleware('auth:sanctum')->put('/faq/update', [FaqController::class, 'update']);

Route::middleware('auth:sanctum')->put('/policy/regist', [PolicyController::class, 'regist']);
Route::get('/policy/detail', [PolicyController::class, 'detail']);
Route::get('/policy/list', [PolicyController::class, 'list']);
Route::middleware('auth:sanctum')->put('/policy/update', [PolicyController::class, 'update']);

Route::post('/image/upload', [ImageController::class, 'upload']);





