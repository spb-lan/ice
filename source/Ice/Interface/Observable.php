<?php

namespace Ice\Interfaces;

interface Observable
{
    //~
    public function addObserver($observer);

    //~
    public function removeObserber($class);

    //~
    public function notifyObservers();
}