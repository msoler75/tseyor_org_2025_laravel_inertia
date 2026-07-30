<?php

$entities = [];
foreach (glob(__DIR__ . '/entities/*.php') as $file) {
    $entities = array_merge($entities, include $file);
}
return $entities;
