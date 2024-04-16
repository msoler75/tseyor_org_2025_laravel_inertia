<?php
namespace Deployer;

require 'recipe/common.php';


function env($key)
{
    $envFile = __DIR__ . '/.env';

    if (!file_exists($envFile))
        throw new \Exception('.env file not found');

    $envLines = preg_split('/[\r\n]+/', file_get_contents($envFile));

    foreach ($envLines as $line) {
        $line = trim($line);
        if ($line && strpos($line, '#') !== 0 && strpos($line, '=') !== false) {
            list($envKey, $envValue) = explode('=', $line, 2);
            if ($envKey === $key) {
                return trim($envValue);
            }
        }
    }

    throw new \Exception("Environment variable $key not found");
}



// Project name
set('application', 'tseyor.org');

set('default_timeout', 800); // default 300 seconds
set('user', 'pigmalion');
// define the paths to PHP & Composer binaries on the server
// set('bin/php', 'php');
// set('bin/npm', 'npm');
// set('bin/composer', 'composer');


// Config

$repository = env('REPOSITORY');

set('repository', $repository);

// Shared files/dirs between deploys
set('shared_files', [
    '.env'
]);
set('shared_dirs', [
    'storage'
]);


// Writable dirs by web server
set('writable_dirs', [
    'storage',
    'bootstrap/cache'
]);

// Set Laravel version
set('laravel_version', function () {
    $result = run('{{bin/php}} {{release_path}}/artisan --version');
    preg_match_all('/(\d+\.?)+/', $result, $matches);
    $version = $matches[0][0] ?? 5.5;
    return $version;
});

// Disable multiplexing
set('ssh_multiplexing', false);


// Helper Tasks

desc('Disable maintenance mode');
task('artisan:up', function () {
    $output = run('if [ -f {{deploy_path}}/current/artisan ]; then {{bin/php}} {{deploy_path}}/current/artisan up; fi');
    writeln('<info>' . $output . '</info>');
});

desc('Enable maintenance mode');
task('artisan:down', function () {
    $output = run('if [ -f {{deploy_path}}/current/artisan ]; then {{bin/php}} {{deploy_path}}/current/artisan down; fi');
    writeln('<info>' . $output . '</info>');
});

desc('Execute artisan migrate');
task('artisan:migrate', function () {
    run('{{bin/php}} {{release_path}}/artisan migrate --force');
})->once();

desc('Execute artisan migrate:fresh');
task('artisan:migrate:fresh', function () {
    run('{{bin/php}} {{release_path}}/artisan migrate:fresh --force');
})->once();

desc('Execute artisan migrate:rollback');
task('artisan:migrate:rollback', function () {
    $output = run('{{bin/php}} {{release_path}}/artisan migrate:rollback --force');
    writeln('<info>' . $output . '</info>');
})->once();

desc('Execute artisan migrate:status');
task('artisan:migrate:status', function () {
    $output = run('{{bin/php}} {{release_path}}/artisan migrate:status');
    writeln('<info>' . $output . '</info>');
})->once();

desc('Execute artisan cache:clear');
task('artisan:cache:clear', function () {
    run('{{bin/php}} {{release_path}}/artisan cache:clear');
});

desc('Execute artisan config:cache');
task('artisan:config:cache', function () {
    run('{{bin/php}} {{release_path}}/artisan config:cache');
});

desc('Execute artisan route:cache');
task('artisan:route:cache', function () {
    run('{{bin/php}} {{release_path}}/artisan route:cache');
});

desc('Execute artisan view:clear');
task('artisan:view:clear', function () {
    run('{{bin/php}} {{release_path}}/artisan view:clear');
});

desc('Execute artisan optimize');
task('artisan:optimize', function () {
    $deprecatedVersion = 5.5;
    $currentVersion = get('laravel_version');
    if (version_compare($currentVersion, $deprecatedVersion, '<')) {
        run('{{bin/php}} {{release_path}}/artisan optimize');
    }
});

desc('Execute artisan queue:restart');
task('artisan:queue:restart', function () {
    run('{{bin/php}} {{release_path}}/artisan queue:restart');
});

desc('Execute artisan storage:link');
task('artisan:storage:link', function () {
    $needsVersion = 5.3;
    $currentVersion = get('laravel_version');
    if (version_compare($currentVersion, $needsVersion, '>=')) {
        run('{{bin/php}} {{release_path}}/artisan storage:link');
    }
});

desc('Generate ziggy routes');
task('artisan:ziggy:generate', function() {
    run('{{bin/php}} {{release_path}}/artisan ziggy:generate');
});

// compile our production assets
desc('Install and compile npm files locally');
task('npm:build', function () {
    run('cd {{release_path}} && {{bin/npm}} install --omit=dev');
    run('cd {{release_path}} && {{bin/npm}} run build');
});



/**
 * Task deploy:public_disk support the public disk.
 * To run this task automatically, please add below line to your deploy.php file
 *
 *     before('deploy:symlink', 'deploy:public_disk');
 *
 * @see https://laravel.com/docs/master/filesystem#the-public-disk
 */

 desc('Make symlink for public disk');
 task('deploy:public_disk', function () {
     // Remove from source.
     run('if [ -d $(echo {{release_path}}/public/storage) ]; then rm -rf {{release_path}}/public/storage; fi');
     // Create shared dir if it does not exist.
     run('mkdir -p {{deploy_path}}/shared/storage/app/public');
     // Symlink shared dir to release dir
     run('{{bin/symlink}} {{deploy_path}}/shared/storage/app/public {{release_path}}/public/storage');
 });

 // Upload build assets
task('upload', function () {
    upload(__DIR__ . "/public/js/", '{{release_path}}/public/js/');
    upload(__DIR__ . "/public/css/", '{{release_path}}/public/css/');
    //upload(__DIR__ . "/public/service-worker.js", '{{release_path}}/public/service-worker.js');
});

 // Hosts

$production_host = env('PRODUCTION_HOST');
$remove_user = env('PRODUCTION_REMOVE_USER');
$deploy_path = env('PRODUCTION_DEPLOY_PATH');

 // Production Server
host( $production_host)
    ->set('deploy_path', $deploy_path)
    ->set('remote_user', $remove_user)
    ->set('branch', 'master');


// a couple of additional options
set('allow_anonymous_stats', false);
set('git_tty', false);


// Group tasks

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'upload',
    'deploy:shared',
    'deploy:vendors',
    'artisan:ziggy:generate',
    'npm:build',
    'deploy:writable',
    'artisan:storage:link',
    'artisan:view:clear',
    'artisan:cache:clear',
    'artisan:config:cache',
    'artisan:optimize',
    'deploy:symlink',
    'deploy:unlock',
    'deploy:cleanup'
]);

// [Optional] Run migrations
after('deploy:vendors', 'artisan:migrate');

// [Optional] If deploy fails automatically unlock
after('deploy:failed', 'deploy:unlock');

// [Optional] Symlink the public disk.
before('deploy:symlink', 'deploy:public_disk');

// Hooks

after('deploy:failed', 'deploy:unlock');

// after a deploy, clear our cache and run optimisations
after('deploy:cleanup', 'artisan:cache:clear');
after('deploy:cleanup', 'artisan:optimize');

// handle queue restarts
// after('deploy:success', 'artisan:queue:restart');
// after('rollback', 'artisan:queue:restart');
