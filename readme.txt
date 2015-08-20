=== All-in-One WP Migration ===
Contributors: yani.iliev, bangelov, pimjitsawang
Tags: db migration, migration, wordpress migration, db backup, db restore, website backup, website restore, website migration, website deploy, wordpress deploy, db backup, database export, database serialization, database find replace
Requires at least: 3.3
Tested up to: 4.3
Stable tag: 4.3
License: GPLv2 or later

All-in-One WP Migration is the only tool that you will ever need to migrate a WordPress site.

== Description ==
The plugin allows you to export your database, media files, plugins, and themes.
You can apply unlimited find/replace operations on your database and the plugin will also fix any serialization problems that occur during find/replace operations.

All in One WP Plugin is the first plugin to offer true mobile experience on WordPress versions 3.3 and up.

= Works on all hosting providers =
* The plugin does not depend on any extensions, making it compatible with all PHP hosting providers.
* The plugin exports and imports data in time chunks of 3 seconds each, which keeps the plugin below the max execution time that most providers set to 30 seconds.
* We have tested the plugin on the major Linux distributions, Mac OS X, and Microsoft Windows.

= Bypass all upload size restriction =
* We use chunks to import your data and that way we bypass any webserver upload size restrictions up to **512MB** - commercial version supports up to **5GB**.

= 0 Dependencies =
* The plugin does not require any php extensions and can work with PHP v5.2.

= Support for MySQL, PDO, MySQLi =
* No matter what php mysql driver your webserver ships with, we support it.

= Support WordPress v3.3 up to v4.2 =
* We tested every WordPress version from `3.3` up to `4.2`.

= Migrate WordPress to most popular cloud services using our completely new extensions =
* [Dropbox](https://servmask.com/products/dropbox-extension)
* [Google Drive](https://servmask.com/products/google-drive-extension)
* [Amazon S3](https://servmask.com/products/amazon-s3-extension)
* [Multisite](https://servmask.com/products/multisite-extension)
* [Unlimited](https://servmask.com/products/unlimited-extension)
* And many more to come

= Contact us =
* [Get free help from us here](https://servmask.com/help)
* [Report a bug or request a feature](https://servmask.com/help)
* [Find out more about us](https://servmask.com)

[youtube http://www.youtube.com/watch?v=5FMzLf9a4Dc]

== Installation ==
1. Upload the `all-in-one-wp-migration` folder to the `/wp-content/plugins/` directory
1. Activate the All in One WP Migration plugin through the 'Plugins' menu in WordPress
1. Configure the plugin by going to the `Site Migration` menu that appears in your admin menu

== Screenshots ==
1. Mobile Export page
2. Mobile Import page
3. Plugin Menu

== Changelog ==
= 4.3 =
* Add URL extension support
* Filter "mu-plugins" directory if "Do not export plugins (files)" is checked
* Fix utf8mb4 issue
* Fix translation issue

= 4.2 =
* Fix .wpress.bin format

= 4.1 =
* Add port to the host header on export/import
* Rename .wpress file to .wpress.bin file

= 4.0 =
* Fix file permission checks

= 3.9 =
* Fix could not resolve domain name on export/import

= 3.8 =
* Fix undefined method on Backups page if PHP version is < 5.3.6

= 3.7 =
* Add IPv6 support on export/import

= 3.6 =
* Fixed undefined constant warnings

= 3.5 =
* Exclude core plugin and extensions on export if they have custom names

= 3.4 =
* Made export/import processes more reliable
* Allow the plugin to work with non-default name
* Preserve backups during plugin updates
* Improved find & replace functionality on the serialized data
* Removed backup file name restrictions

= 3.3 =
* Fixed a bug when retrieving export/import status progress
* Fixed a bug when database encoding utf8mb4_unicode_ci is not available

= 3.2.2 =
* Fixed plugin incompatibility during export/import that was reporting that the process could not be started

= 3.2.1 =
* Added username/password settings for WordPress sites behind HTTP basic authentication
* Fixed a bug when exporting/importing without public DNS record
* Fixed a bug when exporting/importing media files

= 3.2.0 =
* Added advanced settings on export page

= 3.1.1 =
* Fixed secret key issue on upgrade of the plugin

= 3.0.0 =
* Added export to File, [Dropbox](https://servmask.com/products/dropbox-extension), [Amazon S3](https://servmask.com/products/amazon-s3-extension), [Google Drive](https://servmask.com/products/google-drive-extension)
* Added import from File, [Dropbox](https://servmask.com/products/dropbox-extension), [Amazon S3](https://servmask.com/products/amazon-s3-extension), [Google Drive](https://servmask.com/products/google-drive-extension)
* Implemented our own archiving format that reduces export and import by a factor of 10
* One-click export with the new simplified export page
* Improved upload functionality with auto-recognizing chunk size on import
* New **Backups** page for storing all WordPress site exports
* Easy restore WordPress site from **Backups** page
* Monitoring availability of the disk space on the server
* Both export and import happen in time chunks of 3 seconds
* Plugin works behind HTTP basic authentication

= 2.0.4 =
* Updated readme to reflect that the plugin is not multisite compatible

= 2.0.3 =
* Fixed a security issue while importing site using regular users

= 2.0.2 =
* Added support for WordPress v4.0

= 2.0.1 =
* Fixed a bug when all user permissions are lost on import

= 2.0.0 =
* Added support for migration of WordPress in Network Mode (Multi Site)
* New improved UI and UX
* New improved language translations on the menu items and help texts
* Better error handling and notifications
* Fixed a bug while exporting comments and associated comments meta data
* Fixed a bug while using find/replace functionality
* Fixed a bug with storage directory permissions and search indexation

= 1.9.2 =
* Added PHP <= v5.2.7 compatibility

= 1.9.1 =
* Fixed an issue with earlier versions of PHP

= 1.9.0 =
* New improved design on the export/import page
* Added an option for gathering user experience statistics
* Added a message box with important notifications about the plugin
* Fixed a bug while exporting database with multiple WordPress sites
* Fixed a bug while exporting database with table constraints
* Fixed a bug with auto recognizing zip archiver

= 1.8.1 =
* Added "Get Support" link in the plugin list page
* Removed "All in One WP Migration Beta" link from the readme file

= 1.8.0 =
* Added support for dynamically recognizing Site URL and Home URL on the import page
* Fixed a bug when maximum uploaded size is exceeded
* Fixed a bug while exporting big database tables

= 1.7.2 =
* Added support for automatically switching database adapters for better performance and optimization
* Fixed a bug while using host:port syntax with MySQL PDO
* Fixed a bug while using find/replace functionality

= 1.7.1 =
* Fixed a bug while exporting WordPress plugins directory

= 1.7.0 =
* Added storage layer to avoid permission issues with OS's directory used for temporary storage
* Added additional checks to verify the consistency of the imported archive
* Fixed a bug that caused the database to be exported without data
* Removed unused variables from package.json file

= 1.6.0 =
* Added additional check for directory's permissions
* Added additional check for output buffering when exporting a file
* Fixed a bug when the archive was exported or imported with old version of Zlib library
* Fixed a bug with permalinks and flushing the rules

= 1.5.0 =
* Added support for additional errors and exceptions handling
* Added support for reporting a problem in better and easier way
* Improved support process in ZenDesk system for faster response time
* Fixed typos on the import page. Thanks to Terry Heenan

= 1.4.0 =
* Added a Twitter and Facebook share buttons to the sidebar on import and export pages

= 1.3.1 =
* Fixed a bug when the user was unable to import site archive
* Optimized and speeded up import process

= 1.3.0 =
* Added support for mysql connection to happen over sockets or TCP
* Added support for Windows OS and fully tested the plugin on IIS
* Added support for limited memory_limit - 1MB - The plugin now requires only 1MB to operate properly
* Added support for multisite
* Used mysql_unbuffered_query instead of mysql_query to overcome any memory problems
* Fixed a deprecated warning for mysql_pconnect when php 5.5 and above is used
* Fixed memory_limit problem with PCLZIP library
* Fixed a bug when the archive is exported with zero size when using PCLZIP
* Fixed a bug when the archive was exported broken on some servers
* Fixed a deprecated usage of preg_replace \e in php v5.5 and above

= 1.2.1 =
* Fixed an issue when HTTP Error was shown on some hosts after import, credit to Michael Simon
* Fixed an issue when exporting databases with different prefix than wp_, credit to najtrox
* Fixed an issue when PDO is avalable but mysql driver for PDO is not, credit to Jaydesain69
* Deleted a plugin specific option when uninstalling the plugin (clean after itself)
* Support is done via Zendesk
* Included WP Version and Plugin version in the feedback form

= 1.2.0 =
* Increased upload limit of files from 128MB to 512MB
* Used ZipArchive with fallback to PclZip (a few users notified us that they don’t have ZipArchive enabled on their servers)
* Used PDO with fallback to mysql (a few users notified us that they dont have PDO enabled on their servers, mysql is deprecated as of PHP v5.5 but we are supporting PHP v5.2.17)
* Supported PHP v5.2.17 and WordPress v3.3 and above
* Fixed a bug during export that causes plugins to not be exported on some hosts (the problem that you are experiencing)

= 1.1.0 =
* Importing files using chunks to overcome any webserver upload size restriction
* Fixed a bug where HTTP code error was shown to some users

= 1.0.0 =
* Export database as SQL file
* Export media files
* Export themes files
* Export installed plugins
* Unlimited find/replace actions
* Option to exclude spam comments
* Option to apply find/replace to GUIDs
* Option to exclude post revisions
* Option to exclude tables data
