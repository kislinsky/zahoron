<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'phone',
        'email',
        'password',
        'status',
        'patronymic',
        'city',
        'adres',
        'whatsapp',
        'telegram',
        'role',
        'icon',
        'email_notifications',
        'sms_notifications',
        'language',
        'theme',
        'inn',
        'number_cart',
        'organization_id',
        'bank',
        'cemetery_ids',
        'name_organization',
        'organizational_form',
        'edge_id',
        'city_id',
        'ogrn',
        'in_face',
        'regulation',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    public function decoderIncome(){
        $sum=0;
        $payments=Task::where('user_id',$this->id)->where('status',1)->get();
        if($payments->count()>0){
            foreach($payments as $payment){
                $sum=$sum+$payment->price*$payment->count;
            }
        }
        return $sum;
    }

    public function organization(){
        if($this->organization_id!=null){
            $organization=Organization::find($this->organization_id);
            return $organization;
        }
        return null;

        //return $this->belongsTo(Organization::class);
    }

    public function organizations(){
        return $this->hasMany(Organization::class);

    }


    function newBurials(){
        return $this->hasMany(OrderBurial::class)->where('status',0)->get();
    }

    function newServices(){
        return $this->hasMany(OrderService::class)->where('status',0)->get();
    }


    function orderBurials($status=null){
        if($status!=null){
            return $this->hasMany(OrderBurial::class)->orderBy('id','desc')->where('status',$status);
        }
        return $this->hasMany(OrderBurial::class)->orderBy('id','desc');
    }

    function favoriteBurial(){
        return $this->hasMany(FavouriteBurial::class);
    }

    function orderProducts($status=null){
        if($status!=null){
            return $this->hasMany(OrderProduct::class)->orderBy('id','desc')->where('status',$status);
        }
        return $this->hasMany(OrderProduct::class)->orderBy('id','desc');
    }

    function orderServices($status=null){
        if($status!=null){
            if($status==2){
                return $this->hasMany(OrderService::class)->where('status',$status)->where('paid',1);
            }
        }
        return $this->hasMany(OrderService::class);
    }

    function searchBurials($status=null){
        if($status!=null){
            return $this->hasMany(SearchBurial::class)->where('status',$status);
        }
        return $this->hasMany(SearchBurial::class);
    }

    function edge(){
        return $this->belongsTo(Edge::class);
    }

    function city(){
        return $this->belongsTo(City::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
     
}
