<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'address',
        'dob',
        'phoneNumber',
        'gender',
        'profileImage',
        'city',
        'state',
        'country',
        'email',
        'password',
        'provider',
        'provider_id',
        'pin'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pin'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' =>'array'
    ];

    public function storeorder()
    {
        return $this->hasMany(StoreOrder::class);
    }
    public function reports()
    {
        return $this->hasMany(Report::class);
    }
    public function story()
    {
        return $this->hasMany(Story::class);
    }
    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }


    public function linkedSocialAccounts()
    {
        return $this->hasMany(LinkedSocialAccount::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
    public function otp()
    {
        return $this->hasOne(Otp::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function orderhistories()
    {
        return $this->hasMany(OrderHistory::class);
    }
    public function orderinfo()
    {
        return $this->hasMany(OrderInformation::class);
    }
    public function latestOrder()
    {
        return $this->hasOne(Order::class)->latestOfMany();
    }
    public function oldestOrder()
    {
        return $this->hasOne(Order::class)->oldestOfMany();
    }
    public function largestOrder()
    {
        return $this->hasOne(Order::class)->ofMany('total_amount', 'max');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
