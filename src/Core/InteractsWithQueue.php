<?php

namespace Mallabee\Queue\Core;

trait InteractsWithQueue
{
    /**
     * The underlying queue job instance.
     *
     * @var JobInterface
     */
    protected $job;

    /**
     * Get the number of times the job has been attempted.
     *
     * @return int
     */
    public function attempts()
    {
        return $this->job ? $this->job->attempts() : 1;
    }

    /**
     * Delete the job from the queue.
     *
     * @return void
     */
    public function delete()
    {
        if ($this->job) {
            return $this->job->delete();
        }
    }

    /**
     * Fail the job from the queue.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function fail($exception = null)
    {
        if ($this->job) {
            FailingJob::handle($this->job->getConnectionName(), $this->job, $exception);
        }
    }

    /**
     * Release the job back into the queue.
     *
     * @param  int   $delay
     * @return void
     */
    public function release($delay = 0)
    {
        if ($this->job) {
            return $this->job->release($delay);
        }
    }

    /**
     * Set the base queue job instance.
     *
     * @param  JobInterface  $job
     * @return $this
     */
    public function setJob(JobInterface $job)
    {
        $this->job = $job;

        return $this;
    }
}
