# Uploader_XH

Uploader_XH ermöglicht den Upload von Dateien in die `images/`,
`downloads/`, `userfiles/` und `media/` Ordner von CMSimple_XH.
Im Gegensatz zum Standard-Filemanager erlaubt es Warteschlangen- und
gestückelte Uploads, so dass es eine Alternative zu FTP darstellt, wenn
viele und/oder große Dateien hoch geladen werden sollten. Allerdings ist
Uploader_XH kein alternativer Filemanager.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
  - [Backend](#backend)
  - [Frontend](#frontend)
    - [Beispiele](#beispiele)
- [Einschränkungen](#einschränkungen)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Uploader_XH ist ein Plugin für [CMSimple_XH](https://www.cmsimple-xh.org/de/).
Es benötigt CMSimple_XH ≥ 1.7.0, PHP ≥ 7.1.0,
und einen Browser, der von der verwendeten jQuery-Version unterstützt wird.
Uploader_XH benötigt weiterhin [Plib_XH](https://github.com/cmb69/plib_xh) ≥ 1.5;
ist dieses noch nicht installiert (siehe `Einstellungen` → `Info`),
laden Sie das [aktuelle Release](https://github.com/cmb69/plib_xh/releases/latest)
herunter, und installieren Sie es.

## Download

Das [aktuelle Release](https://github.com/cmb69/uploader_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

The Installation erfolgt wie bei vielen anderen CMSimple_XH-Plugins auch.
Im [CMSimple_XH-Wiki](https://wiki.cmsimple-xh.org/de/?fuer-anwender/arbeiten-mit-dem-cms/plugins)
finden Sie weitere Informationen.

1. **Sichern Sie die Daten auf Ihrem Server.**
1. Entpacken Sie die Datei auf Ihrem Computer.
1. Laden Sie das gesamte Verzeichnis `uploader/` auf Ihren Server in
   das `plugins/` Verzeichnis von CMSimple_XH.
1. Vergeben Sie Schreibrechte für die Unterverzeichnisse `config/`,
   `css/` und `languages/`.
1. Navgieren Sie zu `Plugins` → `Uploader` im Administrationsbereich,
   um zu prüfen, ob alle Voraussetzungen erfüllt sind.

## Einstellungen

Die Plugin-Konfiguration erfolgt wie bei vielen anderen CMSimple_XH-Plugins
im Administrationsbereich der Website. Wählen Sie `Plugins` → `Uploader`.

Sie können die Voreinstellungen von Uploader_XH unter `Konfiguration`
ändern. Hinweise zu den Optionen werden beim Überfahren der Hilfe-Icons mit
der Maus angezeigt.

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können die
Zeichenketten in Ihre eigene Sprache übersetzen, falls keine entsprechende
Sprachdatei zur Verfügung steht, oder sie entsprechend Ihren Anforderungen
anpassen.

Das Aussehen von Uploader_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

### Backend

Im Backend unter `Plugins` → `Uploader` → `Upload` finden Sie
das Uploadformular. Dessen Verwendung ist weitgehend selbst erklärend.
Verwenden Sie die Auswahllisten um den Typ des Uploads, den Unterordner und
die Größe von JPEG und PNG Bildern festzulegen.

### Frontend

Es ist möglich Uploader_XH auf einer CMSimple_XH Seite zu verwenden.
**Es wird dringend empfohlen diese Möglichkeit nur auf Seiten zu nutzen, die
*nicht öffentlich zugänglich* sind** (z.B. Seiten, die durch
[Register_XH](https://github.com/cmb69/register_xh) oder
[Memberpages](https://github.com/cmsimple-xh/memberpages) geschützt sind).
Andernfalls könnte sich der Disk-Space Ihres Servers schnell mit
nutzlosen oder gar gefährlichen Dateien füllen.

Um das Upload-Widget anzuzeigen, fügen Sie im Inhalt ein:

    {{{uploader('%TYP%', '%UNTERORDNER%', '%GRÖßE%')}}}

Die Platzhalter haben die folgende Bedeutung:

- `%TYP%`:
  Der Typ des Uploads, d.h. `images`, `downloads`, `media` oder `userfiles`.
  `*` präsentiert dem User eine Auswahlliste. Voreinstellung ist `images`.

- `%UNTERORDNER%`:
  Der Unterordner (mit abschließendem `/`) relativ zum Ordner des
  jeweiligen Typs (eingestellt in der Konfiguration von CMSimple_XH), in den
  die Dateien hoch geladen werden sollen. Beachten Sie, dass der Unterordner
  bereits angelegt sein muss. `*` präsentiert dem User eine
  Auswahlliste. Voreinstellung ist `/`.

- `%GRÖßE%`:
  Der Größenmodus für hoch zu ladende JPEG oder PNG Bilder, d.h. `leer`
  (keine Skalierung), `small` (klein), `medium` (mittel) oder `large` (groß).
  `*` präsentiert dem User eine Auswahlliste. Voreinstellung ist `leer`.
  Die gwünschten Größen können in der Konfiguration von Uploader_XH eingestellt werden.
  Es ist zu beachten, dass dies Bilder nicht hochskaliert,
  sondern dass die Größen nur ein Maximum angeben,
  wobei das Bildseitenverhältnis beibehalten wird.
  Beachten Sie weiterhin, dass die Bilder im Browser skaliert werden,
  so dass Bandbreite während des Hochladens gespart wird.
  Allerdings liefert diese Skalierung nicht unbedingt die bestmögliche Qualität,
  so dass Sie selbst prüfen sollten, ob Sie die Skalierung nutzen möchten.

#### Beispiele

Hochladen von Bildern direkt in den eingestellten Bilderordner:

    {{{uploader()}}}

Hochladen von Bildern direkt in den eingestellten Bilderordner inklusive Skalierung auf eine kleine Größe:

    {{{uploader('images', '', 'small')}}}

Hochladen von Dokumenten in den Unterordner `extern/` des eingestellten Downloadordners:

    {{{uploader('downloads', 'extern/')}}}

Hochladen von Dateien in einen auswählbaren Unterordnerdes eingestellten Userfiles-Ordners:

    {{{uploader('userfiles', '*')}}}

Hochladen mit der vollen Flexibilität, die im Backend verfügbar ist:

    {{{uploader('*', '*', '*')}}}

Separate Upload-Widgets für Bilder und Mediadateien auf der selben Seite:

    {{{uploader('images', '', '')}}}
    {{{uploader('media', '', '')}}}

## Einschränkungen

Der volle Funktionsumfang von Uploader_XH steht nur in zeitgemäßen Browsern
zur Verfügung. Ältere Browser bieten unter Umständen nicht alle Features an,
wie beispielsweise gestückeltes Uploaden, Bild-Skalierung etc.,
oder werden überhaupt nicht unterstützt.

## Problembehebung

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/uploader_xh/issues)
oder im [CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Uploader_XH ist freie Software. Sie können es unter den Bedingungen
der GNU Affero General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Uploader_XH erfolgt in der Hoffnung, dass es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU Affero General Public License.

Sie sollten ein Exemplar der GNU Affero General Public License zusammen mit
Uploader_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

Copyright © Christoph M. Becker

Slovakische Übersetzung © Dr. Martin Sereday<br>
Tschechische Übersetzung © Josef Němec<br>
Dänische Übersetzung © Jens Maegard

## Danksagung

Uploader_XH verwendet [Plupload](https://www.plupload.com/).
Vielen Dank an [Ephox](https://www.ephox.com/) für die Veröffentlichung unter AGPL.

Das Pluginlogo wurde von [schollidesign](https://www.deviantart.com/schollidesign) gestaltet.
Vielen Dank für die Veröffentlichung dieses Icons unter GPL.

Vielen Dank an die Community im [CMSimple_XH-Forum](http://www.cmsimpleforum.com)
für Anregungen, Vorschläge und das Testen.
Besonders möchte ich *twc* danken, der mich auf Plupload aufmerksam gemacht hat,
und *wolfgang_58* und *Tata* fürs Testen und das Melden von Fehlern.
Und vielen Dank an *Holger*, der die ursprüngliche API getestet und bei der Verbesserung mitgewirkt hat.
Ebenfalls vielen Dank an *pmschulze*, der einen schwerwiegenden Fehler
in 1.0beta1 berichtet hat.

Und zu guter letzt vielen Dank an [Peter Harteg](http://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von
[CMSimple_XH](https://www.cmsimple-xh.org/de/) ohne die es dieses
phantastische CMS nicht gäbe.
