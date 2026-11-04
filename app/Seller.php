<?php
namespace App;

use Illuminate\Database\Eloquent\Model;

class Seller extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function corporate_domains()
    {
        return $this->hasMany(SellerCorporateDomains::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get is active CorporateAccess
     * @return \Illuminate\Database\Eloquent\HigherOrderBuilderProxy|mixed
     */
    public function getIsActiveCAAttribute() {
        return $this->user()->first()->enable_corporate_domains;
    }
}

