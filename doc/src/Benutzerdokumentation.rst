.. |date| date:: %d/%m/%Y
.. |year| date:: %Y

.. footer::
   .. class:: tablefooter

   +-------------------------+-------------------------+
   | Stand: |date|           | .. class:: rightalign   |
   |                         |                         |
   |                         | ###Page###/###Total###  |
   +-------------------------+-------------------------+

.. header::
   .. image:: images/logo.png
      :width: 3.5cm
      :height: 1.225cm
      :align: right

.. sectnum::

=====================================================================
fraisr - Extension zur Anbindung von Magento an den fraisr-Marktplatz
=====================================================================

.. raw:: pdf

   PageBreak

.. contents:: fraisr - Benutzerdokumentation

.. raw:: pdf

   PageBreak


Voraussetzungen
===============

Um einen reibungslosen Ablauf der Extension zu gewährleisten, sind folgende Voraussetzungen zu erfüllen:

Magento
-------

Die Extension unterstützt folgende Magento-Versionen:

- Community-Edition 1.5+
- Community-Edition 1.6+
- Community-Edition 1.7+

Server
------

Die Anforderungen der Extension an den Server sind identisch zu den 
Magento-Voraussetzungen (http://www.magentocommerce.com/system-requirements).

Auszug:

- Linux x86, x86-64
- Apache 1.3.x, Apache 2.0.x, Apache 2.2.x
- PHP 5.2.13 - 5.3.24
- MySQL 4.1.20+
- Cronjobs (crontab)


fraisr
------

Um Artikel über die Magento-Extension bei fraisr einzustellen zu können, müssen Sie als gewerblicher
Händler registriert sein. Sofern Sie noch nicht registriert sind, melden Sie sich bitte auf folgender Seite an:
https://www.fraisr.com/register-business .

Folgende Zugangsdaten sind zur Konfiguration notwendig (im fraisr-Backend einsehbar):

- Key
- Secret

Schritt für Schritt - Einrichtungsleitfaden
===========================================

Download der Extension
----------------------

#. Öffnen Sie die MagentoCommerce-Website (TODO: URL einfügen)
#. Akzeptieren Sie die allgemeinen Geschäftsbedingungen (AGB)
#. Kopieren Sie den Extension-Key in die Zwischenablage
#. Melden Sie sich in Ihrem Magento-Backend an
#. Navigieren Sie zu "System" -> "Magento Connect" -> "Magento Connect Manager"
#. Öffnen Sie das Tab "Extensions"
#. Kopieren Sie den Extension-Key aus der Zwischenablage in das Eingabefeld "Paste extension key to install" und wählen Sie anschließend "Install" und im Fall einer Bestätigungs-Aufforderung "Proceed"
#. Sofern im schwarzen Terminal die Nachricht "Package ... installed successfully" erscheint, wurde die Extension korrekt installiert.


Konfiguration
-------------

Um zum fraisr-Konfigurationsbereich im Magento-Backend zu gelangen, navigieren Sie zu:
"System" -> "Konfiguration" -> "SERVICES" -> "fraisr".

Basiskonfiguration
~~~~~~~~~~~~~~~~~~

.. figure:: images/screenshots/configarea_basic.png
   :width: 12cm

   Basiskonfiguration

.. list-table:: Basiskonfiguration
   :widths: 15 40
   :header-rows: 1

   * - Konfiguration

     - Beschreibung

   * - Aktiviert

     - Hier können Sie auswählen, ob die fraisr-Extension aktiv oder inaktiv ist. Sofern hier "Nein" ausgewählt wird, finden keine 
       Synchronisierungen per Cronjob und Anpassungen im Frontend statt. Nach der Installation ist die Extension standardmäßig deaktiviert.

   * - Sandbox

     - Sofern aktiviert werden alle Aktionen gegen die Sandbox-API ausgeführt. Damit lässt sich die Integration und Funktionalität der Extension im Shop überprüfen.

   * - Key

     - Ihr Key ist im fraisr-Backend einsehbar.

   * - Secret

     - Ihr Secret ist im fraisr-Backend einsehbar.

Produkt-Synchronisierung
~~~~~~~~~~~~~~~~~~~~~~~~

.. figure:: images/screenshots/configarea_catalog_sync.png
   :width: 12cm

   Produkt-Synchronisierung




Bestellungs-Synchronisierung
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. figure:: images/screenshots/configarea_frontend.png
   :width: 12cm

   Bestellungs-Synchronisierung



Spendenlabel Produktübersicht
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~


.. figure:: images/screenshots/configarea_frontend_product_detail.png
   :width: 12cm

   Spendenlabel Produktübersicht




Spendenlabel Produktdetailansicht
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

.. figure:: images/screenshots/configarea_order_sync.png
   :width: 12cm

   Spendenlabel Produktdetailansicht



Synchronisation der Spendenpartner und Kategorien
-------------------------------------------------

Produkt-Synchronisation
-----------------------

fraisr-Protokoll
----------------

Synchronisation der Bestellungen
--------------------------------

Shop Frontend
-------------

Spendenprodukt - Abbuchung von Provision
----------------------------------------

