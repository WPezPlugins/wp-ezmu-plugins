WP ezMU-Plugins
===============

The standard WordPress mu-plugins folder is "unstructured". WP ezMU-Plugins approximates something closer to the traditional WP plugins folder structure and UI. It also enables you to control load order, as well as excluded plugins from specified sites with a WP Multisite network. That is, must-use doesn't have to be must-use. 

For more info on WordPress and Must-Use plugins: 

http://codex.wordpress.org/Must_Use_Plugins


Setup WP ezMU-Plugins
=====================

1) If you don't already have one, add your folder: /mu-plugins/. Typically this is within your /wp-content/ folder.

2) Within the /mu-plugins/ folder, you should have the folder for this plugin: /wp-ezmu-plugins/. In other words, something along the lines of: .../wp-content/mu-plugins/wp-ezmu-plugins/

3) Within the /wp-ezmu-plugins/ folder, look in the folder /move-then-copy/. The file wp-ezmu-plugins.php to up and out of /wp-ezmu-plugins/ but within /mu-plugins/. In other words, instead of having all your plugin in /mu-plugins/ you will (probably) only have this file (wp-ezmu-plugins.php).

4) Note: wp-ezmu-plugins.php will no longer be part of your wp-ezmu-plugins repo. But that's okay, you're going to want to customize it anyway. 

5) Open the file wp-ezmu-plugins.php with your text editor. There are three "example" plugins already there. Remove those examples (if you're not using them) and add the plugins you will be using as mu-plugins. 

6) WP ezMU-Plugins adds a sub-menu to your WP Admin > Plugins menu so you're able to see a list of your must-use plugins. This is read / view only. All updates must be done manually via wp-ezmu-plugins.php. You will also have to update your plugins manually. 