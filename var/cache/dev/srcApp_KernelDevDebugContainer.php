<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerIia2RiB\srcApp_KernelDevDebugContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerIia2RiB/srcApp_KernelDevDebugContainer.php') {
    touch(__DIR__.'/ContainerIia2RiB.legacy');

    return;
}

if (!\class_exists(srcApp_KernelDevDebugContainer::class, false)) {
    \class_alias(\ContainerIia2RiB\srcApp_KernelDevDebugContainer::class, srcApp_KernelDevDebugContainer::class, false);
}

return new \ContainerIia2RiB\srcApp_KernelDevDebugContainer([
    'container.build_hash' => 'Iia2RiB',
    'container.build_id' => 'b879668c',
    'container.build_time' => 1562417086,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerIia2RiB');
