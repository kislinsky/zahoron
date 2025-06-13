<?php

namespace App\Http\Controllers\Account\Admin;

use App\Http\Controllers\Controller;
use App\Services\Account\Admin\AdminCemeteryService;
use App\Services\Account\Admin\AdminChurchService;
use App\Services\Account\Admin\AdminColumbariumService;
use App\Services\Account\Admin\AdminCrematoriumService;
use App\Services\Account\Admin\AdminMortuaryService;
use App\Services\Account\Admin\AdminMosqueService;
use Illuminate\Http\Request;

class AdminRitualObjectsController extends Controller
{

    public static function cemetery(){
        return AdminCemeteryService::index();
    }

    public static function cemeteryDelete($id){
        return AdminCemeteryService::delete($id);
    }

    public static function cemeteryParser(){
        return AdminCemeteryService::parser();
    }

    public static function cemeteryImport(Request $request){
        return AdminCemeteryService::import($request);
    }

    public static function cemeteryReviewsImport(Request $request){
        
        $request->validate([
            'file_reviews' => 'required|mimes:xlsx,xls,csv'
        ]);
        return AdminCemeteryService::importReviews($request);
    }


    public static function mortuary(){
        return AdminMortuaryService::index();
    }

    public static function mortuaryDelete($id){
        return AdminMortuaryService::delete($id);
    }



    public static function mortuaryParser(){
        return AdminMortuaryService::parser();
    }

    public static function mortuaryImport(Request $request){
        return AdminMortuaryService::import($request);
    }


    public static function mortuaryReviewsImport(Request $request){
        
        $request->validate([
            'file_reviews' => 'required|mimes:xlsx,xls,csv'
        ]);
        return AdminMortuaryService::importReviews($request);
    }



    public static function crematorium(){
        return AdminCrematoriumService::index();
    }

    public static function crematoriumDelete($id){
        return AdminCrematoriumService::delete($id);
    }

    public static function crematoriumParser(){
        return AdminCrematoriumService::parser();
    }

    public static function crematoriumImport(Request $request){
        return AdminCrematoriumService::import($request);
    }

    public static function crematoriumReviewsImport(Request $request){
        
        $request->validate([
            'file_reviews' => 'required|mimes:xlsx,xls,csv'
        ]);
        return AdminCrematoriumService::importReviews($request);
    }



    public static function  columbarium(){
        return AdminColumbariumService::index();
    }

    public static function  columbariumDelete($id){
        return AdminColumbariumService::delete($id);
    }

    public static function columbariumParser(){
        return AdminColumbariumService::parser();
    }

    public static function columbariumImport(Request $request){
        return AdminColumbariumService::import($request);
    }

    public static function columbariumReviewsImport(Request $request){
        
        $request->validate([
            'file_reviews' => 'required|mimes:xlsx,xls,csv'
        ]);
        return AdminColumbariumService::importReviews($request);
    }

    public static function mosqueParser(){
        return AdminMosqueService::parser();
    }

    public static function mosqueImport(Request $request){
        return AdminMosqueService::import($request);
    }

    public static function churchParser(){
        return AdminChurchService::parser();
    }

    public static function churchImport(Request $request){
        return AdminChurchService::import($request);
    }
}
