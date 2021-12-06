<?php

namespace Ice\Interface;

interface Observable
{
    //~
    public function addObserver($observer);

    //~
    public function removeObserber($class);

    //~
    public function notifyObservers();
}