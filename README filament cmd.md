1. create a resource

php artisan make:filament-resource resource-name

php artisan make:filament-resource resource-name --soft-deletes

php artisan make:filament-resource resource-name --soft-deletes --view

2. create form for create and edit inside Resource class

3. create table for listing entries inside Resource class

4. customize redirects after creating or editing

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

5. customize notification msg after creating or editing

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'User registered';
    }
    protected function getSavedNotificationTitle(): ?string
    {
        return 'User updated';
    }



