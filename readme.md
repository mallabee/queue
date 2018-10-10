Date: October 9, 2018.

State: Still Active.

## What?

Framework agnostic, background task management using multiple drivers with custom drivers registering availability.

What is a background task management? simple words - offloading work to background jobs. read more: https://www.slideshare.net/JurianSluiman/queue-your-work  

## Why?

We faced a problem needing a queue management for a Symfony side-project and a pure PHP project that has a framework agnostic approach in mind. This means - low, if at all amount of dependencies.

The available popular packages at that time were:
- `illuminate/queue` (Laravel Framework, which is available for standalone as can be seen in [here](https://github.com/mattstauffer/Torch/tree/master/components/queue), the problem is the amount of bloated dependencies [e.g.: "illuminate/console", "illuminate/container", "illuminate/contracts", "illuminate/database", "illuminate/events", "illuminate/filesystem", "illuminate/pagination", "illuminate/support": "5.8.*"]).
- `yiisoft/yii2-queue` (which is bound to the Yii Framework).

As each package had it's problems, we sat to create this library - a framework agnostic, queue background task management, available for use for pure PHP projects as well.

## How?

ENTER - Mallabee Queue a.k.a MQ / MQueue (by [mallabee.com](mallabee.com))

By leveraging the understanding of queues, best practices and ease of use from multiple libraries such as `yiisoft/yii2-queue`, `illuminate/queue`.

We have taken the `illuminate/queue` package and highly modified it, so take a note as we are leaning on that when we develop new features and maintain this package.

Advantages / Features:

- Framework agnostic - use in pure PHP projects, Symfony, Laravel, Yii, Zend, anything you wish.
- Register custom drivers.
- Use of generators to allow the project creator to consume the jobs as they wish.

### Introduction

Best practices:
- Why not daemonizing a PHP script? - PHP is not good for long background processes - read more: http://symcbean.blogspot.com/2010/02/php-and-long-running-processes.html
- We don't know when is your application is "Down for maintenance".

### Usage

First, create a new Queue manager instance.

```PHP
use Core\Manager as Queue;

$queue = new Queue;

$instance = $queue->configure('beanstalkd', [
    'host' => 'localhost',
    'queue' => 'default',
]);

// If you like - set last configured instance as global
$queue->setAsGlobal();
```

Once the instance has been registered and configured. You may use it like so:

```PHP
// As an instance...
$queue->push('SendEmail', array('message' => $message));

// If setAsGlobal has been called...
Queue::push('SendEmail', array('message' => $message));
```


### Registering a custom queue handler driver

**Note:** Make sure all your connector, driver and job files are inside same folder

```php
use Core\Manager as Queue;

$queue = new Queue;

// $queue->registerDriver('beanstalkd', '\Drivers\Beanstalkd', 'Drivers/Beanstalkd');
$this->registerDriver('beanstalkd', new \Drivers\Beanstalkd\BeanstalkdConnector());

$instance = $queue->configure('beanstalkd', [
    'host' => 'localhost',
    'queue' => 'default',
]);

// If you like - set last configured instance as global
$queue->setAsGlobal();
```


```php
$generator = $queueService->runQueue($queueConfig, $queueLogger, null,
    function (\Exception $e, \Pheanstalk\Pheanstalk $pheanstalk, \Pheanstalk\Job $job) use ($queueLogger) {
        if ($e instanceof \MarketplaceHandler\Exceptions\MarketplaceOperationTimeoutException) {
            // Occurs when we have cURL errors
            // Don't delete nor continue until we stop having cURL errors
            // [2018-01-28 06:22:38] Monolog.ERROR: [CrawlerService] Error starting worker due to error code 0 - cURL error 6: Could not resolve host: webservices.amazon.com (see http://curl.haxx.se/libcurl/c/libcurl-errors.html) [] []

            $queueLogger->error("cURL error: " . $e->getMessage());

            // Remove the job from the queue
            $queueLogger->error("Monitor: Deleting queue worker due to marketplace throttle");
            $pheanstalk->delete($job);
        } else if ($e instanceof \MarketplaceHandler\Exceptions\MarketplaceErrorException) {
            // If we receive 404 or 503 from marketplace - just delete the job,
            // the products id's aren't that important

            // Remove the job from the queue
            $queueLogger->error("Monitor: Deleting queue worker due to error in marketplace");
            $pheanstalk->delete($job);
        } else if ($e instanceof \Exception) {
            $message = "Monitor: Deleting queue worker due to error in marketplace. Exception: {$e->getMessage()} (at file: {$e->getFile()}, at line: {$e->getLine()}.";

            $slackBot = new \CoreBundle\Service\SlackBotService();
            $slackBot->sendMessage($slackBot->getExceptionsClient(), $message);

            // Remove the job from the queue
            $queueLogger->error($message);
            $pheanstalk->delete($job);
        }
    });
foreach ($generator as $jobData) {
    $workerInstance = null;
    $workerData     = null;

    $decodedJobData = json_decode($jobData);
    switch ($decodedJobData->workerType) {
}
```

### Definitions

- Manager/Queue Manager (sometimes referred to as Queue)
- Worker
- Job
- Connection
- Queue
- Driver/Adapter
- Connector
- Event

## MQ and Laravel Queues (`illuminate/queue`)

### Main differences between MQ and Laravel Queues (`illuminate/queue`)

- Folder structure.
- Container can be null, not a must and not passed.
- `QueueManager` and `Manager` are combined into one class `Manager`.
- There is `QueueUtils` allowing you to handle specific driver/adapter functionalities/implementations.
- Event dispatcher is not a must - if no event dispatcher is used - a null dispatcher will be used.
- The way we register handlers.
- `Worker->getNextJob()` - public instead of private, because maybe you just want to peek on a job.
- `Manager` isDownForMaintenance instead of using Capsule.
- `Manager` addition of `$drivers` property, `registerDefaultDrivers`, `registerDriver` and `configure` functions.
- `ExceptionHandlerInterface` instead of using Symfony `ExceptionHandler` directly.
- In general - interfaces & null dummies for everything that isn't a must.
- Default of connection.


### Structural difference

| Was in place (`illuminate/queue`) | New place in MQ    |
|-----------------------------------|--------------------|
| Capsule\Manager / QueueManager    | Core\Manager       |
| Queue                             | Core\Queue         |
| Worker                            | Core\Worker        |
| WorkerOptions                     | Core\WorkerOptions |


### Contribute

Contribution is highly appreciated.

Here's what we are missing:
- Drivers/Adapters for different queue management (Database, Redis, SQS, etc..).
- Event Dispatcher Adapters for different event dispatcher libraries.
- Container Adapters for e.g.: Symfony, Laravel, Slim, Zend, Yii.
- Bundles for e.g.: Symfony, Laravel, Slim, Zend, Yii.
- Documentation.
- Testing.
- Making sure all the features of `illuminate/queue` work in here too.
- Battle test the library with different production scenarios.