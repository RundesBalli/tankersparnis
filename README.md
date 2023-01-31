# ⛽ [Tankersparnis.net](https://tankersparnis.net)

Tankersparnis.net ist ein Portal zur einfachen Berechnung des gesparten Geldes beim Tanken von Gas gegenüber herkömmlichen Kraftstoffen.  
:bulb: Empfohlen vom YouTuber [Sascha Fahrnünftig](https://www.youtube.com/user/Fahrnuenftig) auf dem ["Eure Videos Fahrnünftig"-YouTube-Kanal](https://www.youtube.com/c/EureVideosFahrnünftig) in den Folgen [185](https://youtu.be/U6eEqCILX70?t=649) und [222](https://youtu.be/cGMxy8QqsRI?t=690).

## Berechnung

Der Heizwert des getankten Gases wird auf den Heizwert von Benzin oder Diesel umgerechnet. Die Menge an fossilem Kraftstoff, wird dann mit aktuellen Spritpreisen (wahlweise standortabhängig oder deutschlandweit) gegengerechnet. Die aktuellen Spritpreise bekommen wir von der [Tankerkönig-API](https://creativecommons.tankerkoenig.de/). Daraus ergibt sich dann die Ersparnis. Durch diese Berechnung werden Mehrverbräuche (z.B. durch Autobahnfahrten) berücksichtigt.
Interessierte und Programmierer können sich die genaue Berechnung [hier](https://github.com/RundesBalli/tankersparnis/blob/74ba896fffcf6bc16b819a5c95e2b4c5d78ad3b6/public/inc/addEntry.php#L147) ansehen.

## Simpel, übersichtlich
### Formular zum Hinzufügen eines Eintrages:  
<img src="/public/img/entry.png" alt="Eintragen"/>  

### Statistiken
<sub>Beispielwerte!</sub>  
<img src="/public/img/totalMonthly.png" alt="Monatswerte"/>   
<img src="/public/img/totalSavings.png" alt="Gesamtwerte"/>   

### Danke
Danke an meinen Freund [NullDev](https://github.com/NullDev) für die GeoLocation Funktion, danke an meinen Freund Soldiermelly für das Logo und die Banner und danke an [Tankerkönig](https://creativecommons.tankerkoenig.de/) für die Spritpreis-API.
