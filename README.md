# PHP_Laravel12_SubScribable_Notification



## Project Description

PHP_Laravel12_SubScribable_Notification is a Laravel 12 based web application that allows users to subscribe to email notifications and manage their subscription preferences.

It provides a complete system where users can:

- Subscribe using a form
- Receive confirmation email
- Unsubscribe using a secure link

The project uses a third-party package to efficiently handle mailing lists and unsubscribe functionality.



## Features

- Users can subscribe using their name and email
- Email notification is sent after successful subscription
- Unsubscribe link in email
- Mailing list support
- Simple UI pages



## Technologies Used

- Laravel 12
- PHP
- MySQL
- Mailtrap (SMTP)
- Blade


## Workflow

1. User submits subscription form  
2. Data stored in database  
3. Email notification sent  
4. User clicks unsubscribe link  
5. Subscription updated/removed  



---



## Installation Steps


---


## STEP 1: Create Laravel 12 Project

### Open terminal / CMD and run:

```
composer create-project laravel/laravel PHP_Laravel12_SubScribable_Notification "12.*"

```

### Go inside project:

```
cd PHP_Laravel12_SubScribable_Notification

```

#### Explanation:

This installs a fresh Laravel 12 project using Composer and creates the project folder.




## STEP 2: Database Setup 

### Update database details:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel12_SubScribable
DB_USERNAME=root
DB_PASSWORD=

```

### Create database in MySQL / phpMyAdmin:

```
Database name: laravel12_SubScribable

```

### Then Run:

```
php artisan migrate

```


#### Explanation:

Connects Laravel with MySQL database and creates default tables using migration.




## STEP 3: Install Subscribable Notifications Package

### Run:

```
composer require ylsideas/subscribable-notifications

```

### Explanation:

Installs the subscribable-notifications package to manage email subscriptions and unsubscribe logic.



## STEP 4: Create Subscribers Table

### Run:

```
php artisan make:model Subscriber -m

```

### Edit migration in database/migrations/xxxx_create_subscribers_table.php:

```
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->json('mailing_lists')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscribers');
    }
};

```


### Then Run:

```
php artisan migrate

```



### app/Models/Subscriber.php

```
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use YlsIdeas\SubscribableNotifications\MailSubscriber;
use YlsIdeas\SubscribableNotifications\Contracts\CanUnsubscribe;

class Subscriber extends Model implements CanUnsubscribe
{
    use Notifiable, MailSubscriber;

    protected $fillable = [
        'name',
        'email',
        'mailing_lists',
        'unsubscribed_at'
    ];

    protected $casts = [
        'mailing_lists' => 'array',
        'unsubscribed_at' => 'datetime',
    ];
}

```

#### Explanation:

Creates a custom table to store subscriber data like name, email, mailing list, and unsubscribe status.

Defines how subscriber data is handled and enables notification + unsubscribe features using traits.




## STEP 5: Create Notification

### Run:

```
php artisan make:notification WelcomeSubscriberNotification

```

### app/Notifications/WelcomeSubscriberNotification.php:

```
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class WelcomeSubscriberNotification extends Notification
{
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Subscription Successful')
            ->greeting("Hello {$notifiable->name}")
            ->line('Thank you for subscribing!')
            ->line(' ')
            ->line('If you wish to unsubscribe, click below:')
            ->action('Unsubscribe', url('/unsubscribe/' . $notifiable->id));
    }
}

```

#### Explanation:

Creates an email notification class to send subscription confirmation with unsubscribe button.





## STEP 6: Create SubscriberServiceProvider

### Run:

```
php artisan make:provider SubscriberServiceProvider

```

### app/Providers/SubscriberServiceProvider.php:

```
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use YlsIdeas\SubscribableNotifications\Facades\Subscriber;
use Illuminate\Support\Facades\Route;
use App\Models\Subscriber as SubscriberModel;

class SubscriberServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Unsubscribe from specific mailing list
        Route::get('/unsubscribe/{subscriber}/{mailingList}', function ($id, $mailingList) {
            $subscriber = SubscriberModel::findOrFail($id);
            $subscriber->mailing_lists = array_merge($subscriber->mailing_lists ?? [], [
                $mailingList => false,
            ]);
            $subscriber->save();

            return view('unsubscribe.confirmed', [
                'message' => "You unsubscribed from {$mailingList}"
            ]);
        })->name('unsubscribe.list');

        // Unsubscribe from all emails
        Route::get('/unsubscribe-all/{subscriber}', function ($id) {
            $subscriber = SubscriberModel::findOrFail($id);
            $subscriber->unsubscribed_at = now();
            $subscriber->save();

            return view('unsubscribe.confirmed', [
                'message' => "You unsubscribed from all emails"
            ]);
        })->name('unsubscribe.all');

        // Optional callbacks
        Subscriber::onUnsubscribeFromMailingList(function ($subscriber, $mailingList) {
            $subscriber->mailing_lists = array_merge($subscriber->mailing_lists ?? [], [
                $mailingList => false,
            ]);
            $subscriber->save();
        });

        Subscriber::onUnsubscribeFromAllMailingLists(function ($subscriber) {
            $subscriber->unsubscribed_at = now();
            $subscriber->save();
        });

        Subscriber::onCompletion(function ($subscriber, $mailingList = null) {
            return view('unsubscribe.confirmed', [
                'message' => $mailingList
                    ? "You unsubscribed from {$mailingList}"
                    : "You unsubscribed from all emails"
            ]);
        });
    }

    public function register(): void
    {
        //
    }
}

```

#### Explanation:

Handles unsubscribe logic for specific lists or all emails and defines custom routes for unsubscribe actions.





## STEP 7: Register Provider

### In config/app.php:

```
'providers' => [
    ...
    App\Providers\SubscriberServiceProvider::class,
],

```

#### Explanation:

Registers the custom provider so Laravel can use unsubscribe functionality.




## STEP 8: Create Controller to Send Emails

### Run:

```
php artisan make:controller SubscriberNotificationController

```

### app/Http/Controllers/SubscriberNotificationController.php:

```
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subscriber;
use App\Notifications\WelcomeSubscriberNotification;

class SubscriberNotificationController extends Controller
{
    /**
     * Store subscriber (simple insert)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:subscribers,email',
        ]);

        $subscriber = Subscriber::create([
            'name' => $request->name,
            'email' => $request->email,

            // ✅ IMPORTANT (NO json_encode)
            'mailing_lists' => [
                'newsletter' => true
            ],
        ]);

        // ✅ Send Notification
        $subscriber->notify(new WelcomeSubscriberNotification('newsletter'));

        return view('success');
    }

    /**
     * Subscribe (update OR create)
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
        ]);

        $subscriber = Subscriber::updateOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,

                // ✅ IMPORTANT (THIS FIXES NULL ISSUE)
                'mailing_lists' => [
                    'newsletter' => true
                ],
            ]
        );

        // ✅ Send Notification
        $subscriber->notify(new WelcomeSubscriberNotification('newsletter'));

        return view('subscribe-success', compact('subscriber'));
    }
}

```

#### Explanation:

Handles form data, stores subscriber in database, and sends email notification.




## STEP 9: Mail Setup

### Configure .env with Mailtrap or SMTP:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="your_mailtrap_Address"
MAIL_FROM_NAME="TrendyKart"

```

#### Explanation:

Configures SMTP (Mailtrap) so Laravel can send emails during development/testing.




## STEP 10: Add Routes 

### Route/web.php:

```
<?php

use Illuminate\Support\Facades\Route;
use App\Models\Subscriber;
use App\Http\Controllers\SubscriberNotificationController;

// Show subscribe form
Route::get('/', function () {
    return view('subscribe');
});

// Handle subscribe
Route::post('/subscribe', [SubscriberNotificationController::class, 'subscribe'])
    ->name('subscribe');

// Unsubscribe
Route::get('/unsubscribe/{subscriber}', function (Subscriber $subscriber) {
    $subscriber->unsubscribed_at = now();
    $subscriber->save();

    return view('unsubscribe-success');
})->name('unsubscribe');

```

#### Explanation:

Defines URLs for showing form, submitting subscription, and handling unsubscribe actions.




## STEP 11: Create Views Files

### resources/views/subscribe.blade.php

```
<!DOCTYPE html>
<html>

<head>
    <title>Subscribe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            height: 100vh;
            background: #0f172a;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            color: #fff;
        }

        .card {
            background: #1e293b;
            padding: 40px;
            border-radius: 15px;
            width: 350px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        h2 {
            margin-bottom: 20px;
        }

        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 8px;
            outline: none;
            background: #334155;
            color: #fff;
        }

        input::placeholder {
            color: #94a3b8;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #22c55e;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #16a34a;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>Subscribe</h2>

        <form method="POST" action="{{ route('subscribe') }}">
            @csrf

            <input type="text" name="name" placeholder="Enter your name" required>
            <input type="email" name="email" placeholder="Enter your email" required>

            <button type="submit">Subscribe Now</button>
        </form>
    </div>

</body>

</html>

```


### resources/views/subscribe-success.blade.php

```
<!DOCTYPE html>
<html>

<head>
    <title>Success</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            height: 100vh;
            background: #0f172a;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial;
            color: white;
        }

        .card {
            background: #1e293b;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        h2 {
            color: #22c55e;
        }

        p {
            color: #cbd5f5;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>✅ Subscription Successful!</h2>
        <p>Thank you for joining our newsletter.</p>
    </div>

</body>

</html>

```




### resources/views/unsubscribe-success.blade.php

```
<!DOCTYPE html>
<html>

<head>
    <title>Unsubscribed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            height: 100vh;
            background: #0f172a;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial;
            color: white;
        }

        .card {
            background: #1e293b;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        h2 {
            color: #ef4444;
        }

        p {
            color: #cbd5f5;
        }
    </style>
</head>

<body>

    <div class="card">
        <h2>❌ You Unsubscribed</h2>
        <p>We're sad to see you go.</p>
    </div>

</body>

</html>

```

#### Explanation:

Creates frontend UI pages like subscribe form, success message, and unsubscribe confirmation.




## STEP 12: Run Application  

### Start dev server:

```
php artisan serve

```

### Open in browser:

```
http://127.0.0.1:8000

```

#### Explanation:





## Expected Output:


### Home Page (Subscription Form):


<img src="screenshots/Screenshot 2026-03-30 103232.png" width="900">


### User Subscription Form:


<img src="screenshots/Screenshot 2026-03-30 104007.png" width="900">


### Subscription Success Page:


<img src="screenshots/Screenshot 2026-03-30 104022.png" width="900">


### Email Notification (Mailtrap Preview):


<img src="screenshots/Screenshot 2026-03-30 104051.png" width="900">


### Unsubscribe Confirmation Page:


<img src="screenshots/Screenshot 2026-03-30 104109.png" width="900">



---

## Project Folder Structure:

```
PHP_Laravel12_SubScribable_Notification/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── SubscriberNotificationController.php
│   │
│   ├── Models/
│   │   └── Subscriber.php
│   │
│   ├── Notifications/
│   │   └── WelcomeSubscriberNotification.php
│   │
│   └── Providers/
│       └── SubscriberServiceProvider.php
│
├── bootstrap/
│
├── config/
│   └── app.php   (Provider registered here)
│
├── database/
│   ├── migrations/
│   │   └── xxxx_create_subscribers_table.php
│   │
│   └── seeders/
│
├── public/
│
├── resources/
│   └── views/
│       ├── subscribe.blade.php
│       ├── subscribe-success.blade.php
│       ├── unsubscribe-success.blade.php
│
├── routes/
│   └── web.php
│
├── storage/
│
├── tests/
│
├── .env
├── artisan
├── composer.json
└── package files...

```
