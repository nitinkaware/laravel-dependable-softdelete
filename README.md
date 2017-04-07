# laravel-dependable-softdelete

This package allows you to delete the dependant relationship of your model.

## Installation

You can install the package via Composer:

``` bash
$ composer require nitinkaware/laravel-dependable-softdelete
```

Now add the service provider in config/app.php file:

``` bash
'providers' => [
    // ...
    Spatie\Permission\PermissionServiceProvider::class,
];
```

##How to use the package?

Just define the relationship array on model.
``` bash
Filename: User.php

class User extends Model {

    use DependableDeleteTrait, SoftDeletes;

    protected static $dependableRelationships = ['comments'];

    public fuction comments()
    {
        return $this->hasMany(Comment::class);
    }
}
```

Now when you will delete the user, all the comments associated with deleting user will
 automatically deleted. If you have other relation defined in Comment.php model, then they will be deleted too.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email nitin.kaware1@gmail.com instead of using the issue tracker.

## Credits

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

