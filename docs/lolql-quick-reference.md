# LOLQL-Kurzanleitung

Diese Kurzanleitung beschreibt Syntax, die in `vendor/cwmoss/lolql` dokumentiert und durch Parser beziehungsweise Tests belegt ist. Sie ist bewusst keine vollstaendige Sprachspezifikation.

## Grundform

Jede Abfrage beginnt mit einer Auswahlfunktion und Klammern:

```lolql
*()                         // alle Dokumente
*(_type == "article")       // nach einem Feld filtern
article()                   // alle Dokumente des Typs article
article(status == "draft")  // Typ und zusaetzliche Bedingung
```

Ein alleinstehendes `*` ist laut README nicht unterstuetzt. Bei Slowfoot liefert `query` die Treffer als JSON-Array.

## Filter

Feldpfade koennen mit Punkten verschachtelt werden. Unterstuetzte Operatoren laut Implementierung:

```lolql
*(_id == "abc.123")
*(visits >= 20 && status != "draft")
*(status == "published" || status == "archived")
*(!(status == "draft"))
*(status in ["published", "archived"])
*("featured" in tags)
*(published_at isnull)
*(published_at notnull)
*(title matches "slowfoot*")
```

Operatoren: `==`, `!=`, `<`, `<=`, `>`, `>=`, `in`, `matches`, `isnull`, `notnull`, `!`, `&&` und `||`. Verwende Klammern, wenn Bedingungen kombiniert werden, damit die Gruppierung eindeutig ist.

Strings werden in Anfuehrungszeichen geschrieben; Zahlen sollten ganzzahlig sein. Fuer eine robuste, gut lesbare Abfrage werden doppelte Anfuehrungszeichen empfohlen.

## Sortierung, Limit und Projektion

Die dokumentierte Reihenfolge ist Auswahl/Filter, optionale Sortierung, optionales Limit, optionale Projektion:

```lolql
*(_type == "article" && status == "published") order(published_at desc) limit(10) {_id, title, "date":published_at}
```

- `order(field)` sortiert aufsteigend; `order(field desc)` absteigend. Mehrere Felder koennen laut Implementierung kommasepariert angegeben werden.
- `limit(size)` begrenzt die Trefferzahl. Die README dokumentiert auch `limit(size offset)`, zum Beispiel `limit(20 40)`.
- `{field, another_field}` projiziert Felder. Mit `"alias":path` kann ein Ergebnisfeld umbenannt werden, zum Beispiel `{"date":published_at}`.
- Verschachtelte Feldpfade verwenden Punkte, zum Beispiel `slug.current` oder `author.name`.

## Aufruf ueber die Slowfoot-CLI

Der Query-Ausdruck muss als einzelnes Shell-Argument uebergeben werden. Aeussere einfache Anfuehrungszeichen schuetzen die doppelten Anfuehrungszeichen des LOLQL-Ausdrucks:

```sh
bin/slowfoot -d=docs query '*(_type == "chapter"){_id, title}'
```

## Nicht verwenden, bis bestaetigt

- Boolean-Filter wie `*(published == true)` und Kurzformen wie `*(published)` sind in der README als nicht funktionierend markiert. **[OFFEN: Gibt es eine unterstuetzte Syntax, um Boolean-Felder zu filtern?]**
- Das Verhalten von `matches` mit `*` ist nicht vollstaendig spezifiziert. **[OFFEN: Bedeutet `*` nur Platzhalter am Anfang/Ende? Sind Muster wie `*foo*`, mehrere `*`, Gross-/Kleinschreibung und ein woertliches `*` definiert?]**
- `===` ist im Parser vorhanden, aber Bedeutung und Gleichheit der Ergebnisse zwischen SQLite und Array-Auswertung sind nicht dokumentiert. **[OFFEN: Soll `===` oeffentlich unterstuetzt werden, und worin unterscheidet es sich von `==`?]**
- `count(...)` und `preview(...)` werden vom Parser als Funktionen auf oberster Ebene erkannt, aber ihr Verhalten ist fuer `slowfoot query` nicht dokumentiert. **[OFFEN: Sind diese Funktionen fuer CLI-Abfragen vorgesehen, und welche Ausgabe sollen sie liefern?]**
- Das Limit-Format mit Offset ist im README beschrieben, jedoch in den Paket-Tests nicht abgedeckt. **[OFFEN: Ist die bestaetigte Form `limit(Anzahl Offset)` mit Leerzeichen, und ist der Offset nullbasiert?]**
- Escaping von Anfuehrungszeichen und Backslashes in String-Literalen ist nicht dokumentiert. **[OFFEN: Welche Escape-Sequenzen unterstuetzt LOLQL?]**

Vermeide ausserdem Funktionen und Muster, die in der README als nicht funktionierend markiert sind, etwa `dateTime(...)`, `now()`, `path(...)`, Dereferenzierung mit `->` oder boolesche Literale, bis sie separat implementiert und getestet sind.
