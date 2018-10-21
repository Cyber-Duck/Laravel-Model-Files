# Model Files

This is still in development, since it was extracted from an existing project some 
functionality is still being developed and changed.

The tests were linked to multiple Models (will be moved soon).

Author: [Tiago Tavares](https://github.com/tiagocyberduck)

Made with :heart: by [Cyber-Duck Ltd](http://www.cyber-duck.co.uk)

## Introduction

Model files provide a simple trait (`HasFiles`) to be used by your eloquent models.

This trait allows you to define in your model through an array a simple storage configuration:

* An observer will handle the storage and pruning of files presents in the disk.
* Another trait is used for methods interacting with the Storage facade.

## Installation

```
composer require cyber-duck/model-files --dev
```

## Usage

Storing/Saving e.g. from one of your controller:
```php
...
public function store(Request $request, Company $company) {
    $validatedData = $request->validate([
        'logo' => 'required|image',
    ]);
    
    // You can set logo to a string (if it's already stored in the disk)
    // If won't create another file
    $company->logo = $validatedData->logo;
    $company->save();
    
    ...
}
```

Accessing the file url:
```php
Company::find(1)->url('logo');
```

Delete file:
```php
$company = Company::find(1);
$company->deleteFile('logo'); // Deletes and sets the attribute to `null`
$company->save();
```

@todo delete model also prunes the file
```php
Company::find(1)->delete(); // File will be deleted as well
```

### Configuration

Make your model implement Storable interface and use the trait `HasFiles` to your models class. 
```
<?php

namespace App;

use Cyberduck\ModelFiles\Storable;
use Cyberduck\ModelFiles\HasFiles;
use Illuminate\Database\Eloquent\Model;

class Company extends Model implements Storable
{
    use HasFiles;
}
```

Define your files on the model:

```php
class Company extends Model implements Storable
{
    ...
    
    protected $files = [
        'logo' => [
            'disk' => 'public', // Default is 'default'
            'folder' => 'logos', // Default is '/'
            'prunable' => true, // Default is true
        ],
        'avatar' => [
            'disk' => 'public',
            'folder' => 'avatars',
            'url' => 'temporary', // available: 'public' (default), 'temporary', 'custom'
            'expiresAt' => '+5 min', (Not implemented yet - for temporary)
        ],
        'invoice' => [
            'folder' => 'invoices',
            'url' => 'custom', // The following method will be called: `getUrlInvoice` with the value of the attribute
        ]
    ];
    
    public function getUrlInvoice($path) {
        // e.g. `companies/1/invoices/randomGenerated.pdf`
        return route('company.invoice', [$company, $path]);
    }
```
