<?php
namespace Deployer;

require 'recipe/laravel.php';

// Config

set('repository', 'https://github.com/msoler75/laravel9_inertia.git');

add('shared_files', []);
add('shared_dirs', []);
add('writable_dirs', []);

// Hosts

host('199.999.00000.0')
    ->set('remote_user', 'deployer')
    ->set('deploy_path', '~/public_html');



// a couple of additional options
set('allow_anonymous_stats', false);
set('git_tty', true);

// define the paths to PHP & Composer binaries on the server
set('bin/php', '/usr/local/bin/php');
set('bin/node', '/usr/local/bin/node');
set('bin/composer', '{{bin/php}} /usr/local/bin/composer');

// compile our production assets
task('npm:build', function () {
    run('cd {{release_path}} && {{bin/node}} install');
    run('cd {{release_path}} && {{bin/node}} run build');
    run('cd {{release_path}} && {{bin/node}} install --omit=dev');
})->desc('Compile npm files locally');
after('deploy:vendors', 'npm:build');

// Hooks

after('deploy:failed', 'deploy:unlock');

// after a deploy, clear our cache and run optimisations
after('deploy:cleanup', 'artisan:cache:clear');
after('deploy:cleanup', 'artisan:optimize');

// handle queue restarts
// after('deploy:success', 'artisan:queue:restart');
// after('rollback', 'artisan:queue:restart');
