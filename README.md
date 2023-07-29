# Laravel Model Settings Bag

<div style="text-align: center">
<a href="https://packagist.org/packages/netkod-bilisim/laravel-model-settings-bag" rel="nofollow">
    <img src="https://img.shields.io/packagist/v/netkod-bilisim/laravel-model-settings-bag" alt="Latest Stable Version">
</a>

<a href="https://packagist.org/packages/netkod-bilisim/laravel-model-settings-bag" rel="nofollow">
    <img src="https://img.shields.io/packagist/dt/netkod-bilisim/laravel-model-settings-bag" alt="Total Downloads">
</a>

<a href="https://packagist.org/packages/netkod-bilisim/laravel-model-settings-bag" rel="nofollow">
    <img src="https://poser.pugx.org/netkod-bilisim/laravel-model-settings-bag/dependents.svg" alt="Dependents">
</a>

<a href="https://packagist.org/packages/netkod-bilisim/laravel-model-settings-bag" rel="nofollow">
    <img src="https://img.shields.io/packagist/l/netkod-bilisim/laravel-model-settings-bag" alt="License">
</a>
</div>

<div style="text-align: center">
<a href="https://packagist.org/packages/netkod-bilisim/laravel-model-settings-bag" rel="nofollow">
    <img src="http://poser.pugx.org/netkod-bilisim/laravel-model-settings-bag/require/php" alt="License">
</a>
<a href="https://scrutinizer-ci.com/g/netkod-bilisim/laravel-model-settings-bag/badges/quality-score.png?b=master" rel="nofollow">
    <img src="https://scrutinizer-ci.com/g/netkod-bilisim/laravel-model-settings-bag/badges/quality-score.png?b=master" alt="Scrutinizer">
</a>
<a href="https://github.styleci.io/repos/672379930?branch=master">
    <img src="https://github.styleci.io/repos/672379930/shield?branch=master" alt="StyleCI">
</a>

</div>

## <img src="public/assets/images/presentation.png" width="25" height="25"> Introduction

You can add simple and flexible, single or multiple settings to your Laravel models.

## <img src="public/assets/images/requirement.png" width="25" height="25"> Requirements

- PHP >= 7.4

## <img src="public/assets/images/inbox.png" width="25" height="25"> Install

```bash
composer require netkod-bilisim/laravel-model-settings-bag:"^1"
```

## <img src="public/assets/images/integration.png" width="25" height="25"> Integration

### Single Setting
#### 1. Add a JSON settings field to your model's migration.
_create_users_table.php_
```php
Schema::create('users', function (Blueprint $table) {
    $table->increments('id');
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->json('settings')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

#### 2. Use the trait `NetkodBilisim\LaravelModelSettingsBag\ModelHasSettingsBag` within your model.
_User.php_
```php
use NetkodBilisim\LaravelModelSettingsBag\ModelHasSettingsBag;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable 
{
    use ModelHasSettingsBag;
     
    // truncated for brevity..
}
```

### Multiple
#### 1. Add a JSON settings field to your model's migration.
_create_user_template_settings_table.php_
```php
Schema::create('user_template_settings', function (Blueprint $table) {
    $table->id();
    $table->unsignedInteger('user_id');
    $table->json('settings')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
```

#### 2. Use the trait `NetkodBilisim\LaravelModelSettingsBag\ModelHasSettingsBag` within your other setting model.
_User TemplateSetting.php_
```php
use NetkodBilisim\LaravelModelSettingsBag\ModelHasSettingsBag;
use Illuminate\Database\Eloquent\Model;

class UserTemplateSetting extends Model 
{
    use ModelHasSettingsBag;
}
```

#### 3. Use the trait `NetkodBilisim\LaravelModelSettingsBag\ModelHasSettingsBag` within your model.
_User.php_
```php
class User extends Model 
{
    use ModelHasSettingsBag;

    public function templateSettings()
    {
        return $this->hasOne(User TemplateSetting::class);
    }
}
```

## <img src="public/assets/images/web-coding.png" width="25" height="25"> Usage

### 1.) Get all of the model's settings.
```php
$user = App\User::first();

$user->settings()->all();    // Returns an array of the user's settings.
$user->settings('template')->get();    // Returns an array of a user's template settings.
```

### 2.) Get a specific setting.
```php
$user = App\User::first();

$user->settings()->get('some.setting');
$user->settings()->get('some.setting', $defaultValue); // With a default value.

$user->settings('template')->get('layout.boxed');
$user->settings('template')->get('layout.boxed', $defaultValue); // With a default value.
```

### 3.) Add or update a setting.
```php
$user = App\User::first();

$user->settings()->update('some.setting', 'new value');
$user->settings('template')->update('layout.boxed', 'new value');
```

### 4.) Determine if the model has a specific setting.
```php
$user = App\User::first();

$user->settings()->has('some.setting');
$user->settings('template')->has('layout.boxed');
```

### 5.) Remove a setting from a model.
```php
$user = App\User::first();

$user->settings()->delete('some.setting');
$user->settings('template')->delete('layout.boxed');
```

### 6.) Set the default settings for a new model.

If you define `$defaultSettings` as an array property on your model, we will use its value as the default settings for
any new models that are created *without* settings.

_User.php_
```php
use NetkodBilisim\LaravelModelSettingsBag\ModelHasSettingsBag;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable 
{
    use ModelHasSettingsBag;

    /**
     * The model's default settings.
     * 
     * @var array
     */
    protected $defaultSettings = [
    	'homepage' => '/profile'
    ];

    // truncated for brevity..
}
```

### 7.) Specify the settings that are allowed.

If you define `$allowedSettings` as an array property then only settings which match a value within
the `$allowedSettings` array will be saved on the model.

_User.php_
```php
use NetkodBilisim\LaravelModelSettingsBag\ModelHasSettingsBag;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable 
{
    use ModelHasSettingsBag;

    /**
     * The model's allowed settings.
     * 
     * @var array
     */
    protected $allowedSettings = ['homepage'];

    // truncated for brevity..
}
```

### 8.) Using another method name other than settings()

If you prefer to use another name other than `settings` , you can do so by defining a `$mapSettingsTo` property. This simply maps calls to the method (such as `config()`) to the `settings()` method.

_User.php_
```php
use NetkodBilisim\LaravelModelSettingsBag\ModelHasSettingsBag;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable 
{
    use ModelHasSettingsBag;

    /**
     * The settings field name.
     * 
     * @var string
     */
    protected $mapSettingsTo = 'config';
}
```

## <img src="public/assets/images/licensing.png" width="25" height="25"> License

This package is open source software licensed under
the [MIT License](https://opensource.org/license/mit/).
