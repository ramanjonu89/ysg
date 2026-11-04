<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GeneralSetting
 *
 * @property int $id
 * @property string $frontend_color
 * @property string $frontend_color_hover
 * @property string $color_menu_bar_area
 * @property string $color_menu_bar_main
 * @property string $color_menu_bar_text
 * @property string|null $logo
 * @property string|null $admin_logo
 * @property string|null $admin_login_background
 * @property string|null $admin_login_sidebar
 * @property string|null $favicon
 * @property string|null $site_name
 * @property string|null $address
 * @property string $description
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $facebook
 * @property string|null $instagram
 * @property string|null $twitter
 * @property string|null $youtube
 * @property string|null $google_plus
 * @property string|null $telegram
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereAdminLoginBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereAdminLoginSidebar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereAdminLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereFacebook($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereFavicon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereFrontendColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereGooglePlus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereInstagram($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereSiteName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereTwitter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\GeneralSetting whereYoutube($value)
 * @mixin \Eloquent
 */
class GeneralSetting extends Model
{
    /**
     * Main hover color.
     *
     * @param  string  $value
     * @return string
     */
    protected $guarded = [];
    public function getFrontendColorHoverAttribute($value)
    {
        if (!$value) {
            return $this->frontend_color;
        }
        return $value;
    }

    public function generalSettingTranslation($languageId = 1){
        return @$this->hasOne(GeneralSettingTranslation::class)->where(['language_id' => $languageId])->first();
    }

    public function translation()
    {
        $languageId = session('sessionLanguageId');
        return $this->hasMany(GeneralSettingTranslation::class)->where('language_id', $languageId);
    }

    public function getTranslationDescriptionAttribute()
    {
        return $this->translation->isEmpty() ? $this->description : $this->translation->first()->description;
    }

}
