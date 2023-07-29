<?php

namespace NetkodBilisim\LaravelModelSettingsBag;

use Illuminate\Support\Arr;

trait ModelHasSettingsBag
{
    // Boot the HasSettingsBag trait.
    public static function bootHasSettingsBag(): void
    {
        self::creating(function ($model) {
            if (! $model->settings) {
                $model->settings = $model->getDefaultSettings() ?: null;
            }
        });

        self::saving(function ($model) {
            if ($model->settings && property_exists($model, 'allowedSettings') && is_array($model->allowedSettings)) {
                $model->settings = Arr::only($model->settings, $model->allowedSettings);
            }
        });
    }

    // Get the model's default settings.
    public function getDefaultSettings(): array
    {
        return (isset($this->defaultSettings) && is_array($this->defaultSettings))
            ? $this->defaultSettings
            : [];
    }

    // Get the settings attribute.
    public function getSettingsAttribute($settings)
    {
        return json_decode($settings, true);
    }

    // Set the settings attribute.
    public function setSettingsAttribute($settings): void
    {
        $this->attributes['settings'] = json_encode($settings);
    }

    // The model's settings.
    public function settings(string $settingName = null): SettingsBag
    {
        if ($settingName === null) {
            return new SettingsBag($this);
        }

        $settingRelation = $settingName.'Settings';
        $settingModel = $this->{$settingRelation};
        if ($settingModel === null) {
            $settingModel = $this->{$settingRelation}()->getRelated();
            $settingModel->{$this->getForeignKey()} = $this->{$this->getKeyName()};
        }

        return new SettingsBag($settingModel);
    }

    // Map settings() to another alias specified with $mapSettingsTo.
    public function __call($name, $args)
    {
        if (isset($this->mapSettingsTo) && $name == $this->mapSettingsTo) {
            return $this->settings(...$args);
        }

        return is_callable(['parent', '__call'])
            ? parent::__call($name, $args)
            : null;
    }
}
