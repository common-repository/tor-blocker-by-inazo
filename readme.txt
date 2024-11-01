=== Plugin Name ===
Contributors: inazo
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9E7RUJAAHB5C2
Tags: tor, security, tor blocker, ip block, ip blocker, ip
Requires at least: 4.5.1
Tested up to: 4.9.1
Stable tag: 1.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html 

This plugin blocks Tor users by preventing them from viewing your website.

== Description ==

[EN]Tor Blocker

As soon as you enable it it will block any connection to your site for Tor network users.

Please use this plugin only on commercial sites that have no reason to know traffic from Tor. Thank you for not blocking tutorial sites, courses, computer security etc. Which could be used by people in non-free Internet areas.

Tor users will be blocked in front and back office. To unblock access you just need to disable it.

The blocking takes place at the earliest in the execution of the code of your site to limit the consume in resources of this kind of visitor.

For works the plugin send DNS request to *ip-port.exitlist.torproject.org, the request ask to torproject if the user's IP address is an exit nodes or not. 

Term of use of Tor Network : https://www.torproject.org/docs/trademark-faq.html.en

[FR]Tor Blocker

Dès que vous l'activez il va bloquer toute connexion à votre site pour les utilisateurs du réseau Tor.

Merci d'utiliser ce plugin uniquement sur des sites commerciaux qui n'ont aucune raison de connaître du traffic provenant de Tor. Merci de ne pas bloquer les sites de tutos, cours, sécurité informatique etc. qui pourraient être utilisé par des personnes dans des zones d'Internet non libre.

Les utilisateurs de Tor seront bloqués en front et en back office. Pour en débloquer l'accès il vous suffit de le désactiver.

Le blocage à lieu au plus tôt dans l'éxécution du code de votre site pour limiter la consomation en ressources de ce genre de visiteur.

Pour fonctionner le plugin envoi une requête DNS à *ip-port.exitlist.torproject.org. La requête va demander à torproject.org si l'adresse IP de l'utilisateur est un noued de sortie du réseau TOR.

CGU : https://www.torproject.org/docs/trademark-faq.html.en

Copyright icon onion : http://fr.freepik.com/vecteurs-libre/collection-d-39-icones-de-legumes_948406.htm

== CHANGELOG ==

= 1.1 =
* adding table in database for caching IP results and increase performance
* add clear log tables every three hours

= 1.0 =
* init version