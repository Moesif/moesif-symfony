# Moesif Symfony SDK Documentation
by [Moesif](https://moesif.com), the [API analytics](https://www.moesif.com/features/api-analytics) and [API monetization](https://www.moesif.com/solutions/metered-api-billing) platform.

[Moesif's official SDK for PHP Symfony](https://github.com/Moesif/moesif-symfony) allows you to automatically capture API traffic and send to the [Moesif API Analytics]({{site.baseurl}}/api-analytics) platform.

> If you're new to Moesif, see [our Getting Started](https://www.moesif.com/docs/) resources to quickly get up and running.

## Supported Symfony Versions
The SDK currently supports all Symfony 5.x versions.

## Prerequisites
Before using this SDK, make sure you have the following:

- [An active Moesif account](https://moesif.com/wrap)
- [A Moesif Application ID](#get-your-moesif-application-id)

### Get Your Moesif Application ID
After you log into [Moesif Portal](https://www.moesif.com/wrap), you can get your Moesif Application ID during the onboarding steps. You can always access the Application ID any time by following these steps from Moesif Portal after logging in:

1. Select the account icon to bring up the settings menu.
2. Select **Installation** or **API Keys**.
3. Copy your Moesif Application ID from the **Collector Application ID** field.

<img class="lazyload blur-up" src="images/app_id.png" width="700" alt="Accessing the settings menu in Moesif Portal">

## Install the SDK
Moesif Symfony SDK is available through [Composer](https://getcomposer.org/), the dependency manager for PHP. After you have Composer installed, execute the following command:

```bash
$ composer require moesif/moesif-symfony
```
 
Otherwise, manually add `moesif/moesif-symfony` to your [`composer.json` file](https://getcomposer.org/doc/01-basic-usage.md#composer-json-project-setup).

## Enable the SDK
Follow these steps to enable the SDK:

1. In your project's `config/packages` directory, add a `moesif.yaml` file. This file holds your configuration options. 
2. The configuration file requires you to at least [specify your Moesif Application ID](#get-your-moesif-application-id). The following shows an example:

    ``` yaml
    moesif:
      moesif_application_id: 'YOUR_MOESIF_APPLICATION_ID.'
      debug: false
      options:
        max_queue_size: 50
        max_batch_size: 25
      hooks_class: 'App\Configuration\MyMoesifHooks'
    ```
For more configuration options, see [YAML Configuration Options](#configuration-options).

## Configure the SDK
See the available [configuration options](#configuration-options) to learn how to configure the SDK for your use case.

## Troubleshoot
For a general troubleshooting guide that can help you solve common problems, see [Server Troubleshooting Guide](https://www.moesif.com/docs/troubleshooting/server-troubleshooting-guide/). 

Other troubleshooting supports:

- [FAQ](https://www.moesif.com/docs/faq/)
- [Moesif support email](mailto:support@moesif.com)

## Configuration Options
The following sections describe the available configuration options that you can define in `moesif.yaml`.

### `applicationId` (Required)
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
  </tr>
  <tr>
   <td>
    <code>String</code>
   </td>
  </tr>
</table>
A string that identifies your application..


### `debug`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
  </tr>
  <tr>
   <td>
    <code>bool</code>
   </td>
  </tr>
</table>

Set to `true` to print debug messages into the debug and error logs. This can help you troubleshoot integration issues.

### `options`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
  </tr>
  <tr>
   <td>
    <code>Object</code>
   </td>
  </tr>
</table>

If set, contains various options to configure the SDK beyond the defaults.

In this object, you can set the following options:

#### `max_queue_size`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
   <th scope="col">
    Default
   </th>
  </tr>
  <tr>
   <td>
    <code>Integer</code>
   </td>
   <td>
    <code>15</code>
   </td>
  </tr>
</table>

If set, overrides the default `max_queue_size` before sending data to Moesif.

#### `max_batch_size`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
   <th scope="col">
    Default
   </th>
  </tr>
  <tr>
   <td>
    <code>Integer</code>
   </td>
   <td>
    <code>10</code>
   </td>
  </tr>
</table>

If set, overrides the default `max_batch_size` that is sent over to Moesif.

#### `hooks_class`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
  </tr>
  <tr>
   <td>
    <code>String</code>
   </td>
  </tr>
</table>

If set, this must be [your implementation of `MoesifHooksInterface`](#user-hook-class-options).

### User Hook Class Options

You can override and customize certain functionalities within the plugin with hooks. For example, create a file `MyMoesifHooks.php` and implement the `MoesifHooksInterface`:

```php
use Moesif\MoesifBundle\Interfaces\MoesifHooksInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MyMoesifHooks implements MoesifHooksInterface {
   // your implementation of every method follows.
}
```
See [the Example Implementation](#example-implementation) for an example.

Make sure to set the [`hooks_class`](#hooks_class) configuration option to the path of your implementation file.

The methods you need to implement for `MoesifHooksInterface` are the following:

#### `identifyUserId`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
   <th scope="col">
    Parameters
   </th>
   <th scope="col">
    Return type
   </th>
  </tr>
  <tr>
   <td>
    Function
   </td>
   <td>
    <code>($request, $response)</code>
   </td>
   <td>
    <code>String</code>
   </td>
  </tr>
</table>

Optional.

This function takes the `$request` and `$response` arguments and returns a string for a user ID. This enables Moesif to attribute API requests to individual unique users so you can understand who calls your API. You can use this simultaneously with [`identifyCompanyId`](#identifycompanyid) to track both individual customers and also the companies they are part of.

#### `identifyCompanyId`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
   <th scope="col">
    Parameters
   </th>
   <th scope="col">
    Return type
   </th>
  </tr>
  <tr>
   <td>
    Function
   </td>
   <td>
    <code>($request, $response)</code>
   </td>
   <td>
    <code>String</code>
   </td>
  </tr>
</table>

Optional. 

This function takes the `$request` and `$response` arguments and returns a string for a company ID. If you have a B2B business, this method enables Moesif to attribute API requests to specific companies or organizations so you can understand which accounts call your API. You can use this simultaneously with [`identifyUserId`](#identifyuserid) to track both individual customers and the companies they are part of.

#### `identifySessionToken`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
   <th scope="col">
    Parameters
   </th>
   <th scope="col">
    Return type
   </th>
  </tr>
  <tr>
   <td>
    Function
   </td>
   <td>
    <code>($request, $response)</code>
   </td>
   <td>
    <code>String</code>
   </td>
  </tr>
</table>

Optional.

A function that takes the `$request` and `$response` arguments and returns a string for session or auth token. Moesif automatically creates sessions by processing your API data, but you can override this through the `identifySessionId` method if you're not happy with the results.

#### `maskRequestHeaders`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
   <th scope="col">
    Parameters
   </th>
   <th scope="col">
    Return type
   </th>
  </tr>
  <tr>
   <td>
    Function
   </td>
   <td>
    <code>($headers)</code>
   </td>
   <td>
    <code>$headers</code>
   </td>
  </tr>
</table>

Optional.

A function that takes the `$headers` argument for a request. The argument is an associative array. The method
returns an associative array with your sensitive request headers removed or masked.

#### `maskRequestBody`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
   <th scope="col">
    Parameters
   </th>
   <th scope="col">
    Return type
   </th>
  </tr>
  <tr>
   <td>
    Function
   </td>
   <td>
    <code>($body)</code>
   </td>
   <td>
    <code>$body</code>
   </td>
  </tr>
</table>

Optional.

A function that takes the `$body` argument for request body. The argument an associative array representation of JSON. The method
returns an associative array with any request body information removed.

#### `maskResponseHeaders`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
   <th scope="col">
    Parameters
   </th>
   <th scope="col">
    Return type
   </th>
  </tr>
  <tr>
   <td>
    Function
   </td>
   <td>
    <code>($headers)</code>
   </td>
   <td>
    <code>$headers</code>
   </td>
  </tr>
</table>

Optional.

Same as [`maskRequestHeaders`](#maskrequestheaders), but for HTTP responses.

#### `maskResponseBody`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
   <th scope="col">
    Parameters
   </th>
   <th scope="col">
    Return type
   </th>
  </tr>
  <tr>
   <td>
    Function
   </td>
   <td>
    <code>($body)</code>
   </td>
   <td>
    <code>$body</code>
   </td>
  </tr>
</table>

Optional.

Same as [`maskResponseBody`](#maskrequestbody), but for HTTP responses.

#### `getMetadata`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
   <th scope="col">
    Parameters
   </th>
   <th scope="col">
    Return type
   </th>
  </tr>
  <tr>
   <td>
    Function
   </td>
   <td>
    <code>($request, $response)</code>
   </td>
   <td>
    <code>Associative Array</code>
   </td>
  </tr>
</table>

Optional.

A function that takes the `$request` and `$response` arguments and returns `$metadata`. `$metadata` is an associative array representation of JSON.

#### `skip`
<table>
  <tr>
   <th scope="col">
    Data type
   </th>
   <th scope="col">
    Parameters
   </th>
   <th scope="col">
    Return type
   </th>
  </tr>
  <tr>
   <td>
    Function
   </td>
   <td>
    <code>($request, $response)</code>
   </td>
   <td>
    <code>bool</code>
   </td>
  </tr>
</table>

Optional.

A function that takes the `$request` and `$response` arguments. Returns `true` if you want to skip the event. Skipping an event means Moesif doesn't log the event.

#### Example Implementation
The following example shows what the `MyMoesifHooks.php` file may look like with a `MoesifHooksInterface` implementation:

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
## Examples
The following examples demonstrate how to add and update customer information.

### Update a Single User

The following example shows how you can create or update a user profile in Moesif using the `updateUser` function.

The `metadata` field can contain any customer demographic or other information you want to store.

Only the `user_id` field is required.

For more information, see the [PHP API Reference about updating a singer user](https://www.moesif.com/docs/api?php#update-a-user).

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

### Update Users in Batch

You can use the `updateUsersBatch` function to update a list of users in one batch.

Only the `user_id` field is required.

For more information, see the [PHP API Reference about updating users in batch](https://www.moesif.com/docs/api?php#update-users-in-batch).

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

### Update a Single Company

To create or update a company profile in Moesif, use the `updateCompany` function.

The `metadata` field can contain any company demographic or other information you want to store.

Only the `company_id` field is required.

For more information, see the [PHP API Reference about updating a single company](https://www.moesif.com/docs/api?php#update-a-company).

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

### Update Companies in Batch

To update a list of companies in one batch, use the `updateCompaniesBatch` function.

Only the `company_id` field is required.

For more information, see the [PHP API Reference about updating companies in batch](https://www.moesif.com/docs/api?php#update-companies-in-batch).

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

## How to Get Help
If you face any issues using this SDK, try the [troubheshooting guidelines](#troubleshoot). For further assistance, reach out to our [support team](mailto:support@moesif.com).

## Explore Other Integrations

Explore other integration options from Moesif:

- [Server integration options documentation](https://www.moesif.com/docs/server-integration//)
- [Client integration options documentation](https://www.moesif.com/docs/client-integration/)
