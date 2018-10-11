<?php

namespace Mallabee\Queue\Core;

trait JobUtils
{
    /**
     * Populated the job properties with a given payload to ease the job usage and possibly for IDE re-casting
     *
     * @param $payload
     * @throws \ReflectionException
     */
    protected function populate($payload)
    {
        if (!empty($payload) && (is_object($payload) || is_array($payload))) {
            $r = new \ReflectionClass($this);
            foreach ($payload as $key => $value) {
                if (!is_null($payload) && property_exists($this, $key)) {
                    if (is_string($value) && !empty($r->getProperty($key)) && is_array($r->getProperty($key)->getValue($this))) {
                        $this->$key = $this->asArray($value);
                    } else {
                        $this->$key = $value;
                    }
                }
            }
        }
    }

    /**
     * @param $text
     *
     * @return array
     */
    private function asArray($text): array
    {
        $arr = [];
        if (is_string($text))
            $arr = explode(",", $text);

        return $arr;
    }
}
