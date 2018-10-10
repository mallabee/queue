Date: October 9, 2018.

State: Still Active.

Leaning on `illuminate/queue` version: 5.7.9



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



## Usage

First thing to know is that most of the missing information you can find at `illuminate/queue` docs as our package is pretty similar (although with some changes):
https://laravel.com/docs/5.7/queues#introduction

### Seeding work to Queue Manager

To start, create a new Queue manager instance.

```PHP
use Mallabee\Queue\Core\Manager as Queue;

$queue = new Queue;

$instance = $queue->configure('beanstalkd', [
    'host' => 'localhost',
    'queue' => 'default',
]);

// If you like - set last configured instance as global
$queue->setAsGlobal();
```

Once the instance has been registered and configured. You may use it like so to seed new jobs:

```PHP
// As an instance...
$queue->push('SendEmail', array('message' => $message));

// If setAsGlobal has been called...
Queue::push('SendEmail', array('message' => $message));
```

### Registering a custom queue handler driver

**Note:** Make sure all your connector, driver and job files are inside same folder

```php
use Mallabee\Queue\Core\Manager as Queue;

$queue = new Queue;

$this->registerDriver('beanstalkd', new \Drivers\Beanstalkd\BeanstalkdConnector());

$instance = $queue->configure('beanstalkd', [
    'host' => 'localhost',
    'queue' => 'default',
]);

// If you like - set last configured instance as global
$queue->setAsGlobal();
```

### Using the queue and processing work via the Worker

Advise the demo app that is located under `tests/example` to understand how you can pull jobs from the queue and process them.

### Traits and their usage

#### InteractsWithQueue (Job trait)

- Allows a job to interact with the queue - delete a job, etc..

#### JobUtils (Job trait)

- Populate the job parameters with ease. Helps when you want to easily interact with the parameters (due to ability to decide the parameters types).

### Passing your Container, Event Dispatcher, Logger, ExceptionHandler

#### Container

We are using the popular PSR container interface.

The container is passed to the job in-order to allow you to get your dependencies easily.

#### Event Dispatcher

Notice that we only use the `listen` & `dispatch` of the interface, no need to implement anything else.

#### Logger

We are using the popular PSR logger interface.

Used in the queue utils.

### Definitions

- Manager/Queue Manager (sometimes referred to as Queue) - The manager that registers drivers, connect to the queue, pushes/seeds jobs to the queue.
- Worker - The worker that pulls jobs from queue, fire relevant events.
- Job - The work that needs to be done.
- Connection - A queue connection.
- Queue - A queue.
- Driver/Adapter - A driver is the way to interact with new queue type (e.g.: Beanstalkd, SQS, Redis, Database).
- Connector - A driver connector is the class that makes the connection between a queue configuration and a driver and generate an instance.
- Event - An event is fired when relevant stuff happens (such as job processed, job failed). You can listen to these events and interact with the queue while they occur.
- Queue Utils - sometimes it happens that a driver have implementation / ideas that other drivers don't share with it, this is the place to put them.

### Best practices for background task management

- Why not daemonizing a PHP script? - PHP is not good for long background processes - read more: http://symcbean.blogspot.com/2010/02/php-and-long-running-processes.html
- Why not using `crontab` to run the worker? cron can only generate a worker each one minute at minimum.


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
- `FailingJob` was merged to `Worker`->`failJob`.
- `Str` & `Arr` helper classes - took only needed functions.


### Structural difference

| Was in place (`illuminate/queue`) | New place in MQ    |
|-----------------------------------|--------------------|
| Capsule\Manager / QueueManager    | Core\Manager       |
| Queue                             | Core\Queue         |
| Worker                            | Core\Worker        |
| WorkerOptions                     | Core\WorkerOptions |



## Contribute

Contribution is highly appreciated.

Here's what we are missing:
- Drivers/Adapters for different queue management (Database [without Doctrine or any other ORM], etc..).
- Event Dispatcher Adapters for different event dispatcher libraries.
- Container Adapters for e.g.: Symfony, Laravel, Slim, Zend, Yii.
- Bundles for e.g.: Symfony, Laravel, Slim, Zend, Yii.
- Documentation.
- Testing.
- Making sure all the features of `illuminate/queue` work in here too.
- Battle test the library with different production scenarios.
- Remove the dependency of Carbon.
- Haven't yet actually tested `Sqs` & `Redis` queues.

#### Fix required

- `FailingJob` is missing.