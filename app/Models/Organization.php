<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Organization extends Model
{
    use HasFactory;
    protected $guarded =[];

    public function views(){
        return $this->hasMany(View::class, 'entity_id')->where('entity_type', 'organization');
    }


    function images(){
        return $this->hasMany(ImageOrganization::class);
    }

    function reviews(){
        return $this->hasMany(ReviewsOrganization::class)->orderBy('id','desc')->where('status',1);
    }

    function user(){
        return $this->belongsTo(User::class);
    }
    
    public function city(){
        return $this->belongsTo(City::class);
    }

    public function openOrNot(){
        $day=addHoursAndGetDay($this->time_difference);
        $time=strtotime(addHoursAndGetTime($this->time_difference));
        $get_hours=WorkingHoursOrganization::where('organization_id',$this->id)->where('day',$day)->first();
        if($get_hours!=null){
           if($get_hours->holiday!=1 && $time<strtotime($get_hours->time_end_work) && $time>strtotime($get_hours->time_start_work)){
                return 'Открыто';
           }
        }
        return 'Закрыто';
    }

    public function countReviews(){
        return $this->hasMany(ReviewsOrganization::class)->where('status',1)->count();
    }

    
    public function route(){
        return route('organization.single',$this->slug);
    }

    public function urlImg(){
        if($this->href_img==0){
            return asset('storage/'.$this->img_file);
        }
        if($this->img_url=='default'){
            return 'default';
        }
        return $this->img_url;
    }


    public function urlImgMain(){
        if($this->href_main_img==0){
            return asset('storage/'.$this->img_main_file);
        }
        if($this->img_main_url=='default'){
            return 'default';
        }
        return $this->img_main_url;
    }

    public function timeEndWorkingNow(){
        $day=addHoursAndGetDay($this->time_difference);
        $get_hours=WorkingHoursOrganization::where('organization_id',$this->id)->where('day',$day)->first();
        if($get_hours!=null){
            if($get_hours->holiday!=1){
                return "Открыто до {$get_hours->time_end_work}";
            }
            return 'Выходной';
        }
        return 'Не указано';
    }


    public function ulWorkingDays(){    
        $days=WorkingHoursOrganization::where('organization_id',$this->id)->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")->get();
        if($days->count()>0){
            foreach($days as $day){
                $day_now=addHoursAndGetDay($this->time_difference);
                if($day->holiday!=1){
                    $text_day=translateDayOfWeek($day->day).': '."{$day->time_start_work}-{$day->time_end_work}";
                }else{
                    $text_day=translateDayOfWeek($day->day).': Выходной';
                }
                if($day_now==$day->day){
                    echo " <div class='li_working_day text_black li_working_day_active'>{$text_day}</div>";
                }else{
                    echo "<div class='li_working_day text_black'>{$text_day}</div>";
                }
            }
        }else{
            echo 'Не указано';
        }
    }
  
    public function timeNow(){
        $day=addHoursAndGetDay($this->time_difference);
        $get_hours=WorkingHoursOrganization::where('organization_id',$this->id)->where('day',$day)->first();
        
        if( $get_hours==null){
            return 'Не указано';
        }
        elseif($get_hours->holiday!=1 ){
            return "{$get_hours->time_start_work}-{$get_hours->time_end_work}";
        }
        return 'Выходной';
    }

    public function timeCity(){
        $time=addHoursAndGetTime($this->time_difference);
        return $time;
    }

    function updateRating(){
        $rating=raitingOrganization($this);
        $this->update([
            'rating'=>$rating,
        ]);   
    }

    function ordersNew(){
        return $this->hasMany(OrderProduct::class)->orderBy('id','desc')->where('status',0);
    }

    function ordersCompleted(){
        return $this->hasMany(OrderProduct::class)->orderBy('id','desc')->where('status',2);
    }

    function ordersInWork(){
        return $this->hasMany(OrderProduct::class)->orderBy('id','desc')->where('status',1);
    }
    
    function products(){
        return $this->hasMany(Product::class);
    }

    function workingHours(){
        return $this->hasMany(WorkingHoursOrganization::class);
    }

    function activityCategories(){
        return $this->hasMany(ActivityCategoryOrganization::class);
    }

    function userRequestCount(){
        return $this->hasMany(UserRequestsCount::class);
    }


    function beatifications(){
        return $this->hasMany(Beautification::class);
    }

    function deadAplications(){
        return $this->hasMany(DeadApplication::class);
    }

    function funeralServices(){
        return $this->hasMany(FuneralService::class);
    }

    function memorials(){
        return $this->hasMany(Memorial::class);
    }

    function orderPorducts(){
        return $this->hasMany(OrderProduct::class);
    }

    function defaultMainImg(){
        $url_white_theme=asset('storage/uploads/Theme=White.svg');
        $url_black_theme=asset('storage/uploads/Theme=Black.svg');
        return [$url_white_theme,$url_black_theme];
    }

    function defaultLogoImg(){
        $url_white_theme=asset('storage/uploads/Theme=White (1).svg');
        $url_black_theme=asset('storage/uploads/Theme=Black (1).svg');
        return [$url_white_theme,$url_black_theme];
    }


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($organization) {
            // Удаляем связанные изображения
            foreach ($organization->images as $image) {
                // Если вы храните файлы изображений в хранилище
                if ($image->path && Storage::exists($image->path)) {
                    Storage::delete($image->path);
                }

                // Удалить запись из базы данных
                $image->delete();
            }
        });
    }
    

    public  function calls(){
        return $this->hasMany(CallStat::class);
    }

   public function haveCalls()
    {

        
        // Если есть звонки (больше 0) или стоит unlimited
        if ($this->calls > 0 ) {
            return 1;
        }

        // Если звонков 0
        if ($this->calls === 0) {
            return 0;
        }

        // Если значение null, проверяем иерархию лимитов
        if ($this->calls === null ||  $this->calls === 'unlimited') {
            $limit = optional($this->city)->limit_calls
                ?? optional($this->city)->area->limit_calls
                ?? optional($this->city)->area->edge->limit_calls
                ?? 'unlimited';

            $this->calls = $limit;
            $this->save();

            return $limit === 'unlimited' || $limit > 0 ? 1 : 0;
        }

        return 0;
    }
    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'like_organizations', 'organization_id', 'user_id')
            ->withTimestamps();
    }
    
    /**
     * Проверяет, лайкнул ли текущий пользователь организацию
     */
    public function getIsLikedAttribute(): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        return $this->likedByUsers()->where('user_id', Auth::id())->exists();
    }
    
    /**
     * Альтернативный метод (статический)
     */
    public static function isLiked($organizationId): bool
    {
        if (!Auth::check()) {
            return false;
        }
        
        return static::whereHas('likedByUsers', function($query) {
            $query->where('user_id', Auth::id());
        })->where('id', $organizationId)->exists();
    }
    
    /**
     * Проверяет, лайкнул ли организацию конкретный пользователь
     */
    public function isLikedByUser($userId = null): bool
    {
        $userId = $userId ?? Auth::id();
        
        if (!$userId) {
            return false;
        }
        
        return $this->likedByUsers()->where('user_id', $userId)->exists();
    }

  public static function getCallStats(array $filters = [])
    {
        $user = Auth::user();
        
        if (!$user) {
            return collect()->paginate(13);
        }
        
        // Исправляем вызов метода organization()
        $organization = $user->organization(); // убрали скобки, если это отношение
        // или если это метод:
        // $organization = $user->organization();
        
        if (!$organization) {
            return collect()->paginate(13);
        }
        
        $callsQuery = $organization->calls();
        
        $sortOrder = $filters['sort'] ?? 'desc';
        $callsQuery->orderBy('created_at', $sortOrder);
        
        // Применяем фильтры
        self::applyCallFilters($callsQuery, $filters);
        
        // Сортировка и пагинация
        return $callsQuery->orderBy('created_at', 'desc')->paginate(13);
    }

    /**
     * Применяет фильтры к запросу звонков
     */
    protected static function applyCallFilters($query, array $filters)
{
    if (empty($filters['period'])) {
        return;
    }
    
    switch ($filters['period']) {
        case 'month':
            $query->whereBetween('created_at', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
            break;
            
        case 'last_month':
            $query->whereBetween('created_at', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ]);
            break;
            
        case 'week':
            $query->whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ]);
            break;
            
        case 'last_week':
            $query->whereBetween('created_at', [
                now()->subWeek()->startOfWeek(),
                now()->subWeek()->endOfWeek()
            ]);
            break;
            
        case 'today':
            $query->whereDate('created_at', now()->toDateString());
            break;
            
        case 'yesterday':
            $query->whereDate('created_at', now()->subDay()->toDateString());
            break;
            
        case 'custom':
            if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
                $query->whereBetween('created_at', [
                    $filters['date_from'] . ' 00:00:00',
                    $filters['date_to'] . ' 23:59:59'
                ]);
            }
            break;
    }
}
}
