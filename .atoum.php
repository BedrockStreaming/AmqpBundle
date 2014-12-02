<?php
foreach (glob(__DIR__.'/src/*/Tests') as $dir) {
    $runner->addTestsFromDirectory($dir);
}
