# Hinweise fuer GitHub Copilot

## Slowfoot-Projekt

- Lies `slowfoot-config.php` und die betroffenen Dateien, bevor du Projektstruktur, Datenquellen, Templates oder Build-Verhalten aenderst.
- Verwende vorhandene Feldnamen und `_type`-Werte aus den Quelldaten. Erfinde keine Felder. Wenn ein Feld nicht belegt ist, untersuche die Daten oder frage nach.
- Halte Aenderungen klein und passend zu den bestehenden PHP-, Phue- und Verzeichnis-Konventionen des Projekts.
- Fuehre Slowfoot-Kommandos aus dem Projektverzeichnis aus. Falls die globale CLI nicht verfuegbar ist, verwende den im Projekt eingerichteten Slowfoot-Aufruf.

## LOLQL-Abfragen

Wenn du Daten untersuchen musst, verwende `slowfoot query` und gib die gesamte Abfrage als ein einzelnes, gequotetes Shell-Argument weiter:

```sh
slowfoot -d=. query '*(_type == "article"){_id, title}'
```

LOLQL-Grundregeln:

- Jede Abfrage beginnt mit einer Auswahlfunktion: `*()` fuer alle Dokumente, `*(Filter)` fuer gefilterte Dokumente oder `typeName(Filter)` fuer einen Dokumenttyp. Ein alleinstehendes `*` ist nicht unterstuetzt.
- Feldpfade koennen mit Punkten verschachtelt werden, zum Beispiel `slug.current`.
- Belegte Filteroperatoren sind `==`, `!=`, `<`, `<=`, `>`, `>=`, `in`, `matches`, `isnull`, `notnull`, `!`, `&&` und `||`.
- Listenliterale verwenden eckige Klammern, zum Beispiel `status in ["draft", "published"]`. Setze Strings in doppelte Anfuehrungszeichen.
- Optional folgen `order(field [asc|desc])`, `limit(size [offset])` und als letzter Teil eine Projektion in `{field, "alias":path}`.
- Beispiele: `*(_type == "article" && status != "draft") order(_id desc) limit(10) {_id, title}` und `article()`.
- Verwende keine Funktionen oder Syntax, deren Unterstuetzung im installierten Slowfoot/LOLQL nicht bestaetigt ist. Insbesondere sind Boolean-Kurzfilter, `dateTime()`, `now()`, `path()` und `->` nicht als verfuegbar vorauszusetzen.

Nutze Query-Ergebnisse als Datenbasis fuer weitere Vorschlaege. `slowfoot query` liest Daten und gibt JSON aus; es aendert den Datensatz nicht.
