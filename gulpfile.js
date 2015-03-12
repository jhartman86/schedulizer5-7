/****************************************************************************
 This file acts as an aggregator for other gulp.js files nested anywhere
 within your packages/ directory. Copy gulp.sample.json to any directory
 requiring its own builds (eg. packages, blocks), rename it to "gulp.js",
 and then you can just run "$:gulp" to watch/build sub-projects in
 entirety.

 Note: when registering or referencing tasks within gulp.js files, ALWAYS
 use the _taskName() function, as it'll automatically prepend the name
 of the parent directory of the gulp.js file to the task for uniqueness.
    - eg: _taskName("some:task")

 When setting watch tasks for your sub-projects, register them with the
 following format:

 gulp.task(_taskName('watches'), function(){
    gulp.watch(_pathTo('{dir}/*.{extension}'), {interval:1000}, [_taskName("some:task")]);
 });
****************************************************************************/
(function( gulp, glob, taskListing ){

    // Path to packages directory
    var _packages = __dirname + '/web/packages/';

    // Run "$:gulp tasks" to see a list of all available tasks
    gulp.task('tasks', taskListing);

    // Search all packages for gulp.js at the root level
    glob.sync("**/gulp.js", {cwd: _packages}).forEach(function(fileName){
        require(_packages + fileName)(gulp);
    });

    // Find any tasks named "watch:{unique}" and add to defaults
    var _defaults = [];
    Object.keys(gulp.tasks).forEach(function( taskName ){
        if( taskName.indexOf('watches') !== -1 ){
            _defaults.push(taskName);
        }
    });

    // Register all watch tasks as defaults
    gulp.task('default', _defaults);

})( require('gulp'), require('glob'), require('gulp-task-listing') );