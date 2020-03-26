BootstrapForm, forms for Laravel 5
==================================

[![Circle CI](https://circleci.com/gh/dwightwatson/bootstrap-form/tree/master.svg?style=shield)](https://circleci.com/gh/dwightwatson/bootstrap-form/tree/master)
[![Total Downloads](https://poser.pugx.org/watson/bootstrap-form/downloads.svg)](https://packagist.org/packages/watson/bootstrap-form)
[![Latest Stable Version](https://poser.pugx.org/watson/bootstrap-form/v/stable.svg)](https://packagist.org/packages/watson/bootstrap-form)
[![Latest Unstable Version](https://poser.pugx.org/watson/bootstrap-form/v/unstable.svg)](https://packagist.org/packages/watson/bootstrap-form)
[![License](https://poser.pugx.org/watson/bootstrap-form/license.svg)](https://packagist.org/packages/watson/bootstrap-form)


This is a package for simply creating Bootstrap 3 styled form groups in Laravel 5. It extends the normal form builder to provide you with horizontal form groups completed with labels, error messages and appropriate class usage.

## Introduction

Simply use the `BootstrapForm` facade in the place of the `Form` facade when you want to generate a Bootstrap 3 form group.

```php
BootForm::text('username');
```

And you'll get back the following:

```html
<div class="form-group">
    <label for="username" class="control-label col-md-2">Username</label>
    <div class="col-md-10">
        <input type="text" name="username" class="form-control">
    </div>
</div>
```

Of course, if there are errors for that field it will even populate them.
```html
<div class="form-group has-error">
    <label for="username" class="control-label col-md-2">Username</label>
    <div class="col-md-10">
        <input type="text" name="username" class="form-control">
        <span class="help-block">The username field is required.</span>
    </div>
</div>
```

## Installation

First, require the package using Composer.

```shell
composer require watson/bootstrap-form
```

Now, add these service providers to your `config/app.php` file (don't add the `HtmlServiceProvider` if you already have it).

```php
Collective\Html\HtmlServiceProvider::class,
Watson\BootstrapForm\BootstrapFormServiceProvider::class,
```

And finally add these to the aliases array (note: Form and Html must be listed before BootstrapForm):

```php
'Form'     => Collective\Html\FormFacade::class,
'HTML'     => Collective\Html\HtmlFacade::class,
'BootForm' => Watson\BootstrapForm\Facades\BootstrapForm::class,
```

Feel free to use a different alias for BootstrapForm if you'd prefer something shorter.

## Configuration

There are a number of configuration options available for BootstrapForm. Run the following Artisan command to publish the configuration option to your `config` directory:

```shell
php artisan vendor:publish
```

### Horizontal form sizes

When using a horizontal form you can specify here the default sizes of the left and right columns. Note you can specify as many classes as you like for each column for improved mobile responsiveness, for example:

```
col-md-3 col-sm-6 col-xs-12
```

### Display errors

By default this package will only display the first validation error for each field. If you'd instead like to list out all the validation errors for a field, simply set this configuration option to true.

## Usage

### Opening a form

BootstrapForm has improved the process of opening forms, both in terms of providing Bootstrap classes as well as managing models for model-based forms.

```php
// Passing an existing, persisted model will trigger a model
// binded form.
$user = User::whereEmail('example@example.com')->first();

// Named routes
BootForm::open(['model' => $user, 'store' => 'users.store', 'update' => 'users.update']);

// Controller actions
BootForm::open(['model' => $user, 'store' => 'UsersController@store', 'update' => 'UsersController@update']);
```

If a model is passed to the open method, it will be configured to use the `update` route with the `PUT` method. Otherwise it will point to the `store` method as a `POST` request. This way you can use the same opening tag for a form that handles creating and saving.

```php
// Passing a model that hasn't been saved or a null value as the
// model value will trigger a `store` form.
$user = new User;

BootForm::open()
```

### Form variations

There are a few helpers for opening the different kinds of Bootstrap forms. By default, `open()` will use the the form style that you have set in the configuration file. These helpers take the same input as the `open()` method.

```php
// Open a vertical Bootstrap form.
BootForm::vertical();

// Open an inline Bootstrap form.
BootForm::inline();

// Open a horizontal Bootstrap form.
BootForm::horizontal();
```

If you want to change the columns for a form for a deviation from the settings in your configuration file, you can also set them through the `$options` array.

```php
BootForm::open(['left_column_class' => 'col-md-2', 'left_column_offset_class' => 'col-md-offset-2', 'right_column_class' => 'col-md-10']);
```

### Text inputs

Here are the various methods for text inputs. Note that the method signatures are relatively close to those provided by the Laravel form builder but take a parameter for the form label.

```php
// The label will be inferred as 'Username'.
BootForm::text('username');

// The field name by default is 'email'.
BootForm::email();

BootForm::textarea('profile');

// The field name by default is 'password'.
BootForm::password();
```

### Checkbox and radio button inputs

Checkboxes and radio buttons are a little bit different and generate different markup.

View the method signature for configuration options.

```php
// A checked checkbox.
BootForm::checkbox('interests[]', 'Laravel', 'laravel', true);
```

Same goes for radio inputs.

```php
BootForm::radio('gender', 'Male', 'male');
```

#### Multiple checkboxes and radio buttons

By simply passing an array of value/label pairs you can generate a group of checkboxes or radio buttons easily.

```php
$label = 'this is just a label';

$interests = [
    'laravel' => 'Laravel',
    'rails'   => 'Rails',
    'ie6'     => 'Internet Explorer 6'
];

// Checkbox inputs with Laravel and Rails selected.
BootForm::checkboxes('interests[]', $label, $interests, ['laravel', 'rails']);

$genders = [
    'male'   => 'Male',
    'female' => 'Female'
];

// Gender inputs inline, 'Gender' label inferred.
BootForm::radios('gender', null, $genders, null, true);

// Gender inputs with female selected.
BootForm::radios('gender', 'Gender', $genders, 'female');
```

### Submit button

```php
// Pretty simple.
BootForm::submit('Login');
```

### Closing the form

```php
// Pretty simple.
BootForm::close();
```

### Labels

#### Hide Labels

You may hide an element's label by setting the the value to `false`.

```php
// An input with no label.
BootForm::text('username', false);
```

#### Labels with HTML

To include HTML code inside a label:

```php
// A label with HTML code using array notation
BootForm::text('username', ['html' => 'Username <span class="required">*</span>']);

// A label with HTML code using HtmlString object
BootForm::text('username', new Illuminate\Support\HtmlString('Username <span class="required">*</span>'));
```

### Help Text

You may pass a `help_text` option to any field to have [Bootstrap Help Text](https://getbootstrap.com/css/#forms-help-text) appended to the rendered form group.

### Form input group (suffix and prefix)

Add prefix and/or suffix to any input - you can add text, icon and buttons.

```php
// Suffix button with 'Call' as label and success class to button
{!! BootForm::text('tel', 'Phone', null, ['suffix' => BootForm::addonButton('Call', ['class' => 'btn-success'])] ) !!}

// Prefix button with 'Call' as label and success class to button
{!! BootForm::text('tel', 'Phone', null, ['prefix' => BootForm::addonButton('Call', ['class' => 'btn-success'])] ) !!}

// Prefix icon (I put second parameter after <i class="fa fa-SECOND_PARAMETER"></i>) with 'dollar' as icon
{!! BootForm::text('tel', 'Phone', null, ['prefix' => BootForm::addonIcon('dollar')] ) !!}

// Prefix and suffix as text
{!! BootForm::text('tel', 'Phone', null, ['prefix' => BootForm::addonText('1-'), 'suffix' => BootForm::addonIcon('phone')] ) !!}

// Prefix and suffix with button
{!! BootForm::text('tel', 'Phone', null, ['suffix' => BootForm::addonButton('Boom!', ['class' => 'btn-danger']), 'prefix' => BootForm::addonButton('Call', ['class' => 'btn-success'])] ) !!}
```
