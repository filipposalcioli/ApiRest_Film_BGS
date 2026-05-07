# Web Service Film BGS

API REST sviluppata in PHP per l'accesso semplificato a informazioni cinematografiche e premi Oscar.

---

##  Descrizione

**Web Service Film BGS** fornisce un layer di astrazione sopra dati pubblici di film, permettendo di:

* cercare film per titolo
* ottenere il cast principale
* consultare e inserire premi Oscar

L'API integra dati esterni e un database locale per offrire risposte più mirate e leggere.

---

## Tecnologie utilizzate

* **Backend:** PHP
* **Server:** Apache
* **Database:** MySQL
* **Formato dati:** JSON
* **API esterna:** https://imdbapi.dev

---

## Avvio del progetto

1. Clona il repository
2. Posiziona i file nella directory del server (es. `htdocs` o `www`)
3. Importa il database:

   * esegui `tabella.sql`
   * esegui `oscar.sql`
4. Configura la connessione in `config/db.php`
5. Avvia Apache e MySQL

---

## 🌐 Base URL

```
http://localhost/
```

---

## Endpoint disponibili

### 1. Ricerca film

**GET** `/api/film/titles`

#### Parametri

| Nome | Tipo   | Obbligatorio | Descrizione     |
| ---- | ------ | ------------ | --------------- |
| film | string | si           | Titolo del film |

#### Esempio richiesta

```
http://localhost/api/film/titles&film=inception
```

#### Risposta

```json
{
  "film": [
    {
      "id": "tt1375666",
      "title": "Inception",
      "year": 2010,
      "image": "...",
      "rating": 8.8
    }
  ]
}
```

---

### 2. Attori di un film

**GET** `/api/film/actors`

#### Parametri

| Nome | Tipo   | Obbligatorio | Descrizione     |
| ---- | ------ | ------------ | --------------- |
| film | string | si           | Titolo del film |

#### Esempio richiesta

```
http://localhost/api/film/actors&film=inception
```

#### Risposta

```json
{
  "film": "Inception",
  "actors": [
    {
      "name": "Leonardo DiCaprio",
      "birthday": "1974-11-11",
      "image": "...",
      "birthLocation": "USA"
    }
  ]
}
```

---

### 3. Premi Oscar

#### GET — Recupero premi

**GET** `/api/film/oscar`

Parametro:

| Nome | Tipo   | Obbligatorio | Descrizione     |
| ---- | ------ | ------------ | --------------- |
| film | string | si           | Titolo del film |

Esempio:

```
http://localhost/api/film/oscar&film=inception
```

---

#### POST — Inserimento premio

**POST** `/api/film/oscar`

#### Body JSON

```json
{
  "title": "Inception",
  "year": 2011,
  "category": "Best Visual Effects",
  "winner": "Inception"
}
```

#### Risposta

```json
{
  "message": "Record creato con successo"
}
```

---

## Codici di errore

| Codice | Significato           |
| ------ | --------------------- |
| 400    | Richiesta non valida  |
| 404    | Risorsa non trovata   |
| 405    | Metodo non consentito |
| 500    | Errore server         |
| 502    | Errore API esterna    |

---

## Autori

* Bacci
* Guerrini
* Salcioli

---
