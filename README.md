# Moesif Symfony SDK

Official SDK for PHP Symfony 5.x to automatically capture API traffic and send to the Moesif API Analytics platform.

[Source Code on GitHub](https://github.com/Moesif/moesif-symfony1.4)

## How to install

Via Composer

```bash
$ composer require moesif/moesif-symfony
```

or add `moesif/moesif-symfony` to your composer.json file accordingly.

## How to enable the Plugin

In your project's `config/packages` directory, add a `moesif.yaml` file that will hold your configuration options. Within the file, at a minimum you'll need to add your `moesif_application_id`. Here is an example of the files contents:

``` yaml
moesif:
    moesif_application_id: 'YOUR_MOESIF_APPLICATION_ID'
    debug: false
    options:
        max_queue_size: 50
        max_batch_size: 25
    hooks_class: 'App\Configuration\MyMoesifHooks'
```

Your Moesif Application Id can be found in the [_Moesif Portal_](https://www.moesif.com/).
After signing up for a Moesif account, your Moesif Application Id will be displayed during the onboarding steps.

You can always find your Moesif Application Id at any time by logging
into the [_Moesif Portal_](https://www.moesif.com/), click on the bottom-left menu,
and then clicking _Installation_.

## YAML Configuration Options

### __`applicationId`__

Type: `String`
Required, a string that identifies your application.

### __`debug`__

Type: `Boolean`
Optional, If true, will print debug messages into the debug and error logs.

### __`options`__

Type: `Object`
Optional, if set, contains various options to configure the SDK beyond the defaults.

Here are the options that can be set within this object:

#### __`max_queue_size`__

Type: Int
If set, will override the default max queue size before data is sent over to Moesif. The default is `15`.

#### __`max_batch_size`__

Type: Int
If set, will override the default max batch size that is sent over to Moesif. The default is `10`.

#### __`hooks_class`__

Type: String
Optional, if set, this should be your implementation of the `Moesif\MoesifBundle\Interfaces\MoesifHooksInterface`.

## User Hook class Options

Certain functionalities can be overridden and customized within the plugin with hooks.


Within the `MyMoesifHooks.php` file, you will need to override the following methods:

```php
use Moesif\MoesifBundle\Interfaces\MoesifHooksInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyMoesifHooks implements MoesifHooksInterface {
   // your implementation of every method below.
}
```

The method you should implement is:

### __`identifyUserId`__

Type: `($request, $response) => String`
Optional, a function that takes a $request and $response and return a string for userId. This enables Moesif to attribute API requests to individual unique users so you can understand who calling your API. This can be used simultaneously with `identifyCompanyId` to track both individual customers and also the companies they are a part of.

### __`identifyCompanyId`__

Type: `($request, $response) => String`
Optional, a function that takes a $request and $response and return a string for companyId. If your business is B2B, this enables Moesif to attribute API requests to specific companies or organizations so you can understand which accounts are calling your API. This can be used simultaneously with `identifyUserId` to track both individual customers and the companies their a part of.

### __`identifySessionToken`__

Type: `($request, $response) => String`
Optional, a function that takes a $request and $response and return a string for session token/auth token. Moesif automatically sessionizes by processing your API data, but you can override this via identifySessionId if you're not happy with the results.

### __`maskRequestHeaders`__

Type: `$headers => $headers`
Optional, a function that takes a $headers, which is an associative array, and
returns an associative array with your sensitive headers removed/masked.

### __`maskRequestBody`__

Type: `$body => $body`
Optional, a function that takes a $body, which is an associative array representation of JSON, and
returns an associative array with any information removed.

### __`maskResponseHeaders`__

Type: `$headers => $headers`
Optional, same as above, but for Responses.

### __`maskResponseBody`__

Type: `$body => $body`
Optional, same as above, but for Responses.

### __`getMetadata`__

Type: `($request, $response) => Associative Array`
Optional, a function that takes a $request and $response and returns $metdata which is an associative array representation of JSON.

### __`skip`__

Type: `($request, $response) => String`
Optional, a function that takes a $request and $response and returns true if this API call should be not be sent to Moesif.

Here is an example of what the `MyMoesifHooks.php` file may look like:

``` php
<?php

namespace App\Configuration;

use Moesif\MoesifBundle\Interfaces\MoesifHooksInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class MyMoesifHooks implements MoesifHooksInterface {

  public function __construct() {
  }

  public function identifyUserId(Request $request, Response $response): string|null
  {
    return 'nihao1';
  }

  public function identifyCompanyId(Request $request, Response $response): string|null
  {
    return $request->headers->get('X-Company-Id');
  }

  public function identifySessionToken(Request $request, Response $response): string|null
  {
    return null;
  }

  public function getMetadata(Request $request, Response $response): ?array
  {
      return null;
  }

  public function skip(Request $request, Response $response): bool
  {
      return false;
  }

  public function maskRequestHeaders(array $headers): array
  {
      return $headers;
  }

  public function maskResponseHeaders(array $headers): array
  {
      return $headers;
  }

  public function maskRequestBody($body)
  {
      // this can be a string or array object.
      // because prior to php 8, can not declare union type (such as string|array)
      return $body;
  }

  public function maskResponseBody($body)
  {
      // this can be a string or array object.
      // because prior to php 8, can not declare union type (such as string|array)
      return $body;
  }
}

```

## Update a Single User

Create or update a user profile in Moesif.
The metadata field can be any customer demographic or other info you want to store.
Only the `user_id` field is required.
For details, visit the [PHP API Reference](https://www.moesif.com/docs/api?php#update-a-user).

```php
<?php
use Moesif\MoesifBundle\Service\MoesifApiService;

private $moesifApiService;

public function __construct(MoesifApiService $moesifApiService)
{
    $this->moesifApiService = $moesifApiService;
}

// metadata can be any custom object
$data->metadata = array(
    "email" => "john@acmeinc.com",
    "first_name" => "John",
    "last_name" => "Doe",
    "title" => "Software Engineer",
    "sales_info" => array(
        "stage" => "Customer",
        "lifetime_value" => 24000,
        "account_owner" => "mary@contoso.com"
    )
);

$userData = [
    'user_id' => $data['userId'],
    'user_email' => $data['userEmail'],
    'company_id' => $data['companyId'],
    'metadata' => $data['metadata'] ?? [], // Include metadata if provided
    // Add more fields as needed
];

$this->moesifApiService->updateUser($userData);
```

## Update Users in Batch

Similar to updateUser, but used to update a list of users in one batch.
Only the `user_id` field is required.
For details, visit the [PHP API Reference](https://www.moesif.com/docs/api?php#update-users-in-batch).

```php
<?php
use Moesif\MoesifBundle\Service\MoesifApiService;

private $moesifApiService;

public function __construct(MoesifApiService $moesifApiService)
{
    $this->moesifApiService = $moesifApiService;
}

// metadata can be any custom object
$data->metadata = array(
    "email" => "john@acmeinc.com",
    "first_name" => "John",
    "last_name" => "Doe",
    "title" => "Software Engineer",
    "sales_info" => array(
        "stage" => "Customer",
        "lifetime_value" => 24000,
        "account_owner" => "mary@contoso.com"
    )
);

$userDataA = [
    'user_id' => $data['userIdA'],
    'user_email' => $data['userEmailA'],
    'company_id' => $data['companyIdA'],
    'metadata' => $data['metadata'] ?? [], // Include metadata if provided
    // Add more fields as needed
];

$userDataB = [
    'user_id' => $data['userIdB'],
    'user_email' => $data['userEmailB'],
    'company_id' => $data['companyIdB'],
    'metadata' => $data['metadata'] ?? [], // Include metadata if provided
    // Add more fields as needed
];

$users = array($userDataA, $userDataB)
$this->moesifApiService->updateUsersBatch($user);
```

## Update a Single Company

Create or update a company profile in Moesif.
The metadata field can be any company demographic or other info you want to store.
Only the `company_id` field is required.
For details, visit the [PHP API Reference](https://www.moesif.com/docs/api?php#update-a-company).

```php
<?php
use Moesif\MoesifBundle\Service\MoesifApiService;

private $moesifApiService;

public function __construct(MoesifApiService $moesifApiService)
{
    $this->moesifApiService = $moesifApiService;
}

// metadata can be any custom object
$data->metadata = array(
    "org_name" => "Acme, Inc",
    "plan_name" => "Free",
    "deal_stage" => "Lead",
    "mrr" => 24000,
    "demographics" => array(
        "alexa_ranking" => 500000,
        "employee_count" => 47
    )
);

// Prepare company data for Moesif
$companyData = [
    'company_id' => $data['companyId'],
    'company_domain' => $data['companyDomain'],
    'metadata' => $data['metadata'] ?? [], // Include metadata if provided
    // Add more fields as needed
];

$this->moesifApiService->updateCompany($companyData);
```

## Update Companies in Batch

Similar to updateCompany, but used to update a list of companies in one batch.
Only the `company_id` field is required.
For details, visit the [PHP API Reference](https://www.moesif.com/docs/api?php#update-companies-in-batch).

```php
<?php
use Moesif\MoesifBundle\Service\MoesifApiService;

private $moesifApiService;

public function __construct(MoesifApiService $moesifApiService)
{
    $this->moesifApiService = $moesifApiService;
}

// metadata can be any custom object
$data->metadata = array(
    "org_name" => "Acme, Inc",
    "plan_name" => "Free",
    "deal_stage" => "Lead",
    "mrr" => 24000,
    "demographics" => array(
        "alexa_ranking" => 500000,
        "employee_count" => 47
    )
);

// Prepare company data for Moesif
$companyDataA = [
    'company_id' => $data['companyIdA'],
    'company_domain' => $data['companyDomainA'],
    'metadata' => $data['metadata'] ?? [], // Include metadata if provided
    // Add more fields as needed
];

$companyDataB = [
    'company_id' => $data['companyIdB'],
    'company_domain' => $data['companyDomainB'],
    'metadata' => $data['metadata'] ?? [], // Include metadata if provided
    // Add more fields as needed
];

$companies = array($companyDataA, $companyDataB)

$this->moesifApiService->updateCompaniesBatch($companies);
```

## Other integrations

To view more documentation on integration options, please visit __[the Integration Options Documentation](https://www.moesif.com/docs/getting-started/integration-options/).__

For Symfony 1.X please see: https://github.com/Moesif/moesif-symfony1.4
