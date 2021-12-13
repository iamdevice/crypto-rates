# Cryptocurrency rates

## Run local environment

```bash
cp .env.dist .env
docker compose up -d
```

## Fetch currency rates from CryptoCompare

```bash
docker compose exec app ./bin/console rates:crypto-compare BTC USD
docker compose exec app ./bin/console rates:crypto-compare BTC EUR
docker compose exec app ./bin/console rates:crypto-compare ETH USD
docker compose exec app ./bin/console rates:crypto-compare ETH EUR
```

## Get rates by API

```http request
GET /rates/BTC/USD

Host: http://localhost
Content-Type: application/json
```

```http request
GET /rates/BTC/USD?period=daily

Host: http://localhost
Content-Type: application/json
```
