# googlesearch2pdf

This little project parses google search results and saves them into a PDF file.

### Installation

```bash
docker-compose up -d
```

### Usage

```bash
docker exec -it CONTAINERNAME php cli.php SEARCH_QUERY RESULT_QUANTITY
```
How to get your container name:
```bash
# CONTAINERNAME = value from NAMES column
docker ps
```
