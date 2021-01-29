# laravel-imap-example

#installation

composer require webklex/laravel-imap

Add the following to the providers array:
Webklex\IMAP\Providers\LaravelServiceProvider::class,

add the following to the aliases array:
'Client' => Webklex\IMAP\Facades\Client::class,


#Publish the configuration file


php artisan vendor:publish --provider="Webklex\IMAP\Providers\LaravelServiceProvider"
