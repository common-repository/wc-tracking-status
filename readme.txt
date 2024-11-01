=== WC Tracking Status ===
Contributors: diurvan
Web Page: https://diurvanconsultores.com
Tags: status tracking, multilingual, woocommerce, status order, status shipping
Requires at least: 5.5
Tested up to: 6.2.2
Stable tag: 6.2.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Update and show aditional states for tracking orders from woocommerce.

== Description ==

-Update and show aditional states for tracking orders from woocommerce.

The objective of this plugin is to serve as a small tool to inform a client about the status of Shipping (Shipping) of their order from the web, and via email. The Plugin adds, through a ShortCode, some screens for the user to enter their order number. The system searches for the order information, and displays the status in a friendly way.

This plugin's code is [available on GitLab](https://gitlab.com/diurvan/wc-tracking-status). Please feel free to fork the repository and send a pull request. If you find a bug in the plugin, open an issue.

Major features in Wc Tracking Status include:

* Includes unlimited statuses, configured by yourself.
* Paint a graduant color depending from these status

Tutorial video:
[youtube https://youtu.be/o7BQVMnkpjA]

** Quick Links **
- Subscribe to [diurvanConsultores YouTube Channel](https://www.youtube.com/channel/UCu29w3t1XwSfIp80avLgrcQ) for tutorials, news and updates for my plugins

If you have any question or features request, please access the plugin's official support forum. You can also get help sending an email to ivan.tapia@diurvanconsultores.com

== Installation ==

= Simple Installation =

1. Search for 'WC Tracking Status' in the 'Plugins > Add New' menu and click 'Install'
2. Activate the plugin through the 'Plugins' menu in WordPress

= Manual Installation =

1. Download the [latest version of the plugin](https://downloads.wordpress.org/plugin/diu-wc-tracking-status.zip)
2. Upload the `wc-tracking-status` directory to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Update Woocommerce state to "Completed" and then, will appear "Status Tracking"
2. Add shortcode to any page or post
3. Test on front writing order number from WooCommerce
4. View info result
5. Set state from which the tracking states will be viewed
6. Yes/Not to include "seguimiento-envios-woocommerce" plugin
7. Column and Bulk action for change order status
8. Better responsive experience on Front and more information from Order
9. Custom Number Tracking. Allow to enable or not by using a control field

== Frequently Asked Questions ==
= 1. What additional plugins do I need to install? =
You just need to install WooCommerce into your WordPress instalation.

= 2. Why don't I see the additional tracking statuses when I edit the order?? =
The order should go to WooCommerce "completed" status. From that state, the additional states of WC Tracking Status can be handled

= 3. How do I include the order tracking search option?? =
You only need to include the shortcode [diurvan_custom_tracking] in any page, post or custom post

= 4. How to get support? =
Please create a support request in the official support forum. You can also get help from my webpage https://diurvanconsultores.com or sending an email to ivan.tapia@diurvanconsultores.com

== Changelog ==
= 2.0.3 =
*Release Date - 17th May, 2023*
* We added the "Use Custom Number Tracking" checkbox to find order by this number (not use default WooCommerce Order Number).
* Show or hide metabox "Number Tracking" or edit order

= 2.0.2 =
*Release Date - 22th February, 2022*
* Product image size modified to view small size in table

= 2.0.1 =
*Release Date - 22th February, 2022*
* We added the "Send Email when status change" checkbox to notify or not the client when there has been a status change
* We added the "Own styles on Tracking Page" checkbox to not use Bootstrap on the tracking page.
* We added the "Don't include notes on track" checkbox to not include information from customer notes on the tracking page.
* We added the thumbnail image in the products table of the Tracking page.

= 2.0.0 =
*Release Date - 27th December, 2021*
* Comtability with plugin "seguimiento-envios-woocommerce" to show metaboxes
* Add Bulk Actions to Shop Orders table with custom states from WC Tracking
* Add WC Tracking column to Shop Orders table
* Add class to button tracking
* Include fee, shipping cost and grand total from order.
* Add responsive for order detail table

= 1.0.6 =
*Release Date - 21th October, 2021*
* New link to add Status from info Order.
* Insert default status into taxonomy to show at least one.

= 1.0.5 =
*Release Date - 09th August, 2021*
* Add a setring section.
* Set a state from which the tracking states will be viewed

= 1.0.4 =
*Release Date - 09th August, 2021*
* Change field "Order number" for allow any kind of text, not only number.

= 1.0.3 =
*Release Date - 09th July, 2021*

* A new taxonomy was included in the Products section that allows adding as many states as needed.
* The configured statuses are available when modifying the order, and are also displayed as a progress bar on the customer page.

= 1.0.2 =
*Release Date - 06th April, 2021*

* Include more information about plugin.

= 1.0.1 =
* Initial release